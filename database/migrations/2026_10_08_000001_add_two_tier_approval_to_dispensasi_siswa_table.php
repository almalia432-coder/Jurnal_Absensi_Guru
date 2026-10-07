<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah kolom status dari varchar(50) ke enum lengkap.
        //    Data existing: 4 × Disetujui, 1 × Selesai — keduanya valid di enum baru.
        DB::statement("ALTER TABLE dispensasi_siswa MODIFY COLUMN status ENUM(
            'Menunggu',
            'Disetujui_Piket',
            'Disetujui',
            'Disetujui_KS',
            'Disetujui_Waka',
            'Ditolak',
            'Selesai',
            'Dibatalkan'
        ) NOT NULL DEFAULT 'Menunggu'");

        // 2. Kolom persetujuan Tahap 1 (guru piket)
        Schema::table('dispensasi_siswa', function (Blueprint $table) {
            $table->foreignId('piket_approved_by')
                  ->nullable()
                  ->after('tanggal_persetujuan')
                  ->constrained('users')
                  ->nullOnDelete();
            $table->datetime('piket_at')
                  ->nullable()
                  ->after('piket_approved_by');
            $table->text('piket_catatan')
                  ->nullable()
                  ->after('piket_at');

            // 3. Catatan waka piket (Tahap 2)
            $table->text('catatan_waka')
                  ->nullable()
                  ->after('piket_catatan');

            // 4. Kolom pembatalan oleh admin
            $table->text('alasan_batal')
                  ->nullable()
                  ->after('catatan_waka');
            $table->foreignId('dibatalkan_oleh')
                  ->nullable()
                  ->after('alasan_batal')
                  ->constrained('users')
                  ->nullOnDelete();
            $table->datetime('dibatalkan_at')
                  ->nullable()
                  ->after('dibatalkan_oleh');

            // 5. Waktu aktual satpam (terpisah dari rencana jam_keluar / jam_kembali)
            $table->time('jam_keluar_aktual')
                  ->nullable()
                  ->after('jam_kembali');
            $table->time('jam_kembali_aktual')
                  ->nullable()
                  ->after('jam_keluar_aktual');
        });
    }

    public function down(): void
    {
        // a. Hapus kolom baru
        Schema::table('dispensasi_siswa', function (Blueprint $table) {
            $table->dropForeign(['piket_approved_by']);
            $table->dropForeign(['dibatalkan_oleh']);
            $table->dropColumn([
                'piket_approved_by',
                'piket_at',
                'piket_catatan',
                'catatan_waka',
                'alasan_batal',
                'dibatalkan_oleh',
                'dibatalkan_at',
                'jam_keluar_aktual',
                'jam_kembali_aktual',
            ]);
        });

        // b. Petakan nilai enum baru ke nilai yang ada di enum lama sebelum rollback.
        //    Disetujui_Piket → Menunggu (belum final)
        //    Dibatalkan → Ditolak (paling mendekati)
        DB::statement("UPDATE dispensasi_siswa
            SET status = CASE
                WHEN status = 'Disetujui_Piket' THEN 'Menunggu'
                WHEN status = 'Dibatalkan'       THEN 'Ditolak'
                ELSE status
            END
            WHERE status IN ('Disetujui_Piket', 'Dibatalkan')");

        // c. Kembalikan ke varchar(50) seperti kondisi sebelum migration ini.
        //    Menggunakan varchar bukan enum asli karena data existing mengandung
        //    'Disetujui' dan 'Selesai' yang tidak ada di enum asli
        //    (Menunggu, Disetujui_KS, Disetujui_Waka, Ditolak).
        DB::statement("ALTER TABLE dispensasi_siswa
            MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Menunggu'");
    }
};
