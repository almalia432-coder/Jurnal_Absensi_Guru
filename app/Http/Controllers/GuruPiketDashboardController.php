<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuruPiket;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\JurnalMengajar;
use App\Models\PresensiSiswa;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\IzinGuru;
use App\Models\DispensasiSiswa;
use App\Models\IzinSiswa;
use App\Models\LaporanPiket;
use App\Models\Jurusan;
use App\Models\KepalaSekolah;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\StatusHarianKbm;
use App\Models\JadwalPiketKbm;
use App\Support\PortalResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GuruPiketDashboardController extends Controller
{
    /**
     * Dashboard Utama Guru Piket
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();

        // Selected Date (defaults to today)
        $tanggalInput = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $targetDate   = Carbon::parse($tanggalInput);
        $today        = $targetDate->format('Y-m-d');
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');
        $hariIni      = $targetDate->translatedFormat('l');

        // Identify current Guru Piket profile
        $guruPiket = null;
        if ($user->role === 'guru_piket') {
            $guruPiket = $user->guruPiket;
        }

        // Admin or supervisor switcher
        if (!$guruPiket && ($user->role === 'admin' || $request->has('piket_id'))) {
            $targetPiketId = $request->input('piket_id');
            if ($targetPiketId) {
                $guruPiket = GuruPiket::find($targetPiketId);
            } else {
                $guruPiket = GuruPiket::first();
            }
        }

        $allGuruPiketList = GuruPiket::where('status_aktif', true)->get();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // ── 1. KPI Counts Hari Ini ──────────────────────────────────
        $totalKelas = Kelas::count();
        $totalGuru  = Guru::where('status_aktif', true)->count();
        $totalSiswa = Siswa::where('status_aktif', true)->count();

        // Jadwal & Realisasi Jurnal
        $jadwalHariIniQuery = JadwalPelajaran::where('hari', $hariIni)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id));
        
        $totalJadwalHariIni = $jadwalHariIniQuery->count();
        $jurnalTerisiCount  = JurnalMengajar::where('tanggal', $today)->count();

        // Kelas dengan jurnal terisi vs kelas aktif yang terjadwal
        $kelasTerjadwalIds = JadwalPelajaran::where('hari', $hariIni)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->distinct()
            ->pluck('id_kelas');
        
        $kelasTerisiIds = JurnalMengajar::where('tanggal', $today)
            ->distinct()
            ->pluck('id_kelas');

        $totalKelasTerjadwal = $kelasTerjadwalIds->count() > 0 ? $kelasTerjadwalIds->count() : $totalKelas;
        $totalKelasTerisi    = $kelasTerisiIds->count();
        $totalKelasKosong    = max(0, $totalKelasTerjadwal - $totalKelasTerisi);
        $pctKelasTerisi      = $totalKelasTerjadwal > 0 ? round(($totalKelasTerisi / $totalKelasTerjadwal) * 100) : 0;

        // Guru Hadir vs Izin
        $guruHadirCount = JurnalMengajar::where('tanggal', $today)
            ->where('status_guru', 'Hadir')
            ->distinct('id_guru')
            ->count('id_guru');

        $guruIzinRecords = IzinGuru::berlaku()
            ->with('guru')
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->get();
        
        $guruIzinCount = $guruIzinRecords->pluck('id_guru')->unique()->count();

        // Dispensasi Siswa Hari Ini
        $dispensasiToday = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser'])
            ->where('tanggal', $today)
            ->orderByDesc('id')
            ->get();

        $totalDispensasi = $dispensasiToday->count();
        $dispensasiPending = $dispensasiToday->where('status', 'Menunggu')->count();
        $dispensasiAktif = $dispensasiToday->filter(fn($d) => in_array($d->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai']))->count();

        // Presensi Siswa Se-Sekolah Hari Ini
        $presensiToday = PresensiSiswa::whereHas('jurnal', function ($q) use ($today) {
            $q->where('tanggal', $today);
        })->get();

        $hadirCount      = $presensiToday->where('status', 'Hadir')->pluck('id_siswa')->unique()->count();
        $sakitCount      = $presensiToday->where('status', 'Sakit')->pluck('id_siswa')->unique()->count();
        $izinCount       = $presensiToday->where('status', 'Izin')->pluck('id_siswa')->unique()->count();
        $alphaCount      = $presensiToday->where('status', 'Alpha')->pluck('id_siswa')->unique()->count();
        $dispPresensiCount = $presensiToday->where('status', 'Dispensasi')->pluck('id_siswa')->unique()->count();

        $totalTidakMasuk = $sakitCount + $izinCount + $alphaCount + $dispPresensiCount;
        $totalPresensi   = $hadirCount + $totalTidakMasuk;
        $pctHadirSekolah = $totalPresensi > 0 ? round(($hadirCount / $totalPresensi) * 100) : ($totalSiswa > 0 ? round(($hadirCount / $totalSiswa) * 100) : 0);

        // ── 2. Live Monitoring Kelas (Status per Rombel Hari Ini) ───
        $allKelas = Kelas::with(['jurusanRelation', 'waliKelas'])->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $allJurnalsToday = JurnalMengajar::with(['guru', 'mapel'])
            ->where('tanggal', $today)
            ->get()
            ->groupBy('id_kelas');

        $allJadwalsToday = JadwalPelajaran::with(['guru', 'mapel'])
            ->where('hari', $hariIni)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->orderBy('jam_ke')
            ->get()
            ->groupBy('id_kelas');

        $kelasMonitoring = $allKelas->map(function ($k) use ($allJurnalsToday, $allJadwalsToday, $guruIzinRecords) {
            $jurnals = $allJurnalsToday->get($k->id_kelas, collect());
            $jadwals = $allJadwalsToday->get($k->id_kelas, collect());

            $latestJurnal = $jurnals->sortByDesc('jam_mulai')->first();
            $currentJadwal = $jadwals->first();

            $statusKelas = 'Kosong';
            $statusBadge = 'secondary';
            $guruName    = '-';
            $mapelName   = '-';
            $jamInfo     = '-';

            if ($latestJurnal) {
                if ($latestJurnal->status_guru !== 'Hadir') {
                    $statusKelas = 'Tugas Mandiri (' . $latestJurnal->status_guru . ')';
                    $statusBadge = 'info';
                } else {
                    $statusKelas = 'Sedang Mengajar';
                    $statusBadge = 'success';
                }
                $guruName    = $latestJurnal->guru->nama_lengkap ?? '-';
                $mapelName   = $latestJurnal->mapel->nama_mapel ?? '-';
                $jamInfo     = $latestJurnal->jam_ke ? "Jam ke-{$latestJurnal->jam_ke}" : Carbon::parse($latestJurnal->jam_mulai)->format('H:i');
            } elseif ($jadwals->count() > 0) {
                // Check if the scheduled teacher is on leave today
                $scheduledGuruIds = $jadwals->pluck('id_guru')->toArray();
                $isGuruIzin = $guruIzinRecords->whereIn('id_guru', $scheduledGuruIds)->first();

                if ($isGuruIzin) {
                    $statusKelas = 'Guru Izin / Kosong';
                    $statusBadge = 'danger';
                    $guruName    = $isGuruIzin->guru->nama_lengkap ?? 'Guru Izin';
                    $mapelName   = $currentJadwal->mapel->nama_mapel ?? 'Mata Pelajaran';
                    $jamInfo     = "Jam ke-{$currentJadwal->jam_ke}";
                } else {
                    $statusKelas = 'Belum Ada Jurnal';
                    $statusBadge = 'warning';
                    $guruName    = $currentJadwal->guru->nama_lengkap ?? '-';
                    $mapelName   = $currentJadwal->mapel->nama_mapel ?? '-';
                    $jamInfo     = "Jam ke-{$currentJadwal->jam_ke}";
                }
            } else {
                $statusKelas = 'Tidak Ada Jadwal';
                $statusBadge = 'light';
            }

            return (object) [
                'id_kelas'     => $k->id_kelas,
                'nama_kelas'   => $k->nama_kelas,
                'tingkat'      => $k->tingkat,
                'jurusan'      => $k->jurusan,
                'status'       => $statusKelas,
                'badge'        => $statusBadge,
                'guru'         => $guruName,
                'mapel'        => $mapelName,
                'jam'          => $jamInfo,
                'total_jurnal' => $jurnals->count(),
                'total_jadwal' => $jadwals->count(),
            ];
        });

        // ── 3. Jadwal Terdampak Guru Izin Hari Ini ─────────────────
        $jadwalTerdampakIzin = collect();
        if ($guruIzinRecords->count() > 0) {
            $izinGuruIds = $guruIzinRecords->pluck('id_guru')->toArray();
            $jadwalTerdampakIzin = JadwalPelajaran::with(['guru', 'kelas', 'mapel'])
                ->where('hari', $hariIni)
                ->whereIn('id_guru', $izinGuruIds)
                ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
                ->orderBy('jam_ke')
                ->get();
        }

        // ── 4. Aktivitas Jurnal Mengajar Terkini ───────────────────
        $recentJurnal = JurnalMengajar::with(['guru', 'kelas', 'mapel'])
            ->where('tanggal', $today)
            ->orderByDesc('id_jurnal')
            ->take(6)
            ->get();

        // ── 5. Catatan Laporan Piket Hari Ini ──────────────────────
        $laporanPiketToday = LaporanPiket::where('tanggal', $today)->first();

        // ── 6. Data Grafik Chart.js ───────────────────────────────
        // Chart 1: Distribusi Siswa Tidak Masuk per Jurusan
        $jurusanLabels = [];
        $jurusanAbsen  = [];
        $allJurusan = Jurusan::orderBy('kode_jurusan')->get();
        if ($allJurusan->count() === 0) {
            $jurusanList = Kelas::distinct()->pluck('jurusan')->filter();
            foreach ($jurusanList as $jur) {
                $jurusanLabels[] = $jur;
                $absenCount = PresensiSiswa::whereHas('jurnal', function ($q) use ($today, $jur) {
                    $q->where('tanggal', $today)->whereHas('kelas', fn($qk) => $qk->where('jurusan', $jur));
                })->whereIn('status', ['Sakit', 'Izin', 'Alpha', 'Dispensasi'])->distinct('id_siswa')->count('id_siswa');
                $jurusanAbsen[] = $absenCount;
            }
        } else {
            foreach ($allJurusan as $jur) {
                $jurusanLabels[] = $jur->kode_jurusan;
                $absenCount = PresensiSiswa::whereHas('jurnal', function ($q) use ($today, $jur) {
                    $q->where('tanggal', $today)->whereHas('kelas', fn($qk) => $qk->where('id_jurusan', $jur->id_jurusan ?? $jur->id)->orWhere('jurusan', $jur->kode_jurusan));
                })->whereIn('status', ['Sakit', 'Izin', 'Alpha', 'Dispensasi'])->distinct('id_siswa')->count('id_siswa');
                $jurusanAbsen[] = $absenCount;
            }
        }

        // Chart 2: Komposisi Presensi Siswa
        $piePresensi = [
            'Hadir'      => $hadirCount,
            'Sakit'      => $sakitCount,
            'Izin'       => $izinCount,
            'Alpha'      => $alphaCount,
            'Dispensasi' => $dispPresensiCount,
        ];

        // Chart 3: Trend 7 Hari Terakhir
        $trendLabels = [];
        $trendHadir  = [];
        $trendAbsen  = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::parse($today)->subDays($i);
            $dStr = $day->format('Y-m-d');
            $trendLabels[] = $day->translatedFormat('D, d M');

            $h = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $dStr))
                ->where('status', 'Hadir')->distinct('id_siswa')->count('id_siswa');

            $a = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $dStr))
                ->whereIn('status', ['Sakit', 'Izin', 'Alpha', 'Dispensasi'])->distinct('id_siswa')->count('id_siswa');

            $trendHadir[] = $h;
            $trendAbsen[] = $a;
        }

        // Siswa list for Quick Dispensasi Modal
        $siswaSelectOption = Siswa::with('kelas')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get(['id_siswa', 'nama_lengkap', 'nisn', 'id_kelas']);

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(['id_kelas', 'nama_kelas', 'tingkat', 'jurusan']);

        // Status KBM Harian (Upacara / Pembiasaan Ditiadakan)
        $statusKbmHariIni = StatusHarianKbm::getEffectiveStatus($today);
        $isMaju = StatusHarianKbm::isMaju($today);

        // Petugas Piket KBM Hari Ini (Siklus A / B)
        $rosterToday = JadwalPiketKbm::getRosterForDate($targetDate);

        return view('guru_piket.dashboard.index', compact(
            'user', 'guruPiket', 'allGuruPiketList', 'tahunAjaranAktif',
            'today', 'todayFormatted', 'hariIni',
            'totalKelas', 'totalGuru', 'totalSiswa', 'totalJadwalHariIni', 'jurnalTerisiCount',
            'totalKelasTerjadwal', 'totalKelasTerisi', 'totalKelasKosong', 'pctKelasTerisi',
            'guruHadirCount', 'guruIzinCount', 'guruIzinRecords', 'jadwalTerdampakIzin',
            'totalDispensasi', 'dispensasiPending', 'dispensasiAktif', 'dispensasiToday',
            'hadirCount', 'sakitCount', 'izinCount', 'alphaCount', 'dispPresensiCount',
            'totalTidakMasuk', 'totalPresensi', 'pctHadirSekolah',
            'kelasMonitoring', 'recentJurnal', 'laporanPiketToday',
            'jurusanLabels', 'jurusanAbsen', 'piePresensi', 'trendLabels', 'trendHadir', 'trendAbsen',
            'siswaSelectOption', 'kelasList', 'statusKbmHariIni', 'isMaju', 'rosterToday'
        ));
    }

    /**
     * Halaman Live Monitoring Kelas Lengkap
     */
    public function monitoringKelas(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $guruPiket = $user->role === 'guru_piket' ? $user->guruPiket : null;

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($tanggal);
        $hariIni = $targetDate->translatedFormat('l');
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');

        $tingkatFilter = $request->input('tingkat');
        $jurusanFilter = $request->input('jurusan');
        $statusFilter  = $request->input('status');

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $allKelas = Kelas::with(['jurusanRelation', 'waliKelas'])
            ->when($tingkatFilter, fn($q) => $q->where('tingkat', $tingkatFilter))
            ->when($jurusanFilter, fn($q) => $q->where('jurusan', $jurusanFilter))
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $allJurnals = JurnalMengajar::with(['guru', 'mapel'])
            ->where('tanggal', $tanggal)
            ->get()
            ->groupBy('id_kelas');

        $allJadwals = JadwalPelajaran::with(['guru', 'mapel'])
            ->where('hari', $hariIni)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->orderBy('jam_ke')
            ->get()
            ->groupBy('id_kelas');

        $guruIzinRecords = IzinGuru::berlaku()
            ->with('guru')
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->get();

        $kelasMonitoring = $allKelas->map(function ($k) use ($allJurnals, $allJadwals, $guruIzinRecords) {
            $jurnals = $allJurnals->get($k->id_kelas, collect());
            $jadwals = $allJadwals->get($k->id_kelas, collect());

            $latestJurnal = $jurnals->sortByDesc('jam_mulai')->first();
            $currentJadwal = $jadwals->first();

            $status = 'Kosong';
            $badge = 'secondary';
            $guruName = '-';
            $mapelName = '-';
            $jamInfo = '-';

            if ($latestJurnal) {
                if ($latestJurnal->status_guru !== 'Hadir') {
                    $status = 'Tugas Mandiri (' . $latestJurnal->status_guru . ')';
                    $badge = 'info';
                } else {
                    $status = 'Sedang Mengajar';
                    $badge = 'success';
                }
                $guruName = $latestJurnal->guru->nama_lengkap ?? '-';
                $mapelName = $latestJurnal->mapel->nama_mapel ?? '-';
                $jamInfo = $latestJurnal->jam_ke ? "Jam ke-{$latestJurnal->jam_ke}" : Carbon::parse($latestJurnal->jam_mulai)->format('H:i');
            } elseif ($jadwals->count() > 0) {
                $scheduledGuruIds = $jadwals->pluck('id_guru')->toArray();
                $isGuruIzin = $guruIzinRecords->whereIn('id_guru', $scheduledGuruIds)->first();

                if ($isGuruIzin) {
                    $status = 'Guru Izin';
                    $badge = 'danger';
                    $guruName = $isGuruIzin->guru->nama_lengkap ?? 'Guru Izin';
                    $mapelName = $currentJadwal->mapel->nama_mapel ?? '-';
                    $jamInfo = "Jam ke-{$currentJadwal->jam_ke}";
                } else {
                    $status = 'Belum Ada Jurnal';
                    $badge = 'warning';
                    $guruName = $currentJadwal->guru->nama_lengkap ?? '-';
                    $mapelName = $currentJadwal->mapel->nama_mapel ?? '-';
                    $jamInfo = "Jam ke-{$currentJadwal->jam_ke}";
                }
            } else {
                $status = 'Tidak Ada Jadwal';
                $badge = 'light';
            }

            return (object) [
                'kelas'        => $k,
                'status'       => $status,
                'badge'        => $badge,
                'guru'         => $guruName,
                'mapel'        => $mapelName,
                'jam'          => $jamInfo,
                'jurnals'      => $jurnals,
                'jadwals'      => $jadwals,
                'total_jurnal' => $jurnals->count(),
                'total_jadwal' => $jadwals->count(),
            ];
        });

        if ($statusFilter) {
            $kelasMonitoring = $kelasMonitoring->filter(function ($item) use ($statusFilter) {
                return $item->status === $statusFilter;
            });
        }

        $allJurusanList = Kelas::distinct()->pluck('jurusan')->filter();

        return view('guru_piket.monitoring.index', compact(
            'user', 'guruPiket', 'tanggal', 'todayFormatted', 'hariIni',
            'kelasMonitoring', 'tingkatFilter', 'jurusanFilter', 'statusFilter', 'allJurusanList'
        ));
    }

    /**
     * Halaman Dispensasi Siswa
     */
    public function dispensasi(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $guruPiket = $user->role === 'guru_piket' ? $user->guruPiket : null;

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $search  = $request->input('search');
        $status  = $request->input('status');

        $dispensasiList = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser', 'disetujuiOlehUser'])
            ->when($tanggal, fn($q) => $q->where('tanggal', $tanggal))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('siswa', function ($qs) use ($search) {
                    $qs->where('nama_lengkap', 'LIKE', "%{$search}%")
                       ->orWhere('nisn', 'LIKE', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $siswaSelectOption = Siswa::with('kelas')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get(['id_siswa', 'nama_lengkap', 'nisn', 'id_kelas']);

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(['id_kelas', 'nama_kelas', 'tingkat', 'jurusan']);

        return view('guru_piket.dispensasi.index', compact(
            'user', 'guruPiket', 'dispensasiList', 'tanggal', 'search', 'status', 'siswaSelectOption', 'kelasList'
        ));
    }

    /**
     * Simpan Dispensasi Baru
     */
    public function storeDispensasi(Request $request)
    {
        $request->validate([
            'id_siswa'    => 'required|exists:siswa,id_siswa',
            'tanggal'     => 'required|date',
            'jam_keluar'  => 'required',
            'jam_kembali' => 'nullable',
            'alasan'      => 'required|string|max:500',
            'bukti_file'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('dispensasi', 'public');
        }

        $disp = DispensasiSiswa::with('siswa')->create([
            'id_siswa'            => $request->id_siswa,
            'tanggal'             => $request->tanggal,
            'jam_keluar'          => $request->jam_keluar,
            'jam_kembali'         => $request->jam_kembali,
            'alasan'              => $request->alasan,
            'bukti_file'          => $buktiPath,
            'status'              => 'Menunggu',
            'disetujui_oleh'      => null,
            'tanggal_persetujuan' => null,
            'diinput_oleh'        => Auth::id(),
        ]);

        $namaSiswa = $disp->siswa->nama_lengkap ?? 'Siswa';
        LogAktivitas::catat(
            'Dispensasi Siswa',
            "Guru Piket mengajukan surat izin dispensasi untuk {$namaSiswa} (Menunggu Persetujuan Waka Piket)",
            $disp,
            Auth::user()
        );

        return back()->with('success', "Pengajuan dispensasi untuk {$namaSiswa} berhasil disimpan dan sekarang menunggu persetujuan Waka Piket.");
    }

    /**
     * Update Status Dispensasi — Tahap 1 (Guru Piket)
     *
     * Aksi:
     *  - setujui : Menunggu → Disetujui_Piket (lanjut ke Waka Piket)
     *  - tolak   : Menunggu → Ditolak (catatan wajib, min 5 karakter)
     *  - kembali : Disetujui → Selesai (catat jam_kembali_aktual)
     */
    public function updateDispensasiStatus(Request $request, $id)
    {
        $request->validate([
            'action'  => 'required|in:setujui,tolak,kembali,batalkan',
            'catatan' => 'nullable|string|max:500',
        ]);

        $dispensasi = DispensasiSiswa::with('siswa')->findOrFail($id);
        $user = Auth::user();
        $action = $request->input('action');

        // ── Otorisasi: guru piket bertugas pada tanggal dispensasi via PortalResolver (atau admin) ──
        $dispDate = Carbon::parse($dispensasi->tanggal);
        $isAuthorized = PortalResolver::hasDuty($user, 'piket', $dispDate);

        if (!$isAuthorized) {
            return back()->with('error', 'Anda tidak bertugas sebagai Guru Piket pada tanggal ' . $dispDate->translatedFormat('d F Y') . '.');
        }

        $namaSiswa = $dispensasi->siswa->nama_lengkap ?? 'Siswa';

        // ── Pembatalan oleh Admin (atau aksi batalkan) ────────────────────
        if ($action === 'batalkan') {
            if ($user->role !== 'admin') {
                return back()->with('error', 'Hanya administrator yang berhak membatalkan dispensasi.');
            }

            $request->validate(['catatan' => 'required|string|min:5|max:500']);

            $msg = DB::transaction(function () use ($dispensasi, $user, $namaSiswa, $request) {
                $locked = DispensasiSiswa::where('id', $dispensasi->id)->lockForUpdate()->first();
                $locked->update([
                    'status'          => 'Dibatalkan',
                    'alasan_batal'    => $request->catatan,
                    'dibatalkan_oleh' => $user->id,
                    'dibatalkan_at'   => now(),
                ]);

                LogAktivitas::catat(
                    'Dispensasi Siswa',
                    "Admin ({$user->name}) MEMBATALKAN dispensasi siswa {$namaSiswa}. Alasan: {$request->catatan}",
                    $locked,
                    $user
                );

                if ($locked->diinput_oleh) {
                    try {
                        Notifikasi::create([
                            'user_id'        => $locked->diinput_oleh,
                            'judul'          => "Dispensasi {$namaSiswa} Dibatalkan",
                            'pesan'          => "Dispensasi atas nama {$namaSiswa} telah dibatalkan oleh Administrator. Alasan: {$request->catatan}",
                            'tipe'           => 'dispensasi_siswa',
                            'reference_id'   => $locked->id,
                            'reference_type' => DispensasiSiswa::class,
                            'is_read'        => false,
                        ]);
                    } catch (\Exception $e) {}
                }

                return "Dispensasi siswa {$namaSiswa} berhasil dibatalkan.";
            });

            return back()->with('success', $msg);
        }

        // ── Setujui (Tahap 1) ─────────────────────────────────────────────
        if ($action === 'setujui') {
            $msg = DB::transaction(function () use ($dispensasi, $user, $namaSiswa, $request) {
                $locked = DispensasiSiswa::where('id', $dispensasi->id)
                    ->where('status', 'Menunggu')
                    ->lockForUpdate()
                    ->first();

                if (!$locked) {
                    return null; // status sudah berubah
                }

                $locked->update([
                    'status'           => 'Disetujui_Piket',
                    'piket_approved_by' => $user->id,
                    'piket_at'          => now(),
                    'piket_catatan'     => $request->input('catatan'),
                ]);

                LogAktivitas::catat(
                    'Dispensasi Siswa',
                    "Guru Piket ({$user->name}) MENYETUJUI Tahap 1 dispensasi siswa {$namaSiswa}",
                    $locked,
                    $user
                );

                // Notifikasi ke Waka Piket yang bertugas pada tanggal dispensasi
                $rosterTarget = JadwalPiketKbm::getRosterForDate(Carbon::parse($locked->tanggal));
                $wakaDuty = $rosterTarget['waka'] ?? null;
                if ($wakaDuty && !empty($wakaDuty['nip'])) {
                    $wakaUser = User::whereHas('guru', fn($q) => $q->where('nip', $wakaDuty['nip']))
                        ->where('is_active', true)->first()
                        ?? User::whereHas('waka', fn($q) => $q->where('nip', $wakaDuty['nip']))
                        ->where('is_active', true)->first();
                    if ($wakaUser) {
                        try {
                            Notifikasi::create([
                                'user_id'        => $wakaUser->id,
                                'judul'          => 'Dispensasi Menunggu Persetujuan Anda',
                                'pesan'          => "Dispensasi atas nama {$namaSiswa} telah disetujui Guru Piket dan menunggu persetujuan Waka Piket.",
                                'tipe'           => 'dispensasi_siswa',
                                'reference_id'   => $locked->id,
                                'reference_type' => DispensasiSiswa::class,
                                'is_read'        => false,
                            ]);
                        } catch (\Exception $e) {}
                    }
                }

                return "Dispensasi {$namaSiswa} disetujui Guru Piket (Tahap 1). Menunggu persetujuan Waka Piket.";
            });

            if (!$msg) {
                return back()->with('error', 'Status dispensasi sudah berubah. Silakan muat ulang halaman.');
            }
            return back()->with('success', $msg);
        }

        // ── Tolak (dari Menunggu) ─────────────────────────────────────────
        if ($action === 'tolak') {
            $request->validate(['catatan' => 'required|string|min:5|max:500']);

            $msg = DB::transaction(function () use ($dispensasi, $user, $namaSiswa, $request) {
                $locked = DispensasiSiswa::where('id', $dispensasi->id)
                    ->where('status', 'Menunggu')
                    ->lockForUpdate()
                    ->first();

                if (!$locked) {
                    return null;
                }

                $locked->update([
                    'status'           => 'Ditolak',
                    'piket_approved_by' => $user->id,
                    'piket_at'          => now(),
                    'piket_catatan'     => $request->input('catatan'),
                ]);

                LogAktivitas::catat(
                    'Dispensasi Siswa',
                    "Guru Piket ({$user->name}) MENOLAK dispensasi siswa {$namaSiswa}. Alasan: {$request->input('catatan')}",
                    $locked,
                    $user
                );

                // Notifikasi ke penginput
                if ($locked->diinput_oleh) {
                    try {
                        Notifikasi::create([
                            'user_id'        => $locked->diinput_oleh,
                            'judul'          => "Dispensasi {$namaSiswa} Ditolak",
                            'pesan'          => "Dispensasi atas nama {$namaSiswa} ditolak oleh Guru Piket. Alasan: {$request->input('catatan')}",
                            'tipe'           => 'dispensasi_siswa',
                            'reference_id'   => $locked->id,
                            'reference_type' => DispensasiSiswa::class,
                            'is_read'        => false,
                        ]);
                    } catch (\Exception $e) {}
                }

                return "Dispensasi {$namaSiswa} ditolak.";
            });

            if (!$msg) {
                return back()->with('error', 'Status dispensasi sudah berubah. Silakan muat ulang halaman.');
            }
            return back()->with('warning', $msg);
        }

        // ── Kembali (dari Disetujui → Selesai) ───────────────────────────
        if ($action === 'kembali') {
            $msg = DB::transaction(function () use ($dispensasi, $user, $namaSiswa) {
                $locked = DispensasiSiswa::where('id', $dispensasi->id)
                    ->where('status', 'Disetujui')
                    ->lockForUpdate()
                    ->first();

                if (!$locked) {
                    return null;
                }

                $locked->update([
                    'status'            => 'Selesai',
                    'jam_kembali_aktual' => Carbon::now()->format('H:i:s'),
                ]);

                LogAktivitas::catat(
                    'Dispensasi Siswa',
                    "Guru Piket ({$user->name}) mencatat siswa {$namaSiswa} telah kembali ke sekolah",
                    $locked,
                    $user
                );

                // Notifikasi ke penginput
                if ($locked->diinput_oleh) {
                    try {
                        Notifikasi::create([
                            'user_id'        => $locked->diinput_oleh,
                            'judul'          => "{$namaSiswa} Telah Kembali",
                            'pesan'          => "Siswa {$namaSiswa} telah kembali ke sekolah pada " . Carbon::now()->format('H:i') . ' WIB.',
                            'tipe'           => 'dispensasi_siswa',
                            'reference_id'   => $locked->id,
                            'reference_type' => DispensasiSiswa::class,
                            'is_read'        => false,
                        ]);
                    } catch (\Exception $e) {}
                }

                return "Siswa {$namaSiswa} tercatat telah kembali ke sekolah.";
            });

            if (!$msg) {
                return back()->with('error', 'Status dispensasi bukan Disetujui. Tidak dapat mencatat kembali.');
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    /**
     * Cetak Slip / Tiket Keluar Dispensasi Siswa
     */
    public function cetakDispensasi($id)
    {
        Carbon::setLocale('id');
        $dispensasi = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser', 'disetujuiOlehUser', 'piketApprovedByUser'])->findOrFail($id);

        if (!in_array($dispensasi->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai'])) {
            abort(403, 'Surat izin dispensasi tidak dapat dicetak sebelum disetujui.');
        }

        $rosterTarget = JadwalPiketKbm::getRosterForDate(Carbon::parse($dispensasi->tanggal));
        $wakaPiket = $rosterTarget['waka'] ?? null;

        return view('guru_piket.dispensasi.cetak', compact('dispensasi', 'wakaPiket'));
    }

    /**
     * Halaman Manajemen Perizinan & Sakit Siswa
     */
    public function izinSiswa(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $guruPiket = $user->role === 'guru_piket' ? $user->guruPiket : null;

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $search  = $request->input('search');
        $jenis   = $request->input('jenis_izin');
        $idKelas = $request->input('id_kelas');

        $izinSiswaList = IzinSiswa::with(['siswa.kelas', 'diinputOlehUser'])
            ->when($tanggal, function ($q) use ($tanggal) {
                $q->whereDate('tanggal_mulai', '<=', $tanggal)
                  ->whereDate('tanggal_selesai', '>=', $tanggal);
            })
            ->when($jenis, fn($q) => $q->where('jenis_izin', $jenis))
            ->when($idKelas, function ($q) use ($idKelas) {
                $q->whereHas('siswa', fn($qs) => $qs->where('id_kelas', $idKelas));
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('siswa', function ($qs) use ($search) {
                    $qs->where('nama_lengkap', 'LIKE', "%{$search}%")
                       ->orWhere('nisn', 'LIKE', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // Rekap KPI pada tanggal yang dipilih
        $allIzinToday = IzinSiswa::whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->where('status', '!=', 'Ditolak')
            ->get();

        $kpi = [
            'total'      => $allIzinToday->count(),
            'sakit'      => $allIzinToday->where('jenis_izin', 'Sakit')->count(),
            'izin'       => $allIzinToday->where('jenis_izin', 'Izin')->count(),
            'dispensasi' => $allIzinToday->where('jenis_izin', 'Dispensasi')->count(),
        ];

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(['id_kelas', 'nama_kelas', 'tingkat', 'jurusan']);

        $siswaSelectOption = Siswa::with('kelas')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get(['id_siswa', 'nama_lengkap', 'nisn', 'id_kelas']);

        return view('guru_piket.izin_siswa.index', compact(
            'user', 'guruPiket', 'izinSiswaList', 'tanggal', 'search', 'jenis', 'idKelas',
            'kpi', 'kelasList', 'siswaSelectOption'
        ));
    }

    /**
     * Simpan Pencatatan Izin / Sakit Siswa
     */
    public function storeIzinSiswa(Request $request)
    {
        $request->validate([
            'id_siswa'        => 'required|exists:siswa,id_siswa',
            'jenis_izin'      => 'required|in:Sakit,Izin,Dispensasi',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|max:500',
            'bukti_file'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('izin_siswa', 'public');
        }

        $izin = IzinSiswa::create([
            'id_siswa'        => $request->id_siswa,
            'jenis_izin'      => $request->jenis_izin,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan'          => $request->alasan,
            'bukti_file'      => $buktiPath,
            'status'          => 'Disetujui',
            'diinput_oleh'    => Auth::id(),
            'catatan'         => $request->catatan,
        ]);

        $siswa = Siswa::find($request->id_siswa);
        $namaSiswa = $siswa->nama_lengkap ?? 'Siswa';

        LogAktivitas::catat(
            'Izin Siswa Piket',
            "Guru Piket mencatat perizinan {$request->jenis_izin} untuk {$namaSiswa} ({$request->tanggal_mulai} s/d {$request->tanggal_selesai})",
            $izin,
            Auth::user()
        );

        return back()->with('success', "Data perizinan {$request->jenis_izin} untuk {$namaSiswa} berhasil dicatat. Presensi siswa di jurnal guru mapel akan terisi otomatis.");
    }

    /**
     * Hapus Catatan Izin Siswa
     */
    public function destroyIzinSiswa($id)
    {
        $izin = IzinSiswa::with('siswa')->findOrFail($id);
        $namaSiswa = $izin->siswa->nama_lengkap ?? 'Siswa';
        $jenis = $izin->jenis_izin ?? 'Izin';
        if ($izin->bukti_file && Storage::disk('public')->exists($izin->bukti_file)) {
            Storage::disk('public')->delete($izin->bukti_file);
        }

        $izin->delete();

        LogAktivitas::catat(
            'Hapus Izin Siswa',
            "Guru Piket menghapus catatan perizinan {$jenis} untuk {$namaSiswa}",
            null,
            Auth::user()
        );

        return back()->with('success', "Catatan perizinan {$namaSiswa} berhasil dihapus.");
    }

    /**
     * Halaman Monitoring & Persetujuan Izin Guru oleh Guru Piket
     */
    public function izinGuru(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $guruPiket = $user->role === 'guru_piket' ? $user->guruPiket : null;

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($tanggal);
        $hariIni = $targetDate->translatedFormat('l');
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // 1. Guru yang izin pada rentang tanggal terpilih (menggunakan scopeBerlaku untuk monitoring kelas terdampak)
        $izinGuruList = IzinGuru::berlaku()
            ->with(['guru', 'diinputOlehUser'])
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->orderByDesc('id')
            ->get();

        $izinGuruIds = $izinGuruList->pluck('id_guru')->toArray();

        // 2. Jadwal kelas yang terdampak guru berhalangan
        $jadwalTerdampak = collect();
        if (count($izinGuruIds) > 0) {
            $jadwalTerdampak = JadwalPelajaran::with(['guru', 'kelas', 'mapel'])
                ->where('hari', $hariIni)
                ->whereIn('id_guru', $izinGuruIds)
                ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
                ->orderBy('jam_ke')
                ->get();

            // Petakan data izin dan status jurnal pendampingan hari ini
            $existingJurnalsToday = JurnalMengajar::whereIn('id_jadwal', $jadwalTerdampak->pluck('id_jadwal'))
                ->where('tanggal', $tanggal)
                ->get()
                ->keyBy('id_jadwal');

            $jadwalTerdampak->each(function ($jt) use ($izinGuruList, $existingJurnalsToday) {
                $jt->izin_guru = $izinGuruList->firstWhere('id_guru', $jt->id_guru);
                $jt->jurnal_terisi = $existingJurnalsToday->get($jt->id_jadwal);
            });
        }

        // 3. Semua riwayat izin untuk tab riwayat lengkap
        $semuaIzinList = IzinGuru::with(['guru', 'diinputOlehUser', 'dibatalkanOlehUser'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('guru_piket.izin_guru.index', compact(
            'user', 'guruPiket', 'tanggal', 'todayFormatted', 'hariIni',
            'izinGuruList', 'jadwalTerdampak', 'semuaIzinList'
        ));
    }

    /**
     * Pengalihan: Pengisian Jurnal KBM dilakukan mandiri oleh Guru Pengampu
     */
    public function formJurnalPendampingan($id_jadwal, Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        return redirect()->route('guru-piket.izin-guru', ['tanggal' => $tanggal])
            ->with('info', 'Pengisian jurnal pembelajaran dilakukan langsung oleh guru pengampu yang bersangkutan melalui menu jurnal mereka (dengan status Izin, Sakit, atau Dinas). Guru Piket bertugas mengawasi kelas dan menyampaikan instruksi tugas jika ada.');
    }

    /**
     * Pengalihan: Guru Piket tidak mengisi jurnal untuk guru izin
     */
    public function storeJurnalPendampingan(Request $request, $id_jadwal)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        return redirect()->route('guru-piket.izin-guru', ['tanggal' => $tanggal])
            ->with('info', 'Pengisian jurnal pembelajaran dilakukan langsung oleh guru pengampu yang bersangkutan.');
    }

    /**
     * Halaman Rekap Presensi Siswa Se-Sekolah
     */
    public function rekapPresensi(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $guruPiket = $user->role === 'guru_piket' ? $user->guruPiket : null;

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $todayFormatted = Carbon::parse($tanggal)->translatedFormat('l, j F Y');
        $statusFilter = $request->input('status');
        $kelasFilter  = $request->input('id_kelas');

        $allKelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $presensiQuery = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $tanggal))
            ->with(['siswa.kelas', 'jurnal.mapel', 'jurnal.guru'])
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->when($kelasFilter, function ($q) use ($kelasFilter) {
                $q->whereHas('siswa', fn($qs) => $qs->where('id_kelas', $kelasFilter));
            });

        $presensiList = $presensiQuery->paginate(25)->withQueryString();

        // Summary counts
        $allPresensiHari = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $tanggal))->get();
        $summary = [
            'total'      => $allPresensiHari->count(),
            'hadir'      => $allPresensiHari->where('status', 'Hadir')->count(),
            'sakit'      => $allPresensiHari->where('status', 'Sakit')->count(),
            'izin'       => $allPresensiHari->where('status', 'Izin')->count(),
            'alpha'      => $allPresensiHari->where('status', 'Alpha')->count(),
            'dispensasi' => $allPresensiHari->where('status', 'Dispensasi')->count(),
        ];

        return view('guru_piket.rekap.index', compact(
            'user', 'guruPiket', 'tanggal', 'todayFormatted', 'presensiList', 'allKelasList',
            'statusFilter', 'kelasFilter', 'summary'
        ));
    }

    /**
     * Halaman Catatan & Laporan Piket Harian
     */
    public function laporan(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $guruPiket = $user->role === 'guru_piket' ? $user->guruPiket : null;

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $todayFormatted = Carbon::parse($tanggal)->translatedFormat('l, j F Y');

        $laporan = LaporanPiket::where('tanggal', $tanggal)->first();
        $allRiwayatLaporan = LaporanPiket::with(['user', 'guruPiket'])->orderByDesc('tanggal')->take(15)->get();

        return view('guru_piket.laporan.index', compact(
            'user', 'guruPiket', 'tanggal', 'todayFormatted', 'laporan', 'allRiwayatLaporan'
        ));
    }

    /**
     * Simpan Catatan / Berita Acara Piket
     */
    public function storeLaporan(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'catatan_kejadian' => 'nullable|string',
            'jam_mulai_piket'  => 'nullable',
            'jam_selesai_piket'=> 'nullable',
        ]);

        $tanggal = $request->tanggal;
        $user = Auth::user();
        $guruPiketId = $user->guruPiket ? $user->guruPiket->id : null;

        // Auto calculate metrics
        $guruHadir = JurnalMengajar::where('tanggal', $tanggal)->where('status_guru', 'Hadir')->distinct('id_guru')->count('id_guru');
        $guruIzin  = IzinGuru::berlaku()->where('tanggal_mulai', '<=', $tanggal)->where('tanggal_selesai', '>=', $tanggal)->distinct('id_guru')->count('id_guru');
        $dispSiswa = DispensasiSiswa::where('tanggal', $tanggal)->count();
        $alphaSiswa = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $tanggal))->where('status', 'Alpha')->count();

        LaporanPiket::updateOrCreate(
            ['tanggal' => $tanggal],
            [
                'user_id'                 => $user->id,
                'guru_piket_id'           => $guruPiketId,
                'jam_mulai_piket'         => $request->jam_mulai_piket ?? '06:45',
                'jam_selesai_piket'       => $request->jam_selesai_piket ?? '15:30',
                'catatan_kejadian'        => $request->catatan_kejadian,
                'jumlah_guru_hadir'       => $guruHadir,
                'jumlah_guru_izin'        => $guruIzin,
                'jumlah_siswa_dispensasi' => $dispSiswa,
                'jumlah_siswa_alpha'      => $alphaSiswa,
                'status_piket'            => 'Selesai',
            ]
        );

        return back()->with('success', 'Catatan laporan piket harian berhasil disimpan.');
    }

    /**
     * Cetak Berita Acara / Laporan Piket Harian
     */
    public function cetakLaporan(Request $request)
    {
        Carbon::setLocale('id');
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($tanggal);
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');
        $hariIni = $targetDate->translatedFormat('l');

        $laporan = LaporanPiket::with(['user', 'guruPiket'])->where('tanggal', $tanggal)->first();

        // Data presensi
        $presensiSummary = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $tanggal))
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Data guru izin
        $guruIzinList = IzinGuru::berlaku()
            ->with('guru')
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->get();

        // Data dispensasi
        $dispensasiList = DispensasiSiswa::with('siswa.kelas')
            ->where('tanggal', $tanggal)
            ->get();

        // Data jurnal
        $jurnalList = JurnalMengajar::with(['guru', 'kelas', 'mapel'])
            ->where('tanggal', $tanggal)
            ->orderBy('jam_mulai')
            ->get();

        $piketStaff = GuruPiket::where('status_aktif', true)->get();
        $userKepsek = User::where('role', 'kepala_sekolah')->first();
        $kepalaSekolah = KepalaSekolah::where('status_aktif', true)->first() 
            ?? ($userKepsek ? $userKepsek->kepalaSekolah : null)
            ?? KepalaSekolah::first();

        $namaKepalaSekolah = $kepalaSekolah->nama_lengkap 
            ?? ($userKepsek ? trim(preg_replace('/\s*\([^)]*\)$/', '', $userKepsek->name)) : 'Kepala Sekolah');
        $nipKepalaSekolah = $kepalaSekolah->nip ?? '-';

        return view('guru_piket.laporan.cetak', compact(
            'tanggal', 'todayFormatted', 'hariIni', 'laporan', 'presensiSummary',
            'guruIzinList', 'dispensasiList', 'jurnalList', 'piketStaff', 'kepalaSekolah',
            'namaKepalaSekolah', 'nipKepalaSekolah'
        ));
    }

    /**
     * Toggle status Upacara (Senin) atau Pembiasaan (Jumat)
     */
    public function toggleStatusKbm(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($tanggal);
        $hariIni = $targetDate->translatedFormat('l');
        $catatan = $request->input('catatan');

        $status = StatusHarianKbm::firstOrNew(['tanggal' => $tanggal]);

        // Hubungkan ke data guru jika ada
        $idGuru = null;
        if ($user->role === 'guru_piket' && $user->guruPiket) {
            $idGuru = Guru::where('nip', $user->guruPiket->nip)->first()?->id_guru;
        } elseif ($user->role === 'guru' && $user->guru) {
            $idGuru = $user->guru->id_guru;
        }

        $hariLower = strtolower($hariIni);
        if ($hariLower === 'senin') {
            if ($request->has('ada_upacara')) {
                $status->ada_upacara = (bool)$request->input('ada_upacara');
            } else {
                $status->ada_upacara = !$status->ada_upacara;
            }
            $msg = $status->ada_upacara 
                ? 'Status KBM hari Senin diatur: Upacara Dilaksanakan (KBM mulai 07.40).' 
                : 'Status KBM hari Senin diatur: Upacara Ditiadakan (KBM Maju ke 07.00).';
        } elseif ($hariLower === 'jumat') {
            if ($request->has('ada_pembiasaan_jumat')) {
                $status->ada_pembiasaan_jumat = (bool)$request->input('ada_pembiasaan_jumat');
            } else {
                $status->ada_pembiasaan_jumat = !$status->ada_pembiasaan_jumat;
            }
            $msg = $status->ada_pembiasaan_jumat 
                ? 'Status KBM hari Jumat diatur: Pembiasaan Dilaksanakan (KBM mulai 07.30).' 
                : 'Status KBM hari Jumat diatur: Pembiasaan Ditiadakan (KBM Maju ke 07.00).';
        } else {
            return redirect()->back()->with('info', 'Penyesuaian jam pembiasaan otomatis hanya berlaku untuk hari Senin dan Jumat.');
        }

        if ($catatan !== null) {
            $status->catatan = $catatan;
        }
        $status->id_guru_piket = $idGuru;
        $status->save();

        LogAktivitas::catat(
            'Ubah Status KBM',
            $msg . ($status->catatan ? " (Catatan: {$status->catatan})" : ''),
            $status,
            $user
        );

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Halaman Jadwal Petugas Guru Piket KBM (Siklus A & B)
     */
    public function jadwalPiket(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $guruPiket = $user->role === 'guru_piket' ? $user->guruPiket : null;

        $tanggalInput = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $targetDate   = Carbon::parse($tanggalInput);
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');
        $selectedHari = $targetDate->translatedFormat('l');

        $activeTab = $request->input('tab', 'hari_ini'); // 'hari_ini', 'siklus_a', 'siklus_b'

        // Roster for the target date
        $rosterTarget = JadwalPiketKbm::getRosterForDate($targetDate);

        // Schedule matrices for Siklus A & B grouped by day
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $siklusAData = [];
        $siklusBData = [];

        foreach ($hariOrder as $h) {
            $siklusAData[$h] = [
                'waka' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->first(),
                'pagi_koordinator' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'koordinator')->first(),
                'pagi_petugas' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'petugas')->orderBy('urutan')->get(),
                'siang_koordinator' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'koordinator')->first(),
                'siang_petugas' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'petugas')->orderBy('urutan')->get(),
            ];

            $siklusBData[$h] = [
                'waka' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->first(),
                'pagi_koordinator' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'koordinator')->first(),
                'pagi_petugas' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'petugas')->orderBy('urutan')->get(),
                'siang_koordinator' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'koordinator')->first(),
                'siang_petugas' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'petugas')->orderBy('urutan')->get(),
            ];
        }

        return view('guru_piket.jadwal.index', compact(
            'user', 'guruPiket', 'tanggalInput', 'todayFormatted', 'selectedHari',
            'activeTab', 'rosterTarget', 'siklusAData', 'siklusBData', 'hariOrder'
        ));
    }

    /**
     * Cetak Laporan Jadwal Piket Semester Ganjil 2026/2027
     */
    public function cetakJadwalPiket(Request $request)
    {
        Carbon::setLocale('id');
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $siklusAData = [];
        $siklusBData = [];
        foreach ($hariOrder as $h) {
            $siklusAData[$h] = [
                'waka' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->first(),
                'pagi_koordinator' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'koordinator')->first(),
                'pagi_petugas' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'petugas')->orderBy('urutan')->get(),
                'siang_koordinator' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'koordinator')->first(),
                'siang_petugas' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'petugas')->orderBy('urutan')->get(),
            ];

            $siklusBData[$h] = [
                'waka' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->first(),
                'pagi_koordinator' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'koordinator')->first(),
                'pagi_petugas' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'petugas')->orderBy('urutan')->get(),
                'siang_koordinator' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'koordinator')->first(),
                'siang_petugas' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'petugas')->orderBy('urutan')->get(),
            ];
        }

        $userKepsek = User::where('role', 'kepala_sekolah')->first();
        $kepalaSekolah = KepalaSekolah::where('status_aktif', true)->first() 
            ?? ($userKepsek ? $userKepsek->kepalaSekolah : null)
            ?? KepalaSekolah::first();

        $namaKepalaSekolah = $kepalaSekolah->nama_lengkap 
            ?? 'TRISNO WIBOWO, S.Pd, M.M';
        $nipKepalaSekolah = $kepalaSekolah->nip ?? '19810115 200312 1 003';

        return view('guru_piket.jadwal.cetak', compact('siklusAData', 'siklusBData', 'hariOrder', 'namaKepalaSekolah', 'nipKepalaSekolah'));
    }

    /**
     * Panduan Resmi Guru Piket
     */
    public function help()
    {
        return view('guru_piket.help.index');
    }
}
