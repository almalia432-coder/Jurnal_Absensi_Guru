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
        Schema::table('izin_guru', function (Blueprint $table) {
            // Guru Piket Approval
            $table->foreignId('piket_approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->enum('piket_status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu')->after('piket_approved_by');
            $table->datetime('piket_at')->nullable()->after('piket_status');
            $table->text('piket_catatan')->nullable()->after('piket_at');

            // Waka SDM Approval
            $table->foreignId('waka_approved_by')->nullable()->after('piket_catatan')->constrained('users')->nullOnDelete();
            $table->enum('waka_status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu')->after('waka_approved_by');
            $table->datetime('waka_at')->nullable()->after('waka_status');
            $table->text('waka_catatan')->nullable()->after('waka_at');

            // Kepala Sekolah Approval
            $table->foreignId('kepsek_approved_by')->nullable()->after('waka_catatan')->constrained('users')->nullOnDelete();
            $table->enum('kepsek_status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu')->after('kepsek_approved_by');
            $table->datetime('kepsek_at')->nullable()->after('kepsek_status');
            $table->text('kepsek_catatan')->nullable()->after('kepsek_at');

            // Tahap & Rejection Tracking
            $table->string('tahap_approval', 30)->default('piket')->after('kepsek_catatan'); // piket, waka_sdm, kepsek, selesai, ditolak
            $table->string('ditolak_oleh_role', 30)->nullable()->after('tahap_approval'); // guru_piket, waka_sdm, kepala_sekolah
            $table->text('ditolak_catatan')->nullable()->after('ditolak_oleh_role');
        });

        // Update existing records for backward compatibility
        DB::table('izin_guru')->where('status', 'Disetujui')->update([
            'piket_status'   => 'Disetujui',
            'waka_status'    => 'Disetujui',
            'kepsek_status'  => 'Disetujui',
            'tahap_approval' => 'selesai',
        ]);

        DB::table('izin_guru')->where('status', 'Ditolak')->update([
            'tahap_approval' => 'ditolak',
        ]);

        DB::table('izin_guru')->where('status', 'Menunggu')->update([
            'tahap_approval' => 'piket',
            'piket_status'   => 'Menunggu',
            'waka_status'    => 'Menunggu',
            'kepsek_status'  => 'Menunggu',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->dropForeign(['piket_approved_by']);
            $table->dropForeign(['waka_approved_by']);
            $table->dropForeign(['kepsek_approved_by']);

            $table->dropColumn([
                'piket_approved_by',
                'piket_status',
                'piket_at',
                'piket_catatan',
                'waka_approved_by',
                'waka_status',
                'waka_at',
                'waka_catatan',
                'kepsek_approved_by',
                'kepsek_status',
                'kepsek_at',
                'kepsek_catatan',
                'tahap_approval',
                'ditolak_oleh_role',
                'ditolak_catatan',
            ]);
        });
    }
};
