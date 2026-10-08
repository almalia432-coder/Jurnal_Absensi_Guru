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

<<<<<<< HEAD
        $loginInput = trim($request->input('username'));
        $password   = $request->input('password');
        $remember   = $request->boolean('remember');
=======
        $loginInput = trim($credentials['username']);
        $password = $credentials['password'];
        $selectedRole = $request->input('role');
        $remember = $request->has('remember');
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f

        // --- Rate Limiting ---
        $throttleKey = $this->throttleKey($loginInput, $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->onlyInput('username');
        }

<<<<<<< HEAD
        // --- Temukan User ---
        $user = $this->findUser($loginInput);
=======
        // 3. If user found and role selected, verify role match with dual-role flexibility
        if ($user && $selectedRole && $selectedRole !== 'semua' && $user->role !== $selectedRole) {
            $isCompatible = false;
            // Guru Mapel & Wali Kelas interoperability:
            // A Wali Kelas is also a Guru Mapel, and a Guru Mapel assigned to a class is a Wali Kelas
            if ($selectedRole === 'guru_mapel' && $user->isGuru()) {
                $isCompatible = true;
            } elseif ($selectedRole === 'wali_kelas' && $user->isWaliKelas()) {
                $isCompatible = true;
            } elseif ($selectedRole === 'waka_piket' && ($user->waka()->exists() || in_array($user->role, ['waka', 'waka_kurikulum', 'waka_sdm', 'admin']) || \App\Models\JadwalPiketKbm::where('piket_waka_nip', $user->guru?->nip)->exists())) {
                $isCompatible = true;
            }
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f

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

<<<<<<< HEAD
        // Reset throttle setelah login berhasil
        RateLimiter::clear($throttleKey);
=======
            // If user explicitly picked a valid dual role during login, redirect to that portal
            if ($selectedRole === 'waka_piket') {
                if ($user->email === 'waka.piket@smkn1boyolangu.sch.id' || $user->role === 'admin') {
                    return redirect()->route('waka-piket.dispensasi');
                }
                return $this->redirectUser(Auth::user());
            }
            if (in_array($selectedRole, ['waka', 'waka_kurikulum', 'waka_sdm']) && $user->isWaka()) {
                return $this->redirectUser(Auth::user());
            }
            if ($selectedRole === 'guru_mapel' && $user->isGuru()) {
                return redirect()->route('guru-mapel.dashboard');
            }
            if ($selectedRole === 'wali_kelas' && $user->isWaliKelas()) {
                return redirect()->route('wali-kelas.dashboard');
            }
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f

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
<<<<<<< HEAD
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
=======
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'wali_kelas':
                return redirect()->route('wali-kelas.dashboard');
            case 'guru_piket':
                return redirect()->route('guru-piket.dashboard');
            case 'guru_mapel':
                return redirect()->route('guru-mapel.dashboard');
            case 'satpam':
                return redirect()->route('satpam.dashboard');
            case 'waka_kurikulum':
                return redirect()->route('waka-kurikulum.dashboard');
            case 'waka_sdm':
                return redirect()->route('waka-sdm.dashboard');
            case 'wali_murid':
                return redirect()->route('wali-murid.dashboard');
            case 'waka':
                if ($user->email === 'waka.piket@smkn1boyolangu.sch.id') {
                    return redirect()->route('waka-piket.dispensasi');
                }
                $bidangLower = strtolower($user->waka?->bidang ?? '');
                if (str_contains($bidangLower, 'sdm')) {
                    return redirect()->route('waka-sdm.dashboard');
                }
                if (str_contains($bidangLower, 'kurikulum')) {
                    return redirect()->route('waka-kurikulum.dashboard');
                }
                if (str_contains($bidangLower, 'kesiswaan') || str_contains($bidangLower, 'kedisiplinan')) {
                    return redirect()->route('waka-kesiswaan.dashboard');
                }
                return redirect()->route('waka-kesiswaan.dashboard');
            case 'kepala_sekolah':
                return redirect()->route('kepala-sekolah.dashboard');
            default:
                return redirect()->route('jurnal.index');
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
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
