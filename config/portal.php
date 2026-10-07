<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Scope Masa Berlaku Portal Piket Guru
    |--------------------------------------------------------------------------
    | 'hari'  : Aktif sepanjang hari tugas piket (00:00 - 23:59).
    | 'shift' : Aktif hanya saat jam shift piket berlangsung (jam_mulai - jam_selesai).
    */
    'piket_scope' => env('PORTAL_PIKET_SCOPE', 'hari'),

    /*
    |--------------------------------------------------------------------------
    | Legacy Role Fallback
    |--------------------------------------------------------------------------
    | Bila true, role lama di users.role (guru_piket, waka_piket, waka_sdm, dll)
    | tetap diakui sebagai portal selama masa transisi migrasi akun tunggal.
    */
    'legacy_role_fallback' => env('PORTAL_LEGACY_ROLE_FALLBACK', true),

    /*
    |--------------------------------------------------------------------------
    | Mapping Kode Bidang Waka ke Portal Waka
    |--------------------------------------------------------------------------
    | Satu waka bisa memiliki beberapa kode.
    | kurikulum                  -> waka_kurikulum
    | sdm                        -> waka_sdm
    | kesiswaan, kedisiplinan    -> waka_kesiswaan
    | sarpras, bk, humas         -> null (tidak memiliki dashboard portal waka khusus)
    */
    'waka_bidang_map' => [
        'kurikulum'    => 'waka_kurikulum',
        'sdm'          => 'waka_sdm',
        'kesiswaan'    => 'waka_kesiswaan',
        'kedisiplinan' => 'waka_kesiswaan',
        'sarpras'      => null,
        'bk'           => null,
        'humas'        => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Definisi Portal dan Konfigurasi Rute
    |--------------------------------------------------------------------------
    */
    'portals' => [
        'admin' => [
            'name'       => 'Administrator',
            'route'      => 'admin.dashboard',
            'icon'       => 'fa-solid fa-shield-halved',
            'color'      => '#3b82f6',
            'permission' => 'Semua Akses Sistem',
        ],
        'guru_mengajar' => [
            'name'       => 'Portal Guru Mapel',
            'route'      => 'guru-mapel.dashboard',
            'icon'       => 'fa-solid fa-chalkboard-user',
            'color'      => '#10b981',
            'permission' => 'Jurnal & Presensi Kelas',
        ],
        'wali_kelas' => [
            'name'       => 'Wali Kelas',
            'route'      => 'wali-kelas.dashboard',
            'icon'       => 'fa-solid fa-user-tie',
            'color'      => '#6366f1',
            'permission' => 'Binaan Kelas & Rekap Siswa',
        ],
        'piket' => [
            'name'       => 'Guru Piket KBM',
            'route'      => 'guru-piket.dashboard',
            'icon'       => 'fa-solid fa-clipboard-check',
            'color'      => '#f59e0b',
            'permission' => 'Monitoring KBM & Rekap Harian',
        ],
        'piket_waka' => [
            'name'       => 'Waka Piket',
            'route'      => 'waka-piket.dispensasi',
            'icon'       => 'fa-solid fa-user-shield',
            'color'      => '#ec4899',
            'permission' => 'Persetujuan Dispen & Izin Terlambat',
        ],
        'waka_kurikulum' => [
            'name'       => 'Waka Kurikulum',
            'route'      => 'waka-kurikulum.dashboard',
            'icon'       => 'fa-solid fa-book-open-reader',
            'color'      => '#06b6d4',
            'permission' => 'Plotting Mapel & Jadwal KBM',
        ],
        'waka_sdm' => [
            'name'       => 'Waka SDM & Kepegawaian',
            'route'      => 'waka-sdm.dashboard',
            'icon'       => 'fa-solid fa-users-gear',
            'color'      => '#8b5cf6',
            'permission' => 'Izin Guru & Kepegawaian',
        ],
        'waka_kesiswaan' => [
            'name'       => 'Waka Kesiswaan',
            'route'      => 'waka-kesiswaan.dashboard',
            'icon'       => 'fa-solid fa-user-graduate',
            'color'      => '#14b8a6',
            'permission' => 'Kedisiplinan & Presensi Siswa',
        ],
        'satpam' => [
            'name'       => 'Satpam / Pos Jaga',
            'route'      => 'satpam.dashboard',
            'icon'       => 'fa-solid fa-building-shield',
            'color'      => '#64748b',
            'permission' => 'Buku Tamu & Gerbang Siswa',
        ],
        'kepala_sekolah' => [
            'name'       => 'Kepala Sekolah',
            'route'      => 'kepala-sekolah.dashboard',
            'icon'       => 'fa-solid fa-award',
            'color'      => '#eab308',
            'permission' => 'Monitoring Eksekutif Sekolah',
        ],
        'wali_murid' => [
            'name'       => 'Wali Murid / Siswa',
            'route'      => 'wali-murid.dashboard',
            'icon'       => 'fa-solid fa-house-user',
            'color'      => '#0284c7',
            'permission' => 'Presensi & Perkembangan Siswa',
        ],
    ],
];
