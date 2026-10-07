<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `izin_guru` MODIFY COLUMN `tahap_approval` VARCHAR(30) NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Berikan nilai default 'piket' untuk record yang null sebelum dikembalikan ke NOT NULL
        DB::statement("UPDATE `izin_guru` SET `tahap_approval` = 'piket' WHERE `tahap_approval` IS NULL");
        DB::statement("ALTER TABLE `izin_guru` MODIFY COLUMN `tahap_approval` VARCHAR(30) NOT NULL DEFAULT 'piket'");
    }
};
