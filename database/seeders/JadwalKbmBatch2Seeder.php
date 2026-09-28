<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Mapel;

class JadwalKbmBatch2Seeder extends Seeder
{
    public function run(): void
    {
        $ta = TahunAjaran::where('is_aktif', true)->first() ?? TahunAjaran::first();
        $idTa = $ta->id;

        $timesSeninKamis = [
            1  => ['07:00:00', '07:40:00'],
            2  => ['07:40:00', '08:20:00'],
            3  => ['08:20:00', '09:00:00'],
            4  => ['09:00:00', '09:40:00'],
            5  => ['10:00:00', '10:40:00'],
            6  => ['10:40:00', '11:20:00'],
            7  => ['11:20:00', '12:00:00'],
            8  => ['13:00:00', '13:40:00'],
            9  => ['13:40:00', '14:20:00'],
            10 => ['14:20:00', '15:00:00'],
        ];

        $timesJumat = [
            1  => ['07:00:00', '07:30:00'],
            2  => ['07:30:00', '08:00:00'],
            3  => ['08:00:00', '08:30:00'],
            4  => ['08:30:00', '09:00:00'],
            5  => ['09:00:00', '09:30:00'],
            6  => ['09:50:00', '10:20:00'],
            7  => ['10:20:00', '10:50:00'],
            8  => ['10:50:00', '11:20:00'],
            9  => ['13:00:00', '13:30:00'],
            10 => ['13:30:00', '14:00:00'],
            11 => ['14:00:00', '14:30:00'],
            12 => ['14:30:00', '15:00:00'],
            13 => ['15:00:00', '15:30:00'],
        ];

        $scheduleDefinition = [
            // ================= 1. X BD 1 =================
            'X BD 1' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Sejarah', 'guru' => 'Pipit Ambarwati, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'Dasar PM', 'guru' => 'Ratih Dian Irawati, SE'],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Arif Setyobudi, S.Pd'],
                    ['jams' => [10], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Dian Mawarti, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Sinta Lestari, S.Pd.I'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Arif Setyobudi, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Laili Ermawati, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Siti Munawaroh, S.Kom., M.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                    ['jams' => [5, 6, 7, 8], 'mapel' => 'Dasar PM', 'guru' => 'Anisa Kusumawati, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Agus Fahruddy, S.Pd., M.Pd'],
                    ['jams' => [8], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                    ['jams' => [9, 10], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Anisa Kusumawati, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Informatika', 'guru' => 'Eko Saputro, S.Pd'],
                    ['jams' => [6, 7, 8], 'mapel' => 'Matematika', 'guru' => 'Laili Ermawati, S.Pd'],
                    ['jams' => [9, 10, 11], 'mapel' => 'Dasar PM', 'guru' => 'Retno Widyastuti, S.Pd., M.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Bahasa Jawa', 'guru' => 'Rizki Putri Wulandari, S.Pd'],
                ],
            ],

            // ================= 2. X BD 2 =================
            'X BD 2' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                    ['jams' => [5, 6, 7, 8], 'mapel' => 'Dasar PM', 'guru' => 'Retno Widyastuti, S.Pd., M.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Anisa Kusumawati, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Jawa', 'guru' => 'Erna Qoriah, S.E.'],
                    ['jams' => [3, 4], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Siti Munawaroh, S.Kom., M.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'Matematika', 'guru' => 'Laili Ermawati, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar PM', 'guru' => 'Ratih Dian Irawati, SE'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Sinta Lestari, S.Pd.I'],
                    ['jams' => [4, 5, 6], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Informatika', 'guru' => 'Eko Saputro, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Dasar PM', 'guru' => 'Anisa Kusumawati, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Inggris', 'guru' => 'Dwi Rini Manfaati, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Sejarah', 'guru' => 'Pipit Ambarwati, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Arif Setyobudi, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Dian Mawarti, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => 'Dwi Rini Manfaati, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Agus Fahruddy, S.Pd., M.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Arif Setyobudi, S.Pd'],
                    ['jams' => [10, 11], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Matematika', 'guru' => 'Laili Ermawati, S.Pd'],
                ],
            ],

            // ================= 3. X BD 3 =================
            'X BD 3' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Fitria Diah Ayu Hartati, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Bella Prakoso, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Matematika', 'guru' => 'Eko Saputro, S.Pd'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Dasar PM', 'guru' => 'Retno Widyastuti, S.Pd., M.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Inggris', 'guru' => 'Dwi Rini Manfaati, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Siti Khoiriyah, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Dasar PM', 'guru' => 'Anisa Kusumawati, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Jawa', 'guru' => 'Muhammad Fajar Assidiqi, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Siti Khoiriyah, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Abdul Rohman, S.Pd'],
                    ['jams' => [3, 4, 5], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Inggris', 'guru' => 'Dwi Rini Manfaati, S.Pd'],
                    ['jams' => [10], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Niken Hari Pratiwi, S.Psi., M.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Dasar PM', 'guru' => 'Ratih Dian Irawati, SE'],
                    ['jams' => [6, 7, 8], 'mapel' => 'Informatika', 'guru' => 'Eko Saputro, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Eko Saputro, S.Pd'],
                    ['jams' => [11, 12, 13], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Siti Munawaroh, S.Kom., M.Pd'],
                ],
            ],

            // ================= 4. X MP 1 =================
            'X MP 1' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Sejarah', 'guru' => 'Fajar Luthfianto, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Purwati, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Dasar MPLB', 'guru' => 'Dwi Kuswanto, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Inggris', 'guru' => 'Komariyah, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Purwati, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Rulik Indrawati, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Inggris', 'guru' => 'Komariyah, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Jawa', 'guru' => 'Muhammad Fajar Assidiqi, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                    ['jams' => [8], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Veronica Damay Rulitasari, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Informatika', 'guru' => 'Kurnila Putri Islamawati, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Tutut Sriatin, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Matematika', 'guru' => 'Rulik Indrawati, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Kurnila Putri Islamawati, S.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar MPLB', 'guru' => 'Dwi Kuswanto, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'PJOK', 'guru' => 'Zainul Arifin, S.Pd'],
                    ['jams' => [5, 6, 7, 8], 'mapel' => 'Dasar MPLB', 'guru' => 'Dwi Kuswanto, S.Pd'],
                    ['jams' => [9, 10, 11], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Fitria Diah Ayu Hartati, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Informatika', 'guru' => 'Kurnila Putri Islamawati, S.Pd'],
                ],
            ],

            // ================= 5. X MP 2 =================
            'X MP 2' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Nishfu Laili, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Tutut Sriatin, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Jawa', 'guru' => 'Muhammad Fajar Assidiqi, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Inggris', 'guru' => 'Komariyah, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Sejarah', 'guru' => 'Fajar Luthfianto, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Inggris', 'guru' => 'Komariyah, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Matematika', 'guru' => 'Rulik Indrawati, S.Pd'],
                    ['jams' => [5, 6, 7, 8], 'mapel' => 'Dasar MPLB', 'guru' => 'Peni Wulandari, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Kurnila Putri Islamawati, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [3, 4, 5], 'mapel' => 'PJOK', 'guru' => 'Zainul Arifin, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Matematika', 'guru' => 'Rulik Indrawati, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Informatika', 'guru' => 'Ary Sunaryo, ST., M.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Purwati, S.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar MPLB', 'guru' => 'Peni Wulandari, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Fitria Diah Ayu Hartati, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Purwati, S.Pd'],
                    ['jams' => [10, 11, 12, 13], 'mapel' => 'Dasar MPLB', 'guru' => 'Peni Wulandari, S.Pd'],
                ],
            ],

