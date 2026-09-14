<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom jp_standar (target jam pelajaran per minggu) ke tabel mapel.
     */
    public function up(): void
    {
        Schema::table('mapel', function (Blueprint $table) {
            $table->unsignedTinyInteger('jp_standar')->nullable()->default(null)->after('kelompok')
                  ->comment('Target jam pelajaran per minggu untuk mapel ini (standar kurikulum)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mapel', function (Blueprint $table) {
            $table->dropColumn('jp_standar');
        });
    }
};
