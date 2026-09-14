<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalPelajaran;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;

class JadwalPelajaranController extends Controller
{
    /**
     * Standard slot time mapping for SMKN 1 Boyolangu
     */
    public static function getDefaultTimeSlot(string $hari, int $jam_ke): array
    {
        $isJumat = strtolower($hari) === 'jumat';

        if ($isJumat) {
            $jumatSlots = [
                1  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:30'],
                2  => ['jam_mulai' => '07:30', 'jam_selesai' => '08:00'],
                3  => ['jam_mulai' => '08:00', 'jam_selesai' => '08:30'],
                4  => ['jam_mulai' => '08:30', 'jam_selesai' => '09:00'],
                5  => ['jam_mulai' => '09:00', 'jam_selesai' => '09:30'],
                6  => ['jam_mulai' => '09:45', 'jam_selesai' => '10:15'],
                7  => ['jam_mulai' => '10:15', 'jam_selesai' => '10:45'],
                8  => ['jam_mulai' => '10:45', 'jam_selesai' => '11:15'],
                9  => ['jam_mulai' => '11:15', 'jam_selesai' => '11:45'],
                10 => ['jam_mulai' => '13:00', 'jam_selesai' => '13:30'],
            ];
            return $jumatSlots[$jam_ke] ?? ['jam_mulai' => '07:00', 'jam_selesai' => '07:30'];
        }

        $regularSlots = [
            1  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:40'],
            2  => ['jam_mulai' => '07:40', 'jam_selesai' => '08:20'],
            3  => ['jam_mulai' => '08:20', 'jam_selesai' => '09:00'],
            4  => ['jam_mulai' => '09:00', 'jam_selesai' => '09:40'],
            5  => ['jam_mulai' => '09:55', 'jam_selesai' => '10:35'],
            6  => ['jam_mulai' => '10:35', 'jam_selesai' => '11:15'],
            7  => ['jam_mulai' => '11:15', 'jam_selesai' => '11:55'],
            8  => ['jam_mulai' => '12:35', 'jam_selesai' => '13:15'],
            9  => ['jam_mulai' => '13:15', 'jam_selesai' => '13:55'],
            10 => ['jam_mulai' => '13:55', 'jam_selesai' => '14:35'],
            11 => ['jam_mulai' => '14:35', 'jam_selesai' => '15:15'],
            12 => ['jam_mulai' => '15:15', 'jam_selesai' => '15:55'],
        ];

        return $regularSlots[$jam_ke] ?? ['jam_mulai' => '07:00', 'jam_selesai' => '07:40'];
    }

