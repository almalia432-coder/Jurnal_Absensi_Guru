<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_harian_kbm', function (Blueprint $table) {
            $table->id('id_status');
            $table->date('tanggal')->unique();
            $table->boolean('ada_upacara')->default(true)->comment('Khusus hari Senin');
            $table->boolean('ada_pembiasaan_jumat')->default(true)->comment('Khusus hari Jumat');
            $table->string('catatan')->nullable()->comment('Contoh: Hujan lebat upacara ditiadakan');
            $table->unsignedBigInteger('id_guru_piket')->nullable();
            $table->timestamps();

            $table->foreign('id_guru_piket')->references('id_guru')->on('guru')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_harian_kbm');
    }
};
