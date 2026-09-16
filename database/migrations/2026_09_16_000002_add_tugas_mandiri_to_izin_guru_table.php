<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->boolean('menitipkan_tugas')->default(false)->after('bukti_file');
            $table->text('keterangan_tugas')->nullable()->after('menitipkan_tugas');
            $table->string('lampiran_tugas')->nullable()->after('keterangan_tugas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->dropColumn(['menitipkan_tugas', 'keterangan_tugas', 'lampiran_tugas']);
        });
    }
};
