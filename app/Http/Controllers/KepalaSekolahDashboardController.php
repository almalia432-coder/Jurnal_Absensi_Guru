<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IzinGuru;
use App\Models\DispensasiSiswa;
use App\Models\Guru;
use App\Models\Siswa;
use Carbon\Carbon;

class KepalaSekolahDashboardController extends Controller
{
    // ───────────────────────────── HELPERS ─────────────────────────────

    private function getSharedStats(): array
    {
        $today = Carbon::today();

        // Izin Guru
        $izinHariIni = IzinGuru::whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->whereIn('status', ['Disetujui', 'Selesai'])
            ->count();

        $izinMenunggu = IzinGuru::where('status', 'Menunggu')->count();
        $izinBulanIni = IzinGuru::whereMonth('tanggal_mulai', $today->month)
            ->whereYear('tanggal_mulai', $today->year)->count();
        $izinMingguan = IzinGuru::whereDate('tanggal_mulai', '>=', $today->copy()->startOfWeek())
            ->whereDate('tanggal_mulai', '<=', $today->copy()->endOfWeek())->count();

        // Dispensasi Siswa
        $dispHariIni = DispensasiSiswa::whereDate('tanggal', $today)
            ->whereIn('status', ['Disetujui', 'Selesai'])
            ->count();
        $dispMenunggu = DispensasiSiswa::where('status', 'Menunggu')->count();
        $dispBulanIni = DispensasiSiswa::whereMonth('tanggal', $today->month)
            ->whereYear('tanggal', $today->year)->count();
        $dispMingguan = DispensasiSiswa::whereDate('tanggal', '>=', $today->copy()->startOfWeek())
            ->whereDate('tanggal', '<=', $today->copy()->endOfWeek())->count();

        return compact(
            'izinHariIni', 'izinMenunggu', 'izinBulanIni', 'izinMingguan',
            'dispHariIni', 'dispMenunggu', 'dispBulanIni', 'dispMingguan'
        );
    }

    // ───────────────────────────── DASHBOARD ─────────────────────────────

    public function dashboard()
    {
        $today = Carbon::today();
        $stats = $this->getSharedStats();

        // 7-day trend data for chart
        $trendLabels = [];
        $trendIzin = [];
        $trendDisp = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $trendLabels[] = $day->locale('id')->isoFormat('ddd D/M');
            $trendIzin[] = IzinGuru::whereDate('tanggal_mulai', '<=', $day)
                ->whereDate('tanggal_selesai', '>=', $day)
                ->whereIn('status', ['Disetujui', 'Selesai'])
                ->count();
            $trendDisp[] = DispensasiSiswa::whereDate('tanggal', $day)
                ->whereIn('status', ['Disetujui', 'Selesai'])
                ->count();
        }

        // Recent izin guru (last 5 approved/pending)
        $recentIzin = IzinGuru::with('guru')
            ->whereIn('status', ['Disetujui', 'Menunggu', 'Selesai'])
            ->latest()
            ->take(5)
            ->get();

        // Recent dispensasi (last 5)
        $recentDisp = DispensasiSiswa::with(['siswa', 'siswa.kelas'])
            ->whereIn('status', ['Disetujui', 'Menunggu', 'Selesai'])
            ->latest()
            ->take(5)
            ->get();

        // Guru aktif izin hari ini (for detail list)
        $guruIzinHariIni = IzinGuru::with('guru')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->whereIn('status', ['Disetujui', 'Selesai'])
            ->get();

        // Siswa dispensasi hari ini
        $siswaDispHariIni = DispensasiSiswa::with(['siswa', 'siswa.kelas'])
            ->whereDate('tanggal', $today)
            ->whereIn('status', ['Disetujui', 'Selesai'])
            ->get();

        // Izin by type (for mini donut)
        $izinByType = IzinGuru::selectRaw('jenis_izin, count(*) as total')
            ->whereMonth('tanggal_mulai', $today->month)
            ->whereYear('tanggal_mulai', $today->year)
            ->groupBy('jenis_izin')
            ->pluck('total', 'jenis_izin')
            ->toArray();

        // expose notif count to layout
        $kepsekIzinMenunggu = $stats['izinMenunggu'];
        $kepsekDispMenunggu = $stats['dispMenunggu'];

        return view('kepala_sekolah.dashboard.index', array_merge($stats, compact(
            'trendLabels', 'trendIzin', 'trendDisp',
            'recentIzin', 'recentDisp',
            'guruIzinHariIni', 'siswaDispHariIni',
            'izinByType',
            'kepsekIzinMenunggu', 'kepsekDispMenunggu',
            'today'
        )));
    }

    // ───────────────────────────── GURU IZIN PAGE ─────────────────────────────

    public function izinGuru(Request $request)
    {
        $today = Carbon::today();
        $stats = $this->getSharedStats();
        $kepsekIzinMenunggu = $stats['izinMenunggu'];
        $kepsekDispMenunggu = $stats['dispMenunggu'];

        $query = IzinGuru::with('guru');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_izin', $request->jenis);
        }
        if ($request->filled('tanggal')) {
            $tgl = Carbon::parse($request->tanggal);
            $query->whereDate('tanggal_mulai', '<=', $tgl)->whereDate('tanggal_selesai', '>=', $tgl);
        }
        if ($request->filled('q')) {
            $kw = $request->q;
            $query->whereHas('guru', fn($g) => $g->where('nama_lengkap', 'like', "%$kw%"));
        }

        $izinList = $query->latest()->paginate(15);

        // Stats for cards
        $guruAktifIzin = IzinGuru::whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->whereIn('status', ['Disetujui', 'Selesai'])->count();
        $totalGuru = Guru::where('status_aktif', true)->count();

        return view('kepala_sekolah.izin_guru.index', array_merge($stats, compact(
            'izinList', 'guruAktifIzin', 'totalGuru',
            'kepsekIzinMenunggu', 'kepsekDispMenunggu', 'today'
        )));
    }

    // ───────────────────────────── DISPENSASI PAGE ─────────────────────────────

    public function dispensasi(Request $request)
    {
        $today = Carbon::today();
        $stats = $this->getSharedStats();
        $kepsekIzinMenunggu = $stats['izinMenunggu'];
        $kepsekDispMenunggu = $stats['dispMenunggu'];

        $query = DispensasiSiswa::with(['siswa', 'siswa.kelas']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('q')) {
            $kw = $request->q;
            $query->whereHas('siswa', fn($s) => $s->where('nama_lengkap', 'like', "%$kw%")
                ->orWhere('nis', 'like', "%$kw%"));
        }

        $dispList = $query->latest()->paginate(15);

        // Stats for cards
        $siswaDispHariIni = DispensasiSiswa::whereDate('tanggal', $today)
            ->whereIn('status', ['Disetujui', 'Selesai'])->count();
        $siswaDispBulan = DispensasiSiswa::whereMonth('tanggal', $today->month)
            ->whereYear('tanggal', $today->year)->count();

        return view('kepala_sekolah.dispensasi.index', array_merge($stats, compact(
            'dispList', 'siswaDispHariIni', 'siswaDispBulan',
            'kepsekIzinMenunggu', 'kepsekDispMenunggu', 'today'
        )));
    }
}
