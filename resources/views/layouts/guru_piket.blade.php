<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guru Piket Dashboard - Jurnal Absensi SMKN 1 BOYOLANGU')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f2f4f8;
            color: #2b3674;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            height: 100vh;
            background-color: #f6f7fb;
            border-right: 1px solid #e3e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 28px 20px;
            flex-shrink: 0;
            overflow-y: auto;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .sidebar-logo-img {
            width: 46px;
            height: 46px;
            object-fit: contain;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-title {
            font-weight: 800;
            font-size: 15px;
            line-height: 1.2;
            color: #1b2559;
            letter-spacing: -0.2px;
        }

        .sidebar-subtitle {
            font-size: 13px;
            color: #707e94;
            font-weight: 600;
            margin-top: 8px;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: 10px;
        }

        .sidebar-category-header {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            padding: 14px 14px 4px 14px;
            display: flex;
            align-items: center;
            user-select: none;
        }

        .sidebar-category-header:first-child {
            padding-top: 4px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            border-radius: 12px;
            color: #6b7a99;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-menu li.active a {
            color: #2b43b9;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(43, 67, 185, 0.08);
            font-weight: 700;
        }

        .sidebar-menu li.active a::before {
            content: '';
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background-color: #2b43b9;
            border-radius: 4px 0 0 4px;
        }

        .sidebar-menu li a:hover:not(.active) {
            background-color: #eaeff8;
            color: #2b43b9;
        }

        .sidebar-menu li a i {
            font-size: 18px;
            width: 22px;
            text-align: center;
        }

        .sidebar-bottom {
            border-top: 1px solid #e3e8f0;
            padding-top: 20px;
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-bottom a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 16px;
            border-radius: 12px;
            color: #6b7a99;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .sidebar-bottom a.logout {
            color: #e63946;
        }

        .sidebar-bottom a:hover {
            background-color: #eaeff8;
        }

        .sidebar-bottom a.logout:hover {
            background-color: #ffeef0;
        }

        .sidebar-bottom a.help-link {
            font-size: 11.5px;
            font-weight: 600;
            color: #94a3b8;
            padding: 7px 16px;
            gap: 10px;
            line-height: 1.35;
        }

        .sidebar-bottom a.help-link i {
            font-size: 13px;
            color: #94a3b8;
        }

        .sidebar-bottom a.help-link:hover {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .sidebar-bottom a.help-link.active {
            color: #2b43b9;
            background-color: #f1f5f9;
            font-weight: 700;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            flex: 1;
            height: 100vh;
            padding: 24px 32px;
            overflow-y: auto;
            overflow-x: hidden;
            min-width: 0;
            -webkit-overflow-scrolling: touch;
        }

        .main-wrapper::-webkit-scrollbar {
            width: 6px;
        }
        .main-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }
        .main-wrapper::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .main-wrapper::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Header Card Banner */
        .top-header-banner {
            background: linear-gradient(135deg, #3d56b2 0%, #2b3a8c 100%);
            border-radius: 20px;
            padding: 24px 32px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 24px rgba(43, 67, 185, 0.2);
            margin-bottom: 24px;
        }

        .header-title-box h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .header-title-box p {
            font-size: 14px;
            opacity: 0.85;
            font-weight: 500;
        }

        .header-user-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Notification Bell */
        .notif-dropdown-wrap {
            position: relative;
        }

        .notif-bell {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1b2559;
            position: relative;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .notif-bell:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            background: #ffffff;
            color: #2b43b9;
        }

        .notif-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            min-width: 19px;
            height: 19px;
            padding: 0 5px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            border-radius: 10px;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
        }

        .notif-dropdown {
            position: absolute;
            top: 56px;
            right: 0;
            width: 390px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(226, 232, 240, 0.8);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 9999;
        }

        .notif-dropdown.show { display: flex; }

        .notif-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notif-header-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 800;
            color: #1b2559;
        }

        .notif-count-pill {
            background: #eef2ff;
            color: #2b43b9;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .notif-btn-read-all {
            background: none;
            border: none;
            color: #6b7a99;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .notif-body {
            max-height: 340px;
            overflow-y: auto;
            padding: 8px 0;
        }

        .notif-empty {
            padding: 40px 20px;
            text-align: center;
            color: #a3aed0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }

        /* User Profile Pill */
        .profile-dropdown-wrap {
            position: relative;
        }

        .user-profile-badge {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 30px;
            padding: 4px 16px 4px 4px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .user-profile-badge:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .avatar-img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            color: white;
        }

        .user-email {
            font-size: 11px;
            opacity: 0.8;
            line-height: 1.2;
            color: white;
        }

        .profile-dropdown {
            position: absolute;
            top: 56px;
            right: 0;
            width: 240px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #eef2f7;
            display: none;
            flex-direction: column;
            padding: 8px;
            z-index: 1000;
        }

        .profile-dropdown.show { display: flex; }

        .dropdown-header {
            padding: 12px 14px 8px;
            border-bottom: 1px solid #f4f7fe;
            margin-bottom: 6px;
        }

        .d-name { font-weight: 700; font-size: 14px; color: #1b2559; }
        .d-email { font-size: 12px; color: #a3aed0; }
        .d-role {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            background: #eef2ff;
            color: #2b43b9;
            padding: 2px 6px;
            border-radius: 4px;
            margin-top: 4px;
            display: inline-block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: #707e94;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f4f7fe;
            color: #2b43b9;
        }

        .dropdown-item.danger { color: #e63946; }
        .dropdown-item.danger:hover { background-color: #ffeef0; color: #e63946; }
        .dropdown-divider { height: 1px; background-color: #f4f7fe; margin: 4px 0; }

        /* Alerts */
        .alert-box {
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
            font-weight: 600;
        }

        .alert-box.success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert-box.danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* Logout Modal */
        .logout-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .logout-modal-backdrop.show { display: flex; }
        .logout-modal-card {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            padding: 26px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .logout-modal-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #fef2f2;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 16px auto;
        }
        .logout-modal-title { font-size: 18px; font-weight: 800; color: #1b2559; margin-bottom: 6px; }
        .logout-modal-desc { font-size: 13.5px; color: #6b7a99; margin-bottom: 22px; }
        .logout-modal-actions { display: flex; gap: 10px; }
        .btn-modal {
            flex: 1;
            padding: 10px 16px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-modal.cancel { background: #f1f5f9; color: #475569; }
        .btn-modal.cancel:hover { background: #e2e8f0; }
        .btn-modal.confirm { background: #ef4444; color: white; }
        .btn-modal.confirm:hover { background: #dc2626; }

        /* ── Mobile Topbar & Responsive Drawer ── */
        .mobile-topbar {
            display: none;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            height: 62px;
            padding: 0 16px;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 9980;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        .mobile-topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-sidebar-hamburger {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-sidebar-hamburger:hover {
            background: #eaeff8;
            color: #2b43b9;
        }

        .mobile-brand {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mobile-logo-img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }
        .mobile-brand-title {
            font-size: 15px;
            font-weight: 800;
            color: #1b2559;
            letter-spacing: -0.2px;
        }

        .mobile-topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header-mobile {
            display: none;
            justify-content: flex-end;
            margin-bottom: 8px;
        }
        .btn-close-sidebar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: none;
            background: #f1f5f9;
            color: #64748b;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .btn-close-sidebar:hover { background: #fee2e2; color: #ef4444; }

        /* Backdrop */
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9988;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .sidebar-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        body.no-scroll {
            overflow: hidden;
        }

        /* Responsive Media Queries (<= 991px) */
        @media (max-width: 991px) {
            body {
                flex-direction: column;
                min-height: 100vh;
                height: auto;
                overflow-x: hidden;
                overflow-y: auto;
            }

            .mobile-topbar {
                display: flex;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 290px;
                max-width: 86vw;
                height: 100vh;
                z-index: 9999;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                box-shadow: 0 20px 40px rgba(0,0,0,0.25);
                background: #ffffff;
                overflow-y: auto;
                padding: 20px 16px;
                display: flex;
                flex-direction: column;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .sidebar-header-mobile {
                display: flex;
            }

            .main-wrapper {
                height: auto;
                padding: 16px 14px;
                width: 100%;
                overflow-x: hidden;
                overflow-y: visible;
            }

            .top-header-banner {
                padding: 18px 20px;
                border-radius: 16px;
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
                margin-bottom: 18px;
            }
            .header-title-box h1 {
                font-size: 22px;
            }
            .header-title-box p {
                font-size: 13px;
            }
            .header-user-nav {
                width: 100%;
                justify-content: space-between;
                padding-top: 12px;
                border-top: 1px solid rgba(255, 255, 255, 0.18);
            }

            .notif-dropdown {
                width: calc(100vw - 32px);
                max-width: 360px;
                right: -60px;
            }

            .profile-dropdown {
                width: calc(100vw - 32px);
                max-width: 320px;
                right: 0;
            }
        }

        /* ── Extra Small Phones (< 480px) ── */
        @media (max-width: 480px) {
            .main-wrapper {
                padding: 12px 10px;
            }
            .top-header-banner {
                padding: 14px 16px;
                border-radius: 14px;
                gap: 12px;
                margin-bottom: 14px;
            }
            .header-title-box h1 {
                font-size: 18px;
            }
            .header-title-box p {
                font-size: 12px;
            }
            .notif-dropdown {
                width: calc(100vw - 16px);
                right: -50px;
                left: 8px;
                max-width: unset;
            }
            .profile-dropdown {
                width: calc(100vw - 20px);
                right: -10px;
                max-width: unset;
            }
            .logout-modal-card {
                margin: 0 12px;
                padding: 24px 20px 20px;
                max-width: calc(100vw - 24px);
            }
        }

        /* ── Global Responsive Utility Classes ── */
        .table-responsive-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0 0 12px 12px;
        }
        .table-responsive-wrap::-webkit-scrollbar { height: 4px; }
        .table-responsive-wrap::-webkit-scrollbar-track { background: #f1f5f9; }
        .table-responsive-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        @media (max-width: 768px) {
            .kpi-grid, .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }
            .charts-grid, .widgets-grid {
                grid-template-columns: 1fr !important;
                gap: 14px !important;
            }
            .page-action-card {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
                padding: 14px 16px !important;
            }
            .filter-group {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
            }
            .filter-input, .filter-select {
                min-width: unset !important;
                width: 100% !important;
            }
            .data-table th, .data-table td {
                padding: 10px 12px !important;
                font-size: 12.5px !important;
            }
            .modal-bx, .modal-form-card {
                width: 94% !important;
                padding: 20px !important;
                border-radius: 18px !important;
            }
            .form-grid-layout {
                grid-template-columns: 1fr !important;
            }
            .pagination-container {
                flex-direction: column !important;
                gap: 10px !important;
                align-items: flex-start !important;
            }
            .table-hdr {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px !important;
            }
            /* piket grid */
            .monitoring-grid, .dispensasi-grid, .rekap-grid {
                grid-template-columns: 1fr !important;
            }
            .user-profile-badge .user-info-text {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .kpi-grid, .stats-grid {
                grid-template-columns: 1fr !important;
            }
            .action-btns-group {
                flex-wrap: wrap !important;
                gap: 6px !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Mobile Topbar -->
    <div class="mobile-topbar">
        <div class="mobile-topbar-left">
            <button type="button" class="btn-sidebar-hamburger" onclick="toggleMobileSidebar()" aria-label="Buka Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="mobile-brand">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo" class="mobile-logo-img">
                <span class="mobile-brand-title">GURU PIKET</span>
            </div>
        </div>
        <div class="mobile-topbar-right">
            <div id="mobileNotifTrigger" class="notif-bell" onclick="toggleNotifDropdown()" role="button" aria-label="Notifikasi" style="width:38px;height:38px;">
                <i class="fa-solid fa-bell" style="font-size: 16px;"></i>
                <span class="notif-badge" id="mobileNotifBadge" style="display: none;">0</span>
            </div>
            <div id="mobileProfileTrigger" onclick="toggleProfileDropdown()" style="cursor:pointer;" role="button" aria-label="Profil">
                <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'Guru Piket').'&background=0284c7&color=ffffff&bold=true' }}" alt="Avatar" style="width:36px;height:36px;border-radius:50%;border:2px solid #0284c7;">
            </div>
        </div>
    </div>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-header-mobile">
                <button type="button" class="btn-close-sidebar" onclick="closeMobileSidebar()" aria-label="Tutup Menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="sidebar-brand">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo Jurnal Absensi" class="sidebar-logo-img">
                <div class="sidebar-brand-text">
                    <span class="sidebar-title">JURNAL<br>ABSENSI</span>
                    <span class="sidebar-subtitle">SMKN 1 BOYOLANGU</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-category-header">Menu Utama</li>
                <li class="{{ request()->routeIs('guru-piket.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('guru-piket.dashboard') }}">
                        <i class="fa-solid fa-border-all"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('guru-piket.monitoring') ? 'active' : '' }}">
                    <a href="{{ route('guru-piket.monitoring') }}">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        <span>Monitoring Kelas</span>
                    </a>
                </li>

                <li class="sidebar-category-header">Layanan Siswa & Guru</li>
                <li class="{{ request()->routeIs('guru-piket.izin-siswa*') ? 'active' : '' }}">
                    <a href="{{ route('guru-piket.izin-siswa') }}">
                        <i class="fa-solid fa-hospital-user"></i>
                        <span>Izin & Sakit Siswa</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('guru-piket.dispensasi*') ? 'active' : '' }}">
                    <a href="{{ route('guru-piket.dispensasi') }}">
                        <i class="fa-solid fa-ticket-simple"></i>
                        <span>Dispensasi Siswa</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('guru-piket.izin-guru') ? 'active' : '' }}">
                    <a href="{{ route('guru-piket.izin-guru') }}">
                        <i class="fa-solid fa-user-clock"></i>
                        <span>Monitoring Izin Guru</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('guru-piket.rekap') ? 'active' : '' }}">
                    <a href="{{ route('guru-piket.rekap') }}">
                        <i class="fa-solid fa-clipboard-user"></i>
                        <span>Rekap Presensi Siswa</span>
                    </a>
                </li>

                <li class="sidebar-category-header">Laporan Harian</li>
                <li class="{{ request()->routeIs('guru-piket.laporan*') ? 'active' : '' }}">
                    <a href="{{ route('guru-piket.laporan') }}">
                        <i class="fa-solid fa-file-signature"></i>
                        <span>Laporan & Catatan Piket</span>
                    </a>
                </li>

                @if(Auth::user()->role === 'admin')
                <li class="sidebar-category-header">Akses Administrator</li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" style="color: #6366f1;">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali ke Admin</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ route('guru-piket.help') }}" class="help-link {{ request()->routeIs('guru-piket.help') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-question"></i>
                <span>Panduan Piket</span>
            </a>
            <a href="#" class="logout" onclick="openLogoutModal(); return false;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Log out</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-wrapper">
        <!-- Top Header Card -->
        <header class="top-header-banner">
            <div class="header-title-box">
                <h1>@yield('header_title', 'Dashboard')</h1>
                <p>@yield('header_subtitle', 'Sistem Informasi Jurnal Mengajar & Absensi Siswa')</p>
            </div>
            <div class="header-user-nav">
                @yield('header_extra')

                {{-- Notification Bell --}}
                <div class="notif-dropdown-wrap" id="notifDropdownWrap">
                    <div class="notif-bell" id="notifBellBtn" onclick="toggleNotifDropdown()">
                        <i class="fa-solid fa-bell" style="font-size: 19px;"></i>
                        <span class="notif-badge" id="notifBadge" style="display: none;">0</span>
                    </div>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <div class="notif-header-title">
                                <i class="fa-solid fa-bell" style="color: #2b43b9;"></i>
                                <span>Notifikasi</span>
                                <span class="notif-count-pill" id="notifCountPill">0 Baru</span>
                            </div>
                            <button type="button" class="notif-btn-read-all" onclick="markAllNotificationsRead()" id="btnMarkAllRead">
                                <i class="fa-solid fa-check-double"></i> Tandai Dibaca
                            </button>
                        </div>
                        <div class="notif-body" id="notifListContainer">
                            <div class="notif-empty">
                                <i class="fa-regular fa-bell-slash" style="font-size: 32px; color: #cbd5e1;"></i>
                                <span>Belum ada notifikasi baru</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Profile Dropdown --}}
                <div class="profile-dropdown-wrap" id="profileDropdownWrap">
                    <div class="user-profile-badge" onclick="toggleProfileDropdown()">
                        <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'Guru Piket').'&background=e0e7ff&color=2b43b9&bold=true' }}" alt="Avatar" class="avatar-img">
                        <div class="user-info-text">
                            <span class="user-name">{{ Auth::user()->name ?? 'Guru Piket' }}</span>
                            <span class="user-email">{{ Auth::user()->email }}</span>
                        </div>
                    </div>

                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">
                            <div class="d-name">{{ Auth::user()->name ?? 'Guru Piket' }}</div>
                            <div class="d-email">{{ Auth::user()->email }}</div>
                            <span class="d-role">{{ strtoupper(str_replace('_', ' ', Auth::user()->role)) }}</span>
                        </div>

                        <a href="{{ route('admin.profil') }}" class="dropdown-item">
                            <i class="fa-solid fa-user-pen"></i> Profil Saya
                        </a>

                        <div class="dropdown-divider"></div>

                        <button type="button" onclick="closeProfileDropdown(); openLogoutModal();" class="dropdown-item danger" style="width:100%;background:none;border:none;font-family:inherit;text-align:left;cursor:pointer;">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert-box success">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color: inherit; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-box danger">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 18px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color: inherit; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Logout Modal -->
    <div class="logout-modal-backdrop" id="logoutModal">
        <div class="logout-modal-card">
            <div class="logout-modal-icon">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <h3 class="logout-modal-title">Konfirmasi Keluar</h3>
            <p class="logout-modal-desc">Apakah Anda yakin ingin keluar dari sesi Guru Piket SMKN 1 Boyolangu?</p>
            <div class="logout-modal-actions">
                <button type="button" class="btn-modal cancel" onclick="closeLogoutModal()">Batal</button>
                <form action="{{ route('logout') }}" method="POST" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-modal confirm" style="width: 100%;">Ya, Keluar</button>
                </form>
            </div>
        </div>
    </div>

    @yield('scripts')
    <script src="{{ asset('js/mobile-layout.js') }}"></script>

    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';

        function toggleProfileDropdown() {
            closeNotifDropdown();
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show') && typeof positionMobileDropdowns === 'function') {
                positionMobileDropdowns();
            }
        }

        function closeProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) dropdown.classList.remove('show');
        }

        function toggleNotifDropdown() {
            closeProfileDropdown();
            const dropdown = document.getElementById('notifDropdown');
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show') && typeof positionMobileDropdowns === 'function') {
                positionMobileDropdowns();
            }
        }

        function closeNotifDropdown() {
            const dropdown = document.getElementById('notifDropdown');
            if (dropdown) dropdown.classList.remove('show');
        }

        function openLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) modal.classList.add('show');
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) modal.classList.remove('show');
        }

        document.addEventListener('click', function(e) {
            if (typeof isProfileAreaClick === 'function' && !isProfileAreaClick(e.target)) {
                closeProfileDropdown();
            }
            if (typeof isNotifAreaClick === 'function' && !isNotifAreaClick(e.target)) {
                closeNotifDropdown();
            }
        });

        // Mobile Sidebar Drawer Toggle
        function toggleMobileSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.toggle('mobile-open');
                backdrop.classList.toggle('active');
                document.body.classList.toggle('no-scroll');
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.remove('mobile-open');
                backdrop.classList.remove('active');
                document.body.classList.remove('no-scroll');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
                closeProfileDropdown();
                closeNotifDropdown();
                closeMobileSidebar();
            }
        });
    </script>
</body>
</html>
