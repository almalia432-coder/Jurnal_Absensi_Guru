<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
<<<<<<< HEAD
use App\Models\Guru;
use App\Models\Waka;
use App\Models\Kelas;
=======
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
use App\Models\DispensasiSiswa;
use App\Models\JadwalPiketKbm;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * WakaWorkflowTest — dibuat self-contained: setiap test membuat user-nya sendiri
 * dengan data guru + waka yang lengkap (bidang_kode, nip di tabel guru).
 * Menggunakan DatabaseTransactions sehingga semua data dibersihkan setelah test.
 */
=======

>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
class WakaWorkflowTest extends TestCase
{
    use DatabaseTransactions;

<<<<<<< HEAD
    /**
     * Buat waka user yang lengkap: User + record waka (bidang_kode diisi)
     * + record guru (wajib agar PortalResolver::resolve L89 lolos $nipInGuru check).
     */
    private function makeWakaUser(string $email, string $bidang, array $bidangKode): User
    {
        // NIP test maksimal 20 karakter (sesuai batas kolom)
        $nip = 'WK-' . substr(md5($email . $bidang), 0, 17);

        $user = User::factory()->create([
            'email'     => $email,
            'password'  => Hash::make('password'),
            'role'      => 'waka',
            'is_active' => true,
        ]);

        // Guru record wajib (PortalResolver cek nipInGuru sebelum beri portal waka)
        Guru::create([
            'user_id'      => $user->id,
            'nip'          => $nip,
            'nama_lengkap' => $user->name,
            'status_aktif' => true,
        ]);

        Waka::create([
            'user_id'      => $user->id,
            'nip'          => $nip,
            'nama_lengkap' => $user->name,
            'jenis_kelamin' => 'L',
            'bidang'       => $bidang,
            'bidang_kode'  => json_encode($bidangKode),
            'status_aktif' => true,
        ]);

        return $user;
    }

