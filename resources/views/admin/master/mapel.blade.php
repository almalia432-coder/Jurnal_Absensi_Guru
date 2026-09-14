@extends('layouts.admin')

@section('title', 'Manajemen Mata Pelajaran — SMKN 1 BOYOLANGU')
@section('header_title', 'Manajemen Mata Pelajaran')
@section('header_subtitle', 'Kelola data mata pelajaran kurikulum SMK (Normatif, Adaptif, Produktif, Muatan Lokal) langsung dari database')

@section('styles')
<style>
    /* Toast Notification */
    .toast-container { position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; }
    .toast {
        display: flex; align-items: center; gap: 12px; background: #ffffff; border-radius: 14px; padding: 14px 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12); border-left: 4px solid #10b981; font-size: 14px; font-weight: 600; color: #0f172a;
        min-width: 320px; animation: toastSlideIn .3s ease;
    }
    .toast.error { border-left-color: #ef4444; }
    .toast i { font-size: 18px; color: #10b981; }
    .toast.error i { color: #ef4444; }
    @keyframes toastSlideIn { from { opacity:0; transform:translateX(40px);} to { opacity:1; transform:translateX(0);} }

    /* KPI Summary Cards */
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
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
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.07); border-color: #cbd5e1; }
    
    .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .kpi-icon.indigo  { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #3730a3; }
    .kpi-icon.emerald { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .kpi-icon.violet  { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #5b21b6; }
    .kpi-icon.sky     { background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0369a1; }
    
    .kpi-val { font-size: 28px; font-weight: 900; color: #0f172a; line-height: 1.1; letter-spacing: -0.5px; }
    .kpi-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 3px; }
    .kpi-sub { font-size: 11px; font-weight: 600; color: #94a3b8; margin-top: 2px; }

    /* Quick Filter Tabs */
    .filter-tabs-wrap {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        margin-bottom: 20px;
        scrollbar-width: thin;
    }
    .filter-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        color: #64748b;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .filter-tab-btn:hover {
        background: #f8fafc;
        color: #2b43b9;
        border-color: #cbd5e1;
    }
    .filter-tab-btn.active {
        background: #2b43b9;
        color: #ffffff;
        border-color: #2b43b9;
        box-shadow: 0 4px 14px rgba(43,67,185,0.25);
    }
    .filter-tab-count {
        background: rgba(0,0,0,0.06);
        color: inherit;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 20px;
    }
    .filter-tab-btn.active .filter-tab-count {
        background: rgba(255,255,255,0.25);
        color: #ffffff;
    }

    /* Page Action Card */
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

    .btn-add-mapel {
        background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 11px 22px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(43,67,185,0.25);
        white-space: nowrap;
    }
    .btn-add-mapel:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(43,67,185,0.35);
        color: #ffffff;
    }

    .btn-import-csv {
        background: #f1f5f9;
        color: #1e293b;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 11px 18px;
        font-size: 13.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-import-csv:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #94a3b8;
    }

    .filter-group { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; flex: 1; }
    .filter-item { display: flex; flex-direction: column; gap: 4px; }
    .filter-label { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: .5px; }
    
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
        min-width: 200px;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: #2b43b9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(43,67,185,0.1);
    }

    .search-input-wrap { position: relative; }
    .search-input-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
    .search-input-wrap input { padding-left: 36px; }

    .btn-filter-action {
        background: #f1f5f9;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-filter-action:hover { background: #e2e8f0; color: #0f172a; }

    /* Table Section */
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
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-title { font-size: 17px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .table-title i { color: #2b43b9; }
    .count-badge { font-size: 12px; font-weight: 700; color: #2b43b9; background: #eaeff8; padding: 5px 14px; border-radius: 20px; }

    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .data-table th {
        font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.6px;
        padding: 12px 16px; text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    }
    .data-table th:first-child { border-radius: 10px 0 0 10px; }
    .data-table th:last-child  { border-radius: 0 10px 10px 0; text-align: right; }
    
    .data-table td {
        padding: 16px; font-size: 13.5px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; transition: background 0.15s ease;
    }
    .data-table tbody tr:hover td { background: #f8fafc; }
    .data-table td:last-child { text-align: right; }

    /* Badges */
    .kode-badge {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 12px;
        font-weight: 700;
        color: #2b43b9;
        background: #eaeff8;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-block;
        border: 1px solid #d5e0f5;
    }

    .kelompok-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .kelompok-badge.Produktif    { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .kelompok-badge.Normatif     { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .kelompok-badge.Adaptif      { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .kelompok-badge.Muatan_Lokal { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

    /* Action Buttons */
    .action-btn-group { display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end; }
    .btn-tbl-action {
        width: 34px; height: 34px; border-radius: 9px;
        border: 1px solid #e2e8f0; background: #ffffff;
        color: #64748b; display: flex; align-items: center; justify-content: center;
        font-size: 13px; cursor: pointer; transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-tbl-action:hover { border-color: #cbd5e1; color: #0f172a; transform: translateY(-1px); }
    .btn-tbl-action.edit:hover   { background: #eff6ff; border-color: #93c5fd; color: #2563eb; }
    .btn-tbl-action.delete:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
    .btn-tbl-action.view:hover   { background: #f0fdf4; border-color: #86efac; color: #16a34a; }

    /* Pagination */
    .pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
    .pag-text { font-size: 13px; color: #64748b; font-weight: 600; }
    .pag-pills { display: flex; gap: 6px; }
    .pag-pills a, .pag-pills span {
        display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px;
        font-size: 13px; font-weight: 700; text-decoration: none; transition: all 0.2s ease;
    }
    .pag-pills a { background: #f1f5f9; color: #475569; }
    .pag-pills a:hover { background: #eaeff8; color: #2b43b9; }
    .pag-pills span.active { background: #2b43b9; color: #ffffff; box-shadow: 0 4px 12px rgba(43,67,185,0.25); }
    .pag-pills span.disabled { background: #f8fafc; color: #cbd5e1; cursor: default; }

    /* Modal Styling */
    .custom-modal-backdrop {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
        z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px;
    }
    .custom-modal-backdrop.show { display: flex; animation: fadeIn .2s ease; }
    .custom-modal {
        background: #ffffff; border-radius: 24px; width: 100%; max-width: 520px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; animation: scaleUp .25s ease;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    .modal-hdr {
        padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title-text { font-size: 17px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .modal-close-btn { background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
    .modal-close-btn:hover { background: #f1f5f9; color: #0f172a; }

    .modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
    .form-group-custom { margin-bottom: 16px; }
    .form-label-custom { display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 6px; }
    .form-label-custom span { color: #ef4444; }
    
    .form-ctrl-custom {
        width: 100%; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px;
        padding: 10px 14px; font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; transition: all 0.2s ease;
    }
    .form-ctrl-custom:focus { border-color: #2b43b9; background: #ffffff; box-shadow: 0 0 0 3px rgba(43,67,185,0.1); }

    .modal-ftr {
        padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: flex-end; gap: 12px;
    }
    .btn-modal-cancel {
        padding: 10px 18px; border-radius: 12px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; font-size: 13px; cursor: pointer;
    }
    .btn-modal-submit {
        padding: 10px 22px; border-radius: 12px; border: none; background: #2b43b9; color: #ffffff; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 4px 14px rgba(43,67,185,0.25);
    }
    .btn-modal-submit:hover { background: #1e3399; }
    .btn-modal-delete {
        padding: 10px 22px; border-radius: 12px; border: none; background: #ef4444; color: #ffffff; font-weight: 700; font-size: 13px; cursor: pointer;
    }
    .btn-modal-delete:hover { background: #dc2626; }

    /* Responsive */
    @media (max-width: 1024px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .kpi-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="mapel-management-container">

    {{-- Flash Notifications --}}
    <div class="toast-container" id="toastContainer">
        @if(session('success'))
            <div class="toast">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="toast error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="toast error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon indigo">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $totalMapel }}</div>
                <div class="kpi-lbl">TOTAL MAPEL</div>
                <div class="kpi-sub">Mata pelajaran kurikulum</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon violet">
                <i class="fa-solid fa-laptop-code"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $totalProduktif }}</div>
                <div class="kpi-lbl">PRODUKTIF / KEJURUAN</div>
                <div class="kpi-sub">Konsentrasi keahlian SMK</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon emerald">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $totalNormatif }}</div>
                <div class="kpi-lbl">NORMATIF (UMUM)</div>
                <div class="kpi-sub">Muatan nasional &amp; karakter</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon sky">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <div class="kpi-val">{{ $totalAdaptif + $totalMulok }}</div>
                <div class="kpi-lbl">ADAPTIF &amp; MULOK</div>
                <div class="kpi-sub">Dasar kejuruan &amp; kearifan lokal</div>
            </div>
        </div>
    </div>

    {{-- Filter Tabs Kelompok --}}
    <div class="filter-tabs-wrap">
        <a href="{{ route('admin.master.mapel', array_merge(request()->except('kelompok', 'page'), ['kelompok' => 'all'])) }}" class="filter-tab-btn {{ empty($kelompok) || $kelompok === 'all' ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i>
            <span>Semua Kelompok</span>
            <span class="filter-tab-count">{{ $kelompokCounts['all'] }}</span>
        </a>
        <a href="{{ route('admin.master.mapel', array_merge(request()->except('kelompok', 'page'), ['kelompok' => 'Produktif'])) }}" class="filter-tab-btn {{ $kelompok === 'Produktif' ? 'active' : '' }}">
            <i class="fa-solid fa-laptop-code"></i>
            <span>Produktif</span>
            <span class="filter-tab-count">{{ $kelompokCounts['Produktif'] }}</span>
        </a>
        <a href="{{ route('admin.master.mapel', array_merge(request()->except('kelompok', 'page'), ['kelompok' => 'Normatif'])) }}" class="filter-tab-btn {{ $kelompok === 'Normatif' ? 'active' : '' }}">
            <i class="fa-solid fa-scale-balanced"></i>
            <span>Normatif</span>
            <span class="filter-tab-count">{{ $kelompokCounts['Normatif'] }}</span>
        </a>
        <a href="{{ route('admin.master.mapel', array_merge(request()->except('kelompok', 'page'), ['kelompok' => 'Adaptif'])) }}" class="filter-tab-btn {{ $kelompok === 'Adaptif' ? 'active' : '' }}">
            <i class="fa-solid fa-calculator"></i>
            <span>Adaptif</span>
            <span class="filter-tab-count">{{ $kelompokCounts['Adaptif'] }}</span>
        </a>
        <a href="{{ route('admin.master.mapel', array_merge(request()->except('kelompok', 'page'), ['kelompok' => 'Muatan_Lokal'])) }}" class="filter-tab-btn {{ $kelompok === 'Muatan_Lokal' ? 'active' : '' }}">
            <i class="fa-solid fa-seedling"></i>
            <span>Muatan Lokal</span>
            <span class="filter-tab-count">{{ $kelompokCounts['Muatan_Lokal'] }}</span>
        </a>
    </div>

    {{-- Filter & Action Bar --}}
    <div class="page-action-card">
        <form action="{{ route('admin.master.mapel') }}" method="GET" class="filter-group">
            @if(!empty($kelompok) && $kelompok !== 'all')
                <input type="hidden" name="kelompok" value="{{ $kelompok }}">
            @endif

            <div class="filter-item">
                <span class="filter-label">Cari Mata Pelajaran</span>
                <div class="search-input-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ $search }}" class="filter-input" placeholder="Kode atau nama mata pelajaran...">
                </div>
            </div>

            <div class="filter-item" style="justify-content: flex-end;">
                <span class="filter-label" style="visibility:hidden;">Aksi</span>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-filter-action">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    @if($search || ($kelompok && $kelompok !== 'all'))
                        <a href="{{ route('admin.master.mapel') }}" class="btn-filter-action" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div style="display:flex;align-items:center;gap:10px;">
            <button type="button" class="btn-import-csv" onclick="openImportModal()">
                <i class="fa-solid fa-file-arrow-up"></i>
                <span>Import CSV</span>
            </button>

            <button type="button" class="btn-add-mapel" onclick="openAddModal()">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Mapel Baru</span>
            </button>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="table-card">
        <div class="table-hdr">
            <div class="table-title">
                <i class="fa-solid fa-book-bookmark"></i>
                <span>Daftar Mata Pelajaran</span>
                <span class="count-badge">{{ $mapelList->total() }} Mapel</span>
            </div>
        </div>

        <div class="table-responsive-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 140px;">Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Kelompok Kurikulum</th>
                        <th>Terhubung ke</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mapelList as $index => $m)
                        @php
                            $kelompokLabel = match($m->kelompok) {
                                'Normatif'     => 'Normatif (A)',
                                'Adaptif'      => 'Adaptif (B)',
                                'Produktif'    => 'Produktif / Kejuruan (C)',
                                'Muatan_Lokal' => 'Muatan Lokal',
                                default        => str_replace('_', ' ', $m->kelompok)
                            };

                            $kelompokIcon = match($m->kelompok) {
                                'Produktif'    => 'fa-laptop-code',
                                'Normatif'     => 'fa-scale-balanced',
                                'Adaptif'      => 'fa-calculator',
                                'Muatan_Lokal' => 'fa-seedling',
                                default        => 'fa-book'
                            };
                        @endphp
                        <tr>
                            <td style="color: #94a3b8; font-weight: 700;">
                                {{ $mapelList->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="kode-badge">{{ $m->kode_mapel }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; font-size: 14px;">
                                    {{ $m->nama_mapel }}
                                </div>
                            </td>
                            <td>
                                <span class="kelompok-badge {{ $m->kelompok }}">
                                    <i class="fa-solid {{ $kelompokIcon }}"></i>
                                    <span>{{ $kelompokLabel }}</span>
                                </span>
                            </td>
                            <td style="color: #64748b; font-size: 12.5px;">
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <span style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px; font-weight: 600;">
                                        <i class="fa-regular fa-calendar" style="color: #2b43b9; margin-right: 3px;"></i> {{ $m->jadwal_pelajaran_count }} Jadwal
                                    </span>
                                    <span style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px; font-weight: 600;">
                                        <i class="fa-solid fa-clipboard-list" style="color: #10b981; margin-right: 3px;"></i> {{ $m->jurnal_mengajar_count }} Jurnal
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="action-btn-group">
                                    {{-- Detail Modal Button --}}
                                    <button 
                                        type="button" 
                                        class="btn-tbl-action view" 
                                        title="Lihat Detail"
                                        onclick='openDetailModal(@json($m), "{{ $kelompokLabel }}")'
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    {{-- Edit Modal Button --}}
                                    <button 
                                        type="button" 
                                        class="btn-tbl-action edit" 
                                        title="Edit Mata Pelajaran"
                                        onclick='openEditModal(@json($m))'
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button 
                                        type="button" 
                                        class="btn-tbl-action delete" 
                                        title="Hapus Mata Pelajaran"
                                        onclick="openDeleteModal({{ $m->id_mapel }}, '{{ addslashes($m->nama_mapel) }}', '{{ $m->kode_mapel }}')"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px 16px;">
                                <div style="color: #94a3b8; font-size: 42px; margin-bottom: 12px;">
                                    <i class="fa-solid fa-book-open"></i>
                                </div>
                                <div style="font-size: 16px; font-weight: 700; color: #475569;">Tidak ada data mata pelajaran ditemukan</div>
                                <p style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Coba gunakan kata kunci pencarian lain atau klik tombol Tambah Mapel Baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($mapelList->hasPages())
        <div class="pagination-container">
            <span class="pag-text">
                Menampilkan {{ $mapelList->firstItem() ?? 0 }}–{{ $mapelList->lastItem() ?? 0 }} dari {{ $mapelList->total() }} mapel
            </span>
            <div class="pag-pills">
                @if($mapelList->onFirstPage())
                    <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                @else
                    <a href="{{ $mapelList->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                @endif

                @foreach($mapelList->getUrlRange(max(1, $mapelList->currentPage() - 2), min($mapelList->lastPage(), $mapelList->currentPage() + 2)) as $page => $url)
                    @if($page == $mapelList->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($mapelList->hasMorePages())
                    <a href="{{ $mapelList->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                @else
                    <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- MODAL TAMBAH MAPEL --}}
<div class="custom-modal-backdrop" id="addMapelModal">
    <div class="custom-modal">
        <div class="modal-hdr">
            <div class="modal-title-text">
                <i class="fa-solid fa-book-bookmark" style="color: #2b43b9;"></i>
                <span>Tambah Mata Pelajaran Baru</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeAddModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('admin.master.mapel.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group-custom">
                    <label class="form-label-custom">Kode Mapel <span>*</span></label>
                    <input type="text" name="kode_mapel" class="form-ctrl-custom" placeholder="Contoh: RPL01, MTK01, BIND01" required>
                    <small style="color: #94a3b8; font-size: 11px; margin-top: 4px; display: block;">Kode harus unik dan mudah diidentifikasi.</small>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Mata Pelajaran <span>*</span></label>
                    <input type="text" name="nama_mapel" class="form-ctrl-custom" placeholder="Contoh: Pemrograman Web & Perangkat Bergerak" required>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Kelompok Kurikulum <span>*</span></label>
                    <select name="kelompok" class="form-ctrl-custom" required>
                        <option value="Produktif" selected>Produktif / Kejuruan (C)</option>
                        <option value="Normatif">Normatif (A - Muatan Nasional)</option>
                        <option value="Adaptif">Adaptif (B - Dasar Kejuruan)</option>
                        <option value="Muatan_Lokal">Muatan Lokal</option>
                    </select>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-modal-cancel" onclick="closeAddModal()">Batal</button>
                <button type="submit" class="btn-modal-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Mapel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT MAPEL --}}
<div class="custom-modal-backdrop" id="editMapelModal">
    <div class="custom-modal">
        <div class="modal-hdr">
            <div class="modal-title-text">
                <i class="fa-solid fa-pen-to-square" style="color: #2b43b9;"></i>
                <span>Edit Data Mata Pelajaran</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editMapelForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group-custom">
                    <label class="form-label-custom">Kode Mapel <span>*</span></label>
                    <input type="text" name="kode_mapel" id="edit_kode_mapel" class="form-ctrl-custom" required>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Mata Pelajaran <span>*</span></label>
                    <input type="text" name="nama_mapel" id="edit_nama_mapel" class="form-ctrl-custom" required>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Kelompok Kurikulum <span>*</span></label>
                    <select name="kelompok" id="edit_kelompok" class="form-ctrl-custom" required>
                        <option value="Produktif">Produktif / Kejuruan (C)</option>
                        <option value="Normatif">Normatif (A - Muatan Nasional)</option>
                        <option value="Adaptif">Adaptif (B - Dasar Kejuruan)</option>
                        <option value="Muatan_Lokal">Muatan Lokal</option>
                    </select>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-modal-cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-modal-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETAIL MAPEL --}}
<div class="custom-modal-backdrop" id="detailMapelModal">
    <div class="custom-modal">
        <div class="modal-hdr">
            <div class="modal-title-text">
                <i class="fa-solid fa-circle-info" style="color: #2b43b9;"></i>
                <span>Detail Mata Pelajaran</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeDetailModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; margin-bottom: 18px;">
                <span id="detail_kode_badge" class="kode-badge" style="font-size: 13px;"></span>
                <h3 id="detail_nama" style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 8px 0 4px 0;"></h3>
                <div id="detail_kelompok_badge" style="margin-top: 6px;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                <div style="background: #ffffff; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase;">ID Mapel</div>
                    <div id="detail_id" style="font-weight: 700; color: #0f172a; margin-top: 4px;"></div>
                </div>
                <div style="background: #ffffff; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase;">Jadwal Terdaftar</div>
                    <div id="detail_jadwal_count" style="font-weight: 700; color: #0f172a; margin-top: 4px;"></div>
                </div>
                <div style="background: #ffffff; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase;">Jurnal Mengajar</div>
                    <div id="detail_jurnal_count" style="font-weight: 700; color: #0f172a; margin-top: 4px;"></div>
                </div>
                <div style="background: #ffffff; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase;">Tanggal Input</div>
                    <div id="detail_created" style="font-weight: 700; color: #0f172a; margin-top: 4px;"></div>
                </div>
            </div>
        </div>
        <div class="modal-ftr">
            <button type="button" class="btn-modal-cancel" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL DELETE KONFIRMASI --}}
<div class="custom-modal-backdrop" id="deleteMapelModal">
    <div class="custom-modal" style="max-width: 440px;">
        <div class="modal-hdr" style="border-bottom: none; padding-bottom: 0;">
            <div class="modal-title-text" style="color: #ef4444;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Konfirmasi Hapus Mapel</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeDeleteModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="deleteMapelForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body" style="padding-top: 12px;">
                <p style="font-size: 14px; color: #475569; line-height: 1.5; margin: 0;">
                    Apakah Anda yakin ingin menghapus mata pelajaran <strong id="delete_mapel_nama" style="color: #0f172a;"></strong> (<span id="delete_mapel_kode"></span>) dari database?
                </p>
                <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 10px; padding: 10px 14px; margin-top: 14px; display: flex; gap: 8px; align-items: center; color: #b91c1c; font-size: 12px;">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Mapel yang sudah memiliki riwayat jurnal tidak dapat dihapus.</span>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="btn-modal-delete">
                    <i class="fa-solid fa-trash-can"></i> Ya, Hapus Mapel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL IMPORT MAPEL --}}
<div class="custom-modal-backdrop" id="importMapelModal">
    <div class="custom-modal" style="max-width: 520px;">
        <div class="modal-hdr">
            <div class="modal-title-text">
                <i class="fa-solid fa-file-csv" style="color: #2b43b9;"></i>
                <span>Import Mata Pelajaran dari CSV</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeImportModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" style="padding-top:14px;">
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;margin-bottom:18px;font-size:12.5px;color:#475569;line-height:1.5;">
                <div style="font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-circle-info" style="color:#3b82f6;"></i> Petunjuk Format Kolom:
                </div>
                Gunakan format kolom: <b>kode_mapel, nama_mapel, kelompok</b>.<br>
                Kelompok: <i>Normatif, Adaptif, Produktif, Muatan_Lokal</i>.
                <div style="margin-top:10px;">
                    <a href="{{ route('admin.import.template', 'mapel') }}" class="btn-import-csv" style="font-size:11.5px;padding:6px 12px;text-decoration:none;">
                        <i class="fa-solid fa-download"></i> Unduh Template CSV Mapel
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.master.mapel.import-csv') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:18px;">
                    <label class="form-label-custom">Pilih File CSV (.csv / .txt) <span style="color:#ef4444">*</span></label>
                    <input type="file" name="file_csv" accept=".csv, .txt, text/csv" class="form-ctrl-custom" required style="padding:8px 10px;">
                </div>

                <div class="modal-ftr" style="padding:0;border:none;margin-top:20px;">
                    <button type="button" class="btn-modal-cancel" onclick="closeImportModal()">Batal</button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Unggah &amp; Proses Impor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Auto dismiss toasts
    setTimeout(() => {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(t => t.style.display = 'none');
    }, 4000);

    // Modal Import
    function openImportModal() {
        document.getElementById('importMapelModal').classList.add('show');
    }
    function closeImportModal() {
        document.getElementById('importMapelModal').classList.remove('show');
    }

    // Modal Add
    function openAddModal() {
        document.getElementById('addMapelModal').classList.add('show');
    }
    function closeAddModal() {
        document.getElementById('addMapelModal').classList.remove('show');
    }

    // Modal Edit
    function openEditModal(mapel) {
        document.getElementById('edit_kode_mapel').value = mapel.kode_mapel || '';
        document.getElementById('edit_nama_mapel').value = mapel.nama_mapel || '';
        document.getElementById('edit_kelompok').value = mapel.kelompok || 'Produktif';

        const form = document.getElementById('editMapelForm');
        form.action = `/admin/master/mapel/${mapel.id_mapel}`;

        document.getElementById('editMapelModal').classList.add('show');
    }
    function closeEditModal() {
        document.getElementById('editMapelModal').classList.remove('show');
    }

    // Modal Detail
    function openDetailModal(mapel, kelompokLabel) {
        document.getElementById('detail_kode_badge').textContent = mapel.kode_mapel || '-';
        document.getElementById('detail_nama').textContent = mapel.nama_mapel || '-';
        document.getElementById('detail_id').textContent = '#' + mapel.id_mapel;
        document.getElementById('detail_jadwal_count').textContent = (mapel.jadwal_pelajaran_count || 0) + ' Sesi Jadwal';
        document.getElementById('detail_jurnal_count').textContent = (mapel.jurnal_mengajar_count || 0) + ' Jurnal';
        document.getElementById('detail_created').textContent = mapel.created_at ? new Date(mapel.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';

        document.getElementById('detail_kelompok_badge').innerHTML = `
            <span class="kelompok-badge ${mapel.kelompok}">
                <i class="fa-solid fa-tag"></i> ${kelompokLabel}
            </span>
        `;

        document.getElementById('detailMapelModal').classList.add('show');
    }
    function closeDetailModal() {
        document.getElementById('detailMapelModal').classList.remove('show');
    }

    // Modal Delete
    function openDeleteModal(idMapel, namaMapel, kodeMapel) {
        document.getElementById('delete_mapel_nama').textContent = namaMapel;
        document.getElementById('delete_mapel_kode').textContent = kodeMapel;

        const form = document.getElementById('deleteMapelForm');
        form.action = `/admin/master/mapel/${idMapel}`;

        document.getElementById('deleteMapelModal').classList.add('show');
    }
    function closeDeleteModal() {
        document.getElementById('deleteMapelModal').classList.remove('show');
    }

    // Close modal when clicking backdrop
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal-backdrop')) {
            e.target.classList.remove('show');
        }
    });
</script>
@endsection
