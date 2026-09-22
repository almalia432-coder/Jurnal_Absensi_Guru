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
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        user-select: none;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #64748b;
        transition: all 0.15s ease;
    }

    .radio-pill input { display: none; }

    .radio-pill.hadir.checked { background: #e6f9f0; color: #10b981; border-color: #10b981; }
    .radio-pill.sakit.checked { background: #e0f2fe; color: #0369a1; border-color: #0369a1; }
    .radio-pill.izin.checked  { background: #fff7ed; color: #f97316; border-color: #f97316; }
    .radio-pill.alpha.checked { background: #fef2f2; color: #ef4444; border-color: #ef4444; }
    .radio-pill.disp.checked  { background: #eef2ff; color: #2b43b9; border-color: #2b43b9; }

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
            <div class="form-card-head" style="justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-users-viewfinder" style="font-size: 18px; color: #2b43b9;"></i>
                    <div>
                        <h3>Presensi Siswa ({{ $siswaList->count() }} Siswa)</h3>
                    </div>
                </div>

                <div style="display: flex; gap: 6px;">
                    <button type="button" class="btn-quick" onclick="setAllAttendance('Hadir')">
                        <i class="fa-solid fa-check-double" style="color: #10b981;"></i> Semua Hadir
                    </button>
                </div>
            </div>

            @if(isset($piketAbsenceCount) && $piketAbsenceCount > 0)
            <div class="piket-alert-box">
                <i class="fa-solid fa-bell-concierge" style="font-size: 20px; color: #16a34a; flex-shrink: 0;"></i>
                <div style="font-size: 12.5px; color: #166534; line-height: 1.45;">
                    <strong>Informasi Guru Piket:</strong> Terdapat <strong>{{ $piketAbsenceCount }} siswa</strong> di kelas ini yang berhalangan hadir dan telah dicatat oleh Guru Piket ({{ !empty($piketDetails['sakit']) ? $piketDetails['sakit'] . ' Sakit' : '' }}{{ !empty($piketDetails['sakit']) && (!empty($piketDetails['izin']) || !empty($piketDetails['dispensasi'])) ? ', ' : '' }}{{ !empty($piketDetails['izin']) ? $piketDetails['izin'] . ' Izin' : '' }}{{ !empty($piketDetails['izin']) && !empty($piketDetails['dispensasi']) ? ', ' : '' }}{{ !empty($piketDetails['dispensasi']) ? $piketDetails['dispensasi'] . ' Dispen' : '' }}). Status kehadiran mereka telah otomatis disetel. Anda tinggal menandai siswa yang <strong>Alpha</strong> jika ada.
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nama Siswa & NISN</th>
                            <th>Status Kehadiran</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $idx => $s)
                        @php
                            $currStatus = $s->piket_status ?? 'Hadir';
                        @endphp
                        <tr data-from-piket="{{ !empty($s->piket_status) ? 'true' : 'false' }}">
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong style="color: #1b2559; font-size: 13px;">{{ $s->nama_lengkap }}</strong>
                                <div style="font-size: 11px; color: #707e94;">NISN: {{ $s->nisn ?? '-' }}</div>
                                @if(!empty($s->piket_status))
                                    @php
                                        $badgeClass = match($s->piket_status) {
                                            'Sakit' => 'status-saki',
                                            'Izin' => 'status-izin',
                                            default => 'status-disp'
                                        };
                                        $badgeIcon = match($s->piket_status) {
                                            'Sakit' => 'fa-notes-medical',
                                            'Izin' => 'fa-envelope-open-text',
                                            default => 'fa-ticket-simple'
                                        };
                                    @endphp
                                    <div>
                                        <span class="badge-piket {{ $badgeClass }}">
                                            <i class="fa-solid {{ $badgeIcon }}"></i> Piket: {{ $s->piket_status }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="radio-group">
                                    <label class="radio-pill hadir {{ $currStatus === 'Hadir' ? 'checked' : '' }}" onclick="selectRadio(this)">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Hadir" {{ $currStatus === 'Hadir' ? 'checked' : '' }}>
                                        <span>H</span>
                                    </label>
                                    <label class="radio-pill sakit {{ $currStatus === 'Sakit' ? 'checked' : '' }}" onclick="selectRadio(this)">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Sakit" {{ $currStatus === 'Sakit' ? 'checked' : '' }}>
                                        <span>S</span>
                                    </label>
                                    <label class="radio-pill izin {{ $currStatus === 'Izin' ? 'checked' : '' }}" onclick="selectRadio(this)">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Izin" {{ $currStatus === 'Izin' ? 'checked' : '' }}>
                                        <span>I</span>
                                    </label>
                                    <label class="radio-pill alpha {{ $currStatus === 'Alpha' ? 'checked' : '' }}" onclick="selectRadio(this)">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Alpha" {{ $currStatus === 'Alpha' ? 'checked' : '' }}>
                                        <span>A</span>
                                    </label>
                                    <label class="radio-pill disp {{ $currStatus === 'Dispensasi' ? 'checked' : '' }}" onclick="selectRadio(this)">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Dispensasi" {{ $currStatus === 'Dispensasi' ? 'checked' : '' }}>
                                        <span>D</span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="text" 
                                       name="keterangan[{{ $s->id_siswa }}]" 
                                       placeholder="Catatan..." 
                                       value="{{ !empty($s->piket_keterangan) ? '(Piket: ' . $s->piket_status . ') ' . $s->piket_keterangan : '' }}"
                                       class="form-control" 
                                       style="padding: 4px 8px; font-size: 12px; border-radius: 8px;">
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
        if (input) input.checked = true;
    }

    function setAllAttendance(status) {
        document.querySelectorAll('.attendance-table tbody tr').forEach(row => {
            // Jangan timpa siswa yang sudah tercatat izin dari piket saat menekan tombol Semua Hadir
            if (status === 'Hadir' && row.getAttribute('data-from-piket') === 'true') {
                return;
            }

            const radio = row.querySelector(`input[value="${status}"]`);
            if (radio) {
                radio.checked = true;
                const parent = radio.closest('.radio-group');
                parent.querySelectorAll('.radio-pill').forEach(el => el.classList.remove('checked'));
                radio.closest('.radio-pill').classList.add('checked');
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
</script>
@endsection
