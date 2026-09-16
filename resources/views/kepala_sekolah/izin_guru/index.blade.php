@extends('layouts.kepala_sekolah')

@section('title', 'Monitoring Izin Guru — SMKN 1 BOYOLANGU')
@section('header_title', 'Monitoring Izin Guru')
@section('header_subtitle', 'Pantau data pengajuan izin dan cuti seluruh guru SMKN 1 Boyolangu')

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
        min-width: 160px;
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

    .guru-profile-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .guru-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #e0e7ff;
        color: #2b43b9;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .guru-name { font-weight: 700; color: #0f172a; font-size: 14px; }
    .guru-nip  { font-size: 12px; color: #64748b; margin-top: 2px; }

    .tag-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 6px;
        background: #f0fdf4;
        color: #15803d;
    }

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

    /* Action Buttons & Tabs */
    .status-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        color: #475569;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .tab-pill:hover { background: #f1f5f9; color: #0f172a; }
    .tab-pill.active { background: #2b43b9; color: #ffffff; border-color: #2b43b9; }
    .tab-count {
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 20px;
        background: rgba(0,0,0,0.08);
    }
    .tab-pill.active .tab-count {
        background: rgba(255,255,255,0.25);
        color: #ffffff;
    }

    .btn-act-approve {
        background: #10b981;
        color: #ffffff;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-act-approve:hover { background: #059669; transform: translateY(-1px); }

    .btn-act-reject {
        background: #ef4444;
        color: #ffffff;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-act-reject:hover { background: #dc2626; transform: translateY(-1px); }

    /* Modal Backdrop & Container */
    .modal-backdrop-kepsek {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-backdrop-kepsek.active { display: flex; }
    .modal-box-kepsek {
        background: #ffffff;
        width: 100%;
        max-width: 480px;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    }
    .modal-head-kepsek {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body-kepsek { padding: 24px; }
    .modal-foot-kepsek {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
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
        <div class="kpi-icon indigo">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <div>
            <div class="kpi-val">{{ $guruAktifIzin }}</div>
            <div class="kpi-lbl">Guru Izin Hari Ini</div>
            <div class="kpi-sub">Sedang berhalangan hadir</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon orange">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div>
            <div class="kpi-val">{{ $kepsekIzinMenunggu }}</div>
            <div class="kpi-lbl">Menunggu Persetujuan Kepsek</div>
            <div class="kpi-sub">Persetujuan final (Tahap 3)</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon emerald">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div>
            <div class="kpi-val">{{ $izinBulanIni }}</div>
            <div class="kpi-lbl">Total Izin Bulan Ini</div>
            <div class="kpi-sub">Rekapitulasi berjalan</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon sky">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
        <div>
            <div class="kpi-val">{{ $totalGuru }}</div>
            <div class="kpi-lbl">Total Guru Terdaftar</div>
            <div class="kpi-sub">Status aktif bertugas</div>
        </div>
    </div>
</div>

<!-- Page Action & Filter Bar (Admin 1:1) -->
<!-- Page Action & Filter Bar (Admin 1:1) -->
<div class="page-action-card">
    <div class="status-tabs">
        <a href="{{ route('kepala-sekolah.izin-guru') }}" class="tab-pill {{ empty(request('status')) ? 'active' : '' }}">
            <span>Semua Data</span>
            <span class="tab-count">{{ $tabCounts->semua ?? $izinList->total() }}</span>
        </a>
        <a href="{{ route('kepala-sekolah.izin-guru', ['status' => 'menunggu_kepsek']) }}" class="tab-pill {{ request('status') === 'menunggu_kepsek' || request('status') === 'Menunggu' ? 'active' : '' }}">
            <span><i class="fa-solid fa-hourglass-half" style="color:#f59e0b;"></i> Menunggu Kepsek</span>
            <span class="tab-count" style="background:#fef3c7; color:#b45309;">{{ $tabCounts->menunggu_kepsek ?? 0 }}</span>
        </a>
        <a href="{{ route('kepala-sekolah.izin-guru', ['status' => 'Disetujui']) }}" class="tab-pill {{ request('status') === 'Disetujui' ? 'active' : '' }}">
            <span><i class="fa-solid fa-circle-check" style="color:#10b981;"></i> Disetujui Penuh</span>
            <span class="tab-count" style="background:#d1fae5; color:#065f46;">{{ $tabCounts->disetujui ?? 0 }}</span>
        </a>
        <a href="{{ route('kepala-sekolah.izin-guru', ['status' => 'Ditolak']) }}" class="tab-pill {{ request('status') === 'Ditolak' ? 'active' : '' }}">
            <span><i class="fa-solid fa-circle-xmark" style="color:#ef4444;"></i> Ditolak</span>
            <span class="tab-count" style="background:#fee2e2; color:#b91c1c;">{{ $tabCounts->ditolak ?? 0 }}</span>
        </a>
    </div>

    <form method="GET" action="{{ route('kepala-sekolah.izin-guru') }}" class="filter-group">
        <div class="filter-item" style="flex:1; min-width:200px;">
            <span class="filter-label">Cari Nama Guru</span>
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" class="filter-input" placeholder="Ketik nama atau NIP guru..." value="{{ request('q') }}" style="width:100%;">
            </div>
        </div>

        <div class="filter-item">
            <span class="filter-label">Status Izin</span>
            <select name="status" class="filter-select">
                <option value="">Semua Status</option>
                <option value="menunggu_kepsek" {{ request('status')=='menunggu_kepsek' || request('status')=='Menunggu' ? 'selected':'' }}>Menunggu Kepsek</option>
                <option value="Disetujui"       {{ request('status')=='Disetujui' ? 'selected':'' }}>Disetujui Penuh</option>
                <option value="Ditolak"         {{ request('status')=='Ditolak'   ? 'selected':'' }}>Ditolak</option>
            </select>
        </div>

        <div class="filter-item">
            <span class="filter-label">Jenis Izin</span>
            <select name="jenis" class="filter-select">
                <option value="">Semua Jenis</option>
                <option value="Sakit"       {{ request('jenis')=='Sakit'       ? 'selected':'' }}>Sakit</option>
                <option value="Izin"        {{ request('jenis')=='Izin'        ? 'selected':'' }}>Izin</option>
                <option value="Dinas_Luar"  {{ request('jenis')=='Dinas_Luar'  ? 'selected':'' }}>Dinas Luar</option>
                <option value="Cuti"        {{ request('jenis')=='Cuti'        ? 'selected':'' }}>Cuti</option>
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
            <a href="{{ route('kepala-sekolah.izin-guru') }}" class="btn-reset-filter">
                <i class="fa-solid fa-arrow-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Table Card (Admin 1:1) -->
<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-clipboard-list" style="color: #2b43b9;"></i>
            <span>Daftar Riwayat Izin Guru</span>
        </div>
        <span class="table-badge-count">{{ $izinList->total() }} Data Ditemukan</span>
    </div>

    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Identitas Guru</th>
                    <th>Kategori & Tanggal</th>
                    <th>Alasan</th>
                    <th>Persetujuan Berjenjang</th>
                    <th>Status Akhir</th>
                    <th style="text-align: right;">Aksi Final Kepsek</th>
                </tr>
            </thead>
            <tbody>
                @forelse($izinList as $idx => $item)
                @php
                    $nama = $item->guru->nama_lengkap ?? 'Guru';
                    $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $nama)));
                    $init = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : strtoupper(substr($nama,0,2));
                    $stl = strtolower($item->status);
                @endphp
                <tr>
                    <td style="font-weight:700; color:#94a3b8;">{{ $izinList->firstItem() + $idx }}</td>
                    <td>
                        <div class="guru-profile-cell">
                            <div class="guru-avatar">{{ $init }}</div>
                            <div>
                                <div class="guru-name">{{ $nama }}</div>
                                <div class="guru-nip">NIP: {{ $item->guru->nip ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="tag-badge">{{ str_replace('_', ' ', $item->jenis_izin) }}</span>
                        <div style="font-weight:700; color:#1e293b; margin-top:4px; font-size:12.5px;">
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}
                            @if($item->tanggal_mulai != $item->tanggal_selesai)
                                <span style="font-size:11px; color:#64748b; font-weight:normal;">s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        @if($item->bukti_file)
                            <a href="{{ Storage::url($item->bukti_file) }}" target="_blank" style="font-size:11px; color:#2b43b9; font-weight:700; text-decoration:none;">
                                <i class="fa-solid fa-paperclip"></i> Lampiran
                            </a>
                        @endif
                    </td>
                    <td style="max-width: 200px;">
                        <span title="{{ $item->alasan }}">{{ Str::limit($item->alasan, 55) }}</span>
                    </td>
                    <td>
                        <div style="font-size:11.5px; line-height:1.4;">
                            <div><strong>1. Piket:</strong> 
                                @if($item->piket_status === 'Disetujui')
                                    <span style="color:#10b981; font-weight:700;">Disetujui</span>
                                @elseif($item->piket_status === 'Ditolak')
                                    <span style="color:#ef4444; font-weight:700;">Ditolak</span>
                                @else
                                    <span style="color:#94a3b8;">Menunggu</span>
                                @endif
                            </div>
                            <div><strong>2. Waka SDM:</strong> 
                                @if($item->waka_status === 'Disetujui')
                                    <span style="color:#10b981; font-weight:700;">Disetujui</span>
                                @elseif($item->waka_status === 'Ditolak')
                                    <span style="color:#ef4444; font-weight:700;">Ditolak</span>
                                @else
                                    <span style="color:#94a3b8;">Menunggu</span>
                                @endif
                            </div>
                            <div><strong>3. Kepsek:</strong> 
                                @if($item->kepsek_status === 'Disetujui')
                                    <span style="color:#10b981; font-weight:700;">Disetujui</span>
                                @elseif($item->kepsek_status === 'Ditolak')
                                    <span style="color:#ef4444; font-weight:700;">Ditolak</span>
                                @else
                                    <span style="color:#f59e0b; font-weight:700;">Menunggu Kepsek</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($item->status === 'Disetujui')
                            <span class="status-badge disetujui"><i class="fa-solid fa-circle-check"></i> Disetujui Penuh</span>
                        @elseif($item->status === 'Ditolak')
                            <span class="status-badge ditolak"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>
                        @else
                            <span class="status-badge menunggu"><i class="fa-solid fa-clock"></i> Dalam Proses</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($item->tahap_approval === 'kepsek')
                            <div style="display:flex; justify-content:flex-end; gap:6px;">
                                <button type="button" class="btn-act-approve" onclick="openApprovalModalKepsek({{ $item->id }}, '{{ addslashes($nama) }}', 'Disetujui')">
                                    <i class="fa-solid fa-check"></i> Setujui Final
                                </button>
                                <button type="button" class="btn-act-reject" onclick="openApprovalModalKepsek({{ $item->id }}, '{{ addslashes($nama) }}', 'Ditolak')">
                                    <i class="fa-solid fa-xmark"></i> Tolak
                                </button>
                            </div>
                        @elseif($item->status === 'Disetujui')
                            <span style="font-size:11.5px; font-weight:700; color:#065f46; background:#d1fae5; padding:4px 8px; border-radius:6px;">
                                Resmi Disetujui
                            </span>
                        @elseif($item->status === 'Ditolak')
                            <span style="font-size:11.5px; font-weight:700; color:#991b1b; background:#fee2e2; padding:4px 8px; border-radius:6px;">
                                Ditolak ({{ $item->ditolak_oleh_role ?? 'Sekolah' }})
                            </span>
                        @else
                            <span style="font-size:11.5px; font-weight:600; color:#64748b;">
                                Menunggu Tahap Sebelumnya
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:48px 16px; color:#94a3b8;">
                        <i class="fa-solid fa-folder-open" style="font-size:36px; margin-bottom:10px; display:block;"></i>
                        <span style="font-weight:600;">Tidak ada data izin guru yang cocok dengan filter</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($izinList->hasPages())
    <div class="pagination-container">
        <div class="pagination-text">
            Menampilkan {{ $izinList->firstItem() }} sampai {{ $izinList->lastItem() }} dari {{ $izinList->total() }} data
        </div>
        <div class="pagination-links">
            @if($izinList->onFirstPage())
                <span style="opacity:0.4;"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $izinList->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            @foreach($izinList->getUrlRange(max(1, $izinList->currentPage()-2), min($izinList->lastPage(), $izinList->currentPage()+2)) as $page => $url)
                @if($page == $izinList->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($izinList->hasMorePages())
                <a href="{{ $izinList->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span style="opacity:0.4;"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Modal Approval Kepala Sekolah -->
<div id="modalApprovalKepsek" class="modal-backdrop-kepsek">
    <div class="modal-box-kepsek">
        <div class="modal-head-kepsek">
            <h4 id="kepsekModalTitle" style="margin:0; font-size:16px; font-weight:800; color:#0f172a;">Persetujuan Akhir Izin Guru</h4>
            <button type="button" onclick="closeApprovalModalKepsek()" style="background:none; border:none; font-size:18px; cursor:pointer; color:#94a3b8;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="formApprovalKepsek" method="POST" action="">
            @csrf
            <div class="modal-body-kepsek">
                <input type="hidden" name="status" id="kepsekModalStatusInput" value="">
                
                <p id="kepsekModalDesc" style="font-size:13.5px; color:#334155; margin-bottom:16px;"></p>

                <div id="kepsekAlertReject" style="display:none; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:12px; margin-bottom:14px; font-size:12.5px; color:#991b1b;">
                    <i class="fa-solid fa-circle-exclamation"></i> <strong>Pemberitahuan Penolakan:</strong> Izin ini akan ditolak secara resmi. Sistem akan langsung mengirim notifikasi kepada guru pengampu bahwa permohonan ditolak dan <strong>diwajibkan untuk tetap melaksanakan / melanjutkan KBM</strong>.
                </div>

                <div id="kepsekAlertApprove" style="display:none; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px; margin-bottom:14px; font-size:12.5px; color:#166534;">
                    <i class="fa-solid fa-circle-check"></i> <strong>Persetujuan Lengkap:</strong> Izin ini telah disetujui Guru Piket dan Waka SDM. Persetujuan Kepala Sekolah akan meresmikan izin dan mengirim notifikasi persetujuan penuh ke guru mapel.
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:12.5px; font-weight:700; color:#1e293b; margin-bottom:6px;">
                        Catatan Kepala Sekolah (Opsional):
                    </label>
                    <textarea name="catatan" id="kepsekModalCatatanInput" rows="3" class="filter-input" style="width:100%; box-sizing:border-box;" placeholder="Tambahkan instruksi atau catatan keputusan..."></textarea>
                </div>
            </div>
            <div class="modal-foot-kepsek">
                <button type="button" onclick="closeApprovalModalKepsek()" style="padding:8px 16px; border-radius:8px; border:1.5px solid #cbd5e1; background:#ffffff; color:#475569; font-weight:700; cursor:pointer;">
                    Batal
                </button>
                <button type="submit" id="kepsekModalSubmitBtn" class="btn-act-approve" style="padding:8px 18px;">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApprovalModalKepsek(id, namaGuru, status) {
        const form = document.getElementById('formApprovalKepsek');
        form.action = `/kepala-sekolah/izin-guru/${id}/status`;

        const statusInput = document.getElementById('kepsekModalStatusInput');
        statusInput.value = status;

        const title = document.getElementById('kepsekModalTitle');
        const desc = document.getElementById('kepsekModalDesc');
        const alertApprove = document.getElementById('kepsekAlertApprove');
        const alertReject = document.getElementById('kepsekAlertReject');
        const submitBtn = document.getElementById('kepsekModalSubmitBtn');
        document.getElementById('kepsekModalCatatanInput').value = '';

        if (status === 'Disetujui') {
            title.textContent = 'Persetujuan Final Izin Guru';
            desc.innerHTML = `Setujui secara final pengajuan izin dari <strong>${namaGuru}</strong>?`;
            alertApprove.style.display = 'block';
            alertReject.style.display = 'none';
            submitBtn.className = 'btn-act-approve';
            submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> Ya, Setujui Penuh';
        } else {
            title.textContent = 'Tolak Izin Guru';
            desc.innerHTML = `Tolak pengajuan izin dari <strong>${namaGuru}</strong>?`;
            alertApprove.style.display = 'none';
            alertReject.style.display = 'block';
            submitBtn.className = 'btn-act-reject';
            submitBtn.innerHTML = '<i class="fa-solid fa-xmark"></i> Tolak (Wajib Lanjut KBM)';
        }

        document.getElementById('modalApprovalKepsek').classList.add('active');
    }

    function closeApprovalModalKepsek() {
        document.getElementById('modalApprovalKepsek').classList.remove('active');
    }

    document.getElementById('modalApprovalKepsek').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApprovalModalKepsek();
        }
    });
</script>
@endsection
