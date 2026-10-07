<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DispensasiSiswa;
use App\Models\JadwalPiketKbm;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use App\Models\User;
use App\Support\PortalResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $status  = $request->input('status', 'Disetujui_Piket'); // default: antrean tahap 2
        $search  = $request->input('search');

        $targetDate = Carbon::parse($tanggal);
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');

        // Cek data roster Waka Piket hari ini
        $rosterToday = JadwalPiketKbm::getRosterForDate($targetDate);
        $wakaPiketInfo = $rosterToday['waka'] ?? null;

        // Cek apakah user yang login adalah Waka Piket pada tanggal terpilih via PortalResolver
        $isWakaPiketToday = PortalResolver::hasDuty($user, 'piket_waka', $targetDate);

        // Query Dispensasi
        $query = DispensasiSiswa::with(['siswa.kelas', 'diinputOlehUser', 'disetujuiOlehUser', 'piketApprovedByUser'])
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
            'total'            => (clone $baseQuery)->count(),
            'menunggu'         => (clone $baseQuery)->where('status', 'Menunggu')->count(),
            'disetujui_piket'  => (clone $baseQuery)->where('status', 'Disetujui_Piket')->count(),
            'disetujui'        => (clone $baseQuery)->where('status', 'Disetujui')->count(),
            'ditolak'          => (clone $baseQuery)->where('status', 'Ditolak')->count(),
            'selesai'          => (clone $baseQuery)->where('status', 'Selesai')->count(),
        ];

        return view('waka_piket.dispensasi.index', compact(
            'user', 'tanggal', 'status', 'search', 'todayFormatted',
            'wakaPiketInfo', 'isWakaPiketToday', 'dispensasiList', 'metrics'
        ));
    }

    /**
     * Konfirmasi Persetujuan / Penolakan Dispensasi — Tahap 2 (Waka Piket)
     *
     * Aksi:
     *  - setujui : Disetujui_Piket → Disetujui
     *  - tolak   : Menunggu|Disetujui_Piket → Ditolak (catatan wajib, min 5 karakter)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'action'  => 'required|in:setujui,tolak,batalkan',
            'catatan' => 'nullable|string|max:500',
        ]);

        $disp = DispensasiSiswa::with('siswa')->findOrFail($id);
        $user = Auth::user();

        // Validasi Otoritas: Waka Piket bertugas pada tanggal dispensasi via PortalResolver (atau admin)
        $dispDate = Carbon::parse($disp->tanggal);
        $isAuthorized = PortalResolver::hasDuty($user, 'piket_waka', $dispDate);

        if (!$isAuthorized) {
            return back()->with('error', "Akses Ditolak! Anda tidak bertugas sebagai Waka Piket KBM pada tanggal " . $dispDate->translatedFormat('d F Y') . ".");
        }

        $namaWaka = $user->name;
        $namaSiswa = $disp->siswa->nama_lengkap ?? 'Siswa';

        // ── Pembatalan oleh Admin ─────────────────────────────────────────
        if ($request->action === 'batalkan') {
            if ($user->role !== 'admin') {
                return back()->with('error', 'Hanya administrator yang berhak membatalkan dispensasi.');
            }

            $request->validate(['catatan' => 'required|string|min:5|max:500']);

            $msg = DB::transaction(function () use ($disp, $user, $namaSiswa, $request) {
                $locked = DispensasiSiswa::where('id', $disp->id)->lockForUpdate()->first();
                $locked->update([
                    'status'          => 'Dibatalkan',
                    'alasan_batal'    => $request->catatan,
                    'dibatalkan_oleh' => $user->id,
                    'dibatalkan_at'   => now(),
                ]);

                LogAktivitas::catat(
                    'Dispensasi Siswa',
                    "Admin ({$user->name}) MEMBATALKAN dispensasi siswa {$namaSiswa}. Alasan: {$request->catatan}",
                    $locked,
                    $user
                );

                if ($locked->diinput_oleh) {
                    try {
                        Notifikasi::create([
                            'user_id'        => $locked->diinput_oleh,
                            'judul'          => "Dispensasi {$namaSiswa} Dibatalkan",
                            'pesan'          => "Dispensasi atas nama {$namaSiswa} telah dibatalkan oleh Administrator. Alasan: {$request->catatan}",
                            'tipe'           => 'dispensasi_siswa',
                            'reference_id'   => $locked->id,
                            'reference_type' => DispensasiSiswa::class,
                            'is_read'        => false,
                        ]);
                    } catch (\Exception $e) {}
                }

                return "Dispensasi siswa {$namaSiswa} berhasil dibatalkan.";
            });

            return back()->with('success', $msg);
        }

        // ── Setujui (Tahap 2): Disetujui_Piket → Disetujui ───────────────
        if ($request->action === 'setujui') {
            $msg = DB::transaction(function () use ($disp, $user, $namaWaka, $namaSiswa, $request) {
                $locked = DispensasiSiswa::where('id', $disp->id)
                    ->where('status', 'Disetujui_Piket')
                    ->lockForUpdate()
                    ->first();

                if (!$locked) {
                    return null;
                }

                $locked->update([
                    'status'              => 'Disetujui',
                    'disetujui_oleh'      => $user->id,
                    'tanggal_persetujuan' => now(),
                    'catatan_waka'        => $request->input('catatan'),
                ]);

                LogAktivitas::catat(
                    'Dispensasi Siswa',
                    "Waka Piket ({$namaWaka}) MENYETUJUI Tahap 2 dispensasi siswa {$namaSiswa}",
                    $locked,
                    $user
                );

                // Notifikasi ke penginput
                if ($locked->diinput_oleh) {
                    try {
                        Notifikasi::create([
                            'user_id'        => $locked->diinput_oleh,
                            'judul'          => 'Dispensasi Disetujui Waka Piket',
                            'pesan'          => "Dispensasi atas nama {$namaSiswa} telah disetujui oleh Waka Piket ({$namaWaka}). Surat izin siap dicetak.",
                            'tipe'           => 'dispensasi_siswa',
                            'reference_id'   => $locked->id,
                            'reference_type' => DispensasiSiswa::class,
                            'is_read'        => false,
                        ]);
                    } catch (\Exception $e) {}
                }

                return "Dispensasi untuk {$namaSiswa} berhasil DISETUJUI (Tahap 2). Siswa diizinkan keluar dan surat dapat dicetak.";
            });

            if (!$msg) {
                return back()->with('error', 'Status dispensasi bukan Disetujui_Piket. Silakan muat ulang halaman.');
            }
            return back()->with('success', $msg);

        // ── Tolak: Menunggu|Disetujui_Piket → Ditolak ────────────────────
        } elseif ($request->action === 'tolak') {
            $request->validate(['catatan' => 'required|string|min:5|max:500']);

            $msg = DB::transaction(function () use ($disp, $user, $namaWaka, $namaSiswa, $request) {
                $locked = DispensasiSiswa::where('id', $disp->id)
                    ->whereIn('status', ['Menunggu', 'Disetujui_Piket'])
                    ->lockForUpdate()
                    ->first();

                if (!$locked) {
                    return null;
                }

                $locked->update([
                    'status'              => 'Ditolak',
                    'disetujui_oleh'      => $user->id,
                    'tanggal_persetujuan' => now(),
                    'catatan_waka'        => $request->input('catatan'),
                ]);

                LogAktivitas::catat(
                    'Dispensasi Siswa',
                    "Waka Piket ({$namaWaka}) MENOLAK dispensasi siswa {$namaSiswa}. Alasan: " . $request->input('catatan'),
                    $locked,
                    $user
                );

                // Notifikasi ke penginput
                if ($locked->diinput_oleh) {
                    try {
                        Notifikasi::create([
                            'user_id'        => $locked->diinput_oleh,
                            'judul'          => "Dispensasi {$namaSiswa} Ditolak",
                            'pesan'          => "Permohonan dispensasi untuk {$namaSiswa} telah DITOLAK oleh Waka Piket ({$namaWaka}). Alasan: " . $request->input('catatan'),
                            'tipe'           => 'dispensasi_siswa',
                            'reference_id'   => $locked->id,
                            'reference_type' => DispensasiSiswa::class,
                            'is_read'        => false,
                        ]);
                    } catch (\Exception $e) {}
                }

                return "Permohonan dispensasi untuk {$namaSiswa} telah DITOLAK.";
            });

            if (!$msg) {
                return back()->with('error', 'Status dispensasi sudah berubah. Silakan muat ulang halaman.');
            }
            return back()->with('error', $msg);
        }

        return back();
    }
}
