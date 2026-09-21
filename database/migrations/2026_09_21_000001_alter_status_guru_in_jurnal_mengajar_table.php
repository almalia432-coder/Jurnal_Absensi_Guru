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
        // Alter status_guru in jurnal_mengajar to support 'Dinas'
        DB::statement("ALTER TABLE jurnal_mengajar MODIFY COLUMN status_guru ENUM('Hadir', 'Izin', 'Sakit', 'Dinas') NOT NULL DEFAULT 'Hadir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE jurnal_mengajar MODIFY COLUMN status_guru ENUM('Hadir', 'Izin', 'Sakit') NOT NULL DEFAULT 'Hadir'");
    }
};
