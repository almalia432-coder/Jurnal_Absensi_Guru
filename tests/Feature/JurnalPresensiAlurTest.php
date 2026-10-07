<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\IzinSiswa;
use App\Models\DispensasiSiswa;
use App\Models\JurnalMengajar;
use App\Models\PresensiSiswa;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class JurnalPresensiAlurTest extends TestCase
{
    use DatabaseTransactions;
    public function test_guru_mapel_jurnal_attendance_flow_and_lock()
    {
        Carbon::setLocale('id');
        $today = Carbon::today()->format('Y-m-d');

        // Cari atau buat Guru Mapel yang memiliki data guru
        $userGuru = User::where('role', 'guru_mapel')->whereHas('guru')->first() ?? User::whereHas('guru')->first();
        $this->assertNotNull($userGuru, 'Tidak ada user guru untuk testing.');

        $guru = $userGuru->guru;
        $this->assertNotNull($guru, 'Data guru tidak ditemukan.');

        // Cari kelas dan mapel
        $kelas = Kelas::first();
        $mapel = Mapel::first();
        $this->assertNotNull($kelas, 'Tidak ada data kelas.');
        $this->assertNotNull($mapel, 'Tidak ada data mapel.');

        // Ambil 3 siswa di kelas ini
        $siswas = Siswa::where('id_kelas', $kelas->id_kelas)->take(3)->get();
        $this->assertGreaterThanOrEqual(3, $siswas->count(), 'Minimal butuh 3 siswa di kelas untuk pengujian lengkap.');

        $siswaPiketSakit = $siswas[0];
        $siswaRegulerHadir = $siswas[1];
        $siswaRegulerAlpha = $siswas[2];

        // 1. Simulasikan Guru Piket membuat izin sakit untuk siswa 1 hari ini
        $izinPiket = IzinSiswa::updateOrCreate(
            [
                'id_siswa' => $siswaPiketSakit->id_siswa,
                'tanggal_mulai' => $today,
                'tanggal_selesai' => $today,
            ],
            [
                'jenis_izin' => 'Sakit',
                'alasan' => 'Demam tinggi dan istirahat dokter',
                'status' => 'Disetujui',
                'diinput_oleh' => $userGuru->id_user,
            ]
        );

        // 2. Akses halaman create jurnal sebagai Guru Mapel
        $response = $this->actingAs($userGuru)->get(route('guru-mapel.jurnal.create', [
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id_mapel,
        ]));

        $response->assertStatus(200);

        // Periksa bahwa tampilan memuat informasi piket
        $response->assertSee('Sakit (Piket)');
        $response->assertSee('Demam tinggi dan istirahat dokter');

        // Periksa bahwa siswa piket memiliki hidden input
        $response->assertSee('name="presensi[' . $siswaPiketSakit->id_siswa . ']"', false);
        $response->assertSee('value="Sakit"', false);

        // Periksa bahwa siswa reguler memiliki radio Hadir dan Alpha
        $response->assertSee('name="presensi[' . $siswaRegulerHadir->id_siswa . ']"', false);
        $response->assertSee('value="Hadir"', false);
        $response->assertSee('value="Alpha"', false);

        // 3. Simulasikan pengiriman form jurnal KBM
        $postData = [
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id_mapel,
            'tanggal' => $today,
            'jam_ke' => '1-2',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:30',
            'status_guru' => 'Hadir',
            'materi' => 'Pengenalan Sistem Database Relasional dan Validasi KBM',
            'catatan' => 'Siswa aktif bertanya, kelas berjalan kondusif.',
            'presensi' => [
                $siswaPiketSakit->id_siswa => 'Sakit', // Dari hidden input piket
                $siswaRegulerHadir->id_siswa => 'Hadir', // Dipilih Hadir
                $siswaRegulerAlpha->id_siswa => 'Alpha', // Dipilih Alpha
            ],
            'keterangan' => [
                $siswaPiketSakit->id_siswa => '(Piket: Sakit) Demam tinggi dan istirahat dokter',
                $siswaRegulerHadir->id_siswa => '',
                $siswaRegulerAlpha->id_siswa => 'Tanpa keterangan kabar',
            ],
        ];

        $postResponse = $this->actingAs($userGuru)->post(route('guru-mapel.jurnal.store'), $postData);
        $postResponse->assertRedirect(route('guru-mapel.jurnal.riwayat'));
        $postResponse->assertSessionHas('success');

        // 4. Verifikasi data jurnal yang tersimpan di DB
        $jurnal = JurnalMengajar::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $kelas->id_kelas)
            ->where('id_mapel', $mapel->id_mapel)
            ->where('tanggal', $today)
            ->latest('id_jurnal')
            ->first();

        $this->assertNotNull($jurnal);
        $this->assertEquals(1, $jurnal->jumlah_siswa_hadir);
        $this->assertEquals(2, $jurnal->jumlah_siswa_tidak_hadir); // 1 Sakit (Piket) + 1 Alpha

        // Verifikasi individual presensi_siswa
        $presensiSakit = PresensiSiswa::where('id_jurnal', $jurnal->id_jurnal)
            ->where('id_siswa', $siswaPiketSakit->id_siswa)
            ->first();
        $this->assertNotNull($presensiSakit);
        $this->assertEquals('Sakit', $presensiSakit->status);
        $this->assertStringContainsString('Piket: Sakit', $presensiSakit->keterangan);

        $presensiHadir = PresensiSiswa::where('id_jurnal', $jurnal->id_jurnal)
            ->where('id_siswa', $siswaRegulerHadir->id_siswa)
            ->first();
        $this->assertNotNull($presensiHadir);
        $this->assertEquals('Hadir', $presensiHadir->status);

        $presensiAlpha = PresensiSiswa::where('id_jurnal', $jurnal->id_jurnal)
            ->where('id_siswa', $siswaRegulerAlpha->id_siswa)
            ->first();
        $this->assertNotNull($presensiAlpha);
        $this->assertEquals('Alpha', $presensiAlpha->status);
        $this->assertEquals('Tanpa keterangan kabar', $presensiAlpha->keterangan);

        // Bersihkan data test
        PresensiSiswa::where('id_jurnal', $jurnal->id_jurnal)->delete();
        $jurnal->delete();
        $izinPiket->delete();
    }

    public function test_tamper_resistance_for_unauthorized_statuses()
    {
        Carbon::setLocale('id');
        $today = Carbon::today()->format('Y-m-d');

        $userGuru = User::where('role', 'guru_mapel')->whereHas('guru')->first() ?? User::whereHas('guru')->first();
        $this->assertNotNull($userGuru, 'Tidak ada user guru untuk testing.');
        $guru = $userGuru->guru;
        $this->assertNotNull($guru, 'Data guru tidak ditemukan.');
        $kelas = Kelas::first();
        $mapel = Mapel::first();
        $siswa = Siswa::where('id_kelas', $kelas->id_kelas)->first();

        // Pastikan tidak ada izin piket untuk siswa ini
        IzinSiswa::where('id_siswa', $siswa->id_siswa)->whereDate('tanggal_mulai', '<=', $today)->whereDate('tanggal_selesai', '>=', $today)->delete();
        DispensasiSiswa::where('id_siswa', $siswa->id_siswa)->whereDate('tanggal', $today)->delete();
        \App\Models\IzinTerlambat::where('id_siswa', $siswa->id_siswa)->whereDate('tanggal', $today)->delete();

        // Guru Mapel mencoba mengirimkan status 'Izin' atau 'Sakit' yang tidak ada izin piketnya (misal modifikasi DevTools)
        $postData = [
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id_mapel,
            'tanggal' => $today,
            'jam_ke' => '3-4',
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:30',
            'status_guru' => 'Hadir',
            'materi' => 'Uji Integritas Keamanan Presensi',
            'presensi' => [
                $siswa->id_siswa => 'Izin', // Bukan dari piket, seharusnya otomatis jadi Hadir
            ],
            'keterangan' => [
                $siswa->id_siswa => 'Mencoba kirim izin tanpa persetujuan piket',
            ],
        ];

        $postResponse = $this->actingAs($userGuru)->post(route('guru-mapel.jurnal.store'), $postData);
        $postResponse->assertRedirect(route('guru-mapel.jurnal.riwayat'));

        $jurnal = JurnalMengajar::where('id_guru', $guru->id_guru)
            ->where('id_kelas', $kelas->id_kelas)
            ->where('tanggal', $today)
            ->latest('id_jurnal')
            ->first();

        $this->assertNotNull($jurnal);
        // Karena bukan dari piket dan bukan Alpha, otomatis menjadi Hadir
        $this->assertEquals(1, $jurnal->jumlah_siswa_hadir);
        $this->assertEquals(0, $jurnal->jumlah_siswa_tidak_hadir);

        $presensi = PresensiSiswa::where('id_jurnal', $jurnal->id_jurnal)
            ->where('id_siswa', $siswa->id_siswa)
            ->first();
        $this->assertEquals('Hadir', $presensi->status);

        PresensiSiswa::where('id_jurnal', $jurnal->id_jurnal)->delete();
        $jurnal->delete();
    }
}
