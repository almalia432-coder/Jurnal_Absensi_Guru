@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Guru Piket KBM — SMKN 1 BOYOLANGU')
@section('header_title', 'Jadwal Guru Piket KBM')
@section('header_subtitle', 'Kelola penugasan piket KBM, Waka Penanggung Jawab, Koordinator & Petugas Shift Pagi dan Siang (Siklus A & B)')

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

    .btn-action-primary {
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
    }
    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(43,67,185,0.35);
        color: #ffffff;
    }

    .btn-action-secondary {
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
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-action-secondary:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #94a3b8;
    }

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
    .kpi-icon.amber   { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .kpi-icon.purple  { background: linear-gradient(135deg, #f3e8ff, #e9d5ff); color: #6b21a8; }

    .kpi-info h4 { font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700; margin-bottom: 4px; }
    .kpi-info .kpi-num { font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1.1; }
    .kpi-info .kpi-sub { font-size: 12px; color: #94a3b8; font-weight: 600; margin-top: 4px; }

    /* Tabs Navigation */
    .tab-nav-container {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        padding: 8px 12px;
        border-radius: 16px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 10px 18px;
        border-radius: 12px;
        border: none;
        background: transparent;
        font-size: 13.5px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .tab-btn:hover {
        background: #f1f5f9;
        color: #2b43b9;
    }

    .tab-btn.active {
        background: #2b43b9;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(43, 67, 185, 0.25);
    }

    /* Cycle & Shift Badges */
    .cycle-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .cycle-badge.a { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .cycle-badge.b { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

    .role-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
    }
    .role-badge.koor { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .role-badge.petugas { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }

    /* Day Card Container in Matrix */
    .day-matrix-box {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .day-matrix-header {
        padding: 16px 22px;
        background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .day-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .day-pill {
        background: #2b43b9;
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 800;
        padding: 6px 14px;
        border-radius: 10px;
        letter-spacing: 0.5px;
    }

    .waka-info-strip {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 6px 14px;
        border-radius: 12px;
    }

    .day-shifts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding: 22px;
    }

    .shift-box {
        background: #fbfcfe;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .shift-header {
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }
    .shift-header.pagi { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); }
    .shift-header.siang { background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%); }

    .shift-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        flex: 1;
    }

    .person-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        transition: all 0.2s ease;
    }
    .person-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        background: #f8fafc;
    }

    .btn-icon-edit {
        width: 30px; height: 30px;
        border-radius: 8px;
        background: #eef2ff;
        color: #2b43b9;
        border: 1px solid #c7d2fe;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-icon-edit:hover { background: #2b43b9; color: #ffffff; }

    .btn-icon-delete {
        width: 30px; height: 30px;
        border-radius: 8px;
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecdd3;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-icon-delete:hover { background: #ef4444; color: #ffffff; }

    .btn-add-to-shift {
        width: 100%;
        padding: 8px;
        border-radius: 10px;
        border: 1.5px dashed #cbd5e1;
        background: #ffffff;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-add-to-shift:hover {
        background: #f1f5f9;
        border-color: #2b43b9;
        color: #2b43b9;
    }

    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }
    .data-table th {
        background: #f8fafc;
        padding: 12px 16px;
        font-weight: 800;
        font-size: 11.5px;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
    }
    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        vertical-align: middle;
    }
    .data-table tr:hover td {
        background: #fbfcfe;
    }

    /* Modal Backdrop & Box */
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-backdrop-custom.show {
        display: flex;
    }
    .modal-content-custom {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0,0,0,0.18);
        border: 1px solid #e2e8f0;
        animation: modalScaleUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes modalScaleUp {
        from { transform: scale(0.92); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .modal-header-custom {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-header-custom h3 {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .modal-close-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-close-btn:hover { background: #fee2e2; color: #ef4444; }

    .modal-body-custom {
        padding: 24px;
    }

    .form-group-custom {
        margin-bottom: 16px;
    }
    .form-label-custom {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13.5px;
        color: #0f172a;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-control-custom:focus {
        border-color: #2b43b9;
    }

    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .modal-footer-custom {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
        border-radius: 0 0 20px 20px;
    }

    @media (max-width: 900px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .day-shifts-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .form-row-2 { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

{{-- Toast Notification --}}
<div class="toast-container" id="toastContainer">
    @if(session('success'))
        <div class="toast">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="toast error">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="toast error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif
</div>

{{-- Page Top Action Bar --}}
<div class="page-action-card">
    <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 46px; height: 46px; border-radius: 14px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #2b43b9; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-clipboard-user"></i>
        </div>
        <div>
            <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0;">Jadwal Resmi Guru Piket KBM</h2>
            <p style="font-size: 13px; color: #64748b; margin: 3px 0 0 0;">Semester Ganjil SMKN 1 Boyolangu TP 2026/2027 (Rotasi Siklus A &amp; B)</p>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('guru-piket.jadwal.cetak') }}" target="_blank" class="btn-action-secondary">
            <i class="fa-solid fa-print"></i> Cetak Format SK
        </a>
        <button type="button" class="btn-action-primary" onclick="openTambahModal()">
            <i class="fa-solid fa-user-plus"></i> + Tambah Petugas Piket
        </button>
    </div>
</div>

{{-- KPI Summary Grid --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon indigo">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="kpi-info">
            <h4>Total Roster Terdaftar</h4>
            <div class="kpi-num">{{ $kpi['total_roster'] }}</div>
            <div class="kpi-sub">Penugasan Piket Terjadwal</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald">
            <i class="fa-solid fa-calendar-week"></i>
        </div>
        <div class="kpi-info">
            <h4>Siklus A (Minggu Ganjil)</h4>
            <div class="kpi-num">{{ $kpi['total_siklus_a'] }}</div>
            <div class="kpi-sub">Minggu 1, 3, dan 5</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber">
            <i class="fa-solid fa-calendar-days"></i>
        </div>
        <div class="kpi-info">
            <h4>Siklus B (Minggu Genap)</h4>
            <div class="kpi-num">{{ $kpi['total_siklus_b'] }}</div>
            <div class="kpi-sub">Minggu 2 dan 4</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon purple">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <div class="kpi-info">
            <h4>Petugas Hari Ini</h4>
            <div class="kpi-num">{{ $kpi['petugas_hari_ini_count'] }}</div>
            <div class="kpi-sub">{{ $todayFormatted }}</div>
        </div>
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="tab-nav-container">
    <a href="{{ route('admin.jadwal-piket', ['tab' => 'siklus_a']) }}" 
       class="tab-btn {{ $activeTab === 'siklus_a' ? 'active' : '' }}">
        <i class="fa-solid fa-rotate"></i>
        <span>Matriks Siklus A (Minggu Ganjil)</span>
        <span class="cycle-badge a" style="padding: 2px 8px; font-size: 11px;">A</span>
    </a>
    <a href="{{ route('admin.jadwal-piket', ['tab' => 'siklus_b']) }}" 
       class="tab-btn {{ $activeTab === 'siklus_b' ? 'active' : '' }}">
        <i class="fa-solid fa-rotate"></i>
        <span>Matriks Siklus B (Minggu Genap)</span>
        <span class="cycle-badge b" style="padding: 2px 8px; font-size: 11px;">B</span>
    </a>
    <a href="{{ route('admin.jadwal-piket', ['tab' => 'semua']) }}" 
       class="tab-btn {{ $activeTab === 'semua' ? 'active' : '' }}">
        <i class="fa-solid fa-table-list"></i>
        <span>Daftar Semua Petugas (Tabel)</span>
    </a>
    <a href="{{ route('admin.jadwal-piket', ['tab' => 'hari_ini', 'tanggal' => $tanggalInput]) }}" 
       class="tab-btn {{ $activeTab === 'hari_ini' ? 'active' : '' }}">
        <i class="fa-solid fa-clock"></i>
        <span>Roster Hari Ini (Live)</span>
    </a>
</div>

{{-- ==================== TAB 1: SIKLUS A ==================== --}}
@if($activeTab === 'siklus_a')
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 800; color: #1b2559; margin: 0; display: flex; align-items: center; gap: 8px;">
                <span class="cycle-badge a"><i class="fa-solid fa-repeat"></i> SIKLUS A</span>
                <span>Matriks Roster Minggu Ganjil (Minggu Ke-1, 3, 5)</span>
            </h3>
            <p style="font-size: 12.5px; color: #64748b; margin-top: 4px;">Kelola Waka Penanggung Jawab, Koordinator, serta Anggota Shift Pagi &amp; Siang</p>
        </div>
    </div>

    @foreach($hariOrder as $h)
    @php $row = $siklusAData[$h] ?? null; @endphp
    <div class="day-matrix-box">
        <div class="day-matrix-header">
            <div class="day-title-wrap">
                <span class="day-pill">{{ $h }}</span>
                <div class="waka-info-strip">
                    <i class="fa-solid fa-user-shield" style="color: #16a34a; font-size: 15px;"></i>
                    <div style="font-size: 12.5px; color: #166534;">
                        <strong>Waka Piket:</strong> {{ $row['waka']->piket_waka_nama ?? 'Belum Ditentukan' }} 
                        @if(!empty($row['waka']->piket_waka_nip))
                            <span style="opacity: 0.8; font-size: 11px;">(NIP: {{ $row['waka']->piket_waka_nip }})</span>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <button type="button" class="btn-action-secondary" style="padding: 6px 14px; font-size: 12px;"
                        onclick="openWakaModal('A', '{{ $h }}', '{{ addslashes($row['waka']->piket_waka_nama ?? '') }}', '{{ addslashes($row['waka']->piket_waka_nip ?? '') }}')">
                    <i class="fa-solid fa-pen-to-square"></i> Atur Waka Piket
                </button>
            </div>
        </div>

        <div class="day-shifts-grid">
            {{-- Shift Pagi --}}
            <div class="shift-box">
                <div class="shift-header pagi">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-sun" style="color: #f59e0b; font-size: 16px;"></i>
                        <strong style="font-size: 14px; color: #92400e;">Shift Pagi (07.00 - 11.00 WIB)</strong>
                    </div>
                    <span class="role-badge koor">Pagi</span>
                </div>
                <div class="shift-body">
                    {{-- Koordinator Pagi --}}
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #b45309; display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-crown"></i> Koordinator Piket Pagi
                        </span>
                        @if($row['pagi_koordinator'])
                        <div class="person-card" style="border-left: 3px solid #f59e0b;">
                            <div>
                                <strong style="font-size: 13.5px; color: #0f172a; display: block;">{{ $row['pagi_koordinator']->nama_guru }}</strong>
                                <span style="font-size: 11.5px; color: #64748b;">NIP: {{ $row['pagi_koordinator']->nip ?? '-' }}</span>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn-icon-edit" title="Edit Koordinator" onclick="openEditModal({{ json_encode($row['pagi_koordinator']) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $row['pagi_koordinator']->id) }}', '{{ addslashes($row['pagi_koordinator']->nama_guru) }}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @else
                        <div style="padding: 10px; text-align: center; background: #fffbeb; border-radius: 10px; font-size: 12px; color: #b45309;">
                            Belum ada koordinator pagi.
                        </div>
                        @endif
                    </div>

                    {{-- Anggota Petugas Pagi --}}
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-users"></i> Anggota Petugas Pagi ({{ $row['pagi_petugas']->count() }})
                        </span>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @forelse($row['pagi_petugas'] as $idx => $p)
                            <div class="person-card">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 22px; height: 22px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <strong style="font-size: 13px; color: #0f172a; display: block;">{{ $p->nama_guru }}</strong>
                                        <span style="font-size: 11px; color: #64748b;">NIP: {{ $p->nip ?? '-' }}</span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="btn-icon-edit" title="Edit Petugas" onclick="openEditModal({{ json_encode($p) }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $p->id) }}', '{{ addslashes($p->nama_guru) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div style="padding: 10px; text-align: center; color: #94a3b8; font-size: 12px;">Belum ada anggota petugas.</div>
                            @endforelse
                        </div>
                    </div>

                    <button type="button" class="btn-add-to-shift" onclick="openTambahPreset('A', '{{ $h }}', 'Pagi')">
                        <i class="fa-solid fa-plus"></i> Tambah Petugas ke Shift Pagi
                    </button>
                </div>
            </div>

            {{-- Shift Siang --}}
            <div class="shift-box">
                <div class="shift-header siang">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-cloud-sun" style="color: #3b82f6; font-size: 16px;"></i>
                        <strong style="font-size: 14px; color: #1e40af;">Shift Siang (11.00 - 15.00 WIB)</strong>
                    </div>
                    <span class="role-badge petugas" style="background:#dbeafe; color:#1e40af; border-color:#bfdbfe;">Siang</span>
                </div>
                <div class="shift-body">
                    {{-- Koordinator Siang --}}
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #2563eb; display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-crown"></i> Koordinator Piket Siang
                        </span>
                        @if($row['siang_koordinator'])
                        <div class="person-card" style="border-left: 3px solid #3b82f6;">
                            <div>
                                <strong style="font-size: 13.5px; color: #0f172a; display: block;">{{ $row['siang_koordinator']->nama_guru }}</strong>
                                <span style="font-size: 11.5px; color: #64748b;">NIP: {{ $row['siang_koordinator']->nip ?? '-' }}</span>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn-icon-edit" title="Edit Koordinator" onclick="openEditModal({{ json_encode($row['siang_koordinator']) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $row['siang_koordinator']->id) }}', '{{ addslashes($row['siang_koordinator']->nama_guru) }}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @else
                        <div style="padding: 10px; text-align: center; background: #eff6ff; border-radius: 10px; font-size: 12px; color: #2563eb;">
                            Belum ada koordinator siang.
                        </div>
                        @endif
                    </div>

                    {{-- Anggota Petugas Siang --}}
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-users"></i> Anggota Petugas Siang ({{ $row['siang_petugas']->count() }})
                        </span>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @forelse($row['siang_petugas'] as $idx => $p)
                            <div class="person-card">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 22px; height: 22px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <strong style="font-size: 13px; color: #0f172a; display: block;">{{ $p->nama_guru }}</strong>
                                        <span style="font-size: 11px; color: #64748b;">NIP: {{ $p->nip ?? '-' }}</span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="btn-icon-edit" title="Edit Petugas" onclick="openEditModal({{ json_encode($p) }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $p->id) }}', '{{ addslashes($p->nama_guru) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div style="padding: 10px; text-align: center; color: #94a3b8; font-size: 12px;">Belum ada anggota petugas.</div>
                            @endforelse
                        </div>
                    </div>

                    <button type="button" class="btn-add-to-shift" onclick="openTambahPreset('A', '{{ $h }}', 'Siang')">
                        <i class="fa-solid fa-plus"></i> Tambah Petugas ke Shift Siang
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

{{-- ==================== TAB 2: SIKLUS B ==================== --}}
@elseif($activeTab === 'siklus_b')
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 800; color: #1b2559; margin: 0; display: flex; align-items: center; gap: 8px;">
                <span class="cycle-badge b"><i class="fa-solid fa-repeat"></i> SIKLUS B</span>
                <span>Matriks Roster Minggu Genap (Minggu Ke-2, 4)</span>
            </h3>
            <p style="font-size: 12.5px; color: #64748b; margin-top: 4px;">Kelola Waka Penanggung Jawab, Koordinator, serta Anggota Shift Pagi &amp; Siang</p>
        </div>
    </div>

    @foreach($hariOrder as $h)
    @php $row = $siklusBData[$h] ?? null; @endphp
    <div class="day-matrix-box">
        <div class="day-matrix-header">
            <div class="day-title-wrap">
                <span class="day-pill" style="background: #92400e;">{{ $h }}</span>
                <div class="waka-info-strip">
                    <i class="fa-solid fa-user-shield" style="color: #16a34a; font-size: 15px;"></i>
                    <div style="font-size: 12.5px; color: #166534;">
                        <strong>Waka Piket:</strong> {{ $row['waka']->piket_waka_nama ?? 'Belum Ditentukan' }} 
                        @if(!empty($row['waka']->piket_waka_nip))
                            <span style="opacity: 0.8; font-size: 11px;">(NIP: {{ $row['waka']->piket_waka_nip }})</span>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <button type="button" class="btn-action-secondary" style="padding: 6px 14px; font-size: 12px;"
                        onclick="openWakaModal('B', '{{ $h }}', '{{ addslashes($row['waka']->piket_waka_nama ?? '') }}', '{{ addslashes($row['waka']->piket_waka_nip ?? '') }}')">
                    <i class="fa-solid fa-pen-to-square"></i> Atur Waka Piket
                </button>
            </div>
        </div>

        <div class="day-shifts-grid">
            {{-- Shift Pagi --}}
            <div class="shift-box">
                <div class="shift-header pagi">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-sun" style="color: #f59e0b; font-size: 16px;"></i>
                        <strong style="font-size: 14px; color: #92400e;">Shift Pagi (07.00 - 11.00 WIB)</strong>
                    </div>
                    <span class="role-badge koor">Pagi</span>
                </div>
                <div class="shift-body">
                    {{-- Koordinator Pagi --}}
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #b45309; display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-crown"></i> Koordinator Piket Pagi
                        </span>
                        @if($row['pagi_koordinator'])
                        <div class="person-card" style="border-left: 3px solid #f59e0b;">
                            <div>
                                <strong style="font-size: 13.5px; color: #0f172a; display: block;">{{ $row['pagi_koordinator']->nama_guru }}</strong>
                                <span style="font-size: 11.5px; color: #64748b;">NIP: {{ $row['pagi_koordinator']->nip ?? '-' }}</span>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn-icon-edit" title="Edit Koordinator" onclick="openEditModal({{ json_encode($row['pagi_koordinator']) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $row['pagi_koordinator']->id) }}', '{{ addslashes($row['pagi_koordinator']->nama_guru) }}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @else
                        <div style="padding: 10px; text-align: center; background: #fffbeb; border-radius: 10px; font-size: 12px; color: #b45309;">
                            Belum ada koordinator pagi.
                        </div>
                        @endif
                    </div>

                    {{-- Anggota Petugas Pagi --}}
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-users"></i> Anggota Petugas Pagi ({{ $row['pagi_petugas']->count() }})
                        </span>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @forelse($row['pagi_petugas'] as $idx => $p)
                            <div class="person-card">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 22px; height: 22px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <strong style="font-size: 13px; color: #0f172a; display: block;">{{ $p->nama_guru }}</strong>
                                        <span style="font-size: 11px; color: #64748b;">NIP: {{ $p->nip ?? '-' }}</span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="btn-icon-edit" title="Edit Petugas" onclick="openEditModal({{ json_encode($p) }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $p->id) }}', '{{ addslashes($p->nama_guru) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div style="padding: 10px; text-align: center; color: #94a3b8; font-size: 12px;">Belum ada anggota petugas.</div>
                            @endforelse
                        </div>
                    </div>

                    <button type="button" class="btn-add-to-shift" onclick="openTambahPreset('B', '{{ $h }}', 'Pagi')">
                        <i class="fa-solid fa-plus"></i> Tambah Petugas ke Shift Pagi
                    </button>
                </div>
            </div>

            {{-- Shift Siang --}}
            <div class="shift-box">
                <div class="shift-header siang">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-cloud-sun" style="color: #3b82f6; font-size: 16px;"></i>
                        <strong style="font-size: 14px; color: #1e40af;">Shift Siang (11.00 - 15.00 WIB)</strong>
                    </div>
                    <span class="role-badge petugas" style="background:#dbeafe; color:#1e40af; border-color:#bfdbfe;">Siang</span>
                </div>
                <div class="shift-body">
                    {{-- Koordinator Siang --}}
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #2563eb; display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-crown"></i> Koordinator Piket Siang
                        </span>
                        @if($row['siang_koordinator'])
                        <div class="person-card" style="border-left: 3px solid #3b82f6;">
                            <div>
                                <strong style="font-size: 13.5px; color: #0f172a; display: block;">{{ $row['siang_koordinator']->nama_guru }}</strong>
                                <span style="font-size: 11.5px; color: #64748b;">NIP: {{ $row['siang_koordinator']->nip ?? '-' }}</span>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn-icon-edit" title="Edit Koordinator" onclick="openEditModal({{ json_encode($row['siang_koordinator']) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $row['siang_koordinator']->id) }}', '{{ addslashes($row['siang_koordinator']->nama_guru) }}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @else
                        <div style="padding: 10px; text-align: center; background: #eff6ff; border-radius: 10px; font-size: 12px; color: #2563eb;">
                            Belum ada koordinator siang.
                        </div>
                        @endif
                    </div>

                    {{-- Anggota Petugas Siang --}}
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-users"></i> Anggota Petugas Siang ({{ $row['siang_petugas']->count() }})
                        </span>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @forelse($row['siang_petugas'] as $idx => $p)
                            <div class="person-card">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 22px; height: 22px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <strong style="font-size: 13px; color: #0f172a; display: block;">{{ $p->nama_guru }}</strong>
                                        <span style="font-size: 11px; color: #64748b;">NIP: {{ $p->nip ?? '-' }}</span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="btn-icon-edit" title="Edit Petugas" onclick="openEditModal({{ json_encode($p) }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $p->id) }}', '{{ addslashes($p->nama_guru) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div style="padding: 10px; text-align: center; color: #94a3b8; font-size: 12px;">Belum ada anggota petugas.</div>
                            @endforelse
                        </div>
                    </div>

                    <button type="button" class="btn-add-to-shift" onclick="openTambahPreset('B', '{{ $h }}', 'Siang')">
                        <i class="fa-solid fa-plus"></i> Tambah Petugas ke Shift Siang
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

{{-- ==================== TAB 3: DAFTAR SEMUA (TABEL) ==================== --}}
@elseif($activeTab === 'semua')
    <div style="background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 24px;">
        {{-- Filter Bar --}}
        <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; background: #fafbfc;">
            <form method="GET" action="{{ route('admin.jadwal-piket') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <input type="hidden" name="tab" value="semua">
                
                <div style="flex: 1; min-width: 220px;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru, NIP, atau Waka..." 
                           class="form-control-custom" style="padding: 8px 12px; font-size: 13px;">
                </div>

                <div style="width: 120px;">
                    <select name="filter_siklus" class="form-control-custom" style="padding: 8px 10px; font-size: 13px;">
                        <option value="">Semua Siklus</option>
                        <option value="A" {{ $filterSiklus === 'A' ? 'selected' : '' }}>Siklus A</option>
                        <option value="B" {{ $filterSiklus === 'B' ? 'selected' : '' }}>Siklus B</option>
                    </select>
                </div>

                <div style="width: 130px;">
                    <select name="filter_hari" class="form-control-custom" style="padding: 8px 10px; font-size: 13px;">
                        <option value="">Semua Hari</option>
                        @foreach($hariOrder as $h)
                            <option value="{{ $h }}" {{ $filterHari === $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="width: 120px;">
                    <select name="filter_shift" class="form-control-custom" style="padding: 8px 10px; font-size: 13px;">
                        <option value="">Semua Shift</option>
                        <option value="Pagi" {{ $filterShift === 'Pagi' ? 'selected' : '' }}>Pagi</option>
                        <option value="Siang" {{ $filterShift === 'Siang' ? 'selected' : '' }}>Siang</option>
                    </select>
                </div>

                <div style="width: 140px;">
                    <select name="filter_peran" class="form-control-custom" style="padding: 8px 10px; font-size: 13px;">
                        <option value="">Semua Peran</option>
                        <option value="koordinator" {{ $filterPeran === 'koordinator' ? 'selected' : '' }}>Koordinator</option>
                        <option value="petugas" {{ $filterPeran === 'petugas' ? 'selected' : '' }}>Petugas</option>
                    </select>
                </div>

                <button type="submit" class="btn-action-primary" style="padding: 8px 16px; font-size: 13px;">
                    <i class="fa-solid fa-magnifying-glass"></i> Filter
                </button>

                @if($search || $filterSiklus || $filterHari || $filterShift || $filterPeran)
                    <a href="{{ route('admin.jadwal-piket', ['tab' => 'semua']) }}" class="btn-action-secondary" style="padding: 8px 14px; font-size: 13px;">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Content --}}
        <div class="table-responsive-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 90px; text-align: center;">Siklus</th>
                        <th style="width: 100px;">Hari</th>
                        <th style="width: 90px;">Shift</th>
                        <th>Peran</th>
                        <th>Nama Guru &amp; NIP</th>
                        <th>Jam Tugas</th>
                        <th>Waka Penanggung Jawab</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semuaPetugas as $index => $item)
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #64748b;">
                            {{ $semuaPetugas->firstItem() + $index }}
                        </td>
                        <td style="text-align: center;">
                            <span class="cycle-badge {{ strtolower($item->siklus) }}">{{ $item->siklus }}</span>
                        </td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->hari }}</strong>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: {{ $item->shift === 'Pagi' ? '#b45309' : '#1e40af' }};">
                                {{ $item->shift }}
                            </span>
                        </td>
                        <td>
                            @if($item->peran === 'koordinator')
                                <span class="role-badge koor"><i class="fa-solid fa-crown"></i> Koordinator</span>
                            @else
                                <span class="role-badge petugas"><i class="fa-solid fa-user"></i> Petugas</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">{{ $item->nama_guru }}</div>
                            <div style="font-size: 11.5px; color: #64748b;">NIP: {{ $item->nip ?? '-' }}</div>
                        </td>
                        <td style="font-size: 12.5px; font-weight: 600; color: #475569;">
                            {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }} WIB
                        </td>
                        <td>
                            <div style="font-size: 12.5px; font-weight: 600; color: #166534;">
                                {{ $item->piket_waka_nama ?? '-' }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <button type="button" class="btn-icon-edit" title="Edit" onclick="openEditModal({{ json_encode($item) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-icon-delete" title="Hapus" onclick="openDeleteModal('{{ route('admin.jadwal-piket.destroy', $item->id) }}', '{{ addslashes($item->nama_guru) }}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 28px; margin-bottom: 8px; display: block;"></i>
                            Tidak ada data petugas piket yang cocok dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($semuaPetugas->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #ffffff;">
            {{ $semuaPetugas->links() }}
        </div>
        @endif
    </div>

{{-- ==================== TAB 4: HARI INI (LIVE) ==================== --}}
@elseif($activeTab === 'hari_ini')
    <div style="background: #ffffff; border-radius: 18px; padding: 18px 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.03); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <form method="GET" action="{{ route('admin.jadwal-piket') }}" style="display: flex; align-items: center; gap: 10px;">
            <input type="hidden" name="tab" value="hari_ini">
            <div>
                <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 4px;">Pilih Tanggal Acuan</span>
                <div style="display: flex; gap: 8px;">
                    <input type="date" name="tanggal" value="{{ $tanggalInput }}" class="form-control-custom" style="padding: 6px 12px; font-size: 13px;">
                    <button type="submit" class="btn-action-primary" style="padding: 6px 14px; font-size: 12.5px;">
                        <i class="fa-solid fa-magnifying-glass"></i> Cek
                    </button>
                    @if($tanggalInput !== Carbon\Carbon::today()->format('Y-m-d'))
                        <a href="{{ route('admin.jadwal-piket', ['tab' => 'hari_ini']) }}" class="btn-action-secondary" style="padding: 6px 12px; font-size: 12.5px;">
                            Hari Ini
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div style="text-align: right;">
            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Rotasi Aktif Tanggal Terpilih</div>
            <div style="display: flex; align-items: center; gap: 8px; justify-content: flex-end;">
                <span class="cycle-badge {{ strtolower($rosterTarget['siklus']) }}">
                    <i class="fa-solid fa-rotate"></i> Siklus {{ $rosterTarget['siklus'] }} (Minggu {{ $rosterTarget['siklus'] === 'A' ? 'Ganjil' : 'Genap' }})
                </span>
                <span style="font-size: 13.5px; font-weight: 800; color: #0f172a;">{{ $todayFormatted }}</span>
            </div>
        </div>
    </div>

    @if($rosterTarget['is_libur'])
        <div style="background: white; border-radius: 18px; padding: 48px; text-align: center; border: 1px solid #eef2f7; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
            <i class="fa-solid fa-calendar-xmark" style="font-size: 40px; color: #94a3b8; margin-bottom: 12px;"></i>
            <h3 style="font-size: 18px; font-weight: 800; color: #1b2559; margin-bottom: 4px;">Hari Libur Akhir Pekan ({{ $rosterTarget['hari'] }})</h3>
            <p style="font-size: 13.5px; color: #64748b;">Tidak ada jadwal kegiatan belajar mengajar dan piket KBM pada hari ini.</p>
        </div>
    @else
        <div style="background: #ffffff; border-radius: 18px; border: 1px solid #eef2f7; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03); padding: 18px 22px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #16a34a; letter-spacing: 0.5px;">Waka Piket Pimpinan (Seharian Penuh)</span>
                    <h3 style="font-size: 16px; font-weight: 800; color: #1b2559; margin-top: 2px;">
                        {{ $rosterTarget['waka']['nama'] ?? 'Belum Ditentukan' }}
                    </h3>
                    <div style="font-size: 12px; color: #64748b;">NIP: {{ $rosterTarget['waka']['nip'] ?? '-' }}</div>
                </div>
            </div>
            <span style="background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px;">
                <i class="fa-solid fa-circle-check"></i> Penanggung Jawab Harian
            </span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <!-- Shift Pagi -->
            <div class="shift-box">
                <div class="shift-header pagi">
                    <strong style="font-size: 14px; color: #92400e;"><i class="fa-solid fa-sun"></i> Shift Pagi (07.00 - 11.00 WIB)</strong>
                    <span class="role-badge koor">Pagi</span>
                </div>
                <div class="shift-body">
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #b45309; display: block; margin-bottom: 6px;">Koordinator Piket</span>
                        <div class="person-card" style="border-left: 3px solid #f59e0b;">
                            <div>
                                <strong style="font-size: 13.5px; color: #0f172a;">{{ $rosterTarget['pagi_koordinator']->nama_guru ?? '-' }}</strong>
                                <div style="font-size: 11.5px; color: #64748b;">NIP: {{ $rosterTarget['pagi_koordinator']->nip ?? '-' }}</div>
                            </div>
                            @if($rosterTarget['pagi_koordinator'])
                            <button type="button" class="btn-icon-edit" onclick="openEditModal({{ json_encode($rosterTarget['pagi_koordinator']) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">Anggota Petugas</span>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @forelse($rosterTarget['pagi_petugas'] as $idx => $p)
                            <div class="person-card">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 22px; height: 22px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">{{ $idx + 1 }}</span>
                                    <div>
                                        <strong style="font-size: 13px; color: #0f172a;">{{ $p->nama_guru }}</strong>
                                        <div style="font-size: 11px; color: #64748b;">NIP: {{ $p->nip ?? '-' }}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-icon-edit" onclick="openEditModal({{ json_encode($p) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                            @empty
                            <div style="text-align: center; color: #94a3b8; font-size: 12px; padding: 10px;">Belum ada anggota.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shift Siang -->
            <div class="shift-box">
                <div class="shift-header siang">
                    <strong style="font-size: 14px; color: #1e40af;"><i class="fa-solid fa-cloud-sun"></i> Shift Siang (11.00 - 15.00 WIB)</strong>
                    <span class="role-badge petugas" style="background:#dbeafe; color:#1e40af; border-color:#bfdbfe;">Siang</span>
                </div>
                <div class="shift-body">
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #2563eb; display: block; margin-bottom: 6px;">Koordinator Piket</span>
                        <div class="person-card" style="border-left: 3px solid #3b82f6;">
                            <div>
                                <strong style="font-size: 13.5px; color: #0f172a;">{{ $rosterTarget['siang_koordinator']->nama_guru ?? '-' }}</strong>
                                <div style="font-size: 11.5px; color: #64748b;">NIP: {{ $rosterTarget['siang_koordinator']->nip ?? '-' }}</div>
                            </div>
                            @if($rosterTarget['siang_koordinator'])
                            <button type="button" class="btn-icon-edit" onclick="openEditModal({{ json_encode($rosterTarget['siang_koordinator']) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">Anggota Petugas</span>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @forelse($rosterTarget['siang_petugas'] as $idx => $p)
                            <div class="person-card">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 22px; height: 22px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;">{{ $idx + 1 }}</span>
                                    <div>
                                        <strong style="font-size: 13px; color: #0f172a;">{{ $p->nama_guru }}</strong>
                                        <div style="font-size: 11px; color: #64748b;">NIP: {{ $p->nip ?? '-' }}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-icon-edit" onclick="openEditModal({{ json_encode($p) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                            @empty
                            <div style="text-align: center; color: #94a3b8; font-size: 12px; padding: 10px;">Belum ada anggota.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

{{-- ==================== MODAL TAMBAH PETUGAS ==================== --}}
<div class="modal-backdrop-custom" id="modalTambah">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-user-plus" style="color: #2b43b9;"></i> Tambah Petugas Piket KBM</h3>
            <button type="button" class="modal-close-btn" onclick="closeTambahModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.jadwal-piket.store') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect_tab" id="tambah_redirect_tab" value="{{ $activeTab }}">
            <div class="modal-body-custom">
                <div class="form-row-2">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Siklus Rotasi <span style="color: #ef4444;">*</span></label>
                        <select name="siklus" id="tambah_siklus" class="form-control-custom" required>
                            <option value="A" {{ $activeTab === 'siklus_a' ? 'selected' : '' }}>Siklus A (Minggu Ganjil)</option>
                            <option value="B" {{ $activeTab === 'siklus_b' ? 'selected' : '' }}>Siklus B (Minggu Genap)</option>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom">Hari Penugasan <span style="color: #ef4444;">*</span></label>
                        <select name="hari" id="tambah_hari" class="form-control-custom" required>
                            @foreach($hariOrder as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Shift Piket <span style="color: #ef4444;">*</span></label>
                        <select name="shift" id="tambah_shift" class="form-control-custom" onchange="autoFillShiftHours('tambah')" required>
                            <option value="Pagi">Shift Pagi (07.00 - 11.00 WIB)</option>
                            <option value="Siang">Shift Siang (11.00 - 15.00 WIB)</option>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom">Peran Penugasan <span style="color: #ef4444;">*</span></label>
                        <select name="peran" id="tambah_peran" class="form-control-custom" required>
                            <option value="petugas">Petugas Piket</option>
                            <option value="koordinator">Koordinator Piket</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Pilih Guru Dari Master (Otomatis Isi Data)</label>
                    <select id="tambah_guru_select" class="form-control-custom" onchange="onGuruSelected(this, 'tambah')">
                        <option value="">-- Pilih Guru SMKN 1 Boyolangu --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id_guru }}" data-nama="{{ $g->nama_lengkap }}" data-nip="{{ $g->nip }}">
                                {{ $g->nama_lengkap }} ({{ $g->nip ?: 'Non-NIP' }})
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="id_guru" id="tambah_id_guru">
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Guru Petugas <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_guru" id="tambah_nama_guru" class="form-control-custom" required placeholder="Contoh: Drs. Bambang Sutopo, M.Pd">
                </div>

                <div class="form-row-2">
                    <div class="form-group-custom">
                        <label class="form-label-custom">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" name="nip" id="tambah_nip" class="form-control-custom" placeholder="Contoh: 19750512 200003 1 005 / -">
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom">Urutan Personil</label>
                        <input type="number" name="urutan" id="tambah_urutan" class="form-control-custom" placeholder="Otomatis (1, 2, 3...)" min="1">
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="tambah_jam_mulai" value="07:00" class="form-control-custom">
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="tambah_jam_selesai" value="11:00" class="form-control-custom">
                    </div>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-action-secondary" onclick="closeTambahModal()">Batal</button>
                <button type="submit" class="btn-action-primary"><i class="fa-solid fa-save"></i> Simpan Penugasan</button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL EDIT PETUGAS ==================== --}}
<div class="modal-backdrop-custom" id="modalEdit">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-pen-to-square" style="color: #2b43b9;"></i> Edit Penugasan Petugas Piket</h3>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditJadwal" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_tab" id="edit_redirect_tab" value="{{ $activeTab }}">
            <div class="modal-body-custom">
                <div class="form-row-2">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Siklus Rotasi <span style="color: #ef4444;">*</span></label>
                        <select name="siklus" id="edit_siklus" class="form-control-custom" required>
                            <option value="A">Siklus A (Minggu Ganjil)</option>
                            <option value="B">Siklus B (Minggu Genap)</option>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom">Hari Penugasan <span style="color: #ef4444;">*</span></label>
                        <select name="hari" id="edit_hari" class="form-control-custom" required>
                            @foreach($hariOrder as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Shift Piket <span style="color: #ef4444;">*</span></label>
                        <select name="shift" id="edit_shift" class="form-control-custom" onchange="autoFillShiftHours('edit')" required>
                            <option value="Pagi">Shift Pagi (07.00 - 11.00 WIB)</option>
                            <option value="Siang">Shift Siang (11.00 - 15.00 WIB)</option>
                        </select>
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom">Peran Penugasan <span style="color: #ef4444;">*</span></label>
                        <select name="peran" id="edit_peran" class="form-control-custom" required>
                            <option value="petugas">Petugas Piket</option>
                            <option value="koordinator">Koordinator Piket</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Ganti Guru Dari Master Data</label>
                    <select id="edit_guru_select" class="form-control-custom" onchange="onGuruSelected(this, 'edit')">
                        <option value="">-- Pilih untuk mengganti guru --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id_guru }}" data-nama="{{ $g->nama_lengkap }}" data-nip="{{ $g->nip }}">
                                {{ $g->nama_lengkap }} ({{ $g->nip ?: 'Non-NIP' }})
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="id_guru" id="edit_id_guru">
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Guru Petugas <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_guru" id="edit_nama_guru" class="form-control-custom" required>
                </div>

                <div class="form-row-2">
                    <div class="form-group-custom">
                        <label class="form-label-custom">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" name="nip" id="edit_nip" class="form-control-custom">
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom">Urutan Personil</label>
                        <input type="number" name="urutan" id="edit_urutan" class="form-control-custom" min="1">
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control-custom">
                    </div>
                    <div class="form-group-custom">
                        <label class="form-label-custom">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="edit_jam_selesai" class="form-control-custom">
                    </div>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-action-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-action-primary"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL ATUR WAKA PIKET ==================== --}}
<div class="modal-backdrop-custom" id="modalWaka">
    <div class="modal-content-custom" style="max-width: 500px;">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-user-shield" style="color: #16a34a;"></i> Atur Waka Piket Harian</h3>
            <button type="button" class="modal-close-btn" onclick="closeWakaModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.jadwal-piket.update-waka') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect_tab" id="waka_redirect_tab" value="{{ $activeTab }}">
            <input type="hidden" name="siklus" id="waka_siklus">
            <input type="hidden" name="hari" id="waka_hari">

            <div class="modal-body-custom">
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px;">
                    <div style="font-size: 13.5px; font-weight: 800; color: #166534;" id="waka_target_label">
                        Hari: Senin (Siklus A)
                    </div>
                    <div style="font-size: 12px; color: #15803d; margin-top: 2px;">
                        Waka Piket bertindak sebagai pimpinan penanggung jawab umum KBM harian.
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Pilih Dari Guru / Pejabat</label>
                    <select id="waka_guru_select" class="form-control-custom" onchange="onWakaSelected(this)">
                        <option value="">-- Pilih Guru untuk auto-fill --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id_guru }}" data-nama="{{ $g->nama_lengkap }}" data-nip="{{ $g->nip }}">
                                {{ $g->nama_lengkap }} ({{ $g->nip ?: 'Non-NIP' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Lengkap Waka Piket <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="piket_waka_nama" id="waka_nama" class="form-control-custom" required placeholder="Contoh: Drs. SUJITO, M.Pd">
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">NIP Waka Piket</label>
                    <input type="text" name="piket_waka_nip" id="waka_nip" class="form-control-custom" placeholder="Contoh: 19680415 199412 1 002">
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-action-secondary" onclick="closeWakaModal()">Batal</button>
                <button type="submit" class="btn-action-primary"><i class="fa-solid fa-save"></i> Perbarui Waka Piket</button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL KONFIRMASI HAPUS ==================== --}}
<div class="modal-backdrop-custom" id="modalDelete">
    <div class="modal-content-custom" style="max-width: 420px; text-align: center; padding: 28px 24px;">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: #fee2e2; color: #ef4444; font-size: 26px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Konfirmasi Hapus</h3>
        <p style="font-size: 13.5px; color: #64748b; line-height: 1.5; margin-bottom: 24px;">
            Apakah Anda yakin ingin menghapus petugas piket <strong id="deleteTargetName" style="color: #0f172a;"></strong> dari jadwal KBM?
        </p>
        <form id="formDeleteJadwal" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="redirect_tab" value="{{ $activeTab }}">
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="btn-action-secondary" style="flex: 1;" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="btn-action-primary" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3); flex: 1;">
                    <i class="fa-solid fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Auto dismiss toasts after 4 seconds
    setTimeout(function() {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(t => {
            t.style.transition = 'all 0.3s ease';
            t.style.opacity = '0';
            t.style.transform = 'translateX(40px)';
            setTimeout(() => t.remove(), 300);
        });
    }, 4000);

    // Auto fill shift default hours
    function autoFillShiftHours(prefix) {
        const shiftVal = document.getElementById(prefix + '_shift').value;
        const jamMulai = document.getElementById(prefix + '_jam_mulai');
        const jamSelesai = document.getElementById(prefix + '_jam_selesai');

        if (shiftVal === 'Pagi') {
            jamMulai.value = '07:00';
            jamSelesai.value = '11:00';
        } else {
            jamMulai.value = '11:00';
            jamSelesai.value = '15:00';
        }
    }

    // On select guru in tambah / edit modal
    function onGuruSelected(selectEl, prefix) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            document.getElementById(prefix + '_id_guru').value = selectedOpt.value;
            document.getElementById(prefix + '_nama_guru').value = selectedOpt.getAttribute('data-nama') || '';
            document.getElementById(prefix + '_nip').value = selectedOpt.getAttribute('data-nip') || '';
        }
    }

    // On select guru in waka modal
    function onWakaSelected(selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            document.getElementById('waka_nama').value = selectedOpt.getAttribute('data-nama') || '';
            document.getElementById('waka_nip').value = selectedOpt.getAttribute('data-nip') || '';
        }
    }

    // Modal Tambah handlers
    function openTambahModal() {
        document.getElementById('modalTambah').classList.add('show');
    }
    function openTambahPreset(siklus, hari, shift) {
        document.getElementById('tambah_siklus').value = siklus;
        document.getElementById('tambah_hari').value = hari;
        document.getElementById('tambah_shift').value = shift;
        autoFillShiftHours('tambah');
        document.getElementById('tambah_redirect_tab').value = 'siklus_' + siklus.toLowerCase();
        openTambahModal();
    }
    function closeTambahModal() {
        document.getElementById('modalTambah').classList.remove('show');
    }

    // Modal Edit handlers
    function openEditModal(item) {
        const form = document.getElementById('formEditJadwal');
        form.action = "{{ url('admin/jadwal-piket') }}/" + item.id;

        document.getElementById('edit_siklus').value = item.siklus;
        document.getElementById('edit_hari').value = item.hari;
        document.getElementById('edit_shift').value = item.shift;
        document.getElementById('edit_peran').value = item.peran;
        document.getElementById('edit_id_guru').value = item.id_guru || '';
        document.getElementById('edit_nama_guru').value = item.nama_guru || '';
        document.getElementById('edit_nip').value = item.nip || '';
        document.getElementById('edit_urutan').value = item.urutan || '';
        document.getElementById('edit_jam_mulai').value = item.jam_mulai ? item.jam_mulai.substring(0, 5) : '';
        document.getElementById('edit_jam_selesai').value = item.jam_selesai ? item.jam_selesai.substring(0, 5) : '';
        document.getElementById('edit_redirect_tab').value = 'siklus_' + item.siklus.toLowerCase();

        // Sync select dropdown if matches
        const select = document.getElementById('edit_guru_select');
        select.value = item.id_guru || '';

        document.getElementById('modalEdit').classList.add('show');
    }
    function closeEditModal() {
        document.getElementById('modalEdit').classList.remove('show');
    }

    // Modal Waka handlers
    function openWakaModal(siklus, hari, nama, nip) {
        document.getElementById('waka_siklus').value = siklus;
        document.getElementById('waka_hari').value = hari;
        document.getElementById('waka_nama').value = nama || '';
        document.getElementById('waka_nip').value = nip || '';
        document.getElementById('waka_target_label').innerText = 'Hari: ' + hari + ' (Siklus ' + siklus + ')';
        document.getElementById('waka_redirect_tab').value = 'siklus_' + siklus.toLowerCase();
        document.getElementById('modalWaka').classList.add('show');
    }
    function closeWakaModal() {
        document.getElementById('modalWaka').classList.remove('show');
    }

    // Modal Delete handlers
    function openDeleteModal(url, nama) {
        document.getElementById('formDeleteJadwal').action = url;
        document.getElementById('deleteTargetName').innerText = nama;
        document.getElementById('modalDelete').classList.add('show');
    }
    function closeDeleteModal() {
        document.getElementById('modalDelete').classList.remove('show');
    }

    // Close on background click
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop-custom')) {
            e.target.classList.remove('show');
        }
    });
</script>
@endsection
