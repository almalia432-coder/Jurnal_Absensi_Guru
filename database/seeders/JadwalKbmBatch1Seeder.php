<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Mapel;

class JadwalKbmBatch1Seeder extends Seeder
{
    public function run(): void
    {
        $ta = TahunAjaran::where('is_aktif', true)->first() ?? TahunAjaran::first();
        $idTa = $ta->id;

        // Timeslots lookup
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

        // Batch 1 schedule definition for 6 classes
        $scheduleDefinition = [
            // ================= 1. X TKI 1 =================
            'X TKI 1' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                    ['jams' => [5, 6], 'mapel' => 'PJOK', 'guru' => 'Ilham Sungeidi, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Yani, S.Pd.'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Jawa', 'guru' => 'Yustin Febrini, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Yani, S.Pd.'],
                    ['jams' => [5, 6], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar TKI', 'guru' => "Rifkotin Na'imah, S.Pd"],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Endang Ary Handayani, S.T., M.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Dasar TKI', 'guru' => 'Sri Kusumastuti, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Muashofah, M.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Informatika', 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Matematika', 'guru' => 'Arvia Rienetasary, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Wiwik Yuniarsih, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar TKI', 'guru' => 'Sri Kusumastuti, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Dasar TKI', 'guru' => "Rifkotin Na'imah, S.Pd"],
                    ['jams' => [5], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Yuni Jiastuti, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Matematika', 'guru' => 'Arvia Rienetasary, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Informatika', 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.'],
                    ['jams' => [10, 11], 'mapel' => 'Sejarah', 'guru' => 'Fajar Luthfianto, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Bahasa Inggris', 'guru' => "Muto'atul Khosi'ah, S.Pd"],
                ],
            ],

            // ================= 2. X TKI 2 =================
            'X TKI 2' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Yani, S.Pd.'],
                    ['jams' => [4, 5], 'mapel' => 'Matematika', 'guru' => 'Arvia Rienetasary, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Wiwik Yuniarsih, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar TKI', 'guru' => 'Sri Kusumastuti, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Yuni Jiastuti, S.Pd'],
                    ['jams' => [2, 3, 4], 'mapel' => 'Dasar TKI', 'guru' => "Rifkotin Na'imah, S.Pd"],
                    ['jams' => [5, 6], 'mapel' => 'Informatika', 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.'],
                    ['jams' => [7, 8], 'mapel' => 'Sejarah', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Endang Ary Handayani, S.T., M.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Matematika', 'guru' => 'Arvia Rienetasary, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Informatika', 'guru' => 'Lutfia Marsalina, S.Pd.I, M.Pd.'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Jawa', 'guru' => 'Yustin Febrini, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Dasar TKI', 'guru' => "Rifkotin Na'imah, S.Pd"],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Yani, S.Pd.'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Mufatiroh, S.Ag'],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Ilham Sungeidi, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Dasar TKI', 'guru' => 'Sri Kusumastuti, S.Pd'],
                    ['jams' => [11, 12, 13], 'mapel' => 'IPAS', 'guru' => 'Khuriyatul Kamila, S.Si'],
                ],
            ],

