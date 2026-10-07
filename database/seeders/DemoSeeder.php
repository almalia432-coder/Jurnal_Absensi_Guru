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

/**
 * Seeder akun demo — hanya dijalankan di environment 'local' dan 'testing'.
 *
 * Akun yang dipindah dari DatabaseSeeder ke sini (semuanya memakai password 'password'):
 *   - admin@smkn1boyolangu.sch.id         (role: admin)
 *   - walikelas@smkn1boyolangu.sch.id      (role: wali_kelas)
 *   - gurupiket@smkn1boyolangu.sch.id      (role: guru_piket)
 *   - gurumapel@smkn1boyolangu.sch.id      (role: guru_mapel)
 *   - kepsek@smkn1boyolangu.sch.id         (role: kepala_sekolah)
 *   - wakakurikulum@smkn1boyolangu.sch.id  (role: waka)
 *   - wakasdm@smkn1boyolangu.sch.id        (role: waka)
 *   - walimurid@smkn1boyolangu.sch.id      (role: wali_murid)
 *   - satpam@smkn1boyolangu.sch.id         (role: satpam)
 *   - waka.piket@smkn1boyolangu.sch.id     (role: waka — akun dinas bersama meja piket)
 *
 * Password 'password' TIDAK BOLEH ada di seeder production.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            $this->command->warn('DemoSeeder dilewati: bukan environment local/testing.');
            return;
        }

        // 1. Admin
        $userAdmin = User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);
        Admin::create([
            'user_id'       => $userAdmin->id,
            'nip'           => '198001012005011001',
            'nama_lengkap'  => 'Administrator Utama',
            'no_hp'         => '081234567890',
        ]);

        // 2. Wali Kelas
        $userWali = User::create([
            'name'      => 'Drs. Ahmad Fauzi, M.Pd (Wali Kelas)',
            'email'     => 'walikelas@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'wali_kelas',
            'is_active' => true,
        ]);
        WaliKelas::create([
            'user_id'      => $userWali->id,
            'nip'          => '197502122003121002',
            'nama_lengkap' => 'Drs. Ahmad Fauzi, M.Pd',
            'jenis_kelamin' => 'L',
            'no_hp'        => '081234567891',
        ]);

        // 3. Guru Piket (Akun Bersama Petugas Piket)
        $userPiket = User::create([
            'name'      => 'Petugas Piket',
            'email'     => 'gurupiket@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'guru_piket',
            'is_active' => true,
        ]);
        GuruPiket::create([
            'user_id'      => $userPiket->id,
            'nip'          => 'PIKET-SMKN1',
            'nama_lengkap' => 'Petugas Piket',
            'jenis_kelamin' => 'L',
            'no_hp'        => '081234567892',
            'hari_piket'   => 'Senin s/d Jumat',
        ]);

        // 4. Guru Mapel
        $userMapel = User::create([
            'name'      => 'Siti Rahayu, S.Kom (Guru Mapel)',
            'email'     => 'gurumapel@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        GuruMapel::create([
            'user_id'      => $userMapel->id,
            'nip'          => '198805202012022004',
            'nama_lengkap' => 'Siti Rahayu, S.Kom',
            'jenis_kelamin' => 'P',
            'no_hp'        => '081234567893',
        ]);

        // 5. Kepala Sekolah
        $userKepsek = User::create([
            'name'      => 'Dr. H. Supriyanto, M.Pd (Kepala Sekolah)',
            'email'     => 'kepsek@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'kepala_sekolah',
            'is_active' => true,
        ]);
        KepalaSekolah::create([
            'user_id'         => $userKepsek->id,
            'nip'             => '196808101994031005',
            'nama_lengkap'    => 'Dr. H. Supriyanto, M.Pd',
            'jenis_kelamin'   => 'L',
            'no_hp'           => '081234567894',
            'periode_jabatan' => '2022-2026',
        ]);

        // 6. Waka Kurikulum
        $userWakaKurikulum = User::create([
            'name'      => 'Budi Santoso, M.T (Waka Kurikulum)',
            'email'     => 'wakakurikulum@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'waka',
            'is_active' => true,
        ]);
        Waka::create([
            'user_id'      => $userWakaKurikulum->id,
            'nip'          => '197911042006041006',
            'nama_lengkap' => 'Budi Santoso, M.T',
            'jenis_kelamin' => 'L',
            'no_hp'        => '081234567895',
            'bidang'       => 'Kurikulum',
        ]);

        // 7. Waka SDM
        $userWakaSdm = User::create([
            'name'      => 'Dr. Hendra Wijaya, M.Pd (Waka SDM)',
            'email'     => 'wakasdm@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'waka',
            'is_active' => true,
        ]);
        Waka::create([
            'user_id'      => $userWakaSdm->id,
            'nip'          => '198103152008011009',
            'nama_lengkap' => 'Dr. Hendra Wijaya, M.Pd',
            'jenis_kelamin' => 'L',
            'no_hp'        => '081234567897',
            'bidang'       => 'SDM',
        ]);

        // 8. Wali Murid
        User::create([
            'name'      => 'Bapak/Ibu Wali Murid',
            'email'     => 'walimurid@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'wali_murid',
            'is_active' => true,
        ]);

        // 9. Satpam
        $userSatpam = User::create([
            'name'      => 'Agus Setiawan (Satpam Gate)',
            'email'     => 'satpam@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'satpam',
            'is_active' => true,
        ]);
        Satpam::create([
            'user_id'      => $userSatpam->id,
            'nip'          => '9900112233',
            'nama_lengkap' => 'Agus Setiawan',
            'jenis_kelamin' => 'L',
            'no_hp'        => '081234567896',
            'pos_jaga'     => 'Gerbang Utama',
        ]);

        // 10. Akun Bersama Meja Piket Waka (ID historis 146 — waka.piket@)
        $userWakaPiket = User::create([
            'name'      => 'Petugas Meja Piket Waka',
            'email'     => 'waka.piket@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'waka',
            'is_active' => true,
        ]);
        Waka::create([
            'user_id'      => $userWakaPiket->id,
            'nip'          => 'WAKA-PIKET-BERSAMA',
            'nama_lengkap' => 'Petugas Meja Piket Waka',
            'jenis_kelamin' => 'L',
            'no_hp'        => '081234560000',
            'bidang'       => 'Piket KBM',
        ]);
    }
}
