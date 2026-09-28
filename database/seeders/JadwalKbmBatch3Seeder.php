<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Mapel;

class JadwalKbmBatch3Seeder extends Seeder
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
            // ================= 1. X AK 1 =================
            'X AK 1' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Matematika', 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                    ['jams' => [6], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Nur Eko Wahyuningsih, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Sejarah', 'guru' => 'Fajar Luthfianto, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Informatika', 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Dasar AKL', 'guru' => 'Pipit Ambarwati, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Informatika', 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'PJOK', 'guru' => 'Agus Fahruddy, S.Pd., M.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Endang Safitri, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Dasar AKL', 'guru' => 'Pipit Ambarwati, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Endang Safitri, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Jawa', 'guru' => 'Fitri Amaliyah, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Dasar AKL', 'guru' => 'Atih Wilupi, S.E, M.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Yuli Ratnasari, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Alfinu Farikh Abdillah, S.Pd.I'],
                    ['jams' => [7, 8, 9], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                    ['jams' => [10, 11, 12, 13], 'mapel' => 'Dasar AKL', 'guru' => 'Agustina Mardika Rini, S.Pd., M.Pd'],
                ],
            ],

            // ================= 2. X AK 2 =================
            'X AK 2' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'Dasar AKL', 'guru' => 'Indayah, S.Pd., M.Pd'],
                    ['jams' => [8], 'mapel' => 'Informatika', 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Endang Safitri, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Informatika', 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Agus Fahruddy, S.Pd., M.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Matematika', 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.'],
                    ['jams' => [10], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Nur Eko Wahyuningsih, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Alfinu Farikh Abdillah, S.Pd.I'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar AKL', 'guru' => 'Agustina Mardika Rini, S.Pd., M.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Dasar AKL', 'guru' => 'Indayah, S.Pd., M.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Matematika', 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.'],
                    ['jams' => [6, 7], 'mapel' => 'IPAS', 'guru' => 'Sri Subekti, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Yuli Ratnasari, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Endang Safitri, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Jawa', 'guru' => 'Fitri Amaliyah, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [10, 11], 'mapel' => 'Dasar AKL', 'guru' => 'Pipit Ambarwati, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Sejarah', 'guru' => 'Fajar Luthfianto, S.Pd'],
                ],
            ],

            // ================= 3. X AK 3 =================
            'X AK 3' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'PJOK', 'guru' => 'Agus Fahruddy, S.Pd., M.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Dasar AKL', 'guru' => 'Pipit Ambarwati, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Ruly Dwi Setyaningrum, S.Kom'],
                    ['jams' => [9, 10], 'mapel' => 'Informatika', 'guru' => 'Ary Sunaryo, ST., M.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Informatika', 'guru' => 'Ary Sunaryo, ST., M.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Abdul Rohman, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Endang Safitri, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'guru' => 'Andri Retno Yuli Astuti, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Alfinu Farikh Abdillah, S.Pd.I'],
                    ['jams' => [4, 5], 'mapel' => 'Matematika', 'guru' => 'Dwi Nowa Setyandari, S.Pd'],
                    ['jams' => [6], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Astra Bella Flamboyan, S.Psi'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar AKL', 'guru' => 'Titin Sukmasari, S.Pd., M.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Sejarah', 'guru' => 'Pipit Ambarwati, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Jawa', 'guru' => 'Rizki Putri Wulandari, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'Dasar AKL', 'guru' => 'Setiyo Winarko, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Inggris', 'guru' => 'Andri Retno Yuli Astuti, S.Pd'],
                    ['jams' => [7, 8, 9], 'mapel' => 'Dasar AKL', 'guru' => 'Setiyo Winarko, S.Pd'],
                    ['jams' => [10, 11], 'mapel' => 'Matematika', 'guru' => 'Dwi Nowa Setyandari, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Endang Safitri, S.Pd'],
                ],
            ],

            // ================= 4. X AK 4 =================
            'X AK 4' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [4], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Astra Bella Flamboyan, S.Psi'],
                    ['jams' => [5, 6, 7], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Muashofah, M.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Sejarah', 'guru' => 'Pipit Ambarwati, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Informatika', 'guru' => 'Ary Sunaryo, ST., M.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Matematika', 'guru' => 'Dwi Nowa Setyandari, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Inggris', 'guru' => 'Andri Retno Yuli Astuti, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Dasar AKL', 'guru' => 'Pipit Ambarwati, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Dasar AKL', 'guru' => 'Septiani, S.Pd., M.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Jawa', 'guru' => 'Rizki Putri Wulandari, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Siti Khoiriyah, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'PJOK', 'guru' => 'Agus Fahruddy, S.Pd., M.Pd'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Dasar AKL', 'guru' => 'Septiani, S.Pd., M.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Inggris', 'guru' => 'Andri Retno Yuli Astuti, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Abdul Rohman, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Informatika', 'guru' => 'Ary Sunaryo, ST., M.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Matematika', 'guru' => 'Dwi Nowa Setyandari, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Siti Khoiriyah, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Ruly Dwi Setyaningrum, S.Kom'],
                    ['jams' => [10, 11, 12, 13], 'mapel' => 'Dasar AKL', 'guru' => 'Titin Sukmasari, S.Pd., M.Pd'],
                ],
            ],

            // ================= 5. X ULW =================
            'X ULW' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [5, 6, 7, 8], 'mapel' => 'Informatika', 'guru' => 'Siswanti Purwaningsih, S.T., M.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Jawa', 'guru' => 'Laili Ermawati, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Mufatiroh, S.Ag'],
                    ['jams' => [4, 5, 6], 'mapel' => 'PJOK', 'guru' => 'Bella Prakoso, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Dasar ULP', 'guru' => 'Risqi Nur Imama, S.Tr.Par'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Sri Rahayu, S.Pd'],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Dasar ULP', 'guru' => 'Risqi Nur Imama, S.Tr.Par'],
                    ['jams' => [7, 8], 'mapel' => 'Matematika', 'guru' => 'Dwi Nowa Setyandari, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Sri Rahayu, S.Pd'],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Dasar ULP', 'guru' => 'Risqi Nur Imama, S.Tr.Par'],
                    ['jams' => [7, 8], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Wiwik Yuniarsih, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Sejarah', 'guru' => 'Fajar Luthfianto, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Niken Hari Pratiwi, S.Psi., M.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Dasar ULP', 'guru' => 'Risqi Nur Imama, S.Tr.Par'],
                    ['jams' => [5, 6], 'mapel' => 'Seni Budaya', 'guru' => 'Angga Widhy Wirawan, S.Pd., M.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom'],
                    ['jams' => [9, 10, 11], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Matematika', 'guru' => 'Dwi Nowa Setyandari, S.Pd'],
                ],
            ],

            // ================= 6. X DKV 1 =================
            'X DKV 1' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Dra. Hanik Pangestuti'],
                    ['jams' => [5, 6, 7], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar DKV', 'guru' => 'Danang Anjar Hymawanto, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Nur Eko Wahyuningsih, S.Pd'],
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fajar Wahyu Pratiwi, S.S'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Dasar DKV', 'guru' => 'Agus Pramono, S.Sn'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Jawa', 'guru' => 'Khoyrotun Hisani, S.Sn'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Erna Rinawati, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Matematika', 'guru' => 'Rulik Indrawati, S.Pd'],
                    ['jams' => [3, 4, 5], 'mapel' => 'PJOK', 'guru' => 'Ilham Sungeidi, S.Pd'],
                    ['jams' => [6, 7, 8], 'mapel' => 'Dasar DKV', 'guru' => 'Khoyrotun Hisani, S.Sn'],
                    ['jams' => [9, 10], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Endik Kuswantoro, S.Kom., M.T'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Informatika', 'guru' => "Elysa Yuli Nur'aini, S.Si"],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fajar Wahyu Pratiwi, S.S'],
                    ['jams' => [5, 6], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Wiwik Yuniarsih, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Dasar DKV', 'guru' => 'Agus Pramono, S.Sn'],
                    ['jams' => [5, 6], 'mapel' => 'Matematika', 'guru' => 'Rulik Indrawati, S.Pd'],
                    ['jams' => [7, 8, 9], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                    ['jams' => [10, 11], 'mapel' => 'Informatika', 'guru' => "Elysa Yuli Nur'aini, S.Si"],
                    ['jams' => [12, 13], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Erna Rinawati, S.Pd'],
                ],
            ],

            // ================= 7. X DKV 2 =================
            'X DKV 2' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Endik Kuswantoro, S.Kom., M.T'],
                    ['jams' => [4, 5], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [6, 7, 8], 'mapel' => 'Dasar DKV', 'guru' => 'Agus Pramono, S.Sn'],
                    ['jams' => [9, 10], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Wiwik Yuniarsih, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'IPAS', 'guru' => 'Fitri Amaliyah, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Erna Rinawati, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fajar Wahyu Pratiwi, S.S'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Dra. Hanik Pangestuti'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Informatika', 'guru' => "Elysa Yuli Nur'aini, S.Si"],
                    ['jams' => [3], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Yuni Jiastuti, S.Pd'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Dasar DKV', 'guru' => 'Agus Pramono, S.Sn'],
                    ['jams' => [7, 8], 'mapel' => 'Matematika', 'guru' => 'Ajeng Okvitasari, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Erna Rinawati, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fajar Wahyu Pratiwi, S.S'],
                    ['jams' => [4, 5], 'mapel' => 'PJOK', 'guru' => 'Ilham Sungeidi, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Informatika', 'guru' => "Elysa Yuli Nur'aini, S.Si"],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar DKV', 'guru' => 'Danang Anjar Hymawanto, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Jawa', 'guru' => 'Khoyrotun Hisani, S.Sn'],
                    ['jams' => [6, 7, 8], 'mapel' => 'Dasar DKV', 'guru' => 'Khoyrotun Hisani, S.Sn'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Ajeng Okvitasari, S.Pd'],
                    ['jams' => [11, 12, 13], 'mapel' => 'IPAS', 'guru' => 'Fitri Amaliyah, S.Pd'],
                ],
            ],

            // ================= 8. X PSPT 1 =================
            'X PSPT 1' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'PJOK', 'guru' => 'Bella Prakoso, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Abdul Rohman, S.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar PSPT', 'guru' => 'Benny Mamora, S.Kom'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Dasar PSPT', 'guru' => 'Benny Mamora, S.Kom'],
                    ['jams' => [5, 6], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Matematika', 'guru' => 'Eko Saputro, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Winartin, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Dra. Hanik Pangestuti'],
                    ['jams' => [4, 5, 6], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Winartin, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Elyana Frisca Monica, S.Pd'],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Informatika', 'guru' => 'Elyana Frisca Monica, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Jawa', 'guru' => 'Khoyrotun Hisani, S.Sn'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Eko Saputro, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                    ['jams' => [6, 7], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [8], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Winarsih, S.Pd, M.Pd'],
                    ['jams' => [9, 10, 11, 12, 13], 'mapel' => 'Dasar PSPT', 'guru' => 'Benny Mamora, S.Kom'],
                ],
            ],

            // ================= 9. X PSPT 2 =================
            'X PSPT 2' => [
                'Senin' => [
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Informatika', 'guru' => 'Elyana Frisca Monica, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'IPAS', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [6], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Winarsih, S.Pd, M.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar PSPT', 'guru' => "Sa'ad Wazis Hiedayat, S.Pd"],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Dasar PSPT', 'guru' => "Sa'ad Wazis Hiedayat, S.Pd"],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Bella Prakoso, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Dra. Hanik Pangestuti'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Umi Kulsum, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Matematika', 'guru' => 'Laili Ermawati, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Abdul Rohman, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Jawa', 'guru' => 'Muhammad Fajar Assidiqi, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Elyana Frisca Monica, S.Pd'],
                    ['jams' => [4, 5, 6, 7], 'mapel' => 'Dasar PSPT', 'guru' => "Sa'ad Wazis Hiedayat, S.Pd"],
                    ['jams' => [8, 9], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [10, 11], 'mapel' => 'Matematika', 'guru' => 'Laili Ermawati, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Umi Kulsum, S.Pd'],
                ],
            ],

            // ================= 10. X AN 1 =================
            'X AN 1' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [6], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Siti Maisaroh, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Siti Khoiriyah, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'guru' => 'Agus Muharyanto, M.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Muashofah, M.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Siti Khoiriyah, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Informatika', 'guru' => 'Tuhu Eries Kudori, S.Sn'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar Animasi', 'guru' => 'Rika Okta Maulida, S.Ds.'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Dasar Animasi', 'guru' => 'Dhuana Putri Puspitasary, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Mega Mahardika, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Matematika', 'guru' => 'Arvia Rienetasary, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Inggris', 'guru' => 'Agus Muharyanto, M.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Matematika', 'guru' => 'Arvia Rienetasary, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Bella Prakoso, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar Animasi', 'guru' => 'Dhuana Putri Puspitasary, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Jawa', 'guru' => 'Muhammad Fajar Assidiqi, S.Pd'],
                    ['jams' => [7, 8, 9], 'mapel' => 'Dasar Animasi', 'guru' => 'Rika Okta Maulida, S.Ds.'],
                    ['jams' => [10, 11], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Endik Kuswantoro, S.Kom., M.T'],
                    ['jams' => [12, 13], 'mapel' => 'Informatika', 'guru' => 'Tuhu Eries Kudori, S.Sn'],
                ],
            ],

            // ================= 11. X AN 2 =================
            'X AN 2' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Muashofah, M.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Endik Kuswantoro, S.Kom., M.T'],
                    ['jams' => [7, 8], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Ajeng Okvitasari, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'PJOK', 'guru' => 'Bella Prakoso, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Matematika', 'guru' => 'Ajeng Okvitasari, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Mega Mahardika, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Inggris', 'guru' => 'Agus Muharyanto, M.Pd'],
                    ['jams' => [10], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Siti Maisaroh, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Siti Khoiriyah, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar Animasi', 'guru' => 'Rika Okta Maulida, S.Ds.'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Siti Khoiriyah, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => 'Agus Muharyanto, M.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'Dasar Animasi', 'guru' => 'Dhuana Putri Puspitasary, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar Animasi', 'guru' => 'Rika Okta Maulida, S.Ds.'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Jawa', 'guru' => 'Muhammad Fajar Assidiqi, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Informatika', 'guru' => 'Rika Okta Maulida, S.Ds.'],
                    ['jams' => [6, 7], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar Animasi', 'guru' => 'Dhuana Putri Puspitasary, S.Pd'],
                    ['jams' => [11, 12, 13], 'mapel' => 'Informatika', 'guru' => 'Rika Okta Maulida, S.Ds.'],
                ],
            ],
        ];

        foreach ($scheduleDefinition as $className => $days) {
            $alias = $className;
            if (strpos($className, 'X AK') !== false) {
                $alias = str_replace('X AK', 'X AKL', $className);
            }

            $kelas = Kelas::where('nama_kelas', $className)
                ->orWhere('nama_kelas', $alias)
                ->orWhere('nama_kelas', 'like', "{$className}%")
                ->orWhere('nama_kelas', 'like', "{$alias}%")
                ->first();

            if (!$kelas) {
                echo "Warning: Kelas '{$className}' / '{$alias}' not found in database.\n";
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
        if (stripos($clean, 'Dasar AKL') !== false || stripos($clean, 'Dasar AK') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar AKL%')->first();
        }
        if (stripos($clean, 'Dasar ULP') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar ULP%')->first();
        }
        if (stripos($clean, 'Dasar DKV') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar DKV%')->first();
        }
        if (stripos($clean, 'Dasar PSPT') !== false || stripos($clean, 'Dasar BP') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar PSPT%')->first();
        }
        if (stripos($clean, 'Dasar Animasi') !== false || stripos($clean, 'Dasar AN') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar Animasi%')->first();
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
        if (stripos($name, 'Benny Mamora') !== false) {
            return Guru::where('nama_lengkap', 'like', '%Benny%')->first();
        }
        if (stripos($name, 'Danang Anjar') !== false) {
            return Guru::where('nama_lengkap', 'like', '%Danang%')->first();
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
