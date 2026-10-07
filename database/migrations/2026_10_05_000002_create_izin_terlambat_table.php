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
        Schema::create('izin_terlambat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('id_kelas');
            $table->date('tanggal');
            $table->time('jam_masuk');
            $table->string('jam_ke_mulai', 10)->comment('Mulai diizinkan masuk jam ke-berapa');
            $table->text('alasan');
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak', 'Dibatalkan'])->default('Menunggu');
            $table->string('nomor_surat', 100)->nullable()->unique();
            $table->unsignedBigInteger('diinput_oleh')->comment('ID User Guru Piket');
            $table->unsignedBigInteger('dikonfirmasi_oleh')->nullable()->comment('ID User Waka Piket / Admin');
            $table->timestamp('dikonfirmasi_at')->nullable();
            $table->text('catatan_konfirmasi')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->cascadeOnDelete();
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->cascadeOnDelete();
            $table->foreign('diinput_oleh')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('dikonfirmasi_oleh')->references('id')->on('users')->nullOnDelete();

            // Indexes for Performance
            $table->index(['tanggal', 'status']);
            $table->index(['id_siswa', 'tanggal']);
            $table->index('status');
            $table->index('id_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_terlambat');
    }
};
