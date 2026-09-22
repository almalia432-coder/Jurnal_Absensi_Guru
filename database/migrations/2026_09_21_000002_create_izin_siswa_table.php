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
        Schema::create('izin_siswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa');
            $table->enum('jenis_izin', ['Sakit', 'Izin', 'Dispensasi'])->default('Sakit');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('alasan');
            $table->string('bukti_file')->nullable();
            $table->enum('status', ['Disetujui', 'Menunggu', 'Ditolak'])->default('Disetujui');
            $table->unsignedBigInteger('diinput_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->cascadeOnDelete();
            $table->foreign('diinput_oleh')->references('id')->on('users')->nullOnDelete();

            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index('jenis_izin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_siswa');
    }
};
