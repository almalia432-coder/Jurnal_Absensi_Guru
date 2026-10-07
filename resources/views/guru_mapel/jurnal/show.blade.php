@extends('layouts.guru_mapel')

@section('title', 'Detail Jurnal Mengajar - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Detail Jurnal Pembelajaran')
@section('header_subtitle', 'Rincian materi pembelajaran dan status presensi siswa pada sesi ini')

@section('header_extra')
    <a href="{{ route('guru-mapel.jurnal.riwayat') }}" class="btn-action-secondary" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; box-shadow: none;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
    </a>
@endsection

@section('styles')
<style>
    .two-cols-layout {
        display: grid;
        grid-template-columns: 1.1fr 1.3fr;
        gap: 24px;
    }

    .detail-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
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
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 13.5px;
        border-bottom: 1px dashed #f1f5f9;
        padding-bottom: 10px;
    }

    .info-item .label {
        color: #707e94;
        font-weight: 600;
    }

    .info-item .val {
        color: #1b2559;
        font-weight: 700;
        text-align: right;
    }

    .materi-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        font-size: 13.5px;
        line-height: 1.6;
        color: #2b3674;
        margin-top: 14px;
    }

    /* Table */
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }
    .custom-table th {
        background-color: #f8fafc;
        padding: 10px 12px;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        color: #707e94;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .custom-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f4f7fe;
        color: #2b3674;
        vertical-align: middle;
    }

    .status-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-badge.hadir      { background: #e6f9f0; color: #10b981; }
    .status-badge.sakit      { background: #e0f2fe; color: #0369a1; }
    .status-badge.izin       { background: #fff7ed; color: #f97316; }
    .status-badge.dinas      { background: #f3e8ff; color: #7e22ce; }
    .status-badge.alpha      { background: #fef2f2; color: #ef4444; }
    .status-badge.dispensasi { background: #eef2ff; color: #2b43b9; }
    .status-badge.terlambat  { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

    @media (max-width: 1024px) {
        .two-cols-layout { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="two-cols-layout">
    <!-- Left: Data Sesi Jurnal -->
    <div class="detail-card">
        <div class="card-head">
            <i class="fa-solid fa-file-signature" style="color: #2b43b9; font-size: 18px;"></i>
            <h3>Data Sesi Pembelajaran</h3>
        </div>

        <div class="info-list">
            <div class="info-item">
                <span class="label">Tanggal Pelaksanaan</span>
                <span class="val">{{ Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Kelas & Rombel</span>
                <span class="val">{{ $jurnal->kelas->nama_kelas ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Mata Pelajaran</span>
                <span class="val">{{ $jurnal->mapel->nama_mapel ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Jam Pelajaran</span>
                <span class="val">Jam ke-{{ $jurnal->jam_ke }} ({{ $jurnal->jam_mulai ? Carbon\Carbon::parse($jurnal->jam_mulai)->format('H:i') : '-' }} - {{ $jurnal->jam_selesai ? Carbon\Carbon::parse($jurnal->jam_selesai)->format('H:i') : '-' }} WIB)</span>
            </div>
            <div class="info-item">
                <span class="label">Status Kehadiran Guru</span>
                <span class="val"><span class="status-badge {{ strtolower($jurnal->status_guru) }}">{{ $jurnal->status_guru }}</span></span>
            </div>
            <div class="info-item">
                <span class="label">Statistik Siswa</span>
                <span class="val">
                    <strong style="color: #10b981;">{{ $jurnal->jumlah_siswa_hadir }} Hadir</strong> / 
                    <strong style="color: #ef4444;">{{ $jurnal->jumlah_siswa_tidak_hadir }} Absen</strong>
                </span>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #707e94;">Capaian / Materi Pembelajaran:</span>
            <div class="materi-box">
                {{ $jurnal->materi }}
            </div>
        </div>

        @if($jurnal->catatan)
        <div style="margin-top: 14px;">
            <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #707e94;">Catatan Tambahan:</span>
            <div class="materi-box" style="background: #fffbeb; border-color: #fef3c7;">
                {{ $jurnal->catatan }}
            </div>
        </div>
        @endif
    </div>

    <!-- Right: Daftar Presensi Siswa -->
    <div class="detail-card">
        <div class="card-head" style="justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-clipboard-user" style="color: #2b43b9; font-size: 18px;"></i>
                <h3>Daftar Presensi Siswa ({{ $jurnal->presensiSiswa->count() }})</h3>
            </div>
        </div>

        <div style="overflow-x: auto; max-height: 520px;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnal->presensiSiswa as $idx => $ps)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <strong style="color: #1b2559;">{{ $ps->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                            <div style="font-size: 11px; color: #707e94;">NISN: {{ $ps->siswa->nisn ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="status-badge {{ strtolower($ps->status) }}">{{ $ps->status }}</span>
                        </td>
                        <td>
                            <span style="font-size: 12px; color: #64748b;">{{ $ps->keterangan ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #707e94; padding: 32px;">
                            Tidak ada rincian presensi siswa tersimpan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
