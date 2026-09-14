@extends('layouts.kepala_sekolah')

@section('title', 'Monitoring Dispensasi Siswa — SMKN 1 BOYOLANGU')
@section('header_title', 'Monitoring Dispensasi Siswa')
@section('header_subtitle', 'Pantau aktivitas dispensasi keluar kelas siswa SMKN 1 Boyolangu')

@section('styles')
<style>
    /* ── KPI Summary Cards (Admin 1:1) ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .kpi-icon.indigo  { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #3730a3; }
    .kpi-icon.orange  { background: linear-gradient(135deg, #ffedd5, #fed7aa); color: #c2410c; }
    .kpi-icon.emerald { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .kpi-icon.sky     { background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0369a1; }

    .kpi-val { font-size: 30px; font-weight: 900; color: #0f172a; line-height: 1.1; letter-spacing: -0.5px; }
    .kpi-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 3px; }
    .kpi-sub { font-size: 11px; font-weight: 600; color: #94a3b8; margin-top: 2px; }

    /* ── Page Action & Filter Card (Admin 1:1) ── */
    .page-action-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        flex: 1;
    }
    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .filter-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: .5px;
    }

    .filter-input, .filter-select {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
        min-width: 170px;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: #2b43b9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(43,67,185,0.1);
    }

    .search-input-wrap { position: relative; }
    .search-input-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
    .search-input-wrap input { padding-left: 36px; }

    .btn-submit-filter {
        background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(43,67,185,0.2);
    }
    .btn-submit-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(43,67,185,0.3);
        color: #ffffff;
    }

    .btn-reset-filter {
        background: #f1f5f9;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-reset-filter:hover { background: #e2e8f0; color: #0f172a; }

    /* ── Table Card (Admin 1:1) ── */
    .table-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }

    .table-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .table-title {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-badge-count {
        background: #eef2ff;
        color: #2b43b9;
        font-size: 12px;
        font-weight: 800;
        padding: 3px 12px;
        border-radius: 20px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1.5px solid #e2e8f0;
    }

    .data-table tbody td {
        padding: 14px 16px;
        font-size: 13.5px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f8fafc; }

    .siswa-profile-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .siswa-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #ede9fe;
        color: #6b21a8;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .siswa-name { font-weight: 700; color: #0f172a; font-size: 14px; }
    .siswa-nis  { font-size: 12px; color: #64748b; margin-top: 2px; }

    .kelas-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 6px;
        background: #fdf4ff;
        color: #7e22ce;
    }

    .time-txt { font-weight: 700; color: #0f172a; }
    .time-arrow { color: #94a3b8; font-size: 12px; margin: 0 4px; }

    .status-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
    }
    .status-badge.disetujui { background: #d1fae5; color: #065f46; }
    .status-badge.menunggu  { background: #fef3c7; color: #92400e; }
    .status-badge.ditolak   { background: #fee2e2; color: #b91c1c; }
    .status-badge.selesai   { background: #e0e7ff; color: #3730a3; }

    /* Pagination (Admin Identical) */
    .pagination-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }
    .pagination-text { font-size: 13px; color: #64748b; font-weight: 600; }
    .pagination-links { display: flex; gap: 6px; }
    .pagination-links a, .pagination-links span {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }
    .pagination-links a {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        transition: all 0.15s ease;
    }
    .pagination-links a:hover { background: #e0e7ff; color: #2b43b9; border-color: #c7d2fe; }
    .pagination-links span.active {
        background: #2b43b9;
        color: #ffffff;
        border: 1px solid #2b43b9;
    }

    @media (max-width: 991px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .kpi-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<!-- KPI Cards Strip (Admin 1:1) -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon emerald">
            <i class="fa-solid fa-person-walking-arrow-right"></i>
        </div>
        <div>
            <div class="kpi-val">{{ $siswaDispHariIni }}</div>
            <div class="kpi-lbl">Dispensasi Hari Ini</div>
            <div class="kpi-sub">Siswa izin keluar aktif</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon orange">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div>
            <div class="kpi-val">{{ $dispMenunggu }}</div>
            <div class="kpi-lbl">Menunggu Verifikasi</div>
            <div class="kpi-sub">Perlu persetujuan Waka</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon sky">
            <i class="fa-solid fa-calendar-days"></i>
        </div>
        <div>
            <div class="kpi-val">{{ $siswaDispBulan }}</div>
            <div class="kpi-lbl">Total Bulan Ini</div>
            <div class="kpi-sub">Rekapitulasi berjalan</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon indigo">
            <i class="fa-solid fa-calendar-week"></i>
        </div>
        <div>
            <div class="kpi-val">{{ $dispMingguan }}</div>
            <div class="kpi-lbl">Total Pekan Ini</div>
            <div class="kpi-sub">Aktivitas mingguan</div>
        </div>
    </div>
</div>

<!-- Page Action & Filter Bar (Admin 1:1) -->
<div class="page-action-card">
    <form method="GET" action="{{ route('kepala-sekolah.dispensasi') }}" class="filter-group">
        <div class="filter-item" style="flex:1; min-width:200px;">
            <span class="filter-label">Cari Nama / NIS Siswa</span>
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" class="filter-input" placeholder="Ketik nama atau NIS siswa..." value="{{ request('q') }}" style="width:100%;">
            </div>
        </div>

        <div class="filter-item">
            <span class="filter-label">Status Dispensasi</span>
            <select name="status" class="filter-select">
                <option value="">Semua Status</option>
                <option value="Menunggu"  {{ request('status')=='Menunggu'  ? 'selected':'' }}>Menunggu</option>
                <option value="Disetujui" {{ request('status')=='Disetujui' ? 'selected':'' }}>Disetujui</option>
                <option value="Ditolak"   {{ request('status')=='Ditolak'   ? 'selected':'' }}>Ditolak</option>
                <option value="Selesai"   {{ request('status')=='Selesai'   ? 'selected':'' }}>Selesai</option>
            </select>
        </div>

        <div class="filter-item">
            <span class="filter-label">Tanggal</span>
            <input type="date" name="tanggal" class="filter-input" value="{{ request('tanggal') }}">
        </div>

        <div style="display:flex; align-items:flex-end; gap:8px; margin-top:20px;">
            <button type="submit" class="btn-submit-filter">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <a href="{{ route('kepala-sekolah.dispensasi') }}" class="btn-reset-filter">
                <i class="fa-solid fa-arrow-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Table Card (Admin 1:1) -->
<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-person-walking" style="color: #2b43b9;"></i>
            <span>Daftar Riwayat Dispensasi Siswa</span>
        </div>
        <span class="table-badge-count">{{ $dispList->total() }} Data Ditemukan</span>
    </div>

    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Identitas Siswa</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th>Jam Keluar → Kembali</th>
                    <th>Alasan Dispensasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dispList as $idx => $item)
                @php
                    $nama  = $item->siswa->nama_lengkap ?? 'Siswa';
                    $nis   = $item->siswa->nis ?? '—';
                    $kelas = optional($item->siswa->kelas)->nama_kelas ?? '—';
                    $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $nama)));
                    $init  = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : strtoupper(substr($nama,0,2));
                    $stl   = strtolower($item->status);
                @endphp
                <tr>
                    <td style="font-weight:700; color:#94a3b8;">{{ $dispList->firstItem() + $idx }}</td>
                    <td>
                        <div class="siswa-profile-cell">
                            <div class="siswa-avatar">{{ $init }}</div>
                            <div>
                                <div class="siswa-name">{{ $nama }}</div>
                                <div class="siswa-nis">NIS: {{ $nis }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="kelas-badge">{{ $kelas }}</span>
                    </td>
                    <td style="font-weight:700; color:#1e293b;">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                    </td>
                    <td>
                        <span class="time-txt">{{ \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') }}</span>
                        <span class="time-arrow">→</span>
                        <span class="time-txt">{{ $item->jam_kembali ? \Carbon\Carbon::parse($item->jam_kembali)->format('H:i') : '—' }}</span>
                    </td>
                    <td style="max-width: 250px;">
                        <span title="{{ $item->alasan }}">{{ Str::limit($item->alasan, 60) }}</span>
                    </td>
                    <td>
                        <span class="status-badge {{ $stl }}">{{ $item->status }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:48px 16px; color:#94a3b8;">
                        <i class="fa-solid fa-folder-open" style="font-size:36px; margin-bottom:10px; display:block;"></i>
                        <span style="font-weight:600;">Tidak ada data dispensasi siswa yang cocok dengan filter</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($dispList->hasPages())
    <div class="pagination-container">
        <div class="pagination-text">
            Menampilkan {{ $dispList->firstItem() }} sampai {{ $dispList->lastItem() }} dari {{ $dispList->total() }} data
        </div>
        <div class="pagination-links">
            @if($dispList->onFirstPage())
                <span style="opacity:0.4;"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $dispList->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            @foreach($dispList->getUrlRange(max(1, $dispList->currentPage()-2), min($dispList->lastPage(), $dispList->currentPage()+2)) as $page => $url)
                @if($page == $dispList->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($dispList->hasMorePages())
                <a href="{{ $dispList->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span style="opacity:0.4;"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection
