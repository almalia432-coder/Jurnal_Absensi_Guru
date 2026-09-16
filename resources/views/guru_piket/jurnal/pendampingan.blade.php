@extends('layouts.guru_piket')

@section('title', 'Jurnal Pendampingan Piket - SMKN 1 BOYOLANGU')
@section('header_title', 'Jurnal Pendampingan Kelas Terdampak')
@section('header_subtitle', 'Pengisian jurnal dan presensi kehadiran siswa kelas kosong yang didampingi oleh Guru Piket')

@section('styles')
<style>
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        color: #1b2559;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s ease;
        margin-bottom: 20px;
    }
    .back-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #2b43b9;
    }

    .info-banner {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1.5px solid #93c5fd;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .info-badge {
        background: #ffffff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #1e40af;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .two-cols-layout {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .two-cols-layout {
            grid-template-columns: 1fr;
        }
    }

    .form-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
    }

    .card-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .card-head h3 {
        font-size: 16px;
        font-weight: 800;
        color: #1b2559;
        margin: 0;
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
        transition: border-color 0.2s;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #2b43b9;
        box-shadow: 0 0 0 3px rgba(43, 67, 185, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    /* Table Presensi Siswa */
    .table-container {
        max-height: 480px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }

    .custom-table th {
        background: #f8fafc;
        padding: 12px 14px;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        color: #707e94;
        border-bottom: 1.5px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .custom-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1b2559;
        vertical-align: middle;
    }

    .custom-table tr:hover td {
        background-color: #f8fafc;
    }

    /* Radio buttons */
    .radio-pill-group {
        display: inline-flex;
        gap: 4px;
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

    .btn-submit {
        background: linear-gradient(135deg, #2b43b9 0%, #1e2f8a 100%);
        color: #ffffff;
        font-weight: 800;
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(43, 67, 185, 0.25);
        transition: all 0.2s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(43, 67, 185, 0.35);
    }
</style>
@endsection

@section('content')

<a href="{{ route('guru-piket.izin-guru', ['tanggal' => $tanggal]) }}" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i> Kembali ke Monitoring Izin Guru
</a>

<!-- Banner Kelas Terdampak -->
<div class="info-banner">
    <div>
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #1d4ed8; margin-bottom: 4px;">
            <i class="fa-solid fa-shield-halved"></i> Pendampingan Guru Piket
        </div>
        <h2 style="font-size: 20px; font-weight: 800; color: #1e3a8a; margin: 0 0 6px 0;">
            Kelas {{ $jadwal->kelas->nama_kelas }} &bull; Jam ke-{{ $jadwal->jam_ke }}
        </h2>
        <div style="font-size: 13px; color: #1e40af;">
            Mata Pelajaran: <strong>{{ $jadwal->mapel->nama_mapel }}</strong> | Guru Pengampu: <strong>{{ $jadwal->guru->nama_lengkap }}</strong>
        </div>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <span class="info-badge">
            <i class="fa-solid fa-calendar-day"></i> {{ Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}
        </span>
        <span class="info-badge" style="background: #fff7ed; border-color: #fed7aa; color: #c2410c;">
            <i class="fa-solid fa-user-clock"></i> Status Guru: {{ $izinGuru ? $izinGuru->jenis_izin : 'Izin' }} (Berhalangan Hadir)
        </span>
    </div>
</div>

<form action="{{ route('guru-piket.jurnal.pendampingan.store', $jadwal->id_jadwal) }}" method="POST">
    @csrf
    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

    <div class="two-cols-layout">
        <!-- Kolom Kiri: Rincian Sesi KBM -->
        <div class="form-card">
            <div class="card-head">
                <i class="fa-solid fa-file-pen" style="color: #2b43b9; font-size: 18px;"></i>
                <h3>Rincian Jurnal Pendampingan</h3>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jam Pelajaran Ke- <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="jam_ke" value="{{ old('jam_ke', $existingJurnal->jam_ke ?? $jadwal->jam_ke) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal <span style="color:#ef4444;">*</span></label>
                    <input type="date" value="{{ $tanggal }}" class="form-control" readonly style="background:#f8fafc; cursor:not-allowed;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jam Mulai (WIB)</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $existingJurnal->jam_mulai ? Carbon\Carbon::parse($existingJurnal->jam_mulai)->format('H:i') : ($jadwal->jam_mulai ? Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '07:00')) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Jam Selesai (WIB)</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $existingJurnal->jam_selesai ? Carbon\Carbon::parse($existingJurnal->jam_selesai)->format('H:i') : ($jadwal->jam_selesai ? Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '09:40')) }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Status Kehadiran Guru Pengampu</label>
                <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; font-weight: 700; color: #475569; font-size: 13px;">
                    <i class="fa-solid fa-user-xmark" style="color: #ef4444; margin-right: 6px;"></i>
                    {{ $izinGuru ? $izinGuru->jenis_izin : 'Izin' }} (Didampingi Guru Piket)
                </div>
            </div>

            <div class="form-group">
                <label>Aktivitas / Capaian Pembelajaran Siswa <span style="color:#ef4444;">*</span></label>
                <textarea name="materi" rows="4" class="form-control" placeholder="Tuliskan aktivitas pengerjaan tugas atau literasi mandiri yang dilakukan siswa selama jam pendampingan..." required>{{ old('materi', $existingJurnal->materi ?? 'Pendampingan Belajar Mandiri / Literasi di kelas oleh Guru Piket (Guru Pengampu berhalangan hadir tanpa titipan tugas).') }}</textarea>
            </div>

            <div class="form-group">
                <label>Catatan Khusus Kejadian di Kelas (Opsional)</label>
                <textarea name="catatan" rows="3" class="form-control" placeholder="Catatan ketertiban kelas, siswa yang izin ke UKS, dsb...">{{ old('catatan', $existingJurnal ? str_replace(' | Didampingi oleh Guru Piket: ' . ($guruPiket->guru->nama_lengkap ?? Auth::user()->name), '', $existingJurnal->catatan) : '') }}</textarea>
            </div>
        </div>

        <!-- Kolom Kanan: Presensi Siswa Real-Time -->
        <div class="form-card">
            <div class="card-head" style="justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-clipboard-user" style="color: #2b43b9; font-size: 18px;"></i>
                    <div>
                        <h3>Presensi Siswa di Kelas</h3>
                        <div style="font-size: 11.5px; color: #64748b;">Total: {{ $siswaList->count() }} Siswa Terdaftar</div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="setAllAttendance('Hadir')" style="padding: 6px 12px; background: #e6f9f0; color: #10b981; border: 1px solid #10b981; border-radius: 8px; font-weight: 700; font-size: 11.5px; cursor: pointer;">
                        <i class="fa-solid fa-check-double"></i> Set Semua Hadir
                    </button>
                </div>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 35px;">No</th>
                            <th>Nama Siswa & NIS</th>
                            <th style="text-align: center;">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $idx => $s)
                        @php
                            $currStatus = $existingPresensi->has($s->id_siswa) ? $existingPresensi->get($s->id_siswa)->status : 'Hadir';
                        @endphp
                        <tr>
                            <td style="color: #707e94; font-weight: 700;">{{ $idx + 1 }}</td>
                            <td>
                                <strong style="color: #1b2559; font-size: 13px;">{{ $s->nama_lengkap }}</strong>
                                <div style="font-size: 11px; color: #6b7a99;">NIS: {{ $s->nis ?? '-' }}</div>
                            </td>
                            <td style="text-align: center;">
                                <div class="radio-pill-group">
                                    <label class="radio-pill hadir {{ $currStatus === 'Hadir' ? 'checked' : '' }}">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Hadir" {{ $currStatus === 'Hadir' ? 'checked' : '' }} onchange="handleRadioChange(this)"> H
                                    </label>
                                    <label class="radio-pill sakit {{ $currStatus === 'Sakit' ? 'checked' : '' }}">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Sakit" {{ $currStatus === 'Sakit' ? 'checked' : '' }} onchange="handleRadioChange(this)"> S
                                    </label>
                                    <label class="radio-pill izin {{ $currStatus === 'Izin' ? 'checked' : '' }}">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Izin" {{ $currStatus === 'Izin' ? 'checked' : '' }} onchange="handleRadioChange(this)"> I
                                    </label>
                                    <label class="radio-pill alpha {{ $currStatus === 'Alpha' ? 'checked' : '' }}">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Alpha" {{ $currStatus === 'Alpha' ? 'checked' : '' }} onchange="handleRadioChange(this)"> A
                                    </label>
                                    <label class="radio-pill disp {{ $currStatus === 'Dispensasi' ? 'checked' : '' }}">
                                        <input type="radio" name="presensi[{{ $s->id_siswa }}]" value="Dispensasi" {{ $currStatus === 'Dispensasi' ? 'checked' : '' }} onchange="handleRadioChange(this)"> D
                                    </label>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #707e94; padding: 24px;">
                                Belum ada data siswa di kelas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Jurnal & Presensi Piket
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function handleRadioChange(radio) {
        const group = radio.closest('.radio-pill-group');
        group.querySelectorAll('.radio-pill').forEach(pill => pill.classList.remove('checked'));
        if (radio.checked) {
            radio.closest('.radio-pill').classList.add('checked');
        }
    }

    function setAllAttendance(status) {
        document.querySelectorAll('.radio-pill-group').forEach(group => {
            const radio = group.querySelector(`input[value="${status}"]`);
            if (radio) {
                radio.checked = true;
                group.querySelectorAll('.radio-pill').forEach(pill => pill.classList.remove('checked'));
                radio.closest('.radio-pill').classList.add('checked');
            }
        });
    }
</script>

@endsection
