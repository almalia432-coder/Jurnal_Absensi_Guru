<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Waka;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\PresensiSiswa;
use App\Models\DispensasiSiswa;
use App\Models\IzinSiswa;
use App\Models\JurnalMengajar;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WakaKesiswaanDashboardController extends Controller
{
    /**
     * Dashboard Utama Waka Kesiswaan & Kedisiplinan
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('l, d F Y');

        // 1. KPI Metrics
        $totalSiswa = Siswa::where('status_aktif', true)->count();

        // Presensi Siswa Hari Ini (dari jurnal KBM hari ini)
        $presensiToday = PresensiSiswa::whereHas('jurnal', function ($q) use ($today) {
            $q->where('tanggal', $today);
        })->get();

        $hadirToday = $presensiToday->where('status', 'Hadir')->pluck('id_siswa')->unique()->count();
        $sakitToday = $presensiToday->where('status', 'Sakit')->pluck('id_siswa')->unique()->count();
        $izinToday  = $presensiToday->where('status', 'Izin')->pluck('id_siswa')->unique()->count();
        $alphaToday = $presensiToday->where('status', 'Alpha')->pluck('id_siswa')->unique()->count();

        // Dispensasi Siswa Hari Ini
        $dispensasiHariIni = DispensasiSiswa::whereDate('tanggal', $today)->get();
        $dispensasiCount = $dispensasiHariIni->count();
        $dispensasiMenunggu = $dispensasiHariIni->where('status', 'Menunggu')->count();
        $dispensasiDisetujui = $dispensasiHariIni->filter(fn($d) => in_array($d->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai']))->count();

        // 2. Daftar Dispensasi Aktif Hari Ini (untuk pemantauan kesiswaan)
        $dispensasiList = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser', 'disetujuiOlehUser'])
            ->whereDate('tanggal', $today)
            ->orderByDesc('id')
            ->take(10)
            ->get();

        // 3. Siswa Butuh Pembinaan (Top 5 Alpha akumulatif bulan ini)
        $startOfMonth = Carbon::today()->startOfMonth()->format('Y-m-d');
        $siswaAlphaTop = PresensiSiswa::where('status', 'Alpha')
            ->whereHas('jurnal', function ($q) use ($startOfMonth, $today) {
                $q->whereBetween('tanggal', [$startOfMonth, $today]);
            })
            ->select('id_siswa', DB::raw('count(*) as total_alpha'))
            ->groupBy('id_siswa')
            ->orderByDesc('total_alpha')
            ->with(['siswa.kelas'])
            ->take(5)
            ->get();

        // 4. Data Statistik Presensi per Tingkat / Jurusan (Untuk Chart)
        $kelasList = Kelas::withCount([
            'siswa as total_siswa' => fn($q) => $q->where('status_aktif', true)
        ])->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('waka_kesiswaan.dashboard.index', compact(
            'user', 'waka', 'today', 'todayFormatted',
            'totalSiswa', 'hadirToday', 'sakitToday', 'izinToday', 'alphaToday',
            'dispensasiCount', 'dispensasiMenunggu', 'dispensasiDisetujui',
            'dispensasiList', 'siswaAlphaTop', 'kelasList'
        ));
    }

    /**
     * Halaman Monitoring Presensi Siswa Se-Sekolah
     */
    public function presensi(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $kelasId = $request->input('id_kelas');
        $statusFilter = $request->input('status');
        $search = $request->input('search');

        $query = PresensiSiswa::with(['siswa.kelas', 'jurnal.mapel', 'jurnal.guru'])
            ->whereHas('jurnal', function ($q) use ($tanggal) {
                $q->where('tanggal', $tanggal);
            });

        if ($kelasId) {
            $query->whereHas('siswa', fn($q) => $q->where('id_kelas', $kelasId));
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        $presensiList = $query->orderByDesc('id_presensi')->paginate(20)->withQueryString();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        // Ringkasan status hari terpilih
        $baseQuery = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $tanggal));
        $summary = [
            'hadir' => (clone $baseQuery)->where('status', 'Hadir')->distinct('id_siswa')->count('id_siswa'),
            'sakit' => (clone $baseQuery)->where('status', 'Sakit')->distinct('id_siswa')->count('id_siswa'),
            'izin'  => (clone $baseQuery)->where('status', 'Izin')->distinct('id_siswa')->count('id_siswa'),
            'alpha' => (clone $baseQuery)->where('status', 'Alpha')->distinct('id_siswa')->count('id_siswa'),
        ];

        return view('waka_kesiswaan.presensi.index', compact(
            'user', 'waka', 'tanggal', 'kelasId', 'statusFilter', 'search',
            'presensiList', 'kelasList', 'summary'
        ));
    }

    /**
     * Halaman Monitoring Dispensasi Siswa Se-Sekolah
     */
    public function dispensasi(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $tanggal = $request->input('tanggal');
        $status  = $request->input('status');
        $search  = $request->input('search');

        $query = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser', 'disetujuiOlehUser']);

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        $dispensasiList = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $metrics = [
            'total'            => DispensasiSiswa::count(),
            'menunggu'         => DispensasiSiswa::where('status', 'Menunggu')->count(),
            'disetujui_piket'  => DispensasiSiswa::where('status', 'Disetujui_Piket')->count(),
            'disetujui'        => DispensasiSiswa::final()->count(),
            'selesai'          => DispensasiSiswa::where('status', 'Selesai')->count(),
            'ditolak'          => DispensasiSiswa::where('status', 'Ditolak')->count(),
        ];

        return view('waka_kesiswaan.dispensasi.index', compact(
            'user', 'waka', 'tanggal', 'status', 'search', 'dispensasiList', 'metrics'
        ));
    }

    /**
     * Rekap Siswa Butuh Pembinaan (Alpha / Indisipliner)
     */
    public function rekapKedisiplinan(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $bulan = $request->input('bulan', Carbon::today()->format('Y-m'));
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        // Siswa dengan akumulasi Alpha / Izin terbanyak di bulan terpilih
        $siswaIndisipliner = PresensiSiswa::whereIn('status', ['Alpha', 'Izin', 'Sakit'])
            ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->select(
                'id_siswa',
                DB::raw("SUM(CASE WHEN status = 'Alpha' THEN 1 ELSE 0 END) as count_alpha"),
                DB::raw("SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as count_izin"),
                DB::raw("SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as count_sakit"),
                DB::raw("COUNT(*) as total_absen")
            )
            ->groupBy('id_siswa')
            ->having('count_alpha', '>', 0)
            ->orderByDesc('count_alpha')
            ->with(['siswa.kelas'])
            ->paginate(20)
            ->withQueryString();

        return view('waka_kesiswaan.kedisiplinan.index', compact(
            'user', 'waka', 'bulan', 'siswaIndisipliner'
        ));
    }
}
