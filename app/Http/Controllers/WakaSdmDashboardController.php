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
        $menungguIzinCount = IzinGuru::where('status', 'Menunggu')->count();
        $menungguDispensasiCount = DispensasiSiswa::where('status', 'Menunggu')->count();

        // Ditolak Hari Ini (Izin Guru + Dispensasi)
        $ditolakHariIniCount = IzinGuru::where('status', 'Ditolak')
            ->whereDate('updated_at', $today)
            ->count() +
            DispensasiSiswa::where('status', 'Ditolak')
            ->whereDate('updated_at', $today)
            ->count();

        // Disetujui Hari Ini (Izin Guru + Dispensasi)
        $disetujuiHariIniCount = IzinGuru::where('status', 'Disetujui')
            ->whereDate('updated_at', $today)
            ->count() +
            DispensasiSiswa::whereIn('status', ['Disetujui', 'Disetujui_Waka'])
            ->whereDate('updated_at', $today)
            ->count();

        // 2. Daftar Pengajuan Menunggu Persetujuan (Tabel Utama Gabungan)
        $pendingIzin = IzinGuru::with(['guru.user'])
            ->where('status', 'Menunggu')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id'            => $item->id,
                    'type'          => 'izin_guru',
                    'type_label'    => 'Izin Guru',
                    'type_class'    => 'blue',
                    'nama'          => $item->guru->nama_lengkap ?? 'Guru Pengajar',
                    'sub_info'      => $item->jenis_izin . ($item->alasan ? ' - ' . Str::limit($item->alasan, 35) : ''),
                    'tanggal'       => Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y'),
                    'tanggal_raw'   => $item->tanggal_mulai,
                    'status'        => 'Menunggu',
                    'created_at'    => $item->created_at ?? now(),
                    'detail_url'    => route('waka-sdm.izin.status', $item->id),
                    'jenis'         => $item->jenis_izin,
                    'alasan'        => $item->alasan,
                    'rentang'       => Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y'),
                    'bukti_file'    => $item->bukti_file,
                    'nip_nisn'      => $item->guru->nip ?? '-',
                ];
            });

        $pendingDispensasi = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser'])
            ->where('status', 'Menunggu')
            ->orderByDesc('created_at')
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
                    'detail_url'    => route('waka-sdm.dispensasi.status', $item->id),
                    'jenis'         => 'Dispensasi Siswa',
                    'alasan'        => $item->alasan,
                    'rentang'       => 'Pukul ' . substr($item->jam_keluar, 0, 5) . ($item->jam_kembali ? ' - ' . substr($item->jam_kembali, 0, 5) : ' WIB'),
                    'bukti_file'    => $item->bukti_file,
                    'nip_nisn'      => $item->siswa->nisn ?? ($item->siswa->nis ?? '-'),
                ];
            });

        // Gabungkan dan urutkan pengajuan menunggu
        $pendingApprovals = $pendingIzin->concat($pendingDispensasi)->sortByDesc('created_at')->values()->take(10);

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
        $monitoringDispensasi = (object) [
            'disetujui_waka'    => $allDispToday->whereIn('status', ['Disetujui', 'Disetujui_Waka', 'Selesai'])->count(),
            'menunggu_keluar'   => $allDispToday->whereIn('status', ['Disetujui', 'Disetujui_Waka'])->where('jam_keluar', null)->count(),
            'sudah_keluar'      => $allDispToday->filter(function ($item) {
                return in_array($item->status, ['Disetujui', 'Disetujui_Waka', 'Selesai']) && !empty($item->jam_keluar);
            })->count(),
            'ditolak'           => $allDispToday->where('status', 'Ditolak')->count(),
        ];

        // Jika data monitoring hari ini masih 0, ambil akumulasi keseluruhan untuk preview representatif
        if ($monitoringDispensasi->disetujui_waka === 0 && $monitoringDispensasi->ditolak === 0) {
            $allDispTotal = DispensasiSiswa::all();
            $monitoringDispensasi = (object) [
                'disetujui_waka'    => max(1, $allDispTotal->whereIn('status', ['Disetujui', 'Disetujui_Waka', 'Selesai'])->count()),
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

            $chartIzinData[] = IzinGuru::where('tanggal_mulai', '<=', $dStr)
                ->where('tanggal_selesai', '>=', $dStr)
                ->count();

            $chartDispData[] = DispensasiSiswa::where('tanggal', $dStr)->count();
        }

        // Unread notifikasi count
        $unreadNotifCount = Notifikasi::where('user_id', $user->id)->unread()->count();

        return view('waka_sdm.dashboard.index', compact(
            'user', 'waka', 'today', 'todayFormatted',
            'menungguIzinCount', 'menungguDispensasiCount', 'ditolakHariIniCount', 'disetujuiHariIniCount',
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

        $query = IzinGuru::with(['guru.user', 'piketApprover', 'wakaApprover', 'kepsekApprover', 'disetujuiOlehUser'])
            ->when($status && $status !== 'semua', function ($q) use ($status) {
                if ($status === 'menunggu_waka' || $status === 'Menunggu') {
                    $q->where('tahap_approval', 'waka_sdm');
                } elseif ($status === 'diteruskan_kepsek') {
                    $q->where('tahap_approval', 'kepsek');
                } else {
                    $q->where('status', $status);
                }
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('guru', fn($qg) => $qg->where('nama_lengkap', 'LIKE', "%{$search}%")->orWhere('nip', 'LIKE', "%{$search}%"))
                  ->orWhere('alasan', 'LIKE', "%{$search}%")
                  ->orWhere('jenis_izin', 'LIKE', "%{$search}%");
            })
            ->when($bulan, function ($q) use ($bulan) {
                $q->where('tanggal_mulai', 'LIKE', "{$bulan}%");
            });

        $izinList = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $counts = (object) [
            'semua'           => IzinGuru::count(),
            'menunggu_waka'   => IzinGuru::where('tahap_approval', 'waka_sdm')->count(),
            'diteruskan_kepsek' => IzinGuru::where('tahap_approval', 'kepsek')->count(),
            'disetujui'       => IzinGuru::where('status', 'Disetujui')->count(),
            'ditolak'         => IzinGuru::where('status', 'Ditolak')->count(),
        ];

        return view('waka_sdm.izin.index', compact(
            'user', 'waka', 'izinList', 'search', 'status', 'bulan', 'counts'
        ));
    }

    /**
     * Update Status Persetujuan Izin Guru oleh Waka SDM (Tahap 2)
     */
    public function updateStatusIzin(Request $request, $id)
    {
        $request->validate([
            'status'  => 'required|in:Disetujui,Ditolak',
            'catatan' => 'nullable|string|max:255',
        ]);

        $izin = IzinGuru::with('guru')->findOrFail($id);
        $guruNama = $izin->guru->nama_lengkap ?? 'Guru';

        if ($request->status === 'Disetujui') {
            $izin->update([
                'waka_status'      => 'Disetujui',
                'waka_approved_by' => Auth::id(),
                'waka_at'          => now(),
                'waka_catatan'     => $request->catatan,
                'tahap_approval'   => 'kepsek',
            ]);

            // Kirim notifikasi ke Kepala Sekolah untuk persetujuan final (Tahap 3)
            $kepsekUsers = User::where('role', 'kepala_sekolah')->get();
            foreach ($kepsekUsers as $kUser) {
                Notifikasi::create([
                    'user_id'        => $kUser->id,
                    'judul'          => 'Persetujuan Izin Guru Final (Tahap 3 - Kepala Sekolah)',
                    'pesan'          => "Pengajuan izin guru {$guruNama} telah disetujui Guru Piket dan Waka SDM. Menunggu persetujuan final dari Anda sebagai Kepala Sekolah.",
                    'tipe'           => 'izin_guru',
                    'reference_id'   => $izin->id,
                    'reference_type' => IzinGuru::class,
                    'is_read'        => false,
                ]);
            }

            return back()->with('success', "Izin guru {$guruNama} berhasil disetujui Waka SDM dan diteruskan ke Kepala Sekolah (Tahap 3).");
        } else {
            // Ditolak oleh Waka SDM
            $izin->update([
                'status'            => 'Ditolak',
                'waka_status'       => 'Ditolak',
                'waka_approved_by'  => Auth::id(),
                'waka_at'           => now(),
                'waka_catatan'      => $request->catatan,
                'tahap_approval'    => 'ditolak',
                'ditolak_oleh_role' => 'waka_sdm',
                'ditolak_catatan'   => $request->catatan,
            ]);

            // Kirim notifikasi ke Guru Mapel: Ditolak dan WAJIB LANJUT KBM
            $targetUserId = $izin->guru?->user_id ?? $izin->diinput_oleh;
            if ($targetUserId) {
                Notifikasi::create([
                    'user_id'        => $targetUserId,
                    'judul'          => 'Pengajuan Izin Ditolak oleh Waka SDM',
                    'pesan'          => "Pengajuan izin {$izin->jenis_izin} Anda TIDAK DISETUJUI oleh Waka SDM." . ($request->catatan ? " Catatan: \"{$request->catatan}\"." : "") . " Anda diwajibkan untuk tetap hadir dan melanjutkan KBM.",
                    'tipe'           => 'izin_guru',
                    'reference_id'   => $izin->id,
                    'reference_type' => IzinGuru::class,
                    'is_read'        => false,
                ]);
            }

            return back()->with('warning', "Pengajuan izin guru {$guruNama} telah ditolak. Guru bersangkutan telah dinotifikasi untuk tetap melanjutkan KBM.");
        }
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
                    $q->whereIn('status', ['Disetujui', 'Disetujui_Waka']);
                } else {
                    $q->where('status', $status);
                }
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('siswa', function ($qs) use ($search) {
                    $qs->where('nama_lengkap', 'LIKE', "%{$search}%")
                       ->orWhere('nisn', 'LIKE', "%{$search}%")
                       ->orWhere('nis', 'LIKE', "%{$search}%");
                })->orWhere('alasan', 'LIKE', "%{$search}%");
            })
            ->when($tanggal, fn($q) => $q->where('tanggal', $tanggal))
            ->when($id_kelas, fn($q) => $q->whereHas('siswa', fn($qs) => $qs->where('id_kelas', $id_kelas)));

        $dispensasiList = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $counts = (object) [
            'semua'     => DispensasiSiswa::count(),
            'menunggu'  => DispensasiSiswa::where('status', 'Menunggu')->count(),
            'disetujui' => DispensasiSiswa::whereIn('status', ['Disetujui', 'Disetujui_Waka'])->count(),
            'ditolak'   => DispensasiSiswa::where('status', 'Ditolak')->count(),
        ];

        return view('waka_sdm.dispensasi.index', compact(
            'user', 'waka', 'dispensasiList', 'search', 'status', 'tanggal', 'id_kelas', 'kelasList', 'counts'
        ));
    }

    /**
     * Update Status Persetujuan Dispensasi Siswa
     */
    public function updateStatusDispensasi(Request $request, $id)
    {
        $request->validate([
            'status'  => 'required|in:Disetujui,Disetujui_Waka,Ditolak',
            'catatan' => 'nullable|string|max:255',
        ]);

        $dispensasi = DispensasiSiswa::with(['siswa', 'diinputOlehUser'])->findOrFail($id);
        $finalStatus = ($request->status === 'Disetujui_Waka' || $request->status === 'Disetujui') ? 'Disetujui' : 'Ditolak';

        $dispensasi->update([
            'status'              => $finalStatus,
            'disetujui_oleh'      => Auth::id(),
            'tanggal_persetujuan' => now(),
        ]);

        // Kirim notifikasi ke penginput dispensasi (misal Guru Piket)
        if ($dispensasi->diinput_oleh) {
            Notifikasi::create([
                'user_id'        => $dispensasi->diinput_oleh,
                'judul'          => "Dispensasi {$dispensasi->siswa->nama_lengkap} " . ($finalStatus === 'Ditolak' ? 'Ditolak' : 'Disetujui'),
                'pesan'          => "Pengajuan dispensasi siswa telah {$finalStatus} oleh Waka SDM." . ($request->catatan ? " Catatan: {$request->catatan}" : ""),
                'tipe'           => 'dispensasi_siswa',
                'reference_id'   => $dispensasi->id,
                'reference_type' => DispensasiSiswa::class,
                'is_read'        => false,
            ]);
        }

        $namaSiswa = $dispensasi->siswa->nama_lengkap ?? 'Siswa';
        return back()->with('success', "Dispensasi siswa {$namaSiswa} berhasil diubah menjadi {$finalStatus}.");
    }

    /**
     * Rekap & Laporan Persetujuan
     */
    public function laporan(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $jenis   = $request->input('jenis', 'semua'); // 'semua', 'izin_guru', 'dispensasi'
        $tglAwal = $request->input('tgl_awal', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $tglAkhir= $request->input('tgl_akhir', Carbon::today()->format('Y-m-d'));

        $izinList = collect();
        if ($jenis === 'semua' || $jenis === 'izin_guru') {
            $izinList = IzinGuru::with(['guru', 'disetujuiOlehUser'])
                ->whereBetween('tanggal_mulai', [$tglAwal, $tglAkhir])
                ->orderByDesc('tanggal_mulai')
                ->get();
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
            'izin_disetujui'    => $izinList->where('status', 'Disetujui')->count(),
            'izin_ditolak'      => $izinList->where('status', 'Ditolak')->count(),
            'total_dispensasi'  => $dispensasiList->count(),
            'disp_disetujui'    => $dispensasiList->whereIn('status', ['Disetujui', 'Disetujui_Waka'])->count(),
            'disp_ditolak'      => $dispensasiList->where('status', 'Ditolak')->count(),
        ];

        return view('waka_sdm.laporan.index', compact(
            'user', 'waka', 'jenis', 'tglAwal', 'tglAkhir', 'izinList', 'dispensasiList', 'summary'
        ));
    }

    /**
     * Export Laporan ke Format CSV
     */
    public function exportLaporan(Request $request)
    {
        $jenis   = $request->input('jenis', 'semua');
        $tglAwal = $request->input('tgl_awal', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $tglAkhir= $request->input('tgl_akhir', Carbon::today()->format('Y-m-d'));

        $filename = "Laporan_Persetujuan_Waka_SDM_{$tglAwal}_{$tglAkhir}.csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($jenis, $tglAwal, $tglAkhir) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, ['No', 'Jenis Pengajuan', 'Nama', 'Keterangan/Kelas', 'Tanggal', 'Status', 'Disetujui Oleh', 'Catatan']);

            $no = 1;

            if ($jenis === 'semua' || $jenis === 'izin_guru') {
                $izins = IzinGuru::with(['guru', 'disetujuiOlehUser'])
                    ->whereBetween('tanggal_mulai', [$tglAwal, $tglAkhir])
                    ->get();
                foreach ($izins as $iz) {
                    fputcsv($file, [
                        $no++,
                        'Izin Guru',
                        $iz->guru->nama_lengkap ?? '-',
                        $iz->jenis_izin . ' - ' . $iz->alasan,
                        $iz->tanggal_mulai . ' s/d ' . $iz->tanggal_selesai,
                        $iz->status,
                        $iz->disetujuiOlehUser->name ?? '-',
                        $iz->catatan_persetujuan ?? '-',
                    ]);
                }
            }

            if ($jenis === 'semua' || $jenis === 'dispensasi') {
                $disps = DispensasiSiswa::with(['siswa.kelas', 'disetujuiOlehUser'])
                    ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                    ->get();
                foreach ($disps as $ds) {
                    fputcsv($file, [
                        $no++,
                        'Dispensasi Siswa',
                        $ds->siswa->nama_lengkap ?? '-',
                        ($ds->siswa->kelas->nama_kelas ?? '-') . ' - ' . $ds->alasan,
                        $ds->tanggal . ' (' . substr($ds->jam_keluar, 0, 5) . '-' . substr($ds->jam_kembali, 0, 5) . ')',
                        $ds->status,
                        $ds->disetujuiOlehUser->name ?? '-',
                        '-',
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
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });

        $guruList = $query->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        return view('waka_sdm.guru.index', compact(
            'user', 'waka', 'guruList', 'search', 'bulan'
        ));
    }
}
