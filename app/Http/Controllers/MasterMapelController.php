<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;
use App\Models\JurnalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\LogAktivitas;

class MasterMapelController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $kelompok = $request->input('kelompok');

        $query = Mapel::withCount(['jadwalPelajaran', 'jurnalMengajar'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $kode_mapel = $search;
                    $q2->where('kode_mapel', 'LIKE', "%{$search}%")
                       ->orWhere('nama_mapel', 'LIKE', "%{$search}%");
                });
            })
            ->when($kelompok && $kelompok !== 'all', function ($q) use ($kelompok) {
                $q->where('kelompok', $kelompok);
            });

        $mapelList = $query->orderBy('kelompok')->orderBy('nama_mapel')->paginate(15)->withQueryString();

        // Statistics
        $totalMapel    = Mapel::count();
        $totalNormatif = Mapel::where('kelompok', 'Normatif')->count();
        $totalAdaptif  = Mapel::where('kelompok', 'Adaptif')->count();
        $totalProduktif= Mapel::where('kelompok', 'Produktif')->count();
        $totalMulok    = Mapel::where('kelompok', 'Muatan_Lokal')->count();

        $kelompokCounts = [
            'all'          => $totalMapel,
            'Normatif'     => $totalNormatif,
            'Adaptif'      => $totalAdaptif,
            'Produktif'    => $totalProduktif,
            'Muatan_Lokal' => $totalMulok,
        ];

        return view('admin.master.mapel', compact(
            'mapelList',
            'totalMapel', 'totalNormatif', 'totalAdaptif', 'totalProduktif', 'totalMulok',
            'kelompokCounts',
            'search', 'kelompok'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mapel,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'kelompok'   => 'required|in:Normatif,Adaptif,Produktif,Muatan_Lokal',
        ]);

        $mapel = Mapel::create($validated);

        LogAktivitas::catat(
            'Tambah Mapel',
            "Admin menambahkan mata pelajaran baru: {$mapel->nama_mapel} ({$mapel->kode_mapel})",
            $mapel
        );

        return redirect()->route('admin.master.mapel')
            ->with('success', "Mata Pelajaran {$validated['nama_mapel']} ({$validated['kode_mapel']}) berhasil ditambahkan ke database.");
    }

    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'kode_mapel' => "required|string|max:20|unique:mapel,kode_mapel,{$mapel->id_mapel},id_mapel",
            'nama_mapel' => 'required|string|max:255',
            'kelompok'   => 'required|in:Normatif,Adaptif,Produktif,Muatan_Lokal',
        ]);

        $mapel->update($validated);

        LogAktivitas::catat(
            'Update Mapel',
            "Admin memperbarui data mata pelajaran: {$mapel->nama_mapel}",
            $mapel
        );

        return redirect()->route('admin.master.mapel')
            ->with('success', "Data mata pelajaran {$mapel->nama_mapel} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $mapel = Mapel::withCount(['jadwalPelajaran', 'jurnalMengajar'])->findOrFail($id);

        if ($mapel->jurnal_mengajar_count > 0) {
            return redirect()->route('admin.master.mapel')
                ->with('error', "Mata pelajaran {$mapel->nama_mapel} tidak dapat dihapus karena telah tercatat dalam {$mapel->jurnal_mengajar_count} riwayat jurnal mengajar.");
        }

        if ($mapel->jadwal_pelajaran_count > 0) {
            return redirect()->route('admin.master.mapel')
                ->with('error', "Mata pelajaran {$mapel->nama_mapel} tidak dapat dihapus karena masih aktif digunakan dalam {$mapel->jadwal_pelajaran_count} jadwal pelajaran.");
        }

        $nama = $mapel->nama_mapel;
        $mapel->delete();

        LogAktivitas::catat(
            'Hapus Mapel',
            "Admin menghapus mata pelajaran: {$nama}",
            'Mapel'
        );

        return redirect()->route('admin.master.mapel')
            ->with('success', "Mata pelajaran {$nama} berhasil dihapus dari database.");
    }
}
