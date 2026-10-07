<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satpam;
use App\Models\DispensasiSiswa;
use App\Models\BukuTamu;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\KepalaSekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SatpamDashboardController extends Controller
{
    /**
     * Helper to resolve active Satpam instance
     */
    private function resolveSatpam()
    {
        $user = Auth::user();
        if ($user->satpam) {
            return $user->satpam;
        }

        $satpam = Satpam::where('user_id', $user->id)->first();
        if ($satpam) {
            return $satpam;
        }

        return Satpam::where('status_aktif', true)->first() ?? Satpam::first();
    }

    /**
     * Dashboard Utama Satpam / Pos Jaga
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $satpam = $this->resolveSatpam();

        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('l, j F Y');
        $nowTime = Carbon::now()->format('H:i:s');

        // 1. Data Dispensasi Hari Ini
        $allDispensasiToday = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser'])
            ->where('tanggal', $today)
            ->orderBy('jam_keluar', 'desc')
            ->get();

        $totalDispensasi = $allDispensasiToday->count();

        // Siswa sedang di luar: Status Disetujui dan jam_kembali belum tercatat
        $siswaDiLuar = $allDispensasiToday->filter(function ($d) {
            return $d->status === 'Disetujui' && empty($d->jam_kembali);
        });

        // Siswa sudah kembali
        $siswaKembali = $allDispensasiToday->filter(function ($d) {
            return $d->status === 'Selesai' || (!empty($d->jam_kembali) && $d->status !== 'Menunggu');
        });

        // Siswa terlambat kembali (overdue)
        $siswaOverdue = $siswaDiLuar->filter(function ($d) use ($nowTime) {
            if (!empty($d->jam_kembali)) return false;
            // Jika jam keluar + 2 jam atau jam kembali perkiraan telah terlewati
            return false; // Dynamic check via blade/time
        });

        // 2. Data Buku Tamu Hari Ini
        $allTamuToday = BukuTamu::where('tanggal', $today)
            ->orderBy('jam_masuk', 'desc')
            ->get();

        $tamuAktif = $allTamuToday->where('status', 'Di Dalam')->count();
        $totalTamu = $allTamuToday->count();

        return view('satpam.dashboard.index', [
            'satpam' => $satpam,
            'user' => $user,
            'today' => $today,
            'todayFormatted' => $todayFormatted,
            'siswaDiLuar' => $siswaDiLuar,
            'allTamuToday' => $allTamuToday->take(6),
            'metrics' => [
                'total_dispensasi' => $totalDispensasi,
                'siswa_di_luar' => $siswaDiLuar->count(),
                'siswa_kembali' => $siswaKembali->count(),
                'tamu_aktif' => $tamuAktif,
                'total_tamu' => $totalTamu,
            ],
        ]);
    }

    /**
     * Live Monitoring Gate (Pemantauan Real-Time Siswa di Luar)
     */
    public function monitoring(Request $request)
    {
        Carbon::setLocale('id');
        $satpam = $this->resolveSatpam();
        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('l, j F Y');
        $statusFilter = $request->input('status', 'di_luar');

        $query = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser'])
            ->where('tanggal', $today)
            ->orderBy('jam_keluar', 'desc');

        if ($statusFilter === 'di_luar') {
            $query->where('status', 'Disetujui')->whereNull('jam_kembali');
        } elseif ($statusFilter === 'selesai') {
            $query->where(function ($q) {
                $q->where('status', 'Selesai')->orWhereNotNull('jam_kembali');
            });
        } elseif ($statusFilter === 'menunggu') {
            $query->where('status', 'Menunggu');
        }

        $dispensasiList = $query->get();

        $counts = [
            'semua' => DispensasiSiswa::where('tanggal', $today)->count(),
            'di_luar' => DispensasiSiswa::where('tanggal', $today)->where('status', 'Disetujui')->whereNull('jam_kembali')->count(),
            'selesai' => DispensasiSiswa::where('tanggal', $today)->where(fn($q) => $q->where('status', 'Selesai')->orWhereNotNull('jam_kembali'))->count(),
            'menunggu' => DispensasiSiswa::where('tanggal', $today)->where('status', 'Menunggu')->count(),
        ];

        return view('satpam.monitoring.index', [
            'satpam' => $satpam,
            'dispensasiList' => $dispensasiList,
            'today' => $today,
            'todayFormatted' => $todayFormatted,
            'statusFilter' => $statusFilter,
            'counts' => $counts,
        ]);
    }

    /**
     * Verifikasi & Pencarian Slip Dispensasi Siswa
     */
    public function dispensasi(Request $request)
    {
        Carbon::setLocale('id');
        $satpam = $this->resolveSatpam();
        $today = Carbon::today()->format('Y-m-d');
        $search = $request->input('search');
        $tanggal = $request->input('tanggal', $today);

        $query = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser'])
            ->where('tanggal', $tanggal)
            ->where('status', 'Disetujui')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('siswa', function ($sq) use ($search) {
                        $sq->where('nama_lengkap', 'like', "%{$search}%")
                           ->orWhere('nisn', 'like', "%{$search}%");
                    })->orWhere('alasan', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc');

        $dispensasiList = $query->paginate(10)->withQueryString();

        return view('satpam.dispensasi.index', [
            'satpam' => $satpam,
            'dispensasiList' => $dispensasiList,
            'tanggal' => $tanggal,
            'search' => $search,
            'today' => $today,
        ]);
    }

    /**
     * Catat Waktu Keluar / Kembali Siswa di Gerbang
     *
     * Satpam TIDAK mengubah status persetujuan — hanya mencatat waktu aktual.
     *  - keluar  : Disetujui → catat jam_keluar_aktual (status tetap Disetujui)
     *  - kembali : Disetujui → Selesai + catat jam_kembali_aktual
     */
    public function updateStatusDispensasi(Request $request, $id)
    {
        $dispensasi = DispensasiSiswa::with('siswa')->findOrFail($id);
        $action = $request->input('action'); // 'keluar' atau 'kembali'
        $namaSiswa = $dispensasi->siswa->nama_lengkap ?? 'Siswa';

        if ($action === 'keluar') {
            $updated = DB::transaction(function () use ($dispensasi) {
                $locked = DispensasiSiswa::where('id', $dispensasi->id)
                    ->where('status', 'Disetujui')
                    ->lockForUpdate()
                    ->first();

                if (!$locked) {
                    return false;
                }

                $locked->update([
                    'jam_keluar_aktual' => Carbon::now()->format('H:i:s'),
                ]);

                return true;
            });

            if (!$updated) {
                return back()->with('error', 'Dispensasi belum disetujui atau status sudah berubah.');
            }

            return back()->with('success', "Siswa {$namaSiswa} telah dikonfirmasi KELUAR gerbang pada " . Carbon::now()->format('H:i') . " WIB.");

        } elseif ($action === 'kembali') {
            $updated = DB::transaction(function () use ($dispensasi) {
                $locked = DispensasiSiswa::where('id', $dispensasi->id)
                    ->where('status', 'Disetujui')
                    ->lockForUpdate()
                    ->first();

                if (!$locked) {
                    return false;
                }

                $locked->update([
                    'status'             => 'Selesai',
                    'jam_kembali_aktual' => Carbon::now()->format('H:i:s'),
                ]);

                return true;
            });

            if (!$updated) {
                return back()->with('error', 'Dispensasi belum disetujui atau status sudah berubah.');
            }

            return back()->with('success', "Siswa {$namaSiswa} telah dikonfirmasi KEMBALI masuk gerbang pada " . Carbon::now()->format('H:i') . " WIB.");
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    /**
     * Buku Tamu Digital
     */
    public function bukuTamu(Request $request)
    {
        Carbon::setLocale('id');
        $satpam = $this->resolveSatpam();
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $search = $request->input('search');
        $status = $request->input('status');

        $query = BukuTamu::where('tanggal', $tanggal)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama_tamu', 'like', "%{$search}%")
                        ->orWhere('instansi', 'like', "%{$search}%")
                        ->orWhere('keperluan', 'like', "%{$search}%")
                        ->orWhere('no_kendaraan', 'like', "%{$search}%");
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('jam_masuk', 'desc');

        $tamuList = $query->paginate(10)->withQueryString();

        $tamuAktifCount = BukuTamu::where('tanggal', $tanggal)->where('status', 'Di Dalam')->count();
        $totalTamuHariIni = BukuTamu::where('tanggal', $tanggal)->count();

        return view('satpam.buku_tamu.index', [
            'satpam' => $satpam,
            'tamuList' => $tamuList,
            'tanggal' => $tanggal,
            'search' => $search,
            'status' => $status,
            'tamuAktifCount' => $tamuAktifCount,
            'totalTamuHariIni' => $totalTamuHariIni,
        ]);
    }

    /**
     * Simpan Kunjungan Tamu Baru
     */
    public function storeBukuTamu(Request $request)
    {
        $validated = $request->validate([
            'nama_tamu'      => 'required|string|max:255',
            'instansi'       => 'nullable|string|max:255',
            'no_hp'          => 'nullable|string|max:25',
            'keperluan'      => 'required|string',
            'bertemu_dengan' => 'required|string|max:255',
            'no_kendaraan'   => 'nullable|string|max:20',
            'jam_masuk'      => 'required',
            'catatan_satpam' => 'nullable|string',
        ]);

        BukuTamu::create([
            'tanggal'        => Carbon::today()->format('Y-m-d'),
            'nama_tamu'      => $validated['nama_tamu'],
            'instansi'       => $validated['instansi'] ?? null,
            'no_hp'          => $validated['no_hp'] ?? null,
            'keperluan'      => $validated['keperluan'],
            'bertemu_dengan' => $validated['bertemu_dengan'],
            'no_kendaraan'   => $validated['no_kendaraan'] ?? null,
            'jam_masuk'      => $validated['jam_masuk'],
            'status'         => 'Di Dalam',
            'catatan_satpam' => $validated['catatan_satpam'] ?? null,
            'satpam_id'      => Auth::id(),
        ]);

        return redirect()->route('satpam.buku-tamu')
            ->with('success', "Tamu {$validated['nama_tamu']} berhasil dicatat masuk pos gerbang.");
    }

    /**
     * Checkout Tamu Keluar
     */
    public function checkoutBukuTamu($id)
    {
        $tamu = BukuTamu::findOrFail($id);
        $tamu->update([
            'jam_keluar' => Carbon::now()->format('H:i:s'),
            'status' => 'Selesai',
        ]);

        return redirect()->route('satpam.buku-tamu')
            ->with('success', "Tamu {$tamu->nama_tamu} telah dicatat meninggalkan lingkungan sekolah pada " . Carbon::now()->format('H:i') . " WIB.");
    }

    /**
     * Riwayat Lalu Lintas Pos Jaga
     */
    public function riwayat(Request $request)
    {
        Carbon::setLocale('id');
        $satpam = $this->resolveSatpam();
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $tab = $request->input('tab', 'siswa'); // 'siswa' atau 'tamu'

        $dispensasiList = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser'])
            ->where('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();

        $tamuList = BukuTamu::where('tanggal', $tanggal)
            ->orderBy('jam_masuk', 'desc')
            ->get();

        return view('satpam.riwayat.index', [
            'satpam' => $satpam,
            'tanggal' => $tanggal,
            'tab' => $tab,
            'dispensasiList' => $dispensasiList,
            'tamuList' => $tamuList,
            'todayFormatted' => Carbon::parse($tanggal)->translatedFormat('l, j F Y'),
        ]);
    }

    /**
     * Cetak Laporan Harian Pos Satpam
     */
    public function cetakLaporan(Request $request)
    {
        Carbon::setLocale('id');
        $satpam = $this->resolveSatpam();
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($tanggal);
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');

        $dispensasiList = DispensasiSiswa::with(['siswa.kelas'])
            ->where('tanggal', $tanggal)
            ->orderBy('jam_keluar')
            ->get();

        $tamuList = BukuTamu::where('tanggal', $tanggal)
            ->orderBy('jam_masuk')
            ->get();

        $userKepsek = User::where('role', 'kepala_sekolah')->first();
        $kepalaSekolah = KepalaSekolah::where('status_aktif', true)->first() 
            ?? ($userKepsek ? $userKepsek->kepalaSekolah : null)
            ?? KepalaSekolah::first();

        $namaKepalaSekolah = $kepalaSekolah->nama_lengkap 
            ?? ($userKepsek ? trim(preg_replace('/\s*\([^)]*\)$/', '', $userKepsek->name)) : 'Kepala Sekolah');
        $nipKepalaSekolah = $kepalaSekolah->nip ?? '-';

        return view('satpam.riwayat.cetak', [
            'satpam' => $satpam,
            'tanggal' => $tanggal,
            'todayFormatted' => $todayFormatted,
            'dispensasiList' => $dispensasiList,
            'tamuList' => $tamuList,
            'namaKepalaSekolah' => $namaKepalaSekolah,
            'nipKepalaSekolah' => $nipKepalaSekolah,
        ]);
    }

    /**
     * Panduan & SOP Satpam Gate
     */
    public function help()
    {
        return view('satpam.help.index');
    }
}
