<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\DispensasiSiswa;
use App\Models\JadwalPiketKbm;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Tes alur dispensasi dua tahap:
 *   Menunggu → Disetujui_Piket → Disetujui → Selesai
 *
 * Semua tes membuat data sendiri; tidak bergantung pada DatabaseSeeder.
 * Menggunakan DatabaseTransactions: rollback otomatis setelah setiap tes.
 */
class DispensasiDuaTahapWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private User $guruPiketUser;
    private User $wakaUser;
    private User $adminUser;
    private Siswa $siswa;
    private string $testNip;
    private string $wakaNip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testNip = '999TEST' . rand(1000, 9999);
        $this->wakaNip = '999WAKA' . rand(1000, 9999);

        // Guru Piket user + guru record
        $this->guruPiketUser = User::create([
            'name'      => 'Guru Piket Test',
            'email'     => 'pikettest' . rand(1000, 9999) . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'guru_piket',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id'       => $this->guruPiketUser->id,
            'nip'           => $this->testNip,
            'nama_lengkap'  => 'Guru Piket Test',
            'jenis_kelamin' => 'L',
            'status_aktif'  => true,
        ]);

        // Waka Piket user + guru record
        $this->wakaUser = User::create([
            'name'      => 'Waka Piket Test',
            'email'     => 'wakatest' . rand(1000, 9999) . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'waka',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id'       => $this->wakaUser->id,
            'nip'           => $this->wakaNip,
            'nama_lengkap'  => 'Waka Piket Test',
            'jenis_kelamin' => 'L',
            'status_aktif'  => true,
        ]);

        // Admin user
        $this->adminUser = User::create([
            'name'      => 'Admin Test',
            'email'     => 'admintest' . rand(1000, 9999) . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Kelas + Siswa
        $kelas = Kelas::first() ?? Kelas::create([
            'nama_kelas' => 'XII-TEST',
            'tingkat'    => '12',
            'jurusan'    => 'RPL',
        ]);

        $this->siswa = Siswa::create([
            'nama_lengkap' => 'Siswa Dispensasi Test',
            'nisn'         => 'NISN' . rand(100000, 999999),
            'nis'          => 'NIS' . rand(10000, 99999),
            'id_kelas'     => $kelas->id_kelas,
            'jenis_kelamin' => 'L',
            'status_aktif' => true,
        ]);
    }

    /**
     * Buat jadwal piket KBM supaya guru piket dan waka piket terdaftar pada tanggal tertentu.
     */
    private function seedRosterForDate(Carbon $date): void
    {
        $siklus = JadwalPiketKbm::getSiklusForDate($date);
        $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat'];
        $hari = $dayNames[$date->dayOfWeekIso] ?? 'Senin';

        JadwalPiketKbm::create([
            'tahun_ajaran'   => '2026/2027',
            'semester'       => 'Ganjil',
            'siklus'         => $siklus,
            'hari'           => $hari,
            'shift'          => 'Pagi',
            'jam_mulai'      => '07:00',
            'jam_selesai'    => '12:00',
            'peran'          => 'petugas',
            'urutan'         => 1,
            'id_guru'        => $this->guruPiketUser->guru->id_guru,
            'nama_guru'      => 'Guru Piket Test',
            'nip'            => $this->testNip,
            'piket_waka_nama' => 'Waka Piket Test',
            'piket_waka_nip'  => $this->wakaNip,
        ]);
    }

    /**
     * Buat dispensasi dengan status tertentu.
     */
    private function createDispensasi(string $status = 'Menunggu', ?Carbon $tanggal = null): DispensasiSiswa
    {
        $tanggal = $tanggal ?? Carbon::today();

        return DispensasiSiswa::create([
            'id_siswa'    => $this->siswa->id_siswa,
            'tanggal'     => $tanggal->format('Y-m-d'),
            'jam_keluar'  => '09:00:00',
            'jam_kembali' => null,
            'alasan'      => 'Keperluan test dispensasi',
            'status'      => $status,
            'diinput_oleh' => $this->guruPiketUser->id,
        ]);
    }

    // ─── TAHAP 1: GURU PIKET ──────────────────────────────────────

    public function test_guru_piket_setujui_tahap_1_menjadi_disetujui_piket()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Menunggu', $tanggal);

        $response = $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action'  => 'setujui',
                'catatan' => 'Lengkap',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $disp->refresh();
        $this->assertEquals('Disetujui_Piket', $disp->status);
        $this->assertEquals($this->guruPiketUser->id, $disp->piket_approved_by);
        $this->assertNotNull($disp->piket_at);
        $this->assertEquals('Lengkap', $disp->piket_catatan);
    }

    public function test_waka_piket_setujui_tahap_2_menjadi_disetujui()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Disetujui_Piket', $tanggal);

        $response = $this->actingAs($this->wakaUser)
            ->post(route('waka-piket.dispensasi.status', $disp->id), [
                'action'  => 'setujui',
                'catatan' => 'OK',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $disp->refresh();
        $this->assertEquals('Disetujui', $disp->status);
        $this->assertEquals($this->wakaUser->id, $disp->disetujui_oleh);
        $this->assertNotNull($disp->tanggal_persetujuan);
        $this->assertEquals('OK', $disp->catatan_waka);
    }

    public function test_waka_piket_tidak_bisa_setujui_dari_status_menunggu()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Menunggu', $tanggal);

        $response = $this->actingAs($this->wakaUser)
            ->post(route('waka-piket.dispensasi.status', $disp->id), [
                'action' => 'setujui',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $disp->refresh();
        $this->assertEquals('Menunggu', $disp->status, 'Status harus tetap Menunggu — waka tidak boleh langsung setujui.');
    }

    public function test_guru_piket_bukan_bertugas_ditolak_otorisasi()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        // TIDAK seedRoster — user tidak terdaftar di jadwal piket tanggal ini.

        $disp = $this->createDispensasi('Menunggu', $tanggal);

        $response = $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action' => 'setujui',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $disp->refresh();
        $this->assertEquals('Menunggu', $disp->status);
    }

    // ─── TOLAK ────────────────────────────────────────────────────

    public function test_tolak_oleh_guru_piket_dari_menunggu()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Menunggu', $tanggal);

        $response = $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action'  => 'tolak',
                'catatan' => 'Alasan tidak valid untuk dispensasi',
            ]);

        $response->assertRedirect();

        $disp->refresh();
        $this->assertEquals('Ditolak', $disp->status);
        $this->assertNotNull($disp->piket_catatan);
        $this->assertGreaterThanOrEqual(5, strlen($disp->piket_catatan));
    }

    public function test_tolak_catatan_kurang_dari_5_karakter_ditolak_validasi()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Menunggu', $tanggal);

        $response = $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action'  => 'tolak',
                'catatan' => 'No',  // hanya 2 karakter
            ]);

        $response->assertSessionHasErrors('catatan');

        $disp->refresh();
        $this->assertEquals('Menunggu', $disp->status, 'Status tidak boleh berubah karena validasi gagal.');
    }

    public function test_tolak_oleh_waka_piket_dari_disetujui_piket()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Disetujui_Piket', $tanggal);

        $response = $this->actingAs($this->wakaUser)
            ->post(route('waka-piket.dispensasi.status', $disp->id), [
                'action'  => 'tolak',
                'catatan' => 'Bukti dispensasi tidak memenuhi syarat',
            ]);

        $response->assertRedirect();

        $disp->refresh();
        $this->assertEquals('Ditolak', $disp->status);
    }

    // ─── KEMBALI ──────────────────────────────────────────────────

    public function test_kembali_hanya_dari_status_disetujui()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Disetujui', $tanggal);

        $response = $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action' => 'kembali',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $disp->refresh();
        $this->assertEquals('Selesai', $disp->status);
        $this->assertNotNull($disp->jam_kembali_aktual);
    }

    public function test_kembali_ditolak_dari_status_menunggu()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Menunggu', $tanggal);

        $response = $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action' => 'kembali',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $disp->refresh();
        $this->assertEquals('Menunggu', $disp->status);
    }

    // ─── ADMIN BATALKAN ───────────────────────────────────────────

    public function test_admin_batalkan_dispensasi_dengan_log()
    {
        $disp = $this->createDispensasi('Disetujui');
        $logCountBefore = LogAktivitas::count();

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.dispensasi.batal', $disp->id), [
                'alasan' => 'Dibatalkan karena surat izin palsu terindikasi',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $disp->refresh();
        $this->assertEquals('Dibatalkan', $disp->status);
        $this->assertEquals($this->adminUser->id, $disp->dibatalkan_oleh);
        $this->assertNotNull($disp->dibatalkan_at);
        $this->assertEquals('Dibatalkan karena surat izin palsu terindikasi', $disp->alasan_batal);
        $this->assertGreaterThan($logCountBefore, LogAktivitas::count(), 'LogAktivitas harus bertambah.');
    }

    public function test_admin_batalkan_dispensasi_alasan_kurang_dari_5_karakter_ditolak()
    {
        $disp = $this->createDispensasi('Disetujui');

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.dispensasi.batal', $disp->id), [
                'alasan' => 'No',
            ]);

        $response->assertSessionHasErrors('alasan');
        $disp->refresh();
        $this->assertEquals('Disetujui', $disp->status);
    }

    public function test_slip_cetak_tidak_bisa_dibuka_sebelum_disetujui()
    {
        $dispMenunggu = $this->createDispensasi('Menunggu');
        $response1 = $this->actingAs($this->guruPiketUser)
            ->get(route('guru-piket.dispensasi.cetak', $dispMenunggu->id));
        $response1->assertStatus(403);

        $dispPiket = $this->createDispensasi('Disetujui_Piket');
        $response2 = $this->actingAs($this->guruPiketUser)
            ->get(route('guru-piket.dispensasi.cetak', $dispPiket->id));
        $response2->assertStatus(403);

        $dispDisetujui = $this->createDispensasi('Disetujui');
        $response3 = $this->actingAs($this->guruPiketUser)
            ->get(route('guru-piket.dispensasi.cetak', $dispDisetujui->id));
        $response3->assertStatus(200);
    }

    // ─── PERSETUJUAN GANDA ────────────────────────────────────────

    public function test_persetujuan_ganda_dicegah_lockforupdate()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $disp = $this->createDispensasi('Menunggu', $tanggal);

        // Pertama: setujui berhasil
        $response1 = $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action' => 'setujui',
            ]);
        $response1->assertRedirect();
        $response1->assertSessionHas('success');

        $disp->refresh();
        $this->assertEquals('Disetujui_Piket', $disp->status);

        // Kedua: coba setujui lagi dari Menunggu → gagal karena sudah Disetujui_Piket
        $response2 = $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action' => 'setujui',
            ]);
        $response2->assertRedirect();
        $response2->assertSessionHas('error');

        $disp->refresh();
        $this->assertEquals('Disetujui_Piket', $disp->status, 'Status harus tetap Disetujui_Piket, bukan diubah ulang.');
    }

    // ─── SCOPE FINAL & PRESENSI ───────────────────────────────────

    public function test_dispensasi_disetujui_piket_tidak_mengunci_presensi()
    {
        $disp = $this->createDispensasi('Disetujui_Piket');

        // scopeFinal() TIDAK boleh mengembalikan dispensasi Disetujui_Piket
        $found = DispensasiSiswa::where('id', $disp->id)->final()->first();
        $this->assertNull($found, 'Disetujui_Piket TIDAK boleh ada di scopeFinal — siswa belum boleh terkunci.');
    }

    public function test_dispensasi_disetujui_mengunci_presensi()
    {
        $disp = $this->createDispensasi('Disetujui');

        $found = DispensasiSiswa::where('id', $disp->id)->final()->first();
        $this->assertNotNull($found, 'Disetujui HARUS ada di scopeFinal — siswa terkunci di presensi.');
    }

    public function test_scope_final_mencakup_status_lama()
    {
        // Disetujui_KS dan Disetujui_Waka (data lama) harus masuk scopeFinal
        $dispKS = $this->createDispensasi('Disetujui_KS');
        $dispWaka = $this->createDispensasi('Disetujui_Waka');

        $ids = DispensasiSiswa::whereIn('id', [$dispKS->id, $dispWaka->id])->final()->pluck('id');
        $this->assertTrue($ids->contains($dispKS->id), 'Disetujui_KS harus ada di scopeFinal.');
        $this->assertTrue($ids->contains($dispWaka->id), 'Disetujui_Waka harus ada di scopeFinal.');
    }

    // ─── SATPAM ───────────────────────────────────────────────────

    public function test_satpam_hanya_catat_waktu_tidak_bisa_setujui()
    {
        $satpamUser = User::create([
            'name'      => 'Satpam Test',
            'email'     => 'satpamtest' . rand(1000, 9999) . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'satpam',
            'is_active' => true,
        ]);

        $disp = $this->createDispensasi('Disetujui');

        // Satpam catat keluar — status TETAP Disetujui
        $response = $this->actingAs($satpamUser)
            ->post(route('satpam.dispensasi.status', $disp->id), [
                'action' => 'keluar',
            ]);

        $response->assertRedirect();
        $disp->refresh();
        $this->assertEquals('Disetujui', $disp->status, 'Status TETAP Disetujui setelah satpam catat keluar.');
        $this->assertNotNull($disp->jam_keluar_aktual);

        // Satpam catat kembali — status berubah ke Selesai
        $response2 = $this->actingAs($satpamUser)
            ->post(route('satpam.dispensasi.status', $disp->id), [
                'action' => 'kembali',
            ]);

        $response2->assertRedirect();
        $disp->refresh();
        $this->assertEquals('Selesai', $disp->status);
        $this->assertNotNull($disp->jam_kembali_aktual);
    }

    // ─── WAKA SDM ROUTE DIHAPUS ───────────────────────────────────

    public function test_waka_sdm_route_update_status_dihapus()
    {
        $disp = $this->createDispensasi('Menunggu');

        $wakaUser = User::create([
            'name'      => 'Waka SDM Test',
            'email'     => 'wakasdmtest' . rand(1000, 9999) . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'waka',
            'is_active' => true,
        ]);

        // POST ke route yang sudah dihapus → 404 atau 405
        $response = $this->actingAs($wakaUser)
            ->post("/waka-sdm/dispensasi/{$disp->id}/status", [
                'status'  => 'Disetujui',
                'catatan' => 'test',
            ]);

        $this->assertTrue(
            in_array($response->status(), [404, 405]),
            'Route POST waka-sdm/dispensasi/{id}/status harus sudah dihapus (404 atau 405), tapi mendapat ' . $response->status()
        );
    }

    // ─── NOTIFIKASI ───────────────────────────────────────────────

    public function test_notifikasi_dikirim_saat_status_berubah()
    {
        $tanggal = Carbon::today()->isWeekday() ? Carbon::today() : Carbon::today()->next(Carbon::MONDAY);
        $this->seedRosterForDate($tanggal);

        $notifCountBefore = Notifikasi::count();

        $disp = $this->createDispensasi('Menunggu', $tanggal);

        // Tahap 1: guru piket setujui → notifikasi ke waka piket
        $this->actingAs($this->guruPiketUser)
            ->post(route('guru-piket.dispensasi.status', $disp->id), [
                'action' => 'setujui',
            ]);

        $this->assertGreaterThan($notifCountBefore, Notifikasi::count(), 'Notifikasi harus terkirim setelah Tahap 1.');

        $notifCountAfterT1 = Notifikasi::count();

        // Tahap 2: waka piket setujui → notifikasi ke penginput
        $disp->refresh();
        $this->assertEquals('Disetujui_Piket', $disp->status);

        $this->actingAs($this->wakaUser)
            ->post(route('waka-piket.dispensasi.status', $disp->id), [
                'action' => 'setujui',
            ]);

        $this->assertGreaterThan($notifCountAfterT1, Notifikasi::count(), 'Notifikasi harus terkirim setelah Tahap 2.');
    }
}
