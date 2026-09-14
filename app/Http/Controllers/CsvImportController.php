<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Guru;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use App\Models\GuruMapel;
use App\Models\GuruPiket;

class CsvImportController extends Controller
{
    /**
     * Download template CSV contoh dengan header UTF-8 BOM
     */
    public function downloadTemplate($type)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Cache-Control'       => 'no-store, no-cache',
            'Pragma'              => 'no-cache',
        ];

        // UTF-8 BOM (\xEF\xBB\xBF) agar saat dibuka di Excel Windows kolomnya otomatis terpisah rapi
        $bom = "\xEF\xBB\xBF";
        $csv = "";

        switch ($type) {
            case 'guru':
                $headers['Content-Disposition'] = 'attachment; filename="template_import_guru.csv"';
                $csv = "nip,nama_lengkap,jenis_kelamin,role,email,no_hp,alamat\n"
                    . "198501152010011005,Drs. Ahmad Fauzi M.Pd,L,guru_mapel,ahmad.fauzi@smkn1boyolangu.sch.id,081234567890,Tulungagung\n"
                    . "199003202015022003,Siti Rahmawati S.Pd,P,wali_kelas,siti.rahma@smkn1boyolangu.sch.id,081298765432,Boyolangu\n"
                    . "199208142019031002,Budi Santoso S.Kom,L,guru_piket,budi.santoso@smkn1boyolangu.sch.id,081377889900,Kedungwaru\n";
                break;

            case 'siswa':
                $headers['Content-Disposition'] = 'attachment; filename="template_import_siswa_boyolangu.csv"';
                // Format standar sesuai lembar presensi peserta didik SMKN 1 Boyolangu
                $csv = "NO,NISN,NAMA,N I S S,L/P,KELAS\n"
                    . "1,0105292765,ADINDA PUTRI YUNIKA,,P,X TKI 1\n"
                    . "2,0116855187,AHMAD ANIS ARIFIANSYAH,,L,X TKI 1\n"
                    . "3,0117713358,AINUN KAROMAH,,P,X TKI 1\n"
                    . "4,0101749815,AJENG DWIKY SIVINIAS,,P,X TKI 1\n"
                    . "5,0104663554,ALENA REGINA PUTRI,,P,X TKI 1\n"
                    . "6,0106174478,ALFIRA ZALFAA HERWIARTA,,P,X TKI 1\n"
                    . "7,0103045458,ALISCA PRICILYA,,P,X TKI 1\n"
                    . "8,0109005753,AMELLIA ALYA ADRIANA,,P,X TKI 1\n"
                    . "9,0109120467,ANDARA SHANTIKA DEWI,,P,X TKI 1\n"
                    . "10,0104046974,ARUM DWI WIDIARTI,,P,X TKI 1\n"
                    . "11,0111829419,ASYIFA ASHARA ROCHAHYANI,,P,X TKI 1\n"
                    . "12,0101089221,AULIA DEWI TSURSYINA,,P,X TKI 1\n";
                break;

            case 'mapel':
                $headers['Content-Disposition'] = 'attachment; filename="template_import_mapel.csv"';
                $csv = "kode_mapel,nama_mapel,kelompok\n"
                    . "BINDO,Bahasa Indonesia,Normatif\n"
                    . "MAT,Matematika,Normatif\n"
                    . "AKL01,Akuntansi Dasar,Produktif\n"
                    . "PJOK,Pendidikan Jasmani Olahraga dan Kesehatan,Adaptif\n"
                    . "BJAWA,Bahasa Jawa,Muatan_Lokal\n";
                break;

            case 'jadwal':
                $headers['Content-Disposition'] = 'attachment; filename="template_import_jadwal.csv"';
                $csv = "nama_kelas,mapel,guru,hari,jam_dari,jam_sampai,tahun_ajaran,semester\n"
                    . "X AKL 1,BINDO,198501152010011005,Senin,2,4,2026/2027,Ganjil\n"
                    . "X AKL 1,MAT,Siti Rahmawati S.Pd,Selasa,1,3,2026/2027,Ganjil\n"
                    . "X AKL 1,AKL01,198501152010011005,Rabu,1,4,2026/2027,Ganjil\n"
                    . "X AKL 1,PJOK,Budi Santoso S.Kom,Jumat,2,3,2026/2027,Ganjil\n";
                break;

            default:
                abort(404, 'Template tidak ditemukan');
        }

        return response($bom . $csv, 200, $headers);
    }

    /**
     * Import Data Guru dari CSV
     */
    public function importGuru(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|max:5120',
        ]);

        $file = $request->file('file_csv');
        $rows = $this->parseCsv($file);

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau tidak memiliki data yang valid.');
        }

        $header = array_map('trim', array_map('strtolower', array_shift($rows)));
        $nipIdx     = $this->findHeaderIndex($header, ['nip', 'no_induk', 'id_guru']);
        $namaIdx    = $this->findHeaderIndex($header, ['nama_lengkap', 'nama', 'nama guru']);
        $jkIdx      = $this->findHeaderIndex($header, ['jenis_kelamin', 'jk', 'gender']);
        $roleIdx    = $this->findHeaderIndex($header, ['role', 'jabatan', 'posisi']);
        $emailIdx   = $this->findHeaderIndex($header, ['email', 'surel']);
        $hpIdx      = $this->findHeaderIndex($header, ['no_hp', 'telepon', 'hp', 'no_telepon']);
        $alamatIdx  = $this->findHeaderIndex($header, ['alamat', 'domisili']);

        if ($nipIdx === null || $namaIdx === null) {
            return back()->with('error', 'Format CSV tidak valid. Wajib menyertakan kolom: nip dan nama_lengkap.');
        }

        $imported = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $lineNum => $row) {
                if (count($row) < 2 || empty(trim($row[$namaIdx] ?? ''))) {
                    continue;
                }

                $nip         = trim($row[$nipIdx] ?? '');
                $namaLengkap = trim($row[$namaIdx] ?? '');
                $jkRaw       = strtoupper(trim($row[$jkIdx] ?? 'L'));
                $jk          = in_array($jkRaw, ['L', 'P']) ? $jkRaw : 'L';
                $roleRaw     = strtolower(trim($row[$roleIdx] ?? 'guru_mapel'));
                $role        = in_array($roleRaw, ['wali_kelas', 'guru_piket', 'guru_mapel']) ? $roleRaw : 'guru_mapel';
                $noHp        = !empty($hpIdx) ? trim($row[$hpIdx] ?? '') : null;
                $alamat      = !empty($alamatIdx) ? trim($row[$alamatIdx] ?? '') : null;

                if (empty($nip) || empty($namaLengkap)) {
                    $skipped++;
                    $errors[] = "Baris " . ($lineNum + 2) . ": NIP atau Nama Guru kosong.";
                    continue;
                }

                // Email handling
                $email = (!empty($emailIdx) && !empty(trim($row[$emailIdx] ?? '')))
                    ? trim($row[$emailIdx])
                    : (strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nip)) . '@smkn1boyolangu.sch.id');

                // Cari atau buat User
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = User::create([
                        'name'      => $namaLengkap,
                        'email'     => $email,
                        'password'  => Hash::make('password123'),
                        'role'      => $role,
                        'is_active' => true,
                    ]);
                } else {
                    $user->update([
                        'name' => $namaLengkap,
                        'role' => $role,
                    ]);
                }

                // Cari atau buat Guru
                $guru = Guru::where('nip', $nip)->first();
                if ($guru) {
                    $guru->update([
                        'user_id'       => $user->id,
                        'nama_lengkap'  => $namaLengkap,
                        'jenis_kelamin' => $jk,
                        'no_hp'         => $noHp ?: $guru->no_hp,
                        'alamat'        => $alamat ?: $guru->alamat,
                        'status_aktif'  => true,
                    ]);
                    $updated++;
                } else {
                    Guru::create([
                        'user_id'       => $user->id,
                        'nip'           => $nip,
                        'nama_lengkap'  => $namaLengkap,
                        'jenis_kelamin' => $jk,
                        'no_hp'         => $noHp,
                        'alamat'        => $alamat,
                        'status_aktif'  => true,
                    ]);
                    $imported++;
                }

                // Sinkronisasi tabel peran (WaliKelas, GuruMapel, GuruPiket)
                $this->syncGuruRole($user, $role, $nip, $namaLengkap, $jk, $noHp);
            }

            DB::commit();

            $msg = "Impor CSV Guru berhasil! {$imported} data baru ditambahkan, {$updated} diperbarui.";
            if ($skipped > 0) {
                $msg .= " ({$skipped} baris dilewati karena format tidak lengkap).";
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses impor file CSV: ' . $e->getMessage());
        }
    }

    /**
     * Import Data Siswa dari CSV
     * Mendukung format daftar presensi SMKN 1 Boyolangu: NO, NISN, NAMA, N I S S, L/P, KELAS
     */
    public function importSiswa(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|max:5120',
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
        ]);

        $file = $request->file('file_csv');
        $rawRows = $this->parseCsv($file);

        if (empty($rawRows)) {
            return back()->with('error', 'File CSV kosong atau tidak memiliki data yang valid.');
        }

        // Cache seluruh kelas untuk pencocokan cepat
        $allKelas = Kelas::all();

        // 1. Cek apakah ada kelas target yang dipilih dari dropdown modal
        $defaultKelas = null;
        if ($request->filled('id_kelas')) {
            $defaultKelas = $allKelas->firstWhere('id_kelas', $request->input('id_kelas'));
        }

        // 2. Deteksi header tabel dan metadata "Kelas : X TKI 1" jika file berasal dari export sheet Excel
        $detectedKelasName = null;
        $headerRowIdx = null;

        foreach ($rawRows as $idx => $row) {
            $rowStr = implode(' ', $row);

            // Deteksi baris metadata "Kelas : X TKI 1"
            if (!$detectedKelasName && preg_match('/kelas\s*[:=,]\s*([a-zA-Z0-9\s\-]+)/i', $rowStr, $matches)) {
                $candidate = trim(explode("\n", $matches[1])[0]);
                $candidate = preg_split('/(wali|tahun|jurusan|mata\s*pelajaran)/i', $candidate)[0];
                $detectedKelasName = trim($candidate);
            }

            // Cari baris header tabel presensi: mengandung NAMA atau NISN
            $cleanRow = array_map(function($val) {
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $val)));
            }, $row);

            $hasNama = in_array('nama', $cleanRow) || in_array('namalengkap', $cleanRow) || in_array('namapesertadidik', $cleanRow);
            $hasId   = in_array('nisn', $cleanRow) || in_array('niss', $cleanRow) || in_array('nis', $cleanRow) || in_array('no', $cleanRow);

            if ($hasNama && $hasId) {
                $headerRowIdx = $idx;
                break;
            }
        }

        // Jika tidak ditemukan baris khusus, gunakan baris pertama (index 0) sebagai header
        if ($headerRowIdx === null) {
            $headerRowIdx = 0;
        }

        $header = array_map('trim', array_map('strtolower', $rawRows[$headerRowIdx]));
        $dataRows = array_slice($rawRows, $headerRowIdx + 1);

        $nisnIdx   = $this->findHeaderIndex($header, ['nisn', 'nis_nasional', 'no_nisn']);
        $namaIdx   = $this->findHeaderIndex($header, ['nama', 'nama_lengkap', 'nama siswa', 'peserta didik', 'namapesertadidik']);
        $nissIdx   = $this->findHeaderIndex($header, ['niss', 'nis', 'no_induk', 'id_siswa', 'noinduk']);
        $lpIdx     = $this->findHeaderIndex($header, ['lp', 'l/p', 'jenis_kelamin', 'jk', 'gender']);
        $kelasIdx  = $this->findHeaderIndex($header, ['nama_kelas', 'kelas', 'rombel']);
        $hpOrtuIdx = $this->findHeaderIndex($header, ['no_hp_ortu', 'no_hp', 'telepon_ortu', 'hp']);
        $alamatIdx = $this->findHeaderIndex($header, ['alamat', 'domisili']);

        if ($namaIdx === null && $nisnIdx === null && $nissIdx === null) {
            return back()->with('error', 'Format CSV tidak valid. Wajib menyertakan kolom: NISN atau NAMA.');
        }

        // Jika belum ada defaultKelas dari dropdown, gunakan kelas yang terdeteksi dari header file
        if (!$defaultKelas && $detectedKelasName) {
            $defaultKelas = $this->resolveKelas($detectedKelasName, $allKelas);
        }

        $imported = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        DB::beginTransaction();
        try {
            foreach ($dataRows as $lineNum => $row) {
                if (count($row) < 2) continue;

                $namaLengkap = $namaIdx !== null ? trim($row[$namaIdx] ?? '') : '';
                $nisn        = $nisnIdx !== null ? trim($row[$nisnIdx] ?? '') : '';
                $niss        = $nissIdx !== null ? trim($row[$nissIdx] ?? '') : '';
                $lpVal       = $lpIdx !== null ? strtoupper(trim($row[$lpIdx] ?? 'L')) : 'L';
                $jk          = in_array($lpVal, ['L', 'P']) ? $lpVal : (str_starts_with($lpVal, 'P') ? 'P' : 'L');
                $rowKelas    = $kelasIdx !== null ? trim($row[$kelasIdx] ?? '') : '';
                $noHpOrtu    = $hpOrtuIdx !== null ? trim($row[$hpOrtuIdx] ?? '') : null;
                $alamat      = $alamatIdx !== null ? trim($row[$alamatIdx] ?? '') : null;

                // Abaikan jika nama kosong atau baris catatan / footer cetak
                if (empty($namaLengkap) && empty($nisn)) continue;
                if (str_contains(strtolower($namaLengkap), 'keterangan') || str_contains(strtolower($namaLengkap), 'wali kelas') || str_contains(strtolower($namaLengkap), 'catatan')) continue;

                // Tentukan NIS: jika NISS terisi gunakan NISS, jika kosong gunakan NISN
                $nis = !empty($niss) ? $niss : (!empty($nisn) ? $nisn : null);
                if (empty($nis)) {
                    $nis = 'SISWA' . str_pad((string)($imported + $updated + 1), 6, '0', STR_PAD_LEFT);
                }

                // Tentukan target kelas: dari kolom baris, atau default dari dropdown/header
                $targetKelas = $defaultKelas;
                if (!empty($rowKelas)) {
                    $targetKelas = $this->resolveKelas($rowKelas, $allKelas);
                }

                if (!$targetKelas) {
                    $skipped++;
                    $errors[] = "Baris " . ($lineNum + $headerRowIdx + 2) . ": Kelas belum ditentukan (pilih kelas target pada modal atau sertakan kolom kelas).";
                    continue;
                }

                // Cari siswa berdasarkan NISN atau NIS
                $siswa = null;
                if (!empty($nisn)) {
                    $siswa = Siswa::where('nisn', $nisn)->first();
                }
                if (!$siswa && !empty($nis)) {
                    $siswa = Siswa::where('nis', $nis)->first();
                }

                if ($siswa) {
                    $siswa->update([
                        'nis'           => $nis ?: $siswa->nis,
                        'nisn'          => !empty($nisn) ? $nisn : $siswa->nisn,
                        'niss'          => !empty($niss) ? $niss : $siswa->niss,
                        'nama_lengkap'  => $namaLengkap ?: $siswa->nama_lengkap,
                        'jenis_kelamin' => $jk,
                        'id_kelas'      => $targetKelas->id_kelas,
                        'no_hp_ortu'    => $noHpOrtu ?: $siswa->no_hp_ortu,
                        'alamat'        => $alamat ?: $siswa->alamat,
                        'status_aktif'  => true,
                    ]);
                    $updated++;
                } else {
                    Siswa::create([
                        'nis'           => $nis,
                        'nisn'          => !empty($nisn) ? $nisn : null,
                        'niss'          => !empty($niss) ? $niss : null,
                        'nama_lengkap'  => $namaLengkap,
                        'jenis_kelamin' => $jk,
                        'id_kelas'      => $targetKelas->id_kelas,
                        'no_hp_ortu'    => $noHpOrtu,
                        'alamat'        => $alamat,
                        'status_aktif'  => true,
                    ]);
                    $imported++;
                }
            }

            DB::commit();

            $msg = "Impor data siswa berhasil! {$imported} data siswa baru ditambahkan, {$updated} diperbarui.";
            if ($skipped > 0) {
                $msg .= " ({$skipped} baris dilewati).";
            }
            if (!empty($errors)) {
                $msg .= " Catatan: " . implode('; ', array_slice($errors, 0, 2));
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses impor file CSV: ' . $e->getMessage());
        }
    }

    /**
     * Import Mata Pelajaran dari CSV
     */
    public function importMapel(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|max:5120',
        ]);

        $file = $request->file('file_csv');
        $rows = $this->parseCsv($file);

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau tidak memiliki data yang valid.');
        }

        $header = array_map('trim', array_map('strtolower', array_shift($rows)));
        $kodeIdx     = $this->findHeaderIndex($header, ['kode_mapel', 'kode', 'id_mapel']);
        $namaIdx     = $this->findHeaderIndex($header, ['nama_mapel', 'nama', 'mata_pelajaran']);
        $kelompokIdx = $this->findHeaderIndex($header, ['kelompok', 'kategori', 'jenis']);

        if ($kodeIdx === null || $namaIdx === null) {
            return back()->with('error', 'Format CSV tidak valid. Wajib menyertakan kolom: kode_mapel dan nama_mapel.');
        }

        $imported = 0;
        $updated  = 0;
        $skipped  = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if (count($row) < 2) continue;

                $kodeMapel = strtoupper(trim($row[$kodeIdx] ?? ''));
                $namaMapel = trim($row[$namaIdx] ?? '');
                $kelompokRaw = !empty($kelompokIdx) ? trim($row[$kelompokIdx] ?? '') : 'Normatif';

                if (empty($kodeMapel) || empty($namaMapel)) {
                    $skipped++;
                    continue;
                }

                // Normalisasi Kelompok
                $kelompok = 'Normatif';
                $lowKel = strtolower($kelompokRaw);
                if (str_contains($lowKel, 'adaptif')) $kelompok = 'Adaptif';
                elseif (str_contains($lowKel, 'produktif')) $kelompok = 'Produktif';
                elseif (str_contains($lowKel, 'muatan') || str_contains($lowKel, 'lokal') || str_contains($lowKel, 'mulok')) $kelompok = 'Muatan_Lokal';

                $mapel = Mapel::where('kode_mapel', $kodeMapel)->first();
                if ($mapel) {
                    $mapel->update([
                        'nama_mapel' => $namaMapel,
                        'kelompok'   => $kelompok,
                    ]);
                    $updated++;
                } else {
                    Mapel::create([
                        'kode_mapel' => $kodeMapel,
                        'nama_mapel' => $namaMapel,
                        'kelompok'   => $kelompok,
                    ]);
                    $imported++;
                }
            }

            DB::commit();

            $msg = "Impor CSV Mata Pelajaran berhasil! {$imported} mapel baru ditambahkan, {$updated} diperbarui.";
            if ($skipped > 0) {
                $msg .= " ({$skipped} baris dilewati).";
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengimpor mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Import Jadwal Pelajaran (Mapel beserta Jam KBM per Kelas) dari CSV
     */
    public function importJadwal(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|max:5120',
        ]);

        $file = $request->file('file_csv');
        $rows = $this->parseCsv($file);

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau tidak memiliki data yang valid.');
        }

        $header = array_map('trim', array_map('strtolower', array_shift($rows)));
        $kelasIdx   = $this->findHeaderIndex($header, ['nama_kelas', 'kelas', 'rombel']);
        $mapelIdx   = $this->findHeaderIndex($header, ['mapel', 'kode_mapel', 'mata_pelajaran', 'nama_mapel']);
        $guruIdx    = $this->findHeaderIndex($header, ['guru', 'nip', 'nama_guru', 'pengajar']);
        $hariIdx    = $this->findHeaderIndex($header, ['hari']);
        $jamDariIdx = $this->findHeaderIndex($header, ['jam_dari', 'jam_ke', 'jam_mulai_ke', 'jam']);
        $jamSmpIdx  = $this->findHeaderIndex($header, ['jam_sampai', 'jam_akhir', 'jam_selesai_ke']);
        $taIdx      = $this->findHeaderIndex($header, ['tahun_ajaran', 'ta']);
        $semIdx     = $this->findHeaderIndex($header, ['semester']);

        if ($kelasIdx === null || $mapelIdx === null || $guruIdx === null || $hariIdx === null || $jamDariIdx === null) {
            return back()->with('error', 'Format CSV tidak valid. Wajib menyertakan kolom: nama_kelas, mapel, guru, hari, jam_dari.');
        }

        // Cache Master Data
        $allKelas = Kelas::all();
        $allMapel = Mapel::all();
        $allGuru  = Guru::all();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $importedSlots = 0;
        $skippedRows   = 0;
        $bentrokErrors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $lineNum => $row) {
                if (count($row) < 4) continue;

                $namaKelas = trim($row[$kelasIdx] ?? '');
                $mapelVal  = trim($row[$mapelIdx] ?? '');
                $guruVal   = trim($row[$guruIdx] ?? '');
                $hariVal   = ucfirst(strtolower(trim($row[$hariIdx] ?? '')));
                $jamDari   = (int)trim($row[$jamDariIdx] ?? 0);
                $jamSampai = !empty($jamSmpIdx) && !empty(trim($row[$jamSmpIdx] ?? '')) 
                    ? (int)trim($row[$jamSmpIdx]) 
                    : $jamDari;
                $taNama    = !empty($taIdx) && !empty(trim($row[$taIdx] ?? '')) ? trim($row[$taIdx]) : ($tahunAjaranAktif->nama ?? date('Y').'/'.(date('Y')+1));
                $semester  = !empty($semIdx) && !empty(trim($row[$semIdx] ?? '')) ? ucfirst(strtolower(trim($row[$semIdx]))) : ($tahunAjaranAktif->semester ?? 'Ganjil');

                // Validasi hari (hanya Senin s/d Jumat)
                if (!in_array($hariVal, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'])) {
                    $skippedRows++;
                    $bentrokErrors[] = "Baris " . ($lineNum + 2) . ": Hari '{$hariVal}' tidak valid (hanya Senin s/d Jumat).";
                    continue;
                }

                // Validasi jam range
                if ($jamDari < 1 || $jamDari > 10 || $jamSampai < $jamDari || ($jamSampai - $jamDari + 1) > 4) {
                    $skippedRows++;
                    $bentrokErrors[] = "Baris " . ($lineNum + 2) . ": Rentang jam {$jamDari}-{$jamSampai} tidak valid (maks 4 jam).";
                    continue;
                }

                // 1. Resolve Kelas
                $kelas = $this->resolveKelas($namaKelas, $allKelas);
                if (!$kelas) {
                    $skippedRows++;
                    $bentrokErrors[] = "Baris " . ($lineNum + 2) . ": Kelas '{$namaKelas}' tidak ditemukan.";
                    continue;
                }

                // 2. Resolve Mapel (by kode or name)
                $mapel = $allMapel->first(function ($m) use ($mapelVal) {
                    return strcasecmp($m->kode_mapel, $mapelVal) === 0 || strcasecmp($m->nama_mapel, $mapelVal) === 0;
                });
                if (!$mapel) {
                    $skippedRows++;
                    $bentrokErrors[] = "Baris " . ($lineNum + 2) . ": Mata Pelajaran '{$mapelVal}' tidak ditemukan.";
                    continue;
                }

                // 3. Resolve Guru (by NIP or Name)
                $guru = $allGuru->first(function ($g) use ($guruVal) {
                    return $g->nip === $guruVal || strcasecmp($g->nama_lengkap, $guruVal) === 0 || str_contains(strtolower($g->nama_lengkap), strtolower($guruVal));
                });
                if (!$guru) {
                    $skippedRows++;
                    $bentrokErrors[] = "Baris " . ($lineNum + 2) . ": Guru '{$guruVal}' tidak ditemukan.";
                    continue;
                }

                // 4. Resolve Tahun Ajaran
                $ta = TahunAjaran::where('nama', $taNama)->where('semester', $semester)->first();
                if (!$ta) {
                    $ta = $tahunAjaranAktif ?: TahunAjaran::create([
                        'nama'            => $taNama,
                        'semester'        => $semester,
                        'tanggal_mulai'   => date('Y-07-15'),
                        'tanggal_selesai' => date('Y-12-31'),
                        'is_aktif'        => true,
                    ]);
                }

                // 5. Buat slot jadwal per jam
                $slots = range($jamDari, $jamSampai);
                foreach ($slots as $slotJam) {
                    // Cek bentrok kelas
                    $kelasBentrok = JadwalPelajaran::where('id_tahun_ajaran', $ta->id)
                        ->where('hari', $hariVal)
                        ->where('jam_ke', $slotJam)
                        ->where('id_kelas', $kelas->id_kelas)
                        ->first();

                    if ($kelasBentrok) {
                        $bentrokErrors[] = "Bentrok Kelas {$kelas->nama_kelas} di {$hariVal} jam ke-{$slotJam} (sudah terisi).";
                        continue;
                    }

                    // Cek bentrok guru
                    $guruBentrok = JadwalPelajaran::where('id_tahun_ajaran', $ta->id)
                        ->where('hari', $hariVal)
                        ->where('jam_ke', $slotJam)
                        ->where('id_guru', $guru->id_guru)
                        ->first();

                    if ($guruBentrok) {
                        $bentrokErrors[] = "Bentrok Guru {$guru->nama_lengkap} di {$hariVal} jam ke-{$slotJam} (mengajar di kelas lain).";
                        continue;
                    }

                    // Slot times standar
                    $timeSlots = JadwalPelajaranController::getDefaultTimeSlot($hariVal, $slotJam);

                    JadwalPelajaran::create([
                        'id_tahun_ajaran' => $ta->id,
                        'hari'            => $hariVal,
                        'jam_ke'          => $slotJam,
                        'jam_mulai'       => $timeSlots['jam_mulai'],
                        'jam_selesai'     => $timeSlots['jam_selesai'],
                        'id_mapel'        => $mapel->id_mapel,
                        'id_guru'         => $guru->id_guru,
                        'id_kelas'        => $kelas->id_kelas,
                    ]);

                    $importedSlots++;
                }
            }

            DB::commit();

            $msg = "Impor Jadwal Pelajaran selesai! {$importedSlots} slot jam KBM berhasil dibuat.";
            if (!empty($bentrokErrors)) {
                $msg .= " Catatan bentrok/lewati: " . implode('; ', array_slice($bentrokErrors, 0, 3));
                if (count($bentrokErrors) > 3) {
                    $msg .= " (dan " . (count($bentrokErrors) - 3) . " catatan lainnya).";
                }
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses impor jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Helper parsing CSV fleksibel (mendukung koma, titik-koma, dan tab separator)
     */
    private function parseCsv($file): array
    {
        $content = file_get_contents($file->getRealPath());

        // Hapus UTF-8 BOM jika ada
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) return [];

        // Deteksi delimiter dari baris pertama (koma, titik-koma, atau tab)
        $firstLine = $lines[0];
        $delimiter = ',';
        $semicolonCount = substr_count($firstLine, ';');
        $commaCount     = substr_count($firstLine, ',');
        $tabCount       = substr_count($firstLine, "\t");

        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            $delimiter = ';';
        } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            $delimiter = "\t";
        }

        $parsed = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $row = str_getcsv($line, $delimiter);
            if (!empty($row)) {
                $parsed[] = $row;
            }
        }

        return $parsed;
    }

    /**
     * Helper mencari indeks kolom header
     */
    private function findHeaderIndex(array $header, array $candidates): ?int
    {
        foreach ($candidates as $cand) {
            foreach ($header as $idx => $col) {
                $cleanCol = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $col)));
                $cleanCand = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $cand)));
                if ($cleanCol === $cleanCand) {
                    return $idx;
                }
            }
        }
        return null;
    }

    /**
     * Helper mencocokkan nama kelas secara fleksibel (misal "X AKL 1" atau "Kelas X AKL 1")
    /**
     * Helper mencocokkan nama kelas secara fleksibel (misal "X AKL 1", "Kelas X AKL 1", atau "X TKI 1")
     * Otomatis membuat kelas baru jika belum terdaftar di database agar proses impor tidak terhenti.
     */
    private function resolveKelas(string $nama, &$allKelas = null): ?Kelas
    {
        $nama = trim($nama);
        if (empty($nama)) return null;

        if ($allKelas === null) {
            $allKelas = Kelas::all();
        }

        $cleanSearch = strtolower(trim(str_replace(['kelas', 'kel', 'rombel'], '', $nama)));

        $found = $allKelas->first(function ($k) use ($cleanSearch, $nama) {
            $cleanK = strtolower(trim(str_replace(['kelas', 'kel', 'rombel'], '', $k->nama_kelas)));
            return $cleanK === $cleanSearch || strcasecmp($k->nama_kelas, $nama) === 0;
        });

        if ($found) {
            return $found;
        }

        // Tentukan tingkat (X, XI, XII)
        $tingkat = 'X';
        $upper = strtoupper($nama);
        if (preg_match('/\b(XII|12)\b/', $upper) || str_starts_with($upper, 'XII')) {
            $tingkat = 'XII';
        } elseif (preg_match('/\b(XI|11)\b/', $upper) || str_starts_with($upper, 'XI')) {
            $tingkat = 'XI';
        } elseif (preg_match('/\b(X|10)\b/', $upper) || str_starts_with($upper, 'X')) {
            $tingkat = 'X';
        }

        // Tentukan jurusan (misal X TKI 1 -> TKI)
        $jurusan = 'Umum';
        $parts = preg_split('/\s+/', $nama);
        if (count($parts) >= 2) {
            $jurusan = $parts[1];
        }

        $newKelas = Kelas::create([
            'nama_kelas' => $nama,
            'tingkat'    => $tingkat,
            'jurusan'    => $jurusan,
        ]);

        if (is_object($allKelas) && method_exists($allKelas, 'push')) {
            $allKelas->push($newKelas);
        }

        return $newKelas;
    }

    /**
     * Helper sinkronisasi tabel peran guru
     */
    private function syncGuruRole(User $user, string $role, string $nip, string $nama, string $jk, ?string $noHp): void
    {
        switch ($role) {
            case 'wali_kelas':
                WaliKelas::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip'          => $nip,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin'=> $jk,
                        'no_hp'        => $noHp,
                        'status_aktif' => true,
                    ]
                );
                break;
            case 'guru_piket':
                GuruPiket::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip'          => $nip,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin'=> $jk,
                        'no_hp'        => $noHp,
                        'hari_piket'   => 'Senin',
                        'status_aktif' => true,
                    ]
                );
                break;
            case 'guru_mapel':
                GuruMapel::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip'          => $nip,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin'=> $jk,
                        'no_hp'        => $noHp,
                        'status_aktif' => true,
                    ]
                );
                break;
        }
    }
}
