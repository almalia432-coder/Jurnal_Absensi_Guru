<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Waka;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\IzinGuru;
use App\Models\DispensasiSiswa;
use App\Models\Notifikasi;
use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WakaSdmDashboardController extends Controller
{
    /**
     * Dashboard Utama Waka SDM / Wakil Kepala Sekolah
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('d F Y');

        // 1. KPI Metrik Utama (4 Kartu Ringkasan)
        $tercatatIzinCount = IzinGuru::where('status', 'Tercatat')->count();
        $menungguDispensasiCount = DispensasiSiswa::where('status', 'Menunggu')->count();

        // Ditolak Hari Ini (Izin Guru + Dispensasi)
        $ditolakHariIniCount = IzinGuru::where('status', 'Ditolak')
            ->whereDate('updated_at', $today)
            ->count() +
            DispensasiSiswa::where('status', 'Ditolak')
            ->whereDate('updated_at', $today)
            ->count();

        // Disetujui / Berlaku Hari Ini (Izin Guru + Dispensasi)
        $disetujuiHariIniCount = IzinGuru::berlaku()
            ->whereDate('updated_at', $today)
            ->count() +
            DispensasiSiswa::final()
            ->whereDate('updated_at', $today)
            ->count();

        // 2. Daftar Pengajuan Menunggu Persetujuan Dispensasi Siswa (Monitoring)
        $pendingApprovals = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser'])
            ->where('status', 'Menunggu')
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id'            => $item->id,
                    'type'          => 'dispensasi',
                    'type_label'    => 'Dispensasi',
                    'type_class'    => 'green',
                    'nama'          => $item->siswa->nama_lengkap ?? 'Siswa',
                    'sub_info'      => ($item->siswa->kelas->nama_kelas ?? 'Kelas') . ' • ' . Str::limit($item->alasan, 30),
                    'tanggal'       => Carbon::parse($item->tanggal)->translatedFormat('d M Y'),
                    'tanggal_raw'   => $item->tanggal,
                    'status'        => 'Menunggu',
                    'created_at'    => $item->created_at ?? now(),
                    'detail_url'    => route('waka-sdm.dispensasi'),
                    'jenis'         => 'Dispensasi Siswa',
                    'alasan'        => $item->alasan,
                    'rentang'       => 'Pukul ' . substr($item->jam_keluar, 0, 5) . ($item->jam_kembali ? ' - ' . substr($item->jam_kembali, 0, 5) : ' WIB'),
                    'bukti_file'    => $item->bukti_file,
                    'nip_nisn'      => $item->siswa->nisn ?? ($item->siswa->nis ?? '-'),
                ];
            });

        // 3. Kartu Bersebelahan: Riwayat Persetujuan Izin Guru Terbaru
        $recentIzinGuru = IzinGuru::with(['guru'])
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->map(function ($iz) {
                $nama = $iz->guru->nama_lengkap ?? 'Guru Pengajar';
                return (object) [
                    'id'        => $iz->id,
                    'nama'      => $nama,
                    'initials'  => $this->getInitials($nama),
                    'sub_info'  => $iz->jenis_izin . ($iz->alasan ? ' • ' . Str::limit($iz->alasan, 25) : ''),
                    'tanggal'   => Carbon::parse($iz->tanggal_mulai)->translatedFormat('d M Y'),
                    'status'    => $iz->status,
                    'bukti_file'=> $iz->bukti_file,
                    'alasan'    => $iz->alasan,
                ];
            });

        // 4. Kartu Bersebelahan: Riwayat Persetujuan Dispensasi Siswa Terbaru
        $recentDispensasi = DispensasiSiswa::with(['siswa.kelas'])
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->map(function ($ds) {
                $nama = $ds->siswa->nama_lengkap ?? 'Siswa';
                $kelas = $ds->siswa->kelas->nama_kelas ?? 'Kelas';
                return (object) [
                    'id'        => $ds->id,
                    'nama'      => $nama,
                    'initials'  => $this->getInitials($nama),
                    'sub_info'  => $kelas . ($ds->alasan ? ' • ' . Str::limit($ds->alasan, 22) : ''),
                    'tanggal'   => Carbon::parse($ds->tanggal)->translatedFormat('d M Y'),
                    'status'    => $ds->status,
                    'bukti_file'=> $ds->bukti_file,
                    'alasan'    => $ds->alasan,
                ];
            });

        // 5. Monitoring Dispensasi Hari Ini (4 Kotak Status 2x2)
        $allDispToday = DispensasiSiswa::where('tanggal', $today)->get();
        $finalStatuses = ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai'];
        $monitoringDispensasi = (object) [
            'disetujui_waka'    => $allDispToday->filter(fn($d) => in_array($d->status, $finalStatuses))->count(),
            'menunggu_keluar'   => $allDispToday->filter(fn($d) => in_array($d->status, $finalStatuses))->where('jam_keluar', null)->count(),
            'sudah_keluar'      => $allDispToday->filter(function ($item) use ($finalStatuses) {
                return in_array($item->status, $finalStatuses) && !empty($item->jam_keluar);
            })->count(),
            'ditolak'           => $allDispToday->where('status', 'Ditolak')->count(),
        ];

        // Jika data monitoring hari ini masih 0, ambil akumulasi keseluruhan untuk preview representatif
        if ($monitoringDispensasi->disetujui_waka === 0 && $monitoringDispensasi->ditolak === 0) {
            $allDispTotal = DispensasiSiswa::all();
            $monitoringDispensasi = (object) [
                'disetujui_waka'    => max(1, $allDispTotal->filter(fn($d) => in_array($d->status, $finalStatuses))->count()),
                'menunggu_keluar'   => $allDispTotal->where('status', 'Menunggu')->count(),
                'sudah_keluar'      => $allDispTotal->where('status', 'Selesai')->count(),
                'ditolak'           => $allDispTotal->where('status', 'Ditolak')->count(),
            ];
        }

        // 6. Data Grafik Statistik Persetujuan 7 Hari Terakhir
        $chartLabels = [];
        $chartIzinData = [];
        $chartDispData = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::parse($today)->subDays($i);
            $dStr = $day->format('Y-m-d');
            $chartLabels[] = $day->translatedFormat('d M');

            $chartIzinData[] = IzinGuru::berlaku()
                ->where('tanggal_mulai', '<=', $dStr)
                ->where('tanggal_selesai', '>=', $dStr)
                ->count();

            $chartDispData[] = DispensasiSiswa::where('tanggal', $dStr)->count();
        }

        // Unread notifikasi count
        $unreadNotifCount = Notifikasi::where('user_id', $user->id)->unread()->count();
        $menungguIzinCount = 0; // Legacy view fallback

        return view('waka_sdm.dashboard.index', compact(
            'user', 'waka', 'today', 'todayFormatted',
            'tercatatIzinCount', 'menungguIzinCount', 'menungguDispensasiCount', 'ditolakHariIniCount', 'disetujuiHariIniCount',
            'pendingApprovals', 'recentIzinGuru', 'recentDispensasi',
            'monitoringDispensasi', 'chartLabels', 'chartIzinData', 'chartDispData',
            'unreadNotifCount'
        ));
    }

    /**
     * Helper membuat inisial nama 2 karakter
     */
    protected function getInitials($name): string
    {
        $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $name)));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2) ?: 'WS');
    }

    /**
     * Halaman Persetujuan Izin Guru
     */
    public function izin(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $search = $request->input('search');
        $status = $request->input('status');
        $bulan  = $request->input('bulan');

        $query = IzinGuru::with(['guru.user', 'diinputOlehUser', 'dibatalkanOlehUser'])
            ->when($status && $status !== 'semua', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('guru', fn($qg) => $qg->where('nama_lengkap', 'LIKE', "%{$search}%")->orWhere('nip', 'LIKE', "%{$search}%"))
                        ->orWhere('alasan', 'LIKE', "%{$search}%")
                        ->orWhere('jenis_izin', 'LIKE', "%{$search}%");
                });
            })
            ->when($bulan, function ($q) use ($bulan) {
                $q->where('tanggal_mulai', 'LIKE', "{$bulan}%");
            });

        $izinList = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $counts = (object) [
            'semua'      => IzinGuru::count(),
            'tercatat'   => IzinGuru::where('status', 'Tercatat')->count(),
            'disetujui'  => IzinGuru::where('status', 'Disetujui')->count(),
            'dibatalkan' => IzinGuru::where('status', 'Dibatalkan')->count(),
            'ditolak'    => IzinGuru::where('status', 'Ditolak')->count(),
        ];

        return view('waka_sdm.izin.index', compact(
            'user', 'waka', 'izinList', 'search', 'status', 'bulan', 'counts'
        ));
    }

    /**
     * Halaman Persetujuan Dispensasi Siswa
     */
    public function dispensasi(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $search   = $request->input('search');
        $status   = $request->input('status');
        $tanggal  = $request->input('tanggal');
        $id_kelas = $request->input('id_kelas');

        $query = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser', 'disetujuiOlehUser'])
            ->when($status && $status !== 'semua', function ($q) use ($status) {
                if ($status === 'Disetujui') {
                    $q->final();
                } else {
                    $q->where('status', $status);
                }
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('siswa', function ($qs) use ($search) {
                        $qs->where('nama_lengkap', 'LIKE', "%{$search}%")
                           ->orWhere('nisn', 'LIKE', "%{$search}%")
                           ->orWhere('nis', 'LIKE', "%{$search}%");
                    })->orWhere('alasan', 'LIKE', "%{$search}%");
                });
            })
            ->when($tanggal, fn($q) => $q->where('tanggal', $tanggal))
            ->when($id_kelas, fn($q) => $q->whereHas('siswa', fn($qs) => $qs->where('id_kelas', $id_kelas)));

        $dispensasiList = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $counts = (object) [
            'semua'     => DispensasiSiswa::count(),
            'menunggu'  => DispensasiSiswa::where('status', 'Menunggu')->count(),
            'disetujui_piket' => DispensasiSiswa::where('status', 'Disetujui_Piket')->count(),
            'disetujui' => DispensasiSiswa::final()->count(),
            'ditolak'   => DispensasiSiswa::where('status', 'Ditolak')->count(),
        ];

        return view('waka_sdm.dispensasi.index', compact(
            'user', 'waka', 'dispensasiList', 'search', 'status', 'tanggal', 'id_kelas', 'kelasList', 'counts'
        ));
    }

    /**
     * Rekapitulasi Perizinan Guru
     */
    public function laporan(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $idGuru  = $request->input('id_guru');
        $jenisIzin = $request->input('jenis_izin');
        $bulan   = $request->input('bulan');
        $jenis   = $request->input('jenis', 'semua'); // 'semua', 'izin_guru', 'dispensasi'
        $tglAwal = $request->input('tgl_awal', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $tglAkhir= $request->input('tgl_akhir', Carbon::today()->format('Y-m-d'));

        $guruList = Guru::where('status_aktif', true)->orderBy('nama_lengkap')->get();

        $izinList = collect();
        if ($jenis === 'semua' || $jenis === 'izin_guru') {
            $izinQuery = IzinGuru::with(['guru', 'diinputOlehUser', 'dibatalkanOlehUser'])
                ->when($idGuru, fn($q) => $q->where('id_guru', $idGuru))
                ->when($jenisIzin && $jenisIzin !== 'semua', fn($q) => $q->where('jenis_izin', $jenisIzin))
                ->when($bulan, fn($q) => $q->where('tanggal_mulai', 'LIKE', "{$bulan}%"))
                ->when(!$bulan, fn($q) => $q->whereBetween('tanggal_mulai', [$tglAwal, $tglAkhir]))
                ->orderByDesc('tanggal_mulai');

            $izinList = $izinQuery->get();
        }

        $dispensasiList = collect();
        if ($jenis === 'semua' || $jenis === 'dispensasi') {
            $dispensasiList = DispensasiSiswa::with(['siswa.kelas', 'disetujuiOlehUser'])
                ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                ->orderByDesc('tanggal')
                ->get();
        }

        $summary = (object) [
            'total_izin'        => $izinList->count(),
            'izin_tercatat'     => $izinList->where('status', 'Tercatat')->count(),
            'izin_disetujui'    => $izinList->where('status', 'Disetujui')->count(),
            'izin_dibatalkan'   => $izinList->where('status', 'Dibatalkan')->count(),
            'izin_ditolak'      => $izinList->where('status', 'Ditolak')->count(),
            'total_dispensasi'  => $dispensasiList->count(),
            'disp_disetujui'    => $dispensasiList->filter(fn($d) => in_array($d->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai']))->count(),
            'disp_ditolak'      => $dispensasiList->where('status', 'Ditolak')->count(),
        ];

        return view('waka_sdm.laporan.index', compact(
            'user', 'waka', 'jenis', 'tglAwal', 'tglAkhir', 'idGuru', 'jenisIzin', 'bulan', 'guruList', 'izinList', 'dispensasiList', 'summary'
        ));
    }

    /**
     * Export Laporan Rekapitulasi ke Format CSV
     */
    public function exportLaporan(Request $request)
    {
        $idGuru  = $request->input('id_guru');
        $jenisIzin = $request->input('jenis_izin');
        $bulan   = $request->input('bulan');
        $jenis   = $request->input('jenis', 'semua');
        $tglAwal = $request->input('tgl_awal', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $tglAkhir= $request->input('tgl_akhir', Carbon::today()->format('Y-m-d'));

        $filename = "Rekap_Perizinan_Guru_{$tglAwal}_{$tglAkhir}.csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($jenis, $tglAwal, $tglAkhir, $idGuru, $jenisIzin, $bulan) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, ['No', 'Jenis Pengajuan', 'Nama', 'NIP / Kelas', 'Kategori', 'Keterangan / Alasan', 'Rentang Tanggal', 'Status', 'Catatan / Alasan Batal']);

            $no = 1;

            if ($jenis === 'semua' || $jenis === 'izin_guru') {
                $izins = IzinGuru::with(['guru', 'dibatalkanOlehUser'])
                    ->when($idGuru, fn($q) => $q->where('id_guru', $idGuru))
                    ->when($jenisIzin && $jenisIzin !== 'semua', fn($q) => $q->where('jenis_izin', $jenisIzin))
                    ->when($bulan, fn($q) => $q->where('tanggal_mulai', 'LIKE', "{$bulan}%"))
                    ->when(!$bulan, fn($q) => $q->whereBetween('tanggal_mulai', [$tglAwal, $tglAkhir]))
                    ->orderByDesc('tanggal_mulai')
                    ->get();

                foreach ($izins as $iz) {
                    fputcsv($file, [
                        $no++,
                        'Izin Guru',
                        $iz->guru->nama_lengkap ?? '-',
                        $iz->guru->nip ?? '-',
                        $iz->jenis_izin,
                        $iz->alasan,
                        $iz->tanggal_mulai . ' s/d ' . $iz->tanggal_selesai,
                        $iz->status,
                        $iz->alasan_batal ?? ($iz->catatan_persetujuan ?? '-'),
                    ]);
                }
            }

            if ($jenis === 'semua' || $jenis === 'dispensasi') {
                $disps = DispensasiSiswa::with(['siswa.kelas', 'disetujuiOlehUser'])
                    ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                    ->orderByDesc('tanggal')
                    ->get();
                foreach ($disps as $ds) {
                    fputcsv($file, [
                        $no++,
                        'Dispensasi Siswa',
                        $ds->siswa->nama_lengkap ?? '-',
                        $ds->siswa->kelas->nama_kelas ?? '-',
                        'Dispensasi',
                        $ds->alasan,
                        $ds->tanggal . ' (' . substr($ds->jam_keluar, 0, 5) . '-' . substr($ds->jam_kembali, 0, 5) . ')',
                        $ds->status,
                        $ds->catatan_waka ?? ($ds->piket_catatan ?? '-'),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Halaman Bantuan & FAQ SOP Waka SDM
     */
    public function help(Request $request)
    {
        $user = Auth::user();
        $waka = $user->waka ?? null;
        return view('waka_sdm.help.index', compact('user', 'waka'));
    }

    /**
     * Rekap Kehadiran & Kepatuhan Guru
     */
    public function guru(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $search = $request->input('search');
        $bulan  = $request->input('bulan', Carbon::today()->format('Y-m'));

        $query = Guru::query()->with('user')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_lengkap', 'LIKE', "%{$search}%")
                        ->orWhere('nip', 'LIKE', "%{$search}%");
                });
            });

        $guruList = $query->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        return view('waka_sdm.guru.index', compact(
            'user', 'waka', 'guruList', 'search', 'bulan'
        ));
    }
}
