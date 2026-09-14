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
        Schema::table('mapel', function (Blueprint $table) {
            if (Schema::hasColumn('mapel', 'jp_standar')) {
                $table->dropColumn('jp_standar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mapel', function (Blueprint $table) {
            $table->unsignedTinyInteger('jp_standar')->nullable()->default(null)->after('kelompok')
                  ->comment('Target jam pelajaran per minggu untuk mapel ini (standar kurikulum)');
        });
    }
};
