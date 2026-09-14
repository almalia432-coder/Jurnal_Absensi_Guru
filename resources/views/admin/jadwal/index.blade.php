@extends('layouts.admin')

@section('title', 'Jadwal Pelajaran — SMKN 1 BOYOLANGU')
@section('header_title', 'Jadwal Pelajaran')
@section('header_subtitle', 'Kelola alokasi jadwal mata pelajaran per kelas secara visual dengan fitur Drag & Drop')

@section('styles')
<style>
    /* Toast Notification */
    .toast-container { position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; max-width: 90vw; }
    .toast {
        display: flex; align-items: center; gap: 12px; background: #ffffff; border-radius: 14px; padding: 14px 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12); border-left: 4px solid #10b981; font-size: 13.5px; font-weight: 600; color: #0f172a;
        min-width: 280px; max-width: 420px; animation: toastSlideIn .3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .toast.error { border-left-color: #ef4444; }
    .toast.info { border-left-color: #3b82f6; }
    .toast i { font-size: 18px; color: #10b981; flex-shrink: 0; }
    .toast.error i { color: #ef4444; }
    .toast.info i { color: #3b82f6; }
    @keyframes toastSlideIn { from { opacity:0; transform:translateX(40px);} to { opacity:1; transform:translateX(0);} }

    /* Top Control Bar */
    .schedule-control-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 16px 20px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .control-left-group {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        flex: 1;
    }

    .class-selector-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        padding: 6px 14px;
        transition: all 0.2s ease;
        flex-wrap: nowrap;
    }
    .class-selector-wrap:focus-within {
        border-color: #2b43b9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(43,67,185,0.1);
    }
    .class-selector-label {
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .class-select-dropdown {
        border: none;
        background: transparent;
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        outline: none;
        cursor: pointer;
        padding: 4px 0;
        min-width: 160px;
    }

    /* Mode View Switcher (Matrix vs Table) */
    .view-mode-toggle {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        gap: 4px;
    }
    .btn-view-mode {
        border: none;
        background: transparent;
        padding: 8px 14px;
        border-radius: 9px;
        font-size: 12.5px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-view-mode:hover { color: #0f172a; }
    .btn-view-mode.active {
        background: #ffffff;
        color: #2b43b9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    /* Buttons */
    .btn-add-jadwal {
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
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(43,67,185,0.25);
        white-space: nowrap;
    }
    .btn-add-jadwal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(43,67,185,0.35);
        color: #ffffff;
    }

    .btn-import-csv {
        background: #f1f5f9;
        color: #1e293b;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 9px 16px;
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-import-csv:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #94a3b8;
    }

    .btn-ta-pill {
        background: #ffffff;
        border: 1.5px solid #c7d2fe;
        border-radius: 12px;
        padding: 7px 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-ta-pill:hover {
        border-color: #2b43b9;
        background: #f8faff;
        transform: translateY(-1px);
    }
    .btn-ta-pill .ta-badge {
        background: #e0e7ff;
        color: #3730a3;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 6px;
    }
    .btn-ta-pill .ta-name { font-size: 12.5px; font-weight: 800; color: #0f172a; }

    /* Class Banner Card (Matching Screenshot) */
    .class-banner-card {
        background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #2563eb 100%);
        border-radius: 18px;
        padding: 16px 24px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
        box-shadow: 0 8px 24px rgba(30, 58, 138, 0.18);
        position: relative;
        overflow: hidden;
        flex-wrap: wrap;
    }
    .class-banner-card::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -40px;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .class-banner-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .class-banner-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ffffff;
        backdrop-filter: blur(4px);
        flex-shrink: 0;
    }
    .class-banner-title {
        font-size: 18px;
        font-weight: 900;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }
    .class-banner-sub {
        font-size: 13px;
        color: #dbeafe;
        font-weight: 600;
        margin-top: 3px;
    }
    .class-banner-badge {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.28);
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        backdrop-filter: blur(4px);
        white-space: nowrap;
    }

    /* Mobile Scroll Hint Banner */
    .mobile-scroll-hint {
        display: none;
        align-items: center;
        gap: 8px;
        background: #eef2ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    /* Timetable Matrix Grid */
    .timetable-wrapper {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Sleek custom scrollbar */
    .timetable-wrapper::-webkit-scrollbar {
        height: 8px;
    }
    .timetable-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .timetable-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .timetable-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .timetable-grid {
        width: 100%;
        border-collapse: separate;
        border-spacing: 10px;
        min-width: 1180px; /* Generous width so all 6 day columns have 165px+ width */
    }

    /* Header pills */
    .th-pill-hdr {
        background: #1e3a8a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        text-align: center;
        padding: 12px 14px;
        border-radius: 12px;
        letter-spacing: 0.4px;
        user-select: none;
    }
    .th-pill-hdr.time-hdr {
        width: 140px;
        min-width: 140px;
        background: #1e293b;
    }
    .th-pill-hdr.day-hdr {
        min-width: 165px;
    }

    /* Time column cell */
    .td-time-slot {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px 14px;
        text-align: center;
        vertical-align: middle;
    }
    .jam-title {
        font-size: 14.5px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .jam-time-sk {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
    }
    .jam-time-jmt {
        font-size: 10.5px;
        font-weight: 700;
        color: #0284c7;
        margin-top: 2px;
    }

    /* Slot Cell Drop Zone */
    .slot-cell {
        vertical-align: top;
        position: relative;
        padding: 0;
        height: 100%;
    }

    /* Scheduled Subject Card - REDESIGNED FOR CRISP RESPONSIVENESS */
    .schedule-card {
        background: #f0f9ff;
        border: 1.5px solid #bae6fd;
        border-left: 4px solid #0284c7;
        border-radius: 14px;
        padding: 10px 12px;
        min-height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 6px;
        cursor: grab;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.06);
    }
    .schedule-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(2, 132, 199, 0.16);
        border-color: #7dd3fc;
        background: #e0f2fe;
    }
    .schedule-card.is-dragging {
        opacity: 0.45;
        transform: scale(0.96);
        cursor: grabbing;
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        border: 2px dashed #0284c7;
    }

    /* Card Top Bar (Badge + Actions) */
    .card-meta-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        padding-bottom: 4px;
        border-bottom: 1px solid rgba(186, 230, 253, 0.6);
    }

    .card-mapel-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 800;
        color: #0369a1;
        background: rgba(255, 255, 255, 0.85);
        padding: 2px 6px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        max-width: 90px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .card-actions-row {
        display: flex;
        align-items: center;
        gap: 3px;
        flex-shrink: 0;
    }
    .card-action-btn {
        background: #ffffff;
        border: 1px solid #e0f2fe;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .card-action-btn:hover { background: #0284c7; color: #ffffff; border-color: #0284c7; }
    .card-action-btn.del:hover { background: #ef4444; color: #ffffff; border-color: #ef4444; }
    .card-action-btn.drag-handle {
        color: #0284c7;
        cursor: grab;
    }
    .card-action-btn.drag-handle:active { cursor: grabbing; }

    /* Mapel Title: Full Width, naturally wraps words */
    .card-mapel-name {
        font-size: 12.5px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.35;
        word-break: normal;
        overflow-wrap: break-word;
        hyphens: auto;
    }

    /* Teacher Name */
    .card-teacher-name {
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 5px;
        line-height: 1.2;
    }
    .card-teacher-name i {
        color: #0284c7;
        font-size: 10.5px;
        flex-shrink: 0;
    }
    .card-teacher-name span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    /* Empty Slot Box (+ Isi Slot) */
    .empty-slot-box {
        width: 100%;
        height: 100%;
        min-height: 100px;
        background: #fbfcfe;
        border: 1.5px dashed #cbd5e1;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 8px;
        text-align: center;
    }
    .empty-slot-box:hover {
        background: #eff6ff;
        border-color: #2b43b9;
        color: #2b43b9;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(43, 67, 185, 0.08);
    }
    .empty-slot-box.drag-over {
        background: #dbeafe !important;
        border-color: #2563eb !important;
        border-style: solid !important;
        color: #1d4ed8 !important;
        transform: scale(1.02);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }

    /* Special slots (Upacara / Pembiasaan) */
    .special-slot-box {
        width: 100%;
        height: 100%;
        min-height: 100px;
        background: #fff7ed;
        border: 1.5px solid #fed7aa;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 8px;
        text-align: center;
        color: #9a3412;
    }
    .special-slot-title {
        font-size: 12px;
        font-weight: 800;
        line-height: 1.2;
    }
    .special-slot-sub {
        font-size: 10.5px;
        font-weight: 600;
        color: #c2410c;
    }
    .btn-override-slot {
        font-size: 10px;
        font-weight: 700;
        color: #ea580c;
        background: rgba(255,255,255,0.85);
        border: 1px solid #fdba74;
        border-radius: 6px;
        padding: 3px 8px;
        cursor: pointer;
        margin-top: 4px;
        transition: all 0.15s ease;
    }
    .btn-override-slot:hover { background: #ffffff; color: #9a3412; }

    /* Break Divider Rows */
    .break-row-cell {
        background: linear-gradient(90deg, #f1f5f9 0%, #e2e8f0 50%, #f1f5f9 100%);
        border-radius: 12px;
        padding: 10px 16px;
        text-align: center;
        font-size: 12px;
        font-weight: 800;
        color: #475569;
        letter-spacing: 0.5px;
    }
    .break-row-cell i { color: #f59e0b; margin-right: 6px; }

    /* Table Mode Styles */
    .table-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        overflow-x: auto;
    }
    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 700px; }
    .data-table th {
        font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.6px;
        padding: 12px 16px; text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    }
    .data-table th:first-child { border-radius: 10px 0 0 10px; }
    .data-table th:last-child  { border-radius: 0 10px 10px 0; }
    .data-table td {
        padding: 14px 16px; font-size: 13.5px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
    }
    .data-table tbody tr:hover td { background: #f8fafc; }

    .badge-hari { display: inline-flex; align-items: center; gap: 6px; font-weight: 800; font-size: 12px; padding: 5px 14px; border-radius: 20px; }
    .badge-hari.senin   { background: #dbeafe; color: #1e40af; }
    .badge-hari.selasa  { background: #d1fae5; color: #065f46; }
    .badge-hari.rabu    { background: #fef3c7; color: #92400e; }
    .badge-hari.kamis   { background: #ede9fe; color: #5b21b6; }
    .badge-hari.jumat   { background: #fce7f3; color: #9d174d; }
    .badge-hari.sabtu   { background: #ffedd5; color: #9a3412; }

    /* Modal Form Styles */
    .modal-bd { position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(5px); display: none; align-items: center; justify-content: center; z-index: 9990; padding: 16px; }
    .modal-bd.show { display: flex; }
    .modal-bx {
        background: #ffffff; border-radius: 24px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; padding: 24px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: modalSlideUp 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes modalSlideUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }

    .modal-hdr { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
    .modal-ttl { font-size: 18px; font-weight: 800; color: #0f172a; }
    .btn-modal-close { background: #f1f5f9; border: none; border-radius: 10px; width: 34px; height: 34px; cursor: pointer; font-size: 16px; color: #64748b; display: flex; align-items: center; justify-content: center; transition: all 0.15s ease; }
    .btn-modal-close:hover { background: #fee2e2; color: #ef4444; }

    .form-grid-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-group-item { display: flex; flex-direction: column; gap: 6px; }
    .form-group-item.full-width { grid-column: 1 / -1; }
    .form-field-lbl { font-size: 12px; font-weight: 700; color: #475569; }
    .form-field-ctrl {
        background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 10px 14px;
        font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; width: 100%; transition: all 0.2s ease;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .form-field-ctrl:focus { border-color: #2b43b9; background: #ffffff; box-shadow: 0 0 0 3px rgba(43,67,185,0.1); }

    .form-btn-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 22px; }
    .btn-modal-cancel { background: #f1f5f9; border: none; border-radius: 12px; padding: 10px 20px; font-size: 13.5px; font-weight: 700; color: #475569; cursor: pointer; }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-submit { background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%); color: #ffffff; border: none; border-radius: 12px; padding: 10px 22px; font-size: 13.5px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(43,67,185,0.25); }

    .jam-pill {
        width: 36px; height: 36px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.15s ease;
    }
    .jam-pill:hover { border-color: #2b43b9; color: #2b43b9; background: #eef2ff; }
    .jam-pill.endpoint { background: #2b43b9; color: #ffffff; border-color: #1e40af; box-shadow: 0 2px 8px rgba(43,67,185,0.35); }
    .jam-pill.in-range { background: #e0e7ff; color: #3730a3; border-color: #a5b4fc; }

    /* ==================== RESPONSIVE MEDIA QUERIES ==================== */
    @media (max-width: 1024px) {
        .mobile-scroll-hint { display: inline-flex; }
        .schedule-control-card {
            flex-direction: column;
            align-items: stretch;
        }
        .control-left-group {
            width: 100%;
            justify-content: space-between;
        }
        .class-selector-wrap {
            flex: 1;
        }
    }

    @media (max-width: 768px) {
        .class-banner-card {
            flex-direction: column;
            align-items: flex-start;
            padding: 16px;
        }
        .class-banner-badge {
            align-self: flex-start;
        }
        .form-grid-layout {
            grid-template-columns: 1fr;
        }
        .form-group-item.full-width {
            grid-column: 1;
        }
        .timetable-wrapper {
            padding: 12px;
            border-radius: 16px;
        }
        .btn-add-jadwal {
            width: 100%;
            justify-content: center;
        }
        .view-mode-toggle {
            width: 100%;
        }
        .btn-view-mode {
            flex: 1;
            justify-content: center;
            padding: 10px 8px;
            font-size: 11.5px;
        }
    }
</style>
@endsection

@section('content')

{{-- Toast Notification --}}
<div class="toast-container" id="toastContainer">
    @if(session('success'))
        <div class="toast"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error') || (isset($errors) && $errors->any()))
        <div class="toast error"><i class="fa-solid fa-circle-xmark"></i><span>{{ session('error') ?? ($errors->first() ?? '') }}</span></div>
    @endif
</div>

{{-- Top Control Bar --}}
<div class="schedule-control-card">
    <div class="control-left-group">
        {{-- Class Matriks Selector --}}
        <div class="class-selector-wrap">
            <span class="class-selector-label">
                <i class="fa-solid fa-door-open" style="color:#2b43b9;"></i> Pilih Kelas Matriks:
            </span>
            <select class="class-select-dropdown" id="classSelector" onchange="onClassChange(this.value)">
                @foreach($kelasList as $k)
                    <option value="{{ $k->id_kelas }}" {{ ($selectedKelas && $selectedKelas->id_kelas == $k->id_kelas) ? 'selected' : '' }}>
                        Kelas {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tahun Ajaran Pill --}}
        <button type="button" class="btn-ta-pill" onclick="openSetTAModal()" title="Klik untuk atur Tahun Ajaran Aktif">
            <i class="fa-solid fa-graduation-cap" style="color:#6366f1;"></i>
            <span class="ta-badge">{{ $selectedTahunAjaran === 'all' ? 'Semua TA' : ($tahunAjaranAktif && $selectedTahunAjaran == $tahunAjaranAktif->id ? 'TA Aktif' : 'TA') }}</span>
            <span class="ta-name">
                @if($selectedTahunAjaran === 'all')
                    Semua Tahun Ajaran
                @else
                    {{ $tahunAjaranList->firstWhere('id', $selectedTahunAjaran)->nama ?? ($tahunAjaranAktif->nama ?? '-') }}
                    ({{ $tahunAjaranList->firstWhere('id', $selectedTahunAjaran)->semester ?? ($tahunAjaranAktif->semester ?? '-') }})
                @endif
                <i class="fa-solid fa-pen" style="font-size:10px;margin-left:4px;color:#818cf8;"></i>
            </span>
        </button>
    </div>

    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        {{-- View Mode Switcher --}}
        <div class="view-mode-toggle">
            <button type="button" class="btn-view-mode {{ $viewMode === 'matrix' ? 'active' : '' }}" onclick="switchViewMode('matrix')">
                <i class="fa-solid fa-table-cells"></i> Matriks per Kelas
            </button>
            <button type="button" class="btn-view-mode {{ $viewMode === 'table' ? 'active' : '' }}" onclick="switchViewMode('table')">
                <i class="fa-solid fa-list-ul"></i> Tabel Data
            </button>
        </div>

        {{-- Import CSV Button --}}
        <button type="button" class="btn-import-csv" onclick="openImportModal()">
            <i class="fa-solid fa-file-arrow-up"></i> Import CSV
        </button>

        {{-- Add Schedule Button --}}
        <button type="button" class="btn-add-jadwal" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Tambah Jadwal
        </button>
    </div>
</div>

@if($viewMode === 'matrix')
    {{-- ==================== MODE 1: MATRIKS TIMETABLE PER KELAS ==================== --}}
    
    @if($selectedKelas)
        {{-- Class Banner Card --}}
        <div class="class-banner-card">
            <div class="class-banner-left">
                <div class="class-banner-icon">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div>
                    <div class="class-banner-title">Kelas {{ $selectedKelas->nama_kelas }}</div>
                    <div class="class-banner-sub">
                        Wali Kelas: {{ $selectedKelas->waliKelas->nama_lengkap ?? 'Belum ditentukan' }}
                        @if($selectedKelas->jurusanRelation)
                            &bull; Jurusan {{ $selectedKelas->jurusanRelation->nama_jurusan }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="class-banner-badge">
                <i class="fa-solid fa-calendar-check"></i>
                <span>{{ $totalMapelKelas }} Mapel Terjadwal ({{ $totalJamKelas }} Jam)</span>
            </div>
        </div>

        {{-- Mobile Swipe Hint --}}
        <div class="mobile-scroll-hint">
            <i class="fa-solid fa-arrows-left-right"></i>
            <span>Geser tabel ke kanan/kiri untuk melihat seluruh hari (Senin s/d Sabtu)</span>
        </div>

        {{-- Timetable Matrix Grid --}}
        <div class="timetable-wrapper">
            <table class="timetable-grid">
                <thead>
                    <tr>
                        <th class="th-pill-hdr time-hdr">
                            <i class="fa-regular fa-clock" style="margin-right:4px;"></i> Jam \ Hari
                        </th>
                        <th class="th-pill-hdr day-hdr">Senin</th>
                        <th class="th-pill-hdr day-hdr">Selasa</th>
                        <th class="th-pill-hdr day-hdr">Rabu</th>
                        <th class="th-pill-hdr day-hdr">Kamis</th>
                        <th class="th-pill-hdr day-hdr">Jumat</th>
                    </tr>
                </thead>
                <tbody>
                    @for($jam = 1; $jam <= 10; $jam++)
                        <tr>
                            {{-- Time Slot Header Column --}}
                            <td class="td-time-slot">
                                <div class="jam-title">Jam {{ $jam }}</div>
                                <div class="jam-time-sk">S-K: {{ $slotTimesSK[$jam]['jam_mulai'] ?? '07.00' }} - {{ $slotTimesSK[$jam]['jam_selesai'] ?? '07.40' }}</div>
                                <div class="jam-time-jmt">Jmt: {{ $slotTimesJmt[$jam]['jam_mulai'] ?? '07.00' }} - {{ $slotTimesJmt[$jam]['jam_selesai'] ?? '07.30' }}</div>
                            </td>

                            {{-- Days Columns --}}
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                <td class="slot-cell" data-hari="{{ $hari }}" data-jam="{{ $jam }}" data-kelas="{{ $selectedKelas->id_kelas }}">
                                    @if(isset($matrixJadwal[$hari][$jam]))
                                        @php $j = $matrixJadwal[$hari][$jam]; @endphp
                                        {{-- Draggable Schedule Card (Redesigned Header + Full Width Title) --}}
                                        <div class="schedule-card" 
                                             id="card-{{ $j->id_jadwal }}"
                                             draggable="true"
                                             data-jadwal-id="{{ $j->id_jadwal }}"
                                             data-hari="{{ $hari }}"
                                             data-jam="{{ $jam }}"
                                             data-mapel="{{ $j->mapel->nama_mapel ?? '' }}"
                                             data-guru="{{ $j->guru->nama_lengkap ?? '' }}"
                                             ondragstart="handleDragStart(event)"
                                             ondragend="handleDragEnd(event)">
                                            
                                            {{-- Meta Bar: Code Badge + Action Buttons --}}
                                            <div class="card-meta-bar">
                                                <span class="card-mapel-badge" title="{{ $j->mapel->kode_mapel ?? 'MAPEL' }}">
                                                    <i class="fa-solid fa-book-bookmark"></i>
                                                    {{ $j->mapel->kode_mapel ?? 'MAPEL' }}
                                                </span>
                                                <div class="card-actions-row">
                                                    <button type="button" class="card-action-btn drag-handle" title="Tahan &amp; geser untuk pindah slot" draggable="false">
                                                        <i class="fa-solid fa-up-down-left-right"></i>
                                                    </button>
                                                    <button type="button" class="card-action-btn" title="Edit Jadwal" onclick="openEditModal({{ $j->id_jadwal }}, '{{ addslashes($j->tahunAjaran->nama ?? '') }}', '{{ $j->tahunAjaran->semester ?? 'Ganjil' }}', '{{ $hari }}', {{ $jam }}, {{ $jam }}, '{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}', '{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}', {{ $j->id_mapel }}, {{ $j->id_guru }}, {{ $j->id_kelas }})">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <button type="button" class="card-action-btn del" title="Hapus Jadwal" onclick="openDeleteModal({{ $j->id_jadwal }}, '{{ addslashes($j->mapel->nama_mapel ?? '') }} ({{ $hari }}, Jam {{ $jam }})')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Full Width Mapel Title --}}
                                            <div class="card-mapel-name" title="{{ $j->mapel->nama_mapel ?? '' }}">
                                                {{ $j->mapel->nama_mapel ?? '-' }}
                                            </div>

                                            {{-- Teacher Footer --}}
                                            <div class="card-teacher-name" title="{{ $j->guru->nama_lengkap ?? '' }}">
                                                <i class="fa-solid fa-chalkboard-user"></i>
                                                <span>{{ $j->guru->nama_lengkap ?? 'Guru Belum Ditentukan' }}</span>
                                            </div>
                                        </div>

                                    @elseif($hari === 'Senin' && $jam === 1)
                                        {{-- Special Slot: Upacara / Pembiasaan --}}
                                        <div class="special-slot-box" 
                                             ondragover="handleDragOver(event)" 
                                             ondragleave="handleDragLeave(event)" 
                                             ondrop="handleDrop(event)">
                                            <div class="special-slot-title">☕ Jam ke-1</div>
                                            <div class="special-slot-sub">Upacara / Pembiasaan</div>
                                            <button type="button" class="btn-override-slot" onclick="openAddModalForSlot('{{ $hari }}', {{ $jam }})">+ Isi Mapel</button>
                                        </div>

                                    @elseif($hari === 'Jumat' && $jam === 1)
                                        {{-- Special Slot: Pembiasaan Keagamaan / Senam --}}
                                        <div class="special-slot-box" 
                                             ondragover="handleDragOver(event)" 
                                             ondragleave="handleDragLeave(event)" 
                                             ondrop="handleDrop(event)">
                                            <div class="special-slot-title">☕ Jam ke-1</div>
                                            <div class="special-slot-sub">Dhuha / Senam Pagi</div>
                                            <button type="button" class="btn-override-slot" onclick="openAddModalForSlot('{{ $hari }}', {{ $jam }})">+ Isi Mapel</button>
                                        </div>

                                    @else
                                        {{-- Empty Clickable & Droppable Slot --}}
                                        <div class="empty-slot-box" 
                                             onclick="openAddModalForSlot('{{ $hari }}', {{ $jam }})"
                                             ondragover="handleDragOver(event)" 
                                             ondragleave="handleDragLeave(event)" 
                                             ondrop="handleDrop(event)">
                                            <i class="fa-solid fa-plus" style="font-size:11px;"></i>
                                            <span>Isi Slot</span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Istirahat 1 Divider (After Jam 4) --}}
                        @if($jam === 4)
                        <tr>
                            <td colspan="6" class="break-row-cell">
                                <i class="fa-solid fa-mug-hot"></i> ISTIRAHAT 1 (09.40 — 09.55 WIB)
                            </td>
                        </tr>
                        @endif

                        {{-- Istirahat 2 / Dzuhur Divider (After Jam 7) --}}
                        @if($jam === 7)
                        <tr>
                            <td colspan="6" class="break-row-cell" style="background:linear-gradient(90deg, #f0fdf4 0%, #dcfce7 50%, #f0fdf4 100%);color:#166534;">
                                <i class="fa-solid fa-mosque" style="color:#16a34a;"></i> ISTIRAHAT 2 &amp; SHOLAT DZUHUR (11.55 — 12.35 WIB)
                            </td>
                        </tr>
                        @endif

                    @endfor
                </tbody>
            </table>
        </div>
    @else
        <div style="background:#ffffff;border-radius:20px;padding:48px 20px;text-align:center;color:#64748b;">
            <i class="fa-solid fa-door-open" style="font-size:42px;color:#cbd5e1;margin-bottom:12px;display:block;"></i>
            <h4>Belum ada data kelas yang tersedia</h4>
            <p style="font-size:13px;margin-top:4px;">Silakan buat data kelas terlebih dahulu di menu Master Data &gt; Kelas.</p>
        </div>
    @endif

@else
    {{-- ==================== MODE 2: TABEL SELURUH DATA JADWAL ==================== --}}
    
    <div class="table-card">
        <form action="{{ route('admin.jadwal') }}" method="GET" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
            <input type="hidden" name="mode" value="table">
            <input type="text" name="search" value="{{ $search }}" class="form-field-ctrl" placeholder="Cari mapel, guru, kelas…" style="width:240px;">
            
            <select name="hari" class="form-field-ctrl" style="width:140px;" onchange="this.form.submit()">
                <option value="">Semua Hari</option>
                @foreach($hariList as $h)
                    <option value="{{ $h }}" {{ $hari === $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>

            <select name="kelas" class="form-field-ctrl" style="width:160px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id_kelas }}" {{ $kelasFilter == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-add-jadwal" style="padding:8px 16px;">
                <i class="fa-solid fa-magnifying-glass"></i> Filter
            </button>

            @if($search || $hari || $kelasFilter)
                <a href="{{ route('admin.jadwal', ['mode' => 'table']) }}" class="btn-modal-cancel" style="padding:8px 14px;text-decoration:none;display:inline-flex;align-items:center;">
                    <i class="fa-solid fa-xmark" style="margin-right:6px;"></i> Reset
                </a>
            @endif
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:44px;">#</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengajar</th>
                    <th>Kelas</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwalList as $i => $j)
                <tr>
                    <td style="color:#94a3b8;font-weight:700;font-size:12px;">{{ $jadwalList->firstItem() + $i }}</td>
                    <td>
                        <span class="badge-hari {{ strtolower($j->hari) }}">
                            <i class="fa-solid fa-calendar-day"></i> {{ $j->hari }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:800;color:#0f172a;">Jam {{ $j->jam_ke }}</div>
                        <div style="font-size:11px;color:#94a3b8;font-weight:600;">
                            {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} — {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:800;color:#0f172a;">{{ $j->mapel->nama_mapel ?? '-' }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ $j->mapel->kode_mapel ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-weight:700;color:#0f172a;">{{ $j->guru->nama_lengkap ?? '-' }}</div>
                    </td>
                    <td>
                        <span style="background:#f1f5f9;color:#334155;font-weight:800;font-size:12px;padding:4px 10px;border-radius:8px;">
                    <td><span class="badge-jam">Jam Ke-{{ $j->jam_ke }}</span></td>
                    <td>
                        <span class="badge-mapel">{{ $j->mapel->nama_mapel ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="guru-item-cell">
                            <div class="guru-avatar-circle">
                                {{ strtoupper(substr($j->guru->nama_lengkap ?? 'G', 0, 1)) }}
                            </div>
                            <span class="guru-name-txt">{{ $j->guru->nama_lengkap ?? '-' }}</span>
                        </div>
                    </td>
                    <td><span class="badge-kelas">{{ $j->kelas->nama_kelas ?? '-' }}</span></td>
                    <td>
                        <div class="action-btns-group" style="justify-content:center;">
                            <button class="btn-table-edit" title="Edit Jadwal" onclick="openEditModal({{ $j->id_jadwal }}, '{{ $j->hari }}', '{{ $j->jam_ke }}', '{{ $j->id_mapel }}', '{{ $j->id_guru }}', '{{ $j->id_kelas }}')">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn-table-delete" title="Hapus Jadwal" onclick="openDeleteModal({{ $j->id_jadwal }}, '{{ addslashes($j->mapel->nama_mapel ?? 'Jadwal') }}', '{{ $j->hari }} (Jam {{ $j->jam_ke }}) - {{ $j->kelas->nama_kelas ?? '' }}')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px 20px;color:#94a3b8;">
                        <i class="fa-solid fa-calendar-xmark" style="font-size:42px;margin-bottom:12px;color:#cbd5e1;display:block;"></i>
                        <p style="font-weight:800;color:#0f172a;">Tidak ada data jadwal ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>{{-- end table-responsive-wrap --}}

        <div style="margin-top:16px;">
            {{ $jadwalList->links() }}
        </div>
    </div>

@endif

{{-- ==================== MODALS ==================== --}}

{{-- Add / Edit Jadwal Modal --}}
<div class="modal-bd" id="jadwalModal">
    <div class="modal-bx">
        <div class="modal-hdr">
            <h4 class="modal-ttl" id="modalTitle">Tambah Jadwal Pelajaran</h4>
            <button class="btn-modal-close" onclick="closeModal('jadwalModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="jadwalForm" method="POST" action="{{ route('admin.jadwal.store') }}">
            @csrf
            <span id="methodField"></span>

            <div class="form-grid-layout">
                <div class="form-group-item">
                    <label class="form-field-lbl">Tahun Ajaran <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama_tahun_ajaran" id="f_nama_ta" class="form-field-ctrl" list="taList" placeholder="Contoh: 2024/2025" required>
                    <datalist id="taList">
                        @foreach($existingTahunList as $t)
                            <option value="{{ $t }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group-item">
                    <label class="form-field-lbl">Semester <span style="color:#ef4444">*</span></label>
                    <select name="semester" id="f_semester" class="form-field-ctrl" required>
                        <option value="Ganjil">Semester Ganjil</option>
                        <option value="Genap">Semester Genap</option>
                    </select>
                </div>

                <div class="form-group-item">
                    <label class="form-field-lbl">Kelas <span style="color:#ef4444">*</span></label>
                    <select name="id_kelas" id="f_kelas" class="form-field-ctrl" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id_kelas }}" {{ ($selectedKelas && $selectedKelas->id_kelas == $k->id_kelas) ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-item">
                    <label class="form-field-lbl">Hari <span style="color:#ef4444">*</span></label>
                    <select name="hari" id="f_hari" class="form-field-ctrl" required onchange="autoFillSlotTimes()">
                        <option value="">-- Pilih Hari --</option>
                        @foreach($hariList as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-item full-width">
                    <label class="form-field-lbl">Jam Ke- <span style="color:#ef4444">*</span> <span style="color:#64748b;font-size:11px;">(Pilih 1 jam atau rentang hingga 4 jam berturut-turut)</span></label>
                    <input type="hidden" name="jam_dari" id="f_jam_dari">
                    <input type="hidden" name="jam_sampai" id="f_jam_sampai">
                    <div id="jamSelectorWrap" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
                        @for($jNum = 1; $jNum <= 10; $jNum++)
                            <button type="button" class="jam-pill" data-jam="{{ $jNum }}" onclick="toggleJamPill({{ $jNum }})">
                                {{ $jNum }}
                            </button>
                        @endfor
                    </div>
                    <div id="jamRangeLabel" style="font-size:11.5px;color:#2b43b9;font-weight:700;margin-top:6px;"></div>
                </div>

                <div class="form-group-item">
                    <label class="form-field-lbl">Jam Mulai</label>
                    <input type="time" name="jam_mulai" id="f_jam_mulai" class="form-field-ctrl">
                </div>
                <div class="form-group-item">
                    <label class="form-field-lbl">Jam Selesai</label>
                    <input type="time" name="jam_selesai" id="f_jam_selesai" class="form-field-ctrl">
                </div>

                <div class="form-group-item full-width">
                    <label class="form-field-lbl">Mata Pelajaran <span style="color:#ef4444">*</span></label>
                    <select name="id_mapel" id="f_mapel" class="form-field-ctrl" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id_mapel }}">{{ $m->kode_mapel }} — {{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-item full-width">
                    <label class="form-field-lbl">Guru Pengajar <span style="color:#ef4444">*</span></label>
                    <select name="id_guru" id="f_guru" class="form-field-ctrl" required>
                        <option value="">-- Pilih Guru Pengajar --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id_guru }}">{{ $g->nama_lengkap }} ({{ $g->nip ?? 'NIP -' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-btn-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('jadwalModal')">Batal</button>
                <button type="submit" class="btn-modal-submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

{{-- Atur Tahun Ajaran Modal --}}
<div class="modal-bd" id="setTAModal">
    <div class="modal-bx" style="max-width:480px;">
        <div class="modal-hdr">
            <h4 class="modal-ttl">Atur Tahun Ajaran Aktif</h4>
            <button class="btn-modal-close" onclick="closeModal('setTAModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form method="POST" action="{{ route('admin.jadwal.set-ta') }}">
            @csrf
            <input type="hidden" name="kelas" value="{{ $selectedKelas->id_kelas ?? '' }}">
            <input type="hidden" name="mode" id="ta_mode" value="manual">

            <div style="margin-bottom:14px;">
                <label class="form-field-lbl">Tahun Ajaran <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama_tahun_ajaran" class="form-field-ctrl" list="taList" placeholder="Contoh: 2026/2027" value="{{ $tahunAjaranAktif->nama ?? date('Y').'/'.(date('Y')+1) }}" required>
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-field-lbl">Semester <span style="color:#ef4444">*</span></label>
                <select name="semester" class="form-field-ctrl" required>
                    <option value="Ganjil" {{ ($tahunAjaranAktif->semester ?? '') === 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="Genap" {{ ($tahunAjaranAktif->semester ?? '') === 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </div>

            <div class="form-btn-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('setTAModal')">Batal</button>
                <button type="submit" class="btn-modal-submit"><i class="fa-solid fa-check"></i> Terapkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal-bd" id="deleteModal">
    <div class="modal-bx" style="max-width:440px;">
        <div class="modal-hdr">
            <h4 class="modal-ttl">Hapus Jadwal Pelajaran</h4>
            <button class="btn-modal-close" onclick="closeModal('deleteModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="text-align:center;padding:12px 0 20px;">
            <div style="font-size:46px;color:#ef4444;margin-bottom:14px;"><i class="fa-solid fa-trash-can"></i></div>
            <div style="font-weight:800;color:#0f172a;font-size:17px;margin-bottom:8px;" id="deleteName"></div>
            <div style="font-size:13px;color:#64748b;">Apakah Anda yakin ingin menghapus jadwal ini? Tindakan ini tidak dapat dibatalkan.</div>
        </div>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="form-btn-actions" style="justify-content:center;">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('deleteModal')">Batal</button>
                <button type="submit" class="btn-modal-submit" style="background:#ef4444;"><i class="fa-solid fa-trash"></i> Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

{{-- Import CSV Modal --}}
<div class="modal-bd" id="importModal">
    <div class="modal-bx" style="max-width:540px;">
        <div class="modal-hdr">
            <h4 class="modal-ttl"><i class="fa-solid fa-file-csv" style="color:#2b43b9;margin-right:8px;"></i>Import Jadwal dari File CSV</h4>
            <button class="btn-modal-close" onclick="closeModal('importModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;margin-bottom:18px;font-size:12.5px;color:#475569;line-height:1.5;">
            <div style="font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-circle-info" style="color:#3b82f6;"></i> Petunjuk Format Kolom:
            </div>
            Gunakan format kolom: <b>nama_kelas, mapel, guru, hari, jam_dari, jam_sampai, tahun_ajaran, semester</b>.<br>
            Sistem otomatis mengecek bentrok guru &amp; kelas serta menghitung durasi waktu KBM (Senin s/d Jumat).
            <div style="margin-top:10px;">
                <a href="{{ route('admin.import.template', 'jadwal') }}" class="btn-ta-pill" style="font-size:11.5px;padding:6px 12px;text-decoration:none;">
                    <i class="fa-solid fa-download"></i> Unduh Template CSV Jadwal
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.jadwal.import-csv') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:18px;">
                <label class="form-field-lbl">Pilih File CSV (.csv / .txt) <span style="color:#ef4444">*</span></label>
                <input type="file" name="file_csv" accept=".csv, .txt, text/csv" class="form-field-ctrl" required style="padding:8px 10px;">
            </div>

            <div class="form-btn-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('importModal')">Batal</button>
                <button type="submit" class="btn-modal-submit">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Unggah &amp; Proses Impor
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    const currentClassId = '{{ $selectedKelas->id_kelas ?? "" }}';
    const currentViewMode = '{{ $viewMode }}';
    const currentTA = '{{ $selectedTahunAjaran }}';

    // Standard slot times mapping
    const slotTimesSK = @json($slotTimesSK);
    const slotTimesJmt = @json($slotTimesJmt);

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type === 'error' ? 'error' : (type === 'info' ? 'info' : '')}`;
        const icon = type === 'error' ? 'fa-circle-xmark' : (type === 'info' ? 'fa-circle-info' : 'fa-circle-check');
        toast.innerHTML = `<i class="fa-solid ${icon}"></i><span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    }

    function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    function openSetTAModal() { document.getElementById('setTAModal').classList.add('show'); }
    function openImportModal() { document.getElementById('importModal').classList.add('show'); }

    function onClassChange(classId) {
        window.location.href = `{{ route('admin.jadwal') }}?kelas=${classId}&mode=${currentViewMode}&tahun_ajaran=${currentTA}`;
    }

    function switchViewMode(mode) {
        window.location.href = `{{ route('admin.jadwal') }}?kelas=${currentClassId}&mode=${mode}&tahun_ajaran=${currentTA}`;
    }

    // ==================== DRAG & DROP ENGINE ====================
    let draggedData = null;

    function handleDragStart(e) {
        const card = e.currentTarget;
        draggedData = {
            id: card.dataset.jadwalId,
            hari: card.dataset.hari,
            jam: parseInt(card.dataset.jam),
            mapel: card.dataset.mapel,
            guru: card.dataset.guru,
            elementId: card.id
        };

        e.dataTransfer.setData('text/plain', JSON.stringify(draggedData));
        e.dataTransfer.effectAllowed = 'move';

        setTimeout(() => {
            card.classList.add('is-dragging');
        }, 10);
    }

    function handleDragEnd(e) {
        const card = e.currentTarget;
        card.classList.remove('is-dragging');
        document.querySelectorAll('.empty-slot-box, .special-slot-box').forEach(el => {
            el.classList.remove('drag-over');
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        const target = e.currentTarget;
        if (!target.classList.contains('drag-over')) {
            target.classList.add('drag-over');
        }
    }

    function handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    async function handleDrop(e) {
        e.preventDefault();
        const dropBox = e.currentTarget;
        dropBox.classList.remove('drag-over');

        const cell = dropBox.closest('.slot-cell');
        if (!cell || !draggedData) return;

        const targetHari = cell.dataset.hari;
        const targetJam = parseInt(cell.dataset.jam);
        const targetKelas = cell.dataset.kelas;

        if (draggedData.hari === targetHari && draggedData.jam === targetJam) {
            return;
        }

        // Send AJAX Move Request
        try {
            const response = await fetch(`/admin/jadwal/${draggedData.id}/move`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    target_hari: targetHari,
                    target_jam: targetJam,
                    id_kelas: targetKelas
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showToast(result.message || 'Jadwal berhasil dipindahkan!', 'success');
                // Reload matrix to update cleanly
                setTimeout(() => window.location.reload(), 450);
            } else {
                showToast(result.message || 'Gagal memindahkan jadwal.', 'error');
            }
        } catch (error) {
            console.error('Error moving schedule:', error);
            showToast('Terjadi kesalahan koneksi saat memindahkan jadwal.', 'error');
        }
    }

    // ==================== JAM RANGE PICKER ====================
    let jamDari = null;
    let jamSampai = null;

    function toggleJamPill(jam) {
        if (jamDari === null) {
            jamDari = jam;
            jamSampai = jam;
        } else if (jamDari === jam && jamSampai === jam) {
            jamDari = null;
            jamSampai = null;
        } else {
            const lo = Math.min(jamDari, jam);
            const hi = Math.max(jamDari, jam);
            if (hi - lo + 1 > 4) {
                alert('Maksimal pemilihan adalah 4 jam berturut-turut.');
                return;
            }
            jamDari = lo;
            jamSampai = hi;
        }
        syncJamPills();
        autoFillSlotTimes();
    }

    function syncJamPills() {
        document.querySelectorAll('.jam-pill').forEach(btn => {
            const j = parseInt(btn.dataset.jam);
            btn.classList.remove('endpoint', 'in-range');
            if (jamDari !== null) {
                if (j === jamDari || j === jamSampai) {
                    btn.classList.add('endpoint');
                } else if (j > jamDari && j < jamSampai) {
                    btn.classList.add('in-range');
                }
            }
        });

        const dari = document.getElementById('f_jam_dari');
        const sampai = document.getElementById('f_jam_sampai');
        const label = document.getElementById('jamRangeLabel');

        if (jamDari !== null) {
            dari.value = jamDari;
            sampai.value = jamSampai;
            label.textContent = (jamDari === jamSampai) 
                ? `✔ Jam ke-${jamDari}` 
                : `✔ Jam ke-${jamDari} s/d ${jamSampai} (${jamSampai - jamDari + 1} jam)`;
        } else {
            dari.value = '';
            sampai.value = '';
            label.textContent = '';
        }
    }

    function resetJamPicker() {
        jamDari = null;
        jamSampai = null;
        syncJamPills();
    }

    function setJamPickerRange(dari, sampai) {
        jamDari = parseInt(dari);
        jamSampai = parseInt(sampai);
        syncJamPills();
    }

    function autoFillSlotTimes() {
        const hari = document.getElementById('f_hari').value;
        if (!hari || !jamDari) return;

        const isJmt = hari.toLowerCase() === 'jumat';
        const sourceMap = isJmt ? slotTimesJmt : slotTimesSK;

        if (sourceMap[jamDari] && sourceMap[jamSampai]) {
            document.getElementById('f_jam_mulai').value = sourceMap[jamDari].jam_mulai;
            document.getElementById('f_jam_selesai').value = sourceMap[jamSampai].jam_selesai;
        }
    }

    // ==================== MODAL HELPERS ====================
    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Jadwal Pelajaran';
        document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-plus"></i> Simpan Jadwal';
        const form = document.getElementById('jadwalForm');
        form.action = '{{ route("admin.jadwal.store") }}';
        document.getElementById('methodField').innerHTML = '';
        form.reset();
        resetJamPicker();

        @if($tahunAjaranAktif)
        document.getElementById('f_nama_ta').value = '{{ $tahunAjaranAktif->nama }}';
        document.getElementById('f_semester').value = '{{ $tahunAjaranAktif->semester }}';
        @endif

        if (currentClassId) {
            document.getElementById('f_kelas').value = currentClassId;
        }

        document.getElementById('jadwalModal').classList.add('show');
    }

    function openAddModalForSlot(hari, jam) {
        openAddModal();
        document.getElementById('f_hari').value = hari;
        setJamPickerRange(jam, jam);
        autoFillSlotTimes();
    }

    function openEditModal(id, namaTA, semester, hari, jamDariVal, jamSampaiVal, jamMulai, jamSelesai, mapel, guru, kelas) {
        document.getElementById('modalTitle').innerText = 'Edit Jadwal Pelajaran';
        document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Perbarui Jadwal';
        const form = document.getElementById('jadwalForm');
        form.action = '/admin/jadwal/' + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';

        document.getElementById('f_nama_ta').value = namaTA;
        document.getElementById('f_semester').value = semester;
        document.getElementById('f_hari').value = hari;
        document.getElementById('f_jam_mulai').value = jamMulai;
        document.getElementById('f_jam_selesai').value = jamSelesai;
        document.getElementById('f_mapel').value = mapel;
        document.getElementById('f_guru').value = guru;
        document.getElementById('f_kelas').value = kelas;

        setJamPickerRange(jamDariVal, jamSampaiVal);
        document.getElementById('jadwalModal').classList.add('show');
    }

    function openDeleteModal(id, nama) {
        document.getElementById('deleteName').innerText = nama;
        document.getElementById('deleteForm').action = '/admin/jadwal/' + id;
        document.getElementById('deleteModal').classList.add('show');
    }

    ['jadwalModal', 'setTAModal', 'deleteModal', 'importModal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if(e.target === this) closeModal(id);
        });
    });

    document.getElementById('jadwalForm').addEventListener('submit', function(e) {
        const dari = document.getElementById('f_jam_dari').value;
        const sampai = document.getElementById('f_jam_sampai').value;
        if (!dari || !sampai) {
            e.preventDefault();
            alert('Wajib memilih setidaknya 1 jam pelajaran pada pilihan Jam Ke-!');
            return false;
        }
    });
</script>
@endsection
