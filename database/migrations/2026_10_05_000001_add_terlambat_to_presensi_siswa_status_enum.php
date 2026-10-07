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
        // Add 'Terlambat' status to presensi_siswa status ENUM
        DB::statement("ALTER TABLE presensi_siswa MODIFY COLUMN status ENUM('Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi', 'Terlambat') NOT NULL DEFAULT 'Hadir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert any existing 'Terlambat' to 'Hadir' to prevent data truncation during rollback
        DB::table('presensi_siswa')->where('status', 'Terlambat')->update(['status' => 'Hadir']);
        DB::statement("ALTER TABLE presensi_siswa MODIFY COLUMN status ENUM('Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi') NOT NULL DEFAULT 'Hadir'");
    }
};
