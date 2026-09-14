<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get first kelas
$kelas = App\Models\Kelas::first();
echo "Kelas count: " . App\Models\Kelas::count() . "\n";
if ($kelas) {
    echo "First kelas: " . $kelas->nama_kelas . " (id=" . $kelas->id_kelas . ")\n";
    
    // Get wali murid user
    $user = App\Models\User::where('email', 'walimurid@smkn1boyolangu.sch.id')->first();
    echo "Wali murid user id: " . $user->id . "\n";
    
    // Create a test siswa
    $siswa = App\Models\Siswa::create([
        'user_id'       => $user->id,
        'nis'           => '2024001',
        'nisn'          => '0012345678',
        'nama_lengkap'  => 'Muhammad Rizki (Test)',
        'jenis_kelamin' => 'L',
        'id_kelas'      => $kelas->id_kelas,
        'no_hp_ortu'    => '081234567890',
        'alamat'        => 'Jl. Test No. 1',
        'status_aktif'  => true,
    ]);
    echo "Siswa created: " . $siswa->nama_lengkap . " (id=" . $siswa->id_siswa . ")\n";
} else {
    echo "No kelas found!\n";
}
