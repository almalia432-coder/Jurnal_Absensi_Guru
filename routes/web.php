<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JurnalMengajarController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAbsensiController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\AdminJadwalPiketController;
use App\Http\Controllers\MasterUserController;
use App\Http\Controllers\MasterSiswaController;
use App\Http\Controllers\MasterGuruController;
use App\Http\Controllers\MasterJurusanController;
use App\Http\Controllers\MasterKelasController;
use App\Http\Controllers\MasterMapelController;
use App\Http\Controllers\AdminLaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\WaliKelasDashboardController;
use App\Http\Controllers\GuruPiketDashboardController;
use App\Http\Controllers\GuruMapelDashboardController;
use App\Http\Controllers\SatpamDashboardController;
use App\Http\Controllers\WakaKurikulumDashboardController;
use App\Http\Controllers\WakaKurikulumMapelController;
use App\Http\Controllers\WakaKurikulumGuruMengajarController;
use App\Http\Controllers\WakaKurikulumJadwalController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\WakaSdmDashboardController;
use App\Http\Controllers\WaliMuridDashboardController;
use App\Http\Controllers\KepalaSekolahDashboardController;
use App\Http\Controllers\WakaPiketController;
use App\Http\Controllers\WakaKesiswaanDashboardController;
use App\Http\Controllers\IzinTerlambatController;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    $user = Auth::user();
    $defaultKey = $user->defaultPortal();
    if (!$defaultKey) {
        return response()->view('errors.403_portal', [
            'user'             => $user,
            'requiredPortals'  => [],
            'availablePortals' => [],
        ], 403);
    }
    $route = config("portal.portals.{$defaultKey}.route", 'login');
    return redirect()->route($route);
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (auth protected)
Route::prefix('admin')->middleware(['auth', 'portal:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Notifikasi API Endpoints
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('admin.notifikasi');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('admin.notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('admin.notifikasi.read-all');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('admin.notifikasi.destroy');

    // Absensi & Jurnal Mengajar
    Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('admin.absensi');
    Route::get('/absensi/export', [AdminAbsensiController::class, 'export'])->name('admin.absensi.export');
    Route::get('/absensi/{id}', [AdminAbsensiController::class, 'show'])->name('admin.absensi.show');

    // Jadwal Pelajaran
    Route::get('/jadwal', [JadwalPelajaranController::class, 'index'])->name('admin.jadwal');
    Route::post('/jadwal', [JadwalPelajaranController::class, 'store'])->name('admin.jadwal.store');
    Route::post('/jadwal/import-csv', [CsvImportController::class, 'importJadwal'])->name('admin.jadwal.import-csv');
    Route::post('/jadwal/set-tahun-ajaran', [JadwalPelajaranController::class, 'setTahunAjaran'])->name('admin.jadwal.set-ta');
    Route::post('/jadwal/swap', [JadwalPelajaranController::class, 'swap'])->name('admin.jadwal.swap');
    Route::post('/jadwal/{id}/move', [JadwalPelajaranController::class, 'move'])->name('admin.jadwal.move');
    Route::put('/jadwal/{id}', [JadwalPelajaranController::class, 'update'])->name('admin.jadwal.update');
    Route::delete('/jadwal/{id}', [JadwalPelajaranController::class, 'destroy'])->name('admin.jadwal.destroy');

    // Jadwal Guru Piket KBM
    Route::get('/jadwal-piket', [AdminJadwalPiketController::class, 'index'])->name('admin.jadwal-piket');
    Route::post('/jadwal-piket', [AdminJadwalPiketController::class, 'store'])->name('admin.jadwal-piket.store');
    Route::put('/jadwal-piket/{id}', [AdminJadwalPiketController::class, 'update'])->name('admin.jadwal-piket.update');
    Route::delete('/jadwal-piket/{id}', [AdminJadwalPiketController::class, 'destroy'])->name('admin.jadwal-piket.destroy');
    Route::post('/jadwal-piket/update-waka', [AdminJadwalPiketController::class, 'updateWaka'])->name('admin.jadwal-piket.update-waka');

    // Download CSV Templates
    Route::get('/import/template/{type}', [CsvImportController::class, 'downloadTemplate'])->name('admin.import.template');

    // Master Data sub-menu
    Route::prefix('master')->name('admin.master.')->group(function () {
        // User CRUD (Manajemen User di atas Siswa)
        Route::get('/user', [MasterUserController::class, 'index'])->name('user');
        Route::post('/user', [MasterUserController::class, 'store'])->name('user.store');
        Route::put('/user/{id}', [MasterUserController::class, 'update'])->name('user.update');
        Route::post('/user/{id}/toggle-status', [MasterUserController::class, 'toggleStatus'])->name('user.toggle-status');
        Route::delete('/user/{id}', [MasterUserController::class, 'destroy'])->name('user.destroy');

        // Siswa CRUD & Import
        Route::get('/siswa', [MasterSiswaController::class, 'index'])->name('siswa');
        Route::post('/siswa', [MasterSiswaController::class, 'store'])->name('siswa.store');
        Route::post('/siswa/import-csv', [CsvImportController::class, 'importSiswa'])->name('siswa.import-csv');
        Route::put('/siswa/{id}', [MasterSiswaController::class, 'update'])->name('siswa.update');
        Route::delete('/siswa/{id}', [MasterSiswaController::class, 'destroy'])->name('siswa.destroy');

        // Guru CRUD & Import
        Route::get('/guru', [MasterGuruController::class, 'index'])->name('guru');
        Route::post('/guru', [MasterGuruController::class, 'store'])->name('guru.store');
        Route::post('/guru/import-csv', [CsvImportController::class, 'importGuru'])->name('guru.import-csv');
        Route::put('/guru/{id}', [MasterGuruController::class, 'update'])->name('guru.update');
        Route::delete('/guru/{id}', [MasterGuruController::class, 'destroy'])->name('guru.destroy');

        // Jurusan CRUD
        Route::get('/jurusan', [MasterJurusanController::class, 'index'])->name('jurusan');
        Route::post('/jurusan', [MasterJurusanController::class, 'store'])->name('jurusan.store');
        Route::put('/jurusan/{id}', [MasterJurusanController::class, 'update'])->name('jurusan.update');
        Route::delete('/jurusan/{id}', [MasterJurusanController::class, 'destroy'])->name('jurusan.destroy');

        // Kelas CRUD
        Route::get('/kelas', [MasterKelasController::class, 'index'])->name('kelas');
        Route::post('/kelas', [MasterKelasController::class, 'store'])->name('kelas.store');
        Route::put('/kelas/{id}', [MasterKelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{id}', [MasterKelasController::class, 'destroy'])->name('kelas.destroy');

        // Mapel CRUD & Import
        Route::get('/mapel', [MasterMapelController::class, 'index'])->name('mapel');
        Route::post('/mapel', [MasterMapelController::class, 'store'])->name('mapel.store');
        Route::post('/mapel/import-csv', [CsvImportController::class, 'importMapel'])->name('mapel.import-csv');
        Route::put('/mapel/{id}', [MasterMapelController::class, 'update'])->name('mapel.update');
        Route::delete('/mapel/{id}', [MasterMapelController::class, 'destroy'])->name('mapel.destroy');
    });

    // Laporan (Pusat Rekap)
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('admin.laporan');
    Route::get('/laporan/export', [AdminLaporanController::class, 'exportCsv'])->name('admin.laporan.export');
    Route::get('/laporan/print', [AdminLaporanController::class, 'printView'])->name('admin.laporan.print');

    Route::get('/help', [AdminDashboardController::class, 'help'])->name('admin.help');

    // Profil Pengguna
    Route::get('/profil', [ProfileController::class, 'index'])->name('admin.profil');
    Route::put('/profil/info', [ProfileController::class, 'updateInfo'])->name('admin.profil.info');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('admin.profil.password');
    Route::post('/profil/photo', [ProfileController::class, 'updatePhoto'])->name('admin.profil.photo');
});

// Wali Kelas Routes (auth protected)
Route::prefix('wali-kelas')->middleware(['auth', 'portal:wali_kelas'])->group(function () {
    Route::get('/dashboard', [WaliKelasDashboardController::class, 'index'])->name('wali-kelas.dashboard');
    Route::get('/siswa', [WaliKelasDashboardController::class, 'siswa'])->name('wali-kelas.siswa');
    Route::get('/jurnal', [WaliKelasDashboardController::class, 'jurnal'])->name('wali-kelas.jurnal');
    Route::get('/terlambat', [IzinTerlambatController::class, 'waliKelasIndex'])->name('wali-kelas.terlambat');
});

// Guru Piket Routes (auth protected)
Route::prefix('guru-piket')->name('guru-piket.')->middleware(['auth', 'portal:piket'])->group(function () {
    Route::get('/dashboard', [GuruPiketDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring-kelas', [GuruPiketDashboardController::class, 'monitoringKelas'])->name('monitoring');
    
    // Dispensasi Siswa
    Route::get('/dispensasi', [GuruPiketDashboardController::class, 'dispensasi'])->name('dispensasi');
    Route::post('/dispensasi', [GuruPiketDashboardController::class, 'storeDispensasi'])->name('dispensasi.store');
    Route::post('/dispensasi/{id}/status', [GuruPiketDashboardController::class, 'updateDispensasiStatus'])->name('dispensasi.status');
    Route::get('/dispensasi/{id}/cetak', [GuruPiketDashboardController::class, 'cetakDispensasi'])->name('dispensasi.cetak');
    
    // Izin Masuk Kelas Siswa Terlambat
    Route::get('/terlambat', [IzinTerlambatController::class, 'index'])->name('terlambat.index');
    Route::post('/terlambat', [IzinTerlambatController::class, 'store'])->name('terlambat.store');
    Route::put('/terlambat/{id}', [IzinTerlambatController::class, 'update'])->name('terlambat.update');
    Route::delete('/terlambat/{id}', [IzinTerlambatController::class, 'destroy'])->name('terlambat.destroy');
    Route::get('/terlambat/{id}/cetak', [IzinTerlambatController::class, 'cetak'])->name('terlambat.cetak');

    // Perizinan Siswa (Sakit, Izin, Dispen)
    Route::get('/izin-siswa', [GuruPiketDashboardController::class, 'izinSiswa'])->name('izin-siswa');
    Route::post('/izin-siswa', [GuruPiketDashboardController::class, 'storeIzinSiswa'])->name('izin-siswa.store');
    Route::delete('/izin-siswa/{id}', [GuruPiketDashboardController::class, 'destroyIzinSiswa'])->name('izin-siswa.destroy');
    
    // Izin Guru & Kelas Terdampak
    Route::get('/izin-guru', [GuruPiketDashboardController::class, 'izinGuru'])->name('izin-guru');
    Route::post('/izin-guru/{id}/status', [GuruPiketDashboardController::class, 'updateStatusIzin'])->name('izin-guru.status');
    Route::get('/jurnal-pendampingan/{id_jadwal}', [GuruPiketDashboardController::class, 'formJurnalPendampingan'])->name('jurnal.pendampingan');
    Route::post('/jurnal-pendampingan/{id_jadwal}', [GuruPiketDashboardController::class, 'storeJurnalPendampingan'])->name('jurnal.pendampingan.store');
    
    // Rekap Presensi Siswa Se-Sekolah
    Route::get('/rekap-presensi', [GuruPiketDashboardController::class, 'rekapPresensi'])->name('rekap');
    
    // Status KBM Harian (Upacara / Pembiasaan Ditiadakan)
    Route::post('/status-kbm/toggle', [GuruPiketDashboardController::class, 'toggleStatusKbm'])->name('status-kbm.toggle');

    // Catatan & Laporan Harian Piket
    Route::get('/laporan', [GuruPiketDashboardController::class, 'laporan'])->name('laporan');
    Route::post('/laporan', [GuruPiketDashboardController::class, 'storeLaporan'])->name('laporan.store');
    Route::get('/laporan/cetak', [GuruPiketDashboardController::class, 'cetakLaporan'])->name('laporan.cetak');
    
    // Jadwal Petugas Guru Piket
    Route::get('/jadwal', [GuruPiketDashboardController::class, 'jadwalPiket'])->name('jadwal');
    Route::get('/jadwal/cetak', [GuruPiketDashboardController::class, 'cetakJadwalPiket'])->name('jadwal.cetak');

    // Panduan Piket
    Route::get('/help', [GuruPiketDashboardController::class, 'help'])->name('help');
});

// Guru Mapel Routes (auth protected)
Route::prefix('guru-mapel')->name('guru-mapel.')->middleware(['auth', 'portal:guru_mengajar'])->group(function () {
    Route::get('/dashboard', [GuruMapelDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jadwal', [GuruMapelDashboardController::class, 'jadwal'])->name('jadwal');
    
    // Jurnal Mengajar & Presensi
    Route::get('/jurnal/create', [GuruMapelDashboardController::class, 'createJurnal'])->name('jurnal.create');
    Route::post('/jurnal', [GuruMapelDashboardController::class, 'storeJurnal'])->name('jurnal.store');
    Route::get('/jurnal/riwayat', [GuruMapelDashboardController::class, 'riwayatJurnal'])->name('jurnal.riwayat');
    Route::get('/jurnal/{id}', [GuruMapelDashboardController::class, 'showJurnal'])->name('jurnal.show');
    
    // Rekap Presensi Siswa Mapel
    Route::get('/rekap', [GuruMapelDashboardController::class, 'rekapPresensi'])->name('rekap');
    
    // Izin Tidak Mengajar
    Route::get('/izin', [GuruMapelDashboardController::class, 'izin'])->name('izin');
    Route::post('/izin', [GuruMapelDashboardController::class, 'storeIzin'])->name('izin.store');
    
    // Panduan Guru Mapel
    Route::get('/help', [GuruMapelDashboardController::class, 'help'])->name('help');
});

// Satpam / Pos Jaga Routes (auth protected)
Route::prefix('satpam')->name('satpam.')->middleware(['auth', 'portal:satpam'])->group(function () {
    Route::get('/dashboard', [SatpamDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [SatpamDashboardController::class, 'monitoring'])->name('monitoring');
    
    // Monitoring Siswa Terlambat Hari Ini
    Route::get('/terlambat', [IzinTerlambatController::class, 'satpamIndex'])->name('terlambat');
    
    // Verifikasi & Konfirmasi Dispensasi
    Route::get('/dispensasi', [SatpamDashboardController::class, 'dispensasi'])->name('dispensasi');
    Route::post('/dispensasi/{id}/status', [SatpamDashboardController::class, 'updateStatusDispensasi'])->name('dispensasi.status');
    
    // Buku Tamu Digital
    Route::get('/buku-tamu', [SatpamDashboardController::class, 'bukuTamu'])->name('buku-tamu');
    Route::post('/buku-tamu', [SatpamDashboardController::class, 'storeBukuTamu'])->name('buku-tamu.store');
    Route::post('/buku-tamu/{id}/checkout', [SatpamDashboardController::class, 'checkoutBukuTamu'])->name('buku-tamu.checkout');
    
    // Riwayat & Cetak Log Pos Jaga
    Route::get('/riwayat', [SatpamDashboardController::class, 'riwayat'])->name('riwayat');
    Route::get('/riwayat/cetak', [SatpamDashboardController::class, 'cetakLaporan'])->name('riwayat.cetak');
    
    // Panduan Satpam Gate
    Route::get('/help', [SatpamDashboardController::class, 'help'])->name('help');
});

// Waka Kurikulum Routes (auth protected)
Route::prefix('waka-kurikulum')->name('waka-kurikulum.')->middleware(['auth', 'portal:waka_kurikulum'])->group(function () {
    Route::get('/dashboard', [WakaKurikulumDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jurnal', [WakaKurikulumDashboardController::class, 'jurnal'])->name('jurnal');

    // Manajemen Mata Pelajaran
    Route::get('/mapel', [WakaKurikulumMapelController::class, 'index'])->name('mapel.index');
    Route::post('/mapel', [WakaKurikulumMapelController::class, 'store'])->name('mapel.store');
    Route::post('/mapel/import-csv', [CsvImportController::class, 'importMapel'])->name('mapel.import-csv');
    Route::put('/mapel/{id}', [WakaKurikulumMapelController::class, 'update'])->name('mapel.update');
    Route::delete('/mapel/{id}', [WakaKurikulumMapelController::class, 'destroy'])->name('mapel.destroy');
    Route::get('/mapel/{id}/detail', [WakaKurikulumMapelController::class, 'detail'])->name('mapel.detail');

    // Pengaturan Guru Mengajar & Rekap Beban Mengajar (JP)
    Route::get('/guru-mengajar', [WakaKurikulumGuruMengajarController::class, 'index'])->name('guru-mengajar.index');
    Route::get('/guru-mengajar/export', [WakaKurikulumGuruMengajarController::class, 'exportCsv'])->name('guru-mengajar.export');
    Route::get('/guru-mengajar/{id}', [WakaKurikulumGuruMengajarController::class, 'detail'])->name('guru-mengajar.detail');
    Route::post('/guru-mengajar/plotting', [WakaKurikulumGuruMengajarController::class, 'storePlotting'])->name('guru-mengajar.plotting');
    Route::delete('/guru-mengajar/{id}/clear-jadwal', [WakaKurikulumGuruMengajarController::class, 'clearJadwalGuru'])->name('guru-mengajar.clear-jadwal');

    // Pengaturan & Manajemen Jadwal Pelajaran
    Route::get('/jadwal', [WakaKurikulumJadwalController::class, 'index'])->name('jadwal');
    Route::post('/jadwal', [WakaKurikulumJadwalController::class, 'store'])->name('jadwal.store');
    Route::post('/jadwal/import-csv', [CsvImportController::class, 'importJadwal'])->name('jadwal.import-csv');
    Route::post('/jadwal/set-tahun-ajaran', [WakaKurikulumJadwalController::class, 'setTahunAjaran'])->name('jadwal.set-ta');
    Route::post('/jadwal/swap', [WakaKurikulumJadwalController::class, 'swap'])->name('jadwal.swap');
    Route::post('/jadwal/{id}/move', [WakaKurikulumJadwalController::class, 'move'])->name('jadwal.move');
    Route::put('/jadwal/{id}', [WakaKurikulumJadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [WakaKurikulumJadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Download CSV Templates for Waka
    Route::get('/import/template/{type}', [CsvImportController::class, 'downloadTemplate'])->name('import.template');
});

// Waka SDM / Kepegawaian Routes (auth protected)
Route::prefix('waka-sdm')->name('waka-sdm.')->middleware(['auth', 'portal:waka_sdm'])->group(function () {
    Route::get('/dashboard', [WakaSdmDashboardController::class, 'index'])->name('dashboard');
    Route::get('/guru', [WakaSdmDashboardController::class, 'guru'])->name('guru');

    // Persetujuan Izin Guru
    Route::get('/izin', [WakaSdmDashboardController::class, 'izin'])->name('izin');
    Route::post('/izin/{id}/status', [WakaSdmDashboardController::class, 'updateStatusIzin'])->name('izin.status');

    // Persetujuan Dispensasi Siswa
    Route::get('/dispensasi', [WakaSdmDashboardController::class, 'dispensasi'])->name('dispensasi');
    Route::post('/dispensasi/{id}/status', [WakaSdmDashboardController::class, 'updateStatusDispensasi'])->name('dispensasi.status');

    // Laporan Rekapitulasi Persetujuan
    Route::get('/laporan', [WakaSdmDashboardController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/export', [WakaSdmDashboardController::class, 'exportLaporan'])->name('laporan.export');

    // Pusat Bantuan / SOP
    Route::get('/help', [WakaSdmDashboardController::class, 'help'])->name('help');
});

// Waka Kesiswaan & Kedisiplinan Routes (auth protected)
Route::prefix('waka-kesiswaan')->name('waka-kesiswaan.')->middleware(['auth', 'portal:waka_kesiswaan'])->group(function () {
    Route::get('/dashboard', [WakaKesiswaanDashboardController::class, 'index'])->name('dashboard');
    Route::get('/presensi', [WakaKesiswaanDashboardController::class, 'presensi'])->name('presensi');
    Route::get('/dispensasi', [WakaKesiswaanDashboardController::class, 'dispensasi'])->name('dispensasi');
    Route::get('/kedisiplinan', [WakaKesiswaanDashboardController::class, 'rekapKedisiplinan'])->name('kedisiplinan');
    Route::get('/rekap-terlambat', [IzinTerlambatController::class, 'rekap'])->name('rekap-terlambat');
});

// Wali Murid / Siswa Portal Routes (auth protected)
Route::prefix('wali-murid')->name('wali-murid.')->middleware(['auth', 'portal:wali_murid'])->group(function () {
    Route::get('/dashboard', [WaliMuridDashboardController::class, 'index'])->name('dashboard');
    Route::get('/presensi', [WaliMuridDashboardController::class, 'presensi'])->name('presensi');
    Route::get('/jurnal', [WaliMuridDashboardController::class, 'jurnal'])->name('jurnal');
    Route::get('/jadwal', [WaliMuridDashboardController::class, 'jadwal'])->name('jadwal');
    Route::get('/dispensasi', [WaliMuridDashboardController::class, 'dispensasi'])->name('dispensasi');
});

// Kepala Sekolah Routes (auth protected)
Route::prefix('kepala-sekolah')->name('kepala-sekolah.')->middleware(['auth', 'portal:kepala_sekolah'])->group(function () {
    Route::get('/dashboard', [KepalaSekolahDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/izin-guru',  [KepalaSekolahDashboardController::class, 'izinGuru'])->name('izin-guru');
    Route::post('/izin-guru/{id}/status', [KepalaSekolahDashboardController::class, 'updateStatusIzin'])->name('izin-guru.status');
    Route::get('/dispensasi', [KepalaSekolahDashboardController::class, 'dispensasi'])->name('dispensasi');
    Route::get('/rekap-terlambat', [IzinTerlambatController::class, 'rekap'])->name('rekap-terlambat');
});

// Waka Piket KBM Routes (Persetujuan Dispensasi Siswa Harian & Izin Terlambat)
Route::prefix('waka-piket')->name('waka-piket.')->middleware(['auth', 'portal:piket_waka'])->group(function () {
    Route::get('/dispensasi', [WakaPiketController::class, 'dispensasi'])->name('dispensasi');
    Route::post('/dispensasi/{id}/status', [WakaPiketController::class, 'updateStatus'])->name('dispensasi.status');

    // Persetujuan Izin Masuk Kelas Siswa Terlambat
    Route::get('/terlambat', [IzinTerlambatController::class, 'wakaIndex'])->name('terlambat.index');
    Route::post('/terlambat/{id}/konfirmasi', [IzinTerlambatController::class, 'konfirmasi'])->name('terlambat.konfirmasi');
});

Route::resource('jurnal', JurnalMengajarController::class)->middleware(['auth', 'portal:guru_mengajar,admin']);