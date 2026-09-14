<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'walimurid@smkn1boyolangu.sch.id')->first();
echo $u ? "found: role=" . $u->role . "\n" : "not found\n";
if ($u) {
    $s = $u->siswa;
    echo "siswa linked: " . ($s ? $s->nama_lengkap . " (id_siswa=" . $s->id_siswa . ")" : "NONE") . "\n";
    echo "siswa count in db: " . App\Models\Siswa::count() . "\n";
    // Fallback - get first active student
    $fallback = App\Models\Siswa::with('kelas.waliKelas')->where('status_aktif', true)->first();
    echo "fallback siswa: " . ($fallback ? $fallback->nama_lengkap : "NONE - no active students!") . "\n";
}
