<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\IzinGuru;
use App\Models\DispensasiSiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

$today = Carbon::today()->format('Y-m-d');
$now = Carbon::now();

// 1. Get or Create Guru "Budi Utomo, S.Pd"
$guruBudi = Guru::where('nama_lengkap', 'LIKE', '%Budi Utomo%')->first();
if (!$guruBudi) {
    $userBudi = User::firstOrCreate(
        ['email' => 'budiutomo@smkn1boyolangu.sch.id'],
        [
            'name' => 'Budi Utomo, S.Pd',
            'password' => Hash::make('password'),
            'role' => 'guru_mapel',
            'is_active' => true,
        ]
    );
    $guruBudi = Guru::create([
        'user_id' => $userBudi->id,
        'nip' => '198405152009021008',
        'nama_lengkap' => 'Budi Utomo, S.Pd',
        'jenis_kelamin' => 'L',
        'no_hp' => '081298765432',
    ]);
}

// 2. Get Kelas XI RPL 1 or First Class
$kelasRpl = Kelas::where('nama_kelas', 'LIKE', '%XI RPL%')->orWhere('nama_kelas', 'LIKE', '%RPL%')->first() ?? Kelas::first();
$idKelas = $kelasRpl ? $kelasRpl->id_kelas : 1;

// 3. Get or Create Siswa "Felisa Putri Maharani" and "Alma Liatul"
$siswaFelisa = Siswa::where('nama_lengkap', 'LIKE', '%Felisa%')->first();
if (!$siswaFelisa) {
    $siswaFelisa = Siswa::create([
        'nis' => '240091',
        'nisn' => '0078829101',
        'nama_lengkap' => 'Felisa Putri Maharani',
        'jenis_kelamin' => 'P',
        'id_kelas' => $idKelas,
        'status_aktif' => true,
    ]);
}

$siswaAlma = Siswa::where('nama_lengkap', 'LIKE', '%Alma Liatul%')->first();
if (!$siswaAlma) {
    $siswaAlma = Siswa::create([
        'nis' => '240092',
        'nisn' => '0078829102',
        'nama_lengkap' => 'Alma Liatul',
        'jenis_kelamin' => 'P',
        'id_kelas' => $idKelas,
        'status_aktif' => true,
    ]);
}

$adminUser = User::where('role', 'admin')->first() ?? User::first();
$adminId = $adminUser ? $adminUser->id : 1;

// 4. Create Sample Izin Guru
// Izin 1: Menunggu - Budi Utomo
IzinGuru::firstOrCreate(
    [
        'id_guru' => $guruBudi->id_guru,
        'tanggal_mulai' => $today,
        'status' => 'Menunggu',
    ],
    [
        'tanggal_selesai' => Carbon::parse($today)->addDays(2)->format('Y-m-d'),
        'jenis_izin' => 'Sakit',
        'alasan' => 'Demam tinggi dan disarankan dokter istirahat 2 hari',
        'diinput_oleh' => $adminId,
    ]
);

// Izin 2: Disetujui
$guru2 = Guru::where('id_guru', '!=', $guruBudi->id_guru)->first();
if ($guru2) {
    IzinGuru::firstOrCreate(
        [
            'id_guru' => $guru2->id_guru,
            'tanggal_mulai' => Carbon::parse($today)->subDays(1)->format('Y-m-d'),
        ],
        [
            'tanggal_selesai' => Carbon::parse($today)->subDays(1)->format('Y-m-d'),
            'jenis_izin' => 'Dinas_Luar',
            'alasan' => 'Menghadiri Rapat Koordinasi MGMP Kabupaten',
            'status' => 'Disetujui',
            'disetujui_oleh' => $adminId,
            'tanggal_persetujuan' => $now,
            'catatan_persetujuan' => 'Disetujui, harap titipkan tugas untuk kelas yang ditinggalkan.',
            'diinput_oleh' => $adminId,
        ]
    );
}

// 5. Create Sample Dispensasi Siswa
// Disp 1: Menunggu - Felisa
DispensasiSiswa::firstOrCreate(
    [
        'id_siswa' => $siswaFelisa->id_siswa,
        'tanggal' => $today,
        'status' => 'Menunggu',
    ],
    [
        'jam_keluar' => '08:30:00',
        'jam_kembali' => '13:00:00',
        'alasan' => 'Mengikuti Seleksi LKS Tingkat Provinsi Jawa Timur',
        'diinput_oleh' => $adminId,
    ]
);

// Disp 2: Menunggu - Alma Liatul
DispensasiSiswa::firstOrCreate(
    [
        'id_siswa' => $siswaAlma->id_siswa,
        'tanggal' => $today,
        'status' => 'Menunggu',
    ],
    [
        'jam_keluar' => '09:00:00',
        'jam_kembali' => '14:30:00',
        'alasan' => 'Persiapan Lomba Robotika & IoT Nasional',
        'diinput_oleh' => $adminId,
    ]
);

echo "Seed Waka SDM Demo Data Completed Successfully!\n";
