<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Guru;
use App\Models\WaliKelas;
use App\Models\Waka;
use App\Models\Kelas;
use App\Models\IzinTerlambat;
use App\Models\Siswa;
use App\Models\JadwalPiketKbm;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EnsurePortalAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['portal.legacy_role_fallback' => true]);
    }

    public function test_guest_accessing_root_redirects_to_login()
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_guest_accessing_portal_route_redirects_to_login()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_logged_in_user_accessing_root_redirects_to_default_portal()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $response = $this->actingAs($admin)->get('/');
        $response->assertRedirect(route('admin.dashboard'));

        $guru = User::where('role', 'guru_mapel')->first() ?? User::factory()->create(['role' => 'guru_mapel', 'is_active' => true]);
        $responseGuru = $this->actingAs($guru)->get('/');
        $responseGuru->assertRedirect(route('guru-mapel.dashboard'));
    }

    public function test_wali_murid_opening_admin_dashboard_returns_403()
    {
        $waliMurid = User::where('role', 'wali_murid')->first() ?? User::factory()->create(['role' => 'wali_murid', 'is_active' => true]);

        $response = $this->actingAs($waliMurid)->get('/admin/dashboard');
        $response->assertStatus(403);
        $response->assertViewIs('errors.403_portal');
        $response->assertSeeText('Akses Portal Ditolak');
    }

    public function test_admin_can_access_any_portal()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $routesToTest = [
            route('admin.dashboard'),
            route('guru-mapel.dashboard'),
            route('wali-kelas.dashboard'),
            route('guru-piket.dashboard'),
            route('satpam.dashboard'),
            route('waka-kurikulum.dashboard'),
            route('waka-sdm.dashboard'),
            route('waka-kesiswaan.dashboard'),
            route('kepala-sekolah.dashboard'),
            route('waka-piket.dispensasi'),
        ];

        foreach ($routesToTest as $r) {
            $response = $this->actingAs($admin)->get($r);
            $response->assertSuccessful();
        }
    }

    public function test_plain_guru_cannot_access_wali_kelas_portal()
    {
        config(['portal.legacy_role_fallback' => false]);

        $user = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id' => $user->id,
            'nip' => '888888888888888001',
            'nama_lengkap' => 'Guru Tanpa Tugas Tambahan',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($user)->get(route('wali-kelas.dashboard'));
        $response->assertStatus(403);
    }

    public function test_guru_with_wali_kelas_can_access_wali_kelas_portal()
    {
        $nip = '888888888888888002';
        $user = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id' => $user->id,
            'nip' => $nip,
            'nama_lengkap' => 'Guru Wali Kelas',
            'status_aktif' => true,
        ]);
        $wali = WaliKelas::create([
            'user_id' => $user->id,
            'nip' => $nip,
            'nama_lengkap' => 'Guru Wali Kelas',
            'status_aktif' => true,
        ]);
        Kelas::create([
            'nama_kelas' => 'XI TEST 1',
            'tingkat' => 'XI',
            'jurusan' => 'RPL',
            'wali_kelas_id' => $wali->id,
            'jumlah_siswa' => 32,
        ]);

        $response = $this->actingAs($user)->get(route('wali-kelas.dashboard'));
        $response->assertSuccessful();
    }

    public function test_jurnal_resource_route_protected()
    {
        $waliMurid = User::where('role', 'wali_murid')->first() ?? User::factory()->create(['role' => 'wali_murid', 'is_active' => true]);
        $response = $this->actingAs($waliMurid)->get('/jurnal');
        $response->assertStatus(403);

        $guru = User::where('role', 'guru_mapel')->first() ?? User::factory()->create(['role' => 'guru_mapel', 'is_active' => true]);
        $responseGuru = $this->actingAs($guru)->get('/jurnal');
        $responseGuru->assertSuccessful();
    }

    public function test_plain_guru_piket_gets_403_on_late_student_confirmation()
    {
        config(['portal.legacy_role_fallback' => false]);

        $guruPiketUser = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id' => $guruPiketUser->id,
            'nip' => '888888888888888003',
            'nama_lengkap' => 'Guru Piket Biasa',
            'status_aktif' => true,
        ]);

        $siswa = Siswa::first();
        $this->assertNotNull($siswa);

        $izin = IzinTerlambat::create([
            'id_siswa'     => $siswa->id_siswa,
            'id_kelas'     => $siswa->id_kelas,
            'tanggal'      => '2026-10-07',
            'jam_masuk'    => '07:20:00',
            'jam_ke_mulai' => 2,
            'alasan'       => 'Hujan deras',
            'status'       => 'Menunggu',
            'diinput_oleh' => $guruPiketUser->id,
        ]);

        // Guru piket biasa mencoba konfirmasi di route waka-piket
        $response = $this->actingAs($guruPiketUser)->post(route('waka-piket.terlambat.konfirmasi', $izin->id), [
            'action' => 'setujui',
        ]);

        // Harus ditolak dengan 403
        $response->assertStatus(403);
    }

    public function test_user_aktif_tanpa_portal_mengakses_root_tidak_loop_dan_menampilkan_403_ramah()
    {
        config(['portal.legacy_role_fallback' => false]);
        $user = User::factory()->create([
            'role' => 'waka',
            'is_active' => true,
        ]);

        $this->assertEquals(0, count($user->availablePortals()));
        $this->assertNull($user->defaultPortal());

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(403);
        $response->assertViewIs('errors.403_portal');
        $response->assertSeeText('Tidak ada portal aktif yang terhubung dengan akun Anda saat ini');
        $response->assertSee(route('logout'));
    }

    public function test_portal_switcher_tidak_muncul_untuk_guru_dengan_satu_portal()
    {
        config(['portal.legacy_role_fallback' => false]);

        $singlePortalUser = User::factory()->create([
            'role' => 'guru_mapel',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id' => $singlePortalUser->id,
            'nip' => '777777777777777001',
            'nama_lengkap' => 'Guru Single Portal',
            'status_aktif' => true,
        ]);

        $this->assertCount(1, $singlePortalUser->availablePortals());

        $response = $this->actingAs($singlePortalUser)->get(route('guru-mapel.dashboard'));
        $response->assertSuccessful();
        $response->assertDontSee('portalSwitcherWrap', false);
        $response->assertDontSee('Beralih Portal');

        // Multi portal: guru + wali kelas
        $wali = WaliKelas::create([
            'user_id' => $singlePortalUser->id,
            'nip' => '777777777777777001',
            'nama_lengkap' => 'Guru Single Portal',
            'status_aktif' => true,
        ]);
        Kelas::create([
            'nama_kelas' => 'XII MULTI 1',
            'tingkat' => 'XII',
            'jurusan' => 'TKJ',
            'wali_kelas_id' => $wali->id,
            'jumlah_siswa' => 30,
        ]);

        $this->assertGreaterThan(1, count($singlePortalUser->availablePortals()));

        $responseMulti = $this->actingAs($singlePortalUser)->get(route('guru-mapel.dashboard'));
        $responseMulti->assertSuccessful();
        $responseMulti->assertSee('portalSwitcherWrap', false);
        $responseMulti->assertSee('Beralih Portal');
    }

    public function test_admin_master_user_bidang_kode_validation_rejects_invalid_codes()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Kirim kode yang tidak valid (misal 'hukum' atau 'bidang_ilegal')
        $response = $this->actingAs($admin)->post(route('admin.master.user.store'), [
            'name' => 'Waka Ilegal',
            'email' => 'waka.ilegal@smkn1boyolangu.sch.id',
            'password' => 'password123',
            'role' => 'waka',
            'waka_bidang_kode' => ['kurikulum', 'bidang_ilegal'],
            'nip' => '999999999999999001',
        ]);

        $response->assertSessionHasErrors(['waka_bidang_kode.1']);

        // Kirim kode yang valid (semua dalam whitelist)
        $responseValid = $this->actingAs($admin)->post(route('admin.master.user.store'), [
            'name' => 'Waka Legal',
            'email' => 'waka.legal@smkn1boyolangu.sch.id',
            'password' => 'password123',
            'role' => 'waka',
            'waka_bidang_kode' => ['kurikulum', 'sdm'],
            'nip' => '999999999999999002',
        ]);

        $responseValid->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'waka.legal@smkn1boyolangu.sch.id']);
    }

    public function test_command_portal_deactivate_demo_waka_dry_run_leaves_db_untouched()
    {
        // Pastikan akun 6, 11, 12, 146 ada dan berstatus is_active = true
        $demoUsers = User::whereIn('id', [6, 11, 12, 146])->get();
        foreach ($demoUsers as $u) {
            $u->update(['is_active' => true]);
        }

        // Jalankan artisan command dengan --dry-run
        $this->artisan('portal:deactivate-demo-waka', ['--dry-run' => true])
            ->expectsOutputToContain('[MODUS SIMULASI / DRY-RUN]')
            ->expectsOutputToContain('Simulasi selesai. Data tetap utuh.')
            ->assertExitCode(0);

        // Verifikasi database TIDAK tersentuh (tetap active = true)
        foreach ($demoUsers as $u) {
            $fresh = User::find($u->id);
            $this->assertTrue((bool)$fresh->is_active);
        }
    }

    public function test_admin_dapat_mengisi_bidang_kode_untuk_guru_dengan_record_waka_tanpa_mengubah_role()
    {
        config(['portal.legacy_role_fallback' => false]);
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Buat akun guru dengan role guru_mapel
        $nip = '789012345678901234';
        $guruUser = User::factory()->create([
            'name'      => 'Guru Rangkap Waka Testing',
            'email'     => 'guru.rangkap.waka@smkn1boyolangu.sch.id',
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);

        $guru = Guru::create([
            'user_id'      => $guruUser->id,
            'nip'          => $nip,
            'nama_lengkap' => 'Guru Rangkap Waka Testing',
            'status_aktif' => true,
        ]);

        $waka = Waka::create([
            'user_id'      => $guruUser->id,
            'nip'          => $nip,
            'nama_lengkap' => 'Guru Rangkap Waka Testing',
            'bidang'       => 'Kurikulum',
            'bidang_kode'  => null,
            'status_aktif' => true,
        ]);

        // Sebelum diedit, akun guru hanya punya portal guru_mengajar
        $this->assertEquals(['guru_mengajar'], $guruUser->availablePortalKeys());

        // Admin update akun guru ini: tetap mengirim role = 'guru_mapel', tapi menyertakan waka_bidang_kode = ['kurikulum', 'sdm']
        $response = $this->actingAs($admin)->put(route('admin.master.user.update', $guruUser->id), [
            'name'             => 'Guru Rangkap Waka Testing',
            'email'            => 'guru.rangkap.waka@smkn1boyolangu.sch.id',
            'role'             => 'guru_mapel', // ROLE TETAP GURU_MAPEL
            'is_active'        => '1',
            'waka_bidang_kode' => ['kurikulum', 'sdm'],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.master.user'));

        // Verifikasi database: role user TIDAK BERUBAH (tetap guru_mapel)
        $guruUser->refresh();
        $this->assertEquals('guru_mapel', $guruUser->role);

        // Verifikasi database: bidang_kode pada waka terisi
        $waka->refresh();
        $this->assertEquals(['kurikulum', 'sdm'], $waka->bidang_kode);

        // Verifikasi resolver: akun guru sekarang otomatis memiliki portal guru_mengajar, waka_kurikulum, dan waka_sdm!
        $portalKeys = $guruUser->availablePortalKeys();
        $this->assertContains('guru_mengajar', $portalKeys);
        $this->assertContains('waka_kurikulum', $portalKeys);
        $this->assertContains('waka_sdm', $portalKeys);
        $this->assertTrue($guruUser->hasPortal('guru_mengajar'));
        $this->assertTrue($guruUser->hasPortal('waka_kurikulum'));
        $this->assertTrue($guruUser->hasPortal('waka_sdm'));
    }
}

