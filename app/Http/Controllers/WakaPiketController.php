<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DispensasiSiswa;
use App\Models\JadwalPiketKbm;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WakaPiketController extends Controller
{
    /**
     * Halaman Persetujuan Dispensasi Siswa oleh Waka Piket
     */
    public function dispensasi(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $status  = $request->input('status', 'Menunggu'); // default filter ke Menunggu
        $search  = $request->input('search');

        $targetDate = Carbon::parse($tanggal);
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');

        // Cek data roster Waka Piket hari ini
        $rosterToday = JadwalPiketKbm::getRosterForDate($targetDate);
        $wakaPiketInfo = $rosterToday['waka'] ?? null;

        // Cek apakah user yang login adalah Waka Piket hari ini
        $isWakaPiketToday = false;
        if ($user->email === 'waka.piket@smkn1boyolangu.sch.id' || $user->role === 'admin') {
            $isWakaPiketToday = true;
        } elseif ($wakaPiketInfo) {
            $userNip = $user->guru->nip ?? ($user->waka->nip ?? null);
            if ($userNip && $userNip === $wakaPiketInfo['nip']) {
                $isWakaPiketToday = true;
            }
        }

        // Query Dispensasi
        $query = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser', 'disetujuiOlehUser'])
            ->whereDate('tanggal', $tanggal);

        if ($status && $status !== 'Semua') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%");
            });
        }

        $dispensasiList = $query->orderByDesc('id')->paginate(15)->withQueryString();

        // Ringkasan Metrics Hari Terpilih
        $baseQuery = DispensasiSiswa::whereDate('tanggal', $tanggal);
        $metrics = [
            'total'     => (clone $baseQuery)->count(),
            'menunggu'  => (clone $baseQuery)->where('status', 'Menunggu')->count(),
            'disetujui' => (clone $baseQuery)->where('status', 'Disetujui')->count(),
            'ditolak'   => (clone $baseQuery)->where('status', 'Ditolak')->count(),
            'selesai'   => (clone $baseQuery)->where('status', 'Selesai')->count(),
        ];

        return view('waka_piket.dispensasi.index', compact(
            'user', 'tanggal', 'status', 'search', 'todayFormatted',
            'wakaPiketInfo', 'isWakaPiketToday', 'dispensasiList', 'metrics'
        ));
    }

    /**
     * Konfirmasi Persetujuan / Penolakan Dispensasi oleh Waka Piket
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:setujui,tolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $disp = DispensasiSiswa::with('siswa')->findOrFail($id);
        $user = Auth::user();

        // Validasi Otoritas: Hanya Waka Piket yang bertugas pada hari tersebut (atau akun bersama/admin) yang berhak approve
        $rosterTarget = JadwalPiketKbm::getRosterForDate(Carbon::parse($disp->tanggal));
        $wakaDuty = $rosterTarget['waka'] ?? null;
        $isAuthorized = false;

        if ($user->email === 'waka.piket@smkn1boyolangu.sch.id' || $user->role === 'admin') {
            $isAuthorized = true;
        } elseif ($wakaDuty) {
            $userNip = $user->guru->nip ?? ($user->waka->nip ?? null);
            if ($userNip && $userNip === $wakaDuty['nip']) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            $petugasNama = $wakaDuty['nama'] ?? 'Waka Piket Lain';
            return back()->with('error', "Akses Ditolak! Anda tidak bertugas sebagai Waka Piket KBM pada tanggal " . Carbon::parse($disp->tanggal)->translatedFormat('d F Y') . ". Penanggung jawab piket adalah {$petugasNama}.");
        }

        $namaWaka = $user->name;
        $namaSiswa = $disp->siswa->nama_lengkap ?? 'Siswa';

        if ($request->action === 'setujui') {
            $disp->update([
                'status'              => 'Disetujui',
                'disetujui_oleh'      => $user->id,
                'tanggal_persetujuan' => now(),
            ]);

            // Catat Log Aktivitas
            LogAktivitas::catat(
                'Dispensasi Siswa',
                "Waka Piket ({$namaWaka}) MENYETUJUI izin dispensasi siswa {$namaSiswa}",
                $disp,
                $user
            );

            // Kirim notifikasi sistem jika tabel ada
            try {
                Notifikasi::create([
                    'user_id'    => $disp->diinput_oleh ?? $user->id,
                    'judul'      => 'Dispensasi Disetujui Waka Piket',
                    'pesan'      => "Dispensasi atas nama {$namaSiswa} telah disetujui oleh Waka Piket ({$namaWaka}). Surat izin siap dicetak.",
                    'tipe'       => 'dispensasi_siswa',
                    'is_read'    => false,
                ]);
            } catch (\Exception $e) {}

            return back()->with('success', "Dispensasi untuk {$namaSiswa} berhasil DISETUJUI. Siswa diizinkan keluar dan surat dapat dicetak.");
        } elseif ($request->action === 'tolak') {
            $disp->update([
                'status'              => 'Ditolak',
                'disetujui_oleh'      => $user->id,
                'tanggal_persetujuan' => now(),
            ]);

            LogAktivitas::catat(
                'Dispensasi Siswa',
                "Waka Piket ({$namaWaka}) MENOLAK izin dispensasi siswa {$namaSiswa}. Alasan: " . ($request->catatan ?? 'Tidak memenuhi syarat'),
                $disp,
                $user
            );

            return back()->with('error', "Permohonan dispensasi untuk {$namaSiswa} telah DITOLAK.");
        }

        return back();
    }
}
