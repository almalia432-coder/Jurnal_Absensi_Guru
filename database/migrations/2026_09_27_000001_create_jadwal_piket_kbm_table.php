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
        Schema::create('jadwal_piket_kbm', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 20)->default('2026/2027');
            $table->string('semester', 20)->default('Ganjil');
            $table->enum('siklus', ['A', 'B'])->default('A');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']);
            $table->enum('shift', ['Pagi', 'Siang']);
            $table->time('jam_mulai')->default('07:00:00');
            $table->time('jam_selesai')->default('11:00:00');
            $table->enum('peran', ['koordinator', 'petugas'])->default('petugas');
            $table->integer('urutan')->default(1);
            $table->unsignedBigInteger('id_guru')->nullable();
            $table->string('nama_guru');
            $table->string('nip', 30)->nullable();
            $table->string('piket_waka_nama')->nullable();
            $table->string('piket_waka_nip', 30)->nullable();
            $table->timestamps();

            $table->foreign('id_guru')->references('id_guru')->on('guru')->nullOnDelete();
            $table->index(['siklus', 'hari', 'shift']);
            $table->index('id_guru');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_piket_kbm');
    }
};
