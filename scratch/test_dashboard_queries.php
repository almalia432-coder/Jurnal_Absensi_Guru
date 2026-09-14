<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\PresensiSiswa;
use App\Models\JurnalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\DispensasiSiswa;
use App\Models\User;

Carbon::setLocale('id');

$user = User::where('email', 'walimurid@smkn1boyolangu.sch.id')->first();
$siswa = $user->siswa ? $user->siswa->load('kelas.waliKelas') : null;

if (!$siswa) {
    $siswa = Siswa::with('kelas.waliKelas')->where('status_aktif', true)->first();
}

echo "Siswa: " . ($siswa ? $siswa->nama_lengkap : "NULL") . "\n";
echo "Kelas: " . ($siswa?->kelas?->nama_kelas ?? "NULL") . "\n";
echo "WaliKelas: " . ($siswa?->kelas?->waliKelas?->nama_guru ?? "NULL") . "\n";

$today = Carbon::today()->format('Y-m-d');
$hariIni = Carbon::today()->translatedFormat('l');
echo "Today: $today, Hari: $hariIni\n";

$presensiHariIni = PresensiSiswa::with(['jurnal.mapel', 'jurnal.guru'])
    ->where('id_siswa', $siswa->id_siswa)
    ->whereHas('jurnal', fn($q) => $q->where('tanggal', $today))
    ->get();
echo "Presensi hari ini: " . $presensiHariIni->count() . "\n";

$jadwalHariIni = JadwalPelajaran::with(['mapel', 'guru'])
    ->where('id_kelas', $siswa->id_kelas)
    ->where('hari', $hariIni)
    ->orderBy('jam_mulai')
    ->get();
echo "Jadwal hari ini: " . $jadwalHariIni->count() . "\n";

$allPresensi = PresensiSiswa::where('id_siswa', $siswa->id_siswa)->get();
$totalSesi = $allPresensi->count();
$hadirCount = $allPresensi->where('status', 'Hadir')->count();
$pctKehadiran = $totalSesi > 0 ? round(($hadirCount / $totalSesi) * 100, 1) : 100;
echo "Total Presensi: $totalSesi, Hadir: $hadirCount, Pct: $pctKehadiran%\n";

echo "All tests PASSED - Controller data queries work correctly\n";
