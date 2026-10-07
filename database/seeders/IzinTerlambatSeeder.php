<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IzinTerlambat;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;

class IzinTerlambatSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today()->format('Y-m-d');

        $guruPiketUser = User::where('role', 'guru_piket')->first() ?? User::where('email', 'like', '%piket%')->first() ?? User::first();
        $wakaPiketUser = User::where('email', 'waka.piket@smkn1boyolangu.sch.id')->first() ?? User::where('role', 'admin')->first();

        $students = Siswa::with('kelas')->take(3)->get();
        if ($students->count() < 3) {
            return;
        }

        // 1. Izin Terlambat Disetujui (Sudah terbit nomor surat)
        $st1 = $students[0];
        IzinTerlambat::create([
            'id_siswa'            => $st1->id_siswa,
            'id_kelas'            => $st1->id_kelas,
            'tanggal'             => $today,
            'jam_masuk'           => '08:15:00',
            'jam_ke_mulai'        => '3',
            'alasan'              => 'Membantu orang tua mengantar adik yang sakit ke puskesmas terdekat',
            'status'              => 'Disetujui',
            'nomor_surat'         => '001/IZIN-TLT/X/2026',
            'diinput_oleh'        => $guruPiketUser->id,
            'dikonfirmasi_oleh'   => $wakaPiketUser->id,
            'dikonfirmasi_at'     => Carbon::now()->subMinutes(30),
            'catatan_konfirmasi'  => 'Disetujui untuk mengikuti pembelajaran mulai jam ke-3.',
        ]);

        // 2. Izin Terlambat Menunggu Persetujuan
        $st2 = $students[1];
        IzinTerlambat::create([
            'id_siswa'            => $st2->id_siswa,
            'id_kelas'            => $st2->id_kelas,
            'tanggal'             => $today,
            'jam_masuk'           => '07:40:00',
            'jam_ke_mulai'        => '2',
            'alasan'              => 'Kendaraan mengalami pecah ban di jalan raya Boyolangu',
            'status'              => 'Menunggu',
            'nomor_surat'         => null,
            'diinput_oleh'        => $guruPiketUser->id,
            'dikonfirmasi_oleh'   => null,
            'dikonfirmasi_at'     => null,
            'catatan_konfirmasi'  => null,
        ]);

        // 3. Izin Terlambat Ditolak
        $st3 = $students[2];
        IzinTerlambat::create([
            'id_siswa'            => $st3->id_siswa,
            'id_kelas'            => $st3->id_kelas,
            'tanggal'             => $today,
            'jam_masuk'           => '09:00:00',
            'jam_ke_mulai'        => '4',
            'alasan'              => 'Bangun kesiangan dan tidak ada alasan mendesak',
            'status'              => 'Ditolak',
            'nomor_surat'         => null,
            'diinput_oleh'        => $guruPiketUser->id,
            'dikonfirmasi_oleh'   => $wakaPiketUser->id,
            'dikonfirmasi_at'     => Carbon::now()->subMinutes(10),
            'catatan_konfirmasi'  => 'Keterlambatan lebih dari 1 jam tanpa alasan valid. Siswa diarahkan ke ruang BK untuk pembinaan.',
        ]);
    }
}
