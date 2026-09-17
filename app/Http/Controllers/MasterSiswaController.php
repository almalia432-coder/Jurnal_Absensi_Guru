<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use Carbon\Carbon;

class MasterSiswaController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $kelasId    = $request->input('id_kelas');
        $status     = $request->input('status');

        $query = Siswa::with('kelas')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('nama_lengkap', 'LIKE', "%{$search}%")
                       ->orWhere('nis', 'LIKE', "%{$search}%")
                       ->orWhere('nisn', 'LIKE', "%{$search}%")
                       ->orWhere('niss', 'LIKE', "%{$search}%");
                });
            })
            ->when($kelasId, fn($q) => $q->where('id_kelas', $kelasId))
            ->when($status !== null && $status !== '', function ($q) use ($status) {
                $q->where('status_aktif', (bool) $status);
            });

        $siswaList  = $query->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        $totalSiswa  = Siswa::count();
        $aktif       = Siswa::where('status_aktif', true)->count();
        $nonAktif    = Siswa::where('status_aktif', false)->count();
        $kelasList   = Kelas::orderBy('nama_kelas')->get();

        return view('admin.master.siswa', compact(
            'siswaList', 'kelasList',
            'totalSiswa', 'aktif', 'nonAktif',
            'search', 'kelasId', 'status'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'           => 'nullable|string|max:30|unique:siswa,nis',
            'nisn'          => 'nullable|string|max:30|unique:siswa,nisn',
            'niss'          => 'nullable|string|max:30',
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'id_kelas'      => 'required|exists:kelas,id_kelas',
            'no_hp_ortu'    => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'status_aktif'  => 'boolean',
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif', true);
        if (empty($validated['nis']) && !empty($validated['nisn'])) {
            $validated['nis'] = $validated['nisn'];
        }
        $siswa = Siswa::create($validated);

        LogAktivitas::catat(
            'Tambah Siswa',
            "Admin menambahkan data siswa baru: {$siswa->nama_lengkap} (" . ($siswa->kelas->nama_kelas ?? 'Siswa') . ")",
            $siswa
        );

        return redirect()->route('admin.master.siswa')->with('success', "Siswa {$validated['nama_lengkap']} berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nis'           => "nullable|string|max:30|unique:siswa,nis,{$siswa->id_siswa},id_siswa",
            'nisn'          => "nullable|string|max:30|unique:siswa,nisn,{$siswa->id_siswa},id_siswa",
            'niss'          => 'nullable|string|max:30',
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'id_kelas'      => 'required|exists:kelas,id_kelas',
            'no_hp_ortu'    => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'status_aktif'  => 'boolean',
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif', true);
        if (empty($validated['nis']) && !empty($validated['nisn'])) {
            $validated['nis'] = $validated['nisn'];
        }
        $siswa->update($validated);

        LogAktivitas::catat(
            'Update Siswa',
            "Admin memperbarui data siswa: {$siswa->nama_lengkap} (" . ($siswa->kelas->nama_kelas ?? 'Siswa') . ")",
            $siswa
        );

        return redirect()->route('admin.master.siswa')->with('success', "Data siswa {$siswa->nama_lengkap} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $nama = $siswa->nama_lengkap;
        $siswa->delete();

        LogAktivitas::catat(
            'Hapus Siswa',
            "Admin menghapus data siswa: {$nama}",
            'Siswa'
        );

        return redirect()->route('admin.master.siswa')->with('success', "Siswa {$nama} berhasil dihapus (soft delete).");
    }
}
