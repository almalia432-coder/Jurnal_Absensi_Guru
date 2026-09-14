<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah kolom 'nis' menjadi NULLABLE agar kompatibel jika data presensi hanya memiliki NISN
        DB::statement("ALTER TABLE `siswa` MODIFY COLUMN `nis` VARCHAR(30) NULL");

        // 2. Tambahkan kolom 'niss' (Nomor Induk Siswa Sekolah) jika belum ada
        if (!Schema::hasColumn('siswa', 'niss')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->string('niss', 30)->nullable()->after('nisn');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('siswa', 'niss')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropColumn('niss');
            });
        }

        DB::statement("ALTER TABLE `siswa` MODIFY COLUMN `nis` VARCHAR(20) NOT NULL");
    }
};
