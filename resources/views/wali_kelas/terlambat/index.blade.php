@extends('layouts.wali_kelas')

@section('title', 'Rekap Siswa Terlambat - Kelas ' . ($kelas->nama_kelas ?? ''))
@section('header_title', 'Rekap Siswa Terlambat — ' . ($kelas->nama_kelas ?? ''))
@section('header_subtitle', 'Pantau frekuensi dan riwayat keterlambatan siswa binaan kelas Anda untuk pembinaan kedisiplinan')

@section('styles')
<style>
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .metric-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .metric-icon.blue   { background: #eef2ff; color: #2b43b9; }
    .metric-icon.orange { background: #fff7ed; color: #ea580c; }
    .metric-icon.purple { background: #f5f3ff; color: #7c3aed; }

    .section-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
        margin-bottom: 24px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }
    .custom-table th {
        background-color: #f8fafc;
        padding: 12px 14px;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        color: #707e94;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .custom-table td {
        padding: 14px;
        border-bottom: 1px solid #f4f7fe;
        color: #2b3674;
        vertical-align: middle;
    }
    .custom-table tr:hover td { background-color: #f8fafc; }

    .status-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-badge.disetujui { background: #e6f9f0; color: #10b981; }
    .status-badge.menunggu  { background: #fff7ed; color: #c2410c; }
    .status-badge.ditolak   { background: #fef2f2; color: #ef4444; }
    .status-badge.dibatalkan{ background: #f1f5f9; color: #64748b; }

    .filter-input {
        padding: 9px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        font-family: inherit;
        font-weight: 600;
        color: #1b2559;
        outline: none;
        background: #ffffff;
    }

    @media (max-width: 768px) {
        .metrics-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="content-container">

    {{-- Filter Card --}}
    <form action="{{ route('wali-kelas.terlambat') }}" method="GET" style="background: #ffffff; border-radius: 16px; padding: 18px 22px; border: 1px solid #eef2f7; box-shadow: 0 4px 14px rgba(0,0,0,0.03); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap;">
        <div>
            <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Pilih Periode Bulan</span>
            <input type="month" name="bulan" value="{{ $bulan }}" class="filter-input" onchange="this.form.submit()">
        </div>
        <div style="font-size: 13px; color: #64748b;">
            Kelas Binaan: <strong style="color: #2b43b9;">{{ $kelas->nama_kelas }}</strong> (Tingkat {{ $kelas->tingkat }})
        </div>
    </form>

    {{-- Metrics Grid --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon blue">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 800; color: #1b2559;">
                    {{ $riwayatTerlambat->total() }}
                </div>
                <div style="font-size: 12px; color: #64748b; font-weight: 600;">Total Kejadian Terlambat</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon orange">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 800; color: #1b2559;">
                    {{ $rekapPerSiswa->count() }}
                </div>
                <div style="font-size: 12px; color: #64748b; font-weight: 600;">Siswa Pernah Terlambat</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon purple">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 800; color: #1b2559;">
                    {{ $rekapPerSiswa->where('total_terlambat', '>=', 3)->count() }}
                </div>
                <div style="font-size: 12px; color: #64748b; font-weight: 600;">Perlu Perhatian Khusus (&ge; 3x)</div>
            </div>
        </div>
    </div>

    {{-- Tabel 1: Frekuensi Siswa Terlambat --}}
    <div class="section-card">
        <h3 style="font-size: 15px; font-weight: 800; color: #1b2559; margin: 0 0 16px 0;">
            <i class="fa-solid fa-chart-simple" style="color: #2b43b9; margin-right: 6px;"></i>
            Frekuensi Keterlambatan per Siswa (Bulan {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }})
        </h3>

        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Total Terlambat</th>
                        <th>Status Kedisiplinan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapPerSiswa as $idx => $r)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong style="color: #1e293b;">{{ $r->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                            </td>
                            <td>{{ $r->siswa->nisn ?? '-' }}</td>
                            <td>
                                <span style="font-weight: 800; font-size: 14px; color: {{ $r->total_terlambat >= 3 ? '#dc2626' : '#2b43b9' }};">
                                    {{ $r->total_terlambat }} kali
                                </span>
                            </td>
                            <td>
                                @if($r->total_terlambat >= 3)
                                    <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                                        <i class="fa-solid fa-circle-exclamation"></i> Perlu Konseling BK
                                    </span>
                                @elseif($r->total_terlambat == 2)
                                    <span style="background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                                        Peringatan Wali Kelas
                                    </span>
                                @else
                                    <span style="background: #f1f5f9; color: #475569; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                                        Keterlambatan Pertama
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">
                                Tidak ada siswa kelas binaan yang terlambat pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel 2: Riwayat Log Detail Keterlambatan --}}
    <div class="section-card">
        <h3 style="font-size: 15px; font-weight: 800; color: #1b2559; margin: 0 0 16px 0;">
            <i class="fa-solid fa-list" style="color: #2b43b9; margin-right: 6px;"></i>
            Log Riwayat Izin Masuk Siswa Terlambat
        </h3>

        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Waktu Tiba</th>
                        <th>Masuk Jam Ke</th>
                        <th>Alasan Keterlambatan</th>
                        <th>Nomor Surat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatTerlambat as $idx => $t)
                        <tr>
                            <td>{{ $riwayatTerlambat->firstItem() + $idx }}</td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($t->tanggal)->translatedFormat('d M Y') }}</strong>
                            </td>
                            <td>{{ $t->siswa->nama_lengkap ?? 'Siswa' }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->jam_masuk)->format('H:i') }} WIB</td>
                            <td>Jam Ke-{{ $t->jam_ke_mulai }}</td>
                            <td>
                                <div style="max-width: 250px; font-size: 12px; color: #475569;">
                                    {{ $t->alasan }}
                                </div>
                            </td>
                            <td>
                                @if($t->nomor_surat)
                                    <code>{{ $t->nomor_surat }}</code>
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status === 'Disetujui')
                                    <span class="status-badge disetujui">Disetujui</span>
                                @elseif($t->status === 'Menunggu')
                                    <span class="status-badge menunggu">Menunggu</span>
                                @else
                                    <span class="status-badge ditolak">{{ $t->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px; color: #94a3b8;">
                                Belum ada riwayat keterlambatan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $riwayatTerlambat->links() }}
        </div>
    </div>

</div>
@endsection
