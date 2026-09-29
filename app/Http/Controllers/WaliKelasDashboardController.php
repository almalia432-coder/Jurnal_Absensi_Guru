<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaliKelas;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JurnalMengajar;
use App\Models\PresensiSiswa;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaliKelasDashboardController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('l, j F Y');
        $hariIni = Carbon::today()->translatedFormat('l');

        // Identify Wali Kelas and assigned Kelas
        [$waliKelas, $kelas] = $this->resolveWaliKelasAndKelas($user, $request);

        $allKelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // If no class assigned
        if (!$kelas) {
            return view('wali_kelas.dashboard.index', [
                'kelas'             => null,
                'waliKelas'         => $waliKelas,
                'user'              => $user,
                'todayFormatted'    => $todayFormatted,
                'tahunAjaranAktif'  => $tahunAjaranAktif,
                'allKelasList'      => $allKelasList,
            ]);
        }

        $idKelas = $kelas->id_kelas;

        // ── 1. KPI Siswa Binaan ──────────────────────────────────
        $totalSiswa  = Siswa::where('id_kelas', $idKelas)->where('status_aktif', true)->count();
        $totalSiswaL = Siswa::where('id_kelas', $idKelas)->where('status_aktif', true)->where('jenis_kelamin', 'L')->count();
        $totalSiswaP = Siswa::where('id_kelas', $idKelas)->where('status_aktif', true)->where('jenis_kelamin', 'P')->count();

        // ── 2. KPI Presensi Hari Ini ─────────────────────────────
        $presensiToday = PresensiSiswa::whereHas('jurnal', function ($q) use ($idKelas, $today) {
            $q->where('id_kelas', $idKelas)->where('tanggal', $today);
        })->with('siswa')->get();

        $hadirCount      = $presensiToday->where('status', 'Hadir')->pluck('id_siswa')->unique()->count();
        $sakitCount      = $presensiToday->where('status', 'Sakit')->pluck('id_siswa')->unique()->count();
        $izinCount       = $presensiToday->where('status', 'Izin')->pluck('id_siswa')->unique()->count();
        $alphaCount      = $presensiToday->where('status', 'Alpha')->pluck('id_siswa')->unique()->count();
        $dispensasiCount = $presensiToday->where('status', 'Dispensasi')->pluck('id_siswa')->unique()->count();

        $tidakHadirCount = $sakitCount + $izinCount + $alphaCount + $dispensasiCount;
        $pctHadir = $totalSiswa > 0 ? round(($hadirCount / $totalSiswa) * 100) : 0;

        // ── 3. Siswa Tidak Hadir Hari Ini ────────────────────────
        $siswaTidakMasukToday = $presensiToday->whereIn('status', ['Sakit', 'Izin', 'Alpha', 'Dispensasi'])
            ->unique('id_siswa')
            ->values();

        // ── 4. Jurnal Mengajar Hari Ini di Kelas Binaan ──────────
        $todayJurnals = JurnalMengajar::with(['guru', 'mapel'])
            ->where('id_kelas', $idKelas)
            ->where('tanggal', $today)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $totalJurnalHariIni = $todayJurnals->count();

        // Jadwal Hari Ini
        $jadwalHariIni = JadwalPelajaran::with(['mapel', 'guru'])
            ->where('id_kelas', $idKelas)
            ->where('hari', $hariIni)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->orderBy('jam_ke', 'asc')
            ->get();

        // ── 5. Siswa Perlu Perhatian (Akumulasi Alpha 30 Hari) ───
        $monthAgo = Carbon::today()->subDays(30)->format('Y-m-d');
        $siswaPerluPerhatian = PresensiSiswa::whereHas('jurnal', function ($q) use ($idKelas, $monthAgo, $today) {
                $q->where('id_kelas', $idKelas)->whereBetween('tanggal', [$monthAgo, $today]);
            })
            ->where('status', 'Alpha')
            ->select('id_siswa', DB::raw('COUNT(*) as total_alpha'))
            ->groupBy('id_siswa')
            ->having('total_alpha', '>=', 2)
            ->with('siswa')
            ->orderByDesc('total_alpha')
            ->take(5)
            ->get();

        // ── 6. Trend Presensi 7 Hari Terakhir (Chart 1) ──────────
        $trendLabels = [];
        $trendHadir  = [];
        $trendAbsen  = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $dStr = $day->format('Y-m-d');
            $trendLabels[] = $day->translatedFormat('D, d M');

            $h = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('id_kelas', $idKelas)->where('tanggal', $dStr))
                ->where('status', 'Hadir')->distinct('id_siswa')->count('id_siswa');

            $a = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('id_kelas', $idKelas)->where('tanggal', $dStr))
                ->whereIn('status', ['Sakit', 'Izin', 'Alpha', 'Dispensasi'])->distinct('id_siswa')->count('id_siswa');

            $trendHadir[] = $h;
            $trendAbsen[] = $a;
        }

        // ── 7. Komposisi Presensi Bulan Ini (Chart 2) ───────────
        $startOfMonth = Carbon::today()->startOfMonth()->format('Y-m-d');
        $monthlyCounts = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('id_kelas', $idKelas)->whereBetween('tanggal', [$startOfMonth, $today]))
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $pieData = [
            'Hadir'      => $monthlyCounts['Hadir'] ?? 0,
            'Sakit'      => $monthlyCounts['Sakit'] ?? 0,
            'Izin'       => $monthlyCounts['Izin'] ?? 0,
            'Alpha'      => $monthlyCounts['Alpha'] ?? 0,
            'Dispensasi' => $monthlyCounts['Dispensasi'] ?? 0,
        ];

        // ── 8. Daftar Seluruh Siswa di Kelas Binaan ──────────────
        $siswaList = Siswa::where('id_kelas', $idKelas)
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return view('wali_kelas.dashboard.index', compact(
            'user', 'waliKelas', 'kelas', 'allKelasList', 'tahunAjaranAktif',
            'todayFormatted', 'hariIni',
            'totalSiswa', 'totalSiswaL', 'totalSiswaP',
            'hadirCount', 'sakitCount', 'izinCount', 'alphaCount', 'dispensasiCount',
            'tidakHadirCount', 'pctHadir',
            'siswaTidakMasukToday', 'todayJurnals', 'jadwalHariIni', 'totalJurnalHariIni',
            'siswaPerluPerhatian',
            'trendLabels', 'trendHadir', 'trendAbsen',
            'pieData', 'siswaList'
        ));
    }

    /**
     * View dedicated list of students in homeroom class
     */
    public function siswa(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        [$waliKelas, $kelas] = $this->resolveWaliKelasAndKelas($user, $request);

        $allKelasList = Kelas::orderBy('nama_kelas')->get();
        $search = $request->input('search');

        $siswaList = collect();
        if ($kelas) {
            $siswaList = Siswa::where('id_kelas', $kelas->id_kelas)
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($q2) use ($search) {
                        $q2->where('nama_lengkap', 'LIKE', "%{$search}%")
                           ->orWhere('nisn', 'LIKE', "%{$search}%")
                           ->orWhere('nis', 'LIKE', "%{$search}%");
                    });
                })
                ->orderBy('nama_lengkap')
                ->paginate(20)
                ->withQueryString();
        }

        return view('wali_kelas.siswa.index', compact('user', 'waliKelas', 'kelas', 'allKelasList', 'siswaList', 'search'));
    }

    /**
     * View journal & attendance records of the homeroom class
     */
    public function jurnal(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        [$waliKelas, $kelas] = $this->resolveWaliKelasAndKelas($user, $request);

        $allKelasList = Kelas::orderBy('nama_kelas')->get();
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));

        $jurnalList = collect();
        if ($kelas) {
            $jurnalList = JurnalMengajar::with(['guru', 'mapel', 'presensiSiswa.siswa'])
                ->where('id_kelas', $kelas->id_kelas)
                ->when($tanggal, fn($q) => $q->where('tanggal', $tanggal))
                ->orderBy('jam_mulai', 'asc')
                ->get();
        }

        return view('wali_kelas.jurnal.index', compact('user', 'waliKelas', 'kelas', 'allKelasList', 'jurnalList', 'tanggal'));
    }

    /**
     * Helper to resolve Wali Kelas and Kelas Binaan for current user
     */
    private function resolveWaliKelasAndKelas($user, Request $request): array
    {
        $waliKelas = null;
        $kelas = null;

        if (!$user) {
            $user = Auth::user();
        }

        if ($user->isWaliKelas()) {
            $waliKelas = $user->waliKelas;
            if (!$waliKelas && $user->guru) {
                $waliKelas = WaliKelas::where('nip', $user->guru->nip)->first();
            }
            if ($waliKelas) {
                $kelas = Kelas::with(['jurusanRelation', 'waliKelas'])->where('wali_kelas_id', $waliKelas->id)->first();
            } elseif ($user->kelas_binaan) {
                $kelas = $user->kelas_binaan;
                $waliKelas = $kelas->waliKelas;
            }
        }

        // If user is admin or requested specific class
        if (!$kelas && ($user->role === 'admin' || $request->has('kelas_id'))) {
            $targetKelasId = $request->input('kelas_id');
            if ($targetKelasId) {
                $kelas = Kelas::with(['jurusanRelation', 'waliKelas'])->find($targetKelasId);
            } else {
                $kelas = Kelas::with(['jurusanRelation', 'waliKelas'])->first();
            }
            if ($kelas && $kelas->waliKelas) {
                $waliKelas = $kelas->waliKelas;
            }
        }

        return [$waliKelas, $kelas];
    }
}
