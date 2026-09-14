<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\JurnalMengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WakaKurikulumGuruMengajarController extends Controller
{
    /**
     * Daftar Guru Pengajar beserta Rekap Beban Jam Mengajar (JP) Mingguan
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $search       = $request->input('search');
        $statusBeban  = $request->input('status_beban'); // 'all', 'memenuhi', 'kurang', 'kosong', 'tinggi'
        $idMapel      = $request->input('id_mapel');

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // Query seluruh guru aktif beserta relasi jadwal, mapel, dan kelas
        $query = Guru::where('status_aktif', true)
            ->with([
                'user',
                'jadwalPelajaran' => function ($q) use ($tahunAjaranAktif) {
                    if ($tahunAjaranAktif) {
                        $q->where('id_tahun_ajaran', $tahunAjaranAktif->id);
                    }
                    $q->with(['mapel', 'kelas']);
                }
            ])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qs) use ($search) {
                    $qs->where('nama_lengkap', 'LIKE', "%{$search}%")
                       ->orWhere('nip', 'LIKE', "%{$search}%");
                });
            })
            ->when($idMapel, function ($q) use ($idMapel) {
                $q->whereHas('jadwalPelajaran', function ($qj) use ($idMapel) {
                    $qj->where('id_mapel', $idMapel);
                });
            });

        $allGuru = $query->orderBy('nama_lengkap')->get();

        // Hitung beban jam (JP), mapel yang diampu, dan kelas yang diajar
        $transformedGuru = $allGuru->map(function ($guru) {
            $jadwals = $guru->jadwalPelajaran;
            $totalJp = $jadwals->count();

            // Mapel yang diajar beserta alokasi JP per mapel
            $mapelSummary = $jadwals->groupBy('id_mapel')->map(function ($group) {
                $first = $group->first();
                return [
                    'id_mapel'   => $first->id_mapel,
                    'nama_mapel' => $first->mapel->nama_mapel ?? '-',
                    'kode_mapel' => $first->mapel->kode_mapel ?? '-',
                    'kelompok'   => $first->mapel->kelompok ?? 'Normatif',
                    'jp'         => $group->count(),
                ];
            })->values();

            // Kelas yang diajar
            $kelasSummary = $jadwals->pluck('kelas')->filter()->unique('id_kelas')->values();

            // Kategori status beban kerja (standar Permendikbud: 24 - 40 JP/minggu)
            if ($totalJp === 0) {
                $bebanStatus = 'kosong';
                $statusLabel = 'Belum Ada Jam';
                $statusBadge = 'secondary';
            } elseif ($totalJp < 24) {
                $bebanStatus = 'kurang';
                $statusLabel = "Kurang Jam ({$totalJp}/24 JP)";
                $statusBadge = 'warning';
            } elseif ($totalJp > 32) {
                $bebanStatus = 'tinggi';
                $statusLabel = "Beban Tinggi ({$totalJp} JP)";
                $statusBadge = 'primary';
            } else {
                $bebanStatus = 'memenuhi';
                $statusLabel = "Memenuhi Standar ({$totalJp} JP)";
                $statusBadge = 'success';
            }

            $guru->total_jp       = $totalJp;
            $guru->mapel_summary  = $mapelSummary;
            $guru->kelas_summary  = $kelasSummary;
            $guru->beban_status   = $bebanStatus;
            $guru->status_label   = $statusLabel;
            $guru->status_badge   = $statusBadge;

            return $guru;
        });

        // Filter koleksi berdasarkan status beban jika dipilih
        if ($statusBeban && $statusBeban !== 'all') {
            $filteredGuru = $transformedGuru->filter(fn($g) => $g->beban_status === $statusBeban)->values();
        } else {
            $filteredGuru = $transformedGuru;
        }

        // Statistik Keseluruhan Guru
        $totalGuruCount    = $transformedGuru->count();
        $memenuhiCount     = $transformedGuru->where('beban_status', 'memenuhi')->count();
        $tinggiCount       = $transformedGuru->where('beban_status', 'tinggi')->count();
        $kurangCount       = $transformedGuru->where('beban_status', 'kurang')->count();
        $kosongCount       = $transformedGuru->where('beban_status', 'kosong')->count();
        $totalJpSekolah    = $transformedGuru->sum('total_jp');
        $avgJpSekolah      = $totalGuruCount > 0 ? round($totalJpSekolah / $totalGuruCount, 1) : 0;

        // Data pendukung form plotting & filter
        $mapelList = Mapel::with(['jadwalPelajaran'])->orderBy('nama_mapel')->get();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $guruList  = Guru::where('status_aktif', true)->orderBy('nama_lengkap')->get();

        // Pagination manual untuk koleksi yang difilter (15 per halaman)
        $perPage = 12;
        $page = $request->input('page', 1);
        $paginatedGuru = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredGuru->forPage($page, $perPage),
            $filteredGuru->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('waka_kurikulum.guru_mengajar.index', compact(
            'user', 'waka', 'paginatedGuru', 'tahunAjaranAktif',
            'totalGuruCount', 'memenuhiCount', 'tinggiCount', 'kurangCount', 'kosongCount',
            'totalJpSekolah', 'avgJpSekolah',
            'mapelList', 'kelasList', 'guruList',
            'search', 'statusBeban', 'idMapel'
        ));
    }

    /**
     * Detail Beban Mengajar & Jadwal Mingguan Guru
     */
    public function detail($id)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $guru = Guru::with(['user'])->findOrFail($id);

        // Jadwal guru di tahun ajaran aktif
        $jadwalList = JadwalPelajaran::with(['mapel', 'kelas'])
            ->where('id_guru', $guru->id_guru)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->orderBy('hari')
            ->orderBy('jam_ke')
            ->get();

        // Kelompokkan jadwal berdasarkan hari
        $jadwalPerHari = [
            'Senin'  => $jadwalList->where('hari', 'Senin')->values(),
            'Selasa' => $jadwalList->where('hari', 'Selasa')->values(),
            'Rabu'   => $jadwalList->where('hari', 'Rabu')->values(),
            'Kamis'  => $jadwalList->where('hari', 'Kamis')->values(),
            'Jumat'  => $jadwalList->where('hari', 'Jumat')->values(),
            'Sabtu'  => $jadwalList->where('hari', 'Sabtu')->values(),
        ];

        // Ringkasan per mapel
        $mapelSummary = $jadwalList->groupBy('id_mapel')->map(function ($items) {
            $first = $items->first();
            return [
                'mapel' => $first->mapel->nama_mapel ?? '-',
                'kode'  => $first->mapel->kode_mapel ?? '-',
                'kelas' => $items->pluck('kelas.nama_kelas')->filter()->unique()->values()->all(),
                'jp'    => $items->count(),
            ];
        })->values();

        // Ringkasan per kelas
        $kelasSummary = $jadwalList->groupBy('id_kelas')->map(function ($items) {
            $first = $items->first();
            return [
                'kelas' => $first->kelas->nama_kelas ?? '-',
                'mapel' => $items->pluck('mapel.nama_mapel')->filter()->unique()->values()->all(),
                'jp'    => $items->count(),
            ];
        })->values();

        $totalJp = $jadwalList->count();

        // Riwayat jurnal terkini yang diisi guru
        $recentJurnal = JurnalMengajar::with(['kelas', 'mapel'])
            ->where('id_guru', $guru->id_guru)
            ->orderByDesc('tanggal')
            ->take(10)
            ->get();

        return view('waka_kurikulum.guru_mengajar.detail', compact(
            'user', 'waka', 'guru', 'tahunAjaranAktif',
            'jadwalList', 'jadwalPerHari', 'mapelSummary', 'kelasSummary',
            'totalJp', 'recentJurnal'
        ));
    }

    /**
     * Plotting Penugasan Mengajar Guru (Tambah Jadwal Langsung)
     */
    public function storePlotting(Request $request)
    {
        $validated = $request->validate([
            'id_guru'    => 'required|exists:guru,id_guru',
            'id_mapel'   => 'required|exists:mapel,id_mapel',
            'id_kelas'   => 'required|exists:kelas,id_kelas',
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke'     => 'required|integer|min:1|max:12',
            'jam_mulai'  => 'nullable|date_format:H:i',
            'jam_selesai'=> 'nullable|date_format:H:i',
        ], [
            'id_guru.required'  => 'Guru pengajar wajib dipilih.',
            'id_mapel.required' => 'Mata pelajaran wajib dipilih.',
            'id_kelas.required' => 'Kelas yang diajar wajib dipilih.',
            'hari.required'     => 'Hari pembelajaran wajib dipilih.',
            'jam_ke.required'   => 'Jam pelajaran ke- wajib ditentukan.',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
        }

        $idTahunAjaran = $tahunAjaranAktif->id;
        $hari   = $validated['hari'];
        $jamKe  = (int) $validated['jam_ke'];

        // Jam default slot SMKN 1 Boyolangu jika tidak diisi manual
        $defaultTime = JadwalPelajaranController::getDefaultTimeSlot($hari, $jamKe);
        $jamMulai   = $validated['jam_mulai'] ?? $defaultTime['jam_mulai'];
        $jamSelesai = $validated['jam_selesai'] ?? $defaultTime['jam_selesai'];

        // 1. VALIDASI BENTROK KELAS: Apakah kelas ini sudah memiliki jadwal pada hari & jam_ke tersebut?
        $bentrokKelas = JadwalPelajaran::with(['mapel', 'guru'])
            ->where('id_tahun_ajaran', $idTahunAjaran)
            ->where('id_kelas', $validated['id_kelas'])
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->first();

        if ($bentrokKelas) {
            $guruNama = $bentrokKelas->guru->nama_lengkap ?? 'Guru Lain';
            $mapelNama = $bentrokKelas->mapel->nama_mapel ?? 'Mapel Lain';
            return back()->with('error', "Bentrok Jadwal Kelas: Kelas tersebut sudah memiliki jadwal '{$mapelNama}' bersama {$guruNama} pada hari {$hari} jam ke-{$jamKe}.");
        }

        // 2. VALIDASI BENTROK GURU: Apakah guru yang sama sedang mengajar di kelas lain pada hari & jam_ke tersebut?
        $bentrokGuru = JadwalPelajaran::with(['kelas', 'mapel'])
            ->where('id_tahun_ajaran', $idTahunAjaran)
            ->where('id_guru', $validated['id_guru'])
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->first();

        if ($bentrokGuru) {
            $kelasNama = $bentrokGuru->kelas->nama_kelas ?? 'Kelas Lain';
            return back()->with('error', "Bentrok Jadwal Guru: Guru tersebut sudah terjadwal mengajar di kelas '{$kelasNama}' pada hari {$hari} jam ke-{$jamKe}.");
        }

        // Buat jadwal baru
        JadwalPelajaran::create([
            'id_tahun_ajaran' => $idTahunAjaran,
            'hari'            => $hari,
            'jam_ke'          => $jamKe,
            'jam_mulai'       => $jamMulai,
            'jam_selesai'     => $jamSelesai,
            'id_mapel'        => $validated['id_mapel'],
            'id_guru'         => $validated['id_guru'],
            'id_kelas'        => $validated['id_kelas'],
        ]);

        $guru = Guru::find($validated['id_guru']);
        $mapel = Mapel::find($validated['id_mapel']);
        $kelas = Kelas::find($validated['id_kelas']);

        return back()->with('success', "Berhasil menugaskan {$guru->nama_lengkap} untuk mengampu '{$mapel->nama_mapel}' di {$kelas->nama_kelas} ({$hari}, Jam ke-{$jamKe}).");
    }

    /**
     * Hapus semua jadwal mengajar seorang guru pada tahun ajaran aktif (Reset Plotting)
     */
    public function clearJadwalGuru($id)
    {
        $guru = Guru::findOrFail($id);
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        if (!$tahunAjaranAktif) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif yang ditemukan.');
        }

        // Hanya hapus jadwal yang belum ada jurnal mengajar-nya
        $jadwalDenganJurnal = JadwalPelajaran::where('id_guru', $guru->id_guru)
            ->where('id_tahun_ajaran', $tahunAjaranAktif->id)
            ->withCount('jurnalMengajar')
            ->get();

        $tidakBisaDihapus = $jadwalDenganJurnal->where('jurnal_mengajar_count', '>', 0)->count();
        $bisaDihapus = $jadwalDenganJurnal->where('jurnal_mengajar_count', 0);

        if ($bisaDihapus->isEmpty()) {
            return back()->with('error', "Semua jadwal {$guru->nama_lengkap} sudah memiliki rekaman jurnal mengajar dan tidak dapat dihapus.");
        }

        $jumlahDihapus = $bisaDihapus->count();
        JadwalPelajaran::whereIn('id_jadwal', $bisaDihapus->pluck('id_jadwal'))->delete();

        $pesan = "Berhasil menghapus {$jumlahDihapus} slot jadwal mengajar {$guru->nama_lengkap}.";
        if ($tidakBisaDihapus > 0) {
            $pesan .= " ({$tidakBisaDihapus} jadwal tidak dihapus karena sudah memiliki rekaman jurnal.)";
        }

        return redirect()->route('waka-kurikulum.guru-mengajar.detail', $guru->id_guru)
            ->with('success', $pesan);
    }

    /**
     * Ekspor Rekap Beban Mengajar Guru sebagai file CSV
     */
    public function exportCsv(Request $request)
    {
        Carbon::setLocale('id');
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $allGuru = Guru::where('status_aktif', true)
            ->with([
                'jadwalPelajaran' => function ($q) use ($tahunAjaranAktif) {
                    if ($tahunAjaranAktif) {
                        $q->where('id_tahun_ajaran', $tahunAjaranAktif->id);
                    }
                    $q->with(['mapel', 'kelas']);
                }
            ])
            ->orderBy('nama_lengkap')
            ->get();

        $rows = [];
        $rows[] = [
            'No',
            'Nama Guru',
            'NIP',
            'Total JP / Minggu',
            'Status Standar',
            'Mata Pelajaran Diampu',
            'Kelas yang Diajar',
        ];

        foreach ($allGuru as $i => $guru) {
            $jadwals = $guru->jadwalPelajaran;
            $totalJp = $jadwals->count();

            if ($totalJp === 0)      $status = 'Belum Ada Jam (0 JP)';
            elseif ($totalJp < 24)   $status = "Kurang Jam ({$totalJp}/24 JP)";
            elseif ($totalJp > 32)   $status = "Beban Tinggi ({$totalJp} JP)";
            else                     $status = "Memenuhi Standar ({$totalJp} JP)";

            $mapelNamaList = $jadwals->groupBy('id_mapel')->map(function ($items) {
                $first = $items->first();
                return ($first->mapel->nama_mapel ?? '-') . ' (' . $items->count() . ' JP)';
            })->values()->implode(', ');

            $kelasNamaList = $jadwals->pluck('kelas.nama_kelas')->filter()->unique()->values()->implode(', ');

            $rows[] = [
                $i + 1,
                $guru->nama_lengkap,
                $guru->nip ?? '-',
                $totalJp,
                $status,
                $mapelNamaList ?: '-',
                $kelasNamaList ?: '-',
            ];
        }

        $tahunAjaranNama = $tahunAjaranAktif ? str_replace('/', '-', $tahunAjaranAktif->nama) : 'aktif';
        $filename = "rekap-beban-mengajar-{$tahunAjaranNama}.csv";

        $output = fopen('php://temp', 'r+');
        // BOM UTF-8 agar Excel bisa baca karakter Indonesia
        fwrite($output, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
