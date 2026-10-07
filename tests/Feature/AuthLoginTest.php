<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Guru;
use App\Models\Admin;
use App\Models\Kelas;
use App\Models\Satpam;
use App\Models\WaliKelas;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Tes login AuthController (refactored: tanpa role, dengan RateLimiter).
 *
 * Semua tes membuat data sendiri; tidak bergantung pada DatabaseSeeder.
 * Menggunakan DatabaseTransactions: rollback otomatis setelah setiap tes.
 */
class AuthLoginTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Bersihkan RateLimiter sebelum setiap tes agar tidak saling mempengaruhi
        RateLimiter::clear($this->throttleKey('test@example.com', '127.0.0.1'));
    }

    private function throttleKey(string $username, string $ip): string
    {
        return 'login|' . strtolower(trim($username)) . '|' . $ip;
    }

    // =========================================================================
    // 1. Login email + password benar → masuk portal default
    // =========================================================================

    public function test_login_email_benar_masuk_portal_default()
    {
        $user = User::factory()->create([
            'email'     => 'guru.email.test@smkn1boyolangu.sch.id',
            'password'  => Hash::make('rahasiakuat123'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        // Buat guru record agar portal guru_mengajar terdeteksi
        Guru::create([
            'user_id'      => $user->id,
            'nip'          => '191111111111110001',
            'nama_lengkap' => 'Guru Email Test',
            'status_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'username' => 'guru.email.test@smkn1boyolangu.sch.id',
            'password' => 'rahasiakuat123',
        ]);

        // defaultPortal() = guru_mengajar → route = guru-mapel.dashboard
        $expectedRoute = config('portal.portals.guru_mengajar.route', 'guru-mapel.dashboard');
        $response->assertRedirect(route($expectedRoute));
        $this->assertAuthenticatedAs($user);
    }

    // =========================================================================
    // 2. Login NIP guru → masuk portal guru mengajar
    // =========================================================================

    public function test_login_nip_guru_masuk_portal_guru_mengajar()
    {
        $user = User::factory()->create([
            'email'     => 'guru.nip.test@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordguru99'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id'      => $user->id,
            'nip'          => '192222222222220002',
            'nama_lengkap' => 'Guru NIP Test',
            'status_aktif' => true,
        ]);

        $response = $this->post('/login', [
            'username' => '192222222222220002', // NIP
            'password' => 'passwordguru99',
        ]);

        $expectedRoute = config('portal.portals.guru_mengajar.route', 'guru-mapel.dashboard');
        $response->assertRedirect(route($expectedRoute));
        $this->assertAuthenticatedAs($user);
    }

    // =========================================================================
    // 3. Guru wali kelas → default wali_kelas, dan portal guru_mengajar tersedia untuk switcher
    //    (PortalResolver mendahulukan wali_kelas sebelum guru_mengajar di priorityOrder)
    // =========================================================================

    public function test_guru_wali_kelas_default_wali_kelas_dan_punya_portal_guru_mengajar()
    {
        $nip  = '193333333333330003';
        $user = User::factory()->create([
            'email'     => 'guru.walikelas.test@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordwk'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id'      => $user->id,
            'nip'          => $nip,
            'nama_lengkap' => 'Guru Wali Kelas Test',
            'status_aktif' => true,
        ]);
        $wali = WaliKelas::create([
            'user_id'      => $user->id,
            'nip'          => $nip,
            'nama_lengkap' => 'Guru Wali Kelas Test',
            'status_aktif' => true,
        ]);
        Kelas::create([
            'nama_kelas'    => 'XI WK TEST 1',
            'tingkat'       => 'XI',
            'jurusan'       => 'RPL',
            'wali_kelas_id' => $wali->id,
            'jumlah_siswa'  => 30,
        ]);

        // PortalResolver::getDefaultPortal memprioritaskan wali_kelas sebelum guru_mengajar
        config(['portal.legacy_role_fallback' => false]);
        $response = $this->post('/login', [
            'username' => 'guru.walikelas.test@smkn1boyolangu.sch.id',
            'password' => 'passwordwk',
        ]);
        $expectedRoute = config('portal.portals.wali_kelas.route', 'wali-kelas.dashboard');
        $response->assertRedirect(route($expectedRoute));

        // Kedua portal harus tersedia untuk "Beralih Portal"
        $user->refresh();
        $portalKeys = $user->availablePortalKeys();
        $this->assertContains('guru_mengajar', $portalKeys, 'Portal Guru Mengajar harus ada untuk portal switcher');
        $this->assertContains('wali_kelas', $portalKeys, 'Portal Wali Kelas harus ada');
        $this->assertGreaterThan(1, count($portalKeys), 'Harus ada lebih dari 1 portal agar switcher muncul');
    }

    // =========================================================================
    // 4. Password salah → pesan generik
    // =========================================================================

    public function test_password_salah_pesan_generik()
    {
        $user = User::factory()->create([
            'email'     => 'user.passwrongt@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordbenar'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);

        RateLimiter::clear($this->throttleKey('user.passwrongt@smkn1boyolangu.sch.id', '127.0.0.1'));

        $response = $this->post('/login', [
            'username' => 'user.passwrongt@smkn1boyolangu.sch.id',
            'password' => 'passwordsalah',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertEquals(
            'NIP, NISN, atau Email / Password tidak sesuai.',
            $response->getSession()->get('errors')->first('username')
        );
        $this->assertGuest();
    }

    // =========================================================================
    // 5. Akun tidak ditemukan → pesan IDENTIK dengan password salah
    // =========================================================================

    public function test_akun_tidak_ditemukan_pesan_identik_dengan_password_salah()
    {
        RateLimiter::clear($this->throttleKey('tidakada@smkn1boyolangu.sch.id', '127.0.0.1'));

        $response = $this->post('/login', [
            'username' => 'tidakada@smkn1boyolangu.sch.id',
            'password' => 'apapun',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertEquals(
            'NIP, NISN, atau Email / Password tidak sesuai.',
            $response->getSession()->get('errors')->first('username')
        );
        $this->assertGuest();
    }

    // =========================================================================
    // 6. Throttle: 6 kali gagal → ditahan (pesan N detik)
    // =========================================================================

    public function test_enam_kali_gagal_login_ditahan()
    {
        $email = 'throttle.test6@smkn1boyolangu.sch.id';
        RateLimiter::clear($this->throttleKey($email, '127.0.0.1'));

        // 5 percobaan gagal (batas MAX_ATTEMPTS)
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username' => $email,
                'password' => 'salah',
            ]);
        }

        // Percobaan ke-6: harus ditahan
        $response = $this->post('/login', [
            'username' => $email,
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('username');
        $errorMsg = $response->getSession()->get('errors')->first('username');
        $this->assertStringContainsString('Terlalu banyak percobaan login', $errorMsg);
        $this->assertStringContainsString('detik', $errorMsg);
        $this->assertGuest();
    }

    // =========================================================================
    // 7. Akun nonaktif + password BENAR → pesan khusus nonaktif
    // =========================================================================

    public function test_akun_nonaktif_password_benar_pesan_khusus()
    {
        $user = User::factory()->create([
            'email'     => 'nonaktif.benar@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordbenar'),
            'role'      => 'guru_mapel',
            'is_active' => false,
        ]);

        RateLimiter::clear($this->throttleKey('nonaktif.benar@smkn1boyolangu.sch.id', '127.0.0.1'));

        $response = $this->post('/login', [
            'username' => 'nonaktif.benar@smkn1boyolangu.sch.id',
            'password' => 'passwordbenar',
        ]);

        $response->assertSessionHasErrors('username');
        $errorMsg = $response->getSession()->get('errors')->first('username');
        $this->assertStringContainsString('dinonaktifkan oleh Administrator', $errorMsg);
        // Pastikan BUKAN pesan generik
        $this->assertStringNotContainsString('NIP, NISN, atau Email / Password tidak sesuai.', $errorMsg);
        $this->assertGuest();
    }

    // =========================================================================
    // 8. Akun nonaktif + password SALAH → pesan GENERIK (bukan "dinonaktifkan")
    // =========================================================================

    public function test_akun_nonaktif_password_salah_pesan_generik()
    {
        User::factory()->create([
            'email'     => 'nonaktif.salah@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordasli'),
            'role'      => 'guru_mapel',
            'is_active' => false,
        ]);

        RateLimiter::clear($this->throttleKey('nonaktif.salah@smkn1boyolangu.sch.id', '127.0.0.1'));

        $response = $this->post('/login', [
            'username' => 'nonaktif.salah@smkn1boyolangu.sch.id',
            'password' => 'passwordSALAH',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertEquals(
            'NIP, NISN, atau Email / Password tidak sesuai.',
            $response->getSession()->get('errors')->first('username')
        );
        // Pastikan BUKAN pesan dinonaktifkan (tidak bocorkan status akun)
        $this->assertStringNotContainsString(
            'dinonaktifkan',
            $response->getSession()->get('errors')->first('username')
        );
        $this->assertGuest();
    }

    // =========================================================================
    // 9. NIP ganda ANTAR TABEL (guru vs satpam): NIP sama di dua akun berbeda
    //    → login ditolak dan dicatat log peringatan.
    // =========================================================================

    public function test_nip_ganda_antar_tabel_login_ditolak_dan_ada_log()
    {
        $nipGanda = '881234567890000001';

        $userGuru = User::factory()->create([
            'email'     => 'guru.nip.ganda@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordguru'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        $userSatpam = User::factory()->create([
            'email'     => 'satpam.nip.ganda@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordsatpam'),
            'role'      => 'satpam',
            'is_active' => true,
        ]);

        Guru::create([
            'user_id'      => $userGuru->id,
            'nip'          => $nipGanda,
            'nama_lengkap' => 'Guru NIP Ganda',
            'status_aktif' => true,
        ]);

        Satpam::create([
            'user_id'      => $userSatpam->id,
            'nip'          => $nipGanda,
            'nama_lengkap' => 'Satpam NIP Ganda',
            'jenis_kelamin' => 'L',
            'pos_jaga'     => 'Gerbang Test',
        ]);

        RateLimiter::clear($this->throttleKey($nipGanda, '127.0.0.1'));

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($msg, $ctx) use ($nipGanda) {
                return str_contains($msg, 'NIP ganda')
                    && isset($ctx['nip'])
                    && $ctx['nip'] === $nipGanda;
            });

        $response = $this->post('/login', [
            'username' => $nipGanda,
            'password' => 'passwordguru',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertEquals(
            'NIP, NISN, atau Email / Password tidak sesuai.',
            $response->getSession()->get('errors')->first('username')
        );
        $this->assertGuest();
    }

    // =========================================================================
    // 11. NIP satpam → user ditemukan lewat jalur non-guru (satpam.nip)
    // =========================================================================

    public function test_nip_non_guru_ditemukan_lewat_jalur_satpam()
    {
        $nipSatpam = '771234567890000077';
        $user = User::factory()->create([
            'email'     => 'satpam.nip.test@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordsatpam'),
            'role'      => 'satpam',
            'is_active' => true,
        ]);
        Satpam::create([
            'user_id'      => $user->id,
            'nip'          => $nipSatpam,
            'nama_lengkap' => 'Satpam NIP Test',
            'jenis_kelamin' => 'L',
            'pos_jaga'     => 'Gerbang Test',
        ]);

        RateLimiter::clear($this->throttleKey($nipSatpam, '127.0.0.1'));

        $response = $this->post('/login', [
            'username' => $nipSatpam,
            'password' => 'passwordsatpam',
        ]);

        $response->assertRedirect();
        $expectedRoute = config('portal.portals.satpam.route', 'satpam.dashboard');
        $response->assertRedirect(route($expectedRoute));
        $this->assertAuthenticatedAs($user);
    }

    // =========================================================================
    // 12. HTML halaman login tidak memuat elemen role maupun demo
    // =========================================================================

    public function test_html_login_tidak_memuat_elemen_role_dan_demo()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);

        // Tidak ada dropdown role
        $response->assertDontSee('name="role"', false);
        $response->assertDontSee('Hak Akses / Role');
        $response->assertDontSee('Pilih Role');

        // Tidak ada tombol / modal demo
        $response->assertDontSee('Akun Demo');
        $response->assertDontSee('openDemoModal');
        $response->assertDontSee('demoAccounts');
        $response->assertDontSee('selectDemoRole');
        $response->assertDontSee('quickFillDemoCredential');

        // Elemen yang harus ada
        $response->assertSee('name="username"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="remember"', false);
        $response->assertSee('Masuk Sekarang');
        $response->assertSee('Lupa Password?');
    }

    // =========================================================================
    // 13. Throttle direset setelah login berhasil
    // =========================================================================

    public function test_throttle_direset_setelah_login_berhasil()
    {
        $email = 'reset.throttle@smkn1boyolangu.sch.id';
        $user  = User::factory()->create([
            'email'     => $email,
            'password'  => Hash::make('passwordbenar'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id'      => $user->id,
            'nip'          => '195555555555550005',
            'nama_lengkap' => 'Guru Reset Throttle',
            'status_aktif' => true,
        ]);

        $key = $this->throttleKey($email, '127.0.0.1');
        RateLimiter::clear($key);

        // 3 percobaan gagal
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'username' => $email,
                'password' => 'salah',
            ]);
        }
        $this->assertEquals(3, RateLimiter::attempts($key));

        // Login berhasil → hitungan harus 0
        $response = $this->post('/login', [
            'username' => $email,
            'password' => 'passwordbenar',
        ]);
        $response->assertRedirect();
        $this->assertEquals(0, RateLimiter::attempts($key));
    }

    // =========================================================================
    // 14. Sudah login → GET /login redirect ke portal default
    // =========================================================================

    public function test_sudah_login_akses_login_page_redirect_ke_portal()
    {
        $user = User::factory()->create([
            'email'     => 'already.loggedin@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);
        Guru::create([
            'user_id'      => $user->id,
            'nip'          => '196666666666660006',
            'nama_lengkap' => 'Guru Already Loggedin',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($user)->get('/login');

        // Harus redirect, bukan tampilkan form login
        $response->assertRedirect();
        $this->assertStringNotContainsString('/login', $response->headers->get('Location') ?? '');
    }

    // =========================================================================
    // 15. User aktif tanpa portal → respons 403 ramah, TANPA redirect loop
    // =========================================================================

    public function test_user_aktif_tanpa_portal_dapat_403_ramah_bukan_loop()
    {
        // User aktif dengan role 'waka' (enum valid), namun belum memiliki data di tabel waka
        // maupun tabel guru. PortalResolver tidak menemukan portal yang cocok
        // sehingga defaultPortal() mengembalikan null.
        // AuthController harus merespons dengan 403 ramah (bukan redirect loop).
        $user = User::factory()->create([
            'email'     => 'noportal.waka@smkn1boyolangu.sch.id',
            'password'  => Hash::make('password123'),
            'role'      => 'waka',
            'is_active' => true,
        ]);
        // Tidak membuat record guru maupun waka

        RateLimiter::clear($this->throttleKey('noportal.waka@smkn1boyolangu.sch.id', '127.0.0.1'));

        $response = $this->post('/login', [
            'username' => 'noportal.waka@smkn1boyolangu.sch.id',
            'password' => 'password123',
        ]);

        // Harus 403, bukan redirect (bukan loop ke /login)
        $response->assertStatus(403);
        $this->assertFalse($response->isRedirect(), 'Seharusnya 403, bukan redirect loop');

        // Halaman harus ramah: ada judul penolakan dan tombol Keluar
        $response->assertSee('Akses Portal Ditolak');
        $response->assertSee('Keluar');
    }

    // =========================================================================
    // 16. Throttle ternormalisasi: huruf besar dan kecil dihitung satu kunci
    // =========================================================================

    public function test_throttle_case_insensitive_dihitung_satu_kunci()
    {
        $emailLower = 'budi.test@smkn1boyolangu.sch.id';
        $emailUpper = 'Budi.Test@smkn1boyolangu.sch.id';  // Huruf besar

        RateLimiter::clear($this->throttleKey($emailLower, '127.0.0.1'));
        RateLimiter::clear($this->throttleKey($emailUpper, '127.0.0.1'));

        // 3 percobaan gagal dengan huruf kecil
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'username' => $emailLower,
                'password' => 'salah',
            ]);
        }

        // 2 percobaan gagal dengan huruf besar (total harusnya 5 = batas)
        for ($i = 0; $i < 2; $i++) {
            $this->post('/login', [
                'username' => $emailUpper,
                'password' => 'salah',
            ]);
        }

        // Percobaan ke-6 dengan huruf besar → harus ditahan
        $response = $this->post('/login', [
            'username' => $emailUpper,
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('username');
        $errorMsg = $response->getSession()->get('errors')->getBag('default')->first('username');
        $this->assertStringContainsString(
            'Terlalu banyak percobaan login',
            $errorMsg,
            'Throttle harus menghitung huruf besar & kecil sebagai kunci yang sama'
        );
    }

    // =========================================================================
    // 17. Pesan error identik byte-by-byte: akun tidak ditemukan vs password salah
    // =========================================================================

    public function test_pesan_error_identik_untuk_tidak_ditemukan_dan_password_salah()
    {
        // Buat user yang ada
        User::factory()->create([
            'email'     => 'ada.di.db@smkn1boyolangu.sch.id',
            'password'  => Hash::make('passwordbenar'),
            'role'      => 'guru_mapel',
            'is_active' => true,
        ]);

        RateLimiter::clear($this->throttleKey('ada.di.db@smkn1boyolangu.sch.id', '127.0.0.1'));
        RateLimiter::clear($this->throttleKey('tidak.ada.di.db@smkn1boyolangu.sch.id', '127.0.0.1'));

        // Skenario A: akun ada, password salah
        $respA = $this->post('/login', [
            'username' => 'ada.di.db@smkn1boyolangu.sch.id',
            'password' => 'passwordSALAH',
        ]);
        $respA->assertSessionHasErrors('username');
        $pesanA = $respA->getSession()->get('errors')->getBag('default')->first('username');

        // Skenario B: akun tidak ada sama sekali
        $respB = $this->post('/login', [
            'username' => 'tidak.ada.di.db@smkn1boyolangu.sch.id',
            'password' => 'passwordapapun',
        ]);
        $respB->assertSessionHasErrors('username');
        $pesanB = $respB->getSession()->get('errors')->getBag('default')->first('username');

        // Pesan harus identik BYTE PER BYTE
        $this->assertNotNull($pesanA, 'Harus ada pesan error untuk password salah');
        $this->assertNotNull($pesanB, 'Harus ada pesan error untuk akun tidak ditemukan');
        $this->assertSame($pesanA, $pesanB, 'Pesan error harus identik — tidak boleh bocorkan apakah akun ada atau tidak');

        // Keduanya harus pesan generik
        $this->assertEquals('NIP, NISN, atau Email / Password tidak sesuai.', $pesanA);
    }
}
