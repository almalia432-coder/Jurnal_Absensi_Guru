<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kebijakan Status Jam Pelajaran Terlewat Sebelum Siswa Terlambat Masuk
    |--------------------------------------------------------------------------
    | Opsi yang didukung:
    | - 'Alpha'       : Jam sebelum siswa masuk dicatat sebagai Alpha otomatis.
    | - 'Terlambat'   : Jam sebelum siswa masuk ikut tercatat Terlambat dengan keterangan.
    | - 'Ikuti_Guru'  : Jam sebelum siswa masuk diserahkan pada penilaian guru mapel.
    */
    'jam_terlewat_terlambat' => env('PRESENSI_JAM_TERLEWAT', 'Alpha'),

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Surat Izin Masuk Kelas Siswa Terlambat
    |--------------------------------------------------------------------------
    | Format template nomor surat: {NOMOR}, {BULAN}, {TAHUN}
    | Contoh hasil: 001/IZIN-TLT/X/2026
    */
    'format_nomor_surat_terlambat' => '{NOMOR}/IZIN-TLT/{BULAN}/{TAHUN}',
    'kode_surat_terlambat'         => 'IZIN-TLT',
    'minimal_karakter_alasan'      => 5,
];
