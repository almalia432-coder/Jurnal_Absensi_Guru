<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\WaliKelas;
use App\Models\GuruPiket;
use App\Models\GuruMapel;
use App\Models\Satpam;
use App\Models\KepalaSekolah;
use App\Models\Waka;
use App\Models\Siswa;
use App\Models\Guru;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role' => 'nullable|string',
        ]);

        $loginInput = trim($credentials['username']);
        $password = $credentials['password'];
        $selectedRole = $request->input('role');
        $remember = $request->has('remember');

        // 1. Search user by email
        $user = User::where('email', $loginInput)->first();

        // 2. If not found by email, search across all separate role tables by NIP / NISN
        if (!$user) {
            $user = $this->findUserByNipInRoleTables($loginInput);
        }

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

            if (!$isCompatible) {
                return back()->withErrors([
                    'username' => 'Akun tidak memiliki hak akses sebagai ' . str_replace('_', ' ', strtoupper($selectedRole)) . '.',
                ])->onlyInput('username');
            }
        }

        // 4. Periksa apakah akun dinonaktifkan oleh Admin
        if ($user && Hash::check($password, $user->password)) {
            if (!$user->is_active) {
                return back()->withErrors([
                    'username' => 'Akun ini sedang dinonaktifkan oleh Administrator. Silakan hubungi pihak sekolah.',
                ])->onlyInput('username');
            }
        }

        // 5. Attempt login
        if ($user && Auth::attempt(['email' => $user->email, 'password' => $password, 'is_active' => true], $remember)) {
            $request->session()->regenerate();

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

            return $this->redirectUser(Auth::user());
        }

        return back()->withErrors([
            'username' => 'NIP, NISN, atau Email / Password tidak sesuai.',
        ])->onlyInput('username');
    }

    /**
     * Search for user by NIP / NISN across separate role tables.
     */
    protected function findUserByNipInRoleTables(string $nip): ?User
    {
        // Check admin
        $admin = Admin::where('nip', $nip)->first();
        if ($admin && $admin->user_id) return User::find($admin->user_id);

        // Check wali_kelas
        $wali = WaliKelas::where('nip', $nip)->first();
        if ($wali && $wali->user_id) return User::find($wali->user_id);

        // Check guru_piket
        $piket = GuruPiket::where('nip', $nip)->first();
        if ($piket && $piket->user_id) return User::find($piket->user_id);

        // Check guru_mapel
        $mapel = GuruMapel::where('nip', $nip)->first();
        if ($mapel && $mapel->user_id) return User::find($mapel->user_id);

        // Check master guru
        $guru = Guru::where('nip', $nip)->first();
        if ($guru && $guru->user_id) return User::find($guru->user_id);

        // Check satpam
        $satpam = Satpam::where('nip', $nip)->first();
        if ($satpam && $satpam->user_id) return User::find($satpam->user_id);

        // Check kepala_sekolah
        $kepsek = KepalaSekolah::where('nip', $nip)->first();
        if ($kepsek && $kepsek->user_id) return User::find($kepsek->user_id);

        // Check waka
        $waka = Waka::where('nip', $nip)->first();
        if ($waka && $waka->user_id) return User::find($waka->user_id);

        // Check siswa / wali_murid by NISN or NIS
        $siswa = Siswa::where('nisn', $nip)->orWhere('nis', $nip)->first();
        if ($siswa && $siswa->user_id) return User::find($siswa->user_id);

        return null;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    protected function redirectUser($user)
    {
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
        }
    }
}
