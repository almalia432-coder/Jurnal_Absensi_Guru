<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\JurnalMengajar;
use App\Models\PresensiSiswa;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');
        $today     = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $hariIni   = Carbon::today()->translatedFormat('l');

        // ── KPI Cards ──────────────────────────────────────────
        $totalSiswa  = Siswa::where('status_aktif', true)->count();
        $totalGuru   = Guru::where('status_aktif', true)->count();
        $totalRombel = Kelas::count();

        // Target & Realisasi Jurnal hari ini
        $jurnalTerisiCount = JurnalMengajar::where('tanggal', $today)->count();
        $targetHariIni     = JadwalPelajaran::where('hari', $hariIni)->count();
        $jurnalTargetCount = $targetHariIni > 0 ? $targetHariIni : max(1, JadwalPelajaran::count());

        // Presensi hari ini
        $hadirHariIni = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $today))
            ->where('status', 'Hadir')->distinct('id_siswa')->count('id_siswa');

        $izinSakitHariIni = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $today))
            ->whereIn('status', ['Sakit', 'Izin', 'Dispensasi'])
            ->distinct('id_siswa')->count('id_siswa');

        $alpaHariIni = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $today))
            ->where('status', 'Alpha')->distinct('id_siswa')->count('id_siswa');

        $alpaKemarin = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $yesterday))
            ->where('status', 'Alpha')->distinct('id_siswa')->count('id_siswa');

        $totalPresensiHariIni = $hadirHariIni + $izinSakitHariIni + $alpaHariIni;

        // If today has no attendance records yet (e.g. morning/outside class hours), calculate percentages gracefully
        $pctHadir = $totalPresensiHariIni > 0
            ? round(($hadirHariIni / $totalPresensiHariIni) * 100)
            : 0;

        $pctIzinSakit = $totalPresensiHariIni > 0
            ? round(($izinSakitHariIni / $totalPresensiHariIni) * 100)
            : 0;

        $pctAlpa = $totalPresensiHariIni > 0
            ? (100 - $pctHadir - $pctIzinSakit)
            : 0;

        // ── Trend Kehadiran 7 Hari Terakhir (Real Data) ────────
        $trendLabels = [];
        $trendData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $day   = Carbon::today()->subDays($i);
            $count = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('tanggal', $day->format('Y-m-d')))
                ->where('status', 'Hadir')->count();

            $trendLabels[] = $day->translatedFormat('D');
            $trendData[]   = $count;
        }

        $trend7Hari = [
            'labels' => $trendLabels,
            'data'   => $trendData,
        ];

        // ── Aktivitas Terbaru (Khusus Aktivitas Administrator & Sistem) ──────────
        $activityFeed = collect();

        // 1. Log dari tabel log_aktivitas (Aktivitas Admin / Master Data / Sistem)
        $adminLogs = LogAktivitas::with('user')
            ->adminActivities()
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        foreach ($adminLogs as $l) {
            $tglTime = $l->created_at ? Carbon::parse($l->created_at) : now();
            $aksiLower = strtolower($l->aksi ?? '');
            $modelLower = strtolower($l->model_type ?? '');

            // Tentukan tag, icon, dan styling berdasarkan aksi & model administratif
            if ($modelLower === 'user' || str_contains($aksiLower, 'user') || str_contains($aksiLower, 'akun')) {
                $tag       = 'User';
                $icon      = 'fa-solid fa-user-gear';
                $iconBg    = '#eff6ff';
                $iconColor = '#2563eb';
            } elseif ($modelLower === 'guru' || str_contains($aksiLower, 'guru')) {
                $tag       = 'Master Guru';
                $icon      = 'fa-solid fa-chalkboard-user';
                $iconBg    = '#ecfdf5';
                $iconColor = '#059669';
            } elseif ($modelLower === 'siswa' || str_contains($aksiLower, 'siswa')) {
                $tag       = 'Master Siswa';
                $icon      = 'fa-solid fa-user-graduate';
                $iconBg    = '#f5f3ff';
                $iconColor = '#7c3aed';
            } elseif ($modelLower === 'kelas' || str_contains($aksiLower, 'kelas')) {
                $tag       = 'Master Kelas';
                $icon      = 'fa-solid fa-school';
                $iconBg    = '#fffbeb';
                $iconColor = '#d97706';
            } elseif ($modelLower === 'mapel' || str_contains($aksiLower, 'mapel')) {
                $tag       = 'Master Mapel';
                $icon      = 'fa-solid fa-book';
                $iconBg    = '#f0fdfa';
                $iconColor = '#0d9488';
            } elseif (str_contains($aksiLower, 'import')) {
                $tag       = 'Impor Data';
                $icon      = 'fa-solid fa-file-import';
                $iconBg    = '#fdf4ff';
                $iconColor = '#c026d3';
            } else {
                $tag       = 'Sistem';
                $icon      = 'fa-solid fa-sliders';
                $iconBg    = '#f1f5f9';
                $iconColor = '#475569';
            }

            $activityFeed->push([
                'deskripsi'  => $l->deskripsi,
                'waktu'      => $tglTime->diffForHumans(),
                'tag'        => $tag,
                'icon'       => $icon,
                'icon_bg'    => $iconBg,
                'icon_color' => $iconColor,
                'timestamp'  => $tglTime->timestamp,
            ]);
        }

        // 2. Data administratif terbaru (User, Master Guru, Master Siswa, Kelas) untuk melengkapi feed
        $recentUsers = User::where('role', '!=', 'siswa')
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();

        foreach ($recentUsers as $u) {
            $tglTime = $u->updated_at ?? now();
            $roleLabel = match($u->role) {
                'admin' => 'Admin',
                'guru_mapel' => 'Guru Mapel',
                'guru_piket' => 'Guru Piket',
                'wali_kelas' => 'Wali Kelas',
                'waka_kurikulum' => 'Waka Kurikulum',
                'waka_sdm' => 'Waka SDM',
                'kepala_sekolah' => 'Kepala Sekolah',
                'satpam' => 'Satpam',
                'wali_murid' => 'Wali Murid',
                default => ucfirst($u->role),
            };

            $isNew = $u->created_at && $u->created_at->diffInHours(now()) < 48;
            $desc = $isNew
                ? "Akun pengguna baru {$u->name} ({$roleLabel}) berhasil didaftarkan"
                : "Data akun pengguna {$u->name} ({$roleLabel}) diperbarui";

            $activityFeed->push([
                'deskripsi'  => $desc,
                'waktu'      => $tglTime->diffForHumans(),
                'tag'        => 'User',
                'icon'       => 'fa-solid fa-user-gear',
                'icon_bg'    => '#eff6ff',
                'icon_color' => '#2563eb',
                'timestamp'  => $tglTime->timestamp,
            ]);
        }

        $recentGurus = Guru::orderByDesc('updated_at')->take(2)->get();
        foreach ($recentGurus as $g) {
            $tglTime = $g->updated_at ?? now();
            $activityFeed->push([
                'deskripsi'  => "Master data guru {$g->nama_lengkap} diperbarui",
                'waktu'      => $tglTime->diffForHumans(),
                'tag'        => 'Master Guru',
                'icon'       => 'fa-solid fa-chalkboard-user',
                'icon_bg'    => '#ecfdf5',
                'icon_color' => '#059669',
                'timestamp'  => $tglTime->timestamp,
            ]);
        }

        $recentSiswas = Siswa::with('kelas')->orderByDesc('updated_at')->take(2)->get();
        foreach ($recentSiswas as $s) {
            $tglTime = $s->updated_at ?? now();
            $kelasName = $s->kelas->nama_kelas ?? 'Kelas';
            $activityFeed->push([
                'deskripsi'  => "Data siswa {$s->nama_lengkap} ({$kelasName}) diperbarui",
                'waktu'      => $tglTime->diffForHumans(),
                'tag'        => 'Master Siswa',
                'icon'       => 'fa-solid fa-user-graduate',
                'icon_bg'    => '#f5f3ff',
                'icon_color' => '#7c3aed',
                'timestamp'  => $tglTime->timestamp,
            ]);
        }

        // Urutkan berdasarkan timestamp terbaru dan ambil 6 teratas (tanpa duplikasi deskripsi)
        $aktivitasTerbaru = $activityFeed->unique('deskripsi')->sortByDesc('timestamp')->take(6)->values()->all();

        // ── Perlu Perhatian (100% Real Dynamic Alerts) ──────────
        $perluPerhatian = [];

        // 1. Siswa dengan Catatan Alpha (Prioritas Tinggi)
        $alpaStudents = PresensiSiswa::where('status', 'Alpha')
            ->with(['siswa.kelas'])
            ->select('id_siswa', DB::raw('count(*) as total_alpha'))
            ->groupBy('id_siswa')
            ->orderByDesc('total_alpha')
            ->get();

        if ($alpaStudents->count() > 0) {
            $topAlpa   = $alpaStudents->first();
            $siswaName = $topAlpa->siswa->nama_lengkap ?? 'Siswa';
            $kelasName = $topAlpa->siswa->kelas->nama_kelas ?? '-';
            $totalSiswaAlpa = $alpaStudents->count();

            $perluPerhatian[] = [
                'type'     => 'danger',
                'title'    => "{$totalSiswaAlpa} siswa memiliki catatan Alpha (tertinggi: {$siswaName} {$topAlpa->total_alpha}x)",
                'subtitle' => "Kelas {$kelasName} — segera koordinasikan dengan wali kelas & BK",
                'icon'     => 'fa-solid fa-triangle-exclamation',
                'url'      => route('admin.absensi'),
            ];
        }

        // 2. Kelas dengan Tingkat Ketidakhadiran Tertinggi
        $absenByClass = PresensiSiswa::whereIn('status', ['Alpha', 'Sakit', 'Izin'])
            ->join('siswa', 'presensi_siswa.id_siswa', '=', 'siswa.id_siswa')
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->select(
                'kelas.nama_kelas',
                'kelas.id_kelas',
                DB::raw('count(*) as total_absen'),
                DB::raw("SUM(CASE WHEN presensi_siswa.status = 'Alpha' THEN 1 ELSE 0 END) as alpha_cnt"),
                DB::raw("SUM(CASE WHEN presensi_siswa.status = 'Sakit' THEN 1 ELSE 0 END) as sakit_cnt"),
                DB::raw("SUM(CASE WHEN presensi_siswa.status = 'Izin' THEN 1 ELSE 0 END) as izin_cnt")
            )
            ->groupBy('kelas.id_kelas', 'kelas.nama_kelas')
            ->orderByDesc('total_absen')
            ->first();

        if ($absenByClass && $absenByClass->total_absen > 0) {
            $details = [];
            if ($absenByClass->alpha_cnt > 0) $details[] = "{$absenByClass->alpha_cnt} Alpha";
            if ($absenByClass->sakit_cnt > 0) $details[] = "{$absenByClass->sakit_cnt} Sakit";
            if ($absenByClass->izin_cnt > 0) $details[] = "{$absenByClass->izin_cnt} Izin";
            $detailStr = implode(', ', $details);

            $perluPerhatian[] = [
                'type'     => 'warning',
                'title'    => "Ketidakhadiran menonjol di kelas {$absenByClass->nama_kelas}",
                'subtitle' => "Total {$absenByClass->total_absen} ketidakhadiran ({$detailStr})",
                'icon'     => 'fa-solid fa-users-viewfinder',
                'url'      => route('admin.absensi'),
            ];
        }

        // 3. Jadwal Hari Ini yang Belum Terisi Jurnalnya
        if ($targetHariIni > 0 && $jurnalTerisiCount < $targetHariIni) {
            $selisih = $targetHariIni - $jurnalTerisiCount;
            $perluPerhatian[] = [
                'type'     => 'warning',
                'title'    => "{$selisih} dari {$targetHariIni} jadwal hari {$hariIni} belum diisi jurnalnya",
                'subtitle' => "Pantau pengisian jurnal guru pengajar hari ini",
                'icon'     => 'fa-solid fa-clock-rotate-left',
                'url'      => route('admin.absensi'),
            ];
        }

        // 4. Siswa Sakit & Izin yang Memerlukan Pemantauan
        $sakitIzinCount = PresensiSiswa::whereIn('status', ['Sakit', 'Izin'])->count();
        if ($sakitIzinCount > 0 && count($perluPerhatian) < 3) {
            $perluPerhatian[] = [
                'type'     => 'info',
                'title'    => "{$sakitIzinCount} catatan siswa Sakit / Izin terdata",
                'subtitle' => "Pastikan surat keterangan izin/sakit telah diserahkan ke wali kelas",
                'icon'     => 'fa-solid fa-notes-medical',
                'url'      => route('admin.absensi'),
            ];
        }

        // 5. Fallback jika seluruh data kehadiran bersih tanpa anomali
        if (empty($perluPerhatian)) {
            $perluPerhatian[] = [
                'type'     => 'success',
                'title'    => 'Seluruh aktivitas presensi & pembelajaran berjalan normal',
                'subtitle' => 'Tidak ada peringatan atau catatan pelanggaran kehadiran',
                'icon'     => 'fa-solid fa-circle-check',
                'url'      => route('admin.absensi'),
            ];
        }

        $authUser      = Auth::user();
        $namaUser      = $authUser ? $authUser->name : 'Admin';
        $dateFormatted = Carbon::now()->translatedFormat('l, j F Y');

        return view('admin.dashboard.index', compact(
            'totalSiswa',
            'totalRombel',
            'hadirHariIni',
            'izinSakitHariIni',
            'alpaHariIni',
            'alpaKemarin',
            'pctHadir',
            'pctIzinSakit',
            'pctAlpa',
            'jurnalTerisiCount',
            'jurnalTargetCount',
            'trend7Hari',
            'perluPerhatian',
            'aktivitasTerbaru',
            'dateFormatted',
            'namaUser'
        ));
    }

    public function help()
    {
        return view('admin.help.index');
    }
}
