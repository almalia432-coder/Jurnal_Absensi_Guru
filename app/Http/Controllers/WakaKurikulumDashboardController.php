<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Waka;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\JurnalMengajar;
use App\Models\PresensiSiswa;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WakaKurikulumDashboardController extends Controller
{
    /**
     * Dashboard Utama Waka Kurikulum
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('l, d F Y');
        $hariIni = Carbon::today()->translatedFormat('l');

        $hour = (int) Carbon::now()->format('H');
        if ($hour < 11) {
            $greetingText = 'Selamat pagi';
        } elseif ($hour < 15) {
            $greetingText = 'Selamat siang';
        } elseif ($hour < 18) {
            $greetingText = 'Selamat sore';
        } else {
            $greetingText = 'Selamat malam';
        }
        $namaUser = $user ? $user->name : 'Waka Kurikulum';

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // 1. KPI Metrics
        $totalKelas = Kelas::count();
        $totalMapel = Mapel::count();
        $totalGuru  = Guru::where('status_aktif', true)->count();

        // Statistik Kurikulum: Guru Pengajar & Beban JP
        $guruWithJadwal = Guru::where('status_aktif', true)
            ->withCount(['jadwalPelajaran' => function ($q) use ($tahunAjaranAktif) {
                if ($tahunAjaranAktif) {
                    $q->where('id_tahun_ajaran', $tahunAjaranAktif->id);
                }
            }])
            ->get();

        $totalGuruMengajar = $guruWithJadwal->where('jadwal_pelajaran_count', '>', 0)->count();
        $guruMemenuhiBeban = $guruWithJadwal->where('jadwal_pelajaran_count', '>=', 24)->count();
        $totalJpSekolah    = $guruWithJadwal->sum('jadwal_pelajaran_count');
        $avgJpPerGuru      = $totalGuruMengajar > 0 ? round($totalJpSekolah / $totalGuruMengajar, 1) : 0;

        // Target jadwal & jurnal hari ini
        $totalJadwalHariIni = JadwalPelajaran::where('hari', $hariIni)->count();
        $jurnalTerisiCount = JurnalMengajar::where('tanggal', $today)->count();
        $pctKbmBerjalan = $totalJadwalHariIni > 0 ? min(100, round(($jurnalTerisiCount / $totalJadwalHariIni) * 100)) : 0;

        // Guru terjadwal & mengajar hari ini
        $guruTerjadwalHariIniCount = JadwalPelajaran::where('hari', $hariIni)
            ->distinct('id_guru')
            ->count('id_guru');
        $guruMengajarHariIniCount = JurnalMengajar::where('tanggal', $today)
            ->distinct('id_guru')
            ->count('id_guru');

        // Presensi Siswa Hari Ini (dari seluruh jurnal mengajar)
        $presensiToday = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $today))->get();
        $totalPresensi = $presensiToday->count();
        $hadirCount = $presensiToday->where('status', 'Hadir')->count();
        $sakitCount = $presensiToday->where('status', 'Sakit')->count();
        $izinCount  = $presensiToday->where('status', 'Izin')->count();
        $alphaCount = $presensiToday->where('status', 'Alpha')->count();
        $dispCount  = $presensiToday->where('status', 'Dispensasi')->count();

        $pctHadir = $totalPresensi > 0 ? round(($hadirCount / $totalPresensi) * 100, 1) : 0;
        $pctSakit = $totalPresensi > 0 ? round(($sakitCount / $totalPresensi) * 100, 1) : 0;
        $pctIzin  = $totalPresensi > 0 ? round(($izinCount / $totalPresensi) * 100, 1) : 0;
        $pctAlpha = $totalPresensi > 0 ? round(($alphaCount / $totalPresensi) * 100, 1) : 0;
        $pctDisp  = $totalPresensi > 0 ? round(($dispCount / $totalPresensi) * 100, 1) : 0;

        // 2. Monitoring Jurnal Mengajar Terkini Hari Ini
        $recentJurnal = JurnalMengajar::with(['guru', 'kelas', 'mapel'])
            ->where('tanggal', $today)
            ->orderByDesc('id_jurnal')
            ->take(10)
            ->get();

        // 3. Ketercapaian Jurnal per Tingkat Kelas
        $tingkatStats = [];
        foreach (['X', 'XI', 'XII'] as $tk) {
            $kelasTingkatIds = Kelas::where('tingkat', $tk)->pluck('id_kelas');
            $jadwalTingkatCount = JadwalPelajaran::where('hari', $hariIni)->whereIn('id_kelas', $kelasTingkatIds)->count();
            $jurnalTingkatCount = JurnalMengajar::where('tanggal', $today)->whereIn('id_kelas', $kelasTingkatIds)->count();
            $tingkatStats[$tk] = [
                'target' => $jadwalTingkatCount,
                'terisi' => $jurnalTingkatCount,
                'pct'    => $jadwalTingkatCount > 0 ? min(100, round(($jurnalTingkatCount / $jadwalTingkatCount) * 100)) : 0,
            ];
        }

        // 4. Trend Ketercapaian Jurnal 7 Hari Terakhir (Real Data)
        $trendLabels = [];
        $trendData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $day   = Carbon::today()->subDays($i);
            $count = JurnalMengajar::where('tanggal', $day->format('Y-m-d'))->count();

            $trendLabels[] = $day->translatedFormat('D');
            $trendData[]   = $count;
        }

        $trend7Hari = [
            'labels' => $trendLabels,
            'data'   => $trendData,
        ];

        // 5. Perlu Perhatian (Smart Dynamic Alerts Kurikulum)
        $perluPerhatian = [];

        // Alert A: Jadwal Hari Ini yang Belum Terisi Jurnalnya
        if ($totalJadwalHariIni > 0 && $jurnalTerisiCount < $totalJadwalHariIni) {
            $selisih = $totalJadwalHariIni - $jurnalTerisiCount;
            $perluPerhatian[] = [
                'type'     => 'warning',
                'title'    => "{$selisih} dari {$totalJadwalHariIni} sesi jadwal hari {$hariIni} belum terisi jurnal",
                'subtitle' => "Pantau dan koordinasikan pengisian jurnal guru pengajar hari ini",
                'icon'     => 'fa-solid fa-clock-rotate-left',
                'url'      => route('waka-kurikulum.jurnal'),
            ];
        }

        // Alert B: Guru Mengajar di Bawah 24 JP
        $guruKurangBeban = $totalGuruMengajar - $guruMemenuhiBeban;
        if ($guruKurangBeban > 0) {
            $perluPerhatian[] = [
                'type'     => 'info',
                'title'    => "{$guruKurangBeban} guru mengajar aktif belum memenuhi target 24 JP",
                'subtitle' => "Periksa kembali plotting distribusi beban mengajar pengajar",
                'icon'     => 'fa-solid fa-chalkboard-user',
                'url'      => route('waka-kurikulum.guru-mengajar.index'),
            ];
        }

        // Alert C: Catatan Alpha Siswa Hari Ini
        if ($alphaCount > 0) {
            $perluPerhatian[] = [
                'type'     => 'danger',
                'title'    => "{$alphaCount} catatan Alpha siswa terdata pada sesi KBM hari ini",
                'subtitle' => "Koordinasikan dengan wali kelas dan guru BK untuk tindak lanjut",
                'icon'     => 'fa-solid fa-triangle-exclamation',
                'url'      => route('waka-kurikulum.jurnal'),
            ];
        }

        // Alert D: Kelas dengan Ketidakhadiran Menonjol Hari Ini
        $topAbsenKelas = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $today))
            ->whereIn('status', ['Alpha', 'Sakit', 'Izin'])
            ->join('siswa', 'presensi_siswa.id_siswa', '=', 'siswa.id_siswa')
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->select('kelas.nama_kelas', DB::raw('count(*) as total_absen'))
            ->groupBy('kelas.id_kelas', 'kelas.nama_kelas')
            ->orderByDesc('total_absen')
            ->first();

        if ($topAbsenKelas && $topAbsenKelas->total_absen > 0 && count($perluPerhatian) < 4) {
            $perluPerhatian[] = [
                'type'     => 'warning',
                'title'    => "Ketidakhadiran siswa menonjol di {$topAbsenKelas->nama_kelas}",
                'subtitle' => "Tercatat {$topAbsenKelas->total_absen} ketidakhadiran siswa pada jam KBM",
                'icon'     => 'fa-solid fa-users-viewfinder',
                'url'      => route('waka-kurikulum.jurnal'),
            ];
        }

        // Fallback jika tidak ada anomali
        if (empty($perluPerhatian)) {
            $perluPerhatian[] = [
                'type'     => 'success',
                'title'    => 'Seluruh aktivitas KBM & entri jurnal berjalan normal',
                'subtitle' => 'Tidak ada kendala beban mengajar atau anomali absensi',
                'icon'     => 'fa-solid fa-circle-check',
                'url'      => route('waka-kurikulum.jurnal'),
            ];
        }

        // 6. Aktivitas KBM & Jurnal Terbaru (Multi-Source Real Feed)
        $activityFeed = collect();

        foreach ($recentJurnal->take(6) as $j) {
            $guruName  = $j->guru->nama_lengkap ?? 'Guru';
            $mapelName = $j->mapel->nama_mapel ?? 'Mata Pelajaran';
            $kelasName = $j->kelas->nama_kelas ?? 'Kelas';
            $jamKe     = $j->jam_ke ? " (Jam ke-{$j->jam_ke})" : "";
            $tglTime   = $j->created_at ?? Carbon::parse($j->tanggal);

            $activityFeed->push([
                'deskripsi'  => "{$guruName} mengentri jurnal {$mapelName} di {$kelasName}{$jamKe}",
                'waktu'      => $tglTime->diffForHumans(),
                'tag'        => 'Jurnal',
                'icon'       => 'fa-solid fa-book-open-reader',
                'icon_bg'    => 'linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%)',
                'icon_color' => '#3730a3',
                'timestamp'  => $tglTime->timestamp,
            ]);
        }

        $recentJadwal = JadwalPelajaran::with(['mapel', 'kelas', 'guru'])
            ->orderByDesc('id_jadwal')
            ->take(3)
            ->get();

        foreach ($recentJadwal as $jadwal) {
            $mapelName = $jadwal->mapel->nama_mapel ?? 'Mapel';
            $kelasName = $jadwal->kelas->nama_kelas ?? 'Kelas';
            $tglTime   = $jadwal->created_at ?? now();

            $activityFeed->push([
                'deskripsi'  => "Jadwal {$mapelName} ({$kelasName}) — {$jadwal->hari} jam ke-{$jadwal->jam_ke} diperbarui",
                'waktu'      => $tglTime->diffForHumans(),
                'tag'        => 'Jadwal',
                'icon'       => 'fa-solid fa-calendar-check',
                'icon_bg'    => '#dcfce7',
                'icon_color' => '#15803d',
                'timestamp'  => $tglTime->timestamp,
            ]);
        }

        $recentNonHadir = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $today))
            ->whereIn('status', ['Alpha', 'Dispensasi', 'Sakit', 'Izin'])
            ->with(['siswa.kelas'])
            ->orderByDesc('id')
            ->take(3)
            ->get();

        foreach ($recentNonHadir as $p) {
            $siswaName = $p->siswa->nama_lengkap ?? 'Siswa';
            $kelasName = $p->siswa->kelas->nama_kelas ?? '-';
            $tglTime   = $p->created_at ?? ($p->jurnal ? Carbon::parse($p->jurnal->tanggal) : now());

            $activityFeed->push([
                'deskripsi'  => "{$siswaName} ({$kelasName}) tercatat {$p->status}" . ($p->keterangan ? " — \"{$p->keterangan}\"" : ""),
                'waktu'      => $tglTime->diffForHumans(),
                'tag'        => $p->status,
                'icon'       => match($p->status) {
                    'Alpha'      => 'fa-solid fa-user-xmark',
                    'Dispensasi' => 'fa-solid fa-id-badge',
                    'Sakit'      => 'fa-solid fa-notes-medical',
                    default      => 'fa-solid fa-envelope-open-text',
                },
                'icon_bg'    => match($p->status) {
                    'Alpha'      => '#fee2e2',
                    'Dispensasi' => '#ede9fe',
                    'Sakit'      => '#fef3c7',
                    default      => '#e0f2fe',
                },
                'icon_color' => match($p->status) {
                    'Alpha'      => '#ef4444',
                    'Dispensasi' => '#7c3aed',
                    'Sakit'      => '#d97706',
                    default      => '#0284c7',
                },
                'timestamp'  => $tglTime->timestamp,
            ]);
        }

        $aktivitasTerbaru = $activityFeed->sortByDesc('timestamp')->take(6)->values()->all();

        return view('waka_kurikulum.dashboard.index', compact(
            'user', 'waka', 'today', 'todayFormatted', 'hariIni', 'tahunAjaranAktif',
            'greetingText', 'namaUser',
            'totalKelas', 'totalMapel', 'totalGuru', 'totalGuruMengajar', 'guruMemenuhiBeban', 'avgJpPerGuru', 'totalJpSekolah',
            'totalJadwalHariIni', 'jurnalTerisiCount', 'pctKbmBerjalan',
            'guruTerjadwalHariIniCount', 'guruMengajarHariIniCount',
            'totalPresensi', 'hadirCount', 'sakitCount', 'izinCount', 'alphaCount', 'dispCount',
            'pctHadir', 'pctSakit', 'pctIzin', 'pctAlpha', 'pctDisp',
            'recentJurnal', 'tingkatStats', 'trend7Hari', 'perluPerhatian', 'aktivitasTerbaru'
        ));
    }

    /**
     * Monitoring Seluruh Jurnal Mengajar Guru
     */
    public function jurnal(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $search  = $request->input('search');
        $idKelas = $request->input('id_kelas');
        $idMapel = $request->input('id_mapel');
        $idGuru  = $request->input('id_guru');

        $jurnalList = JurnalMengajar::with(['guru', 'kelas', 'mapel'])
            ->when($tanggal, fn($q) => $q->where('tanggal', $tanggal))
            ->when($idKelas, fn($q) => $q->where('id_kelas', $idKelas))
            ->when($idMapel, fn($q) => $q->where('id_mapel', $idMapel))
            ->when($idGuru,  fn($q) => $q->where('id_guru', $idGuru))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qs) use ($search) {
                    $qs->where('materi', 'LIKE', "%{$search}%")
                       ->orWhere('catatan', 'LIKE', "%{$search}%")
                       ->orWhereHas('guru', fn($qg) => $qg->where('nama_lengkap', 'LIKE', "%{$search}%"));
                });
            })
            ->orderByDesc('id_jurnal')
            ->paginate(15)
            ->withQueryString();

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();
        $guruList  = Guru::orderBy('nama_lengkap')->get();

        return view('waka_kurikulum.jurnal.index', compact(
            'user', 'waka', 'jurnalList', 'kelasList', 'mapelList', 'guruList',
            'tanggal', 'search', 'idKelas', 'idMapel', 'idGuru'
        ));
    }

    /**
     * Pengaturan & Monitoring Jadwal Pelajaran
     */
    public function jadwal(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $hariFilter = $request->input('hari', Carbon::today()->translatedFormat('l'));
        $idKelas    = $request->input('id_kelas');
        $idJurusan  = $request->input('id_jurusan');
        $tingkat    = $request->input('tingkat');
        $idGuru     = $request->input('id_guru');
        $idMapel    = $request->input('id_mapel');

        $query = JadwalPelajaran::with(['guru', 'kelas', 'mapel', 'tahunAjaran'])
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->when($hariFilter && $hariFilter !== 'Semua', fn($q) => $q->where('hari', $hariFilter))
            ->when($idKelas, fn($q) => $q->where('id_kelas', $idKelas))
            ->when($idGuru,  fn($q) => $q->where('id_guru', $idGuru))
            ->when($idMapel, fn($q) => $q->where('id_mapel', $idMapel))
            ->when($tingkat, function ($q) use ($tingkat) {
                $q->whereHas('kelas', fn($qk) => $qk->where('tingkat', $tingkat));
            })
            ->when($idJurusan, function ($q) use ($idJurusan) {
                $q->whereHas('kelas', fn($qk) => $qk->where('id_jurusan', $idJurusan));
            });

        $jadwalList  = $query->orderBy('hari')->orderBy('jam_ke')->get();
        $kelasList   = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $jurusanList = Jurusan::all();
        $guruList    = Guru::where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $mapelList   = Mapel::orderBy('nama_mapel')->get();

        return view('waka_kurikulum.jadwal.index', compact(
            'user', 'waka', 'jadwalList', 'kelasList', 'jurusanList', 'guruList', 'mapelList', 'tahunAjaranAktif',
            'hariFilter', 'idKelas', 'idJurusan', 'tingkat', 'idGuru', 'idMapel'
        ));
    }

    /**
     * Tambah Jadwal Pelajaran Baru (dengan validasi bentrok guru dan kelas)
     */
    public function storeJadwal(Request $request)
    {
        $validated = $request->validate([
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke'      => 'required|integer|min:1|max:12',
            'id_kelas'    => 'required|exists:kelas,id_kelas',
            'id_mapel'    => 'required|exists:mapel,id_mapel',
            'id_guru'     => 'required|exists:guru,id_guru',
            'jam_mulai'   => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif. Silakan tentukan tahun ajaran terlebih dahulu.');
        }

        $idTahunAjaran = $tahunAjaranAktif->id;
        $hari   = $validated['hari'];
        $jamKe  = (int) $validated['jam_ke'];

        $defaultTime = JadwalPelajaranController::getDefaultTimeSlot($hari, $jamKe);
        $jamMulai   = $validated['jam_mulai'] ?? $defaultTime['jam_mulai'];
        $jamSelesai = $validated['jam_selesai'] ?? $defaultTime['jam_selesai'];

        // Cek bentrok kelas
        $bentrokKelas = JadwalPelajaran::where('id_tahun_ajaran', $idTahunAjaran)
            ->where('id_kelas', $validated['id_kelas'])
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->first();

        if ($bentrokKelas) {
            return back()->with('error', "Bentrok Jadwal: Kelas ini sudah memiliki mata pelajaran pada {$hari} jam ke-{$jamKe}.");
        }

        // Cek bentrok guru
        $bentrokGuru = JadwalPelajaran::where('id_tahun_ajaran', $idTahunAjaran)
            ->where('id_guru', $validated['id_guru'])
            ->where('hari', $hari)
            ->where('jam_ke', $jamKe)
            ->first();

        if ($bentrokGuru) {
            return back()->with('error', "Bentrok Guru: Guru tersebut sudah terjadwal mengajar di kelas lain pada {$hari} jam ke-{$jamKe}.");
        }

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

        return back()->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    /**
     * Perbarui Jadwal Pelajaran
     */
    public function updateJadwal(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $validated = $request->validate([
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke'      => 'required|integer|min:1|max:12',
            'id_kelas'    => 'required|exists:kelas,id_kelas',
            'id_mapel'    => 'required|exists:mapel,id_mapel',
            'id_guru'     => 'required|exists:guru,id_guru',
            'jam_mulai'   => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
        ]);

        $defaultTime = JadwalPelajaranController::getDefaultTimeSlot($validated['hari'], (int) $validated['jam_ke']);
        $jamMulai   = $validated['jam_mulai'] ?? $defaultTime['jam_mulai'];
        $jamSelesai = $validated['jam_selesai'] ?? $defaultTime['jam_selesai'];

        // Cek bentrok kelas (abaikan ID saat ini)
        $bentrokKelas = JadwalPelajaran::where('id_tahun_ajaran', $jadwal->id_tahun_ajaran)
            ->where('id_kelas', $validated['id_kelas'])
            ->where('hari', $validated['hari'])
            ->where('jam_ke', $validated['jam_ke'])
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->first();

        if ($bentrokKelas) {
            return back()->with('error', "Bentrok Jadwal: Kelas ini sudah terisi jadwal lain pada hari {$validated['hari']} jam ke-{$validated['jam_ke']}.");
        }

        // Cek bentrok guru (abaikan ID saat ini)
        $bentrokGuru = JadwalPelajaran::where('id_tahun_ajaran', $jadwal->id_tahun_ajaran)
            ->where('id_guru', $validated['id_guru'])
            ->where('hari', $validated['hari'])
            ->where('jam_ke', $validated['jam_ke'])
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->first();

        if ($bentrokGuru) {
            return back()->with('error', "Bentrok Guru: Guru tersebut sedang mengajar di kelas lain pada hari {$validated['hari']} jam ke-{$validated['jam_ke']}.");
        }

        $jadwal->update([
            'hari'        => $validated['hari'],
            'jam_ke'      => $validated['jam_ke'],
            'jam_mulai'   => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'id_mapel'    => $validated['id_mapel'],
            'id_guru'     => $validated['id_guru'],
            'id_kelas'    => $validated['id_kelas'],
        ]);

        return back()->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    /**
     * Hapus Jadwal Pelajaran
     */
    public function destroyJadwal($id)
    {
        $jadwal = JadwalPelajaran::withCount('jurnalMengajar')->findOrFail($id);

        if ($jadwal->jurnal_mengajar_count > 0) {
            return back()->with('error', "Jadwal ini tidak dapat dihapus karena sudah memiliki {$jadwal->jurnal_mengajar_count} rekaman jurnal mengajar.");
        }

        $jadwal->delete();

        return back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