            // ================= 6. X MP 3 =================
            'X MP 3' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'PJOK', 'guru' => 'Zainul Arifin, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'Bahasa Inggris', 'guru' => 'Komariyah, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar MPLB', 'guru' => 'Lilik Suratmi, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Arif Setyobudi, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => 'Komariyah, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Badrus Sulaiman, S.Pd.'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar MPLB', 'guru' => 'Lilik Suratmi, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Arif Setyobudi, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Matematika', 'guru' => 'Basuki Sarjono, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Fitria Diah Ayu Hartati, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                    ['jams' => [4, 5], 'mapel' => 'Matematika', 'guru' => 'Basuki Sarjono, S.Pd'],
                    ['jams' => [6], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Nur Eko Wahyuningsih, S.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Informatika', 'guru' => 'Badrus Sulaiman, S.Pd.'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Khuriyatul Kamila, S.Si'],
                    ['jams' => [4, 5], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Jawa', 'guru' => 'Rizki Putri Wulandari, S.Pd'],
                    ['jams' => [8, 9, 10, 11], 'mapel' => 'Dasar MPLB', 'guru' => 'Lilik Suratmi, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                ],
            ],

            // ================= 7. X MP 4 =================
            'X MP 4' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                    ['jams' => [5, 6, 7, 8], 'mapel' => 'Dasar MPLB', 'guru' => 'Rindang Rejeki, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Eko Saputro, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'PJOK', 'guru' => 'Zainul Arifin, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Arif Setyobudi, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Badrus Sulaiman, S.Pd.'],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Dasar MPLB', 'guru' => 'Rindang Rejeki, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Arif Setyobudi, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Sejarah', 'guru' => 'Fajar Luthfianto, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                    ['jams' => [3], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Siti Maisaroh, S.Pd'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Alfinu Farikh Abdillah, S.Pd.I'],
                    ['jams' => [7, 8], 'mapel' => 'Matematika', 'guru' => 'Eko Saputro, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Informatika', 'guru' => 'Badrus Sulaiman, S.Pd.'],
                    ['jams' => [6, 7], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Abdul Rohman, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Jawa', 'guru' => 'Rizki Putri Wulandari, S.Pd'],
                    ['jams' => [10, 11, 12, 13], 'mapel' => 'Dasar MPLB', 'guru' => 'Rindang Rejeki, S.Pd'],
                ],
            ],
        ];