    public function test_waka_sdm_redirect()
    {
        $wakaSdmUser = $this->makeWakaUser(
            'test.waka.sdm@smkn1boyolangu.sch.id',
            'SDM',
            ['sdm']
        );
=======
    public function test_waka_sdm_redirect()
    {
        $wakaSdmUser = User::where('role', 'waka')->whereHas('waka', fn($q) => $q->where('bidang', 'SDM'))->first();
        $this->assertNotNull($wakaSdmUser, 'User Waka SDM tidak ditemukan.');
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f

        $this->assertTrue($wakaSdmUser->isWakaSdm());
        $this->assertTrue($wakaSdmUser->hasTeachingDuty());

<<<<<<< HEAD
        // Login tanpa field role — AuthController baru tidak memerlukan role
        $response = $this->post('/login', [
=======
        // Login with unified role 'waka'
        $response = $this->post('/login', [
            'role' => 'waka',
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
            'username' => $wakaSdmUser->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('waka-sdm.dashboard'));
    }

    public function test_waka_kurikulum_redirect()
    {
<<<<<<< HEAD
        $wakaKurUser = $this->makeWakaUser(
            'test.waka.kurikulum@smkn1boyolangu.sch.id',
            'Kurikulum',
            ['kurikulum']
        );
=======
        $wakaKurUser = User::where('role', 'waka')->whereHas('waka', fn($q) => $q->where('bidang', 'Kurikulum'))->first();
        $this->assertNotNull($wakaKurUser, 'User Waka Kurikulum tidak ditemukan.');
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f

        $this->assertTrue($wakaKurUser->isWakaKurikulum());
        $this->assertTrue($wakaKurUser->hasTeachingDuty());

<<<<<<< HEAD
        // Login tanpa field role
        $response = $this->post('/login', [
=======
        // Login with unified role 'waka'
        $response = $this->post('/login', [
            'role' => 'waka',
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
            'username' => $wakaKurUser->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('waka-kurikulum.dashboard'));
    }

    public function test_waka_piket_shared_account_has_no_teaching_duty()
    {
<<<<<<< HEAD
        // Cari akun waka.piket yang sudah ada di DB (akun historis)
=======
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
        $sharedPiket = User::where('email', 'waka.piket@smkn1boyolangu.sch.id')->first();
        $this->assertNotNull($sharedPiket, 'User Waka Piket tidak ditemukan.');

        $this->assertTrue($sharedPiket->isWaka());
        $this->assertFalse($sharedPiket->hasTeachingDuty());
        $this->assertFalse($sharedPiket->isGuru());

<<<<<<< HEAD
        // Login — akun ini punya portal piket_waka via resolvePiketWaka() legacy fallback
        $response = $this->post('/login', [
=======
        // Login as shared piket must redirect to waka-piket.dispensasi
        $response = $this->post('/login', [
            'role' => 'waka_piket',
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
            'username' => 'waka.piket@smkn1boyolangu.sch.id',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('waka-piket.dispensasi'));

        // View waka-piket page: must NOT have 'Portal Guru Mapel'
        $pageResponse = $this->actingAs($sharedPiket)->get(route('waka-piket.dispensasi'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertDontSee('Portal Guru Mapel');
    }

    public function test_waka_kesiswaan_and_kedisiplinan_portal()
    {
<<<<<<< HEAD
        // Buat waka kesiswaan self-contained dengan bidang_kode ['kesiswaan', 'kedisiplinan']
        $fajarUser = $this->makeWakaUser(
            'test.fajar.kesiswaan@smkn1boyolangu.sch.id',
            'Kesiswaan & Kedisiplinan',
            ['kesiswaan', 'kedisiplinan']
        );
=======
        $fajarUser = User::where('email', 'fajar.luthfianto@smkn1boyolangu.sch.id')->first()
            ?? User::where('role', 'waka')->whereHas('waka', fn($q) => $q->where('bidang', 'like', '%kedisiplinan%'))->first();
        $this->assertNotNull($fajarUser, 'User Waka Kedisiplinan / Fajar tidak ditemukan.');
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f

        $this->assertTrue($fajarUser->isWakaKesiswaan());
        $this->assertTrue($fajarUser->hasTeachingDuty());

<<<<<<< HEAD
        // Login — harus redirect ke waka-kesiswaan.dashboard (NOT waka-piket.dispensasi)
        $response = $this->post('/login', [
=======
        // Login as Pak Fajar with unified role 'waka' must redirect to waka-kesiswaan.dashboard (NOT waka-piket.dispensasi)
        $response = $this->post('/login', [
            'role' => 'waka',
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
            'username' => $fajarUser->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('waka-kesiswaan.dashboard'));

        // Check all 4 Waka Kesiswaan routes
        $dashboardResponse = $this->actingAs($fajarUser)->get(route('waka-kesiswaan.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard Kesiswaan & Kedisiplinan');
        $dashboardResponse->assertSee('btn-portal-switch');
        $dashboardResponse->assertSee('Portal Guru Mapel');
        // Ensure NO Jadwal Piket KBM menu in waka kesiswaan sidebar
        $dashboardResponse->assertDontSee('Jadwal Piket KBM');

        $presensiResponse = $this->actingAs($fajarUser)->get(route('waka-kesiswaan.presensi'));
        $presensiResponse->assertStatus(200);

        $dispensasiResponse = $this->actingAs($fajarUser)->get(route('waka-kesiswaan.dispensasi'));
        $dispensasiResponse->assertStatus(200);

        $kedisiplinanResponse = $this->actingAs($fajarUser)->get(route('waka-kesiswaan.kedisiplinan'));
        $kedisiplinanResponse->assertStatus(200);

<<<<<<< HEAD
        // Check visiting Guru Mapel dashboard: sees Portal Waka Kesiswaan switch button
=======
        // Check Pak Fajar visiting Guru Mapel dashboard sees Portal Waka Kesiswaan switch button
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
        $guruMapelResponse = $this->actingAs($fajarUser)->get(route('guru-mapel.dashboard'));
        $guruMapelResponse->assertStatus(200);
        $guruMapelResponse->assertSee('Portal Waka Kesiswaan');
        $guruMapelResponse->assertSee(route('waka-kesiswaan.dashboard'));
        // Ensure NO Tugas Waka Piket menu in guru mapel sidebar
        $guruMapelResponse->assertDontSee('Tugas Waka Piket');
    }
}
