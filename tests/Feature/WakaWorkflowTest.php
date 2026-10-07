<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DispensasiSiswa;
use App\Models\JadwalPiketKbm;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WakaWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_waka_sdm_redirect()
    {
        $wakaSdmUser = User::where('role', 'waka')->whereHas('waka', fn($q) => $q->where('bidang', 'SDM'))->first();
        $this->assertNotNull($wakaSdmUser, 'User Waka SDM tidak ditemukan.');

        $this->assertTrue($wakaSdmUser->isWakaSdm());
        $this->assertTrue($wakaSdmUser->hasTeachingDuty());

        // Login with unified role 'waka'
        $response = $this->post('/login', [
            'role' => 'waka',
            'username' => $wakaSdmUser->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('waka-sdm.dashboard'));
    }

    public function test_waka_kurikulum_redirect()
    {
        $wakaKurUser = User::where('role', 'waka')->whereHas('waka', fn($q) => $q->where('bidang', 'Kurikulum'))->first();
        $this->assertNotNull($wakaKurUser, 'User Waka Kurikulum tidak ditemukan.');

        $this->assertTrue($wakaKurUser->isWakaKurikulum());
        $this->assertTrue($wakaKurUser->hasTeachingDuty());

        // Login with unified role 'waka'
        $response = $this->post('/login', [
            'role' => 'waka',
            'username' => $wakaKurUser->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('waka-kurikulum.dashboard'));
    }

    public function test_waka_piket_shared_account_has_no_teaching_duty()
    {
        $sharedPiket = User::where('email', 'waka.piket@smkn1boyolangu.sch.id')->first();
        $this->assertNotNull($sharedPiket, 'User Waka Piket tidak ditemukan.');

        $this->assertTrue($sharedPiket->isWaka());
        $this->assertFalse($sharedPiket->hasTeachingDuty());
        $this->assertFalse($sharedPiket->isGuru());

        // Login as shared piket must redirect to waka-piket.dispensasi
        $response = $this->post('/login', [
            'role' => 'waka_piket',
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
        $fajarUser = User::where('email', 'fajar.luthfianto@smkn1boyolangu.sch.id')->first()
            ?? User::where('role', 'waka')->whereHas('waka', fn($q) => $q->where('bidang', 'like', '%kedisiplinan%'))->first();
        $this->assertNotNull($fajarUser, 'User Waka Kedisiplinan / Fajar tidak ditemukan.');

        $this->assertTrue($fajarUser->isWakaKesiswaan());
        $this->assertTrue($fajarUser->hasTeachingDuty());

        // Login as Pak Fajar with unified role 'waka' must redirect to waka-kesiswaan.dashboard (NOT waka-piket.dispensasi)
        $response = $this->post('/login', [
            'role' => 'waka',
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

        // Check Pak Fajar visiting Guru Mapel dashboard sees Portal Waka Kesiswaan switch button
        $guruMapelResponse = $this->actingAs($fajarUser)->get(route('guru-mapel.dashboard'));
        $guruMapelResponse->assertStatus(200);
        $guruMapelResponse->assertSee('Portal Waka Kesiswaan');
        $guruMapelResponse->assertSee(route('waka-kesiswaan.dashboard'));
        // Ensure NO Tugas Waka Piket menu in guru mapel sidebar
        $guruMapelResponse->assertDontSee('Tugas Waka Piket');
    }
}