    public function index(Request $request)
    {
        $search        = $request->input('search');
        $hari          = $request->input('hari');
        $kelasFilter   = $request->input('kelas');
        $guru          = $request->input('guru');
        $tahunAjaranId = $request->input('tahun_ajaran');
        $viewMode      = $request->input('mode', 'matrix'); // 'matrix' or 'table'

        // Active Tahun Ajaran
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // If no filter selected, default to active tahun ajaran (if exists)
        if ($tahunAjaranId === null && $tahunAjaranAktif) {
            $selectedTahunAjaran = $tahunAjaranAktif->id;
        } elseif ($tahunAjaranId === 'all') {
            $selectedTahunAjaran = 'all';
        } else {
            $selectedTahunAjaran = $tahunAjaranId;
        }

        // Dropdown & suggestion data
        $guruList          = Guru::where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $kelasList         = Kelas::with(['waliKelas', 'jurusanRelation'])->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapelList         = Mapel::orderBy('nama_mapel')->get();
        $tahunAjaranList   = TahunAjaran::orderByDesc('is_aktif')->orderByDesc('id')->get();
        $existingTahunList = TahunAjaran::select('nama')->distinct()->pluck('nama')->toArray();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Selected Class for Matrix View (defaults to first class in list if not specified)
        $selectedKelasId = $kelasFilter ?: ($kelasList->first()?->id_kelas ?? null);
        $selectedKelas   = $kelasList->firstWhere('id_kelas', $selectedKelasId) ?? $kelasList->first();

        // Matrix Data for Selected Class
        $matrixJadwal = [];
        $totalMapelKelas = 0;
        $totalJamKelas   = 0;

        if ($selectedKelas) {
            $matrixQuery = JadwalPelajaran::with(['guru', 'mapel', 'kelas', 'tahunAjaran'])
                ->where('id_kelas', $selectedKelas->id_kelas);

            if ($selectedTahunAjaran && $selectedTahunAjaran !== 'all') {
                $matrixQuery->where('id_tahun_ajaran', $selectedTahunAjaran);
            }

            $matrixJadwalRaw = $matrixQuery->get();
            $totalMapelKelas = $matrixJadwalRaw->pluck('id_mapel')->unique()->count();
            $totalJamKelas   = $matrixJadwalRaw->count();

            foreach ($matrixJadwalRaw as $item) {
                $matrixJadwal[$item->hari][$item->jam_ke] = $item;
            }
        }

        // Table Query (for Table Mode or Search)
        $query = JadwalPelajaran::with(['guru', 'kelas.waliKelas', 'mapel', 'tahunAjaran'])
            ->when($selectedTahunAjaran && $selectedTahunAjaran !== 'all', function ($q) use ($selectedTahunAjaran) {
                $q->where('id_tahun_ajaran', $selectedTahunAjaran);
            })
            ->when($hari && $hari !== 'all', function ($q) use ($hari) {
                $q->where('hari', $hari);
            })
            ->when($kelasFilter, function ($q) use ($kelasFilter) {
                $q->where('id_kelas', $kelasFilter);
            })
            ->when($guru, function ($q) use ($guru) {
                $q->where('id_guru', $guru);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->whereHas('mapel', function ($q3) use ($search) {
                        $q3->where('nama_mapel', 'LIKE', "%{$search}%")
                           ->orWhere('kode_mapel', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('guru', function ($q3) use ($search) {
                        $q3->where('nama_lengkap', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('kelas', function ($q3) use ($search) {
                        $q3->where('nama_kelas', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('tahunAjaran', function ($q3) use ($search) {
                        $q3->where('nama', 'LIKE', "%{$search}%");
                    });
                });
            });

        $jadwalList = $query->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat')")
            ->orderBy('jam_ke')
            ->paginate(20)
            ->withQueryString();

        // Statistics
        $baseQuery = JadwalPelajaran::query();
        if ($selectedTahunAjaran && $selectedTahunAjaran !== 'all') {
            $baseQuery->where('id_tahun_ajaran', $selectedTahunAjaran);
        }

        $totalJadwal     = (clone $baseQuery)->count();
        $totalGuruAktif  = (clone $baseQuery)->distinct('id_guru')->count('id_guru');
        $totalKelasAktif = (clone $baseQuery)->distinct('id_kelas')->count('id_kelas');
        $totalMapelAktif = (clone $baseQuery)->distinct('id_mapel')->count('id_mapel');

        // Standard Slot Times definition for 1..10 slots
        $slotTimesSK = [];
        $slotTimesJmt = [];
        for ($s = 1; $s <= 10; $s++) {
            $slotTimesSK[$s]  = self::getDefaultTimeSlot('Senin', $s);
            $slotTimesJmt[$s] = self::getDefaultTimeSlot('Jumat', $s);
        }

        return view('admin.jadwal.index', compact(
            'jadwalList',
            'matrixJadwal',
            'selectedKelas',
            'totalMapelKelas',
            'totalJamKelas',
            'slotTimesSK',
            'slotTimesJmt',
            'totalJadwal',
            'totalGuruAktif',
            'totalKelasAktif',
            'totalMapelAktif',
            'guruList',
            'kelasList',
            'mapelList',
            'tahunAjaranList',
            'tahunAjaranAktif',
            'existingTahunList',
            'hariList',
            'search',
            'hari',
            'kelasFilter',
            'guru',
            'selectedTahunAjaran',
            'viewMode'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:20',
            'semester'          => 'required|in:Ganjil,Genap',
            'hari'              => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_dari'          => 'required|integer|min:1|max:12',
            'jam_sampai'        => 'required|integer|min:1|max:12|gte:jam_dari',
            'jam_mulai'         => 'nullable|date_format:H:i',
            'jam_selesai'       => 'nullable|date_format:H:i',
            'id_mapel'          => 'required|exists:mapel,id_mapel',
            'id_guru'           => 'required|exists:guru,id_guru',
            'id_kelas'          => 'required|exists:kelas,id_kelas',
        ]);

        // Validate max 4 jam range
        if (($validated['jam_sampai'] - $validated['jam_dari'] + 1) > 4) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Maksimal pemilihan jam adalah 4 jam berturut-turut.'], 422);
            }
            return redirect()->route('admin.jadwal', ['kelas' => $validated['id_kelas']])
                ->with('error', 'Maksimal pemilihan jam adalah 4 jam berturut-turut.');
        }

        $namaTahun = trim($validated['nama_tahun_ajaran']);
        $semester  = $validated['semester'];
        $tahunAjaran   = $this->resolveTahunAjaran($namaTahun, $semester);
        $idTahunAjaran = $tahunAjaran->id;

        $jamRange = range((int)$validated['jam_dari'], (int)$validated['jam_sampai']);
        $errors   = [];
        $createdList = [];

        foreach ($jamRange as $jam) {
            // Check kelas conflict for this slot
            $kelasConflict = JadwalPelajaran::where('id_tahun_ajaran', $idTahunAjaran)
                ->where('hari', $validated['hari'])
                ->where('jam_ke', $jam)
                ->where('id_kelas', $validated['id_kelas'])
                ->exists();

            if ($kelasConflict) {
                $errors[] = "Kelas sudah terisi di jam ke-{$jam}";
                continue;
            }

            // Check guru conflict
            $guruConflict = JadwalPelajaran::where('id_tahun_ajaran', $idTahunAjaran)
                ->where('hari', $validated['hari'])
                ->where('jam_ke', $jam)
                ->where('id_guru', $validated['id_guru'])
                ->exists();

            if ($guruConflict) {
                $guru = Guru::find($validated['id_guru']);
                $errors[] = "Guru {$guru->nama_lengkap} sudah mengajar di jam ke-{$jam}";
                continue;
            }

            // Calculate slot times if not explicitly provided or if multi-slot
            $defaultTimes = self::getDefaultTimeSlot($validated['hari'], $jam);
            $mulai   = !empty($validated['jam_mulai']) ? $validated['jam_mulai'] : $defaultTimes['jam_mulai'];
            $selesai = !empty($validated['jam_selesai']) ? $validated['jam_selesai'] : $defaultTimes['jam_selesai'];

            $newJadwal = JadwalPelajaran::create([
                'id_tahun_ajaran' => $idTahunAjaran,
                'hari'            => $validated['hari'],
                'jam_ke'          => $jam,
                'jam_mulai'       => $mulai,
                'jam_selesai'     => $selesai,
                'id_mapel'        => $validated['id_mapel'],
                'id_guru'         => $validated['id_guru'],
                'id_kelas'        => $validated['id_kelas'],
            ]);

            $createdList[] = $newJadwal->load(['mapel', 'guru', 'kelas']);
        }

        $totalJam = count($jamRange);
        $sukses   = $totalJam - count($errors);

        if (!empty($errors) && $sukses === 0) {
            $pesanError = implode('; ', $errors);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "Gagal menyimpan jadwal: {$pesanError}"], 422);
            }
            return redirect()->route('admin.jadwal', ['kelas' => $validated['id_kelas']])
                ->with('error', "Gagal menyimpan jadwal: {$pesanError}");
        }

        $jamLabel = $validated['jam_dari'] === $validated['jam_sampai']
            ? "Jam ke-{$validated['jam_dari']}"
            : "Jam ke-{$validated['jam_dari']} s/d {$validated['jam_sampai']} ({$sukses} slot)";

        $successMsg = "Jadwal {$namaTahun} ({$semester}) — {$jamLabel} berhasil ditambahkan.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'data'    => $createdList,
                'partial' => !empty($errors),
                'errors'  => $errors,
            ]);
        }

