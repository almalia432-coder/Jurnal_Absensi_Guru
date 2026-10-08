<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Guru;
use App\Models\Admin;
use App\Models\Satpam;
use App\Models\KepalaSekolah;
use App\Models\Siswa;

class AuthController extends Controller
{
    /**
     * Pesan error generik — identik untuk "akun tidak ditemukan" maupun "password salah",
     * agar tidak bocorkan informasi tentang keberadaan akun.
     */
    private const ERR_GENERIC = 'NIP, NISN, atau Email / Password tidak sesuai.';

    /** Batas percobaan login sebelum ditahan (per kombinasi username+IP). */
    private const MAX_ATTEMPTS = 5;

    /** Durasi blokir dalam detik. */
    private const DECAY_SECONDS = 60;

    // -------------------------------------------------------------------------
    // Tampilkan Halaman Login
    // -------------------------------------------------------------------------

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    // -------------------------------------------------------------------------
    // Proses Login
    // -------------------------------------------------------------------------

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('username'));
        $password   = $request->input('password');
        $remember   = $request->boolean('remember');

        // --- Rate Limiting ---
        $throttleKey = $this->throttleKey($loginInput, $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->onlyInput('username');
        }

        // --- Temukan User ---
        $user = $this->findUser($loginInput);

        // --- Cek Password ---
        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
            return back()->withErrors([
                'username' => self::ERR_GENERIC,
            ])->onlyInput('username');
        }

        // --- Cek Status Aktif (hanya setelah password benar) ---
        if (!$user->is_active) {
            // Tidak menambah hitungan throttle — password sudah benar
            return back()->withErrors([
                'username' => 'Akun ini sedang dinonaktifkan oleh Administrator. Silakan hubungi pihak sekolah.',
            ])->onlyInput('username');
        }

        // --- Login ---
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Reset throttle setelah login berhasil
        RateLimiter::clear($throttleKey);

        return $this->redirectUser($user);
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Kunci throttle berdasarkan username ternormalisasi (lowercase, trim) + IP.
     */
    protected function throttleKey(string $username, string $ip): string
    {
        return 'login|' . Str::lower(trim($username)) . '|' . $ip;
    }

    /**
     * Temukan User berdasarkan email, NIP guru (utama), atau tabel non-guru.
     *
     * Urutan pencarian:
     *  1. users.email  — cocok persis
     *  2. guru.nip     — berlaku untuk semua guru (termasuk yang juga wali kelas / waka)
     *  3. tabel non-guru: admin.nip, satpam.nip, kepala_sekolah.nip,
     *     siswa.nisn, siswa.nis
     *
     * Jika NIP cocok di lebih dari satu akun (guru+non-guru), tolak login dan tulis log.
     */
    protected function findUser(string $input): ?User
    {
        // 1. Cari via email
        $user = User::where('email', $input)->first();
        if ($user) {
            return $user;
        }

        // Kumpulkan matching user_id dari guru dan tabel non-guru
        $matches = collect();

        // 2. Cari via guru.nip (jalur utama untuk semua guru)
        $guruUserIds = Guru::where('nip', $input)
            ->whereNotNull('user_id')
            ->pluck('user_id');
        $matches = $matches->merge($guruUserIds);

        // 3. Cari di tabel non-guru
        $nonGuruUserIds = $this->findUserIdsInNonGuruTables($input);
        $matches = $matches->merge($nonGuruUserIds);

        $uniqueUserIds = $matches->unique()->values();

        if ($uniqueUserIds->count() > 1) {
            Log::warning('AuthController: NIP ganda ditemukan di lebih dari satu akun. Login ditolak.', [
                'nip'      => $input,
                'user_ids' => $uniqueUserIds->toArray(),
            ]);
            return null; // Tolak — NIP ambigu
        }

        if ($uniqueUserIds->count() === 1) {
            return User::find($uniqueUserIds->first());
        }

        return null;
    }

    /**
     * Cari ID user di tabel non-guru: admin, satpam, kepala_sekolah, siswa.
     *
     * @return \Illuminate\Support\Collection<int>
     */
    protected function findUserIdsInNonGuruTables(string $input): \Illuminate\Support\Collection
    {
        $userIds = collect();

        // Admin
        $adminIds = Admin::where('nip', $input)->whereNotNull('user_id')->pluck('user_id');
        $userIds = $userIds->merge($adminIds);

        // Satpam
        $satpamIds = Satpam::where('nip', $input)->whereNotNull('user_id')->pluck('user_id');
        $userIds = $userIds->merge($satpamIds);

        // Kepala Sekolah
        $kepsekIds = KepalaSekolah::where('nip', $input)->whereNotNull('user_id')->pluck('user_id');
        $userIds = $userIds->merge($kepsekIds);

        // Siswa / Wali Murid via NISN atau NIS
        $siswaIds = Siswa::where(function ($q) use ($input) {
            $q->where('nisn', $input)->orWhere('nis', $input);
        })->whereNotNull('user_id')->pluck('user_id');
        $userIds = $userIds->merge($siswaIds);

        return $userIds;
    }

    /**
     * Redirect user ke portal default setelah login berhasil.
     * Menggunakan User::defaultPortal() dan config('portal.portals.*.route').
     * User aktif tanpa portal: tampilkan 403 ramah dengan tombol logout.
     */
    protected function redirectUser(User $user)
    {
        $defaultKey = $user->defaultPortal();

        if (!$defaultKey) {
            return response()->view('errors.403_portal', [
                'user'             => $user,
                'requiredPortals'  => [],
                'availablePortals' => [],
            ], 403);
        }

        $route = config("portal.portals.{$defaultKey}.route", 'login');
        return redirect()->route($route);
    }
}
