<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\WaliKelas;
use App\Models\GuruPiket;
use App\Models\GuruMapel;
use App\Models\KepalaSekolah;
use App\Models\Waka;
use App\Models\Satpam;
use App\Models\Siswa;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MasterUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role   = $request->input('role');
        $status = $request->input('status');

        $query = User::query()
            ->with(['guru', 'admin', 'waliKelas', 'guruPiket', 'guruMapel', 'kepalaSekolah', 'waka', 'satpam', 'siswa.kelas'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%")
                       ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->when($role && $role !== 'all', function ($q) use ($role) {
                $q->where('role', $role);
            })
            ->when($status !== null && $status !== '', function ($q) use ($status) {
                $q->where('is_active', (bool) $status);
            });

        $userList = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Global Statistics
        $totalUser      = User::count();
        $totalActive    = User::where('is_active', true)->count();
        $totalAdmin     = User::where('role', 'admin')->count();
        $totalGuru      = User::whereIn('role', ['wali_kelas', 'guru_mapel', 'guru_piket'])->count();
        $totalStaff     = User::whereIn('role', ['kepala_sekolah', 'waka', 'waka_sdm', 'waka_kurikulum', 'satpam'])->count();
        $totalWaliMurid = User::where('role', 'wali_murid')->count();

        // Role Breakdown Counts for Quick Tabs
        $roleCounts = [
            'all'            => User::count(),
            'admin'          => User::where('role', 'admin')->count(),
            'wali_kelas'     => User::where('role', 'wali_kelas')->count(),
            'guru_mapel'     => User::where('role', 'guru_mapel')->count(),
            'guru_piket'     => User::where('role', 'guru_piket')->count(),
            'waka_kurikulum' => User::where('role', 'waka_kurikulum')->count(),
            'waka_sdm'       => User::where('role', 'waka_sdm')->count(),
            'wali_murid'     => User::where('role', 'wali_murid')->count(),
            'kepala_sekolah' => User::where('role', 'kepala_sekolah')->count(),
            'satpam'         => User::where('role', 'satpam')->count(),
            'waka'           => User::where('role', 'waka')->count(),
        ];

        $siswaList = Siswa::with('kelas')->where('status_aktif', true)->orderBy('nama_lengkap')->get(['id_siswa', 'nama_lengkap', 'nisn', 'id_kelas']);

        return view('admin.master.user', compact(
            'userList',
            'totalUser', 'totalActive', 'totalAdmin', 'totalGuru', 'totalStaff', 'totalWaliMurid',
            'roleCounts', 'siswaList',
            'search', 'role', 'status'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:admin,wali_kelas,guru_mapel,guru_piket,kepala_sekolah,waka,waka_sdm,waka_kurikulum,wali_murid,satpam',
            'is_active' => 'nullable|boolean',
            'nip'       => 'nullable|string|max:30',
            'no_hp'     => 'nullable|string|max:20',
            'id_siswa'  => 'nullable|exists:siswa,id_siswa',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'role'      => $validated['role'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            $nip = $validated['nip'] ?? ('USER-' . time());
            $noHp = $validated['no_hp'] ?? null;

            // Link to role table if relevant
            switch ($validated['role']) {
                case 'admin':
                    Admin::create([
                        'user_id'      => $user->id,
                        'nip'          => $nip,
                        'nama_lengkap' => $validated['name'],
                        'no_hp'        => $noHp,
                    ]);
                    break;

                case 'wali_kelas':
                    WaliKelas::create([
                        'user_id'      => $user->id,
                        'nip'          => $nip,
                        'nama_lengkap' => $validated['name'],
                        'jenis_kelamin'=> 'L',
                        'no_hp'        => $noHp,
                    ]);
                    Guru::firstOrCreate(
                        ['nip' => $nip],
                        [
                            'user_id'      => $user->id,
                            'nama_lengkap' => $validated['name'],
                            'jenis_kelamin'=> 'L',
                            'no_hp'        => $noHp,
                            'status_aktif' => true,
                        ]
                    );
                    break;

                case 'guru_piket':
                    GuruPiket::create([
                        'user_id'      => $user->id,
                        'nip'          => $nip,
                        'nama_lengkap' => $validated['name'],
                        'jenis_kelamin'=> 'L',
                        'no_hp'        => $noHp,
                        'hari_piket'   => 'Senin',
                    ]);
                    Guru::firstOrCreate(
                        ['nip' => $nip],
                        [
                            'user_id'      => $user->id,
                            'nama_lengkap' => $validated['name'],
                            'jenis_kelamin'=> 'L',
                            'no_hp'        => $noHp,
                            'status_aktif' => true,
                        ]
                    );
                    break;

                case 'guru_mapel':
                    GuruMapel::create([
                        'user_id'      => $user->id,
                        'nip'          => $nip,
                        'nama_lengkap' => $validated['name'],
                        'jenis_kelamin'=> 'L',
                        'no_hp'        => $noHp,
                    ]);
                    Guru::firstOrCreate(
                        ['nip' => $nip],
                        [
                            'user_id'      => $user->id,
                            'nama_lengkap' => $validated['name'],
                            'jenis_kelamin'=> 'L',
                            'no_hp'        => $noHp,
                            'status_aktif' => true,
                        ]
                    );
                    break;

                case 'kepala_sekolah':
                    KepalaSekolah::create([
                        'user_id'         => $user->id,
                        'nip'             => $nip,
                        'nama_lengkap'    => $validated['name'],
                        'jenis_kelamin'   => 'L',
                        'no_hp'           => $noHp,
                        'periode_jabatan' => date('Y') . '-' . (date('Y') + 4),
                    ]);
                    break;

                case 'waka':
                case 'waka_kurikulum':
                    Waka::create([
                        'user_id'      => $user->id,
                        'nip'          => $nip,
                        'nama_lengkap' => $validated['name'],
                        'jenis_kelamin'=> 'L',
                        'no_hp'        => $noHp,
                        'bidang'       => 'Kurikulum',
                    ]);
                    break;

                case 'waka_sdm':
                    Waka::create([
                        'user_id'      => $user->id,
                        'nip'          => $nip,
                        'nama_lengkap' => $validated['name'],
                        'jenis_kelamin'=> 'L',
                        'no_hp'        => $noHp,
                        'bidang'       => 'SDM',
                    ]);
                    break;

                case 'wali_murid':
                    if (!empty($validated['id_siswa'])) {
                        $siswa = Siswa::find($validated['id_siswa']);
                        if ($siswa) {
                            $siswa->update(['user_id' => $user->id]);
                        }
                    }
                    break;

                case 'satpam':
                    Satpam::create([
                        'user_id'      => $user->id,
                        'nip'          => $nip,
                        'nama_lengkap' => $validated['name'],
                        'jenis_kelamin'=> 'L',
                        'no_hp'        => $noHp,
                        'pos_jaga'     => 'Gerbang Utama',
                    ]);
                    break;
            }

            DB::commit();

            LogAktivitas::catat(
                'Tambah User',
                "Admin menambahkan akun pengguna baru: {$user->name} (" . ucfirst(str_replace('_', ' ', $user->role)) . ")",
                $user
            );

            return redirect()->route('admin.master.user')->with('success', "Pengguna {$validated['name']} berhasil ditambahkan ke database.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => "required|email|max:255|unique:users,email,{$user->id}",
            'password'  => 'nullable|string|min:6',
            'role'      => 'required|in:admin,wali_kelas,guru_mapel,guru_piket,kepala_sekolah,waka,waka_sdm,waka_kurikulum,wali_murid,satpam',
            'is_active' => 'nullable|boolean',
            'no_hp'     => 'nullable|string|max:20',
            'id_siswa'  => 'nullable|exists:siswa,id_siswa',
        ]);

        $updateData = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if ($validated['role'] === 'wali_murid' && !empty($validated['id_siswa'])) {
            // unlink previous student if any
            Siswa::where('user_id', $user->id)->where('id_siswa', '!=', $validated['id_siswa'])->update(['user_id' => null]);
            $siswa = Siswa::find($validated['id_siswa']);
            if ($siswa) {
                $siswa->update(['user_id' => $user->id]);
            }
        }

        $cleanName = trim(preg_replace('/\s*\([^)]*\)$/', '', $validated['name']));

        // Update related profile name if exists
        if ($user->guru) {
            $user->guru->update(['nama_lengkap' => $cleanName]);
        }
        if ($user->admin) {
            $user->admin->update(['nama_lengkap' => $cleanName]);
        }
        if ($user->kepalaSekolah) {
            $user->kepalaSekolah->update(['nama_lengkap' => $cleanName]);
        }
        if ($user->waka) {
            $bidang = ($validated['role'] === 'waka_sdm') ? 'SDM' : 'Kurikulum';
            $user->waka->update(['nama_lengkap' => $cleanName, 'bidang' => $bidang]);
        }
        if ($user->satpam) {
            $user->satpam->update(['nama_lengkap' => $cleanName]);
        }
        if ($user->waliKelas) {
            $user->waliKelas->update(['nama_lengkap' => $cleanName]);
        }
        if ($user->guruPiket) {
            $user->guruPiket->update(['nama_lengkap' => $cleanName]);
        }

        LogAktivitas::catat(
            'Update User',
            "Admin memperbarui data akun pengguna: {$user->name} (" . ucfirst(str_replace('_', ' ', $user->role)) . ")",
            $user
        );

        return redirect()->route('admin.master.user')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan saat ini.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        LogAktivitas::catat(
            'Status User',
            "Admin mengubah status akun {$user->name} menjadi " . ($user->is_active ? 'Aktif' : 'Nonaktif'),
            $user
        );

        return redirect()->back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login!');
        }

        $name = $user->name;
        $role = $user->role;
        $user->delete();

        LogAktivitas::catat(
            'Hapus User',
            "Admin menghapus akun pengguna: {$name} (" . ucfirst(str_replace('_', ' ', $role)) . ")",
            'User'
        );

        return redirect()->route('admin.master.user')->with('success', "Pengguna {$name} berhasil dihapus dari database.");
    }
}
