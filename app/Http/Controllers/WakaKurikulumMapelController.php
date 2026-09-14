<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;
use App\Models\JadwalPelajaran;
use App\Models\JurnalMengajar;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class WakaKurikulumMapelController extends Controller
{
    /**
     * Tampilkan daftar seluruh Mata Pelajaran dengan statistik pengajar
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $search   = $request->input('search');
        $kelompok = $request->input('kelompok');

        $query = Mapel::with(['jadwalPelajaran.guru', 'jadwalPelajaran.kelas'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('kode_mapel', 'LIKE', "%{$search}%")
                       ->orWhere('nama_mapel', 'LIKE', "%{$search}%");
                });
            })
            ->when($kelompok && $kelompok !== 'all', function ($q) use ($kelompok) {
                $q->where('kelompok', $kelompok);
            });

        $mapelList = $query->orderBy('kelompok')->orderBy('nama_mapel')->paginate(12)->withQueryString();

        // Hitung statistik guru pengampu dan kelas per mapel
        $mapelList->getCollection()->transform(function ($mapel) {
            $distinctGuru = $mapel->jadwalPelajaran->pluck('guru')->filter()->unique('id_guru')->values();
            $distinctKelas = $mapel->jadwalPelajaran->pluck('kelas')->filter()->unique('id_kelas')->values();
            $totalJp = $mapel->jadwalPelajaran->count();

            $mapel->assigned_guru = $distinctGuru;
            $mapel->assigned_kelas = $distinctKelas;
            $mapel->total_jp = $totalJp;

            return $mapel;
        });

        // Statistik kelompok mapel
        $totalMapel     = Mapel::count();
        $totalNormatif  = Mapel::where('kelompok', 'Normatif')->count();
        $totalAdaptif   = Mapel::where('kelompok', 'Adaptif')->count();
        $totalProduktif = Mapel::where('kelompok', 'Produktif')->count();
        $totalMulok     = Mapel::where('kelompok', 'Muatan_Lokal')->count();

        $kelompokCounts = [
            'all'          => $totalMapel,
            'Normatif'     => $totalNormatif,
            'Adaptif'      => $totalAdaptif,
            'Produktif'    => $totalProduktif,
            'Muatan_Lokal' => $totalMulok,
        ];

        return view('waka_kurikulum.mapel.index', compact(
            'user', 'waka', 'mapelList',
            'totalMapel', 'totalNormatif', 'totalAdaptif', 'totalProduktif', 'totalMulok',
            'kelompokCounts', 'search', 'kelompok'
        ));
    }

    /**
     * Tambah Mata Pelajaran baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mapel,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'kelompok'   => 'required|in:Normatif,Adaptif,Produktif,Muatan_Lokal',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique'   => 'Kode mata pelajaran sudah terdaftar.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'kelompok.required'   => 'Kelompok mata pelajaran wajib dipilih.',
        ]);

        Mapel::create($validated);

        return redirect()->route('waka-kurikulum.mapel.index')
            ->with('success', "Mata pelajaran '{$validated['nama_mapel']}' ({$validated['kode_mapel']}) berhasil ditambahkan ke kurikulum.");
    }

    /**
     * Perbarui Mata Pelajaran
     */
    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'kode_mapel' => "required|string|max:20|unique:mapel,kode_mapel,{$mapel->id_mapel},id_mapel",
            'nama_mapel' => 'required|string|max:255',
            'kelompok'   => 'required|in:Normatif,Adaptif,Produktif,Muatan_Lokal',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique'   => 'Kode mata pelajaran sudah digunakan oleh mapel lain.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'kelompok.required'   => 'Kelompok mata pelajaran wajib dipilih.',
        ]);

        $mapel->update($validated);

        return redirect()->route('waka-kurikulum.mapel.index')
            ->with('success', "Data mata pelajaran '{$mapel->nama_mapel}' berhasil diperbarui.");
    }

    /**
     * Hapus Mata Pelajaran (dengan proteksi integritas data)
     */
    public function destroy($id)
    {
        $mapel = Mapel::withCount(['jadwalPelajaran', 'jurnalMengajar'])->findOrFail($id);

        if ($mapel->jurnal_mengajar_count > 0) {
            return redirect()->route('waka-kurikulum.mapel.index')
                ->with('error', "Mata pelajaran '{$mapel->nama_mapel}' tidak dapat dihapus karena sudah memiliki {$mapel->jurnal_mengajar_count} catatan riwayat jurnal mengajar.");
        }

        if ($mapel->jadwal_pelajaran_count > 0) {
            return redirect()->route('waka-kurikulum.mapel.index')
                ->with('error', "Mata pelajaran '{$mapel->nama_mapel}' tidak dapat dihapus karena sedang aktif digunakan dalam {$mapel->jadwal_pelajaran_count} jadwal pelajaran. Silakan hapus/pindahkan jadwal terlebih dahulu.");
        }

        $nama = $mapel->nama_mapel;
        $mapel->delete();

        return redirect()->route('waka-kurikulum.mapel.index')
            ->with('success', "Mata pelajaran '{$nama}' berhasil dihapus dari kurikulum.");
    }

    /**
     * Detail guru pengampu dan kelas untuk suatu mapel (JSON API / Modal data)
     */
    public function detail($id)
    {
        $mapel = Mapel::with(['jadwalPelajaran.guru', 'jadwalPelajaran.kelas'])->findOrFail($id);

        $guruList = $mapel->jadwalPelajaran
            ->groupBy('id_guru')
            ->map(function ($jadwals) {
                $guru = $jadwals->first()->guru;
                $kelasList = $jadwals->pluck('kelas.nama_kelas')->unique()->values();
                $totalJp = $jadwals->count();

                return [
                    'id_guru'      => $guru->id_guru ?? null,
                    'nama_lengkap' => $guru->nama_lengkap ?? 'Belum ditentukan',
                    'nip'          => $guru->nip ?? '-',
                    'kelas'        => $kelasList,
                    'total_jp'     => $totalJp,
                ];
            })->values();

        return response()->json([
            'mapel' => [
                'id_mapel'   => $mapel->id_mapel,
                'kode_mapel' => $mapel->kode_mapel,
                'nama_mapel' => $mapel->nama_mapel,
                'kelompok'   => $mapel->kelompok,
            ],
            'pengajar' => $guruList,
            'total_guru' => $guruList->count(),
            'total_jp'   => $mapel->jadwalPelajaran->count(),
        ]);
    }
}
