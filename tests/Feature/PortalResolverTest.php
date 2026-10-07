<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Guru;
use App\Models\Waka;
use App\Models\WaliKelas;
use App\Models\Kelas;
use App\Models\JadwalPiketKbm;
use App\Models\TahunAjaran;
use App\Support\PortalResolver;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;

class PortalResolverTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['portal.legacy_role_fallback' => true]);
        config(['portal.piket_scope' => 'hari']);
    }

    public function test_admin_has_admin_portal_and_access_to_all_portals()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $keys = $admin->availablePortalKeys();
        $this->assertContains('admin', $keys);

        // Admin bisa semua portal
        $this->assertTrue($admin->hasPortal('admin'));
        $this->assertTrue($admin->hasPortal('guru_mengajar'));
        $this->assertTrue($admin->hasPortal('wali_kelas'));
        $this->assertTrue($admin->hasPortal('piket'));
        $this->assertTrue($admin->hasPortal('waka_kurikulum'));
        $this->assertTrue($admin->hasPortal('waka_sdm'));
        $this->assertTrue($admin->hasPortal('waka_kesiswaan'));
        $this->assertTrue($admin->hasPortal('satpam'));
        $this->assertTrue($admin->hasPortal('kepala_sekolah'));
        $this->assertTrue($admin->hasPortal('wali_murid'));
    }

    public function test_plain_guru_only_has_guru_mengajar_portal()
    {
        // Cari atau buat guru biasa tanpa tugas tambahan
        $user = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => '999999999999999001',
            'nama_lengkap' => 'Guru Biasa Testing',
            'status_aktif' => true,
        ]);

        $keys = $user->availablePortalKeys();

        $this->assertEquals(['guru_mengajar'], $keys);
        $this->assertTrue($user->hasPortal('guru_mengajar'));
        $this->assertFalse($user->hasPortal('wali_kelas'));
        $this->assertFalse($user->hasPortal('piket'));
        $this->assertFalse($user->hasPortal('waka_kurikulum'));
        $this->assertFalse($user->hasPortal('admin'));
    }

    public function test_guru_who_is_wali_kelas_has_wali_kelas_and_guru_mengajar()
    {
        $nip = '999999999999999002';
        $user = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => $nip,
            'nama_lengkap' => 'Guru Wali Kelas Testing',
            'status_aktif' => true,
        ]);

        $wali = WaliKelas::create([
            'user_id' => $user->id,
            'nip' => $nip,
            'nama_lengkap' => 'Guru Wali Kelas Testing',
            'status_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'X TEST 1',
            'tingkat' => 'X',
            'jurusan' => 'TKJ',
            'wali_kelas_id' => $wali->id,
            'jumlah_siswa' => 30,
        ]);

        $keys = $user->availablePortalKeys();

        $this->assertContains('guru_mengajar', $keys);
        $this->assertContains('wali_kelas', $keys);
        $this->assertTrue($user->hasPortal('wali_kelas'));
        $this->assertTrue($user->hasPortal('guru_mengajar'));
    }

    public function test_guru_scheduled_piket_today_has_piket_portal_and_not_on_other_days()
    {
        $ta = TahunAjaran::aktif()->first();
        $this->assertNotNull($ta);

        // Pilih tanggal tes (Senin)
        $datePiket = Carbon::create(2026, 9, 7); // Senin, Siklus A
        $siklus = JadwalPiketKbm::getSiklusForDate($datePiket);
        $this->assertEquals('A', $siklus);

        $user = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => '999999999999999003',
            'nama_lengkap' => 'Guru Piket Testing',
            'status_aktif' => true,
        ]);

        // Jadwalkan piket pada hari Senin Siklus A
        $jadwal = JadwalPiketKbm::create([
            'tahun_ajaran' => $ta->nama,
            'semester' => $ta->semester,
            'siklus' => 'A',
            'hari' => 'Senin',
            'shift' => 'Pagi',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '11:00:00',
            'peran' => 'koordinator',
            'urutan' => 1,
            'id_guru' => $guru->id_guru,
            'nama_guru' => $guru->nama_lengkap,
            'nip' => $guru->nip,
        ]);

        // Nonaktifkan legacy role fallback agar murni menguji deteksi jadwal
        config(['portal.legacy_role_fallback' => false]);

        // Pada tanggal tugas piket
        $portalsOnDuty = PortalResolver::resolve($user, $datePiket);
        $this->assertArrayHasKey('piket', $portalsOnDuty);
        $this->assertEquals('Shift Pagi (Koordinator)', $portalsOnDuty['piket']['badge']);

        // Pada hari lain (Selasa, 8 Sep 2026)
        $dateOther = Carbon::create(2026, 9, 8);
        $portalsOther = PortalResolver::resolve($user, $dateOther);
        $this->assertArrayNotHasKey('piket', $portalsOther);
    }

    public function test_waka_piket_duty_today_matches_by_nip_only()
    {
        $ta = TahunAjaran::aktif()->first();
        $datePiket = Carbon::create(2026, 9, 7); // Senin Siklus A

        $wakaUser = User::factory()->create([
            'role' => 'waka',
            'name' => 'Waka Piket Bertugas',
            'is_active' => true,
        ]);
        $guruWaka = Guru::create([
            'user_id' => $wakaUser->id,
            'nip' => '999999999999999004',
            'nama_lengkap' => 'Waka Piket Bertugas',
            'status_aktif' => true,
        ]);

        // Buat jadwal dengan piket_waka_nip yang cocok
        JadwalPiketKbm::create([
            'tahun_ajaran' => $ta->nama,
            'semester' => $ta->semester,
            'siklus' => 'A',
            'hari' => 'Senin',
            'shift' => 'Pagi',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '11:00:00',
            'peran' => 'koordinator',
            'urutan' => 1,
            'id_guru' => $guruWaka->id_guru,
            'nama_guru' => $guruWaka->nama_lengkap,
            'nip' => $guruWaka->nip,
            'piket_waka_nama' => 'Waka Piket Bertugas',
            'piket_waka_nip' => $guruWaka->nip,
        ]);

        config(['portal.legacy_role_fallback' => false]);

        // User bertugas memiliki portal piket_waka
        $portalsDuty = PortalResolver::resolve($wakaUser, $datePiket);
        $this->assertArrayHasKey('piket_waka', $portalsDuty);

        // User lain yang namanya mirip tapi NIP beda TIDAK boleh dapat portal piket_waka
        $otherUser = User::factory()->create([
            'role' => 'guru_mapel',
            'name' => 'Waka Piket Bertugas Mirip',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id' => $otherUser->id,
            'nip' => '999999999999999099',
            'nama_lengkap' => 'Waka Piket Bertugas Mirip',
            'status_aktif' => true,
        ]);

        $portalsOther = PortalResolver::resolve($otherUser, $datePiket);
        $this->assertArrayNotHasKey('piket_waka', $portalsOther);
    }

    public function test_waka_bidang_kode_mapping_to_portals()
    {
        config(['portal.legacy_role_fallback' => false]);

        // 1. Kurikulum (Hardini Indahing Budi)
        $userKur = User::whereHas('waka', fn($q) => $q->where('nip', '198208222014072002'))->first();
        $this->assertNotNull($userKur, 'User Waka Kurikulum tidak ditemukan');
        $this->assertTrue($userKur->hasPortal('waka_kurikulum'));
        $this->assertFalse($userKur->hasPortal('waka_sdm'));
        $this->assertFalse($userKur->hasPortal('waka_kesiswaan'));

        // 2. Niken: bk & sdm -> waka_sdm (tanpa portal waka bk)
        $userSdm = User::whereHas('waka', fn($q) => $q->where('nip', '198203032009012009'))->first();
        $this->assertNotNull($userSdm, 'User Waka SDM tidak ditemukan');
        $this->assertTrue($userSdm->hasPortal('waka_sdm'));
        $this->assertFalse($userSdm->hasPortal('waka_kurikulum'));

        // 3. Setiyo: kesiswaan -> waka_kesiswaan
        $userKesiswaan = User::whereHas('waka', fn($q) => $q->where('nip', '197210302003121002'))->first();
        $this->assertNotNull($userKesiswaan, 'User Waka Kesiswaan tidak ditemukan');
        $this->assertTrue($userKesiswaan->hasPortal('waka_kesiswaan'));

        // 4. Fajar: kedisiplinan -> waka_kesiswaan
        $userKedisiplinan = User::whereHas('waka', fn($q) => $q->where('nip', '197808102023211005'))->first();
        $this->assertNotNull($userKedisiplinan, 'User Waka Kedisiplinan tidak ditemukan');
        $this->assertTrue($userKedisiplinan->hasPortal('waka_kesiswaan'));

        // 5. Hendro: sarpras -> tidak ada portal waka
        $userSarpras = User::whereHas('waka', fn($q) => $q->where('nip', '197711122022211007'))->first();
        $this->assertNotNull($userSarpras, 'User Waka Sarpras tidak ditemukan');
        $this->assertFalse($userSarpras->hasPortal('waka_kurikulum'));
        $this->assertFalse($userSarpras->hasPortal('waka_sdm'));
        $this->assertFalse($userSarpras->hasPortal('waka_kesiswaan'));
    }

    public function test_waka_with_empty_bidang_kode_gets_no_waka_portal_and_logs_warning()
    {
        config(['portal.legacy_role_fallback' => false]);
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'bidang_kode kosong');
            });

        $user = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => '999999999999999005',
            'nama_lengkap' => 'Waka Kosong Kode',
            'status_aktif' => true,
        ]);
        Waka::create([
            'user_id' => $user->id,
            'nip' => $guru->nip,
            'nama_lengkap' => 'Waka Kosong Kode',
            'bidang' => 'Kurikulum',
            'bidang_kode' => null, // Kosong
            'status_aktif' => true,
        ]);

        $portals = PortalResolver::resolve($user);

        $this->assertArrayNotHasKey('waka_kurikulum', $portals);
        $this->assertArrayNotHasKey('waka_sdm', $portals);
        $this->assertArrayNotHasKey('waka_kesiswaan', $portals);
    }

    public function test_inactive_waka_or_user_gets_no_waka_portal()
    {
        config(['portal.legacy_role_fallback' => false]);

        $user = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => '999999999999999006',
            'nama_lengkap' => 'Waka Nonaktif Testing',
            'status_aktif' => true,
        ]);
        $waka = Waka::create([
            'user_id' => $user->id,
            'nip' => $guru->nip,
            'nama_lengkap' => 'Waka Nonaktif Testing',
            'bidang' => 'Kurikulum',
            'bidang_kode' => ['kurikulum'],
            'status_aktif' => false, // Nonaktif
        ]);

        $portals = PortalResolver::resolve($user);
        $this->assertArrayNotHasKey('waka_kurikulum', $portals);

        // Kasus users.is_active = false
        $waka->update(['status_aktif' => true]);
        $user->update(['is_active' => false]);

        $portalsUserNonaktif = PortalResolver::resolve($user);
        $this->assertEmpty($portalsUserNonaktif);
    }

    public function test_waka_record_without_nip_in_guru_table_gets_no_waka_portal()
    {
        config(['portal.legacy_role_fallback' => false]);

        $user = User::factory()->create([
            'role' => 'waka',
            'is_active' => true,
        ]);
        Waka::create([
            'user_id' => $user->id,
            'nip' => 'WAKA-DEMO-TEST', // Tidak ada di tabel guru
            'nama_lengkap' => 'Waka Demo',
            'bidang' => 'Kurikulum',
            'bidang_kode' => ['kurikulum'],
            'status_aktif' => true,
        ]);

        $portals = PortalResolver::resolve($user);
        $this->assertArrayNotHasKey('waka_kurikulum', $portals);
    }

    public function test_wali_murid_only_has_wali_murid_portal()
    {
        $waliMurid = User::factory()->create([
            'role' => 'wali_murid',
            'is_active' => true,
        ]);

        $keys = $waliMurid->availablePortalKeys();
        $this->assertEquals(['wali_murid'], $keys);
        $this->assertTrue($waliMurid->hasPortal('wali_murid'));
        $this->assertFalse($waliMurid->hasPortal('admin'));
    }

    public function test_jadwal_piket_semester_lain_tidak_memberikan_portal_piket()
    {
        $ta = TahunAjaran::aktif()->first();
        $this->assertNotNull($ta);
        $this->assertEquals('Ganjil', $ta->semester);

        $datePiket = Carbon::create(2026, 9, 7); // Senin, Siklus A

        $user = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => '999999999999999077',
            'nama_lengkap' => 'Guru Beda Semester',
            'status_aktif' => true,
        ]);

        // Buat jadwal untuk semester GENAP (berbeda dari semester aktif GANJIL)
        JadwalPiketKbm::create([
            'tahun_ajaran' => $ta->nama,
            'semester'     => 'Genap', // Semester lain!
            'siklus'       => 'A',
            'hari'         => 'Senin',
            'shift'        => 'Pagi',
            'jam_mulai'    => '07:00:00',
            'jam_selesai'  => '11:00:00',
            'peran'        => 'petugas',
            'urutan'       => 1,
            'id_guru'      => $guru->id_guru,
            'nama_guru'    => $guru->nama_lengkap,
            'nip'          => $guru->nip,
        ]);

        config(['portal.legacy_role_fallback' => false]);

        $portals = PortalResolver::resolve($user, $datePiket);
        $this->assertArrayNotHasKey('piket', $portals);
    }

    public function test_akun_waka_piket_shared_hanya_punya_piket_waka_dan_tidak_punya_guru_mengajar()
    {
        $sharedUser = User::where('email', 'waka.piket@smkn1boyolangu.sch.id')->first();
        $this->assertNotNull($sharedUser, 'Akun waka.piket@smkn1boyolangu.sch.id tidak ditemukan di database.');

        $keys = $sharedUser->availablePortalKeys();

        // Hanya punya piket_waka
        $this->assertEquals(['piket_waka'], $keys);
        $this->assertTrue($sharedUser->hasPortal('piket_waka'));
        $this->assertFalse($sharedUser->hasPortal('guru_mengajar'));
        $this->assertFalse($sharedUser->isGuru());
    }
}