        return redirect()->route('admin.jadwal', ['kelas' => $validated['id_kelas']])
            ->with('success', $successMsg);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $validated = $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:20',
            'semester'          => 'required|in:Ganjil,Genap',
            'hari'              => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_dari'          => 'required|integer|min:1|max:12',
            'jam_sampai'        => 'required|integer|min:1|max:12|gte:jam_dari',
            'jam_mulai'         => 'nullable|date_format:H:i',
            'jam_selesai'       => 'nullable|date_format:H:i',
            'id_mapel'          => 'required|exists:mapel,id_mapel',
            'id_guru'           => 'required|exists:guru,id_guru',
            'id_kelas'          => 'required|exists:kelas,id_kelas',
        ]);

        $namaTahun     = trim($validated['nama_tahun_ajaran']);
        $semester      = $validated['semester'];
        $tahunAjaran   = $this->resolveTahunAjaran($namaTahun, $semester);
        $idTahunAjaran = $tahunAjaran->id;

        $jam = (int)$validated['jam_dari'];

        $kelasConflict = JadwalPelajaran::where('id_tahun_ajaran', $idTahunAjaran)
            ->where('hari', $validated['hari'])
            ->where('jam_ke', $jam)
            ->where('id_kelas', $validated['id_kelas'])
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->exists();

        if ($kelasConflict) {
            $msg = "Jadwal untuk kelas ini pada hari {$validated['hari']} jam ke-{$jam} sudah terisi.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('admin.jadwal', ['kelas' => $validated['id_kelas']])->with('error', $msg);
        }

        $guruConflict = JadwalPelajaran::where('id_tahun_ajaran', $idTahunAjaran)
            ->where('hari', $validated['hari'])
            ->where('jam_ke', $jam)
            ->where('id_guru', $validated['id_guru'])
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->with('kelas')
            ->first();

        if ($guruConflict) {
            $guru = Guru::find($validated['id_guru']);
            $kelasNama = $guruConflict->kelas->nama_kelas ?? 'kelas lain';
            $msg = "Guru {$guru->nama_lengkap} sudah memiliki jadwal di {$kelasNama} pada hari {$validated['hari']} jam ke-{$jam}.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('admin.jadwal', ['kelas' => $validated['id_kelas']])->with('error', $msg);
        }

        $defaultTimes = self::getDefaultTimeSlot($validated['hari'], $jam);
        $mulai   = !empty($validated['jam_mulai']) ? $validated['jam_mulai'] : $defaultTimes['jam_mulai'];
        $selesai = !empty($validated['jam_selesai']) ? $validated['jam_selesai'] : $defaultTimes['jam_selesai'];

        $jadwal->update([
            'id_tahun_ajaran' => $idTahunAjaran,
            'hari'            => $validated['hari'],
            'jam_ke'          => $jam,
            'jam_mulai'       => $mulai,
            'jam_selesai'     => $selesai,
            'id_mapel'        => $validated['id_mapel'],
            'id_guru'         => $validated['id_guru'],
            'id_kelas'        => $validated['id_kelas'],
        ]);

        $msg = "Jadwal pelajaran TA {$namaTahun} ({$semester}) berhasil diperbarui.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'jadwal'  => $jadwal->load(['mapel', 'guru', 'kelas', 'tahunAjaran']),
            ]);
        }

        return redirect()->route('admin.jadwal', ['kelas' => $validated['id_kelas']])->with('success', $msg);
    }

    /**
     * AJAX endpoint for Drag and Drop moving a schedule to a target slot
     */
    public function move(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::with(['guru', 'mapel', 'kelas'])->findOrFail($id);

        $validated = $request->validate([
            'target_hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'target_jam'  => 'required|integer|min:1|max:12',
            'id_kelas'    => 'nullable|exists:kelas,id_kelas',
        ]);

        $targetHari  = $validated['target_hari'];
        $targetJam   = (int)$validated['target_jam'];
        $targetKelas = $validated['id_kelas'] ?? $jadwal->id_kelas;

        // If dropped onto the exact same slot, return success without doing anything
        if ($jadwal->hari === $targetHari && (int)$jadwal->jam_ke === $targetJam && (int)$jadwal->id_kelas === (int)$targetKelas) {
            return response()->json([
                'success' => true,
                'message' => 'Posisi slot tidak berubah.',
                'jadwal'  => $jadwal,
            ]);
        }

        // Check if destination slot is occupied in this class
        $kelasConflict = JadwalPelajaran::where('id_tahun_ajaran', $jadwal->id_tahun_ajaran)
            ->where('hari', $targetHari)
            ->where('jam_ke', $targetJam)
            ->where('id_kelas', $targetKelas)
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->exists();

        if ($kelasConflict) {
            return response()->json([
                'success' => false,
                'message' => "Slot hari {$targetHari} jam ke-{$targetJam} sudah terisi oleh jadwal lain.",
            ], 422);
        }

        // Check if teacher is busy teaching another class at that slot
        $guruConflict = JadwalPelajaran::where('id_tahun_ajaran', $jadwal->id_tahun_ajaran)
            ->where('hari', $targetHari)
            ->where('jam_ke', $targetJam)
            ->where('id_guru', $jadwal->id_guru)
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->with('kelas')
            ->first();

        if ($guruConflict) {
            $guruName = $jadwal->guru->nama_lengkap ?? 'Guru bersangkutan';
            $otherKelas = $guruConflict->kelas->nama_kelas ?? 'kelas lain';
            return response()->json([
                'success' => false,
                'message' => "Bentrok: {$guruName} sudah mengajar di {$otherKelas} pada hari {$targetHari} jam ke-{$targetJam}!",
            ], 422);
        }

        // Auto compute slot times
        $times = self::getDefaultTimeSlot($targetHari, $targetJam);

        $jadwal->update([
            'hari'        => $targetHari,
            'jam_ke'      => $targetJam,
            'id_kelas'    => $targetKelas,
            'jam_mulai'   => $times['jam_mulai'],
            'jam_selesai' => $times['jam_selesai'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Jadwal {$jadwal->mapel->nama_mapel} berhasil dipindahkan ke {$targetHari} jam ke-{$targetJam}.",
            'jadwal'  => $jadwal->load(['guru', 'mapel', 'kelas']),
            'times'   => $times,
        ]);
    }

    /**
     * AJAX endpoint for swapping two schedules
     */
    public function swap(Request $request)
    {
        $validated = $request->validate([
            'source_id' => 'required|exists:jadwal_pelajaran,id_jadwal',
            'target_id' => 'required|exists:jadwal_pelajaran,id_jadwal',
        ]);

        $j1 = JadwalPelajaran::with(['guru', 'mapel', 'kelas'])->findOrFail($validated['source_id']);
        $j2 = JadwalPelajaran::with(['guru', 'mapel', 'kelas'])->findOrFail($validated['target_id']);

        // Check if teacher 1 can go to j2 slot
        $conflict1 = JadwalPelajaran::where('id_tahun_ajaran', $j1->id_tahun_ajaran)
            ->where('hari', $j2->hari)
            ->where('jam_ke', $j2->jam_ke)
            ->where('id_guru', $j1->id_guru)
            ->whereNotIn('id_jadwal', [$j1->id_jadwal, $j2->id_jadwal])
            ->exists();

        if ($conflict1) {
            return response()->json([
                'success' => false,
                'message' => "Bentrok: {$j1->guru->nama_lengkap} sudah mengajar di slot target ({$j2->hari} Jam {$j2->jam_ke}).",
            ], 422);
        }

        // Check if teacher 2 can go to j1 slot
        $conflict2 = JadwalPelajaran::where('id_tahun_ajaran', $j2->id_tahun_ajaran)
            ->where('hari', $j1->hari)
            ->where('jam_ke', $j1->jam_ke)
            ->where('id_guru', $j2->id_guru)
            ->whereNotIn('id_jadwal', [$j1->id_jadwal, $j2->id_jadwal])
            ->exists();

        if ($conflict2) {
            return response()->json([
                'success' => false,
                'message' => "Bentrok: {$j2->guru->nama_lengkap} sudah mengajar di slot target ({$j1->hari} Jam {$j1->jam_ke}).",
            ], 422);
        }

        DB::transaction(function () use ($j1, $j2) {
            $h1 = $j1->hari;
            $jk1 = $j1->jam_ke;
            $m1 = $j1->jam_mulai;
            $s1 = $j1->jam_selesai;

            $h2 = $j2->hari;
            $jk2 = $j2->jam_ke;
            $m2 = $j2->jam_mulai;
            $s2 = $j2->jam_selesai;

            $j1->update([
                'hari'        => $h2,
                'jam_ke'      => $jk2,
                'jam_mulai'   => $m2,
                'jam_selesai' => $s2,
            ]);

            $j2->update([
                'hari'        => $h1,
                'jam_ke'      => $jk1,
                'jam_mulai'   => $m1,
                'jam_selesai' => $s1,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Posisi jadwal {$j1->mapel->nama_mapel} dan {$j2->mapel->nama_mapel} berhasil ditukar.",
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::with(['mapel', 'kelas', 'guru'])->findOrFail($id);

        // Check if jadwal has related jurnal
        if ($jadwal->jurnalMengajar()->count() > 0) {
            $msg = "Jadwal ini tidak dapat dihapus karena sudah memiliki {$jadwal->jurnalMengajar()->count()} catatan jurnal mengajar.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('admin.jadwal', ['kelas' => $jadwal->id_kelas])
                ->with('error', $msg);
        }

        $info = "{$jadwal->mapel->nama_mapel} - {$jadwal->kelas->nama_kelas} ({$jadwal->hari}, Jam ke-{$jadwal->jam_ke})";
        $kelasId = $jadwal->id_kelas;
        $jadwal->delete();

        $msg = "Jadwal {$info} berhasil dihapus.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.jadwal', ['kelas' => $kelasId])
            ->with('success', $msg);
    }

    /**
     * Set or manually create an active Tahun Ajaran
     */
    public function setTahunAjaran(Request $request)
    {
        $action = $request->input('mode', 'manual');

        if ($action === 'select' && $request->filled('id_tahun_ajaran')) {
            $target = TahunAjaran::findOrFail($request->input('id_tahun_ajaran'));
        } else {
            $validated = $request->validate([
                'nama_tahun_ajaran' => 'required|string|max:20',
                'semester'          => 'required|in:Ganjil,Genap',
            ]);

            $target = $this->resolveTahunAjaran(trim($validated['nama_tahun_ajaran']), $validated['semester']);
        }

        // Set all others to false, set target to true
        TahunAjaran::query()->update(['is_aktif' => false]);
        $target->is_aktif = true;
        $target->save();

        return redirect()->route('admin.jadwal', ['tahun_ajaran' => $target->id, 'kelas' => $request->input('kelas')])
            ->with('success', "Tahun Ajaran {$target->nama} — Semester {$target->semester} berhasil ditetapkan sebagai Tahun Ajaran Aktif.");
    }

    /**
     * Helper to find or create TahunAjaran model
     */
    private function resolveTahunAjaran(string $nama, string $semester): TahunAjaran
    {
        $ta = TahunAjaran::where('nama', $nama)->where('semester', $semester)->first();

        if ($ta) {
            return $ta;
        }

        // Generate start/end dates
        $yearParts = explode('/', $nama);
        $startYear = isset($yearParts[0]) && is_numeric($yearParts[0]) ? (int)$yearParts[0] : now()->year;
        $endYear   = isset($yearParts[1]) && is_numeric($yearParts[1]) ? (int)$yearParts[1] : ($startYear + 1);

        if ($semester === 'Ganjil') {
            $tanggalMulai   = "{$startYear}-07-15";
            $tanggalSelesai = "{$startYear}-12-31";
        } else {
            $tanggalMulai   = "{$endYear}-01-02";
            $tanggalSelesai = "{$endYear}-06-30";
        }

        $isAktif = TahunAjaran::where('is_aktif', true)->doesntExist();

        return TahunAjaran::create([
            'nama'            => $nama,
            'semester'        => $semester,
            'tanggal_mulai'   => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'is_aktif'        => $isAktif,
        ]);
    }
}
