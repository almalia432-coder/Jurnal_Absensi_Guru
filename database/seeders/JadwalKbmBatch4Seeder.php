<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Mapel;

class JadwalKbmBatch4Seeder extends Seeder
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
        ];

        $scheduleDefinition = [
            // ================= XI TKI 1 =================
            'XI TKI 1' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [3, 4, 5], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Ayu Pusposorini, ST', 'id_guru' => 70],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Diana Hartanti, S.T., M.Pd', 'id_guru' => 67],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Rizki Putri Wulandari, S.Pd', 'id_guru' => 134],
                    ['jams' => [2, 3, 4], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                    ['jams' => [5, 6], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Basuki Sarjono, S.Pd', 'id_guru' => 50],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Yustin Febrini, S.Pd', 'id_guru' => 125],
                    ['jams' => [2, 3], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Rifkotin Na\'imah, S.Pd', 'id_guru' => 114],
                    ['jams' => [4, 5, 6], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [7], 'mapel' => 'Mapel Pilihan TKI', 'id_mapel' => 17, 'guru' => 'Sri Kusumastuti, S.Pd', 'id_guru' => 105],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Sri Kusumastuti, S.Pd', 'id_guru' => 105],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Yani, S.Pd.', 'id_guru' => 37],
                    ['jams' => [3, 4, 5], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Ilham Sungeidi, S.Pd', 'id_guru' => 60],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Mufatiroh, S.Ag', 'id_guru' => 132],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Ayu Pusposorini, ST', 'id_guru' => 70],
                    ['jams' => [4, 5, 6, 7], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Diana Hartanti, S.T., M.Pd', 'id_guru' => 67],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Yuni Jiastuti, S.Pd', 'id_guru' => 128],
                ],
            ],
            // ================= XI TKI 2 =================
            'XI TKI 2' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Ilham Sungeidi, S.Pd', 'id_guru' => 60],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Basuki Sarjono, S.Pd', 'id_guru' => 50],
                    ['jams' => [7], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Rizki Putri Wulandari, S.Pd', 'id_guru' => 134],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Komariyah, S.Pd', 'id_guru' => 56],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [2, 3, 4], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [5], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Rifkotin Na\'imah, S.Pd', 'id_guru' => 114],
                    ['jams' => [6, 7], 'mapel' => 'Mapel Pilihan TKI', 'id_mapel' => 17, 'guru' => 'Sri Kusumastuti, S.Pd', 'id_guru' => 105],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Sri Kusumastuti, S.Pd', 'id_guru' => 105],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Yani, S.Pd.', 'id_guru' => 37],
                    ['jams' => [4], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Ayu Pusposorini, ST', 'id_guru' => 70],
                    ['jams' => [5, 6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Diana Hartanti, S.T., M.Pd', 'id_guru' => 67],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Mufatiroh, S.Ag', 'id_guru' => 132],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Diana Hartanti, S.T., M.Pd', 'id_guru' => 67],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi TKI', 'id_mapel' => 16, 'guru' => 'Ayu Pusposorini, ST', 'id_guru' => 70],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [7], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Komariyah, S.Pd', 'id_guru' => 56],
                    ['jams' => [8, 9], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Yuni Jiastuti, S.Pd', 'id_guru' => 128],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Yustin Febrini, S.Pd', 'id_guru' => 125],
                ],
            ],
            // ================= XI RPL 1 =================
            'XI RPL 1' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Mufatiroh, S.Ag', 'id_guru' => 132],
                    ['jams' => [5], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Widodo, S.Pd', 'id_guru' => 31],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Kurnila Putri Islamawati, S.Pd', 'id_guru' => 2],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Kurnila Putri Islamawati, S.Pd', 'id_guru' => 2],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom', 'id_guru' => 11],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Anisa Kusumawati, S.Pd', 'id_guru' => 12],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom', 'id_guru' => 11],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Isti Mufadah, S.Pd', 'id_guru' => 14],
                    ['jams' => [7], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Laili Ermawati, S.Pd', 'id_guru' => 15],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Shinta Indyar Shanty Susanto, S.Kom', 'id_guru' => 11],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Mapel Pilihan RPL', 'id_mapel' => 23, 'guru' => 'Hendro Suwignyo, ST', 'id_guru' => 16],
                    ['jams' => [6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Zainul Arifin, S.Pd', 'id_guru' => 33],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Umi Kulsum, S.Pd', 'id_guru' => 13],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Anisa Kusumawati, S.Pd', 'id_guru' => 12],
                    ['jams' => [3, 4], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                    ['jams' => [5, 6, 7], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [8], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Isti Mufadah, S.Pd', 'id_guru' => 14],
                    ['jams' => [9, 10, 11, 12], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.', 'id_guru' => 18],
                ],
            ],
            // ================= XI RPL 2 =================
            'XI RPL 2' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Anisa Kusumawati, S.Pd', 'id_guru' => 12],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fajar Wahyu Pratiwi, S.S', 'id_guru' => 103],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Badrus Sulaiman, S.Pd.', 'id_guru' => 91],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.', 'id_guru' => 18],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fajar Wahyu Pratiwi, S.S', 'id_guru' => 103],
                    ['jams' => [6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Zainul Arifin, S.Pd', 'id_guru' => 33],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Badrus Sulaiman, S.Pd.', 'id_guru' => 91],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Kurnila Putri Islamawati, S.Pd', 'id_guru' => 2],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Mapel Pilihan RPL', 'id_mapel' => 23, 'guru' => 'Hendro Suwignyo, ST', 'id_guru' => 16],
                    ['jams' => [7], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                    ['jams' => [8, 9, 10], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [2, 3, 4], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Widodo, S.Pd', 'id_guru' => 31],
                    ['jams' => [5], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Laili Ermawati, S.Pd', 'id_guru' => 15],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Kurnila Putri Islamawati, S.Pd', 'id_guru' => 2],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Winartin, S.Pd', 'id_guru' => 30],
                    ['jams' => [4, 5, 6, 7], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Mufatiroh, S.Ag', 'id_guru' => 132],
                    ['jams' => [8], 'mapel' => 'Konsentrasi RPL', 'id_mapel' => 22, 'guru' => 'Badrus Sulaiman, S.Pd.', 'id_guru' => 91],
                    ['jams' => [9, 10, 11, 12], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Anisa Kusumawati, S.Pd', 'id_guru' => 12],
                ],
            ],
            // ================= XI TKJ 1 =================
            'XI TKJ 1' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [4], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Sri Rahayu, S.Pd', 'id_guru' => 25],
                    ['jams' => [5, 6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Siti Munawaroh, S.Kom.,M.Pd', 'id_guru' => 65],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Isti Mufadah, S.Pd', 'id_guru' => 14],
                    ['jams' => [2, 3, 4], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Niken Dewi Hastika, S.Pd', 'id_guru' => 111],
                    ['jams' => [5, 6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Ilham Sungeidi, S.Pd', 'id_guru' => 60],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Siswanti Purwaningsih, S.T., M.Pd', 'id_guru' => 69],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Isti Mufadah, S.Pd', 'id_guru' => 14],
                    ['jams' => [4, 5, 6], 'mapel' => 'Mapel Pilihan TKJ', 'id_mapel' => 20, 'guru' => 'Listyana Hartati, S.Kom.,M.Pd', 'id_guru' => 78],
                    ['jams' => [7], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Endang Ary Handayani, S.T., M.Pd', 'id_guru' => 52],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Dra. Hanik Pangestuti', 'id_guru' => 26],
                    ['jams' => [3, 4], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Nishfu Laili, S.Pd', 'id_guru' => 121],
                    ['jams' => [5, 6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Listyana Hartati, S.Kom.,M.Pd', 'id_guru' => 78],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.', 'id_guru' => 18],
                    ['jams' => [4, 5, 6, 7, 8], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Ary Sunaryo, ST.,M.Pd', 'id_guru' => 77],
                    ['jams' => [9], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Fitri Amaliyah, S.Pd', 'id_guru' => 110],
                    ['jams' => [10, 11, 12], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Niken Dewi Hastika, S.Pd', 'id_guru' => 111],
                ],
            ],
            // ================= XI TKJ 2 =================
            'XI TKJ 2' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                    ['jams' => [3, 4, 5], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Endang Ary Handayani, S.T., M.Pd', 'id_guru' => 52],
                    ['jams' => [6, 7], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Dra. Hanik Pangestuti', 'id_guru' => 26],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Isti Mufadah, S.Pd', 'id_guru' => 14],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Elysa Yuli Nur\'aini, S.Si', 'id_guru' => 71],
                    ['jams' => [3, 4], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                    ['jams' => [5, 6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Listyana Hartati, S.Kom.,M.Pd', 'id_guru' => 78],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Ilham Sungeidi, S.Pd', 'id_guru' => 60],
                    ['jams' => [2, 3, 4], 'mapel' => 'Mapel Pilihan TKJ', 'id_mapel' => 20, 'guru' => 'Listyana Hartati, S.Kom.,M.Pd', 'id_guru' => 78],
                    ['jams' => [5], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Nishfu Laili, S.Pd', 'id_guru' => 121],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Ary Sunaryo, ST.,M.Pd', 'id_guru' => 77],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Baskoro, S.Si', 'id_guru' => 93],
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Isti Mufadah, S.Pd', 'id_guru' => 14],
                    ['jams' => [4, 5, 6], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Siswanti Purwaningsih, S.T., M.Pd', 'id_guru' => 69],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Fitri Amaliyah, S.Pd', 'id_guru' => 110],
                    ['jams' => [3, 4, 5], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Sri Rahayu, S.Pd', 'id_guru' => 25],
                    ['jams' => [6, 7, 8, 9], 'mapel' => 'Konsentrasi TKJ', 'id_mapel' => 19, 'guru' => 'Siti Munawaroh, S.Kom.,M.Pd', 'id_guru' => 65],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                ],
            ],
            // ================= XI BD 1 =================
            'XI BD 1' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Muhammad Fajar Assidiqi, S.Pd', 'id_guru' => 127],
                    ['jams' => [3, 4, 5], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Mapel Pilihan BD', 'id_mapel' => 26, 'guru' => 'Andri Krisdianto, SE.,M.Pd', 'id_guru' => 76],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Abdul Rohman, S.Pd', 'id_guru' => 133],
                    ['jams' => [2, 3, 4], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Dwi Rini Manfaati, S.Pd', 'id_guru' => 40],
                    ['jams' => [5, 6], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Nurul Azizah, S.Pd', 'id_guru' => 92],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Sinta Lestari, S.Pd.I', 'id_guru' => 115],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Arvia Rienetasary, S.Pd', 'id_guru' => 41],
                    ['jams' => [6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Agus Fahruddy, S.Pd., M.Pd', 'id_guru' => 61],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Niken Dewi Hastika, S.Pd', 'id_guru' => 111],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Nur Eko Wahyuningsih, S.Pd', 'id_guru' => 101],
                    ['jams' => [2, 3, 4], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Andri Krisdianto, SE.,M.Pd', 'id_guru' => 76],
                    ['jams' => [5, 6], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Agung Yulianto, S.Pd', 'id_guru' => 104],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Niken Dewi Hastika, S.Pd', 'id_guru' => 111],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Nurul Azizah, S.Pd', 'id_guru' => 92],
                    ['jams' => [3, 4], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                    ['jams' => [5, 6, 7], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Erna Rinawati, S.Pd', 'id_guru' => 44],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Dwi Rini Manfaati, S.Pd', 'id_guru' => 40],
                ],
            ],
            // ================= XI BD 2 =================
            'XI BD 2' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Agung Yulianto, S.Pd', 'id_guru' => 104],
                    ['jams' => [5, 6], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Erna Rinawati, S.Pd', 'id_guru' => 44],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Arvia Rienetasary, S.Pd', 'id_guru' => 41],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Mapel Pilihan BD', 'id_mapel' => 26, 'guru' => 'Andri Krisdianto, SE.,M.Pd', 'id_guru' => 76],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Nurul Azizah, S.Pd', 'id_guru' => 92],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Agus Fahruddy, S.Pd., M.Pd', 'id_guru' => 61],
                    ['jams' => [2, 3], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Niken Dewi Hastika, S.Pd', 'id_guru' => 111],
                    ['jams' => [4, 5, 6], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                    ['jams' => [7], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Andri Krisdianto, SE.,M.Pd', 'id_guru' => 76],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Dwi Rini Manfaati, S.Pd', 'id_guru' => 40],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                    ['jams' => [3, 4, 5], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Niken Dewi Hastika, S.Pd', 'id_guru' => 111],
                    ['jams' => [6, 7], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Abdul Rohman, S.Pd', 'id_guru' => 133],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Muhammad Fajar Assidiqi, S.Pd', 'id_guru' => 127],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                    ['jams' => [3, 4, 5], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Sinta Lestari, S.Pd.I', 'id_guru' => 115],
                    ['jams' => [6, 7, 8], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Dwi Rini Manfaati, S.Pd', 'id_guru' => 40],
                    ['jams' => [9], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Dian Mawarti, S.Pd', 'id_guru' => 63],
                    ['jams' => [10, 11, 12], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                ],
            ],
            // ================= XI BD 3 =================
            'XI BD 3' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Mapel Pilihan BD', 'id_mapel' => 26, 'guru' => 'Niken Dewi Hastika, S.Pd', 'id_guru' => 111],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Dian Mawarti, S.Pd', 'id_guru' => 63],
                    ['jams' => [2, 3, 4], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                    ['jams' => [5, 6, 7], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Agung Yulianto, S.Pd', 'id_guru' => 104],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Niken Dewi Hastika, S.Pd', 'id_guru' => 111],
                    ['jams' => [2, 3, 4], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                    ['jams' => [5, 6], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Nurul Azizah, S.Pd', 'id_guru' => 92],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Umi Kulsum, S.Pd', 'id_guru' => 13],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Dwi Rini Manfaati, S.Pd', 'id_guru' => 40],
                    ['jams' => [2, 3, 4], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Bella Prakoso, S.Pd', 'id_guru' => 124],
                    ['jams' => [5, 6], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Ajeng Okvitasari, S.Pd', 'id_guru' => 120],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Nurul Azizah, S.Pd', 'id_guru' => 92],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Fitria Diah Ayu Hartati, S.Pd', 'id_guru' => 117],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Dwi Rini Manfaati, S.Pd', 'id_guru' => 40],
                    ['jams' => [7], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Anisa Kusumawati, S.Pd', 'id_guru' => 12],
                    ['jams' => [8, 9], 'mapel' => 'Konsentrasi BD', 'id_mapel' => 25, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                    ['jams' => [10, 11, 12], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Nurul Azizah, S.Pd', 'id_guru' => 92],
                ],
            ],
            // ================= XI MP 1 =================
            'XI MP 1' => [
                'Senin' => [
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Martiin, S.Pd', 'id_guru' => 36],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Purwati, S.Pd', 'id_guru' => 53],
                    ['jams' => [8, 9, 10], 'mapel' => 'Mapel Pilihan MOP', 'id_mapel' => 32, 'guru' => 'Tutut Sriatin, S.Pd', 'id_guru' => 107],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Martiin, S.Pd', 'id_guru' => 36],
                    ['jams' => [3, 4], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Martiin, S.Pd', 'id_guru' => 36],
                    ['jams' => [5, 6, 7], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Fitria Diah Ayu Hartati, S.Pd', 'id_guru' => 117],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Komariyah, S.Pd', 'id_guru' => 56],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Dra. Susakti Yuharini', 'id_guru' => 118],
                    ['jams' => [2, 3, 4, 5, 6], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Martiin, S.Pd', 'id_guru' => 36],
                    ['jams' => [7], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Abdul Rohman, S.Pd', 'id_guru' => 133],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Zainul Arifin, S.Pd', 'id_guru' => 33],
                    ['jams' => [2, 3, 4], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [5, 6], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Dra. Susakti Yuharini', 'id_guru' => 118],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Laili Ermawati, S.Pd', 'id_guru' => 15],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Martiin, S.Pd', 'id_guru' => 36],
                    ['jams' => [4, 5], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Martiin, S.Pd', 'id_guru' => 36],
                    ['jams' => [6, 7, 8], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Veronica Damay Rulitasari, S.Pd', 'id_guru' => 94],
                    ['jams' => [9], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Komariyah, S.Pd', 'id_guru' => 56],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Muhammad Fajar Assidiqi, S.Pd', 'id_guru' => 127],
                ],
            ],
            // ================= XI MP 2 =================
            'XI MP 2' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Dra. Susakti Yuharini', 'id_guru' => 118],
                    ['jams' => [3, 4, 5], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Laili Ermawati, S.Pd', 'id_guru' => 15],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Sunarti, S.Pd', 'id_guru' => 45],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Fitria Diah Ayu Hartati, S.Pd', 'id_guru' => 117],
                    ['jams' => [2, 3, 4, 5, 6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Zainul Arifin, S.Pd', 'id_guru' => 33],
                    ['jams' => [7], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Sunarti, S.Pd', 'id_guru' => 45],
                    ['jams' => [8, 9, 10], 'mapel' => 'Mapel Pilihan MOP', 'id_mapel' => 32, 'guru' => 'Tutut Sriatin, S.Pd', 'id_guru' => 107],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Sunarti, S.Pd', 'id_guru' => 45],
                    ['jams' => [3], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [4, 5, 6], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Abdul Rohman, S.Pd', 'id_guru' => 133],
                    ['jams' => [7], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Sunarti, S.Pd', 'id_guru' => 45],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Komariyah, S.Pd', 'id_guru' => 56],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Dra. Susakti Yuharini', 'id_guru' => 118],
                    ['jams' => [3, 4, 5], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Muhammad Fajar Assidiqi, S.Pd', 'id_guru' => 127],
                    ['jams' => [6], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Sunarti, S.Pd', 'id_guru' => 45],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Purwati, S.Pd', 'id_guru' => 53],
                    ['jams' => [4, 5, 6], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Komariyah, S.Pd', 'id_guru' => 56],
                    ['jams' => [7, 8, 9], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Sunarti, S.Pd', 'id_guru' => 45],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Siti Maisaroh, S.Pd', 'id_guru' => 126],
                ],
            ],
            // ================= XI MP 3 =================
            'XI MP 3' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Rulik Indrawati, S.Pd', 'id_guru' => 48],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Titik Samsistini, S.Pd', 'id_guru' => 51],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Dra. Susakti Yuharini', 'id_guru' => 118],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Endang Safitri, S.Pd', 'id_guru' => 131],
                    ['jams' => [7], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Titik Samsistini, S.Pd', 'id_guru' => 51],
                    ['jams' => [8, 9, 10], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Titik Samsistini, S.Pd', 'id_guru' => 51],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Zainul Arifin, S.Pd', 'id_guru' => 33],
                    ['jams' => [2, 3, 4, 5, 6], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Titik Samsistini, S.Pd', 'id_guru' => 51],
                    ['jams' => [7], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Andri Retno Yuli Astuti, S.Pd', 'id_guru' => 68],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Abdul Rohman, S.Pd', 'id_guru' => 133],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Fitria Diah Ayu Hartati, S.Pd', 'id_guru' => 117],
                    ['jams' => [2, 3, 4, 5, 6], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Titik Samsistini, S.Pd', 'id_guru' => 51],
                    ['jams' => [7], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Veronica Damay Rulitasari, S.Pd', 'id_guru' => 94],
                    ['jams' => [8, 9, 10], 'mapel' => 'Mapel Pilihan MOP', 'id_mapel' => 32, 'guru' => 'Tutut Sriatin, S.Pd', 'id_guru' => 107],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [3, 4, 5], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Rizki Putri Wulandari, S.Pd', 'id_guru' => 134],
                    ['jams' => [6], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Dra. Susakti Yuharini', 'id_guru' => 118],
                    ['jams' => [7, 8, 9], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Titik Samsistini, S.Pd', 'id_guru' => 51],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Andri Retno Yuli Astuti, S.Pd', 'id_guru' => 68],
                ],
            ],
            // ================= XI MP 4 =================
            'XI MP 4' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Mega Mahardika, S.Pd', 'id_guru' => 113],
                    ['jams' => [5, 6], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Veronica Damay Rulitasari, S.Pd', 'id_guru' => 94],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Rulik Indrawati, S.Pd', 'id_guru' => 48],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Mega Mahardika, S.Pd', 'id_guru' => 113],
                    ['jams' => [4, 5, 6], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [7], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Muto\'atul Khosi\'ah, S.Pd', 'id_guru' => 123],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Rizki Putri Wulandari, S.Pd', 'id_guru' => 134],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Muto\'atul Khosi\'ah, S.Pd', 'id_guru' => 123],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Dra. Susakti Yuharini', 'id_guru' => 118],
                    ['jams' => [6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Zainul Arifin, S.Pd', 'id_guru' => 33],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Mega Mahardika, S.Pd', 'id_guru' => 113],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Mega Mahardika, S.Pd', 'id_guru' => 113],
                    ['jams' => [3, 4, 5], 'mapel' => 'Mapel Pilihan MOP', 'id_mapel' => 32, 'guru' => 'Tutut Sriatin, S.Pd', 'id_guru' => 107],
                    ['jams' => [6], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Alfinu Farikh Abdillah, S.Pd.I', 'id_guru' => 96],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Abdul Rohman, S.Pd', 'id_guru' => 133],
                    ['jams' => [3, 4], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Dra. Susakti Yuharini', 'id_guru' => 118],
                    ['jams' => [5, 6, 7], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Mega Mahardika, S.Pd', 'id_guru' => 113],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Endang Safitri, S.Pd', 'id_guru' => 131],
                    ['jams' => [10, 11, 12], 'mapel' => 'Konsentrasi MP', 'id_mapel' => 31, 'guru' => 'Mega Mahardika, S.Pd', 'id_guru' => 113],
                ],
            ],
            // ================= XI AK 1 =================
            'XI AK 1' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Kasmi, S.Pd., M.Pd', 'id_guru' => 21],
                    ['jams' => [3, 4, 5], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [6, 7], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Eko Saputro, S.Pd.', 'id_guru' => 130],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Muhammad Fajar Assidiqi, S.Pd', 'id_guru' => 127],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Agus Fahruddy, S.Pd., M.Pd', 'id_guru' => 61],
                    ['jams' => [2, 3, 4], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Astra Bella Flamboyan, S.Psi', 'id_guru' => 116],
                    ['jams' => [5, 6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Indayah, S.Pd., M.Pd', 'id_guru' => 47],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                    ['jams' => [2, 3, 4], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                    ['jams' => [5], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Septiani, S.Pd.,M.Pd', 'id_guru' => 73],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Alfinu Farikh Abdillah, S.Pd.I', 'id_guru' => 96],
                    ['jams' => [3, 4], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Agustina Mardika Rini, S.Pd.,M.Pd', 'id_guru' => 55],
                    ['jams' => [5, 6, 7, 8, 9, 10], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Kasmi, S.Pd., M.Pd', 'id_guru' => 21],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                    ['jams' => [3, 4, 5, 6, 7], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Dyah Esti Rahayu, S.Pd', 'id_guru' => 23],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Winartin, S.Pd', 'id_guru' => 30],
                    ['jams' => [10, 11, 12], 'mapel' => 'Mapel Pilihan AK', 'id_mapel' => 29, 'guru' => 'Yuli Ratnasari, S.Pd', 'id_guru' => 106],
                ],
            ],
            // ================= XI AK 2 =================
            'XI AK 2' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Dyah Esti Rahayu, S.Pd', 'id_guru' => 23],
                    ['jams' => [5, 6, 7], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Alfinu Farikh Abdillah, S.Pd.I', 'id_guru' => 96],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [2, 3, 4], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Agus Fahruddy, S.Pd., M.Pd', 'id_guru' => 61],
                    ['jams' => [5], 'mapel' => 'Mapel Pilihan AK', 'id_mapel' => 29, 'guru' => 'Yuli Ratnasari, S.Pd', 'id_guru' => 106],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Yuli Ratnasari, S.Pd', 'id_guru' => 106],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3, 4, 5], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Pipit Ambarwati, S.Pd', 'id_guru' => 109],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Muhammad Fajar Assidiqi, S.Pd', 'id_guru' => 127],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Kasmi, S.Pd., M.Pd', 'id_guru' => 21],
                    ['jams' => [5, 6, 7], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Winartin, S.Pd', 'id_guru' => 30],
                    ['jams' => [8, 9, 10], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Setiyo Winarko, S.Pd', 'id_guru' => 46],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                    ['jams' => [3, 4, 5], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Ajeng Okvitasari, S.Pd', 'id_guru' => 120],
                    ['jams' => [6, 7, 8], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                    ['jams' => [9], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Kasmi, S.Pd., M.Pd', 'id_guru' => 21],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Winarsih, S.Pd, M.Pd', 'id_guru' => 39],
                ],
            ],
            // ================= XI AK 3 =================
            'XI AK 3' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Alfinu Farikh Abdillah, S.Pd.I', 'id_guru' => 96],
                    ['jams' => [4, 5], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Agus Fahruddy, S.Pd., M.Pd', 'id_guru' => 61],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Atih Wilupi, S.E, M.Pd', 'id_guru' => 57],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Winartin, S.Pd', 'id_guru' => 30],
                    ['jams' => [3, 4, 5, 6, 7], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Ninik Sriwidayati, S.Pd.,M.Pd', 'id_guru' => 54],
                    ['jams' => [8, 9, 10], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Setiyo Winarko, S.Pd', 'id_guru' => 46],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3, 4, 5], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Atih Wilupi, S.E, M.Pd', 'id_guru' => 57],
                    ['jams' => [6, 7], 'mapel' => 'Mapel Pilihan AK', 'id_mapel' => 29, 'guru' => 'Yuli Ratnasari, S.Pd', 'id_guru' => 106],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Rizki Putri Wulandari, S.Pd', 'id_guru' => 134],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [2, 3, 4], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [5, 6, 7], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Dyah Esti Rahayu, S.Pd', 'id_guru' => 23],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Andri Retno Yuli Astuti, S.Pd', 'id_guru' => 68],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Arvia Rienetasary, S.Pd', 'id_guru' => 41],
                    ['jams' => [4, 5], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Indayah, S.Pd., M.Pd', 'id_guru' => 47],
                    ['jams' => [6, 7, 8], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                    ['jams' => [9], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Andri Retno Yuli Astuti, S.Pd', 'id_guru' => 68],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Nur Eko Wahyuningsih, S.Pd', 'id_guru' => 101],
                ],
            ],
            // ================= XI AK 4 =================
            'XI AK 4' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Ninik Sriwidayati, S.Pd.,M.Pd', 'id_guru' => 54],
                    ['jams' => [5, 6, 7, 8], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Andri Retno Yuli Astuti, S.Pd', 'id_guru' => 68],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Mapel Pilihan AK', 'id_mapel' => 29, 'guru' => 'Yuli Ratnasari, S.Pd', 'id_guru' => 106],
                    ['jams' => [2, 3, 4], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [5], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Nur Eko Wahyuningsih, S.Pd', 'id_guru' => 101],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Atih Wilupi, S.E, M.Pd', 'id_guru' => 57],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Rizki Putri Wulandari, S.Pd', 'id_guru' => 134],
                    ['jams' => [3], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Andri Retno Yuli Astuti, S.Pd', 'id_guru' => 68],
                    ['jams' => [4, 5, 6], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                    ['jams' => [7], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Ninik Sriwidayati, S.Pd.,M.Pd', 'id_guru' => 54],
                    ['jams' => [8, 9, 10], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Indayah, S.Pd., M.Pd', 'id_guru' => 47],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Titin Sukmasari, S.Pd., M.Pd', 'id_guru' => 62],
                    ['jams' => [4, 5, 6], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Siti Khoiriyah, S.Pd', 'id_guru' => 58],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Basuki Sarjono, S.Pd', 'id_guru' => 50],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Agus Fahruddy, S.Pd., M.Pd', 'id_guru' => 61],
                    ['jams' => [4, 5, 6, 7, 8], 'mapel' => 'Konsentrasi AK', 'id_mapel' => 28, 'guru' => 'Septiani, S.Pd.,M.Pd', 'id_guru' => 73],
                    ['jams' => [9, 10, 11, 12], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Muashofah, M.Pd', 'id_guru' => 9],
                ],
            ],
            // ================= XI ULW =================
            'XI ULW' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Purwati, S.Pd', 'id_guru' => 53],
                    ['jams' => [4, 5, 6, 7], 'mapel' => 'Konsentrasi ULW', 'id_mapel' => 43, 'guru' => 'Risqi Nur Imama, S.Tr.Par', 'id_guru' => 90],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fitria Renytasari, S.Pd', 'id_guru' => 34],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fitria Renytasari, S.Pd', 'id_guru' => 34],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Konsentrasi ULW', 'id_mapel' => 43, 'guru' => 'Risqi Nur Imama, S.Tr.Par', 'id_guru' => 90],
                    ['jams' => [6, 7], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Nur Nastutisari, S.ST.Par.', 'id_guru' => 95],
                    ['jams' => [8, 9, 10], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                    ['jams' => [2, 3, 4], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Nur Nastutisari, S.ST.Par.', 'id_guru' => 95],
                    ['jams' => [5, 6, 7], 'mapel' => 'Konsentrasi ULW', 'id_mapel' => 43, 'guru' => 'Nur Nastutisari, S.ST.Par.', 'id_guru' => 95],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Ajeng Okvitasari, S.Pd', 'id_guru' => 120],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Bella Prakoso, S.Pd', 'id_guru' => 124],
                    ['jams' => [2, 3, 4], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Dwi Nowa Setyandari, S.Pd', 'id_guru' => 66],
                    ['jams' => [5, 6, 7], 'mapel' => 'Konsentrasi ULW', 'id_mapel' => 43, 'guru' => 'Nur Nastutisari, S.ST.Par.', 'id_guru' => 95],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Jepang', 'id_mapel' => 14, 'guru' => 'Sulistyowati, SS', 'id_guru' => 24],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Muashofah, M.Pd', 'id_guru' => 9],
                    ['jams' => [4, 5, 6, 7, 8], 'mapel' => 'Konsentrasi ULW', 'id_mapel' => 43, 'guru' => 'Risqi Nur Imama, S.Tr.Par', 'id_guru' => 90],
                    ['jams' => [9], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Niken Hari Pratiwi, S.Psi.,M.Pd', 'id_guru' => 64],
                    ['jams' => [10, 11, 12], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                ],
            ],
            // ================= XI DKV 1 =================
            'XI DKV 1' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fajar Wahyu Pratiwi, S.S', 'id_guru' => 103],
                    ['jams' => [3, 4, 5], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Danang Anjar Hymawanto, S.Pd', 'id_guru' => 86],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Istiana Suhartati, S.T', 'id_guru' => 89],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Agus Pramono, S.Sn', 'id_guru' => 119],
                    ['jams' => [2, 3, 4, 5, 6], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Khoyrotun Hisani, S.Sn', 'id_guru' => 97],
                    ['jams' => [7], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Mas\'an Widodo, S.Pd. M.T', 'id_guru' => 81],
                    ['jams' => [8, 9, 10], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Indriati, S.Pd', 'id_guru' => 32],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Astra Bella Flamboyan, S.Psi', 'id_guru' => 116],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Khoyrotun Hisani, S.Sn', 'id_guru' => 97],
                    ['jams' => [6], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Danang Anjar Hymawanto, S.Pd', 'id_guru' => 86],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Mas\'an Widodo, S.Pd. M.T', 'id_guru' => 81],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Ilham Sungeidi, S.Pd', 'id_guru' => 60],
                    ['jams' => [2, 3, 4], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Khoyrotun Hisani, S.Sn', 'id_guru' => 97],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Endang Safitri, S.Pd', 'id_guru' => 131],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Elysa Yuli Nur\'aini, S.Si', 'id_guru' => 71],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Mapel Pilihan DKV', 'id_mapel' => 35, 'guru' => 'Endik Kuswantoro, S.Kom.,M.T', 'id_guru' => 82],
                    ['jams' => [4, 5, 6, 7], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fajar Wahyu Pratiwi, S.S', 'id_guru' => 103],
                    ['jams' => [8, 9], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Sinta Lestari, S.Pd.I', 'id_guru' => 115],
                    ['jams' => [10, 11, 12], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Abdul Rohman, S.Pd', 'id_guru' => 133],
                ],
            ],
            // ================= XI DKV 2 =================
            'XI DKV 2' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Astra Bella Flamboyan, S.Psi', 'id_guru' => 116],
                    ['jams' => [3, 4, 5], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Khoyrotun Hisani, S.Sn', 'id_guru' => 97],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Khoyrotun Hisani, S.Sn', 'id_guru' => 97],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Abdul Rohman, S.Pd', 'id_guru' => 133],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Khoyrotun Hisani, S.Sn', 'id_guru' => 97],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Istiana Suhartati, S.T', 'id_guru' => 89],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Danang Anjar Hymawanto, S.Pd', 'id_guru' => 86],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Danang Anjar Hymawanto, S.Pd', 'id_guru' => 86],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Endang Safitri, S.Pd', 'id_guru' => 131],
                    ['jams' => [6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Ilham Sungeidi, S.Pd', 'id_guru' => 60],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Elysa Yuli Nur\'aini, S.Si', 'id_guru' => 71],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Mas\'an Widodo, S.Pd. M.T', 'id_guru' => 81],
                    ['jams' => [3, 4, 5], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Indriati, S.Pd', 'id_guru' => 32],
                    ['jams' => [6], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fajar Wahyu Pratiwi, S.S', 'id_guru' => 103],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Sinta Lestari, S.Pd.I', 'id_guru' => 115],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Widodo, S.Pd', 'id_guru' => 31],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fajar Wahyu Pratiwi, S.S', 'id_guru' => 103],
                    ['jams' => [5, 6, 7, 8], 'mapel' => 'Mapel Pilihan DKV', 'id_mapel' => 35, 'guru' => 'Endik Kuswantoro, S.Kom.,M.T', 'id_guru' => 82],
                    ['jams' => [9, 10, 11, 12], 'mapel' => 'Konsentrasi DKV', 'id_mapel' => 34, 'guru' => 'Agus Pramono, S.Sn', 'id_guru' => 119],
                ],
            ],
            // ================= XI PSPT 1 =================
            'XI PSPT 1' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Umi Kulsum, S.Pd', 'id_guru' => 13],
                    ['jams' => [4, 5], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Benny Mamora, S.Kom', 'id_guru' => 85],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Joko Priyanto, S.Kom', 'id_guru' => 98],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Sa\'ad Wazis Hiedayat, S.Pd', 'id_guru' => 100],
                    ['jams' => [2, 3, 4], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Sa\'ad Wazis Hiedayat, S.Pd', 'id_guru' => 100],
                    ['jams' => [5], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Joko Priyanto, S.Kom', 'id_guru' => 98],
                    ['jams' => [6, 7], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Joko Priyanto, S.Kom', 'id_guru' => 98],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Winarsih, S.Pd, M.Pd', 'id_guru' => 39],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Tuhu Eries Kudori, S.Sn', 'id_guru' => 129],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fajar Wahyu Pratiwi, S.S', 'id_guru' => 103],
                    ['jams' => [7], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Fitri Amaliyah, S.Pd', 'id_guru' => 110],
                    ['jams' => [8, 9, 10], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Siti Umiharsih, S.Pd', 'id_guru' => 38],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Mapel Pilihan PSPT', 'id_mapel' => 38, 'guru' => 'Tuhu Eries Kudori, S.Sn', 'id_guru' => 129],
                    ['jams' => [4, 5, 6], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Dra. Hanik Pangestuti', 'id_guru' => 26],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Ajeng Okvitasari, S.Pd', 'id_guru' => 120],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Fajar Wahyu Pratiwi, S.S', 'id_guru' => 103],
                    ['jams' => [3, 4, 5], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [6], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Bella Prakoso, S.Pd', 'id_guru' => 124],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                    ['jams' => [9, 10, 11, 12], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Siti Umiharsih, S.Pd', 'id_guru' => 38],
                ],
            ],
            // ================= XI PSPT 2 =================
            'XI PSPT 2' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Eko Saputro, S.Pd.', 'id_guru' => 130],
                    ['jams' => [4, 5], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Siti Umiharsih, S.Pd', 'id_guru' => 38],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Tuhu Eries Kudori, S.Sn', 'id_guru' => 129],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Dra. Hanik Pangestuti', 'id_guru' => 26],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Siti Umiharsih, S.Pd', 'id_guru' => 38],
                    ['jams' => [7], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Dra. Anik Indriani', 'id_guru' => 22],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Muto\'atul Khosi\'ah, S.Pd', 'id_guru' => 123],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Joko Priyanto, S.Kom', 'id_guru' => 98],
                    ['jams' => [4, 5, 6, 7], 'mapel' => 'Mapel Pilihan PSPT', 'id_mapel' => 38, 'guru' => 'Tuhu Eries Kudori, S.Sn', 'id_guru' => 129],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Joko Priyanto, S.Kom', 'id_guru' => 98],
                    ['jams' => [2, 3], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Joko Priyanto, S.Kom', 'id_guru' => 98],
                    ['jams' => [4, 5, 6], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Sa\'ad Wazis Hiedayat, S.Pd', 'id_guru' => 100],
                    ['jams' => [7], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Sa\'ad Wazis Hiedayat, S.Pd', 'id_guru' => 100],
                    ['jams' => [8, 9, 10], 'mapel' => 'Konsentrasi PSPT', 'id_mapel' => 37, 'guru' => 'Benny Mamora, S.Kom', 'id_guru' => 85],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Bella Prakoso, S.Pd', 'id_guru' => 124],
                    ['jams' => [3, 4, 5], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Winarsih, S.Pd, M.Pd', 'id_guru' => 39],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Umi Kulsum, S.Pd', 'id_guru' => 13],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Muto\'atul Khosi\'ah, S.Pd', 'id_guru' => 123],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Erna Qoriah, S.E.', 'id_guru' => 17],
                ],
            ],
            // ================= XI AN 1 =================
            'XI AN 1' => [
                'Senin' => [
                    ['jams' => [2], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Arif Setyobudi, S.Pd', 'id_guru' => 84],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                    ['jams' => [7], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Andika Christian Sasmita, S.ST', 'id_guru' => 122],
                    ['jams' => [8, 9, 10], 'mapel' => 'Mapel Pilihan Ainimasi', 'id_mapel' => 41, 'guru' => 'Endik Kuswantoro, S.Kom.,M.T', 'id_guru' => 82],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Erwan Septiyono, S.Pd', 'id_guru' => 88],
                    ['jams' => [3, 4, 5, 6], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Rika Okta Maulida, S.Ds.', 'id_guru' => 99],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Dhuana Putri Puspitasary, S.Pd', 'id_guru' => 79],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Endik Kuswantoro, S.Kom.,M.T', 'id_guru' => 82],
                    ['jams' => [2, 3, 4, 5, 6], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Andika Christian Sasmita, S.ST', 'id_guru' => 122],
                    ['jams' => [7], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Yustin Febrini, S.Pd', 'id_guru' => 125],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Khuriyatul Kamila, S.Si', 'id_guru' => 27],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Erwan Septiyono, S.Pd', 'id_guru' => 88],
                    ['jams' => [3, 4, 5], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Andika Christian Sasmita, S.ST', 'id_guru' => 122],
                    ['jams' => [6, 7], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Muashofah, M.Pd', 'id_guru' => 9],
                    ['jams' => [8, 9, 10], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                ],
                'Jumat' => [
                    ['jams' => [2], 'mapel' => 'Mapel Pilihan Ainimasi', 'id_mapel' => 41, 'guru' => 'Andika Christian Sasmita, S.ST', 'id_guru' => 122],
                    ['jams' => [3, 4], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Bella Prakoso, S.Pd', 'id_guru' => 124],
                    ['jams' => [5, 6, 7], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Dwi Nowa Setyandari, S.Pd', 'id_guru' => 66],
                    ['jams' => [8, 9], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Siti Maisaroh, S.Pd', 'id_guru' => 126],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Ajeng Okvitasari, S.Pd', 'id_guru' => 120],
                ],
            ],
            // ================= XI AN 2 =================
            'XI AN 2' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Dhuana Putri Puspitasary, S.Pd', 'id_guru' => 79],
                    ['jams' => [4, 5, 6, 7], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Rika Okta Maulida, S.Ds.', 'id_guru' => 99],
                    ['jams' => [8, 9, 10], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Andika Christian Sasmita, S.ST', 'id_guru' => 122],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Andika Christian Sasmita, S.ST', 'id_guru' => 122],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Mapel Pilihan Ainimasi', 'id_mapel' => 41, 'guru' => 'Endik Kuswantoro, S.Kom.,M.T', 'id_guru' => 82],
                    ['jams' => [6], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Bahasa Indonesia', 'id_mapel' => 3, 'guru' => 'Arif Setyobudi, S.Pd', 'id_guru' => 84],
                ],
                'Rabu' => [
                    ['jams' => [1], 'mapel' => 'Bahasa Jawa', 'id_mapel' => 13, 'guru' => 'Luluk Munfarida, S.Pd', 'id_guru' => 20],
                    ['jams' => [2, 3, 4], 'mapel' => 'PJOK', 'id_mapel' => 4, 'guru' => 'Bella Prakoso, S.Pd', 'id_guru' => 124],
                    ['jams' => [5], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Endik Kuswantoro, S.Kom.,M.T', 'id_guru' => 82],
                    ['jams' => [6, 7, 8, 9, 10], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Erwan Septiyono, S.Pd', 'id_guru' => 88],
                ],
                'Kamis' => [
                    ['jams' => [1], 'mapel' => 'Bimbingan Konseling', 'id_mapel' => 45, 'guru' => 'Siti Maisaroh, S.Pd', 'id_guru' => 126],
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'id_mapel' => 1, 'guru' => 'Muashofah, M.Pd', 'id_guru' => 9],
                    ['jams' => [6], 'mapel' => 'Matematika', 'id_mapel' => 5, 'guru' => 'Dwi Nowa Setyandari, S.Pd', 'id_guru' => 66],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Sejarah', 'id_mapel' => 8, 'guru' => 'Yustin Febrini, S.Pd', 'id_guru' => 125],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Konsentrasi Animasi', 'id_mapel' => 40, 'guru' => 'Erwan Septiyono, S.Pd', 'id_guru' => 88],
                    ['jams' => [4, 5, 6], 'mapel' => 'Mapel Pilihan Ainimasi', 'id_mapel' => 41, 'guru' => 'Andika Christian Sasmita, S.ST', 'id_guru' => 122],
                    ['jams' => [7], 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan', 'id_mapel' => 12, 'guru' => 'Andika Christian Sasmita, S.ST', 'id_guru' => 122],
                    ['jams' => [8, 9], 'mapel' => 'Pendidikan Pancasila', 'id_mapel' => 2, 'guru' => 'Wiwik Yuniarsih, S.Pd.', 'id_guru' => 19],
                    ['jams' => [10, 11, 12], 'mapel' => 'Bahasa Inggris', 'id_mapel' => 6, 'guru' => 'Agus Muharyanto, M.Pd', 'id_guru' => 72],
                ],
            ],
        ];

        foreach ($scheduleDefinition as $className => $days) {
            // Find class in DB
            $kelas = Kelas::where('nama_kelas', $className)->first();
            if (!$kelas) {
                $alt1 = str_replace('AK', 'AKL', $className);
                $kelas = Kelas::where('nama_kelas', $alt1)->first();
            }
            if (!$kelas) {
                $alt2 = str_replace('XI BD 3', 'XI BD 3 (ALFAMART)', $className);
                $kelas = Kelas::where('nama_kelas', $alt2)->first();
            }

            if (!$kelas) {
                $this->command->error("Kelas {$className} not found in database.");
                continue;
            }

            $idKelas = $kelas->id_kelas;

            // Hard delete old schedule for this class in this TA to prevent duplicate slot index collisions
            \Illuminate\Support\Facades\DB::table('jadwal_pelajaran')
                ->where('id_kelas', $idKelas)
                ->where('id_tahun_ajaran', $idTa)
                ->delete();

            $insertRows = [];

            foreach ($days as $hari => $lessons) {
                $slotTable = ($hari === 'Jumat') ? $timesJumat : $timesSeninKamis;

                foreach ($lessons as $lesson) {
                    $idMapel = $lesson['id_mapel'] ?? null;
                    if (!$idMapel) {
                        $m = Mapel::where('nama_mapel', $lesson['mapel'])->first();
                        $idMapel = $m ? $m->id_mapel : null;
                    }

                    $idGuru = $lesson['id_guru'] ?? null;
                    if (!$idGuru) {
                        $g = Guru::where('nama_lengkap', $lesson['guru'])->first();
                        $idGuru = $g ? $g->id_guru : null;
                    }

                    if (!$idMapel || !$idGuru) {
                        $this->command->warn("Missing mapel/guru for {$className} on {$hari}: {$lesson['mapel']} / {$lesson['guru']}");
                        continue;
                    }

                    $jams = $lesson['jams'];
                    $startJam = min($jams);
                    $endJam = max($jams);
                    $jamMulai = $slotTable[$startJam][0] ?? '07:00:00';
                    $jamSelesai = $slotTable[$endJam][1] ?? '15:00:00';

                    foreach ($jams as $jamKe) {
                        $insertRows[] = [
                            'id_tahun_ajaran' => $idTa,
                            'id_kelas'        => $idKelas,
                            'id_mapel'        => $idMapel,
                            'id_guru'         => $idGuru,
                            'hari'            => $hari,
                            'jam_ke'          => $jamKe,
                            'jam_mulai'       => $jamMulai,
                            'jam_selesai'     => $jamSelesai,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }
            }

            if (!empty($insertRows)) {
                JadwalPelajaran::insert($insertRows);
                $this->command->info("Successfully seeded schedule for {$className} ({$kelas->nama_kelas}) - " . count($insertRows) . " JP.");
            }
        }
    }
}