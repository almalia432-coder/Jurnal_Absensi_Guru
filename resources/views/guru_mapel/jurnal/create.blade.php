@extends('layouts.guru_mapel')

@section('title', 'Isi Jurnal & Presensi - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Form Input Jurnal & Presensi Siswa')
@section('header_subtitle', 'Catat ringkasan materi pembelajaran dan rekam kehadiran siswa di kelas')

@section('styles')
<style>
    .two-cols-layout {
        display: grid;
        grid-template-columns: 1fr 1.3fr;
        gap: 24px;
        align-items: flex-start;
    }

    .form-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
    }

    .form-card-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-card-head h3 {
        font-size: 16px;
        font-weight: 800;
        color: #1b2559;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #2b3674;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        font-size: 13.5px;
        font-family: inherit;
        color: #1b2559;
        outline: none;
        transition: border-color 0.2s ease;
    }
    .form-control:focus {
        border-color: #2b43b9;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    /* Attendance Table Styling */
    .table-responsive { overflow-x: auto; max-height: 480px; }
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }

    .attendance-table th {
        background-color: #f8fafc;
        padding: 10px 12px;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        color: #707e94;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .attendance-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f4f7fe;
        color: #2b3674;
        vertical-align: middle;
    }

    .radio-group {
        display: flex;
        gap: 6px;
    }

    .radio-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        user-select: none;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #64748b;
        transition: all 0.15s ease;
    }

    .radio-pill input { display: none; }

    .radio-pill.hadir.checked { 
        background: #e6f9f0; 
        color: #059669; 
        border-color: #10b981; 
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2); 
    }
    .radio-pill.alpha.checked { 
        background: #fef2f2; 
        color: #dc2626; 
        border-color: #ef4444; 
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2); 
    }
    .radio-pill.hadir:not(.checked):hover {
        border-color: #10b981;
        color: #059669;
        background: #f0fdf4;
    }
    .radio-pill.alpha:not(.checked):hover {
        border-color: #ef4444;
        color: #dc2626;
        background: #fef2f2;
    }

    .tr-alpha {
        background-color: #fff5f5 !important;
        transition: background-color 0.2s ease;
    }

    .badge-piket-locked {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 8px;
        white-space: nowrap;
    }
    .badge-piket-locked.status-saki { background: #e0f2fe; color: #0284c7; border: 1.5px solid #7dd3fc; }
    .badge-piket-locked.status-izin { background: #fff7ed; color: #ea580c; border: 1.5px solid #fdba74; }
    .badge-piket-locked.status-disp { background: #fdf4ff; color: #9333ea; border: 1.5px solid #d8b4fe; }

    .presensi-summary-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
    }
    .summary-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .summary-chip.chip-hadir {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .summary-chip.chip-alpha {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .summary-chip.chip-piket {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }
    .summary-chip.chip-total {
        background: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .badge-piket {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        margin-top: 4px;
    }
    .badge-piket.status-saki { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-piket.status-izin { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .badge-piket.status-disp { background: #fdf4ff; color: #7e22ce; border: 1px solid #f5d0fe; }
    .badge-piket.status-terlambat { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

    .badge-piket-locked {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 8px;
    }
    .badge-piket-locked.status-saki { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-piket-locked.status-izin { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .badge-piket-locked.status-disp { background: #fdf4ff; color: #7e22ce; border: 1px solid #f5d0fe; }
    .badge-piket-locked.status-terlambat { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-piket-locked.status-alpha { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

    .piket-alert-box {
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-quick {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #1b2559;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-quick:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-submit {
        background: #2b43b9;
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(43, 67, 185, 0.25);
        transition: all 0.2s ease;
    }
    .btn-submit:hover {
        background: #1e35a0;
        transform: translateY(-2px);
    }

    @media (max-width: 1024px) {
        .two-cols-layout { grid-template-columns: 1fr; }
    }

    @media (max-width: 576px) {
        .form-card { padding: 16px; border-radius: 14px; }
        .form-row { grid-template-columns: 1fr; gap: 10px; }
        .radio-group { flex-wrap: wrap; gap: 4px; }
        .radio-pill { padding: 4px 6px; font-size: 10px; }
        .btn-submit { width: 100%; justify-content: center; }
    }
</style>
@endsection

@section('content')
<form action="{{ route('guru-mapel.jurnal.store') }}" method="POST">
    @csrf

    <div class="two-cols-layout">
        <!-- Left: Identitas Sesi Pembelajaran & Materi -->
        <div class="form-card">
            <div class="form-card-head">
                <i class="fa-solid fa-book-open" style="font-size: 18px; color: #2b43b9;"></i>
                <h3>Identitas Pembelajaran & Materi</h3>
            </div>

            <!-- Active Izin Notification Banner -->
            @if(isset($activeIzinHariIni) && $activeIzinHariIni)
            <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1.5px solid #93c5fd; border-radius: 12px; padding: 14px 16px; margin-bottom: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 10px;">
                    <i class="fa-solid fa-circle-info" style="color: #2563eb; font-size: 18px; margin-top: 2px;"></i>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                            <strong style="color: #1e40af; font-size: 13.5px;">
                                Anda Sedang Izin Hari Ini ({{ str_replace('_', ' ', $activeIzinHariIni->jenis_izin) }})
                            </strong>
                            <span style="background: #2563eb; color: #ffffff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 6px;">
                                {{ $activeIzinHariIni->status }}
                            </span>
                        </div>
                        <p style="font-size: 12px; color: #1e3a8a; margin: 0; line-height: 1.5;">
                            Anda tetap mengisi jurnal mengajar & penugasan mandiri siswa di kelas yang Anda ampu. Guru Piket akan mengawasi kelas dan menyampaikan tugas yang telah Anda titipkan.
                        </p>
                        @if($activeIzinHariIni->menitipkan_tugas && $activeIzinHariIni->keterangan_tugas)
                        <div style="margin-top: 10px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <button type="button" onclick="isiMateriDariTugas({{ json_encode($activeIzinHariIni->keterangan_tugas) }})" style="padding: 6px 12px; font-size: 11.5px; font-weight: 700; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-copy"></i> Salin Instruksi Tugas ke Kolom Materi
                            </button>
                            <span style="font-size: 11px; color: #2563eb;">(Tugas telah dititipkan kepada Guru Piket)</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if(isset($isMaju) && $isMaju)
            @php
                $hariIniIndo = \Carbon\Carbon::parse($tanggal)->translatedFormat('l');
                $pembiasaanNama = strtolower($hariIniIndo) === 'senin' ? 'Upacara' : 'Pembiasaan';
            @endphp
            <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #fef08a; color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div style="font-size: 12.5px; color: #92400e;">
                    <strong>Penyesuaian Jam KBM Aktif:</strong> Hari ini {{ $pembiasaanNama }} ditiadakan sehingga KBM dimulai pukul <strong>07:00 WIB</strong> (Jam istirahat tetap sama). Kolom Jam Mulai & Selesai di bawah otomatis menggunakan waktu yang disesuaikan.
                </div>
            </div>
            @endif

            <!-- Shortcut Jadwal Hari Ini -->
            @if($jadwalHariIniOptions->isNotEmpty())
            <div class="form-group" style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <label style="color: #2b43b9; font-weight: 800;">
                    <i class="fa-solid fa-bolt"></i> Pilih Cepat dari Jadwal Hari Ini:
                </label>
                <select class="form-control" onchange="if(this.value) window.location.href='{{ route('guru-mapel.jurnal.create') }}?id_jadwal='+this.value">
                    <option value="">-- Pilih Sesi Terjadwal Hari Ini --</option>
                    @foreach($jadwalHariIniOptions as $jho)
                        <option value="{{ $jho->id_jadwal }}" {{ ($selectedJadwal && in_array($selectedJadwal->id_jadwal, $jho->jadwal_ids ?? [$jho->id_jadwal])) ? 'selected' : '' }}>
                            Jam ke-{{ $jho->jam_ke }} &bull; {{ $jho->kelas->nama_kelas }} &bull; {{ $jho->mapel->nama_mapel }}
                            @if(isset($jho->total_jp) && $jho->total_jp > 1)
                                ({{ $jho->total_jp }} JP)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($selectedJadwal)
                <input type="hidden" name="id_jadwal" value="{{ $selectedJadwal->id_jadwal }}">
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label>Pilih Kelas <span style="color:#ef4444;">*</span></label>
                    <select name="id_kelas" class="form-control" required onchange="window.location.href='{{ route('guru-mapel.jurnal.create') }}?id_kelas='+this.value+'&id_mapel={{ $idMapel }}'">
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id_kelas }}" {{ $idKelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->jurusan ?? 'Umum' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                    <select name="id_mapel" class="form-control" required>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id_mapel }}" {{ $idMapel == $m->id_mapel ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Tanggal Mengajar <span style="color:#ef4444;">*</span></span>
                        <span style="font-size: 11px; font-weight: 700; color: #475569; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <i class="fa-solid fa-lock" style="font-size: 10px; color: #64748b;"></i> Terkunci Otomatis
                        </span>
                    </label>
                    <div style="position: relative;">
                        <input type="date" name="tanggal" value="{{ $today }}" class="form-control" readonly required tabindex="-1" style="background-color: #f8fafc; cursor: not-allowed; color: #334155; font-weight: 700; border-color: #cbd5e1; pointer-events: none;">
                        <div style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; font-size: 13px;">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                    </div>
                    <small style="font-size: 11px; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-shield-halved" style="color: #2b43b9;"></i> Tanggal otomatis hari ini demi integritas validitas KBM.
                    </small>
                </div>

                <div class="form-group">
                    <label>Jam Pelajaran Ke- <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="jam_ke" value="{{ old('jam_ke', $jamKeSuggestion ?? ($selectedJadwal->jam_ke ?? '1-2')) }}" class="form-control" placeholder="Contoh: 1-2 atau 5-6" required>
                    @if(isset($jamKeSuggestion) && str_contains($jamKeSuggestion, '-'))
                        <small style="font-size: 11px; color: #2b43b9; margin-top: 3px; display: block;">
                            <i class="fa-solid fa-circle-check"></i> Otomatis mencakup jam pembelajaran berurutan ({{ $jamKeSuggestion }}).
                        </small>
                    @endif
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jam Mulai (WIB)</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $jamMulaiSuggestion ? Carbon\Carbon::parse($jamMulaiSuggestion)->format('H:i') : ($selectedJadwal ? Carbon\Carbon::parse($selectedJadwal->jam_mulai)->format('H:i') : now()->format('H:i'))) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>Jam Selesai (WIB)</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $jamSelesaiSuggestion ? Carbon\Carbon::parse($jamSelesaiSuggestion)->format('H:i') : ($selectedJadwal ? Carbon\Carbon::parse($selectedJadwal->jam_selesai)->format('H:i') : now()->addHours(2)->format('H:i'))) }}" class="form-control">
                </div>
            </div>

            @php
                $defaultStatus = 'Hadir';
                if (isset($activeIzinHariIni) && $activeIzinHariIni) {
                    if ($activeIzinHariIni->jenis_izin === 'Sakit') {
                        $defaultStatus = 'Sakit';
                    } elseif ($activeIzinHariIni->jenis_izin === 'Dinas_Luar') {
                        $defaultStatus = 'Dinas';
                    } else {
                        $defaultStatus = 'Izin';
                    }
                }
                $selectedStatus = old('status_guru', $defaultStatus);
            @endphp

            <div class="form-group">
                <label>Status Kehadiran Guru <span style="color:#ef4444;">*</span></label>
                <select name="status_guru" class="form-control" required>
                    <option value="Hadir" {{ $selectedStatus === 'Hadir' ? 'selected' : '' }}>Hadir Mengajar di Kelas</option>
                    <option value="Izin" {{ $selectedStatus === 'Izin' ? 'selected' : '' }}>Izin (Penugasan Mandiri)</option>
                    <option value="Sakit" {{ $selectedStatus === 'Sakit' ? 'selected' : '' }}>Sakit (Penugasan Mandiri)</option>
                    <option value="Dinas" {{ $selectedStatus === 'Dinas' ? 'selected' : '' }}>Dinas Luar (Tugas Kedinasan)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Capaian / Ringkasan Materi Pokok <span style="color:#ef4444;">*</span></label>
                <textarea name="materi" rows="4" class="form-control" placeholder="Tuliskan pokok materi pembelajaran yang disampaikan kepada siswa..." required></textarea>
            </div>

            <div class="form-group">
                <label>Catatan Khusus KBM / Hambatan (Opsional)</label>
                <textarea name="catatan" rows="3" class="form-control" placeholder="Catatan keaktifan siswa, penugasan PR, kendala proyektor, dsb..."></textarea>
            </div>
        </div>

        <!-- Right: Presensi Siswa Per Rombel -->
        <div class="form-card">
            <div class="form-card-head" style="justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-users-viewfinder" style="font-size: 18px; color: #2b43b9;"></i>
                    <div>
                        <h3 style="margin: 0;">Presensi Siswa ({{ $siswaList->count() }} Siswa)</h3>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <input type="text" id="searchSiswaInput" placeholder="Cari siswa..." onkeyup="filterSiswa()" style="padding: 6px 10px 6px 28px; font-size: 12px; border-radius: 8px; border: 1px solid #cbd5e1; width: 140px; outline: none;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #94a3b8;"></i>
                    </div>
                    <button type="button" class="btn-quick" onclick="setAllAttendance('Hadir')">
                        <i class="fa-solid fa-check-double" style="color: #10b981;"></i> Semua Hadir
                    </button>
                </div>
            </div>

            <!-- Presensi Live Summary Bar -->
            <div class="presensi-summary-bar">
                <span class="summary-chip chip-total">
                    <i class="fa-solid fa-users"></i> Total: <strong>{{ $siswaList->count() }}</strong>
                </span>
                <span class="summary-chip chip-hadir">
                    <i class="fa-solid fa-circle-check"></i> Hadir: <strong id="statHadirCount">0</strong>
                </span>
                <span class="summary-chip chip-alpha">
                    <i class="fa-solid fa-circle-xmark"></i> Alpha: <strong id="statAlphaCount">0</strong>
                </span>
                @if(isset($piketAbsenceCount) && $piketAbsenceCount > 0)
                <span class="summary-chip chip-piket">
                    <i class="fa-solid fa-shield-halved"></i> Piket: <strong id="statPiketCount">{{ $piketAbsenceCount }}</strong>
                </span>
                @endif
            </div>

            @if(isset($piketAbsenceCount) && $piketAbsenceCount > 0)
            <div class="piket-alert-box">
                <i class="fa-solid fa-bell-concierge" style="font-size: 20px; color: #16a34a; flex-shrink: 0;"></i>
                <div style="font-size: 12px; color: #166534; line-height: 1.45;">
                    <strong>Informasi Guru Piket:</strong> Terdapat <strong>{{ $piketAbsenceCount }} siswa</strong> yang berhalangan hadir dan telah dicatat oleh Guru Piket ({{ !empty($piketDetails['sakit']) ? $piketDetails['sakit'] . ' Sakit' : '' }}{{ !empty($piketDetails['sakit']) && (!empty($piketDetails['izin']) || !empty($piketDetails['dispensasi'])) ? ', ' : '' }}{{ !empty($piketDetails['izin']) ? $piketDetails['izin'] . ' Izin' : '' }}{{ !empty($piketDetails['izin']) && !empty($piketDetails['dispensasi']) ? ', ' : '' }}{{ !empty($piketDetails['dispensasi']) ? $piketDetails['dispensasi'] . ' Dispen' : '' }}). Status kehadiran mereka otomatis terkunci dengan izin resmi. Guru Mapel hanya perlu menandai siswa yang <strong>Alpha</strong> jika ada.
                </div>
            </div>
            @else
            <div style="font-size: 12px; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 12px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-info" style="color: #2b43b9;"></i>
                <span><strong>SOP Presensi:</strong> Siswa Sakit, Izin, & Dispensasi dicatat terpusat oleh Guru Piket. Guru Pengajar hanya mengabsen siswa <strong>Hadir</strong> dan <strong>Alpha</strong>.</span>
            </div>
            @endif

            <div class="table-responsive">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th>Nama Siswa & NISN</th>
                            <th style="min-width: 170px;">Status Kehadiran</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $idx => $s)
                        <tr data-from-piket="{{ !empty($s->piket_status) ? 'true' : 'false' }}">
                            <td style="color: #707e94; font-weight: 700; text-align: center;">{{ $idx + 1 }}</td>
                            <td class="nama-siswa-cell">
                                <strong style="color: #1b2559; font-size: 13px;">{{ $s->nama_lengkap }}</strong>
                                <div style="font-size: 11px; color: #707e94;">NISN: {{ $s->nisn ?? '-' }}</div>
                            </td>
                            <td>
                                @if(!empty($s->piket_status))
                                    @php
                                        $badgeClass = match($s->piket_status) {
                                            'Sakit' => 'status-saki',
                                            'Izin' => 'status-izin',
                                            'Terlambat' => 'status-terlambat',
                                            'Dispensasi' => 'status-disp',
                                            default => 'status-alpha'
                                        };
                                        $badgeIcon = match($s->piket_status) {
                                            'Sakit' => 'fa-notes-medical',
                                            'Izin' => 'fa-envelope-open-text',
                                            'Terlambat' => 'fa-clock-rotate-left',
                                            'Dispensasi' => 'fa-ticket-simple',
                                            default => 'fa-clock'
                                        };
                                        $piketLabel = $s->piket_status . ' (Piket)';
                                        if ($s->piket_status === 'Terlambat' && $s->piket_jam_ke_mulai) {
                                            $piketLabel = 'Terlambat (Jam Ke-' . $s->piket_jam_ke_mulai . ')';
                                        }
                                    @endphp
                                    {{-- Terkunci dari Catatan Guru Piket --}}
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="badge-piket-locked {{ $badgeClass }}" title="{{ $s->piket_keterangan ?: 'Tercatat resmi oleh Guru Piket' }}">
                                            <i class="fa-solid {{ $badgeIcon }}"></i> {{ $piketLabel }}
                                            <i class="fa-solid fa-lock" style="font-size: 10px; opacity: 0.65; margin-left: 2px;"></i>
                                        </span>
                                        <input type="hidden" name="presensi[{{ $s->id_siswa }}]" value="{{ $s->piket_status }}">
                                    </div>
                                @else
                                    {{-- Guru Mapel hanya mengabsen Hadir atau Alpha --}}
                                    <div class="radio-group">
                                        <label class="radio-pill hadir checked" onclick="selectRadio(this)" title="Hadir Mengikuti KBM">
                                            <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Hadir" checked>
                                            <i class="fa-solid fa-check"></i>
                                            <span>Hadir</span>
                                        </label>
                                        <label class="radio-pill alpha" onclick="selectRadio(this)" title="Alpha / Tidak Hadir Tanpa Keterangan">
                                            <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Alpha">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>Alpha</span>
                                        </label>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if(!empty($s->piket_status))
                                    <div style="font-size: 12px; color: #475569; font-style: italic; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px dashed #cbd5e1; display: flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-clipboard-check" style="color: #10b981;"></i>
                                        <span>{{ $s->piket_keterangan ?: 'Dicatat oleh Guru Piket' }}</span>
                                    </div>
                                    <input type="hidden" name="keterangan[{{ $s->id_siswa }}]" value="(Piket: {{ $s->piket_status }}) {{ $s->piket_keterangan }}">
                                @else
                                    <input type="text" 
                                           name="keterangan[{{ $s->id_siswa }}]" 
                                           placeholder="Catatan jika alpha / kendala..." 
                                           class="form-control" 
                                           style="padding: 6px 10px; font-size: 12px; border-radius: 8px;">
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #707e94; padding: 36px;">
                                <i class="fa-solid fa-user-group" style="font-size: 28px; margin-bottom: 6px; color: #cbd5e1;"></i>
                                <div>Belum ada data siswa di kelas yang dipilih.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Jurnal & Presensi Siswa
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function selectRadio(label) {
        const parent = label.closest('.radio-group');
        parent.querySelectorAll('.radio-pill').forEach(el => el.classList.remove('checked'));
        label.classList.add('checked');
        const input = label.querySelector('input');
        if (input) {
            input.checked = true;
            const tr = label.closest('tr');
            if (input.value === 'Alpha') {
                tr.classList.add('tr-alpha');
            } else {
                tr.classList.remove('tr-alpha');
            }
        }
        updateAttendanceStats();
    }

    function setAllAttendance(status) {
        document.querySelectorAll('.attendance-table tbody tr').forEach(row => {
            // Jangan timpa siswa yang sudah tercatat izin dari piket saat menekan tombol Semua Hadir
            if (row.getAttribute('data-from-piket') === 'true') {
                return;
            }

            const radio = row.querySelector(`input[value="${status}"]`);
            if (radio) {
                radio.checked = true;
                const parent = radio.closest('.radio-group');
                parent.querySelectorAll('.radio-pill').forEach(el => el.classList.remove('checked'));
                radio.closest('.radio-pill').classList.add('checked');
                if (status === 'Alpha') {
                    row.classList.add('tr-alpha');
                } else {
                    row.classList.remove('tr-alpha');
                }
            }
        });
        updateAttendanceStats();
    }

    function updateAttendanceStats() {
        let hadir = 0;
        let alpha = 0;
        let piket = 0;

        document.querySelectorAll('.attendance-table tbody tr').forEach(row => {
            if (row.getAttribute('data-from-piket') === 'true') {
                piket++;
            } else {
                const checked = row.querySelector('input[type="radio"]:checked');
                if (checked && checked.value === 'Alpha') {
                    alpha++;
                } else {
                    hadir++;
                }
            }
        });

        const elHadir = document.getElementById('statHadirCount');
        const elAlpha = document.getElementById('statAlphaCount');
        const elPiket = document.getElementById('statPiketCount');
        if (elHadir) elHadir.innerText = hadir;
        if (elAlpha) elAlpha.innerText = alpha;
        if (elPiket) elPiket.innerText = piket;
    }

    function filterSiswa() {
        const query = (document.getElementById('searchSiswaInput')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.attendance-table tbody tr').forEach(row => {
            const cell = row.querySelector('.nama-siswa-cell');
            if (!cell) return;
            const text = cell.innerText.toLowerCase();
            if (text.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function isiMateriDariTugas(tugasText) {
        const materiArea = document.querySelector('textarea[name="materi"]');
        if (materiArea) {
            materiArea.value = tugasText;
            materiArea.focus();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateAttendanceStats();
    });
</script>
@endsection
