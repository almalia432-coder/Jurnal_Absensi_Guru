<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalPiketKbm;
use App\Models\Guru;
use Carbon\Carbon;

class AdminJadwalPiketController extends Controller
{
    /**
     * Tampilkan data manajemen Jadwal Guru Piket untuk Administrator
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $activeTab = $request->input('tab', 'siklus_a'); // 'siklus_a', 'siklus_b', 'semua', 'hari_ini'
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Live Roster target date
        $tanggalInput = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $targetDate   = Carbon::parse($tanggalInput);
        $todayFormatted = $targetDate->translatedFormat('l, j F Y');
        $selectedHari = $targetDate->translatedFormat('l');
        $rosterTarget = JadwalPiketKbm::getRosterForDate($targetDate);

        // Schedule matrices for Siklus A & B grouped by day
        $siklusAData = [];
        $siklusBData = [];

        foreach ($hariOrder as $h) {
            $siklusAData[$h] = [
                'waka' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->first(),
                'pagi_koordinator' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'koordinator')->first(),
                'pagi_petugas' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'petugas')->orderBy('urutan')->get(),
                'siang_koordinator' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'koordinator')->first(),
                'siang_petugas' => JadwalPiketKbm::where('siklus', 'A')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'petugas')->orderBy('urutan')->get(),
            ];

            $siklusBData[$h] = [
                'waka' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->first(),
                'pagi_koordinator' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'koordinator')->first(),
                'pagi_petugas' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Pagi')->where('peran', 'petugas')->orderBy('urutan')->get(),
                'siang_koordinator' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'koordinator')->first(),
                'siang_petugas' => JadwalPiketKbm::where('siklus', 'B')->where('hari', $h)->where('shift', 'Siang')->where('peran', 'petugas')->orderBy('urutan')->get(),
            ];
        }

        // Query untuk Tab Semua Data (List CRUD)
        $search = $request->input('search');
        $filterSiklus = $request->input('filter_siklus');
        $filterHari = $request->input('filter_hari');
        $filterShift = $request->input('filter_shift');
        $filterPeran = $request->input('filter_peran');

        $querySemua = JadwalPiketKbm::with('guru')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('nama_guru', 'LIKE', "%{$search}%")
                       ->orWhere('nip', 'LIKE', "%{$search}%")
                       ->orWhere('piket_waka_nama', 'LIKE', "%{$search}%");
                });
            })
            ->when($filterSiklus, fn($q) => $q->where('siklus', $filterSiklus))
            ->when($filterHari, fn($q) => $q->where('hari', $filterHari))
            ->when($filterShift, fn($q) => $q->where('shift', $filterShift))
            ->when($filterPeran, fn($q) => $q->where('peran', $filterPeran))
            ->orderBy('siklus')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderByRaw("FIELD(shift, 'Pagi', 'Siang')")
            ->orderByRaw("FIELD(peran, 'koordinator', 'petugas')")
            ->orderBy('urutan');

        $semuaPetugas = $querySemua->paginate(15)->withQueryString();

        // Daftar Guru untuk Dropdown Select Picker
        $guruList = Guru::orderBy('nama_lengkap')->get(['id_guru', 'nama_lengkap', 'nip', 'status_aktif']);

        // KPI Ringkasan
        $kpi = [
            'total_roster' => JadwalPiketKbm::count(),
            'total_siklus_a' => JadwalPiketKbm::where('siklus', 'A')->count(),
            'total_siklus_b' => JadwalPiketKbm::where('siklus', 'B')->count(),
            'total_koordinator' => JadwalPiketKbm::where('peran', 'koordinator')->count(),
            'petugas_hari_ini_count' => ($rosterTarget['pagi_petugas']->count() ?? 0) 
                                      + ($rosterTarget['siang_petugas']->count() ?? 0)
                                      + ($rosterTarget['pagi_koordinator'] ? 1 : 0)
                                      + ($rosterTarget['siang_koordinator'] ? 1 : 0),
        ];

        return view('admin.jadwal_piket.index', compact(
            'activeTab', 'hariOrder', 'tanggalInput', 'todayFormatted', 'selectedHari',
            'rosterTarget', 'siklusAData', 'siklusBData', 'semuaPetugas', 'guruList',
            'search', 'filterSiklus', 'filterHari', 'filterShift', 'filterPeran', 'kpi'
        ));
    }

    /**
     * Tambah Penugasan Guru Piket Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siklus'      => 'required|in:A,B',
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'shift'       => 'required|in:Pagi,Siang',
            'peran'       => 'required|in:koordinator,petugas',
            'id_guru'     => 'nullable|exists:guru,id_guru',
            'nama_guru'   => 'required|string|max:255',
            'nip'         => 'nullable|string|max:50',
            'jam_mulai'   => 'nullable|string',
            'jam_selesai' => 'nullable|string',
            'urutan'      => 'nullable|integer|min:1',
        ]);

        // Default jam jika kosong
        if (empty($validated['jam_mulai'])) {
            $validated['jam_mulai'] = $validated['shift'] === 'Pagi' ? '07:00:00' : '11:00:00';
        }
        if (empty($validated['jam_selesai'])) {
            $validated['jam_selesai'] = $validated['shift'] === 'Pagi' ? '11:00:00' : '15:00:00';
        }

        // Auto isi nama & NIP jika memilih dari dropdown Guru
        if (!empty($validated['id_guru'])) {
            $guru = Guru::find($validated['id_guru']);
            if ($guru) {
                if (empty($validated['nama_guru'])) {
                    $validated['nama_guru'] = $guru->nama_lengkap;
                }
                if (empty($validated['nip'])) {
                    $validated['nip'] = $guru->nip;
                }
            }
        }

        // Tentukan urutan
        if (empty($validated['urutan'])) {
            $maxUrutan = JadwalPiketKbm::where('siklus', $validated['siklus'])
                ->where('hari', $validated['hari'])
                ->where('shift', $validated['shift'])
                ->where('peran', $validated['peran'])
                ->max('urutan');
            $validated['urutan'] = ($maxUrutan ?? 0) + 1;
        }

        // Ambil waka penanggung jawab dari hari & siklus yang sama jika sudah ada
        $existingSameDay = JadwalPiketKbm::where('siklus', $validated['siklus'])
            ->where('hari', $validated['hari'])
            ->first();
        if ($existingSameDay) {
            $validated['piket_waka_nama'] = $existingSameDay->piket_waka_nama;
            $validated['piket_waka_nip']  = $existingSameDay->piket_waka_nip;
        }

        $validated['tahun_ajaran'] = '2026/2027';
        $validated['semester'] = 'Ganjil';

        JadwalPiketKbm::create($validated);

        return redirect()->route('admin.jadwal-piket', [
            'tab' => $request->input('redirect_tab', 'siklus_' . strtolower($validated['siklus']))
        ])->with('success', "Penugasan piket ({$validated['nama_guru']}) berhasil ditambahkan!");
    }

    /**
     * Update Penugasan Guru Piket
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalPiketKbm::findOrFail($id);

        $validated = $request->validate([
            'siklus'      => 'required|in:A,B',
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'shift'       => 'required|in:Pagi,Siang',
            'peran'       => 'required|in:koordinator,petugas',
            'id_guru'     => 'nullable|exists:guru,id_guru',
            'nama_guru'   => 'required|string|max:255',
            'nip'         => 'nullable|string|max:50',
            'jam_mulai'   => 'nullable|string',
            'jam_selesai' => 'nullable|string',
            'urutan'      => 'nullable|integer|min:1',
        ]);

        if (empty($validated['jam_mulai'])) {
            $validated['jam_mulai'] = $validated['shift'] === 'Pagi' ? '07:00:00' : '11:00:00';
        }
        if (empty($validated['jam_selesai'])) {
            $validated['jam_selesai'] = $validated['shift'] === 'Pagi' ? '11:00:00' : '15:00:00';
        }

        // Auto isi nama & NIP jika memilih guru
        if (!empty($validated['id_guru']) && $validated['id_guru'] != $jadwal->id_guru) {
            $guru = Guru::find($validated['id_guru']);
            if ($guru) {
                $validated['nama_guru'] = $guru->nama_lengkap;
                $validated['nip'] = $guru->nip;
            }
        }

        $jadwal->update($validated);

        return redirect()->route('admin.jadwal-piket', [
            'tab' => $request->input('redirect_tab', 'siklus_' . strtolower($validated['siklus']))
        ])->with('success', "Data penugasan piket ({$jadwal->nama_guru}) berhasil diperbarui!");
    }

    /**
     * Hapus Penugasan Guru Piket
     */
    public function destroy(Request $request, $id)
    {
        $jadwal = JadwalPiketKbm::findOrFail($id);
        $nama = $jadwal->nama_guru;
        $siklus = $jadwal->siklus;

        $jadwal->delete();

        return redirect()->route('admin.jadwal-piket', [
            'tab' => $request->input('redirect_tab', 'siklus_' . strtolower($siklus))
        ])->with('success', "Penugasan piket ({$nama}) berhasil dihapus dari jadwal!");
    }

    /**
     * Update Waka Piket Harian (Pimpinan Penanggung Jawab)
     */
    public function updateWaka(Request $request)
    {
        $request->validate([
            'siklus'          => 'required|in:A,B',
            'hari'            => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'piket_waka_nama' => 'required|string|max:255',
            'piket_waka_nip'  => 'nullable|string|max:50',
        ]);

        $affected = JadwalPiketKbm::where('siklus', $request->siklus)
            ->where('hari', $request->hari)
            ->update([
                'piket_waka_nama' => $request->piket_waka_nama,
                'piket_waka_nip'  => $request->piket_waka_nip,
            ]);

        return redirect()->route('admin.jadwal-piket', [
            'tab' => $request->input('redirect_tab', 'siklus_' . strtolower($request->siklus))
        ])->with('success', "Waka Penanggung Jawab piket hari {$request->hari} (Siklus {$request->siklus}) berhasil diperbarui!");
    }
}
