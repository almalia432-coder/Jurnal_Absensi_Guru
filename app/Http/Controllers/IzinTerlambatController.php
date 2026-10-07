<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreIzinTerlambatRequest;
use App\Http\Requests\UpdateIzinTerlambatRequest;
use App\Models\IzinTerlambat;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\JadwalPiketKbm;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IzinTerlambatController extends Controller
{
    /**
     * Helper validasi hak akses konfirmasi Waka Piket
     */
    private function checkWakaPiketAuthority($targetDate): bool
    {
        $user = Auth::user();
        if ($user->email === 'waka.piket@smkn1boyolangu.sch.id' || $user->role === 'admin') {
            return true;
        }

        $rosterTarget = JadwalPiketKbm::getRosterForDate(Carbon::parse($targetDate));
        $wakaDuty = $rosterTarget['waka'] ?? null;

        if ($wakaDuty) {
            $userNip = $user->guru->nip ?? ($user->waka->nip ?? null);
            if ($userNip && $userNip === $wakaDuty['nip']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Halaman Utama Guru Piket: Input + Riwayat Izin Terlambat Hari Ini
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $status = $request->input('status');
        $kelasId = $request->input('id_kelas');
        $search = $request->input('search');

        $query = IzinTerlambat::with(['siswa.kelas', 'diinputOlehUser', 'dikonfirmasiOlehUser'])
            ->whereDate('tanggal', $tanggal);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($kelasId) {
            $query->where('id_kelas', $kelasId);
        }

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        $terlambatList = $query->orderByDesc('id')->paginate(15)->withQueryString();

        // Metrics Hari Terpilih
        $baseMetrics = IzinTerlambat::whereDate('tanggal', $tanggal);
        $metrics = [
            'total'     => (clone $baseMetrics)->count(),
            'menunggu'  => (clone $baseMetrics)->where('status', 'Menunggu')->count(),
            'disetujui' => (clone $baseMetrics)->where('status', 'Disetujui')->count(),
            'ditolak'   => (clone $baseMetrics)->whereIn('status', ['Ditolak', 'Dibatalkan'])->count(),
        ];

        // Master Data untuk Form Input
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $siswaList = Siswa::with('kelas')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get(['id_siswa', 'id_kelas', 'nama_lengkap', 'nisn']);

        $todayFormatted = Carbon::parse($tanggal)->translatedFormat('l, d F Y');

        return view('guru_piket.terlambat.index', compact(
            'user', 'tanggal', 'status', 'kelasId', 'search',
            'terlambatList', 'metrics', 'kelasList', 'siswaList', 'todayFormatted'
        ));
    }

    /**
     * Halaman Antrean Persetujuan Waka Piket
     */
    public function wakaIndex(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $tab = $request->input('tab', 'menunggu'); // 'menunggu' atau 'riwayat'
        $search = $request->input('search');

        $query = IzinTerlambat::with(['siswa.kelas', 'diinputOlehUser', 'dikonfirmasiOlehUser'])
            ->whereDate('tanggal', $tanggal);

        if ($tab === 'menunggu') {
            $query->where('status', 'Menunggu');
        } else {
            $query->whereIn('status', ['Disetujui', 'Ditolak', 'Dibatalkan']);
        }

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        $terlambatList = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $baseMetrics = IzinTerlambat::whereDate('tanggal', $tanggal);
        $metrics = [
            'total'     => (clone $baseMetrics)->count(),
            'menunggu'  => (clone $baseMetrics)->where('status', 'Menunggu')->count(),
            'disetujui' => (clone $baseMetrics)->where('status', 'Disetujui')->count(),
            'ditolak'   => (clone $baseMetrics)->whereIn('status', ['Ditolak', 'Dibatalkan'])->count(),
        ];

        $todayFormatted = Carbon::parse($tanggal)->translatedFormat('l, d F Y');

        // Cek wewenang approval hari ini
        $isAuthorized = $this->checkWakaPiketAuthority($tanggal);

        return view('waka_piket.terlambat.index', compact(
            'user', 'tanggal', 'tab', 'search', 'terlambatList', 'metrics', 'todayFormatted', 'isAuthorized'
        ));
    }

    /**
     * Simpan Pengajuan Izin Masuk Siswa Terlambat (Guru Piket)
     */
    public function store(StoreIzinTerlambatRequest $request)
    {
        $siswa = Siswa::with('kelas')->findOrFail($request->id_siswa);

        $izin = IzinTerlambat::create([
            'id_siswa'            => $siswa->id_siswa,
            'id_kelas'            => $siswa->id_kelas,
            'tanggal'             => $request->tanggal,
            'jam_masuk'           => $request->jam_masuk,
            'jam_ke_mulai'        => $request->jam_ke_mulai,
            'alasan'              => $request->alasan,
            'status'              => 'Menunggu',
            'nomor_surat'         => null,
            'diinput_oleh'        => Auth::id(),
            'dikonfirmasi_oleh'   => null,
            'dikonfirmasi_at'     => null,
            'catatan_konfirmasi'  => null,
        ]);

        // Catat Log Aktivitas
        LogAktivitas::catat(
            'Izin Siswa Terlambat',
            "Guru Piket mencatat siswa terlambat: {$siswa->nama_lengkap} ({$siswa->kelas->nama_kelas}) tiba pukul {$request->jam_masuk}, izin masuk jam ke-{$request->jam_ke_mulai}. Alasan: {$request->alasan}",
            $izin,
            Auth::user()
        );

        // Notifikasi ke meja Waka Piket
        try {
            $wakaPiketUser = User::where('email', 'waka.piket@smkn1boyolangu.sch.id')->first();
            if ($wakaPiketUser) {
                Notifikasi::create([
                    'user_id' => $wakaPiketUser->id,
                    'judul'   => 'Siswa Terlambat Menunggu Persetujuan',
                    'pesan'   => "{$siswa->nama_lengkap} ({$siswa->kelas->nama_kelas}) terlambat dan mengajukan izin masuk jam ke-{$request->jam_ke_mulai}.",
                    'tipe'    => 'izin_terlambat',
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {}

        return back()->with('success', "Izin masuk terlambat untuk {$siswa->nama_lengkap} berhasil dicatat dan sedang menunggu konfirmasi Waka Piket.");
    }

    /**
     * Edit Izin Terlambat (Hanya saat status masih Menunggu)
     */
    public function update(UpdateIzinTerlambatRequest $request, $id)
    {
        $izin = IzinTerlambat::with('siswa')->findOrFail($id);

        if ($izin->status !== 'Menunggu' && Auth::user()->role !== 'admin') {
            return back()->with('error', 'Izin terlambat ini sudah dikonfirmasi dan tidak dapat diubah.');
        }

        $siswa = Siswa::findOrFail($request->id_siswa);

        $izin->update([
            'id_siswa'     => $siswa->id_siswa,
            'id_kelas'     => $siswa->id_kelas,
            'tanggal'      => $request->tanggal,
            'jam_masuk'    => $request->jam_masuk,
            'jam_ke_mulai' => $request->jam_ke_mulai,
            'alasan'       => $request->alasan,
        ]);

        LogAktivitas::catat(
            'Izin Siswa Terlambat',
            "Memperbarui data izin terlambat siswa: {$siswa->nama_lengkap}",
            $izin,
            Auth::user()
        );

        return back()->with('success', "Data keterlambatan {$siswa->nama_lengkap} berhasil diperbarui.");
    }

    /**
     * Hapus / Batalkan Pengajuan (Hanya saat status masih Menunggu)
     */
    public function destroy($id)
    {
        $izin = IzinTerlambat::with('siswa')->findOrFail($id);
        $user = Auth::user();

        if ($izin->status !== 'Menunggu' && $user->role !== 'admin') {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat dihapus.');
        }

        $namaSiswa = $izin->siswa->nama_lengkap ?? 'Siswa';
        $izin->delete();

        LogAktivitas::catat(
            'Izin Siswa Terlambat',
            "Menghapus pengajuan izin terlambat siswa {$namaSiswa}",
            null,
            $user
        );

        return back()->with('success', "Pengajuan izin terlambat {$namaSiswa} berhasil dibatalkan.");
    }

    /**
     * Konfirmasi Persetujuan / Penolakan / Pembatalan Izin Terlambat
     */
    public function konfirmasi(Request $request, $id)
    {
        $request->validate([
            'action'  => 'required|in:setujui,tolak,batalkan',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $action = $request->action;

        // Admin Special Action: Pembatalan Izin yang sudah Disetujui
        if ($action === 'batalkan') {
            if ($user->role !== 'admin') {
                return back()->with('error', 'Akses Ditolak! Hanya Administrator yang berhak membatalkan surat izin yang telah disetujui.');
            }

            if (empty(trim($request->catatan)) || strlen(trim($request->catatan)) < 5) {
                return back()->with('error', 'Pembatalan izin yang telah disetujui wajib disertai alasan tertulis minimal 5 karakter.');
            }

            $izin = IzinTerlambat::with(['siswa.kelas'])->findOrFail($id);

            DB::transaction(function () use ($izin, $user, $request) {
                $izin->update([
                    'status'             => 'Dibatalkan',
                    'catatan_konfirmasi' => "Dibatalkan oleh Admin ({$user->name}): " . $request->catatan,
                ]);

                LogAktivitas::catat(
                    'Izin Siswa Terlambat',
                    "Admin ({$user->name}) MEMBATALKAN izin terlambat nomor {$izin->nomor_surat} untuk {$izin->siswa->nama_lengkap}. Alasan: {$request->catatan}",
                    $izin,
                    $user
                );
            });

            return back()->with('success', "Surat izin terlambat nomor {$izin->nomor_surat} berhasil DIBATALKAN.");
        }

        // Action Setujui / Tolak oleh Waka Piket
        if ($user->role !== 'admin' && !$user->hasPortal('piket_waka')) {
            abort(403, 'Akses Ditolak! Hanya Waka Piket KBM yang berhak mengonfirmasi izin terlambat.');
        }

        $izin = IzinTerlambat::with(['siswa.kelas'])->findOrFail($id);

        if (!$this->checkWakaPiketAuthority($izin->tanggal)) {
            return back()->with('error', 'Akses Ditolak! Anda tidak memiliki wewenang persetujuan piket pada tanggal tersebut.');
        }

        if ($izin->status !== 'Menunggu' && $user->role !== 'admin') {
            return back()->with('error', 'Izin terlambat ini sudah diproses sebelumnya dan tidak dapat diubah lagi.');
        }

        $namaSiswa = $izin->siswa->nama_lengkap ?? 'Siswa';
        $namaWaka = $user->name;

        DB::transaction(function () use ($izin, $action, $user, $request, $namaSiswa, $namaWaka) {
            if ($action === 'setujui') {
                $nomorSurat = IzinTerlambat::generateNomorSurat(Carbon::parse($izin->tanggal));

                $izin->update([
                    'status'             => 'Disetujui',
                    'nomor_surat'        => $nomorSurat,
                    'dikonfirmasi_oleh'  => $user->id,
                    'dikonfirmasi_at'    => now(),
                    'catatan_konfirmasi' => $request->catatan,
                ]);

                LogAktivitas::catat(
                    'Izin Siswa Terlambat',
                    "Waka Piket ({$namaWaka}) MENYETUJUI izin terlambat {$namaSiswa} (Nomor: {$nomorSurat})",
                    $izin,
                    $user
                );

                // Kirim notifikasi ke Guru Piket
                try {
                    Notifikasi::create([
                        'user_id' => $izin->diinput_oleh,
                        'judul'   => 'Izin Terlambat Disetujui',
                        'pesan'   => "Izin masuk terlambat {$namaSiswa} disetujui Waka Piket dengan nomor surat {$nomorSurat}. Surat siap dicetak.",
                        'tipe'    => 'izin_terlambat',
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {}
            } elseif ($action === 'tolak') {
                $izin->update([
                    'status'             => 'Ditolak',
                    'dikonfirmasi_oleh'  => $user->id,
                    'dikonfirmasi_at'    => now(),
                    'catatan_konfirmasi' => $request->catatan,
                ]);

                LogAktivitas::catat(
                    'Izin Siswa Terlambat',
                    "Waka Piket ({$namaWaka}) MENOLAK izin terlambat {$namaSiswa}. Catatan: " . ($request->catatan ?: '-'),
                    $izin,
                    $user
                );
            }
        });

        $msg = ($action === 'setujui')
            ? "Izin masuk terlambat untuk {$namaSiswa} berhasil DISETUJUI. Nomor surat resmi telah diterbitkan dan surat siap dicetak."
            : "Permohonan izin terlambat {$namaSiswa} telah DITOLAK.";

        return back()->with('success', $msg);
    }

    /**
     * Cetak Slip / Lembar Surat Izin Masuk Kelas Siswa Terlambat
     */
    public function cetak($id)
    {
        Carbon::setLocale('id');
        $terlambat = IzinTerlambat::with(['siswa.kelas', 'diinputOlehUser', 'dikonfirmasiOlehUser'])->findOrFail($id);

        if ($terlambat->status !== 'Disetujui' && Auth::user()->role !== 'admin') {
            return back()->with('error', 'Hanya surat izin yang telah disetujui yang dapat dicetak.');
        }

        $tanggalFormatted = Carbon::parse($terlambat->tanggal)->translatedFormat('l, d F Y');
        $waktuPersetujuan = $terlambat->dikonfirmasi_at
            ? Carbon::parse($terlambat->dikonfirmasi_at)->translatedFormat('d F Y • H:i') . ' WIB'
            : '-';

        return view('guru_piket.terlambat.cetak', compact('terlambat', 'tanggalFormatted', 'waktuPersetujuan'));
    }

    /**
     * Read-Only Monitoring Siswa Terlambat untuk Pos Jaga Satpam
     */
    public function satpamIndex(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('l, d F Y');

        $search = $request->input('search');

        $query = IzinTerlambat::with(['siswa.kelas'])
            ->whereDate('tanggal', $today);

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        $terlambatList = $query->orderByDesc('id')->get();

        return view('satpam.terlambat.index', compact('user', 'today', 'todayFormatted', 'search', 'terlambatList'));
    }

    /**
     * Rekap Keterlambatan Siswa Kelas Binaan untuk Wali Kelas
     */
    public function waliKelasIndex(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $bulan = $request->input('bulan', Carbon::today()->format('Y-m'));

        // Dapatkan kelas binaan wali kelas
        $kelas = $user->kelasBinaan;
        if (!$kelas) {
            return back()->with('error', 'Akun Anda belum terhubung dengan kelas binaan manapun.');
        }

        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        $riwayatTerlambat = IzinTerlambat::with(['siswa'])
            ->where('id_kelas', $kelas->id_kelas)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderByDesc('tanggal')
            ->paginate(20)
            ->withQueryString();

        $rekapPerSiswa = IzinTerlambat::where('id_kelas', $kelas->id_kelas)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('status', 'Disetujui')
            ->select('id_siswa', DB::raw('count(*) as total_terlambat'))
            ->groupBy('id_siswa')
            ->orderByDesc('total_terlambat')
            ->with('siswa')
            ->get();

        return view('wali_kelas.terlambat.index', compact('user', 'kelas', 'bulan', 'riwayatTerlambat', 'rekapPerSiswa'));
    }

    /**
     * Rekap Eksekutif Keterlambatan Siswa Se-Sekolah (Waka Kesiswaan & Kepala Sekolah)
     */
    public function rekap(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();

        $bulan = $request->input('bulan', Carbon::today()->format('Y-m'));
        $kelasId = $request->input('id_kelas');

        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        $query = IzinTerlambat::with(['siswa.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($kelasId) {
            $query->where('id_kelas', $kelasId);
        }

        $totalTerlambat = (clone $query)->where('status', 'Disetujui')->count();

        // Top 10 Siswa Paling Sering Terlambat
        $topSiswa = (clone $query)->where('status', 'Disetujui')
            ->select('id_siswa', DB::raw('count(*) as total_terlambat'))
            ->groupBy('id_siswa')
            ->orderByDesc('total_terlambat')
            ->with('siswa.kelas')
            ->take(10)
            ->get();

        // Rekap per Kelas
        $rekapKelas = (clone $query)->where('status', 'Disetujui')
            ->select('id_kelas', DB::raw('count(*) as total_terlambat'))
            ->groupBy('id_kelas')
            ->orderByDesc('total_terlambat')
            ->with('kelas')
            ->get();

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('waka_kesiswaan.terlambat.rekap', compact(
            'user', 'bulan', 'kelasId', 'totalTerlambat', 'topSiswa', 'rekapKelas', 'kelasList'
        ));
    }
}
