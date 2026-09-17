<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\WaliKelas;
use App\Models\Siswa;
use App\Models\LogAktivitas;

class MasterKelasController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $tingkat   = $request->input('tingkat');
        $jurusanId = $request->input('id_jurusan');

        $query = Kelas::with(['jurusanRelation', 'waliKelas'])
            ->withCount('siswa')
            ->when($search, function ($q) use ($search) {
                $q->where('nama_kelas', 'LIKE', "%{$search}%");
            })
            ->when($tingkat, fn($q) => $q->where('tingkat', $tingkat))
            ->when($jurusanId, fn($q) => $q->where('id_jurusan', $jurusanId));

        $kelasList = $query->orderBy('tingkat')->orderBy('nama_kelas')->paginate(15)->withQueryString();

        $totalKelas  = Kelas::count();
        $tingkatX    = Kelas::where('tingkat', 'X')->count();
        $tingkatXI   = Kelas::where('tingkat', 'XI')->count();
        $tingkatXII  = Kelas::where('tingkat', 'XII')->count();
        $jurusanList = Jurusan::where('status_aktif', true)->orderBy('kode_jurusan')->get();
        $waliList    = WaliKelas::where('status_aktif', true)->orderBy('nama_lengkap')->get();

        return view('admin.master.kelas', compact(
            'kelasList', 'jurusanList', 'waliList',
            'totalKelas', 'tingkatX', 'tingkatXI', 'tingkatXII',
            'search', 'tingkat', 'jurusanId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas'     => 'required|string|max:30|unique:kelas,nama_kelas',
            'tingkat'        => 'required|in:X,XI,XII',
            'id_jurusan'     => 'required|exists:jurusan,id_jurusan',
            'wali_kelas_id'  => 'nullable|exists:wali_kelas,id',
        ]);

        $jurusan = Jurusan::findOrFail($validated['id_jurusan']);

        $kelas = Kelas::create([
            'nama_kelas'    => $validated['nama_kelas'],
            'tingkat'       => $validated['tingkat'],
            'id_jurusan'    => $validated['id_jurusan'],
            'jurusan'       => $jurusan->kode_jurusan,
            'wali_kelas_id' => $validated['wali_kelas_id'] ?? null,
            'jumlah_siswa'  => 0,
        ]);

        LogAktivitas::catat(
            'Tambah Kelas',
            "Admin menambahkan rombel kelas baru: {$kelas->nama_kelas}",
            $kelas
        );

        return redirect()->route('admin.master.kelas')->with('success', "Kelas {$validated['nama_kelas']} berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas'     => "required|string|max:30|unique:kelas,nama_kelas,{$kelas->id_kelas},id_kelas",
            'tingkat'        => 'required|in:X,XI,XII',
            'id_jurusan'     => 'required|exists:jurusan,id_jurusan',
            'wali_kelas_id'  => 'nullable|exists:wali_kelas,id',
        ]);

        $jurusan = Jurusan::findOrFail($validated['id_jurusan']);

        $kelas->update([
            'nama_kelas'    => $validated['nama_kelas'],
            'tingkat'       => $validated['tingkat'],
            'id_jurusan'    => $validated['id_jurusan'],
            'jurusan'       => $jurusan->kode_jurusan,
            'wali_kelas_id' => $validated['wali_kelas_id'] ?? null,
            'jumlah_siswa'  => Siswa::where('id_kelas', $kelas->id_kelas)->count(),
        ]);

        LogAktivitas::catat(
            'Update Kelas',
            "Admin memperbarui data rombel kelas: {$kelas->nama_kelas}",
            $kelas
        );

        return redirect()->route('admin.master.kelas')->with('success', "Data kelas {$kelas->nama_kelas} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $kelas = Kelas::withCount('siswa')->findOrFail($id);

        if ($kelas->siswa_count > 0) {
            return redirect()->route('admin.master.kelas')
                ->with('error', "Kelas {$kelas->nama_kelas} tidak dapat dihapus karena masih memiliki {$kelas->siswa_count} siswa.");
        }

        $nama = $kelas->nama_kelas;
        $kelas->delete();

        LogAktivitas::catat(
            'Hapus Kelas',
            "Admin menghapus rombel kelas: {$nama}",
            'Kelas'
        );

        return redirect()->route('admin.master.kelas')->with('success', "Kelas {$nama} berhasil dihapus.");
    }
}