        foreach ($scheduleDefinition as $className => $days) {
            $kelas = Kelas::where('nama_kelas', $className)
                ->orWhere('nama_kelas', 'like', "{$className}%")
                ->first();

            if (!$kelas) {
                echo "Warning: Kelas '{$className}' not found in database.\n";
                continue;
            }

            JadwalPelajaran::where('id_kelas', $kelas->id_kelas)
                ->where('id_tahun_ajaran', $idTa)
                ->forceDelete();

            foreach ($days as $hari => $slots) {
                $timeSlots = ($hari === 'Jumat') ? $timesJumat : $timesSeninKamis;

                foreach ($slots as $slot) {
                    $mapel = $this->resolveMapel($slot['mapel']);
                    $guru = $this->resolveGuru($slot['guru']);

                    if (!$mapel) {
                        echo "Warning: Mapel '{$slot['mapel']}' not found.\n";
                        continue;
                    }
                    if (!$guru) {
                        echo "Warning: Guru '{$slot['guru']}' not found.\n";
                        continue;
                    }

                    $firstJam = reset($slot['jams']);
                    $lastJam = end($slot['jams']);
                    $blockJamMulai = $timeSlots[$firstJam][0] ?? '07:00:00';
                    $blockJamSelesai = $timeSlots[$lastJam][1] ?? '15:00:00';

                    foreach ($slot['jams'] as $jamKe) {
                        JadwalPelajaran::create([
                            'id_tahun_ajaran' => $idTa,
                            'hari'            => $hari,
                            'jam_ke'          => $jamKe,
                            'jam_mulai'       => $blockJamMulai,
                            'jam_selesai'     => $blockJamSelesai,
                            'id_mapel'        => $mapel->id_mapel,
                            'id_guru'         => $guru->id_guru,
                            'id_kelas'        => $kelas->id_kelas,
                        ]);
                    }
                }
            }
            echo "Successfully seeded schedule for {$className} ({$kelas->nama_kelas}).\n";
        }
    }

    private function resolveMapel(string $name): ?Mapel
    {
        $clean = trim($name);
        if ($clean === 'BK' || $clean === 'Bimbingan Konseling') {
            return Mapel::where('nama_mapel', 'like', '%Bimbingan%')->first();
        }
        if (stripos($clean, 'Dasar PM') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar%Pemasaran%')->first() 
                ?? Mapel::where('nama_mapel', 'like', '%Dasar PM%')->first();
        }
        if (stripos($clean, 'Dasar MPLB') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar MPLB%')->first();
        }
        if (stripos($clean, 'Agama Islam') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Pendidikan Agama Islam%')->first();
        }

        return Mapel::where('nama_mapel', $clean)
            ->orWhere('nama_mapel', 'like', "%{$clean}%")
            ->first();
    }

    private function resolveGuru(string $name): ?Guru
    {
        if (stripos($name, 'Muto') !== false) {
            return Guru::where('nama_lengkap', 'like', '%Muto%')->first();
        }
        if (stripos($name, 'Elysa') !== false) {
            return Guru::where('nama_lengkap', 'like', '%Elysa%')->first();
        }
        if (stripos($name, 'Yani, S.Pd') !== false) {
            return Guru::where('nama_lengkap', 'like', 'Yani, S.Pd%')->first();
        }

        $clean = trim(preg_replace('/[,\.\']/', ' ', $name));
        $words = array_values(array_filter(explode(' ', $clean), fn($w) => strlen($w) > 2));

        $q = Guru::query();
        foreach ($words as $w) {
            $q->where('nama_lengkap', 'like', "%{$w}%");
        }
        $g = $q->first();
        if ($g) return $g;

        if (count($words) >= 2) {
            $g = Guru::where('nama_lengkap', 'like', "%{$words[0]}%")
                ->where('nama_lengkap', 'like', "%{$words[1]}%")
                ->first();
            if ($g) return $g;
        }

        return Guru::where('nama_lengkap', 'like', "%{$words[0]}%")->first();
    }
}
