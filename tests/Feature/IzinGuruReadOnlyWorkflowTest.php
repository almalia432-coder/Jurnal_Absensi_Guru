<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\JadwalPelajaran;
use App\Models\JadwalPiketKbm;
use App\Models\IzinGuru;
use App\Models\LogAktivitas;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

/**
 * Tes Alur Izin Guru Read-Only (Tahap C):
 * Status 'Tercatat', tanpa alur approval/penolakan berjenjang.
 */
class IzinGuruReadOnlyWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private User $guruUser;
    private Guru $guru;
    private User $guruPiketUser;
    private Guru $guruPiketGuru;
    private User $wakaPiketUser;
    private User $wakaSdmUser;
    private User $kepsekUser;
    private User $adminUser;
    private TahunAjaran $tahunAjaran;
    private string $hariIndo;
    private string $siklus;

    protected function setUp(): void
    {
        parent::setUp();

        $rand = rand(1000, 9999);

        // Active Tahun Ajaran (pakai is_aktif dan nama)
        $this->tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        if (!$this->tahunAjaran) {
            $this->tahunAjaran = TahunAjaran::create([
                'nama' => '2026/2027',
                'semester' => 'Ganjil',
                'tanggal_mulai' => Carbon::now()->subMonths(2),
                'tanggal_selesai' => Carbon::now()->addMonths(4),
                'is_aktif' => true,
            ]);
        }

        // Tentukan hari pengajuan izin = besok
        $targetDate = Carbon::tomorrow();
        $hariInggris = $targetDate->format('l');
        $mapHari = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];
        $this->hariIndo = $mapHari[$hariInggris] ?? 'Senin';
        $this->siklus = JadwalPiketKbm::getSiklusForDate($targetDate);

        // Guru Mapel
        $this->guruUser = User::create([
            'name'      => 'Guru Mapel Test ' . $rand,
            'email'     => 'gurumapel' . $rand . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        $this->guru = Guru::create([
            'user_id'       => $this->guruUser->id,
            'nip'           => 'GURU' . $rand,
            'nama_lengkap'  => 'Guru Mapel Test ' . $rand,
            'jenis_kelamin' => 'L',
            'status_aktif'  => true,
        ]);

        // Guru Piket bertugas (pakai field id_guru, siklus, tahun_ajaran, semester)
        $this->guruPiketUser = User::create([
            'name'      => 'Piket Petugas ' . $rand,
            'email'     => 'piketpetugas' . $rand . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'guru_piket',
            'is_active' => true,
        ]);
        $this->guruPiketGuru = Guru::create([
            'user_id'       => $this->guruPiketUser->id,
            'nip'           => 'PIKET' . $rand,
            'nama_lengkap'  => 'Piket Petugas ' . $rand,
            'jenis_kelamin' => 'L',
            'status_aktif'  => true,
        ]);

        JadwalPiketKbm::create([
            'id_guru'      => $this->guruPiketGuru->id_guru,
            'nama_guru'    => $this->guruPiketGuru->nama_lengkap,
            'nip'          => $this->guruPiketGuru->nip,
            'hari'         => $this->hariIndo,
            'siklus'       => $this->siklus,
            'tahun_ajaran' => $this->tahunAjaran->nama,
            'semester'     => $this->tahunAjaran->semester,
            'shift'        => 'Pagi',
            'peran'        => 'petugas',
            'urutan'       => 1,
            'piket_waka_nama' => null,
            'piket_waka_nip'  => null,
        ]);

        // Waka Piket bertugas (piket_waka_nip match NIP-nya)
        $wakaPiketNip = 'WAKAPIKET' . $rand;
        $this->wakaPiketUser = User::create([
            'name'      => 'Waka Piket Petugas ' . $rand,
            'email'     => 'wakapiket' . $rand . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'waka',
            'is_active' => true,
        ]);
        $wakaPiketGuru = Guru::create([
            'user_id'       => $this->wakaPiketUser->id,
            'nip'           => $wakaPiketNip,
            'nama_lengkap'  => 'Waka Piket Petugas ' . $rand,
            'jenis_kelamin' => 'L',
            'status_aktif'  => true,
        ]);

        // JadwalPiketKbm with piket_waka_nip so resolvePiketWaka can find it
        JadwalPiketKbm::create([
            'id_guru'         => $this->guruPiketGuru->id_guru,
            'nama_guru'       => $this->guruPiketGuru->nama_lengkap,
            'nip'             => $this->guruPiketGuru->nip,
            'hari'            => $this->hariIndo,
            'siklus'          => $this->siklus,
            'tahun_ajaran'    => $this->tahunAjaran->nama,
            'semester'        => $this->tahunAjaran->semester,
            'shift'           => 'Pagi',
            'peran'           => 'koordinator',
            'urutan'          => 2,
            'piket_waka_nama' => $wakaPiketGuru->nama_lengkap,
            'piket_waka_nip'  => $wakaPiketNip,
        ]);

        // Waka SDM (role-based legacy)
        $this->wakaSdmUser = User::create([
            'name'      => 'Waka SDM Test ' . $rand,
            'email'     => 'wakasdm' . $rand . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'waka_sdm',
            'is_active' => true,
        ]);

        // Kepala Sekolah
        $this->kepsekUser = User::create([
            'name'      => 'Kepala Sekolah Test ' . $rand,
            'email'     => 'kepsek' . $rand . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'kepala_sekolah',
            'is_active' => true,
        ]);

        // Admin
        $this->adminUser = User::create([
            'name'      => 'Admin Test ' . $rand,
            'email'     => 'admin' . $rand . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Buat jadwal mengajar guru agar impact calculation tidak nol
        $kelas = Kelas::first();
        $mapel = Mapel::first();
        if ($kelas && $mapel && $this->tahunAjaran) {
            JadwalPelajaran::create([
                'id_tahun_ajaran' => $this->tahunAjaran->id,
                'id_kelas'        => $kelas->id_kelas,
                'id_guru'         => $this->guru->id_guru,
                'id_mapel'        => $mapel->id_mapel,
                'hari'            => $this->hariIndo,
                'jam_ke'          => '1-4',
                'jam_mulai'       => '07:00',
                'jam_selesai'     => '10:00',
            ]);
        }
    }

    public function test_guru_mengisi_izin_langsung_tercatat_tahap_approval_null_dan_notifikasi_ke_4_pihak()
    {
        $targetDate = Carbon::tomorrow()->format('Y-m-d');

        $countBefore = Notifikasi::count();

        $response = $this->actingAs($this->guruUser)->post(route('guru-mapel.izin.store'), [
            'jenis_izin'      => 'Sakit',
            'tanggal_mulai'   => $targetDate,
            'tanggal_selesai' => $targetDate,
            'alasan'          => 'Sakit demam butuh istirahat dan rekomendasi dokter',
            'menitipkan_tugas' => '0',
        ]);

        $response->assertSessionHas('success');

        $izin = IzinGuru::where('guru_id', $this->guru->id)
            ->whereDate('tanggal_mulai', $targetDate)
            ->latest()
            ->first();

        $this->assertNotNull($izin, 'Record izin harus tersimpan');
        $this->assertEquals('Tercatat', $izin->status);
        $this->assertNull($izin->tahap_approval);
        $this->assertEquals($this->guruUser->id, $izin->diinput_oleh);

        // Notifikasi harus bertambah
        $countAfter = Notifikasi::count();
        $this->assertGreaterThan($countBefore, $countAfter, 'Harus ada notifikasi baru terkirim');

        // Notifikasi dikirim ke guru piket, waka piket, waka sdm, kepsek
        $recipientIds = Notifikasi::where('reference_id', $izin->id)
            ->where('reference_type', IzinGuru::class)
            ->pluck('user_id')
            ->toArray();

        $this->assertContains($this->guruPiketUser->id, $recipientIds, 'Guru piket harus dapat notifikasi');
        $this->assertContains($this->wakaPiketUser->id, $recipientIds, 'Waka piket harus dapat notifikasi');
        $this->assertContains($this->wakaSdmUser->id, $recipientIds, 'Waka SDM harus dapat notifikasi');
        $this->assertContains($this->kepsekUser->id, $recipientIds, 'Kepala Sekolah harus dapat notifikasi');

        // Tidak ada duplikat
        $this->assertEquals(count($recipientIds), count(array_unique($recipientIds)), 'Tidak boleh ada duplikat penerima');

        // Pesan harus mencantumkan nama guru, jenis izin, dan kata terdampak
        $sampleNotif = Notifikasi::where('reference_id', $izin->id)
            ->where('reference_type', IzinGuru::class)
            ->first();
        $this->assertNotNull($sampleNotif);
        $this->assertStringContainsString($this->guru->nama_lengkap, $sampleNotif->pesan);
        $this->assertStringContainsString('Sakit', $sampleNotif->pesan);
        $this->assertStringContainsString('terdampak', $sampleNotif->pesan);

        // LogAktivitas harus ada
        $log = LogAktivitas::where('user_id', $this->guruUser->id)
            ->where('aktivitas', 'Pengajuan Izin Guru')
            ->latest()
            ->first();
        $this->assertNotNull($log, 'LogAktivitas harus dicatat');
    }

    public function test_post_ke_route_status_lama_menghasilkan_404_atau_405()
    {
        $izin = IzinGuru::create([
            'guru_id'         => $this->guru->id,
            'jenis_izin'      => 'Izin',
            'tanggal_mulai'   => Carbon::tomorrow()->format('Y-m-d'),
            'tanggal_selesai' => Carbon::tomorrow()->format('Y-m-d'),
            'alasan'          => 'Keperluan keluarga penting',
            'status'          => 'Tercatat',
            'tahap_approval'  => null,
            'diinput_oleh'    => $this->guruUser->id,
        ]);

        // Route guru-piket/izin-guru/{id}/status sudah dihapus
        $respPiket = $this->actingAs($this->guruPiketUser)
            ->post("/guru-piket/izin-guru/{$izin->id}/status", ['status' => 'Disetujui']);
        $this->assertContains($respPiket->status(), [404, 405]);

        // Route waka-sdm/izin/{id}/status sudah dihapus
        $respWaka = $this->actingAs($this->wakaSdmUser)
            ->post("/waka-sdm/izin/{$izin->id}/status", ['status' => 'Disetujui']);
        $this->assertContains($respWaka->status(), [404, 405]);

        // Route kepala-sekolah/izin-guru/{id}/status sudah dihapus
        $respKepsek = $this->actingAs($this->kepsekUser)
            ->post("/kepala-sekolah/izin-guru/{$izin->id}/status", ['status' => 'Disetujui']);
        $this->assertContains($respKepsek->status(), [404, 405]);
    }

    public function test_izin_tumpang_tindih_untuk_guru_yang_sama_ditolak_kecuali_yang_dibatalkan()
    {
        $tglMulai   = Carbon::tomorrow()->format('Y-m-d');
        $tglSelesai = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        // Buat izin aktif terlebih dulu
        IzinGuru::create([
            'guru_id'         => $this->guru->id,
            'jenis_izin'      => 'Sakit',
            'tanggal_mulai'   => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'alasan'          => 'Izin aktif awal',
            'status'          => 'Tercatat',
            'tahap_approval'  => null,
            'diinput_oleh'    => $this->guruUser->id,
        ]);

        // Submit izin yang tumpang tindih
        $overlapResponse = $this->actingAs($this->guruUser)->post(route('guru-mapel.izin.store'), [
            'jenis_izin'      => 'Izin',
            'tanggal_mulai'   => Carbon::tomorrow()->addDay()->format('Y-m-d'),
            'tanggal_selesai' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'alasan'          => 'Izin tumpang tindih dengan yang sudah ada',
            'menitipkan_tugas' => '0',
        ]);
        $overlapResponse->assertSessionHasErrors(['tanggal_mulai']);

        // Batalkan izin sebelumnya
        IzinGuru::where('guru_id', $this->guru->id)->update(['status' => 'Dibatalkan']);

        // Submit ulang — seharusnya berhasil karena izin lama sudah Dibatalkan
        $retryResponse = $this->actingAs($this->guruUser)->post(route('guru-mapel.izin.store'), [
            'jenis_izin'      => 'Izin',
            'tanggal_mulai'   => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'alasan'          => 'Izin baru setelah izin sebelumnya dibatalkan',
            'menitipkan_tugas' => '0',
        ]);
        $retryResponse->assertSessionHas('success');
    }

    public function test_scope_berlaku_mencakup_tercatat_dan_disetujui_tetapi_mengecualikan_dibatalkan_dan_ditolak()
    {
        $base = [
            'guru_id'        => $this->guru->id,
            'jenis_izin'     => 'Sakit',
            'tanggal_mulai'  => Carbon::today()->subDays(5)->format('Y-m-d'),
            'tanggal_selesai' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'alasan'         => 'Test scope berlaku',
            'tahap_approval' => null,
            'diinput_oleh'   => $this->guruUser->id,
        ];

        $izinTercatat  = IzinGuru::create(array_merge($base, ['status' => 'Tercatat']));
        $izinDisetujui = IzinGuru::create(array_merge($base, ['status' => 'Disetujui']));
        $izinDibatalkan = IzinGuru::create(array_merge($base, ['status' => 'Dibatalkan']));
        $izinDitolak   = IzinGuru::create(array_merge($base, ['status' => 'Ditolak']));

        $berlakuIds = IzinGuru::berlaku()->pluck('id')->toArray();

        $this->assertContains($izinTercatat->id,   $berlakuIds, 'Tercatat harus masuk scopeBerlaku');
        $this->assertContains($izinDisetujui->id,  $berlakuIds, 'Disetujui harus masuk scopeBerlaku');
        $this->assertNotContains($izinDibatalkan->id, $berlakuIds, 'Dibatalkan tidak boleh masuk scopeBerlaku');
        $this->assertNotContains($izinDitolak->id, $berlakuIds, 'Ditolak tidak boleh masuk scopeBerlaku');
    }

    public function test_guru_membatalkan_sebelum_tanggal_mulai_berhasil()
    {
        $izin = IzinGuru::create([
            'guru_id'         => $this->guru->id,
            'jenis_izin'      => 'Izin',
            'tanggal_mulai'   => Carbon::tomorrow()->format('Y-m-d'),
            'tanggal_selesai' => Carbon::tomorrow()->format('Y-m-d'),
            'alasan'          => 'Keperluan keluarga mendesak',
            'status'          => 'Tercatat',
            'tahap_approval'  => null,
            'diinput_oleh'    => $this->guruUser->id,
        ]);

        $response = $this->actingAs($this->guruUser)
            ->post(route('guru-mapel.izin.batal', $izin->id), [
                'alasan_batal' => 'Acara keluarga ditunda oleh pihak keluarga',
            ]);

        $response->assertSessionHas('success');

        $izin->refresh();
        $this->assertEquals('Dibatalkan', $izin->status);
        $this->assertEquals('Acara keluarga ditunda oleh pihak keluarga', $izin->alasan_batal);
        $this->assertEquals($this->guruUser->id, $izin->dibatalkan_oleh);
        $this->assertNotNull($izin->dibatalkan_at);

        // LogAktivitas
        $log = LogAktivitas::where('user_id', $this->guruUser->id)
            ->where('aktivitas', 'Pembatalan Izin Guru')
            ->latest()
            ->first();
        $this->assertNotNull($log, 'LogAktivitas pembatalan harus dicatat');
    }

    public function test_guru_membatalkan_pada_atau_setelah_tanggal_mulai_ditolak()
    {
        $izin = IzinGuru::create([
            'guru_id'         => $this->guru->id,
            'jenis_izin'      => 'Izin',
            'tanggal_mulai'   => Carbon::today()->format('Y-m-d'),
            'tanggal_selesai' => Carbon::today()->format('Y-m-d'),
            'alasan'          => 'Izin yang sudah dimulai hari ini',
            'status'          => 'Tercatat',
            'tahap_approval'  => null,
            'diinput_oleh'    => $this->guruUser->id,
        ]);

        $response = $this->actingAs($this->guruUser)
            ->post(route('guru-mapel.izin.batal', $izin->id), [
                'alasan_batal' => 'Ingin membatalkan izin hari ini',
            ]);

        $response->assertSessionHas('error');

        $izin->refresh();
        $this->assertEquals('Tercatat', $izin->status, 'Status tidak boleh berubah');
        $this->assertNull($izin->alasan_batal);
    }

    public function test_guru_tidak_dapat_membatalkan_izin_guru_lain()
    {
        $otherUser = User::create([
            'name'      => 'Guru Lain Test',
            'email'     => 'gurulain' . rand(1000, 9999) . '@test.id',
            'password'  => Hash::make('password'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        $otherGuru = Guru::create([
            'user_id'       => $otherUser->id,
            'nip'           => 'LAIN' . rand(1000, 9999),
            'nama_lengkap'  => 'Guru Lain Test',
            'jenis_kelamin' => 'P',
            'status_aktif'  => true,
        ]);

        $izin = IzinGuru::create([
            'guru_id'         => $otherGuru->id,
            'jenis_izin'      => 'Sakit',
            'tanggal_mulai'   => Carbon::tomorrow()->format('Y-m-d'),
            'tanggal_selesai' => Carbon::tomorrow()->format('Y-m-d'),
            'alasan'          => 'Izin milik guru lain',
            'status'          => 'Tercatat',
            'tahap_approval'  => null,
            'diinput_oleh'    => $otherUser->id,
        ]);

        $response = $this->actingAs($this->guruUser)
            ->post(route('guru-mapel.izin.batal', $izin->id), [
                'alasan_batal' => 'Mencoba membatalkan izin orang lain',
            ]);

        $response->assertStatus(403);

        $izin->refresh();
        $this->assertEquals('Tercatat', $izin->status, 'Status izin orang lain tidak boleh berubah');
    }

    public function test_admin_dapat_membatalkan_izin_kapan_saja_dengan_alasan_min_5_karakter()
    {
        $izin = IzinGuru::create([
            'guru_id'         => $this->guru->id,
            'jenis_izin'      => 'Dinas_Luar',
            'tanggal_mulai'   => Carbon::yesterday()->format('Y-m-d'),
            'tanggal_selesai' => Carbon::tomorrow()->format('Y-m-d'),
            'alasan'          => 'Dinas luar kota selama 3 hari',
            'status'          => 'Tercatat',
            'tahap_approval'  => null,
            'diinput_oleh'    => $this->guruUser->id,
        ]);

        // Alasan terlalu pendek harus gagal validasi
        $failResponse = $this->actingAs($this->adminUser)
            ->post(route('admin.izin-guru.batal', $izin->id), [
                'alasan_batal' => 'test',
            ]);
        $failResponse->assertSessionHasErrors(['alasan_batal']);

        // Alasan valid => berhasil
        $successResponse = $this->actingAs($this->adminUser)
            ->post(route('admin.izin-guru.batal', $izin->id), [
                'alasan_batal' => 'Dibatalkan oleh admin karena kegiatan sekolah mendadak',
            ]);
        $successResponse->assertSessionHas('success');

        $izin->refresh();
        $this->assertEquals('Dibatalkan', $izin->status);
        $this->assertEquals('Dibatalkan oleh admin karena kegiatan sekolah mendadak', $izin->alasan_batal);
        $this->assertEquals($this->adminUser->id, $izin->dibatalkan_oleh);
        $this->assertNotNull($izin->dibatalkan_at);

        // LogAktivitas admin
        $log = LogAktivitas::where('user_id', $this->adminUser->id)
            ->where('aktivitas', 'Pembatalan Izin Guru oleh Admin')
            ->latest()
            ->first();
        $this->assertNotNull($log, 'LogAktivitas admin harus dicatat');
    }
}
