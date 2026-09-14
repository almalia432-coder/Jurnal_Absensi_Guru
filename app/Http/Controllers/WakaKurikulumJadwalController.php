<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalPelajaran;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WakaKurikulumJadwalController extends Controller
{
    /**
     * Tampilan Utama Jadwal Pelajaran (Mode Matriks Timetable & Mode Tabel Data)
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $waka = $user->waka ?? null;

        $search        = $request->input('search');
        $hari          = $request->input('hari');
        $kelasFilter   = $request->input('kelas');
        $guru          = $request->input('guru');
        $tahunAjaranId = $request->input('tahun_ajaran');
        $viewMode      = $request->input('mode', 'matrix'); // 'matrix' atau 'table'

        // Tahun Ajaran Aktif
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // Filter Tahun Ajaran (default ke TA aktif jika ada)
        if ($tahunAjaranId === null && $tahunAjaranAktif) {
            $selectedTahunAjaran = $tahunAjaranAktif->id;
        } elseif ($tahunAjaranId === 'all') {
            $selectedTahunAjaran = 'all';
        } else {
            $selectedTahunAjaran = $tahunAjaranId;
        }

        // Data Master untuk Dropdown & Filter
        $guruList          = Guru::where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $kelasList         = Kelas::with(['waliKelas', 'jurusanRelation'])->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapelList         = Mapel::orderBy('nama_mapel')->get();
        $tahunAjaranList   = TahunAjaran::orderByDesc('is_aktif')->orderByDesc('id')->get();
        $existingTahunList = TahunAjaran::select('nama')->distinct()->pluck('nama')->toArray();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Kelas yang dipilih untuk Mode Matriks (default ke kelas pertama jika tidak dipilih)
        $selectedKelasId = $kelasFilter ?: ($kelasList->first()?->id_kelas ?? null);
        $selectedKelas   = $kelasList->firstWhere('id_kelas', $selectedKelasId) ?? $kelasList->first();

        // Data Matriks untuk Kelas Terpilih
        $matrixJadwal    = [];
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

        // Data Query untuk Mode Tabel Data
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

        // Statistik Jadwal
        $baseQuery = JadwalPelajaran::query();
        if ($selectedTahunAjaran && $selectedTahunAjaran !== 'all') {
            $baseQuery->where('id_tahun_ajaran', $selectedTahunAjaran);
        }

        $totalJadwal     = (clone $baseQuery)->count();
        $totalGuruAktif  = (clone $baseQuery)->distinct('id_guru')->count('id_guru');
        $totalKelasAktif = (clone $baseQuery)->distinct('id_kelas')->count('id_kelas');
        $totalMapelAktif = (clone $baseQuery)->distinct('id_mapel')->count('id_mapel');

        // Mapping Slot Waktu Standar SMKN 1 Boyolangu (Jam 1 s/d 10)
        $slotTimesSK  = [];
        $slotTimesJmt = [];
        for ($s = 1; $s <= 10; $s++) {
            $slotTimesSK[$s]  = JadwalPelajaranController::getDefaultTimeSlot('Senin', $s);
            $slotTimesJmt[$s] = JadwalPelajaranController::getDefaultTimeSlot('Jumat', $s);
        }

        return view('waka_kurikulum.jadwal.index', compact(
            'user',
            'waka',
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

    /**
     * Tambah Jadwal Pelajaran Baru (Mendukung rentang 1-4 jam berturut-turut & deteksi bentrok)
     */
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

        // Validasi maksimal 4 jam berturut-turut
        if (($validated['jam_sampai'] - $validated['jam_dari'] + 1) > 4) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Maksimal alokasi jam adalah 4 jam berturut-turut.'], 422);
            }
            return redirect()->route('waka-kurikulum.jadwal', ['kelas' => $validated['id_kelas']])
                ->with('error', 'Maksimal alokasi jam adalah 4 jam berturut-turut.');
        }

        $namaTahun     = trim($validated['nama_tahun_ajaran']);
        $semester      = $validated['semester'];
        $tahunAjaran   = $this->resolveTahunAjaran($namaTahun, $semester);
        $idTahunAjaran = $tahunAjaran->id;

        $jamRange    = range((int)$validated['jam_dari'], (int)$validated['jam_sampai']);
        $errors      = [];
        $createdList = [];

        foreach ($jamRange as $jam) {
            // Cek bentrok kelas pada jam ini
            $kelasConflict = JadwalPelajaran::where('id_tahun_ajaran', $idTahunAjaran)
                ->where('hari', $validated['hari'])
                ->where('jam_ke', $jam)
                ->where('id_kelas', $validated['id_kelas'])
                ->exists();

            if ($kelasConflict) {
                $errors[] = "Kelas sudah terisi jadwal lain di jam ke-{$jam}";
                continue;
            }

            // Cek bentrok guru pada jam ini
            $guruConflict = JadwalPelajaran::where('id_tahun_ajaran', $idTahunAjaran)
                ->where('hari', $validated['hari'])
                ->where('jam_ke', $jam)
                ->where('id_guru', $validated['id_guru'])
                ->with('kelas')
                ->first();

            if ($guruConflict) {
                $guru = Guru::find($validated['id_guru']);
                $kelasLain = $guruConflict->kelas->nama_kelas ?? 'kelas lain';
                $errors[] = "Guru {$guru->nama_lengkap} sedang mengajar di {$kelasLain} pada jam ke-{$jam}";
                continue;
            }

            // Hitung slot waktu default
            $defaultTimes = JadwalPelajaranController::getDefaultTimeSlot($validated['hari'], $jam);
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
            return redirect()->route('waka-kurikulum.jadwal', ['kelas' => $validated['id_kelas']])
                ->with('error', "Gagal menyimpan jadwal: {$pesanError}");
        }

        $jamLabel = $validated['jam_dari'] === $validated['jam_sampai']
            ? "Jam ke-{$validated['jam_dari']}"
            : "Jam ke-{$validated['jam_dari']} s/d {$validated['jam_sampai']} ({$sukses} slot)";

        $successMsg = "Jadwal {$namaTahun} ({$semester}) — {$jamLabel} berhasil disimpan.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'data'    => $createdList,
                'partial' => !empty($errors),
                'errors'  => $errors,
            ]);
        }

        return redirect()->route('waka-kurikulum.jadwal', ['kelas' => $validated['id_kelas']])
            ->with('success', $successMsg);
    }

    /**
     * Perbarui Data Jadwal Pelajaran
     */
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

        // Cek bentrok kelas
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
            return redirect()->route('waka-kurikulum.jadwal', ['kelas' => $validated['id_kelas']])->with('error', $msg);
        }

        // Cek bentrok guru
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
            $msg = "Bentrok Guru: {$guru->nama_lengkap} sudah terjadwal di {$kelasNama} pada hari {$validated['hari']} jam ke-{$jam}.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('waka-kurikulum.jadwal', ['kelas' => $validated['id_kelas']])->with('error', $msg);
        }

        $defaultTimes = JadwalPelajaranController::getDefaultTimeSlot($validated['hari'], $jam);
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

        return redirect()->route('waka-kurikulum.jadwal', ['kelas' => $validated['id_kelas']])->with('success', $msg);
    }

    /**
     * AJAX endpoint: Drag and Drop perpindahan slot jadwal pelajaran
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

        // Jika slot sama persis
        if ($jadwal->hari === $targetHari && (int)$jadwal->jam_ke === $targetJam && (int)$jadwal->id_kelas === (int)$targetKelas) {
            return response()->json([
                'success' => true,
                'message' => 'Posisi slot tidak berubah.',
                'jadwal'  => $jadwal,
            ]);
        }

        // Cek apakah slot tujuan sudah terisi di kelas ini
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

        // Cek bentrok guru di kelas lain pada jam tujuan
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

        // Hitung waktu slot otomatis
        $defaultTimes = JadwalPelajaranController::getDefaultTimeSlot($targetHari, $targetJam);

        $jadwal->update([
            'hari'        => $targetHari,
            'jam_ke'      => $targetJam,
            'id_kelas'    => $targetKelas,
            'jam_mulai'   => $defaultTimes['jam_mulai'],
            'jam_selesai' => $defaultTimes['jam_selesai'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Jadwal {$jadwal->mapel->nama_mapel} berhasil dipindahkan ke {$targetHari} jam ke-{$targetJam}!",
            'jadwal'  => $jadwal->fresh(['mapel', 'guru', 'kelas']),
        ]);
    }

    /**
     * AJAX endpoint: Tukar slot dua jadwal pelajaran (Swap)
     */
    public function swap(Request $request)
    {
        $validated = $request->validate([
            'id_jadwal_a' => 'required|exists:jadwal_pelajaran,id_jadwal',
            'id_jadwal_b' => 'required|exists:jadwal_pelajaran,id_jadwal',
        ]);

        $jadwalA = JadwalPelajaran::with(['guru', 'mapel', 'kelas'])->findOrFail($validated['id_jadwal_a']);
        $jadwalB = JadwalPelajaran::with(['guru', 'mapel', 'kelas'])->findOrFail($validated['id_jadwal_b']);

        // Simpan posisi lama A
        $oldHariA = $jadwalA->hari;
        $oldJamA  = $jadwalA->jam_ke;
        $oldHariB = $jadwalB->hari;
        $oldJamB  = $jadwalB->jam_ke;

        // Cek apakah guru A bentrok di waktu B
        $guruAConflict = JadwalPelajaran::where('id_tahun_ajaran', $jadwalA->id_tahun_ajaran)
            ->where('hari', $oldHariB)
            ->where('jam_ke', $oldJamB)
            ->where('id_guru', $jadwalA->id_guru)
            ->whereNotIn('id_jadwal', [$jadwalA->id_jadwal, $jadwalB->id_jadwal])
            ->exists();

        if ($guruAConflict) {
            return response()->json([
                'success' => false,
                'message' => "Guru {$jadwalA->guru->nama_lengkap} bentrok di waktu target {$oldHariB} jam ke-{$oldJamB}.",
            ], 422);
        }

        // Cek apakah guru B bentrok di waktu A
        $guruBConflict = JadwalPelajaran::where('id_tahun_ajaran', $jadwalB->id_tahun_ajaran)
            ->where('hari', $oldHariA)
            ->where('jam_ke', $oldJamA)
            ->where('id_guru', $jadwalB->id_guru)
            ->whereNotIn('id_jadwal', [$jadwalA->id_jadwal, $jadwalB->id_jadwal])
            ->exists();

        if ($guruBConflict) {
            return response()->json([
                'success' => false,
                'message' => "Guru {$jadwalB->guru->nama_lengkap} bentrok di waktu target {$oldHariA} jam ke-{$oldJamA}.",
            ], 422);
        }

        $timesA = JadwalPelajaranController::getDefaultTimeSlot($oldHariB, $oldJamB);
        $timesB = JadwalPelajaranController::getDefaultTimeSlot($oldHariA, $oldJamA);

        DB::transaction(function () use ($jadwalA, $jadwalB, $oldHariA, $oldJamA, $oldHariB, $oldJamB, $timesA, $timesB) {
            $jadwalA->update([
                'hari'        => $oldHariB,
                'jam_ke'      => $oldJamB,
                'jam_mulai'   => $timesA['jam_mulai'],
                'jam_selesai' => $timesA['jam_selesai'],
            ]);

            $jadwalB->update([
                'hari'        => $oldHariA,
                'jam_ke'      => $oldJamA,
                'jam_mulai'   => $timesB['jam_mulai'],
                'jam_selesai' => $timesB['jam_selesai'],
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Jadwal {$jadwalA->mapel->nama_mapel} dan {$jadwalB->mapel->nama_mapel} berhasil ditukar posisinya!",
        ]);
    }

    /**
     * Hapus Jadwal Pelajaran (Dengan proteksi relasi Jurnal Mengajar)
     */
    public function destroy(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::with(['mapel', 'kelas'])->findOrFail($id);

        if ($jadwal->jurnalMengajar()->exists()) {
            $count = $jadwal->jurnalMengajar()->count();
            $msg = "Jadwal ini tidak dapat dihapus karena sudah memiliki {$count} catatan jurnal mengajar aktif.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('waka-kurikulum.jadwal', ['kelas' => $jadwal->id_kelas])
                ->with('error', $msg);
        }

        $info = "{$jadwal->mapel->nama_mapel} - {$jadwal->kelas->nama_kelas} ({$jadwal->hari}, Jam ke-{$jadwal->jam_ke})";
        $kelasId = $jadwal->id_kelas;
        $jadwal->delete();

        $msg = "Jadwal {$info} berhasil dihapus.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('waka-kurikulum.jadwal', ['kelas' => $kelasId])
            ->with('success', $msg);
    }

    /**
     * Pengaturan Cepat Tahun Ajaran Aktif
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

        TahunAjaran::query()->update(['is_aktif' => false]);
        $target->is_aktif = true;
        $target->save();

        return redirect()->route('waka-kurikulum.jadwal', ['tahun_ajaran' => $target->id, 'kelas' => $request->input('kelas')])
            ->with('success', "Tahun Ajaran {$target->nama} — Semester {$target->semester} berhasil ditetapkan sebagai Tahun Ajaran Aktif.");
    }

    /**
     * Helper privat untuk menemukan atau membuat TahunAjaran
     */
    private function resolveTahunAjaran(string $nama, string $semester): TahunAjaran
    {
        $ta = TahunAjaran::where('nama', $nama)->where('semester', $semester)->first();

        if ($ta) {
            return $ta;
        }

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
