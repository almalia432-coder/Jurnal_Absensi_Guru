@extends(Auth::user()->role === 'kepala_sekolah' ? 'layouts.kepala_sekolah' : 'layouts.waka_kesiswaan')

@section('title', 'Rekap Eksekutif Siswa Terlambat — SMKN 1 Boyolangu')
@section('header_title', 'Rekapitulasi Siswa Terlambat Se-Sekolah')
@section('header_subtitle', 'Laporan statistik keterlambatan siswa untuk evaluasi kedisiplinan dan pembinaan kesiswaan')

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
        padding: 20px 22px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
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
        font-size: 13.5px;
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

    .filter-input, .filter-select {
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
    .filter-input:focus, .filter-select:focus { border-color: #2b43b9; }

    @media (max-width: 900px) {
        .metrics-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="content-container">

    {{-- Filter Card --}}
    <form action="{{ url()->current() }}" method="GET" style="background: #ffffff; border-radius: 16px; padding: 18px 22px; border: 1px solid #eef2f7; box-shadow: 0 4px 14px rgba(0,0,0,0.03); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Pilih Bulan</span>
                <input type="month" name="bulan" value="{{ $bulan }}" class="filter-input" onchange="this.form.submit()">
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Filter Kelas</span>
                <select name="id_kelas" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id_kelas }}" {{ $kelasId == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="button" onclick="window.print()" style="background: #2b43b9; color: white; border: none; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-print"></i> Cetak Laporan Rekap
        </button>
    </form>

    {{-- Metrics Grid --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon blue">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div style="font-size: 26px; font-weight: 800; color: #1b2559;">
                    {{ $totalTerlambat }}
                </div>
                <div style="font-size: 12px; color: #64748b; font-weight: 600;">Total Kejadian Terlambat Disetujui</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon orange">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div style="font-size: 26px; font-weight: 800; color: #1b2559;">
                    {{ count($topSiswa) }}
                </div>
                <div style="font-size: 12px; color: #64748b; font-weight: 600;">Siswa Dalam Pantauan Keterlambatan</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon purple">
                <i class="fa-solid fa-school"></i>
            </div>
            <div>
                <div style="font-size: 26px; font-weight: 800; color: #1b2559;">
                    {{ count($rekapKelas) }}
                </div>
                <div style="font-size: 12px; color: #64748b; font-weight: 600;">Kelas Terdampak Keterlambatan</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        {{-- Section 1: Top 10 Siswa Terbanyak Terlambat --}}
        <div class="section-card" style="margin-bottom: 0;">
            <h3 style="font-size: 15px; font-weight: 800; color: #1b2559; margin: 0 0 16px 0;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #ea580c; margin-right: 6px;"></i>
                Top Siswa Paling Sering Terlambat
            </h3>

            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th style="text-align: right;">Total Telat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSiswa as $idx => $s)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong style="color: #1e293b;">{{ $s->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                                </td>
                                <td>{{ $s->siswa->kelas->nama_kelas ?? '-' }}</td>
                                <td style="text-align: right;">
                                    <span style="background: {{ $s->total_terlambat >= 3 ? '#fee2e2' : '#f1f5f9' }}; color: {{ $s->total_terlambat >= 3 ? '#dc2626' : '#1e293b' }}; padding: 3px 10px; border-radius: 6px; font-weight: 800;">
                                        {{ $s->total_terlambat }}x
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    Tidak ada data keterlambatan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 2: Rekapitulasi per Kelas --}}
        <div class="section-card" style="margin-bottom: 0;">
            <h3 style="font-size: 15px; font-weight: 800; color: #1b2559; margin: 0 0 16px 0;">
                <i class="fa-solid fa-chart-column" style="color: #2b43b9; margin-right: 6px;"></i>
                Rekapitulasi Keterlambatan per Kelas
            </h3>

            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Nama Kelas</th>
                            <th>Tingkat</th>
                            <th style="text-align: right;">Total Kasus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapKelas as $idx => $k)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong style="color: #1e293b;">{{ $k->kelas->nama_kelas ?? 'Kelas' }}</strong>
                                </td>
                                <td>Tingkat {{ $k->kelas->tingkat ?? '-' }}</td>
                                <td style="text-align: right;">
                                    <span style="background: #eef2ff; color: #2b43b9; padding: 3px 10px; border-radius: 6px; font-weight: 800;">
                                        {{ $k->total_terlambat }} siswa
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    Tidak ada data keterlambatan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
