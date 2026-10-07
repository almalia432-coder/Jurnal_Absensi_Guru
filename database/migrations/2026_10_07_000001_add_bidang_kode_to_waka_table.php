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
        Schema::table('waka', function (Blueprint $table) {
            $table->json('bidang_kode')->nullable()->after('bidang');
        });

        // Seed awal bidang_kode berdasarkan kecocokan NIP di tabel guru
        $initialData = [
            [
                'nip'         => '198208222014072002', // Hardini Indahing Budi
                'bidang_kode' => ['kurikulum'],
            ],
            [
                'nip'         => '198203032009012009', // Niken Hari Pratiwi
                'bidang_kode' => ['bk', 'sdm'],
            ],
            [
                'nip'         => '197210302003121002', // Setiyo Winarko
                'bidang_kode' => ['kesiswaan'],
            ],
            [
                'nip'         => '197808102023211005', // Fajar Luthfianto
                'bidang_kode' => ['kedisiplinan'],
            ],
            [
                'nip'         => '197711122022211007', // Hendro Suwignyo
                'bidang_kode' => ['sarpras'],
            ],
        ];

        foreach ($initialData as $item) {
            $nip = (string) $item['nip'];
            $kodeList = $item['bidang_kode'];

            // Cocokkan NIP dengan tabel guru terlebih dahulu
            $guruExists = DB::table('guru')->where('nip', $nip)->exists();
            if ($guruExists) {
                DB::table('waka')->where('nip', $nip)->update([
                    'bidang_kode' => json_encode($kodeList),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waka', function (Blueprint $table) {
            $table->dropColumn('bidang_kode');
        });
    }
};
