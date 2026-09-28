<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\JurnalMengajar;
use App\Models\PresensiSiswa;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\IzinGuru;
use App\Models\IzinSiswa;
use App\Models\DispensasiSiswa;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\StatusHarianKbm;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GuruMapelDashboardController extends Controller
{
    /**
     * Helper to resolve active Guru instance for current authenticated user
     */
    private function resolveGuru()
    {
        $user = Auth::user();
        if ($user->guru) {
            return $user->guru;
        }

        $guru = Guru::where('user_id', $user->id)->first();
        if ($guru) {
            return $guru;
        }

        // If user is registered via wali_kelas, try finding guru by NIP
        if ($user->waliKelas && $user->waliKelas->nip) {
            $guru = Guru::where('nip', $user->waliKelas->nip)->first();
            if ($guru) {
                return $guru;
            }
        }

        // Fallback for admin or unlinked account
        return Guru::where('status_aktif', true)->first() ?? Guru::first();
    }

    /**
     * Helper to group consecutive schedules into continuous teaching blocks.
     * Only merges schedules if:
     * - Same day (hari)
     * - Same class (id_kelas)
     * - Same subject (id_mapel)
     * - Consecutive jam_ke ($next->jam_ke == $last->jam_ke + 1)
     */
    private function groupConsecutiveSchedules($schedules, ?string $forDate = null)
    {
        if ($schedules->isEmpty()) {
            return collect();
        }

        $sorted = $schedules->sortBy('jam_ke')->values();
        $groups = collect();
        $currentGroup = null;

        foreach ($sorted as $item) {
            if ($currentGroup === null) {
                $currentGroup = [
                    'id_jadwal'   => $item->id_jadwal,
                    'jadwal_ids'  => [$item->id_jadwal],
                    'id_guru'     => $item->id_guru,
                    'id_kelas'    => $item->id_kelas,
                    'id_mapel'    => $item->id_mapel,
                    'hari'        => $item->hari,
                    'jam_ke_list' => [(int)$item->jam_ke],
                    'jam_mulai'   => $item->jam_mulai,
                    'jam_selesai' => $item->jam_selesai,
                    'kelas'       => $item->kelas,
                    'mapel'       => $item->mapel,
                    'items'       => collect([$item]),
                ];
                continue;
            }

            $lastJamKe = end($currentGroup['jam_ke_list']);
            $isSameClass = $currentGroup['id_kelas'] == $item->id_kelas;
            $isSameMapel = $currentGroup['id_mapel'] == $item->id_mapel;
            $isSameHari  = $currentGroup['hari'] == $item->hari;
            $isConsecutiveJam = ((int)$item->jam_ke == $lastJamKe + 1);

            if ($isSameClass && $isSameMapel && $isSameHari && $isConsecutiveJam) {
                $currentGroup['jadwal_ids'][] = $item->id_jadwal;
                $currentGroup['jam_ke_list'][] = (int)$item->jam_ke;
                $currentGroup['jam_selesai'] = $item->jam_selesai;
                $currentGroup['items']->push($item);
            } else {
                $groups->push($this->formatGroupedSchedule($currentGroup, $forDate));
                $currentGroup = [
                    'id_jadwal'   => $item->id_jadwal,
                    'jadwal_ids'  => [$item->id_jadwal],
                    'id_guru'     => $item->id_guru,
                    'id_kelas'    => $item->id_kelas,
                    'id_mapel'    => $item->id_mapel,
                    'hari'        => $item->hari,
                    'jam_ke_list' => [(int)$item->jam_ke],
                    'jam_mulai'   => $item->jam_mulai,
                    'jam_selesai' => $item->jam_selesai,
                    'kelas'       => $item->kelas,
                    'mapel'       => $item->mapel,
                    'items'       => collect([$item]),
                ];
            }
        }

        if ($currentGroup !== null) {
            $groups->push($this->formatGroupedSchedule($currentGroup, $forDate));
        }

        return $groups;
    }

    private function formatGroupedSchedule(array $group, ?string $forDate = null)
    {
        $count = count($group['jam_ke_list']);
        $firstJam = reset($group['jam_ke_list']);
        $lastJam = end($group['jam_ke_list']);

        $jamLabel = ($count > 1) ? "{$firstJam} - {$lastJam}" : (string)$firstJam;
        $jamKeRaw = ($count > 1) ? "{$firstJam}-{$lastJam}" : (string)$firstJam;

        $jamMulai = $group['jam_mulai'];
        $jamSelesai = $group['jam_selesai'];

        // Jika tanggal disertakan, sesuaikan waktu slot dengan kondisi KBM hari tersebut (Upacara/Pembiasaan)
        if ($forDate) {
            $slotStart = StatusHarianKbm::getTimeSlot($group['hari'], $firstJam, $forDate);
            $slotEnd   = StatusHarianKbm::getTimeSlot($group['hari'], $lastJam, $forDate);
            $jamMulai   = $slotStart['jam_mulai'];
            $jamSelesai = $slotEnd['jam_selesai'];
        }

        $obj = new \stdClass();
        $obj->id_jadwal = $group['id_jadwal'];
        $obj->jadwal_ids = $group['jadwal_ids'];
        $obj->id_guru = $group['id_guru'];
        $obj->id_kelas = $group['id_kelas'];
        $obj->id_mapel = $group['id_mapel'];
        $obj->hari = $group['hari'];
        $obj->jam_ke = $jamLabel;
        $obj->jam_ke_display = $jamLabel;
        $obj->jam_ke_raw = $jamKeRaw;
        $obj->jam_ke_list = $group['jam_ke_list'];
        $obj->jam_mulai = $jamMulai;
        $obj->jam_selesai = $jamSelesai;
        $obj->kelas = $group['kelas'];
        $obj->mapel = $group['mapel'];
        $obj->items = $group['items'];
        $obj->total_jp = $count;
        $obj->is_filled = false;
        $obj->jurnal = null;

        return $obj;
    }

    /**
     * Dashboard Utama Guru Mata Pelajaran
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $guru = $this->resolveGuru();

        if (!$guru) {
            return view('guru_mapel.dashboard.index', [
                'guru' => null,
                'user' => $user,
                'todayFormatted' => Carbon::today()->translatedFormat('l, j F Y'),
                'jadwalHariIni' => collect(),
                'riwayatJurnal' => collect(),
                'metrics' => [
                    'jadwal_hari_ini' => 0,
                    'jurnal_terisi_hari_ini' => 0,
                    'total_jam_mingguan' => 0,
                    'tingkat_presensi' => 0,
                    'total_jurnal_semester' => 0,
                ],
            ]);
        }

        $today = Carbon::today()->format('Y-m-d');
        $todayFormatted = Carbon::today()->translatedFormat('l, j F Y');
        $hariIni = Carbon::today()->translatedFormat('l');

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // 1. Jadwal Hari Ini (Raw)
        $rawJadwalHariIni = JadwalPelajaran::with(['kelas.jurusanRelation', 'mapel'])
            ->where('id_guru', $guru->id_guru)
            ->where('hari', $hariIni)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->orderBy('jam_ke')
            ->get();

        // Kelompokkan jadwal jam berurutan pada kelas & mapel yang sama (jam blok) dengan slot waktu hari ini
        $groupedJadwalHariIni = $this->groupConsecutiveSchedules($rawJadwalHariIni, $today);

        // 2. Jurnal Hari Ini
        $jurnalHariIni = JurnalMengajar::where('id_guru', $guru->id_guru)
            ->where('tanggal', $today)
            ->get();

        // Attach status to Jadwal Hari Ini
        $jadwalCards = $groupedJadwalHariIni->map(function ($jd) use ($jurnalHariIni) {
            $matchingJurnal = $jurnalHariIni->first(function ($j) use ($jd) {
                // Cocokkan berdasarkan ID Jadwal (jika ada pada salah satu ID dalam blok jadwal)
                if ($j->id_jadwal && in_array($j->id_jadwal, $jd->jadwal_ids)) {
                    return true;
                }

                // Cocokkan berdasarkan kesamaan Kelas dan Mapel
                if ($j->id_kelas == $jd->id_kelas && $j->id_mapel == $jd->id_mapel) {
                    // Cek kesamaan string jam_ke
                    if ($j->jam_ke == $jd->jam_ke || $j->jam_ke == $jd->jam_ke_raw) {
                        return true;
                    }

                    // Cek apakah angka jam_ke jurnal berada dalam rentang jam_ke sesi blok ini
                    preg_match_all('/\d+/', (string)$j->jam_ke, $matches);
                    $jurnalJams = array_map('intval', $matches[0] ?? []);
                    if (!empty($jurnalJams) && !empty(array_intersect($jurnalJams, $jd->jam_ke_list))) {
                        return true;
                    }
                }

                return false;
            });

            $jd->is_filled = !is_null($matchingJurnal);
            $jd->jurnal = $matchingJurnal;
            return $jd;
        });

        // 3. KPI Metrics
        // Total sesi mengajar yang harus diisi hari ini (berdasarkan sesi blok pembelajaran)
        $totalJadwalHariIni = $jadwalCards->count();
        // Total sesi yang jurnalnya sudah terisi hari ini
        $totalJurnalHariIni = $jadwalCards->where('is_filled', true)->count();

        // Total jam mengajar (JP beban mengajar mingguan)
        $totalJamMingguan = JadwalPelajaran::where('id_guru', $guru->id_guru)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->count();

        $totalJurnalSemester = JurnalMengajar::where('id_guru', $guru->id_guru)->count();

        // Presensi rate across teacher's classes
        $presensiStats = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('id_guru', $guru->id_guru))
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalPresensi = array_sum($presensiStats);
        $totalHadir = $presensiStats['Hadir'] ?? 0;
        $tingkatPresensi = $totalPresensi > 0 ? round(($totalHadir / $totalPresensi) * 100, 1) : 100;

        // 4. Riwayat Jurnal Terakhir
        $riwayatJurnal = JurnalMengajar::with(['kelas', 'mapel', 'presensiSiswa'])
            ->where('id_guru', $guru->id_guru)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->take(6)
            ->get();

        // 5. Cek Notifikasi Status Izin Terakhir (Persetujuan / Penolakan Lanjut KBM)
        $latestIzinNotice = IzinGuru::with(['piketApprover', 'wakaApprover', 'kepsekApprover'])
            ->where('id_guru', $guru->id_guru)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->whereIn('status', ['Disetujui', 'Ditolak'])
            ->latest('updated_at')
            ->first();

        // Status KBM Hari Ini (Upacara / Pembiasaan Ditiadakan)
        $statusKbmHariIni = StatusHarianKbm::getEffectiveStatus($today);
        $isMaju = StatusHarianKbm::isMaju($today);

        return view('guru_mapel.dashboard.index', [
            'guru' => $guru,
            'user' => $user,
            'today' => $today,
            'todayFormatted' => $todayFormatted,
            'hariIni' => $hariIni,
            'jadwalCards' => $jadwalCards,
            'riwayatJurnal' => $riwayatJurnal,
            'latestIzinNotice' => $latestIzinNotice,
            'statusKbmHariIni' => $statusKbmHariIni,
            'isMaju' => $isMaju,
            'metrics' => [
                'jadwal_hari_ini' => $totalJadwalHariIni,
                'jurnal_terisi_hari_ini' => $totalJurnalHariIni,
                'total_jam_mingguan' => $totalJamMingguan,
                'tingkat_presensi' => $tingkatPresensi,
                'total_jurnal_semester' => $totalJurnalSemester,
                'presensi_stats' => $presensiStats,
            ],
        ]);
    }

    /**
     * Jadwal Mengajar Mingguan Guru
     */
    public function jadwal(Request $request)
    {
        Carbon::setLocale('id');
        $guru = $this->resolveGuru();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $rawAllJadwal = JadwalPelajaran::with(['kelas.jurusanRelation', 'kelas.waliKelas', 'mapel'])
            ->where('id_guru', $guru->id_guru ?? 0)
            ->when($tahunAjaranAktif, fn($q) => $q->where('id_tahun_ajaran', $tahunAjaranAktif->id))
            ->orderBy('jam_ke')
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $totalJamMingguan = $rawAllJadwal->count();
        $totalKelasDiajar = $rawAllJadwal->pluck('id_kelas')->unique()->count();
        $totalMapelDiajar = $rawAllJadwal->pluck('id_mapel')->unique()->count();

        // Kelompokkan jadwal berturut-turut per hari
        $allJadwal = collect();
        foreach ($hariList as $h) {
            $daySchedules = $rawAllJadwal->where('hari', $h);
            $allJadwal->put($h, $this->groupConsecutiveSchedules($daySchedules));
        }

        return view('guru_mapel.jadwal.index', [
            'guru' => $guru,
            'allJadwal' => $allJadwal,
            'hariList' => $hariList,
            'totalSesiMingguan' => $totalJamMingguan,
            'totalKelasDiajar' => $totalKelasDiajar,
            'totalMapelDiajar' => $totalMapelDiajar,
            'tahunAjaranAktif' => $tahunAjaranAktif,
        ]);
    }

    /**
     * Form Isi Jurnal Mengajar & Presensi Siswa
     */
    public function createJurnal(Request $request)
    {
        Carbon::setLocale('id');
        $guru = $this->resolveGuru();
        $today = Carbon::today()->format('Y-m-d');
        $hariIni = Carbon::today()->translatedFormat('l');

        $idJadwal = $request->input('id_jadwal');
        $idKelas = $request->input('id_kelas');
        $idMapel = $request->input('id_mapel');
        $tanggal = $today; // Tanggal mengajar terkunci otomatis ke hari ini

        $selectedJadwal = null;
        $jamKeSuggestion = null;
        $jamMulaiSuggestion = null;
        $jamSelesaiSuggestion = null;

        if ($idJadwal) {
            $selectedJadwal = JadwalPelajaran::with(['kelas', 'mapel'])->find($idJadwal);
            if ($selectedJadwal) {
                $idKelas = $selectedJadwal->id_kelas;
                $idMapel = $selectedJadwal->id_mapel;

                // Cari blok jam berurutan yang memuat jadwal terpilih
                $blockSchedules = JadwalPelajaran::where('id_guru', $selectedJadwal->id_guru)
                    ->where('hari', $selectedJadwal->hari)
                    ->where('id_kelas', $selectedJadwal->id_kelas)
                    ->where('id_mapel', $selectedJadwal->id_mapel)
                    ->orderBy('jam_ke')
                    ->get();

                $grouped = $this->groupConsecutiveSchedules($blockSchedules, $tanggal);
                $activeGroup = $grouped->first(function ($g) use ($idJadwal) {
                    return in_array($idJadwal, $g->jadwal_ids);
                });

                if ($activeGroup) {
                    $jamKeSuggestion = $activeGroup->jam_ke;
                    $jamMulaiSuggestion = $activeGroup->jam_mulai;
                    $jamSelesaiSuggestion = $activeGroup->jam_selesai;
                } else {
                    $slot = StatusHarianKbm::getTimeSlot($selectedJadwal->hari, $selectedJadwal->jam_ke, $tanggal);
                    $jamMulaiSuggestion = $slot['jam_mulai'];
                    $jamSelesaiSuggestion = $slot['jam_selesai'];
                }
            }
        }

        // List kelas & mapel diajar guru ini
        $kelasIds = JadwalPelajaran::where('id_guru', $guru->id_guru ?? 0)->pluck('id_kelas')->unique();
        $mapelIds = JadwalPelajaran::where('id_guru', $guru->id_guru ?? 0)->pluck('id_mapel')->unique();

        $kelasList = Kelas::whereIn('id_kelas', $kelasIds)->orderBy('tingkat')->orderBy('nama_kelas')->get();
        if ($kelasList->isEmpty()) {
            $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        }

        $mapelList = Mapel::whereIn('id_mapel', $mapelIds)->orderBy('nama_mapel')->get();
        if ($mapelList->isEmpty()) {
            $mapelList = Mapel::orderBy('nama_mapel')->get();
        }

        // Auto select first class if none selected
        if (!$idKelas && $kelasList->isNotEmpty()) {
            $idKelas = $kelasList->first()->id_kelas;
        }
        if (!$idMapel && $mapelList->isNotEmpty()) {
            $idMapel = $mapelList->first()->id_mapel;
        }

        // Siswa di kelas terpilih
        $siswaList = collect();
        $piketAbsenceCount = 0;
        $piketDetails = [
            'sakit' => 0,
            'izin' => 0,
            'dispensasi' => 0,
        ];

        if ($idKelas) {
            $siswaList = Siswa::where('id_kelas', $idKelas)
                ->where('status_aktif', true)
                ->orderBy('nama_lengkap')
                ->get();

            $siswaIds = $siswaList->pluck('id_siswa')->toArray();

            if (!empty($siswaIds)) {
                // Ambil perizinan siswa dari Guru Piket yang aktif pada tanggal ini
                $izinSiswaMap = IzinSiswa::whereIn('id_siswa', $siswaIds)
                    ->whereDate('tanggal_mulai', '<=', $tanggal)
                    ->whereDate('tanggal_selesai', '>=', $tanggal)
                    ->where('status', '!=', 'Ditolak')
                    ->get()
                    ->keyBy('id_siswa');

                // Ambil dispensasi siswa yang disetujui pada tanggal ini
                $dispensasiSiswaMap = DispensasiSiswa::whereIn('id_siswa', $siswaIds)
                    ->whereDate('tanggal', $tanggal)
                    ->whereIn('status', ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai'])
                    ->get()
                    ->keyBy('id_siswa');

                // Pasangkan ke data setiap siswa untuk otomatisasi presensi di jurnal
                $siswaList->transform(function ($s) use ($izinSiswaMap, $dispensasiSiswaMap, &$piketAbsenceCount, &$piketDetails) {
                    $s->piket_status = null;
                    $s->piket_keterangan = null;

                    if ($izin = $izinSiswaMap->get($s->id_siswa)) {
                        $s->piket_status = $izin->jenis_izin; // 'Sakit', 'Izin', 'Dispensasi'
                        $s->piket_keterangan = $izin->alasan;
                        $piketAbsenceCount++;
                        if ($izin->jenis_izin === 'Sakit') {
                            $piketDetails['sakit']++;
                        } elseif ($izin->jenis_izin === 'Izin') {
                            $piketDetails['izin']++;
                        } else {
                            $piketDetails['dispensasi']++;
                        }
                    } elseif ($disp = $dispensasiSiswaMap->get($s->id_siswa)) {
                        $s->piket_status = 'Dispensasi';
                        $s->piket_keterangan = $disp->alasan;
                        $piketAbsenceCount++;
                        $piketDetails['dispensasi']++;
                    }

                    return $s;
                });
            }
        }

        // Jadwal Hari Ini options (dikelompokkan per sesi blok)
        $rawJadwalHariIniOptions = JadwalPelajaran::with(['kelas', 'mapel'])
            ->where('id_guru', $guru->id_guru ?? 0)
            ->where('hari', $hariIni)
            ->orderBy('jam_ke')
            ->get();

        $jadwalHariIniOptions = $this->groupConsecutiveSchedules($rawJadwalHariIniOptions, $tanggal);

        // Cek apakah guru memiliki izin aktif hari ini
        $activeIzinHariIni = IzinGuru::where('id_guru', $guru->id_guru ?? 0)
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today)
            ->where('status', '!=', 'Ditolak')
            ->first();

        // Status KBM Hari Ini (Upacara / Pembiasaan Ditiadakan)
        $statusKbmHariIni = StatusHarianKbm::getEffectiveStatus($tanggal);
        $isMaju = StatusHarianKbm::isMaju($tanggal);

        return view('guru_mapel.jurnal.create', [
            'guru' => $guru,
            'today' => $today,
            'tanggal' => $tanggal,
            'selectedJadwal' => $selectedJadwal,
            'jamKeSuggestion' => $jamKeSuggestion,
            'jamMulaiSuggestion' => $jamMulaiSuggestion,
            'jamSelesaiSuggestion' => $jamSelesaiSuggestion,
            'idKelas' => $idKelas,
            'idMapel' => $idMapel,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
            'siswaList' => $siswaList,
            'jadwalHariIniOptions' => $jadwalHariIniOptions,
            'activeIzinHariIni' => $activeIzinHariIni,
            'piketAbsenceCount' => $piketAbsenceCount,
            'piketDetails' => $piketDetails,
            'statusKbmHariIni' => $statusKbmHariIni,
            'isMaju' => $isMaju,
        ]);
    }

    /**
     * Simpan Jurnal Mengajar & Presensi Siswa
     */
    public function storeJurnal(Request $request)
    {
        $guru = $this->resolveGuru();

        $validated = $request->validate([
            'id_kelas'     => 'required|exists:kelas,id_kelas',
            'id_mapel'     => 'required|exists:mapel,id_mapel',
            'tanggal'      => 'nullable|date',
            'jam_ke'       => 'required|string|max:20',
            'jam_mulai'    => 'nullable',
            'jam_selesai'  => 'nullable',
            'materi'       => 'required|string',
            'status_guru'  => 'required|in:Hadir,Izin,Sakit,Dinas',
            'catatan'      => 'nullable|string',
            'id_jadwal'    => 'nullable|exists:jadwal_pelajaran,id_jadwal',
            'presensi'     => 'nullable|array',
            'keterangan'   => 'nullable|array',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $presensiData = $request->input('presensi', []);
        $keteranganData = $request->input('keterangan', []);

        $hadirCount = 0;
        $tidakHadirCount = 0;

        foreach ($presensiData as $status) {
            if ($status === 'Hadir') {
                $hadirCount++;
            } else {
                $tidakHadirCount++;
            }
        }

        DB::beginTransaction();
        try {
            $jurnal = JurnalMengajar::create([
                'id_jadwal'               => $validated['id_jadwal'] ?? null,
                'id_guru'                 => $guru->id_guru,
                'id_kelas'                => $validated['id_kelas'],
                'id_mapel'                => $validated['id_mapel'],
                'tanggal'                 => $today, // Wajib sesuai tanggal hari ini (server) untuk mencegah manipulasi
                'jam_ke'                  => $validated['jam_ke'],
                'jam_mulai'               => $validated['jam_mulai'],
                'jam_selesai'             => $validated['jam_selesai'],
                'materi'                  => $validated['materi'],
                'jumlah_siswa_hadir'      => $hadirCount,
                'jumlah_siswa_tidak_hadir'=> $tidakHadirCount,
                'status_guru'             => $validated['status_guru'],
                'catatan'                 => $validated['catatan'] ?? null,
            ]);

            // Save individual student attendance
            foreach ($presensiData as $idSiswa => $status) {
                PresensiSiswa::create([
                    'id_jurnal'   => $jurnal->id_jurnal,
                    'id_siswa'    => $idSiswa,
                    'status'      => $status,
                    'keterangan'  => $keteranganData[$idSiswa] ?? null,
                ]);
            }

            DB::commit();

            LogAktivitas::catat(
                'Input Jurnal',
                "Guru {$guru->nama_lengkap} menginput jurnal & presensi KBM kelas " . ($jurnal->kelas->nama_kelas ?? 'Kelas'),
                $jurnal,
                Auth::user()
            );

            return redirect()->route('guru-mapel.jurnal.riwayat')
                ->with('success', "Jurnal mengajar dan presensi {$hadirCount} siswa hadir berhasil disimpan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat Jurnal Mengajar
     */
    public function riwayatJurnal(Request $request)
    {
        Carbon::setLocale('id');
        $guru = $this->resolveGuru();

        $tglMulai = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');
        $kelasFilter = $request->input('id_kelas');
        $mapelFilter = $request->input('id_mapel');
        $search = $request->input('search');

        $query = JurnalMengajar::with(['kelas', 'mapel', 'presensiSiswa'])
            ->where('id_guru', $guru->id_guru ?? 0)
            ->when($tglMulai, fn($q) => $q->where('tanggal', '>=', $tglMulai))
            ->when($tglSelesai, fn($q) => $q->where('tanggal', '<=', $tglSelesai))
            ->when($kelasFilter, fn($q) => $q->where('id_kelas', $kelasFilter))
            ->when($mapelFilter, fn($q) => $q->where('id_mapel', $mapelFilter))
            ->when($search, fn($q) => $q->where('materi', 'like', "%{$search}%"))
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc');

        $jurnalList = $query->paginate(10)->withQueryString();

        $kelasIds = JadwalPelajaran::where('id_guru', $guru->id_guru ?? 0)->pluck('id_kelas')->unique();
        $kelasList = Kelas::whereIn('id_kelas', $kelasIds)->orderBy('nama_kelas')->get();
        if ($kelasList->isEmpty()) {
            $kelasList = Kelas::orderBy('nama_kelas')->get();
        }

        $mapelIds = JadwalPelajaran::where('id_guru', $guru->id_guru ?? 0)->pluck('id_mapel')->unique();
        $mapelList = Mapel::whereIn('id_mapel', $mapelIds)->orderBy('nama_mapel')->get();
        if ($mapelList->isEmpty()) {
            $mapelList = Mapel::orderBy('nama_mapel')->get();
        }

        // Summary counts
        $totalSesi = JurnalMengajar::where('id_guru', $guru->id_guru ?? 0)->count();
        $totalHadir = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('id_guru', $guru->id_guru ?? 0))->where('status', 'Hadir')->count();
        $totalSakit = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('id_guru', $guru->id_guru ?? 0))->where('status', 'Sakit')->count();
        $totalIzin  = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('id_guru', $guru->id_guru ?? 0))->where('status', 'Izin')->count();
        $totalAlpha = PresensiSiswa::whereHas('jurnal', fn($q) => $q->where('id_guru', $guru->id_guru ?? 0))->where('status', 'Alpha')->count();

        return view('guru_mapel.jurnal.riwayat', [
            'guru' => $guru,
            'jurnalList' => $jurnalList,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
            'tglMulai' => $tglMulai,
            'tglSelesai' => $tglSelesai,
            'kelasFilter' => $kelasFilter,
            'mapelFilter' => $mapelFilter,
            'search' => $search,
            'summary' => [
                'total_sesi' => $totalSesi,
                'hadir' => $totalHadir,
                'sakit' => $totalSakit,
                'izin' => $totalIzin,
                'alpha' => $totalAlpha,
            ]
        ]);
    }

    /**
     * Detail Jurnal Mengajar
     */
    public function showJurnal($id)
    {
        Carbon::setLocale('id');
        $guru = $this->resolveGuru();

        $jurnal = JurnalMengajar::with(['kelas', 'mapel', 'guru', 'presensiSiswa.siswa'])
            ->where('id_guru', $guru->id_guru ?? 0)
            ->findOrFail($id);

        return view('guru_mapel.jurnal.show', [
            'guru' => $guru,
            'jurnal' => $jurnal,
        ]);
    }

    /**
     * Rekap Presensi Siswa Khusus Mapel Guru
     */
    public function rekapPresensi(Request $request)
    {
        Carbon::setLocale('id');
        $guru = $this->resolveGuru();

        $idKelas = $request->input('id_kelas');
        $idMapel = $request->input('id_mapel');

        $kelasIds = JadwalPelajaran::where('id_guru', $guru->id_guru ?? 0)->pluck('id_kelas')->unique();
        $kelasList = Kelas::whereIn('id_kelas', $kelasIds)->orderBy('nama_kelas')->get();
        if ($kelasList->isEmpty()) {
            $kelasList = Kelas::orderBy('nama_kelas')->get();
        }

        $mapelIds = JadwalPelajaran::where('id_guru', $guru->id_guru ?? 0)->pluck('id_mapel')->unique();
        $mapelList = Mapel::whereIn('id_mapel', $mapelIds)->orderBy('nama_mapel')->get();
        if ($mapelList->isEmpty()) {
            $mapelList = Mapel::orderBy('nama_mapel')->get();
        }

        if (!$idKelas && $kelasList->isNotEmpty()) {
            $idKelas = $kelasList->first()->id_kelas;
        }
        if (!$idMapel && $mapelList->isNotEmpty()) {
            $idMapel = $mapelList->first()->id_mapel;
        }

        $selectedKelas = Kelas::find($idKelas);
        $selectedMapel = Mapel::find($idMapel);

        // Rekap per siswa di kelas tersebut
        $siswaRekap = collect();
        if ($selectedKelas && $selectedMapel) {
            $siswaList = Siswa::where('id_kelas', $idKelas)->where('status_aktif', true)->orderBy('nama_lengkap')->get();
            $jurnals = JurnalMengajar::where('id_guru', $guru->id_guru ?? 0)
                ->where('id_kelas', $idKelas)
                ->where('id_mapel', $idMapel)
                ->pluck('id_jurnal');

            $totalPertemuan = $jurnals->count();

            $presensiGroup = PresensiSiswa::whereIn('id_jurnal', $jurnals)
                ->get()
                ->groupBy('id_siswa');

            $siswaRekap = $siswaList->map(function ($s) use ($presensiGroup, $totalPertemuan) {
                $records = $presensiGroup->get($s->id_siswa, collect());
                $hadir = $records->where('status', 'Hadir')->count();
                $sakit = $records->where('status', 'Sakit')->count();
                $izin  = $records->where('status', 'Izin')->count();
                $alpha = $records->where('status', 'Alpha')->count();
                $disp  = $records->where('status', 'Dispensasi')->count();

                $persentase = $totalPertemuan > 0 ? round(($hadir / $totalPertemuan) * 100, 1) : 100;

                return (object) [
                    'siswa' => $s,
                    'total_pertemuan' => $totalPertemuan,
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpha' => $alpha,
                    'dispensasi' => $disp,
                    'persentase' => $persentase,
                ];
            });
        }

        return view('guru_mapel.rekap.index', [
            'guru' => $guru,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
            'idKelas' => $idKelas,
            'idMapel' => $idMapel,
            'selectedKelas' => $selectedKelas,
            'selectedMapel' => $selectedMapel,
            'siswaRekap' => $siswaRekap,
        ]);
    }

    /**
     * Pengajuan Izin Tidak Mengajar
     */
    public function izin(Request $request)
    {
        Carbon::setLocale('id');
        $guru = $this->resolveGuru();

        $izinList = IzinGuru::with(['guru', 'piketApprover', 'wakaApprover', 'kepsekApprover'])
            ->where('id_guru', $guru->id_guru ?? 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Cari permohonan izin yang ditolak dan belum lewat tanggalnya (tanggal_selesai >= hari ini)
        $recentRejected = IzinGuru::with(['piketApprover', 'wakaApprover', 'kepsekApprover'])
            ->where('id_guru', $guru->id_guru ?? 0)
            ->where('status', 'Ditolak')
            ->whereDate('tanggal_selesai', '>=', Carbon::today()->format('Y-m-d'))
            ->latest('updated_at')
            ->first();

        return view('guru_mapel.izin.index', [
            'guru'           => $guru,
            'izinList'       => $izinList,
            'recentRejected' => $recentRejected,
            'today'          => Carbon::today()->format('Y-m-d'),
        ]);
    }

    /**
     * Simpan Pengajuan Izin Guru
     */
    public function storeIzin(Request $request)
    {
        $guru = $this->resolveGuru();

        $validated = $request->validate([
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin'       => 'required|in:Sakit,Izin,Cuti,Dinas_Luar,Lainnya',
            'alasan'           => 'required|string',
            'bukti_file'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'menitipkan_tugas' => 'nullable|in:0,1',
            'keterangan_tugas' => 'nullable|string',
            'lampiran_tugas'   => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:3072',
        ]);

        $filePath = null;
        if ($request->hasFile('bukti_file')) {
            $filePath = $request->file('bukti_file')->store('izin_guru', 'public');
        }

        $menitipkanTugas = $request->boolean('menitipkan_tugas');
        $tugasFilePath = null;
        if ($menitipkanTugas && $request->hasFile('lampiran_tugas')) {
            $tugasFilePath = $request->file('lampiran_tugas')->store('tugas_izin', 'public');
        }

        $izin = IzinGuru::create([
            'id_guru'           => $guru->id_guru,
            'tanggal_mulai'     => $validated['tanggal_mulai'],
            'tanggal_selesai'   => $validated['tanggal_selesai'],
            'jenis_izin'        => $validated['jenis_izin'],
            'alasan'            => $validated['alasan'],
            'bukti_file'        => $filePath,
            'menitipkan_tugas'  => $menitipkanTugas,
            'keterangan_tugas'  => $menitipkanTugas ? $request->input('keterangan_tugas') : null,
            'lampiran_tugas'    => $tugasFilePath,
            'status'            => 'Menunggu',
            'tahap_approval'    => 'piket',
            'piket_status'      => 'Menunggu',
            'waka_status'       => 'Menunggu',
            'kepsek_status'     => 'Menunggu',
            'diinput_oleh'      => Auth::id(),
        ]);

        LogAktivitas::catat(
            'Pengajuan Izin',
            "Guru {$guru->nama_lengkap} mengajukan permohonan izin {$validated['jenis_izin']}",
            $izin,
            Auth::user()
        );

        // Notifikasi ke seluruh Guru Piket yang sedang aktif/terdaftar
        $piketUsers = User::where('role', 'guru_piket')->get();
        $tanggalStr = Carbon::parse($validated['tanggal_mulai'])->translatedFormat('d M Y');
        if ($validated['tanggal_mulai'] !== $validated['tanggal_selesai']) {
            $tanggalStr .= ' s/d ' . Carbon::parse($validated['tanggal_selesai'])->translatedFormat('d M Y');
        }
        $infoTugas = $menitipkanTugas ? ' (Disertai tugas mandiri untuk siswa)' : ' (Tanpa tugas mandiri - Butuh pantauan/pengganti)';

        foreach ($piketUsers as $pUser) {
            Notifikasi::create([
                'user_id'        => $pUser->id,
                'judul'          => 'Pengajuan Izin Guru Baru (Tahap 1 - Piket)',
                'pesan'          => "Guru {$guru->nama_lengkap} mengajukan izin {$validated['jenis_izin']} ({$tanggalStr}){$infoTugas}. Menunggu peninjauan & persetujuan Anda sebagai Guru Piket.",
                'tipe'           => 'izin_guru',
                'reference_id'   => $izin->id,
                'reference_type' => IzinGuru::class,
                'is_read'        => false,
            ]);
        }

        return redirect()->route('guru-mapel.izin')
            ->with('success', 'Pengajuan izin berhasil dikirimkan. Permintaan saat ini masuk ke sistem Guru Piket untuk peninjauan tahap 1.');
    }

    /**
     * Pusat Panduan & SOP Guru Mapel
     */
    public function help()
    {
        return view('guru_mapel.help.index');
    }
}