            // ================= 3. X RPL 1 =================
            'X RPL 1' => [
                'Senin' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Winartin, S.Pd'],
                    ['jams' => [4], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Widodo, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Zainul Arifin, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'IPAS', 'guru' => 'Indriati, S.Pd'],
                    ['jams' => [4, 5], 'mapel' => 'Bahasa Jawa', 'guru' => 'Rizki Putri Wulandari, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Mufatiroh, S.Ag'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Matematika', 'guru' => "Elysa Yuli Nur'aini, S.Si"],
                    ['jams' => [5, 6], 'mapel' => 'Informatika', 'guru' => 'Kurnila Putri Islamawati, S.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar PPLG', 'guru' => 'Badrus Sulaiman, S.Pd.'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fitria Renytasari, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Matematika', 'guru' => "Elysa Yuli Nur'aini, S.Si"],
                    ['jams' => [5, 6], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar PPLG', 'guru' => 'Ruly Dwi Setyaningrum, S.Kom'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Dasar PPLG', 'guru' => 'Ruly Dwi Setyaningrum, S.Kom'],
                    ['jams' => [6, 7], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Elyana Frisca Monica, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Abdul Rohman, S.Pd'],
                    ['jams' => [10, 11], 'mapel' => 'Informatika', 'guru' => 'Kurnila Putri Islamawati, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Winartin, S.Pd'],
                ],
            ],

            // ================= 4. X RPL 2 =================
            'X RPL 2' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Dasar PPLG', 'guru' => 'Ruly Dwi Setyaningrum, S.Kom'],
                    ['jams' => [5, 6, 7], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Umi Kulsum, S.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Fitri Amaliyah, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Informatika', 'guru' => 'Elyana Frisca Monica, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Elyana Frisca Monica, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Abdul Rohman, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Bahasa Inggris', 'guru' => 'Fajar Wahyu Pratiwi, S.S'],
                    ['jams' => [5, 6], 'mapel' => 'Matematika', 'guru' => "Elysa Yuli Nur'aini, S.Si"],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar PPLG', 'guru' => 'Ruly Dwi Setyaningrum, S.Kom'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3], 'mapel' => 'Dasar PPLG', 'guru' => 'Badrus Sulaiman, S.Pd.'],
                    ['jams' => [4, 5, 6], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Mufatiroh, S.Ag'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Fitri Amaliyah, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Umi Kulsum, S.Pd'],
                    ['jams' => [4], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Widodo, S.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'PJOK', 'guru' => 'Zainul Arifin, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Jawa', 'guru' => 'Laili Ermawati, S.Pd'],
                    ['jams' => [10, 11], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [12, 13], 'mapel' => 'Matematika', 'guru' => "Elysa Yuli Nur'aini, S.Si"],
                ],
            ],

            // ================= 5. X TKJ 1 =================
            'X TKJ 1' => [
                'Senin' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'IPAS', 'guru' => 'Fitri Amaliyah, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Inggris', 'guru' => 'Isti Mufadah, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Listyana Hartati, S.Kom., M.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Dasar TKJ', 'guru' => 'Listyana Hartati, S.Kom., M.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Informatika', 'guru' => 'Siswanti Purwaningsih, S.T., M.Pd'],
                    ['jams' => [5, 6, 7], 'mapel' => 'IPAS', 'guru' => 'Fitri Amaliyah, S.Pd'],
                    ['jams' => [8, 9], 'mapel' => 'Bahasa Jawa', 'guru' => 'Muhammad Fajar Assidiqi, S.Pd'],
                    ['jams' => [10], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Nishfu Laili, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Matematika', 'guru' => 'Basuki Sarjono, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Sri Rahayu, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Dasar TKJ', 'guru' => 'Endang Ary Handayani, S.T., M.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Matematika', 'guru' => 'Basuki Sarjono, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'guru' => 'Isti Mufadah, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2], 'mapel' => 'Sejarah', 'guru' => 'Ista Nofasari, S.Pd'],
                    ['jams' => [3, 4], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Sri Rahayu, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Wiwik Yuniarsih, S.Pd'],
                    ['jams' => [7, 8, 9, 10], 'mapel' => 'Dasar TKJ', 'guru' => 'Siti Munawaroh, S.Kom., M.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'PJOK', 'guru' => 'Ilham Sungeidi, S.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [7, 8, 9], 'mapel' => 'Dasar TKJ', 'guru' => 'Siswanti Purwaningsih, S.T., M.Pd'],
                    ['jams' => [10, 11, 12, 13], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Sinta Lestari, S.Pd.I'],
                ],
            ],

            // ================= 6. X TKJ 2 =================
            'X TKJ 2' => [
                'Senin' => [
                    ['jams' => [2, 3, 4, 5], 'mapel' => 'Dasar TKJ', 'guru' => 'Siti Munawaroh, S.Kom., M.Pd'],
                    ['jams' => [6, 7, 8], 'mapel' => 'Pendidikan Agama Islam dan Budi Perkerti', 'guru' => 'Sinta Lestari, S.Pd.I'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Basuki Sarjono, S.Pd'],
                ],
                'Selasa' => [
                    ['jams' => [1, 2], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Sri Rahayu, S.Pd'],
                    ['jams' => [3, 4, 5], 'mapel' => 'PJOK', 'guru' => 'Ilham Sungeidi, S.Pd'],
                    ['jams' => [6, 7], 'mapel' => 'Dasar TKJ', 'guru' => 'Endang Ary Handayani, S.T., M.Pd'],
                    ['jams' => [8, 9, 10], 'mapel' => 'IPAS', 'guru' => 'Fitri Amaliyah, S.Pd'],
                ],
                'Rabu' => [
                    ['jams' => [1, 2], 'mapel' => 'Pendidikan Pancasila', 'guru' => 'Wiwik Yuniarsih, S.Pd'],
                    ['jams' => [3], 'mapel' => 'Bimbingan Konseling', 'guru' => 'Nishfu Laili, S.Pd'],
                    ['jams' => [4, 5, 6], 'mapel' => 'IPAS', 'guru' => 'Fitri Amaliyah, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Dasar TKJ', 'guru' => 'Listyana Hartati, S.Kom., M.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Matematika', 'guru' => 'Basuki Sarjono, S.Pd'],
                ],
                'Kamis' => [
                    ['jams' => [1, 2, 3, 4], 'mapel' => 'Informatika', 'guru' => 'Siswanti Purwaningsih, S.T., M.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Bahasa Indonesia', 'guru' => 'Sri Rahayu, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Seni Budaya', 'guru' => 'Anang Prasetyo, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'guru' => 'Isti Mufadah, S.Pd'],
                ],
                'Jumat' => [
                    ['jams' => [2, 3, 4], 'mapel' => 'Dasar TKJ', 'guru' => 'Siswanti Purwaningsih, S.T., M.Pd'],
                    ['jams' => [5, 6], 'mapel' => 'Sejarah', 'guru' => 'Yustin Febrini, S.Pd'],
                    ['jams' => [7, 8], 'mapel' => 'Bahasa Jawa', 'guru' => 'Muhammad Fajar Assidiqi, S.Pd'],
                    ['jams' => [9, 10], 'mapel' => 'Bahasa Inggris', 'guru' => 'Isti Mufadah, S.Pd'],
                    ['jams' => [11, 12, 13], 'mapel' => 'Koding dan Kecerdasan Artifisial', 'guru' => 'Listyana Hartati, S.Kom., M.Pd'],
                ],
            ],
        ];

        foreach ($scheduleDefinition as $className => $days) {
            $kelas = Kelas::where('nama_kelas', $className)->first();
            if (!$kelas) {
                echo "Warning: Kelas '{$className}' not found in database.\n";
                continue;
            }

            // Remove existing schedule for this class to avoid duplicates
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

                    // Block start and end times
                    $firstJam = reset($slot['jams']);
                    $lastJam = end($slot['jams']);
                    $blockJamMulai = $timeSlots[$firstJam][0] ?? '07:00:00';
                    $blockJamSelesai = $timeSlots[$lastJam][1] ?? '15:00:00';

                    // Insert individual row per jam_ke
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
            echo "Successfully seeded schedule for {$className}.\n";
        }
    }

    private function resolveMapel(string $name): ?Mapel
    {
        $clean = trim($name);
        if ($clean === 'BK' || $clean === 'Bimbingan Konseling') {
            return Mapel::where('nama_mapel', 'like', '%Bimbingan%')->first();
        }
        if (stripos($clean, 'Dasar TKJ') !== false || stripos($clean, 'Dasar TJKT') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar TKJ%')->first();
        }
        if (stripos($clean, 'Dasar PPLG') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar PPLG%')->first();
        }
        if (stripos($clean, 'Dasar TKI') !== false) {
            return Mapel::where('nama_mapel', 'like', '%Dasar TKI%')->first();
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
