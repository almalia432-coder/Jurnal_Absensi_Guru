<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\WaliKelas;
use App\Models\GuruPiket;
use App\Models\GuruMapel;
use App\Models\Satpam;
use App\Models\KepalaSekolah;
use App\Models\Waka;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Mapel;
use App\Models\TahunAjaran;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tahun Ajaran Aktif
        $ta = TahunAjaran::create([
            'nama' => '2024/2025',
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2024-07-15',
            'tanggal_selesai' => '2024-12-20',
            'is_aktif' => true,
        ]);

        // 2. Sample Mapel
        $mapelRpl = Mapel::create(['kode_mapel' => 'RPL01', 'nama_mapel' => 'Pemrograman Web & Perangkat Bergerak', 'kelompok' => 'Produktif']);
        $mapelTkj = Mapel::create(['kode_mapel' => 'TKJ01', 'nama_mapel' => 'Administrasi Infrastruktur Jaringan', 'kelompok' => 'Produktif']);
        $mapelMtk = Mapel::create(['kode_mapel' => 'MTK01', 'nama_mapel' => 'Matematika Terapan', 'kelompok' => 'Adaptif']);

        // 3. User & Role Records across separate tables
        // Admin
        $userAdmin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        Admin::create([
            'user_id' => $userAdmin->id,
            'nip' => '198001012005011001',
            'nama_lengkap' => 'Administrator Utama',
            'no_hp' => '081234567890',
        ]);

        // Wali Kelas
        $userWali = User::create([
            'name' => 'Drs. Ahmad Fauzi, M.Pd (Wali Kelas)',
            'email' => 'walikelas@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'wali_kelas',
            'is_active' => true,
        ]);
        $waliKelasRecord = WaliKelas::create([
            'user_id' => $userWali->id,
            'nip' => '197502122003121002',
            'nama_lengkap' => 'Drs. Ahmad Fauzi, M.Pd',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567891',
        ]);

        // Guru Piket (Akun Bersama Petugas Piket)
        $userPiket = User::create([
            'name' => 'Petugas Piket',
            'email' => 'gurupiket@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru_piket',
            'is_active' => true,
        ]);
        GuruPiket::create([
            'user_id' => $userPiket->id,
            'nip' => 'PIKET-SMKN1',
            'nama_lengkap' => 'Petugas Piket',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567892',
            'hari_piket' => 'Senin s/d Jumat',
        ]);

        // Guru Mapel
        $userMapel = User::create([
            'name' => 'Siti Rahayu, S.Kom (Guru Mapel)',
            'email' => 'gurumapel@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        GuruMapel::create([
            'user_id' => $userMapel->id,
            'nip' => '198805202012022004',
            'nama_lengkap' => 'Siti Rahayu, S.Kom',
            'jenis_kelamin' => 'P',
            'no_hp' => '081234567893',
        ]);

        // Kepala Sekolah
        $userKepsek = User::create([
            'name' => 'Dr. H. Supriyanto, M.Pd (Kepala Sekolah)',
            'email' => 'kepsek@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'kepala_sekolah',
            'is_active' => true,
        ]);
        KepalaSekolah::create([
            'user_id' => $userKepsek->id,
            'nip' => '196808101994031005',
            'nama_lengkap' => 'Dr. H. Supriyanto, M.Pd',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567894',
            'periode_jabatan' => '2022-2026',
        ]);

        // Waka Kurikulum
        $userWakaKurikulum = User::create([
            'name' => 'Budi Santoso, M.T (Waka Kurikulum)',
            'email' => 'wakakurikulum@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'waka_kurikulum',
            'is_active' => true,
        ]);
        Waka::create([
            'user_id' => $userWakaKurikulum->id,
            'nip' => '197911042006041006',
            'nama_lengkap' => 'Budi Santoso, M.T',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567895',
            'bidang' => 'Kurikulum',
        ]);

        // Waka SDM
        $userWakaSdm = User::create([
            'name' => 'Dr. Hendra Wijaya, M.Pd (Waka SDM)',
            'email' => 'wakasdm@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'waka_sdm',
            'is_active' => true,
        ]);
        Waka::create([
            'user_id' => $userWakaSdm->id,
            'nip' => '198103152008011009',
            'nama_lengkap' => 'Dr. Hendra Wijaya, M.Pd',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567897',
            'bidang' => 'SDM',
        ]);

        // Wali Murid / Siswa Sample
        $userWaliMurid = User::create([
            'name' => 'Bapak/Ibu Wali Murid',
            'email' => 'walimurid@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'wali_murid',
            'is_active' => true,
        ]);

        // Satpam
        $userSatpam = User::create([
            'name' => 'Agus Setiawan (Satpam Gate)',
            'email' => 'satpam@smkn1boyolangu.sch.id',
            'password' => Hash::make('password'),
            'role' => 'satpam',
            'is_active' => true,
        ]);
        Satpam::create([
            'user_id' => $userSatpam->id,
            'nip' => '9900112233',
            'nama_lengkap' => 'Agus Setiawan',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567896',
            'pos_jaga' => 'Gerbang Utama',
        ]);

        // 4. Sample Kelas & Guru entries
        $guru1 = Guru::create([
            'user_id' => $userWali->id,
            'nip' => '197502122003121002',
            'nama_lengkap' => 'Drs. Ahmad Fauzi, M.Pd',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567891',
        ]);

        $guru2 = Guru::create([
            'user_id' => $userMapel->id,
            'nip' => '198805202012022004',
            'nama_lengkap' => 'Siti Rahayu, S.Kom',
            'jenis_kelamin' => 'P',
            'no_hp' => '081234567893',
        ]);

        // Create 28 Rombel Aktif (Classes)
        $jurusanRecords = Jurusan::all()->keyBy('kode_jurusan');
        $tingkatList = ['X', 'XI', 'XII'];
        $kelasInstances = [];

        $kelasCount = 0;
        foreach ($tingkatList as $tingkat) {
            foreach ($jurusanRecords as $j) {
                for ($num = 1; $num <= 2; $num++) {
                    $kelasCount++;
                    if ($kelasCount > 28) break;
                    $namaKelas = "{$tingkat} {$j->kode_jurusan} {$num}";
                    $k = Kelas::create([
                        'nama_kelas' => $namaKelas,
                        'tingkat' => $tingkat,
                        'id_jurusan' => $j->id_jurusan,
                        'jurusan' => $j->kode_jurusan,
                        'wali_kelas_id' => $waliKelasRecord->id,
                        'jumlah_siswa' => rand(28, 32),
                    ]);
                    $kelasInstances[] = $k;
                }
            }
        }

        // Create Siswa (Total ~842)
        $totalSiswaTarget = 842;
        $createdSiswa = 0;
        $siswaInstances = [];

        foreach ($kelasInstances as $idx => $k) {
            $siswaPerKelas = ($idx === count($kelasInstances) - 1) ? ($totalSiswaTarget - $createdSiswa) : rand(29, 31);
            for ($s = 1; $s <= $siswaPerKelas; $s++) {
                $createdSiswa++;
                $nis = sprintf("2024%04d", $createdSiswa);
                $nisn = sprintf("006%07d", $createdSiswa);
                $gender = ($createdSiswa % 2 == 0) ? 'L' : 'P';
                $siswaObj = \App\Models\Siswa::create([
                    'nis' => $nis,
                    'nisn' => $nisn,
                    'nama_lengkap' => "Siswa {$createdSiswa} ({$k->nama_kelas})",
                    'jenis_kelamin' => $gender,
                    'id_kelas' => $k->id_kelas,
                    'no_hp_ortu' => '0812' . sprintf("%08d", $createdSiswa),
                    'alamat' => 'Boyolangu, Tulungagung',
                    'status_aktif' => true,
                ]);
                $siswaInstances[] = $siswaObj;
            }
        }

        // Today's Date
        $today = date('Y-m-d');

        // Create 34 Jurnal entries for today out of 48 target
        for ($j = 1; $j <= 34; $j++) {
            $targetKelas = $kelasInstances[$j % count($kelasInstances)];
            $jurnal = \App\Models\JurnalMengajar::create([
                'id_guru' => ($j % 2 == 0) ? $guru1->id_guru : $guru2->id_guru,
                'id_kelas' => $targetKelas->id_kelas,
                'id_mapel' => ($j % 3 == 0) ? $mapelMtk->id_mapel : (($j % 2 == 0) ? $mapelTkj->id_mapel : $mapelRpl->id_mapel),
                'tanggal' => $today,
                'jam_ke' => (string)(($j % 8) + 1),
                'jam_mulai' => sprintf("%02d:00:00", 7 + ($j % 6)),
                'jam_selesai' => sprintf("%02d:45:00", 7 + ($j % 6)),
                'materi' => "Materi Pembelajaran Pertemuan Ke-{$j}",
                'jumlah_siswa_hadir' => 28,
                'jumlah_siswa_tidak_hadir' => 2,
                'status_guru' => 'Hadir',
                'catatan' => 'Kondisi kelas kondusif',
            ]);

            // Create Attendance records for sample students in this journal
            $studentsInClass = \App\Models\Siswa::where('id_kelas', $targetKelas->id_kelas)->get();
            foreach ($studentsInClass as $stIndex => $st) {
                $status = 'Hadir';
                if ($stIndex == 0 && $j % 5 == 0) $status = 'Alpha';
                elseif ($stIndex == 1 && $j % 3 == 0) $status = 'Izin';
                elseif ($stIndex == 2 && $j % 4 == 0) $status = 'Sakit';

                \App\Models\PresensiSiswa::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_siswa' => $st->id_siswa,
                    'status' => $status,
                    'keterangan' => ($status != 'Hadir') ? 'Keterangan ' . $status : null,
                ]);
            }
        }

        // Seed Activity Logs
        \App\Models\User::all();
        \Illuminate\Support\Facades\DB::table('log_aktivitas')->insert([
            [
                'user_id' => $userMapel->id,
                'aksi' => 'Mengisi Jurnal',
                'deskripsi' => 'Bu Sari mengisi jurnal & absensi XII RPL 1',
                'model_type' => 'JurnalMengajar',
                'model_id' => 1,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0',
                'created_at' => now()->subMinutes(15),
                'updated_at' => now()->subMinutes(15),
            ],
            [
                'user_id' => $userWali->id,
                'aksi' => 'Mengisi Jurnal',
                'deskripsi' => 'Pak Ahmad mengisi jurnal & absensi X TKJ 2',
                'model_type' => 'JurnalMengajar',
                'model_id' => 2,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0',
                'created_at' => now()->subMinutes(42),
                'updated_at' => now()->subMinutes(42),
            ],
            [
                'user_id' => $userAdmin->id,
                'aksi' => 'Tambah Siswa',
                'deskripsi' => 'Bu Rina menambahkan data siswa baru',
                'model_type' => 'Siswa',
                'model_id' => 10,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0',
                'created_at' => now()->subHours(1)->subMinutes(8),
                'updated_at' => now()->subHours(1)->subMinutes(8),
            ],
        ]);

        // Seed Jadwal Piket KBM and full 48-class KBM Schedules
        $this->call([
            JadwalPiketKbmSeeder::class,
            JadwalKbmBatch1Seeder::class,
            JadwalKbmBatch2Seeder::class,
            JadwalKbmBatch3Seeder::class,
            JadwalKbmBatch4Seeder::class,
        ]);
    }
}
