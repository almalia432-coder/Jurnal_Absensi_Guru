@extends('layouts.waka_kurikulum')
@section('title', 'Guru & Beban Mengajar - Waka Kurikulum')
@section('header_title', 'Guru & Beban Mengajar')
@section('header_subtitle', 'Pengaturan pembagian tugas mengajar guru, alokasi mata pelajaran, dan pemantauan standar 24 JP')

@section('header_extra')
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('waka-kurikulum.guru-mengajar.export', request()->query()) }}"
           class="btn-primary-action" style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3);text-decoration:none;"
           title="Unduh rekap beban mengajar sebagai file CSV">
            <i class="fa-solid fa-file-csv"></i> Ekspor CSV
        </a>
        <button type="button" class="btn-primary-action" onclick="openPlottingModal()">
            <i class="fa-solid fa-calendar-plus"></i> + Plotting Guru Mengajar
        </button>
    </div>
@endsection

@section('styles')
<style>
    .btn-primary-action {
        background: #ffffff; color: #0284c7; border: none; padding: 10px 18px;
        border-radius: 12px; font-weight: 700; font-size: 13.5px; cursor: pointer;
        display: flex; align-items: center; gap: 8px; transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .btn-primary-action:hover { background: #f0f9ff; transform: translateY(-1px); }

    /* KPI Row */
    .kpi-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 24px; }
    .kpi-card {
        background: white; border-radius: 18px; border: 1px solid #e2e8f0;
        padding: 18px 20px; display: flex; align-items: center; gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .kpi-icon {
        width: 48px; height: 48px; border-radius: 14px; display: flex;
        align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
    }
    .kpi-icon.total { background: #e0f2fe; color: #0284c7; }
    .kpi-icon.memenuhi { background: #dcfce7; color: #16a34a; }
    .kpi-icon.kurang { background: #fef3c7; color: #d97706; }
    .kpi-icon.tinggi { background: #ede9fe; color: #7c3aed; }
    .kpi-icon.kosong { background: #f1f5f9; color: #64748b; }
    .kpi-info h4 { font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1.1; }
    .kpi-info p { font-size: 11px; font-weight: 600; color: #64748b; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Filter Bar */
    .filter-bar {
        background: white; border-radius: 18px; border: 1px solid #e2e8f0;
        padding: 16px 22px; margin-bottom: 22px; display: flex; align-items: center;
        justify-content: space-between; gap: 16px; flex-wrap: wrap;
    }
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-tab {
        padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 700;
        cursor: pointer; text-decoration: none; background: #f8fafc; color: #64748b;
        border: 1.5px solid #e2e8f0; transition: all 0.15s; display: inline-flex; align-items: center; gap: 8px;
    }
    .filter-tab.active { background: #0284c7; color: white; border-color: #0284c7; }
    .filter-tab:hover:not(.active) { background: #f0f9ff; color: #0284c7; border-color: #bae6fd; }
    .badge-count { font-size: 11px; padding: 1px 7px; border-radius: 6px; background: rgba(0,0,0,0.08); }
    .filter-tab.active .badge-count { background: rgba(255,255,255,0.25); color: white; }

    .filter-inputs-row { display: flex; align-items: center; gap: 12px; }
    .select-filter-mapel {
        padding: 9px 14px; border-radius: 10px; border: 1.5px solid #e2e8f0;
        font-size: 13px; font-family: inherit; min-width: 170px;
    }
    .select-filter-mapel:focus { outline: none; border-color: #0284c7; }

    .search-wrap { display: flex; align-items: center; position: relative; width: 250px; }
    .search-input {
        width: 100%; padding: 9px 14px 9px 36px; border-radius: 10px;
        border: 1.5px solid #e2e8f0; font-size: 13px; font-family: inherit;
    }
    .search-input:focus { outline: none; border-color: #0284c7; }
    .search-icon { position: absolute; left: 12px; color: #94a3b8; font-size: 14px; }

    /* Table Card */
    .table-card { background: white; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; }
    .table-header-custom {
        padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex;
        align-items: center; justify-content: space-between;
    }
    .table-title { font-size: 15px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
        padding: 12px 20px; text-align: left; font-size: 11.5px; font-weight: 700;
        text-transform: uppercase; color: #94a3b8; border-bottom: 1px solid #f1f5f9; background: #fafcff;
    }
    .data-table td {
        padding: 15px 20px; font-size: 13.5px; border-bottom: 1px solid #f8fafc;
        color: #334155; vertical-align: middle;
    }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #f8fafc; }

    /* Teacher Profile Block */
    .guru-profile-cell { display: flex; align-items: center; gap: 12px; }
    .guru-avatar-circle {
        width: 40px; height: 40px; border-radius: 12px; background: #e0f2fe;
        color: #0284c7; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 14px; flex-shrink: 0;
    }
    .guru-meta-name { font-weight: 700; color: #0f172a; line-height: 1.2; }
    .guru-meta-sub { font-size: 12px; color: #64748b; margin-top: 2px; }

    /* Mapel & Class Tags */
    .mapel-pill-group { display: flex; gap: 6px; flex-wrap: wrap; }
    .mapel-pill {
        display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px;
        border-radius: 8px; font-size: 11.5px; font-weight: 700; background: #f1f5f9; color: #334155;
    }
    .mapel-pill.Normatif { background: #dcfce7; color: #15803d; }
    .mapel-pill.Adaptif { background: #fef3c7; color: #b45309; }
    .mapel-pill.Produktif { background: #f3e8ff; color: #7e22ce; }
    .mapel-pill.Muatan_Lokal { background: #ffe4e6; color: #be123c; }

    .kelas-pill-group { display: flex; gap: 4px; flex-wrap: wrap; }
    .kelas-pill-sm {
        padding: 2px 7px; border-radius: 6px; font-size: 11px; font-weight: 600;
        background: #e2e8f0; color: #475569;
    }

    /* Status Badge Beban */
    .status-badge-beban {
        padding: 5px 12px; border-radius: 10px; font-size: 12px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .status-badge-beban.success { background: #dcfce7; color: #15803d; }
    .status-badge-beban.warning { background: #fef3c7; color: #b45309; }
    .status-badge-beban.primary { background: #ede9fe; color: #7c3aed; }
    .status-badge-beban.secondary { background: #f1f5f9; color: #64748b; }

    /* Modal Form Styles */
    .custom-modal-backdrop {
        display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px); z-index: 10000; align-items: center; justify-content: center; padding: 20px;
    }
    .custom-modal-backdrop.open { display: flex; }
    .custom-modal-box {
        background: white; border-radius: 22px; width: 100%; max-width: 540px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.25); overflow: hidden; animation: popIn 0.25s ease-out;
    }
    .modal-hdr {
        padding: 20px 24px; background: linear-gradient(135deg, #3d56b2, #2b3a8c);
        color: white; display: flex; align-items: center; justify-content: space-between;
    }
    .modal-hdr h3 { font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .modal-hdr-close {
        width: 32px; height: 32px; border-radius: 50%; border: none;
        background: rgba(255,255,255,0.2); color: white; cursor: pointer; font-size: 15px;
    }
    .modal-bdy { padding: 24px; }
    .modal-ftr { padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px; }
    .form-group-custom { margin-bottom: 16px; }
    .form-group-custom label { display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control-custom {
        width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid #e2e8f0;
        font-size: 13.5px; font-family: inherit;
    }
    .form-control-custom:focus { outline: none; border-color: #0284c7; box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1); }
    .btn-cancel-modal {
        padding: 9px 18px; border-radius: 10px; border: 1px solid #cbd5e1;
        background: white; color: #475569; font-weight: 700; font-size: 13.5px; cursor: pointer;
    }
    .btn-submit-modal {
        padding: 9px 22px; border-radius: 10px; border: none;
        background: #0284c7; color: white; font-weight: 700; font-size: 13.5px; cursor: pointer;
    }
    .btn-submit-modal:hover { background: #0369a1; }

    @media (max-width: 1100px) {
        .kpi-row { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .kpi-row { grid-template-columns: 1fr; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-inputs-row { flex-direction: column; width: 100%; }
        .search-wrap { width: 100%; }
        .select-filter-mapel { width: 100%; }
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }
    .pag-text {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }
    .pag-pills {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .pag-pills a, .pag-pills span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .pag-pills a {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .pag-pills a:hover {
        background: #e0f2fe;
        color: #0284c7;
        border-color: #bae6fd;
    }
    .pag-pills span.active {
        background: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }
    .pag-pills span.disabled {
        background: #f8fafc;
        color: #cbd5e1;
        border: 1px solid #f1f5f9;
        cursor: not-allowed;
    }

    /* ============================================================
       Executive Unassigned Mapel Panel Styles
       ============================================================ */
    .unassigned-panel-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        margin-top: 24px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .unassigned-header {
        padding: 18px 24px;
        background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .unassigned-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        min-width: 280px;
    }
    .unassigned-icon-badge {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #fff1f2;
        color: #e11d48;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
        border: 1px solid #fecdd3;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.08);
    }
    .unassigned-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .unassigned-title-row h3 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .unassigned-total-badge {
        font-size: 11px;
        font-weight: 800;
        background: #fee2e2;
        color: #e11d48;
        border: 1px solid #fecaca;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }
    .unassigned-subtitle {
        font-size: 12px;
        color: #64748b;
        margin-top: 3px;
        line-height: 1.4;
    }

    .unassigned-header-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .unassigned-search-box {
        position: relative;
        width: 240px;
    }
    .unassigned-search-box .search-ico {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
        pointer-events: none;
    }
    .unassigned-search-box input {
        width: 100%;
        padding: 8px 30px 8px 34px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        font-size: 12.5px;
        font-family: inherit;
        background: #f8fafc;
        color: #0f172a;
        outline: none;
        transition: all 0.2s;
    }
    .unassigned-search-box input:focus {
        border-color: #0284c7;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
    }
    .btn-clear-search {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        font-size: 12px;
    }
    .btn-clear-search:hover { color: #0f172a; }

    .unassigned-view-toggles {
        display: flex;
        align-items: center;
        gap: 4px;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 10px;
    }
    .btn-view-toggle {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: all 0.15s ease;
    }
    .btn-view-toggle.active {
        background: #ffffff;
        color: #0284c7;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .btn-view-toggle:hover:not(.active) {
        color: #0f172a;
    }

    /* Filter Toolbar */
    .unassigned-filter-toolbar {
        padding: 10px 24px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .unassigned-tabs {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .unassigned-tab {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .unassigned-tab:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .unassigned-tab.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    .unassigned-tab .tab-count {
        background: rgba(0,0,0,0.07);
        padding: 1px 6px;
        border-radius: 6px;
        font-size: 10.5px;
    }
    .unassigned-tab.active .tab-count {
        background: rgba(255,255,255,0.22);
        color: #ffffff;
    }
    .unassigned-tab .dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .unassigned-tab .dot.normatif { background: #10b981; }
    .unassigned-tab .dot.adaptif { background: #f59e0b; }
    .unassigned-tab .dot.produktif { background: #8b5cf6; }
    .unassigned-tab .dot.mulok { background: #ec4899; }

    .unassigned-status-info {
        font-size: 12px;
        color: #64748b;
    }

    /* Body Container */
    .unassigned-body {
        padding: 18px 24px;
        background: #fafbfd;
        transition: all 0.3s ease;
    }

    /* Grid Layout */
    .unassigned-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 14px;
        max-height: 480px;
        overflow-y: auto;
        padding: 2px 4px 6px;
    }
    .unassigned-grid-container::-webkit-scrollbar,
    .unassigned-table-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .unassigned-grid-container::-webkit-scrollbar-track,
    .unassigned-table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    .unassigned-grid-container::-webkit-scrollbar-thumb,
    .unassigned-table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }

    /* Individual Subject Card */
    .unassigned-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 10px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    .unassigned-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        border-color: #0284c7;
    }
    .unassigned-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .unassigned-code {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
        font-size: 11px;
        font-weight: 800;
        background: #f0f9ff;
        color: #0284c7;
        padding: 2px 7px;
        border-radius: 6px;
        border: 1px solid #bae6fd;
        letter-spacing: 0.3px;
    }
    .unassigned-badge-group {
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
    }
    .unassigned-badge-group.Normatif { background: #ecfdf5; color: #059669; }
    .unassigned-badge-group.Adaptif { background: #fffbeb; color: #d97706; }
    .unassigned-badge-group.Produktif { background: #f5f3ff; color: #7c3aed; }
    .unassigned-badge-group.Muatan_Lokal { background: #fdf2f8; color: #db2777; }

    .unassigned-card-mid {
        flex: 1;
        min-height: 38px;
        display: flex;
        align-items: center;
    }
    .unassigned-name {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .unassigned-card-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px solid #f8fafc;
        gap: 8px;
    }
    .unassigned-status-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: #e11d48;
    }
    .status-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #e11d48;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.4);
        animation: pulseRedDot 1.8s infinite;
    }
    @keyframes pulseRedDot {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.4); }
        70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(225, 29, 72, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
    }

    .btn-plot-trigger {
        background: #0284c7;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.18);
    }
    .btn-plot-trigger:hover {
        background: #0369a1;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.28);
    }

    /* Table View */
    .unassigned-table-container {
        max-height: 480px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow-x: auto;
    }
    .unassigned-compact-table {
        width: 100%;
        border-collapse: collapse;
    }
    .unassigned-compact-table th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        padding: 11px 16px;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        z-index: 2;
    }
    .unassigned-compact-table td {
        padding: 12px 16px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .unassigned-compact-table tr:hover td {
        background: #fafcff;
    }
    .btn-plot-trigger-sm {
        background: #0284c7;
        color: #ffffff;
        border: none;
        border-radius: 7px;
        padding: 5px 12px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s ease;
    }
    .btn-plot-trigger-sm:hover {
        background: #0369a1;
    }

    /* Empty State */
    .unassigned-empty-state {
        text-align: center;
        padding: 42px 20px;
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        margin: 6px 0;
    }

    @media (max-width: 768px) {
        .unassigned-header {
            flex-direction: column;
            align-items: stretch;
        }
        .unassigned-header-right {
            flex-direction: column;
            align-items: stretch;
        }
        .unassigned-search-box {
            width: 100%;
        }
        .unassigned-grid-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div>
    <!-- KPI Row -->
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-icon total"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-info">
                <h4>{{ $totalGuruCount }}</h4>
                <p>Total Guru Aktif</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon memenuhi"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-info">
                <h4>{{ $memenuhiCount }}</h4>
                <p>Memenuhi Standar (>=24 JP)</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon kurang"><i class="fa-solid fa-circle-exclamation"></i></div>
            <div class="kpi-info">
                <h4>{{ $kurangCount }}</h4>
                <p>Kurang Jam (<24 JP)</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon tinggi"><i class="fa-solid fa-bolt"></i></div>
            <div class="kpi-info">
                <h4>{{ $tinggiCount }}</h4>
                <p>Beban Tinggi (>32 JP)</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon kosong"><i class="fa-solid fa-user-clock"></i></div>
            <div class="kpi-info">
                <h4>{{ $kosongCount }}</h4>
                <p>Belum Ada Jam (0 JP)</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-tabs">
            <a href="{{ route('waka-kurikulum.guru-mengajar.index', array_merge(request()->except('status_beban'), ['status_beban' => 'all'])) }}"
               class="filter-tab {{ ($statusBeban === 'all' || !$statusBeban) ? 'active' : '' }}">
                <span>Semua</span>
                <span class="badge-count">{{ $totalGuruCount }}</span>
            </a>
            <a href="{{ route('waka-kurikulum.guru-mengajar.index', array_merge(request()->except('status_beban'), ['status_beban' => 'memenuhi'])) }}"
               class="filter-tab {{ $statusBeban === 'memenuhi' ? 'active' : '' }}">
                <span>>= 24 JP</span>
                <span class="badge-count">{{ $memenuhiCount }}</span>
            </a>
            <a href="{{ route('waka-kurikulum.guru-mengajar.index', array_merge(request()->except('status_beban'), ['status_beban' => 'kurang'])) }}"
               class="filter-tab {{ $statusBeban === 'kurang' ? 'active' : '' }}">
                <span>< 24 JP</span>
                <span class="badge-count">{{ $kurangCount }}</span>
            </a>
            <a href="{{ route('waka-kurikulum.guru-mengajar.index', array_merge(request()->except('status_beban'), ['status_beban' => 'tinggi'])) }}"
               class="filter-tab {{ $statusBeban === 'tinggi' ? 'active' : '' }}">
                <span>> 32 JP</span>
                <span class="badge-count">{{ $tinggiCount }}</span>
            </a>
            <a href="{{ route('waka-kurikulum.guru-mengajar.index', array_merge(request()->except('status_beban'), ['status_beban' => 'kosong'])) }}"
               class="filter-tab {{ $statusBeban === 'kosong' ? 'active' : '' }}">
                <span>0 JP</span>
                <span class="badge-count">{{ $kosongCount }}</span>
            </a>
        </div>

        <div class="filter-inputs-row">
            <form action="{{ route('waka-kurikulum.guru-mengajar.index') }}" method="GET" style="display:flex;gap:10px;flex-wrap:wrap;">
                @if($statusBeban)
                    <input type="hidden" name="status_beban" value="{{ $statusBeban }}">
                @endif
                <select name="id_mapel" class="select-filter-mapel" onchange="this.form.submit()">
                    <option value="">-- Filter Mata Pelajaran --</option>
                    @foreach($mapelList as $m)
                        <option value="{{ $m->id_mapel }}" {{ $idMapel == $m->id_mapel ? 'selected' : '' }}>
                            {{ $m->nama_mapel }}
                        </option>
                    @endforeach
                </select>

                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" class="search-input" placeholder="Cari nama / NIP guru..." value="{{ $search }}">
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-header-custom">
            <div class="table-title">
                <i class="fa-solid fa-chalkboard-user" style="color:#0284c7;"></i>
                <span>Rekap Pembagian Tugas & Beban Mengajar Guru</span>
                <span style="font-size:12px;background:#e0f2fe;color:#0284c7;padding:2px 10px;border-radius:8px;font-weight:700;">
                    Rata-rata: {{ $avgJpSekolah }} JP / Guru
                </span>
            </div>
            <div style="font-size:12px;color:#64748b;font-weight:600;">
                Tahun Ajaran: <strong style="color:#0f172a;">{{ $tahunAjaranAktif->nama ?? 'Aktif' }}</strong>
            </div>
        </div>

        <div class="table-responsive-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Guru Pengajar</th>
                        <th>Mata Pelajaran yang Diampu</th>
                        <th>Kelas yang Diajar</th>
                        <th style="text-align: center; width: 100px;">Beban Jam</th>
                        <th style="width: 170px;">Status Standar</th>
                        <th style="text-align: center; width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedGuru as $index => $guru)
                        <tr>
                            <td>{{ $paginatedGuru->firstItem() + $index }}</td>
                            <td>
                                <div class="guru-profile-cell">
                                    <div class="guru-avatar-circle">
                                        {{ substr($guru->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="guru-meta-name">{{ $guru->nama_lengkap }}</div>
                                        <div class="guru-meta-sub">NIP: {{ $guru->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(count($guru->mapel_summary) > 0)
                                    <div class="mapel-pill-group">
                                        @foreach($guru->mapel_summary as $mapel)
                                            <span class="mapel-pill {{ $mapel['kelompok'] }}" title="{{ $mapel['nama_mapel'] }} ({{ $mapel['jp'] }} JP)">
                                                <i class="fa-solid fa-book" style="font-size:10px;"></i>
                                                <span>{{ Str::limit($mapel['nama_mapel'], 18) }}</span>
                                                <strong style="margin-left:2px;background:rgba(0,0,0,0.1);padding:0 5px;border-radius:4px;">{{ $mapel['jp'] }} JP</strong>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size:12px;color:#94a3b8;font-style:italic;">Belum ada alokasi mapel</span>
                                @endif
                            </td>
                            <td>
                                @if(count($guru->kelas_summary) > 0)
                                    <div class="kelas-pill-group">
                                        @foreach($guru->kelas_summary->take(4) as $kelas)
                                            <span class="kelas-pill-sm">{{ $kelas->nama_kelas }}</span>
                                        @endforeach
                                        @if(count($guru->kelas_summary) > 4)
                                            <span class="kelas-pill-sm" style="background:#0284c7;color:white;">+{{ count($guru->kelas_summary) - 4 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="font-size:12px;color:#94a3b8;">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span style="font-size: 16px; font-weight: 800; color: #0284c7;">
                                    {{ $guru->total_jp }}
                                </span>
                                <span style="font-size: 11px; color: #94a3b8; display: block;">JP / minggu</span>
                            </td>
                            <td>
                                <span class="status-badge-beban {{ $guru->status_badge }}">
                                    @if($guru->beban_status === 'memenuhi')
                                        <i class="fa-solid fa-circle-check"></i>
                                    @elseif($guru->beban_status === 'kurang')
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    @elseif($guru->beban_status === 'tinggi')
                                        <i class="fa-solid fa-bolt"></i>
                                    @else
                                        <i class="fa-solid fa-circle-minus"></i>
                                    @endif
                                    <span>{{ $guru->status_label }}</span>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ route('waka-kurikulum.guru-mengajar.detail', $guru->id_guru) }}" class="btn-action-icon info" title="Lihat Profil & Jadwal Mengajar">
                                        <i class="fa-solid fa-calendar-week"></i>
                                    </a>
                                    <button type="button" class="btn-action-icon edit" title="Tambah Jam Mengajar (Plotting)" onclick="quickPlottingForGuru({{ $guru->id_guru }}, '{{ addslashes($guru->nama_lengkap) }}')">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                                <i class="fa-solid fa-user-xmark" style="font-size: 40px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                                <span style="font-size: 14px; font-weight: 600;">Tidak ada data guru yang cocok dengan filter.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedGuru->hasPages())
        <div class="pagination-container">
            <span class="pag-text">
                Menampilkan <b>{{ $paginatedGuru->firstItem() ?? 0 }}</b>–<b>{{ $paginatedGuru->lastItem() ?? 0 }}</b> dari <b>{{ $paginatedGuru->total() }}</b> guru
            </span>
            <div class="pag-pills">
                @if($paginatedGuru->onFirstPage())
                    <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                @else
                    <a href="{{ $paginatedGuru->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                @endif

                @foreach($paginatedGuru->getUrlRange(max(1, $paginatedGuru->currentPage() - 2), min($paginatedGuru->lastPage(), $paginatedGuru->currentPage() + 2)) as $page => $url)
                    @if($page == $paginatedGuru->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($paginatedGuru->hasMorePages())
                    <a href="{{ $paginatedGuru->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                @else
                    <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ============ PANEL: Ringkasan Mapel tanpa Pengampu (Executive Workspace) ============ --}}
    @php
        $mapelTanpaPengampu = $mapelList->filter(fn($m) => !$m->jadwalPelajaran->count())->values();
        $countNormatif = $mapelTanpaPengampu->where('kelompok', 'Normatif')->count();
        $countAdaptif = $mapelTanpaPengampu->where('kelompok', 'Adaptif')->count();
        $countProduktif = $mapelTanpaPengampu->where('kelompok', 'Produktif')->count();
        $countMulok = $mapelTanpaPengampu->where('kelompok', 'Muatan_Lokal')->count();
    @endphp
    @if($mapelTanpaPengampu->count() > 0)
    <div class="unassigned-panel-card" id="unassignedPanel">
        <!-- Header -->
        <div class="unassigned-header">
            <div class="unassigned-header-left">
                <div class="unassigned-icon-badge">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="unassigned-title-row">
                        <h3>Mata Pelajaran Belum Memiliki Guru Pengampu</h3>
                        <span class="unassigned-total-badge">{{ $mapelTanpaPengampu->count() }} Perlu Plotting</span>
                    </div>
                    <p class="unassigned-subtitle">
                        Mata pelajaran ini belum dialokasikan ke jadwal guru manapun. Anda dapat memfilter, mencari, atau langsung melakukan penugasan (plotting).
                    </p>
                </div>
            </div>
            <div class="unassigned-header-right">
                <div class="unassigned-search-box">
                    <i class="fa-solid fa-magnifying-glass search-ico"></i>
                    <input type="text" id="unassignedSearchInput" placeholder="Cari kode atau nama mapel..." oninput="filterUnassignedMapel()">
                    <button type="button" id="unassignedClearSearch" class="btn-clear-search" onclick="clearUnassignedSearch()" style="display:none;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="unassigned-view-toggles">
                    <button type="button" class="btn-view-toggle active" id="btnViewGrid" onclick="setUnassignedView('grid')" title="Tampilan Kartu Grid">
                        <i class="fa-solid fa-grip"></i>
                    </button>
                    <button type="button" class="btn-view-toggle" id="btnViewTable" onclick="setUnassignedView('table')" title="Tampilan Tabel">
                        <i class="fa-solid fa-table-list"></i>
                    </button>
                    <button type="button" class="btn-view-toggle" id="btnCollapsePanel" onclick="toggleUnassignedCollapse()" title="Kecilkan / Perluas Panel">
                        <i class="fa-solid fa-chevron-up" id="iconCollapsePanel"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="unassigned-filter-toolbar" id="unassignedToolbar">
            <div class="unassigned-tabs">
                <button type="button" class="unassigned-tab active" data-filter="all" onclick="filterUnassignedKelompok('all', this)">
                    <span>Semua</span>
                    <span class="tab-count">{{ $mapelTanpaPengampu->count() }}</span>
                </button>
                @if($countNormatif > 0)
                <button type="button" class="unassigned-tab" data-filter="Normatif" onclick="filterUnassignedKelompok('Normatif', this)">
                    <span class="dot normatif"></span>
                    <span>Normatif</span>
                    <span class="tab-count">{{ $countNormatif }}</span>
                </button>
                @endif
                @if($countAdaptif > 0)
                <button type="button" class="unassigned-tab" data-filter="Adaptif" onclick="filterUnassignedKelompok('Adaptif', this)">
                    <span class="dot adaptif"></span>
                    <span>Adaptif</span>
                    <span class="tab-count">{{ $countAdaptif }}</span>
                </button>
                @endif
                @if($countProduktif > 0)
                <button type="button" class="unassigned-tab" data-filter="Produktif" onclick="filterUnassignedKelompok('Produktif', this)">
                    <span class="dot produktif"></span>
                    <span>Produktif</span>
                    <span class="tab-count">{{ $countProduktif }}</span>
                </button>
                @endif
                @if($countMulok > 0)
                <button type="button" class="unassigned-tab" data-filter="Muatan_Lokal" onclick="filterUnassignedKelompok('Muatan_Lokal', this)">
                    <span class="dot mulok"></span>
                    <span>Muatan Lokal</span>
                    <span class="tab-count">{{ $countMulok }}</span>
                </button>
                @endif
            </div>

            <div class="unassigned-status-info">
                Menampilkan <strong id="unassignedCountDisplay">{{ $mapelTanpaPengampu->count() }}</strong> dari {{ $mapelTanpaPengampu->count() }} mapel
            </div>
        </div>

        <!-- Body Content -->
        <div class="unassigned-body" id="unassignedBody">
            <!-- Grid View (Default) -->
            <div class="unassigned-grid-container" id="unassignedGridView">
                @foreach($mapelTanpaPengampu as $m)
                <div class="unassigned-card item-unassigned"
                     data-kelompok="{{ $m->kelompok }}"
                     data-search="{{ strtolower($m->kode_mapel . ' ' . $m->nama_mapel . ' ' . str_replace('_', ' ', $m->kelompok)) }}">
                    <div class="unassigned-card-top">
                        <span class="unassigned-code">{{ $m->kode_mapel }}</span>
                        <span class="unassigned-badge-group {{ $m->kelompok }}">
                            {{ str_replace('_', ' ', $m->kelompok) }}
                        </span>
                    </div>

                    <div class="unassigned-card-mid">
                        <div class="unassigned-name" title="{{ $m->nama_mapel }}">
                            {{ $m->nama_mapel }}
                        </div>
                    </div>

                    <div class="unassigned-card-bottom">
                        <div class="unassigned-status-tag">
                            <span class="status-pulse-dot"></span>
                            <span>Belum ada pengampu</span>
                        </div>
                        <button type="button" class="btn-plot-trigger"
                                onclick="quickPlottingForMapel({{ $m->id_mapel }}, '{{ addslashes($m->nama_mapel) }}')">
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>Plotting</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Table View (Toggled) -->
            <div class="unassigned-table-container" id="unassignedTableView" style="display: none;">
                <table class="unassigned-compact-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 120px;">Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <th style="width: 160px;">Kelompok Kurikulum</th>
                            <th style="width: 170px;">Status Penugasan</th>
                            <th style="width: 130px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mapelTanpaPengampu as $idx => $m)
                        <tr class="item-unassigned-row"
                            data-kelompok="{{ $m->kelompok }}"
                            data-search="{{ strtolower($m->kode_mapel . ' ' . $m->nama_mapel . ' ' . str_replace('_', ' ', $m->kelompok)) }}">
                            <td class="row-num" style="color:#94a3b8; font-weight:600;">{{ $idx + 1 }}</td>
                            <td>
                                <span class="unassigned-code">{{ $m->kode_mapel }}</span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #0f172a;">{{ $m->nama_mapel }}</span>
                            </td>
                            <td>
                                <span class="unassigned-badge-group {{ $m->kelompok }}">
                                    {{ str_replace('_', ' ', $m->kelompok) }}
                                </span>
                            </td>
                            <td>
                                <div class="unassigned-status-tag">
                                    <span class="status-pulse-dot"></span>
                                    <span>0 Pengampu</span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-plot-trigger-sm"
                                        onclick="quickPlottingForMapel({{ $m->id_mapel }}, '{{ addslashes($m->nama_mapel) }}')">
                                    <i class="fa-solid fa-calendar-plus"></i> Plotting
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Empty Search State -->
            <div id="unassignedEmptySearch" class="unassigned-empty-state" style="display: none;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                <p style="font-weight: 700; color: #334155; font-size: 14px; margin-bottom: 4px;">Tidak ada mata pelajaran yang cocok</p>
                <p style="font-size: 12.5px; color: #94a3b8; margin: 0;">Coba gunakan kata kunci lain atau pilih tab kelompok kurikulum yang berbeda.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- ================= MODAL PLOTTING GURU MENGAJAR ================= -->
<div class="custom-modal-backdrop" id="modalPlotting">
    <div class="custom-modal-box">
        <div class="modal-hdr">
            <h3><i class="fa-solid fa-calendar-plus"></i> Plotting Tugas Mengajar Guru</h3>
            <button type="button" class="modal-hdr-close" onclick="closePlottingModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('waka-kurikulum.guru-mengajar.plotting') }}" method="POST">
            @csrf
            <div class="modal-bdy">
                <div class="form-group-custom">
                    <label for="plot_id_guru">Guru Pengajar <span style="color:#ef4444;">*</span></label>
                    <select name="id_guru" id="plot_id_guru" class="form-control-custom" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id_guru }}">{{ $g->nama_lengkap }} ({{ $g->nip ?? 'No NIP' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="plot_id_mapel">Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                    <select name="id_mapel" id="plot_id_mapel" class="form-control-custom" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="plot_id_kelas">Kelas Rombel <span style="color:#ef4444;">*</span></label>
                    <select name="id_kelas" id="plot_id_kelas" class="form-control-custom" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group-custom">
                        <label for="plot_hari">Hari Pembelajaran <span style="color:#ef4444;">*</span></label>
                        <select name="hari" id="plot_hari" class="form-control-custom" required onchange="updateJamSuggestion()">
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label for="plot_jam_ke">Jam Pelajaran Ke <span style="color:#ef4444;">*</span></label>
                        <select name="jam_ke" id="plot_jam_ke" class="form-control-custom" required onchange="updateJamSuggestion()">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">Jam ke-{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group-custom">
                        <label for="plot_jam_mulai">Jam Mulai (Opsional)</label>
                        <input type="time" name="jam_mulai" id="plot_jam_mulai" class="form-control-custom">
                    </div>
                    <div class="form-group-custom">
                        <label for="plot_jam_selesai">Jam Selesai (Opsional)</label>
                        <input type="time" name="jam_selesai" id="plot_jam_selesai" class="form-control-custom">
                    </div>
                </div>
                <div style="font-size:11.5px;color:#64748b;margin-top:-6px;">
                    <i class="fa-solid fa-circle-info" style="color:#0284c7;"></i> Waktu mulai & selesai otomatis menggunakan slot jam resmi SMKN 1 Boyolangu jika dikosongkan.
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-cancel-modal" onclick="closePlottingModal()">Batal</button>
                <button type="submit" class="btn-submit-modal">Simpan Penugasan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openPlottingModal() {
        document.getElementById('modalPlotting').classList.add('open');
    }
    function closePlottingModal() {
        document.getElementById('modalPlotting').classList.remove('open');
    }

    function quickPlottingForGuru(idGuru, namaGuru) {
        document.getElementById('plot_id_guru').value = idGuru;
        openPlottingModal();
    }

    // Default slot suggestions for Boyolangu
    const slotTimesRegular = {
        1: ['07:00', '07:40'], 2: ['07:40', '08:20'], 3: ['08:20', '09:00'],
        4: ['09:00', '09:40'], 5: ['09:55', '10:35'], 6: ['10:35', '11:15'],
        7: ['11:15', '11:55'], 8: ['12:35', '13:15'], 9: ['13:15', '13:55'],
        10: ['13:55', '14:35'], 11: ['14:35', '15:15'], 12: ['15:15', '15:55']
    };
    const slotTimesJumat = {
        1: ['07:00', '07:30'], 2: ['07:30', '08:00'], 3: ['08:00', '08:30'],
        4: ['08:30', '09:00'], 5: ['09:00', '09:30'], 6: ['09:45', '10:15'],
        7: ['10:15', '10:45'], 8: ['10:45', '11:15'], 9: ['11:15', '11:45'],
        10: ['13:00', '13:30']
    };

    function updateJamSuggestion() {
        const hari = document.getElementById('plot_hari').value;
        const jamKe = parseInt(document.getElementById('plot_jam_ke').value);
        const isJumat = hari.toLowerCase() === 'jumat';
        const slots = isJumat ? slotTimesJumat : slotTimesRegular;

        if (slots[jamKe]) {
            document.getElementById('plot_jam_mulai').value = slots[jamKe][0];
            document.getElementById('plot_jam_selesai').value = slots[jamKe][1];
        }
    }

    document.addEventListener('DOMContentLoaded', updateJamSuggestion);

    // ==========================================
    // Interactive Unassigned Mapel Panel Logic
    // ==========================================
    let currentUnassignedKelompok = 'all';

    function filterUnassignedMapel() {
        const searchInput = document.getElementById('unassignedSearchInput');
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const clearBtn = document.getElementById('unassignedClearSearch');
        if (clearBtn) {
            clearBtn.style.display = query ? 'block' : 'none';
        }

        const cards = document.querySelectorAll('.item-unassigned');
        const rows = document.querySelectorAll('.item-unassigned-row');
        let visibleCount = 0;

        cards.forEach(card => {
            const itemKelompok = card.getAttribute('data-kelompok');
            const itemSearch = card.getAttribute('data-search') || '';

            const matchesKelompok = (currentUnassignedKelompok === 'all') || (itemKelompok === currentUnassignedKelompok);
            const matchesQuery = !query || itemSearch.includes(query);

            if (matchesKelompok && matchesQuery) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        let rowIdx = 1;
        rows.forEach(row => {
            const itemKelompok = row.getAttribute('data-kelompok');
            const itemSearch = row.getAttribute('data-search') || '';

            const matchesKelompok = (currentUnassignedKelompok === 'all') || (itemKelompok === currentUnassignedKelompok);
            const matchesQuery = !query || itemSearch.includes(query);

            if (matchesKelompok && matchesQuery) {
                row.style.display = '';
                const numEl = row.querySelector('.row-num');
                if (numEl) numEl.textContent = rowIdx++;
            } else {
                row.style.display = 'none';
            }
        });

        const countDisplay = document.getElementById('unassignedCountDisplay');
        if (countDisplay) {
            countDisplay.textContent = visibleCount;
        }

        const emptyState = document.getElementById('unassignedEmptySearch');
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    function filterUnassignedKelompok(kelompok, tabBtn) {
        currentUnassignedKelompok = kelompok;
        document.querySelectorAll('.unassigned-tab').forEach(btn => btn.classList.remove('active'));
        if (tabBtn) tabBtn.classList.add('active');
        filterUnassignedMapel();
    }

    function clearUnassignedSearch() {
        const input = document.getElementById('unassignedSearchInput');
        if (input) {
            input.value = '';
            input.focus();
        }
        filterUnassignedMapel();
    }

    function setUnassignedView(mode) {
        const gridView = document.getElementById('unassignedGridView');
        const tableView = document.getElementById('unassignedTableView');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnTable = document.getElementById('btnViewTable');

        if (mode === 'table') {
            if (gridView) gridView.style.display = 'none';
            if (tableView) tableView.style.display = 'block';
            if (btnGrid) btnGrid.classList.remove('active');
            if (btnTable) btnTable.classList.add('active');
        } else {
            if (gridView) gridView.style.display = 'grid';
            if (tableView) tableView.style.display = 'none';
            if (btnGrid) btnGrid.classList.add('active');
            if (btnTable) btnTable.classList.remove('active');
        }
    }

    function toggleUnassignedCollapse() {
        const body = document.getElementById('unassignedBody');
        const toolbar = document.getElementById('unassignedToolbar');
        const icon = document.getElementById('iconCollapsePanel');

        if (body.style.display === 'none') {
            body.style.display = 'block';
            if (toolbar) toolbar.style.display = 'flex';
            if (icon) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        } else {
            body.style.display = 'none';
            if (toolbar) toolbar.style.display = 'none';
            if (icon) {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }

    function quickPlottingForMapel(idMapel, namaMapel) {
        const select = document.getElementById('plot_id_mapel');
        if (select) {
            select.value = idMapel;
        }
        openPlottingModal();
    }
</script>
@endsection
