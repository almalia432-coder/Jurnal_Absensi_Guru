<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\JurnalMengajar;
use App\Models\PresensiSiswa;
use App\Models\DispensasiSiswa;
use App\Models\IzinGuru;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AdminLaporanController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        // Tab Laporan Aktif (default: absensi_siswa)
        $activeTab = $request->input('tab', 'absensi_siswa');

        // Periode Filter
        $startDate = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('tanggal_selesai', Carbon::today()->format('Y-m-d'));
        $selectedKelas = $request->input('id_kelas');
        $selectedGuru  = $request->input('id_guru');

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $guruList  = Guru::orderBy('nama_lengkap')->get();

        $userRole = Auth::user()->role ?? 'admin';

        // ── DATA TAB 1: LAPORAN ABSENSI SISWA ──────────────────
        $absensiSiswaData = collect();
        if ($activeTab === 'absensi_siswa') {
            $siswaQuery = Siswa::with('kelas')
                ->when($selectedKelas, fn($q) => $q->where('id_kelas', $selectedKelas))
                ->orderBy('nama_lengkap');

            $siswaList = $siswaQuery->get();

            foreach ($siswaList as $s) {
                $presensi = PresensiSiswa::where('id_siswa', $s->id_siswa)
                    ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->get();

                $hadir = $presensi->where('status', 'Hadir')->count();
                $sakit = $presensi->where('status', 'Sakit')->count();
                $izin  = $presensi->where('status', 'Izin')->count();
                $alpa  = $presensi->where('status', 'Alpha')->count();
                $dispen = $presensi->where('status', 'Dispensasi')->count();

                $totalSesi = $hadir + $sakit + $izin + $alpa + $dispen;
                $pct = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 100;

                $absensiSiswaData->push([
                    'id_siswa'     => $s->id_siswa,
                    'nis'          => $s->nis,
                    'nama_lengkap' => $s->nama_lengkap,
                    'nama_kelas'   => $s->kelas->nama_kelas ?? '-',
                    'hadir'        => $hadir,
                    'sakit'        => $sakit,
                    'izin'         => $izin,
                    'alpa'         => $alpa,
                    'dispen'       => $dispen,
                    'total_sesi'   => $totalSesi,
                    'persentase'   => $pct,
                ]);
            }
        }

        // ── DATA TAB 2: LAPORAN JURNAL MENGAJAR ───────────────
        $jurnalData = collect();
        if ($activeTab === 'jurnal_mengajar') {
            $jurnalData = JurnalMengajar::with(['guru', 'kelas', 'mapel'])
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->when($selectedKelas, fn($q) => $q->where('id_kelas', $selectedKelas))
                ->when($selectedGuru, fn($q) => $q->where('id_guru', $selectedGuru))
                ->orderBy('tanggal', 'desc')
                ->orderBy('jam_mulai', 'asc')
                ->get();
        }

        // ── DATA TAB 3: LAPORAN DISPENSASI SISWA ───────────────
        $dispensasiData = collect();
        if ($activeTab === 'dispensasi_siswa') {
            $dispensasiData = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser', 'disetujuiOlehUser'])
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->when($selectedKelas, function ($q) use ($selectedKelas) {
                    $q->whereHas('siswa', fn($sq) => $sq->where('id_kelas', $selectedKelas));
                })
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        // ── DATA TAB 4: LAPORAN KEHADIRAN & IZIN GURU ──────────
        $izinGuruData = collect();
        if ($activeTab === 'izin_guru') {
            $izinGuruData = IzinGuru::with(['guru', 'diinputOlehUser', 'disetujuiOlehUser'])
                ->whereBetween('tanggal_mulai', [$startDate, $endDate])
                ->when($selectedGuru, fn($q) => $q->where('id_guru', $selectedGuru))
                ->orderBy('tanggal_mulai', 'desc')
                ->get();
        }

        // ── DATA TAB 5: RINGKASAN SEMESTER ─────────────────────
        $ringkasanSemester = [];
        if ($activeTab === 'ringkasan_semester') {
            $totalJurnal = JurnalMengajar::whereBetween('tanggal', [$startDate, $endDate])->count();
            $totalPresensi = PresensiSiswa::whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })->count();

            $totalHadir = PresensiSiswa::whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })->where('status', 'Hadir')->count();

            $totalAlpa = PresensiSiswa::whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })->where('status', 'Alpha')->count();

            $ringkasanSemester = [
                'total_siswa'   => Siswa::where('status_aktif', true)->count(),
                'total_guru'    => Guru::where('status_aktif', true)->count(),
                'total_rombel'  => Kelas::count(),
                'total_jurnal'  => $totalJurnal,
                'total_presensi'=> $totalPresensi,
                'total_hadir'   => $totalHadir,
                'total_alpa'    => $totalAlpa,
                'pct_kehadiran' => $totalPresensi > 0 ? round(($totalHadir / $totalPresensi) * 100) : 100,
            ];
        }

        return view('admin.laporan.index', compact(
            'activeTab',
            'startDate',
            'endDate',
            'selectedKelas',
            'selectedGuru',
            'kelasList',
            'guruList',
            'userRole',
            'absensiSiswaData',
            'jurnalData',
            'dispensasiData',
            'izinGuruData',
            'ringkasanSemester'
        ));
    }

    public function exportCsv(Request $request)
    {
        $activeTab  = $request->input('tab', 'absensi_siswa');
        $startDate  = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate    = $request->input('tanggal_selesai', Carbon::today()->format('Y-m-d'));

        $filename = "Laporan_{$activeTab}_{$startDate}_sd_{$endDate}.csv";
        $csvData  = "";

        if ($activeTab === 'absensi_siswa') {
            $csvData = "NIS;Nama Siswa;Kelas;Hadir;Sakit;Izin;Alpa;Dispen;Persentase Kehadiran\n";
            $siswaList = Siswa::with('kelas')->orderBy('nama_lengkap')->get();
            foreach ($siswaList as $s) {
                $presensi = PresensiSiswa::where('id_siswa', $s->id_siswa)->get();
                $h = $presensi->where('status', 'Hadir')->count();
                $s_cnt = $presensi->where('status', 'Sakit')->count();
                $i = $presensi->where('status', 'Izin')->count();
                $a = $presensi->where('status', 'Alpha')->count();
                $d = $presensi->where('status', 'Dispensasi')->count();
                $tot = $h + $s_cnt + $i + $a + $d;
                $pct = $tot > 0 ? round(($h / $tot) * 100) : 100;

                $csvData .= sprintf(
                    "%s;\"%s\";\"%s\";%d;%d;%d;%d;%d;%d%%\n",
                    $s->nis, $s->nama_lengkap, $s->kelas->nama_kelas ?? '-', $h, $s_cnt, $i, $a, $d, $pct
                );
            }
        } elseif ($activeTab === 'jurnal_mengajar') {
            $csvData = "Tanggal;Jam;Kelas;Guru;Mapel;Materi;Hadir;Tidak Hadir;Status Guru\n";
            $jurnals = JurnalMengajar::with(['guru', 'kelas', 'mapel'])->whereBetween('tanggal', [$startDate, $endDate])->get();
            foreach ($jurnals as $j) {
                $csvData .= sprintf(
                    "%s;%s;\"%s\";\"%s\";\"%s\";\"%s\";%d;%d;%s\n",
                    $j->tanggal->format('Y-m-d'), $j->jam_ke, $j->kelas->nama_kelas ?? '', $j->guru->nama_lengkap ?? '',
                    $j->mapel->nama_mapel ?? '', str_replace('"', '""', $j->materi), $j->jumlah_siswa_hadir, $j->jumlah_siswa_tidak_hadir, $j->status_guru
                );
            }
        } else {
            $csvData = "Laporan {$activeTab} Periode {$startDate} s/d {$endDate}\nData terunduh otomatis.\n";
        }

        return Response::make($csvData, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function printView(Request $request)
    {
        Carbon::setLocale('id');

        $activeTab = $request->input('tab', 'absensi_siswa');
        $startDate = $request->input('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('tanggal_selesai', Carbon::today()->format('Y-m-d'));
        $selectedKelas = $request->input('id_kelas');
        $selectedGuru  = $request->input('id_guru');

        $kelasNama = $selectedKelas ? (Kelas::find($selectedKelas)->nama_kelas ?? 'Semua Kelas') : 'Semua Kelas';
        $guruNama  = $selectedGuru  ? (Guru::find($selectedGuru)->nama_lengkap ?? 'Semua Guru') : 'Semua Guru';

        // Fetch dataset based on active tab
        $dataList = collect();
        if ($activeTab === 'absensi_siswa') {
            $siswaList = Siswa::with('kelas')
                ->when($selectedKelas, fn($q) => $q->where('id_kelas', $selectedKelas))
                ->orderBy('nama_lengkap')->get();

            foreach ($siswaList as $s) {
                $presensi = PresensiSiswa::where('id_siswa', $s->id_siswa)->get();
                $h = $presensi->where('status', 'Hadir')->count();
                $s_cnt = $presensi->where('status', 'Sakit')->count();
                $i = $presensi->where('status', 'Izin')->count();
                $a = $presensi->where('status', 'Alpha')->count();
                $d = $presensi->where('status', 'Dispensasi')->count();
                $tot = $h + $s_cnt + $i + $a + $d;

                $dataList->push([
                    'nis' => $s->nis,
                    'nama' => $s->nama_lengkap,
                    'kelas' => $s->kelas->nama_kelas ?? '-',
                    'hadir' => $h,
                    'sakit' => $s_cnt,
                    'izin' => $i,
                    'alpa' => $a,
                    'dispen' => $d,
                    'pct' => $tot > 0 ? round(($h / $tot) * 100) : 100,
                ]);
            }
        } elseif ($activeTab === 'jurnal_mengajar') {
            $dataList = JurnalMengajar::with(['guru', 'kelas', 'mapel'])
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->when($selectedKelas, fn($q) => $q->where('id_kelas', $selectedKelas))
                ->when($selectedGuru, fn($q) => $q->where('id_guru', $selectedGuru))
                ->orderBy('tanggal', 'desc')->get();
        }

        $formattedStart = Carbon::parse($startDate)->translatedFormat('j F Y');
        $formattedEnd   = Carbon::parse($endDate)->translatedFormat('j F Y');
        $todayPrintDate = Carbon::now()->translatedFormat('j F Y');

        return view('admin.laporan.print', compact(
            'activeTab',
            'startDate',
            'endDate',
            'formattedStart',
            'formattedEnd',
            'todayPrintDate',
            'kelasNama',
            'guruNama',
            'dataList'
        ));
    }

    /**
     * Pembatalan dispensasi oleh Admin
     */
    public function batalkanDispensasi(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|min:5|max:500',
        ]);

        $disp = DispensasiSiswa::with('siswa')->findOrFail($id);
        $user = Auth::user();
        $namaSiswa = $disp->siswa->nama_lengkap ?? 'Siswa';

        DB::transaction(function () use ($disp, $user, $request, $namaSiswa) {
            $locked = DispensasiSiswa::where('id', $disp->id)->lockForUpdate()->first();
            $locked->update([
                'status'          => 'Dibatalkan',
                'alasan_batal'    => $request->alasan,
                'dibatalkan_oleh' => $user->id,
                'dibatalkan_at'   => now(),
            ]);

            LogAktivitas::catat(
                'Dispensasi Siswa',
                "Admin ({$user->name}) MEMBATALKAN dispensasi siswa {$namaSiswa}. Alasan: {$request->alasan}",
                $locked,
                $user
            );

            if ($locked->diinput_oleh) {
                try {
                    Notifikasi::create([
                        'user_id'        => $locked->diinput_oleh,
                        'judul'          => "Dispensasi {$namaSiswa} Dibatalkan",
                        'pesan'          => "Dispensasi atas nama {$namaSiswa} telah dibatalkan oleh Administrator. Alasan: {$request->alasan}",
                        'tipe'           => 'dispensasi_siswa',
                        'reference_id'   => $locked->id,
                        'reference_type' => DispensasiSiswa::class,
                        'is_read'        => false,
                    ]);
                } catch (\Exception $e) {}
            }
        });

        return back()->with('success', "Dispensasi untuk {$namaSiswa} berhasil dibatalkan.");
    }

    /**
     * Pembatalan Izin Guru oleh Administrator
     */
    public function batalkanIzinGuru(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|min:5',
        ], [
            'alasan.required' => 'Alasan pembatalan wajib diisi.',
            'alasan.min'      => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        $izin = IzinGuru::with('guru')->findOrFail($id);
        $user = Auth::user();
        $namaGuru = $izin->guru->nama_lengkap ?? 'Guru';

        DB::transaction(function () use ($izin, $user, $request, $namaGuru) {
            $locked = IzinGuru::where('id', $izin->id)->lockForUpdate()->first();
            $locked->update([
                'status'          => 'Dibatalkan',
                'alasan_batal'    => $request->alasan,
                'dibatalkan_oleh' => $user->id,
                'dibatalkan_at'   => now(),
            ]);

            LogAktivitas::catat(
                'Pembatalan Izin Guru',
                "Admin ({$user->name}) MEMBATALKAN izin guru {$namaGuru}. Alasan: {$request->alasan}",
                $locked,
                $user
            );

            $targetUserId = $locked->guru?->user_id ?? $locked->diinput_oleh;
            if ($targetUserId) {
                try {
                    Notifikasi::create([
                        'user_id'        => $targetUserId,
                        'judul'          => "Izin {$namaGuru} Dibatalkan oleh Admin",
                        'pesan'          => "Izin Anda ({$locked->jenis_izin}) telah dibatalkan oleh Administrator. Alasan: {$request->alasan}",
                        'tipe'           => 'izin_guru',
                        'reference_id'   => $locked->id,
                        'reference_type' => IzinGuru::class,
                        'is_read'        => false,
                    ]);
                } catch (\Exception $e) {}
            }
        });

        return back()->with('success', "Izin untuk guru {$namaGuru} berhasil dibatalkan.");
    }
}
