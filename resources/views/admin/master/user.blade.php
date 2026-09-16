@extends('layouts.admin')

@section('title', 'Manajemen User — SMKN 1 BOYOLANGU')
@section('header_title', 'Manajemen User')
@section('header_subtitle', 'Kelola data akun pengguna, hak akses per manajemen, dan status langsung dari database')

@section('styles')
<style>
    /* ── Toast Notification ── */
    .toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 99999;
        display: flex\\\\\\\
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }
    .toast {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border-radius: 14px;
        padding: 14px 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        border-left: 4px solid #10b981;
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        min-width: 320px;
        pointer-events: auto;
        animation: toastSlideIn .3s ease;
    }
    .toast.error { border-left-color: #ef4444; }
    .toast i { font-size: 18px; color: #10b981; }
    .toast.error i { color: #ef4444; }
    @keyframes toastSlideIn { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }

    /* ── Main Container ── */
    .user-page-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ── KPI Summary Cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s ease;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.07);
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
    .kpi-icon.indigo, .kpi-icon.total   { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #3730a3; }
    .kpi-icon.emerald, .kpi-icon.active { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .kpi-icon.violet, .kpi-icon.admin   { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #5b21b6; }
    .kpi-icon.sky, .kpi-icon.guru       { background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0369a1; }
    
    .kpi-val { font-size: 26px; font-weight: 800; color: #0f172a; line-height: 1.1; letter-spacing: -0.5px; }
    .kpi-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-sub { font-size: 11.5px; font-weight: 500; color: #94a3b8; margin-top: 2px; }

    /* ── Role Navigation Tabs ── */
    .role-tabs-wrap {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }
    .role-tabs-wrap::-webkit-scrollbar { height: 4px; }
    .role-tabs-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .role-tab-btn {
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
        flex-shrink: 0;
    }
    .role-tab-btn:hover {
        background: #f8fafc;
        color: #2b43b9;
        border-color: #cbd5e1;
    }
    .role-tab-btn.active {
        background: #2b43b9;
        color: #ffffff;
        border-color: #2b43b9;
        box-shadow: 0 4px 14px rgba(43, 67, 185, 0.25);
    }
    .role-tab-count {
        background: rgba(0, 0, 0, 0.06);
        color: inherit;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 20px;
    }
    .role-tab-btn.active .role-tab-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* ── Action & Filter Card ── */
    .page-action-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 16px 22px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-add-user {
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
        box-shadow: 0 4px 14px rgba(43, 67, 185, 0.25);
        white-space: nowrap;
    }
    .btn-add-user:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(43, 67, 185, 0.35);
        color: #ffffff;
    }

    .filter-group {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        flex: 1;
    }
    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
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
        min-width: 180px;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: #2b43b9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(43, 67, 185, 0.1);
    }

    .search-input-wrap { position: relative; }
    .search-input-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }
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
    .btn-filter-action:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* ── Table Card ── */
    .table-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
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
    .table-title {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .table-title i { color: #2b43b9; }
    .count-badge {
        font-size: 12px;
        font-weight: 700;
        color: #2b43b9;
        background: #eaeff8;
        padding: 5px 14px;
        border-radius: 20px;
    }

    .table-responsive-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .data-table th {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.6px;
        padding: 12px 16px;
        text-align: left;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .data-table th:first-child { border-radius: 10px 0 0 10px; }
    .data-table th:last-child  { border-radius: 0 10px 10px 0; text-align: right; }
    
    .data-table td {
        padding: 16px;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        transition: background 0.15s ease;
    }
    .data-table tbody tr:hover td { background: #f8fafc; }
    .data-table td:last-child { text-align: right; }

    /* User Profile Info in Table */
    .user-info-box { display: flex; align-items: center; gap: 12px; }
    .user-avatar-circle {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        object-fit: cover;
        background: #eaeff8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        color: #2b43b9;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }
    .user-name-text { font-weight: 700; color: #0f172a; font-size: 14px; line-height: 1.2; }
    .user-email-text { font-size: 12px; color: #64748b; margin-top: 3px; }

    /* Role Badges */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }
    .role-badge.admin          { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .role-badge.wali_kelas     { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .role-badge.guru_mapel     { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .role-badge.guru_piket     { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .role-badge.waka_kurikulum { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .role-badge.waka_sdm       { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .role-badge.wali_murid     { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .role-badge.kepala_sekolah { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .role-badge.waka           { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .role-badge.satpam         { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .status-badge.active   { background: #d1fae5; color: #065f46; }
    .status-badge.inactive { background: #fee2e2; color: #991b1b; }
    .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-badge.active .status-dot   { background: #10b981; box-shadow: 0 0 6px rgba(16, 185, 129, 0.6); }
    .status-badge.inactive .status-dot { background: #ef4444; box-shadow: 0 0 6px rgba(239, 68, 68, 0.6); }

    /* Action Buttons */
    .action-btn-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }
    .btn-tbl-action {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13.5px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-tbl-action:hover {
        border-color: #cbd5e1;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .btn-tbl-action.view:hover   { background: #f0fdf4; border-color: #86efac; color: #16a34a; }
    .btn-tbl-action.edit:hover   { background: #eff6ff; border-color: #93c5fd; color: #2563eb; }
    .btn-tbl-action.toggle:hover { background: #fef3c7; border-color: #fde68a; color: #b45309; }
    .btn-tbl-action.delete:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

    /* Empty state */
    .table-empty-cell {
        text-align: center;
        padding: 56px 16px;
        color: #94a3b8;
    }
    .empty-icon-box {
        font-size: 40px;
        margin-bottom: 12px;
        color: #cbd5e1;
    }
    .empty-title {
        font-weight: 700;
        font-size: 15px;
        color: #475569;
    }
    .empty-desc {
        font-size: 13px;
        margin-top: 4px;
    }

    /* ── Pagination ── */
    .table-pagination-footer {
        padding: 16px 4px 4px 4px;
        border-top: 1px solid #f1f5f9;
        margin-top: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .pag-text { font-size: 13px; color: #64748b; font-weight: 600; }

    /* ── Modal Styling ── */
    .custom-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .custom-modal-backdrop.show {
        display: flex;
        animation: fadeIn .2s ease;
    }
    .custom-modal {
        background: #ffffff;
        border-radius: 24px;
        width: 100%;
        max-width: 560px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: scaleUp .25s ease;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    .modal-hdr {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-title-text {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-close-btn {
        background: none;
        border: none;
        font-size: 18px;
        color: #94a3b8;
        cursor: pointer;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .modal-close-btn:hover { background: #f1f5f9; color: #0f172a; }

    .modal-body {
        padding: 24px;
        max-height: 75vh;
        overflow-y: auto;
    }
    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    .form-group-custom { margin-bottom: 16px; }
    .form-label-custom {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-label-custom span { color: #ef4444; }
    
    .form-ctrl-custom {
        width: 100%;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13.5px;
        font-weight: 600;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
    }
    .form-ctrl-custom:focus {
        border-color: #2b43b9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(43, 67, 185, 0.1);
    }

    .modal-ftr {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }
    .btn-modal-cancel {
        padding: 10px 18px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-modal-cancel:hover { background: #e2e8f0; color: #0f172a; }

    .btn-modal-submit {
        padding: 10px 22px;
        border-radius: 12px;
        border: none;
        background: #2b43b9;
        color: #ffffff;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(43, 67, 185, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .btn-modal-submit:hover {
        background: #1e3399;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(43, 67, 185, 0.35);
    }

    .btn-modal-delete {
        padding: 10px 22px;
        border-radius: 12px;
        border: none;
        background: #ef4444;
        color: #ffffff;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .btn-modal-delete:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
    }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .form-row-2 { grid-template-columns: 1fr; }
        .filter-group { flex-direction: column; align-items: stretch; }
        .filter-input, .filter-select { min-width: 100%; }
        .btn-add-user { width: 100%; justify-content: center; }
    }
</style>
@endsection

@section('content')

{{-- Flash Toast Notifications --}}
<div class="toast-container" id="toastContainer">
    @if(session('success'))
        <div class="toast">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error') || (isset($errors) && $errors->any()))
        <div class="toast error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') ?? ($errors->first() ?? '') }}</span>
        </div>
    @endif
</div>

<div class="user-page-wrapper">

    {{-- KPI Summary Cards --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon indigo">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="kpi-val">{{ number_format($totalUser, 0, ',', '.') }}</div>
                <div class="kpi-lbl">Total Akun</div>
                <div class="kpi-sub">Terdaftar di Database</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon emerald">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <div class="kpi-val">{{ number_format($totalActive, 0, ',', '.') }}</div>
                <div class="kpi-lbl">Akun Aktif</div>
                <div class="kpi-sub">{{ $totalUser > 0 ? round(($totalActive / $totalUser) * 100) : 0 }}% Akses Diizinkan</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon violet">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <div class="kpi-val">{{ number_format($totalAdmin, 0, ',', '.') }}</div>
                <div class="kpi-lbl">Administrator</div>
                <div class="kpi-sub">Hak Akses Penuh Sistem</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon sky">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div>
                <div class="kpi-val">{{ number_format($totalGuru, 0, ',', '.') }}</div>
                <div class="kpi-lbl">Dewan Guru</div>
                <div class="kpi-sub">Wali &amp; Pengampu Mapel</div>
            </div>
        </div>
    </div>

    {{-- Quick Role Tabs (Melihat User di Tiap Manajemen) --}}
    <div class="role-tabs-wrap">
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'all'])) }}" class="role-tab-btn {{ empty($role) || $role === 'all' ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i>
            <span>Semua</span>
            <span class="role-tab-count">{{ $roleCounts['all'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}" class="role-tab-btn {{ $role === 'admin' ? 'active' : '' }}">
            <i class="fa-solid fa-user-shield"></i>
            <span>Admin</span>
            <span class="role-tab-count">{{ $roleCounts['admin'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'wali_kelas'])) }}" class="role-tab-btn {{ $role === 'wali_kelas' ? 'active' : '' }}">
            <i class="fa-solid fa-user-tie"></i>
            <span>Wali Kelas</span>
            <span class="role-tab-count">{{ $roleCounts['wali_kelas'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'guru_mapel'])) }}" class="role-tab-btn {{ $role === 'guru_mapel' ? 'active' : '' }}">
            <i class="fa-solid fa-book-open-reader"></i>
            <span>Guru Mapel</span>
            <span class="role-tab-count">{{ $roleCounts['guru_mapel'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'guru_piket'])) }}" class="role-tab-btn {{ $role === 'guru_piket' ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-check"></i>
            <span>Guru Piket</span>
            <span class="role-tab-count">{{ $roleCounts['guru_piket'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'waka_kurikulum'])) }}" class="role-tab-btn {{ $role === 'waka_kurikulum' ? 'active' : '' }}">
            <i class="fa-solid fa-book-bookmark"></i>
            <span>Waka Kurikulum</span>
            <span class="role-tab-count">{{ $roleCounts['waka_kurikulum'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'waka_sdm'])) }}" class="role-tab-btn {{ $role === 'waka_sdm' ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear"></i>
            <span>Waka SDM</span>
            <span class="role-tab-count">{{ $roleCounts['waka_sdm'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'wali_murid'])) }}" class="role-tab-btn {{ $role === 'wali_murid' ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            <span>Wali Murid</span>
            <span class="role-tab-count">{{ $roleCounts['wali_murid'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'kepala_sekolah'])) }}" class="role-tab-btn {{ $role === 'kepala_sekolah' ? 'active' : '' }}">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>Kepala Sekolah</span>
            <span class="role-tab-count">{{ $roleCounts['kepala_sekolah'] }}</span>
        </a>
        <a href="{{ route('admin.master.user', array_merge(request()->except('role', 'page'), ['role' => 'satpam'])) }}" class="role-tab-btn {{ $role === 'satpam' ? 'active' : '' }}">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Satpam</span>
            <span class="role-tab-count">{{ $roleCounts['satpam'] }}</span>
        </a>
    </div>

    {{-- Filter & Action Bar --}}
    <div class="page-action-card">
        <form action="{{ route('admin.master.user') }}" method="GET" class="filter-group">
            @if(!empty($role) && $role !== 'all')
                <input type="hidden" name="role" value="{{ $role }}">
            @endif

            <div class="filter-item">
                <span class="filter-label">Cari Pengguna</span>
                <div class="search-input-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ $search }}" class="filter-input" placeholder="Nama atau email user...">
                </div>
            </div>

            <div class="filter-item">
                <span class="filter-label">Status Akun</span>
                <select name="status" class="filter-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ $status === '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>

            <div class="filter-item">
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-filter-action">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    @if($search || ($status !== null && $status !== '') || ($role && $role !== 'all'))
                        <a href="{{ route('admin.master.user') }}" class="btn-filter-action" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <button type="button" class="btn-add-user" onclick="openAddModal()">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah User Baru</span>
        </button>
    </div>

    {{-- Table Section --}}
    <div class="table-card">
        <div class="table-hdr">
            <div class="table-title">
                <i class="fa-solid fa-users-gear"></i>
                <span>Daftar Akun Pengguna</span>
                <span class="count-badge">{{ $userList->total() }} User</span>
            </div>
        </div>

        <div class="table-responsive-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Pengguna</th>
                        <th>Role / Manajemen</th>
                        <th>Status Akun</th>
                        <th>Tanggal Terdaftar</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userList as $index => $u)
                        @php
                            $roleLabel = match($u->role) {
                                'admin'          => 'Administrator',
                                'wali_kelas'     => 'Wali Kelas',
                                'guru_mapel'     => 'Guru Mapel',
                                'guru_piket'     => 'Guru Piket',
                                'waka_kurikulum' => 'Waka Kurikulum',
                                'waka_sdm'       => 'Waka SDM',
                                'wali_murid'     => 'Wali Murid',
                                'kepala_sekolah' => 'Kepala Sekolah',
                                'waka'           => 'Waka',
                                'satpam'         => 'Satpam',
                                default          => ucfirst(str_replace('_', ' ', $u->role))
                            };
                            
                            $roleIcon = match($u->role) {
                                'admin'          => 'fa-user-shield',
                                'wali_kelas'     => 'fa-user-tie',
                                'guru_mapel'     => 'fa-book-open-reader',
                                'guru_piket'     => 'fa-clipboard-check',
                                'waka_kurikulum' => 'fa-book-bookmark',
                                'waka_sdm'       => 'fa-user-gear',
                                'wali_murid'     => 'fa-users',
                                'kepala_sekolah' => 'fa-graduation-cap',
                                'waka'           => 'fa-briefcase',
                                'satpam'         => 'fa-shield-halved',
                                default          => 'fa-user'
                            };

                            $avatarUrl = $u->photo 
                                ? Storage::url($u->photo) 
                                : 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=eaeff8&color=2b43b9&bold=true&rounded=true';
                        @endphp
                        <tr>
                            <td style="color: #94a3b8; font-weight: 700;">
                                {{ $userList->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="user-info-box">
                                    <img src="{{ $avatarUrl }}" alt="{{ $u->name }}" class="user-avatar-circle">
                                    <div>
                                        <div class="user-name-text">
                                            {{ $u->name }}
                                            @if($u->id === Auth::id())
                                                <span style="font-size: 11px; background: #e0e7ff; color: #3730a3; padding: 2px 7px; border-radius: 6px; font-weight: 700; margin-left: 4px;">Akun Anda</span>
                                            @endif
                                        </div>
                                        <div class="user-email-text">
                                            <i class="fa-regular fa-envelope" style="font-size: 11px; margin-right: 4px;"></i>{{ $u->email }}
                                            @if($u->role === 'wali_murid' && $u->siswa)
                                                <span style="display: block; font-size: 11px; color: #047857; margin-top: 2px;">
                                                    <i class="fa-solid fa-graduation-cap"></i> Siswa: {{ $u->siswa->nama_lengkap }} ({{ $u->siswa->kelas->nama_kelas ?? '-' }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge {{ $u->role }}">
                                    <i class="fa-solid {{ $roleIcon }}"></i>
                                    <span>{{ $roleLabel }}</span>
                                </span>
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="status-badge active">
                                        <span class="status-dot"></span> Aktif
                                    </span>
                                @else
                                    <span class="status-badge inactive">
                                        <span class="status-dot"></span> Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td style="color: #64748b; font-size: 13px;">
                                {{ $u->created_at ? $u->created_at->locale('id')->isoFormat('D MMM Y') : '-' }}
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btn-group">
                                    {{-- Toggle Aktif --}}
                                    <form action="{{ route('admin.master.user.toggle-status', $u->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" 
                                                class="btn-tbl-action toggle" 
                                                title="{{ $u->is_active ? 'Non-aktifkan Akun' : 'Aktifkan Akun' }}"
                                                {{ $u->id === Auth::id() ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>
                                            <i class="fa-solid {{ $u->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                        </button>
                                    </form>

                                    {{-- Detail Modal --}}
                                    <button type="button" 
                                            class="btn-tbl-action view" 
                                            title="Lihat Detail Akun"
                                            onclick="openDetailModal(@json($u), '{{ $roleLabel }}', '{{ $avatarUrl }}')">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    {{-- Edit Modal --}}
                                    <button type="button" 
                                            class="btn-tbl-action edit" 
                                            title="Edit Akun"
                                            onclick="openEditModal(@json($u))">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    {{-- Hapus Modal --}}
                                    <button type="button" 
                                            class="btn-tbl-action delete" 
                                            title="Hapus Akun"
                                            {{ $u->id === Auth::id() ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}
                                            onclick="openDeleteModal({{ $u->id }}, '{{ addslashes($u->name) }}')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty-cell">
                                <div class="empty-icon-box">
                                    <i class="fa-solid fa-users-slash"></i>
                                </div>
                                <div class="empty-title">Tidak ada data pengguna ditemukan</div>
                                <div class="empty-desc">Coba ubah kata kunci pencarian atau filter role.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($userList->hasPages())
            <div class="table-pagination-footer">
                <div class="pag-text">
                    Menampilkan <strong>{{ $userList->firstItem() }}</strong> - <strong>{{ $userList->lastItem() }}</strong> dari <strong>{{ $userList->total() }}</strong> pengguna
                </div>
                <div>
                    {{ $userList->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- MODAL TAMBAH USER --}}
<div class="custom-modal-backdrop" id="addUserModal">
    <div class="custom-modal">
        <div class="modal-hdr">
            <div class="modal-title-text">
                <i class="fa-solid fa-user-plus" style="color: #2b43b9;"></i>
                <span>Tambah Pengguna Baru</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeAddModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('admin.master.user.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Lengkap <span>*</span></label>
                    <input type="text" name="name" class="form-ctrl-custom" placeholder="Contoh: Ahmad Fauzi, S.Pd" required>
                </div>

                <div class="form-row-2">
                    <div>
                        <label class="form-label-custom">Email Login <span>*</span></label>
                        <input type="email" name="email" class="form-ctrl-custom" placeholder="email@smkn1boyolangu.sch.id" required>
                    </div>
                    <div>
                        <label class="form-label-custom">Password <span>*</span></label>
                        <input type="password" name="password" class="form-ctrl-custom" placeholder="Minimal 6 karakter" minlength="6" required>
                    </div>
                </div>

                <div class="form-row-2">
                    <div>
                        <label class="form-label-custom">Role / Manajemen <span>*</span></label>
                        <select name="role" id="add_role" class="form-ctrl-custom" onchange="onRoleChange('add')" required>
                            <option value="admin">Administrator</option>
                            <option value="wali_kelas">Wali Kelas</option>
                            <option value="guru_mapel" selected>Guru Mapel</option>
                            <option value="guru_piket">Guru Piket</option>
                            <option value="waka_kurikulum">Waka Kurikulum</option>
                            <option value="waka_sdm">Waka SDM / Kepegawaian</option>
                            <option value="wali_murid">Wali Murid / Siswa</option>
                            <option value="kepala_sekolah">Kepala Sekolah</option>
                            <option value="satpam">Satpam</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label-custom">Status Akun <span>*</span></label>
                        <select name="is_active" class="form-ctrl-custom">
                            <option value="1" selected>Aktif (Bisa Login)</option>
                            <option value="0">Non-Aktif (Ditangguhkan)</option>
                        </select>
                    </div>
                </div>

                {{-- Siswa Link Option for Wali Murid --}}
                <div class="form-group-custom" id="add_siswa_wrap" style="display: none;">
                    <label class="form-label-custom">Hubungkan dengan Siswa</label>
                    <select name="id_siswa" class="form-ctrl-custom">
                        <option value="">-- Pilih Siswa yang Diwakili --</option>
                        @foreach($siswaList as $sw)
                            <option value="{{ $sw->id_siswa }}">{{ $sw->nama_lengkap }} ({{ $sw->kelas->nama_kelas ?? '-' }}) - NISN: {{ $sw->nisn }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row-2">
                    <div>
                        <label class="form-label-custom">NIP / No. Identitas <small style="color:#94a3b8;">(Opsional)</small></label>
                        <input type="text" name="nip" class="form-ctrl-custom" placeholder="Nomor Induk Pegawai">
                    </div>
                    <div>
                        <label class="form-label-custom">No. WhatsApp / HP <small style="color:#94a3b8;">(Opsional)</small></label>
                        <input type="text" name="no_hp" class="form-ctrl-custom" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-modal-cancel" onclick="closeAddModal()">Batal</button>
                <button type="submit" class="btn-modal-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT USER --}}
<div class="custom-modal-backdrop" id="editUserModal">
    <div class="custom-modal">
        <div class="modal-hdr">
            <div class="modal-title-text">
                <i class="fa-solid fa-user-pen" style="color: #2b43b9;"></i>
                <span>Edit Data Pengguna</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Lengkap <span>*</span></label>
                    <input type="text" name="name" id="edit_name" class="form-ctrl-custom" required>
                </div>

                <div class="form-row-2">
                    <div>
                        <label class="form-label-custom">Email Login <span>*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-ctrl-custom" required>
                    </div>
                    <div>
                        <label class="form-label-custom">Password Baru <small style="color:#94a3b8;">(Kosongkan jika tetap)</small></label>
                        <input type="password" name="password" id="edit_password" class="form-ctrl-custom" placeholder="Ganti password (min. 6)">
                    </div>
                </div>

                <div class="form-row-2">
                    <div>
                        <label class="form-label-custom">Role / Manajemen <span>*</span></label>
                        <select name="role" id="edit_role" class="form-ctrl-custom" onchange="onRoleChange('edit')" required>
                            <option value="admin">Administrator</option>
                            <option value="wali_kelas">Wali Kelas</option>
                            <option value="guru_mapel">Guru Mapel</option>
                            <option value="guru_piket">Guru Piket</option>
                            <option value="waka_kurikulum">Waka Kurikulum</option>
                            <option value="waka_sdm">Waka SDM / Kepegawaian</option>
                            <option value="wali_murid">Wali Murid / Siswa</option>
                            <option value="kepala_sekolah">Kepala Sekolah</option>
                            <option value="satpam">Satpam</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label-custom">Status Akun <span>*</span></label>
                        <select name="is_active" id="edit_is_active" class="form-ctrl-custom">
                            <option value="1">Aktif (Bisa Login)</option>
                            <option value="0">Non-Aktif (Ditangguhkan)</option>
                        </select>
                    </div>
                </div>

                {{-- Siswa Link Option for Wali Murid in Edit Modal --}}
                <div class="form-group-custom" id="edit_siswa_wrap" style="display: none;">
                    <label class="form-label-custom">Hubungkan dengan Siswa</label>
                    <select name="id_siswa" id="edit_id_siswa" class="form-ctrl-custom">
                        <option value="">-- Pilih Siswa yang Diwakili --</option>
                        @foreach($siswaList as $sw)
                            <option value="{{ $sw->id_siswa }}">{{ $sw->nama_lengkap }} ({{ $sw->kelas->nama_kelas ?? '-' }}) - NISN: {{ $sw->nisn }}</option>
                        @endforeach
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

{{-- MODAL DETAIL USER --}}
<div class="custom-modal-backdrop" id="detailUserModal">
    <div class="custom-modal">
        <div class="modal-hdr">
            <div class="modal-title-text">
                <i class="fa-solid fa-id-card" style="color: #2b43b9;"></i>
                <span>Detail Akun Pengguna</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeDetailModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;">
                <img id="detail_avatar" src="" alt="Avatar" style="width: 64px; height: 64px; border-radius: 18px; object-fit: cover; border: 2px solid #e2e8f0;">
                <div>
                    <h3 id="detail_name" style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0;"></h3>
                    <p id="detail_email" style="font-size: 13px; color: #64748b; margin: 3px 0 0 0;"></p>
                    <div style="margin-top: 8px;" id="detail_role_badge"></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase;">Status Akun</div>
                    <div id="detail_status" style="font-weight: 700; margin-top: 4px;"></div>
                </div>
                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase;">ID Pengguna</div>
                    <div id="detail_id" style="font-weight: 700; color: #0f172a; margin-top: 4px;"></div>
                </div>
                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase;">Terdaftar Sejak</div>
                    <div id="detail_created" style="font-weight: 700; color: #0f172a; margin-top: 4px;"></div>
                </div>
                <div style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase;">Email Terverifikasi</div>
                    <div id="detail_verified" style="font-weight: 700; color: #0f172a; margin-top: 4px;"></div>
                </div>
            </div>
        </div>
        <div class="modal-ftr">
            <button type="button" class="btn-modal-cancel" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL DELETE KONFIRMASI --}}
<div class="custom-modal-backdrop" id="deleteUserModal">
    <div class="custom-modal" style="max-width: 440px;">
        <div class="modal-hdr" style="border-bottom: none; padding-bottom: 0;">
            <div class="modal-title-text" style="color: #ef4444;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Konfirmasi Hapus Pengguna</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeDeleteModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="deleteUserForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body" style="padding-top: 12px;">
                <p style="font-size: 14px; color: #475569; line-height: 1.5; margin: 0;">
                    Apakah Anda yakin ingin menghapus akun pengguna <strong id="delete_user_name" style="color: #0f172a;"></strong> dari database?
                </p>
                <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 10px; padding: 10px 14px; margin-top: 14px; display: flex; gap: 8px; align-items: center; color: #b91c1c; font-size: 12px;">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Tindakan ini akan menghapus akun login pengguna.</span>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="btn-modal-delete">
                    <i class="fa-solid fa-trash-can"></i> Ya, Hapus Pengguna
                </button>
            </div>
        </form>
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

    function onRoleChange(type) {
        const select = document.getElementById(type + '_role');
        const wrap = document.getElementById(type + '_siswa_wrap');
        if (select && wrap) {
            wrap.style.display = (select.value === 'wali_murid') ? 'block' : 'none';
        }
    }

    // Modal Add
    function openAddModal() {
        document.getElementById('add_role').value = 'guru_mapel';
        onRoleChange('add');
        document.getElementById('addUserModal').classList.add('show');
    }
    function closeAddModal() {
        document.getElementById('addUserModal').classList.remove('show');
    }

    // Modal Edit
    function openEditModal(user) {
        document.getElementById('edit_name').value = user.name || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_role').value = user.role || 'guru_mapel';
        document.getElementById('edit_is_active').value = user.is_active ? '1' : '0';

        if (user.role === 'wali_murid' && user.siswa) {
            document.getElementById('edit_id_siswa').value = user.siswa.id_siswa;
        } else {
            document.getElementById('edit_id_siswa').value = '';
        }
        onRoleChange('edit');

        const form = document.getElementById('editUserForm');
        form.action = `/admin/master/user/${user.id}`;

        document.getElementById('editUserModal').classList.add('show');
    }
    function closeEditModal() {
        document.getElementById('editUserModal').classList.remove('show');
    }

    // Modal Detail
    function openDetailModal(user, roleLabel, avatarUrl) {
        document.getElementById('detail_avatar').src = avatarUrl;
        document.getElementById('detail_name').textContent = user.name || '-';
        document.getElementById('detail_email').textContent = user.email || '-';
        document.getElementById('detail_id').textContent = '#' + user.id;
        document.getElementById('detail_created').textContent = user.created_at ? new Date(user.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
        document.getElementById('detail_verified').textContent = user.email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi';
        
        document.getElementById('detail_status').innerHTML = user.is_active 
            ? '<span style="color: #065f46;">● Aktif</span>' 
            : '<span style="color: #991b1b;">● Non-Aktif</span>';

        let siswaInfo = '';
        if (user.role === 'wali_murid' && user.siswa) {
            siswaInfo = `<div style="font-size: 11.5px; color: #047857; margin-top: 4px;"><i class="fa-solid fa-graduation-cap"></i> Mewakili Siswa: <strong>${user.siswa.nama_lengkap}</strong></div>`;
        }

        document.getElementById('detail_role_badge').innerHTML = `
            <span class="role-badge ${user.role}">
                <i class="fa-solid fa-tag"></i> ${roleLabel}
            </span>
            ${siswaInfo}
        `;

        document.getElementById('detailUserModal').classList.add('show');
    }
    function closeDetailModal() {
        document.getElementById('detailUserModal').classList.remove('show');
    }

    // Modal Delete
    function openDeleteModal(id, name) {
        document.getElementById('delete_user_name').textContent = name;
        const form = document.getElementById('deleteUserForm');
        form.action = `/admin/master/user/${id}`;
        document.getElementById('deleteUserModal').classList.add('show');
    }
    function closeDeleteModal() {
        document.getElementById('deleteUserModal').classList.remove('show');
    }

    // Close modal when clicking backdrop
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal-backdrop')) {
            e.target.classList.remove('show');
        }
    });

    // Close modal on Escape key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.custom-modal-backdrop.show').forEach(m => m.classList.remove('show'));
        }
    });
</script>
@endsection
