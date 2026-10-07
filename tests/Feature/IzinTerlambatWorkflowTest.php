<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\IzinTerlambat;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\PresensiSiswa;
use App\Models\DispensasiSiswa;
use App\Models\IzinSiswa;
use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IzinTerlambatWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_migration_and_model_relations_work()
    {
        $this->assertGreaterThan(0, IzinTerlambat::count());

        $first = IzinTerlambat::with(['siswa', 'kelas', 'diinputOlehUser'])->first();
        $this->assertNotNull($first);
        $this->assertNotNull($first->siswa);
        $this->assertNotNull($first->kelas);
        $this->assertNotNull($first->diinputOlehUser);
        $this->assertEquals($first->id_siswa, $first->siswa_id);
        $this->assertEquals($first->id_kelas, $first->kelas_id);
    }

    public function test_presensi_siswa_accepts_terlambat_status()
    {
        $presensi = new PresensiSiswa();
        $presensi->id_jurnal = 1;
        $presensi->id_siswa = 1;
        $presensi->status = 'Terlambat';
        $presensi->keterangan = 'Izin Terlambat Masuk Jam Ke-3';

        $this->assertEquals('Terlambat', $presensi->status);
    }

    public function test_nomor_surat_generator()
    {
        $date = Carbon::parse('2026-10-05');
        $nomor = IzinTerlambat::generateNomorSurat($date);

        $this->assertStringContainsString('IZIN-TLT', $nomor);
        $this->assertStringContainsString('2026', $nomor);
    }

    public function test_guru_piket_can_store_late_student_permit()
    {
        $guruPiketUser = User::where('role', 'guru_piket')->first() ?? User::factory()->create(['role' => 'guru_piket']);
        $siswa = Siswa::first();

        $response = $this->actingAs($guruPiketUser)->post(route('guru-piket.terlambat.store'), [
            'id_siswa'     => $siswa->id_siswa,
            'tanggal'      => '2026-10-07',
            'jam_masuk'    => '07:30',
            'jam_ke_mulai' => 3,
            'alasan'       => 'Ban sepeda motor bocor di jalan',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('izin_terlambat', [
            'id_siswa'     => $siswa->id_siswa,
            'tanggal'      => '2026-10-07',
            'jam_ke_mulai' => 3,
            'status'       => 'Menunggu',
            'alasan'       => 'Ban sepeda motor bocor di jalan',
        ]);
    }

    public function test_validation_rejects_reason_less_than_5_chars()
    {
        $guruPiketUser = User::where('role', 'guru_piket')->first() ?? User::factory()->create(['role' => 'guru_piket']);
        $siswa = Siswa::first();

        $response = $this->actingAs($guruPiketUser)->post(route('guru-piket.terlambat.store'), [
            'id_siswa'     => $siswa->id_siswa,
            'tanggal'      => '2026-10-05',
            'jam_masuk'    => '07:30',
            'jam_ke_mulai' => 3,
            'alasan'       => 'Ban', // Kurang dari 5 karakter
        ]);

        $response->assertSessionHasErrors(['alasan']);
    }

    public function test_waka_piket_can_approve_permit_and_generate_nomor_surat()
    {
        $wakaUser = User::where('email', 'waka.piket@smkn1boyolangu.sch.id')->first() ?? User::where('role', 'admin')->first();
        $this->assertNotNull($wakaUser, 'User waka piket atau admin tidak ditemukan.');

        $piketUser = User::where('role', 'guru_piket')->first() ?? $wakaUser;
        $this->assertNotNull($piketUser, 'User guru piket atau waka tidak ditemukan.');
        $siswa = Siswa::first();
        $this->assertNotNull($siswa, 'Data siswa tidak ditemukan.');

        $izin = IzinTerlambat::create([
            'id_siswa'          => $siswa->id_siswa,
            'id_kelas'          => $siswa->id_kelas,
            'tanggal'           => '2026-10-05',
            'jam_masuk'         => '07:45:00',
            'jam_ke_mulai'      => 3,
            'alasan'            => 'Ketinggalan angkot pagi hari',
            'status'            => 'Menunggu',
            'diinput_oleh'      => $piketUser->id,
        ]);

        $response = $this->actingAs($wakaUser)->post(route('waka-piket.terlambat.konfirmasi', $izin->id), [
            'action'  => 'setujui',
            'catatan' => 'Diberikan toleransi keterlambatan 15 menit',
        ]);

        $response->assertSessionHas('success');

        $izin->refresh();
        $this->assertEquals('Disetujui', $izin->status);
        $this->assertNotNull($izin->nomor_surat);
        $this->assertStringContainsString('IZIN-TLT', $izin->nomor_surat);
        $this->assertEquals($wakaUser->id, $izin->dikonfirmasi_oleh);
    }

    public function test_waka_piket_can_reject_permit()
    {
        $wakaUser = User::where('email', 'waka.piket@smkn1boyolangu.sch.id')->first() ?? User::where('role', 'admin')->first();
        $this->assertNotNull($wakaUser, 'User waka piket atau admin tidak ditemukan.');
        $siswa = Siswa::first();
        $this->assertNotNull($siswa, 'Data siswa tidak ditemukan.');

        $izin = IzinTerlambat::create([
            'id_siswa'          => $siswa->id_siswa,
            'id_kelas'          => $siswa->id_kelas,
            'tanggal'           => '2026-10-05',
            'jam_masuk'         => '09:00:00',
            'jam_ke_mulai'      => 4,
            'alasan'            => 'Bangun kesiangan tanpa alasan jelas',
            'status'            => 'Menunggu',
            'diinput_oleh'      => $wakaUser->id,
        ]);

        $response = $this->actingAs($wakaUser)->post(route('waka-piket.terlambat.konfirmasi', $izin->id), [
            'action'  => 'tolak',
            'catatan' => 'Terlambat lebih dari 1 jam tanpa keterangan orang tua',
        ]);

        $response->assertSessionHas('success');

        $izin->refresh();
        $this->assertEquals('Ditolak', $izin->status);
        $this->assertNull($izin->nomor_surat);
    }

    public function test_admin_can_cancel_approved_permit_with_reason_and_logs()
    {
        $admin = User::where('role', 'admin')->first();
        $siswa = Siswa::first();

        $izin = IzinTerlambat::create([
            'id_siswa'          => $siswa->id_siswa,
            'id_kelas'          => $siswa->id_kelas,
            'tanggal'           => '2026-10-05',
            'jam_masuk'         => '07:20:00',
            'jam_ke_mulai'      => 2,
            'alasan'            => 'Hujan lebat di perbatasan kota',
            'status'            => 'Disetujui',
            'nomor_surat'       => '999/IZIN-TLT/X/2026',
            'diinput_oleh'      => $admin->id,
            'dikonfirmasi_oleh' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('waka-piket.terlambat.konfirmasi', $izin->id), [
            'action'  => 'batalkan',
            'catatan' => 'Dibatalkan karena surat izin palsu terindikasi',
        ]);

        $response->assertSessionHas('success');

        $izin->refresh();
        $this->assertEquals('Dibatalkan', $izin->status);
        $this->assertStringContainsString('Dibatalkan oleh Admin', $izin->catatan_konfirmasi);
    }

    public function test_presensi_resolution_logic()
    {
        $izin = new IzinTerlambat([
            'jam_ke_mulai' => 3,
            'alasan'       => 'Ban bocor',
        ]);

        // 1. Sesi KBM memuat jam_ke_mulai (misal jam '3' atau '3-4') -> Terlambat
        $resMatching = $izin->resolveStatusForTeachingHour('3-4');
        $this->assertEquals('Terlambat', $resMatching['status']);
        $this->assertTrue($resMatching['is_locked']);

        // 2. Sesi KBM sebelum jam_ke_mulai (misal jam '1-2') -> Alpha (atau jam_terlewat)
        $resPreceding = $izin->resolveStatusForTeachingHour('1-2');
        $this->assertEquals(config('presensi.jam_terlewat_terlambat', 'Alpha'), $resPreceding['status']);
        $this->assertTrue($resPreceding['is_locked']);

        // 3. Sesi KBM setelah jam_ke_mulai (misal jam '5-6') -> Hadir
        $resSubsequent = $izin->resolveStatusForTeachingHour('5-6');
        $this->assertEquals('Hadir', $resSubsequent['status']);
        $this->assertFalse($resSubsequent['is_locked']);
    }

    public function test_satpam_can_access_read_only_monitoring()
    {
        $satpam = User::where('role', 'satpam')->first() ?? User::factory()->create(['role' => 'satpam']);

        $response = $this->actingAs($satpam)->get(route('satpam.terlambat'));
        $response->assertStatus(200);
        $response->assertViewIs('satpam.terlambat.index');
    }

    public function test_wali_kelas_can_view_late_recap_for_their_class()
    {
        $waliKelasUser = User::whereHas('waliKelas.kelas')->first();
        $this->assertNotNull($waliKelasUser, 'User wali kelas dengan kelas binaan tidak ditemukan');
        $this->assertNotNull($waliKelasUser->waliKelas, 'Record wali kelas tidak ditemukan');
        $this->assertNotNull($waliKelasUser->waliKelas->kelas, 'Record kelas binaan tidak ditemukan');

        $response = $this->actingAs($waliKelasUser)->get(route('wali-kelas.terlambat'));
        $response->assertStatus(200);
        $response->assertViewIs('wali_kelas.terlambat.index');
    }

    public function test_print_slip_view_renders_for_approved_permit()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin, 'User admin tidak ditemukan');

        $approvedIzin = IzinTerlambat::where('status', 'Disetujui')->first();
        if (!$approvedIzin) {
            $siswa = Siswa::first();
            $this->assertNotNull($siswa, 'Data siswa tidak ditemukan');
            $approvedIzin = IzinTerlambat::create([
                'id_siswa'          => $siswa->id_siswa,
                'id_kelas'          => $siswa->id_kelas,
                'tanggal'           => '2026-10-05',
                'jam_masuk'         => '07:20:00',
                'jam_ke_mulai'      => 2,
                'alasan'            => 'Hujan lebat',
                'status'            => 'Disetujui',
                'nomor_surat'       => '123/IZIN-TLT/X/2026',
                'diinput_oleh'      => $admin->id,
                'dikonfirmasi_oleh' => $admin->id,
            ]);
        }
        $this->assertNotNull($approvedIzin, 'Record izin terlambat berstatus Disetujui tidak ditemukan');

        $response = $this->actingAs($admin)->get(route('guru-piket.terlambat.cetak', $approvedIzin->id));
        $response->assertStatus(200);
        $response->assertViewIs('guru_piket.terlambat.cetak');
        $response->assertSee('SURAT IZIN MASUK KELAS SISWA TERLAMBAT');
    }

    public function test_duplicate_submission_is_prevented()
    {
        $guruPiketUser = User::where('role', 'guru_piket')->first() ?? User::factory()->create(['role' => 'guru_piket']);
        $siswa = Siswa::first();
        $this->assertNotNull($siswa, 'Data siswa tidak ditemukan');

        // Buat izin pertama
        IzinTerlambat::create([
            'id_siswa'     => $siswa->id_siswa,
            'id_kelas'     => $siswa->id_kelas,
            'tanggal'      => '2026-10-09',
            'jam_masuk'    => '07:15',
            'jam_ke_mulai' => 2,
            'alasan'       => 'Hujan deras lebat di jalan',
            'status'       => 'Menunggu',
            'diinput_oleh' => $guruPiketUser->id,
        ]);

        // Coba ajukan izin kedua untuk siswa, tanggal, dan jam_ke_mulai yang sama
        $response = $this->actingAs($guruPiketUser)->post(route('guru-piket.terlambat.store'), [
            'id_siswa'     => $siswa->id_siswa,
            'tanggal'      => '2026-10-09',
            'jam_masuk'    => '07:20',
            'jam_ke_mulai' => 2,
            'alasan'       => 'Pengajuan kedua identik duplikat',
        ]);

        $response->assertSessionHasErrors(['id_siswa']);
    }

    public function test_rekap_route_accessible_by_waka_kesiswaan_and_kepala_sekolah()
    {
        $waka = User::where('role', 'waka_kesiswaan')->first()
            ?? User::whereHas('waka', fn($q) => $q->whereJsonContains('bidang_kode', 'kesiswaan'))->first();
        $this->assertNotNull($waka, 'User Waka Kesiswaan tidak ditemukan');

        $response = $this->actingAs($waka)->get(route('waka-kesiswaan.rekap-terlambat'));
        $response->assertStatus(200);
        $response->assertViewIs('waka_kesiswaan.terlambat.rekap');

        $kepsek = User::where('role', 'kepala_sekolah')->first();
        $this->assertNotNull($kepsek, 'User Kepala Sekolah tidak ditemukan');

        $responseKepsek = $this->actingAs($kepsek)->get(route('kepala-sekolah.rekap-terlambat'));
        $responseKepsek->assertStatus(200);
        $responseKepsek->assertViewIs('waka_kesiswaan.terlambat.rekap');
    }
}
