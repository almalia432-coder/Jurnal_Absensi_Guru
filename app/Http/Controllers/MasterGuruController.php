<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\User;
use App\Models\WaliKelas;
use App\Models\GuruPiket;
use App\Models\GuruMapel;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterGuruController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $role   = $request->input('role');

        $query = Guru::with(['user.waliKelas.kelas', 'user.guruPiket', 'user.guruMapel'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('nama_lengkap', 'LIKE', "%{$search}%")
                       ->orWhere('nip', 'LIKE', "%{$search}%");
                });
            })
            ->when($status !== null && $status !== '', function ($q) use ($status) {
                $q->where('status_aktif', (bool) $status);
            })
            ->when($role && $role !== 'all', function ($q) use ($role) {
                $q->whereHas('user', function ($qu) use ($role) {
                    $qu->where('role', $role);
                });
            });

        $guruList = $query->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        // Statistics
        $totalGuru       = Guru::count();
        $totalWaliKelas  = User::where('role', 'wali_kelas')->count();
        $totalGuruMapel  = User::where('role', 'guru_mapel')->count();
        $totalGuruPiket  = User::where('role', 'guru_piket')->count();
        $aktif           = Guru::where('status_aktif', true)->count();
        $nonAktif        = Guru::where('status_aktif', false)->count();

        // All classes for assigning Wali Kelas
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('admin.master.guru', compact(
            'guruList', 'kelasList',
            'totalGuru', 'totalWaliKelas', 'totalGuruMapel', 'totalGuruPiket',
            'aktif', 'nonAktif',
            'search', 'status', 'role'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'           => 'required|string|max:30|unique:guru,nip',
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'role'          => 'required|in:guru_mapel,wali_kelas,guru_piket',
            'id_kelas'      => 'nullable|exists:kelas,id_kelas',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'email'         => 'nullable|email|unique:users,email',
            'password'      => 'nullable|string|min:6',
            'status_aktif'  => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Auto generate or use provided email
            $email = $request->filled('email') 
                ? $request->input('email') 
                : (strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['nip'])) . '@smkn1boyolangu.sch.id');

            // 1. Create or retrieve User account
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'name'      => $validated['nama_lengkap'],
                    'email'     => $email,
                    'password'  => Hash::make($request->input('password', 'password')),
                    'role'      => $validated['role'],
                    'is_active' => $request->boolean('status_aktif', true),
                ]);
            } else {
                $user->update([
                    'name' => $validated['nama_lengkap'],
                    'role' => $validated['role'],
                ]);
            }

            // 2. Create Guru record
            $guru = Guru::create([
                'user_id'       => $user->id,
                'nip'           => $validated['nip'],
                'nama_lengkap'  => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'no_hp'         => $validated['no_hp'] ?? null,
                'alamat'        => $validated['alamat'] ?? null,
                'status_aktif'  => $request->boolean('status_aktif', true),
            ]);

            // 3. Sync Role specific tables
            $this->syncRoleTables($user, $validated, $request->input('id_kelas'));

            DB::commit();

            LogAktivitas::catat(
                'Tambah Guru',
                "Admin menambahkan master guru baru: {$validated['nama_lengkap']} (NIP: {$validated['nip']})",
                $guru
            );

            $roleLabel = $this->getRoleLabel($validated['role']);
            return redirect()->route('admin.master.guru')
                ->with('success', "Guru {$validated['nama_lengkap']} berhasil ditambahkan dengan jabatan {$roleLabel}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.master.guru')
                ->with('error', "Gagal menambahkan data guru: " . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nip'           => "required|string|max:30|unique:guru,nip,{$guru->id_guru},id_guru",
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'role'          => 'required|in:guru_mapel,wali_kelas,guru_piket',
            'id_kelas'      => 'nullable|exists:kelas,id_kelas',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'email'         => 'nullable|email|unique:users,email,' . ($guru->user_id ?? 0),
            'password'      => 'nullable|string|min:6',
            'status_aktif'  => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Guru Table
            $guru->update([
                'nip'           => $validated['nip'],
                'nama_lengkap'  => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'no_hp'         => $validated['no_hp'] ?? $guru->no_hp,
                'alamat'        => $validated['alamat'] ?? $guru->alamat,
                'status_aktif'  => $request->boolean('status_aktif', true),
            ]);

            // 2. Update or Create User
            $user = $guru->user;
            if ($user) {
                $userUpdates = [
                    'name'      => $validated['nama_lengkap'],
                    'role'      => $validated['role'],
                    'is_active' => $request->boolean('status_aktif', true),
                ];
                if ($request->filled('email')) {
                    $userUpdates['email'] = $request->input('email');
                }
                if ($request->filled('password')) {
                    $userUpdates['password'] = Hash::make($request->input('password'));
                }
                $user->update($userUpdates);
            } else {
                $email = $request->filled('email')
                    ? $request->input('email')
                    : (strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['nip'])) . '@smkn1boyolangu.sch.id');

                $user = User::create([
                    'name'      => $validated['nama_lengkap'],
                    'email'     => $email,
                    'password'  => Hash::make($request->input('password', 'password')),
                    'role'      => $validated['role'],
                    'is_active' => $request->boolean('status_aktif', true),
                ]);

                $guru->update(['user_id' => $user->id]);
            }

            // 3. Sync Role specific tables
            $this->syncRoleTables($user, $validated, $request->input('id_kelas'));

            DB::commit();

            LogAktivitas::catat(
                'Update Guru',
                "Admin memperbarui data master guru: {$guru->nama_lengkap} (NIP: {$guru->nip})",
                $guru
            );

            $roleLabel = $this->getRoleLabel($validated['role']);
            return redirect()->route('admin.master.guru')
                ->with('success', "Data guru {$guru->nama_lengkap} (Jabatan: {$roleLabel}) berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.master.guru')
                ->with('error', "Gagal memperbarui data guru: " . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $nama = $guru->nama_lengkap;
        $nip  = $guru->nip;

        if ($guru->user) {
            $guru->user->delete();
        }
        $guru->delete();

        LogAktivitas::catat(
            'Hapus Guru',
            "Admin menghapus data guru: {$nama} (NIP: {$nip})",
            'Guru'
        );

        return redirect()->route('admin.master.guru')
            ->with('success', "Guru {$nama} berhasil dihapus (soft delete).");
    }

    /**
     * Helper to sync role specific models (WaliKelas, GuruPiket, GuruMapel)
     */
    private function syncRoleTables(User $user, array $validated, ?int $idKelas)
    {
        $nip = $validated['nip'];
        $nama = $validated['nama_lengkap'];
        $jk = $validated['jenis_kelamin'];
        $noHp = $validated['no_hp'] ?? null;
        $role = $validated['role'];

        switch ($role) {
            case 'wali_kelas':
                $wali = WaliKelas::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip'          => $nip,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin'=> $jk,
                        'no_hp'        => $noHp,
                        'status_aktif' => true,
                    ]
                );

                if ($idKelas) {
                    // Remove previous class assignment if any
                    Kelas::where('wali_kelas_id', $wali->id)->where('id_kelas', '!=', $idKelas)->update(['wali_kelas_id' => null]);
                    // Assign to new class
                    Kelas::where('id_kelas', $idKelas)->update(['wali_kelas_id' => $wali->id]);
                }
                break;

            case 'guru_piket':
                GuruPiket::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip'          => $nip,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin'=> $jk,
                        'no_hp'        => $noHp,
                        'hari_piket'   => 'Senin',
                        'status_aktif' => true,
                    ]
                );
                break;

            case 'guru_mapel':
                GuruMapel::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip'          => $nip,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin'=> $jk,
                        'no_hp'        => $noHp,
                        'status_aktif' => true,
                    ]
                );
                break;
        }
    }

    private function getRoleLabel(string $role): string
    {
        return match ($role) {
            'wali_kelas' => 'Guru Wali Kelas',
            'guru_piket' => 'Guru Piket',
            default      => 'Guru Mata Pelajaran',
        };
    }
}
