<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\PresensiSiswa;
use App\Models\JurnalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\DispensasiSiswa;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WaliMuridDashboardController extends Controller
{
    /**
     * Get active student associated with current user or fallback
     */
    private function getStudent(User $user): ?Siswa
    {
        // 1. Direct relationship
        if ($user->siswa) {
            return $user->siswa->load('kelas.waliKelas');
        }

        // 2. Check if student has user_id = user->id
        $siswa = Siswa::with('kelas.waliKelas')->where('user_id', $user->id)->first();
        if ($siswa) return $siswa;

        // 3. Fallback to first active student for demo convenience
        return Siswa::with('kelas.waliKelas')->where('status_aktif', true)->first();
    }

    /**
     * Dashboard Utama Portal Wali Murid / Siswa
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $siswa = $this->getStudent($user);

        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('l, d F Y');
        $hariIni = Carbon::today()->translatedFormat('l');

        if (!$siswa) {
            return view('wali_murid.dashboard.no_siswa', compact('user', 'todayFormatted'));
        }

        // 1. Presensi Siswa Hari Ini (dari Jurnal Mengajar di kelasnya)
        $presensiHariIni = PresensiSiswa::with(['jurnal.mapel', 'jurnal.guru'])
            ->where('id_siswa', $siswa->id_siswa)
            ->whereHas('jurnal', fn($q) => $q->where('tanggal', $today))
            ->get();

        // 2. Materi Pembelajaran / Jurnal Kelas Hari Ini
        $jurnalKelasHariIni = JurnalMengajar::with(['mapel', 'guru'])
            ->where('id_kelas', $siswa->id_kelas)
            ->where('tanggal', $today)
            ->orderBy('jam_ke')
            ->get();

        // 3. Rekap Statistik Presensi Siswa Keseluruhan
        $allPresensi = PresensiSiswa::where('id_siswa', $siswa->id_siswa)->get();
        $totalSesi = $allPresensi->count();
        $hadirCount = $allPresensi->where('status', 'Hadir')->count();
        $sakitCount = $allPresensi->where('status', 'Sakit')->count();
        $izinCount  = $allPresensi->where('status', 'Izin')->count();
        $alphaCount = $allPresensi->where('status', 'Alpha')->count();
        $dispCount  = $allPresensi->where('status', 'Dispensasi')->count();
        $pctKehadiran = $totalSesi > 0 ? round(($hadirCount / $totalSesi) * 100, 1) : 100;

        // 4. Jadwal Pelajaran Kelas Siswa Hari Ini
        $jadwalHariIni = JadwalPelajaran::with(['mapel', 'guru'])
            ->where('id_kelas', $siswa->id_kelas)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // 5. Surat Dispensasi Terbaru
        $dispensasiTerbaru = DispensasiSiswa::where('id_siswa', $siswa->id_siswa)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('wali_murid.dashboard.index', compact(
            'user', 'siswa', 'today', 'todayFormatted', 'hariIni',
            'presensiHariIni', 'jurnalKelasHariIni', 'jadwalHariIni', 'dispensasiTerbaru',
            'totalSesi', 'hadirCount', 'sakitCount', 'izinCount', 'alphaCount', 'dispCount', 'pctKehadiran'
        ));
    }

    /**
     * Riwayat Presensi Lengkap Siswa
     */
    public function presensi(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $siswa = $this->getStudent($user);

        if (!$siswa) {
            return redirect()->route('wali-murid.dashboard');
        }

        $bulanFilter = $request->input('bulan', Carbon::today()->format('Y-m'));
        $statusFilter = $request->input('status');

        $presensiList = PresensiSiswa::with(['jurnal.mapel', 'jurnal.guru'])
            ->where('id_siswa', $siswa->id_siswa)
            ->whereHas('jurnal', function ($q) use ($bulanFilter) {
                if ($bulanFilter) {
                    $q->where('tanggal', 'LIKE', "{$bulanFilter}%");
                }
            })
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->orderByDesc('id_presensi')
            ->paginate(15)
            ->withQueryString();

        return view('wali_murid.presensi.index', compact(
            'user', 'siswa', 'presensiList', 'bulanFilter', 'statusFilter'
        ));
    }

    /**
     * Catatan Jurnal & Materi Pembelajaran di Kelas
     */
    public function jurnal(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $siswa = $this->getStudent($user);

        if (!$siswa) {
            return redirect()->route('wali-murid.dashboard');
        }

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $search  = $request->input('search');

        $jurnalList = JurnalMengajar::with(['mapel', 'guru'])
            ->where('id_kelas', $siswa->id_kelas)
            ->when($tanggal, fn($q) => $q->where('tanggal', $tanggal))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('materi', 'LIKE', "%{$search}%")
                        ->orWhere('catatan', 'LIKE', "%{$search}%")
                        ->orWhereHas('mapel', fn($qm) => $qm->where('nama_mapel', 'LIKE', "%{$search}%"));
                });
            })
            ->orderBy('jam_ke')
            ->paginate(10)
            ->withQueryString();

        return view('wali_murid.jurnal.index', compact(
            'user', 'siswa', 'jurnalList', 'tanggal', 'search'
        ));
    }

    /**
     * Jadwal Pelajaran Mingguan Kelas Siswa
     */
    public function jadwal(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $siswa = $this->getStudent($user);

        if (!$siswa) {
            return redirect()->route('wali-murid.dashboard');
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalSeminggu = [];

        foreach ($hariList as $h) {
            $jadwalSeminggu[$h] = JadwalPelajaran::with(['mapel', 'guru'])
                ->where('id_kelas', $siswa->id_kelas)
                ->where('hari', $h)
                ->orderBy('jam_mulai')
                ->get();
        }

        return view('wali_murid.jadwal.index', compact(
            'user', 'siswa', 'jadwalSeminggu', 'hariList'
        ));
    }

    /**
     * Riwayat Surat Dispensasi Siswa
     */
    public function dispensasi(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $siswa = $this->getStudent($user);

        if (!$siswa) {
            return redirect()->route('wali-murid.dashboard');
        }

        $dispensasiList = DispensasiSiswa::where('id_siswa', $siswa->id_siswa)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('wali_murid.dispensasi.index', compact(
            'user', 'siswa', 'dispensasiList'
        ));
    }
}
