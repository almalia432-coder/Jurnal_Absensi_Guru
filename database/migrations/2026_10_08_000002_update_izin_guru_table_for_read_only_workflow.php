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
        // 1. Perluas enum status untuk menambahkan 'Tercatat' dan 'Dibatalkan'
        DB::statement("ALTER TABLE `izin_guru` MODIFY COLUMN `status` ENUM('Tercatat', 'Menunggu', 'Disetujui', 'Ditolak', 'Dibatalkan') NOT NULL DEFAULT 'Tercatat'");

        // 2. Konversi data eksisting: 'Menunggu' menjadi 'Tercatat'
        DB::statement("UPDATE `izin_guru` SET `status` = 'Tercatat' WHERE `status` = 'Menunggu'");

        // 3. Tambahkan kolom pembatalan izin
        Schema::table('izin_guru', function (Blueprint $table) {
            if (!Schema::hasColumn('izin_guru', 'alasan_batal')) {
                $table->text('alasan_batal')->nullable()->after('status');
            }
            if (!Schema::hasColumn('izin_guru', 'dibatalkan_oleh')) {
                $table->foreignId('dibatalkan_oleh')->nullable()->after('alasan_batal')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('izin_guru', 'dibatalkan_at')) {
                $table->dateTime('dibatalkan_at')->nullable()->after('dibatalkan_oleh');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Kembalikan status: 'Dibatalkan' ke 'Ditolak', 'Tercatat' ke 'Menunggu'
        DB::statement("UPDATE `izin_guru` SET `status` = 'Ditolak' WHERE `status` = 'Dibatalkan'");
        DB::statement("UPDATE `izin_guru` SET `status` = 'Menunggu' WHERE `status` = 'Tercatat'");

        // 2. Drop foreign key dan kolom pembatalan
        Schema::table('izin_guru', function (Blueprint $table) {
            if (Schema::hasColumn('izin_guru', 'dibatalkan_oleh')) {
                $table->dropForeign(['dibatalkan_oleh']);
                $table->dropColumn('dibatalkan_oleh');
            }
            if (Schema::hasColumn('izin_guru', 'alasan_batal')) {
                $table->dropColumn('alasan_batal');
            }
            if (Schema::hasColumn('izin_guru', 'dibatalkan_at')) {
                $table->dropColumn('dibatalkan_at');
            }
        });

        // 3. Kembalikan enum status semula
        DB::statement("ALTER TABLE `izin_guru` MODIFY COLUMN `status` ENUM('Menunggu', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Menunggu'");
    }
};
