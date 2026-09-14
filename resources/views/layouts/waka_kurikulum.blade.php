<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Waka Kurikulum - Jurnal Absensi SMKN 1 BOYOLANGU')</title>
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

        body {
            background-color: #f2f4f8;
            color: #2b3674;
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar Styling (Admin Identical) ── */
        .sidebar {
            width: 260px;
            background-color: #f6f7fb;
            border-right: 1px solid #e3e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 28px 20px;
            flex-shrink: 0;
            min-height: 100vh;
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
            transition: all 0.15s ease;
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

        /* ── Main Content Wrapper ── */
        .main-wrapper {
            flex: 1;
            padding: 24px 32px;
            overflow-y: auto;
            min-width: 0;
        }

        /* ── Header Card Banner (Admin Identical) ── */
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
            animation: fadeInSlideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .header-title-box h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
            animation: fadeIn 0.5s ease-out;
        }

        .header-title-box p {
            font-size: 14px;
            opacity: 0.85;
            font-weight: 500;
        }

        .header-user-nav {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Notification Bell */
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

        .notif-dropdown-wrap {
            position: relative;
        }

        .notif-dropdown {
            position: absolute;
            top: 56px;
            right: 0;
            width: 380px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(226, 232, 240, 0.8);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 9999;
            animation: dropIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .notif-dropdown.show {
            display: flex;
        }

        .notif-header {
            padding: 16px 20px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notif-header-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .notif-count-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 20px;
        }

        .notif-btn-read-all {
            background: none;
            border: none;
            color: #2b43b9;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .notif-btn-read-all:hover {
            background: #eaeff8;
        }

        .notif-body {
            max-height: 380px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .notif-empty {
            padding: 36px 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 13.5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid #f8fafc;
            transition: background 0.15s ease;
            cursor: pointer;
            position: relative;
        }

        .notif-item:hover {
            background: #f8fafc;
        }

        .notif-item.unread {
            background: #f0f4ff;
        }

        .notif-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: #ffffff;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .notif-content {
            display: flex;
            flex-direction: column;
            gap: 3px;
            flex: 1;
        }

        .notif-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .notif-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }

        .notif-meta-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
        }

        .notif-badge-tag {
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .notif-time-txt {
            font-size: 11px;
            color: #94a3b8;
        }

        .notif-unread-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2b43b9;
            flex-shrink: 0;
            margin-top: 6px;
        }

        .notif-footer {
            padding: 10px 20px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }

        /* User Profile Badge */
        .profile-dropdown-wrap {
            position: relative;
        }

        .user-profile-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 16px;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: opacity 0.2s ease;
            user-select: none;
        }

        .avatar-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.8);
            transition: transform 0.2s ease;
        }

        .user-profile-badge:hover .avatar-img {
            transform: scale(1.06);
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 700;
            font-size: 15px;
            color: white;
        }

        .user-email {
            font-size: 12px;
            opacity: 0.75;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.14);
            border: 1px solid #e2e8f0;
            min-width: 230px;
            padding: 8px;
            display: none;
            z-index: 9999;
            animation: dropIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .profile-dropdown.show { display: block; }

        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInSlideDown {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .dropdown-header {
            padding: 12px 14px 10px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 6px;
        }
        .dropdown-header .d-name { font-size: 14px; font-weight: 800; color: #0f172a; }
        .dropdown-header .d-email { font-size: 12px; color: #64748b; font-weight: 600; margin-top: 2px; }
        .dropdown-header .d-role {
            display: inline-block;
            background: #eef2ff;
            color: #3730a3;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 10px;
            border-radius: 20px;
            margin-top: 6px;
            text-transform: uppercase;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 700;
            color: #334155;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .dropdown-item:hover { background: #f8fafc; color: #2b43b9; }
        .dropdown-item i { width: 18px; text-align: center; font-size: 14px; }
        .dropdown-item.danger { color: #dc2626; }
        .dropdown-item.danger:hover { background: #fff5f5; color: #b91c1c; }
        .dropdown-divider { height: 1px; background: #f1f5f9; margin: 6px 0; }

        /* ── Animated Logout Confirmation Modal ── */
        .logout-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .logout-modal-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        .logout-modal-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 32px 28px 28px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            border: 1px solid #e2e8f0;
            transform: scale(0.85) translateY(10px);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .logout-modal-backdrop.show .logout-modal-card {
            transform: scale(1) translateY(0);
        }

        .logout-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fee2e2 0%, #fecdd3 100%);
            color: #ef4444;
            font-size: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.2);
        }

        .logout-modal-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .logout-modal-desc {
            font-size: 13.5px;
            color: #64748b;
            font-weight: 500;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .logout-modal-actions {
            display: flex;
            gap: 12px;
        }

        .btn-cancel-logout {
            flex: 1;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-cancel-logout:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .btn-confirm-logout {
            flex: 1;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
        }
        .btn-confirm-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

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

        @media (max-width: 991px) {
            body {
                flex-direction: column;
                min-height: 100vh;
                overflow-x: hidden;
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
                z-index: 9990;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
                background: #ffffff;
                overflow-y: auto;
                padding: 24px 18px;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar-header-mobile {
                display: flex;
            }

            .main-wrapper {
                padding: 16px 14px;
                width: 100%;
                overflow-x: hidden;
            }

            .top-header-banner {
                padding: 18px 20px;
                border-radius: 16px;
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                margin-bottom: 20px;
            }

            .header-title-box h1 {
                font-size: 22px;
            }

            .header-user-nav {
                width: 100%;
                justify-content: space-between;
                padding-top: 14px;
                border-top: 1px solid rgba(255, 255, 255, 0.18);
            }

            .notif-dropdown {
                width: calc(100vw - 32px);
                max-width: 360px;
                right: -50px;
            }

            .profile-dropdown {
                width: calc(100vw - 32px);
                max-width: 300px;
                right: 0;
            }
        }

        /* ── Alerts ── */
        .alert-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 600;
        }
        .alert-box.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-box.danger  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        .table-responsive-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-responsive-wrap::-webkit-scrollbar {
            height: 4px;
        }
        .table-responsive-wrap::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .table-responsive-wrap::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* ── Pagination Styles & SVG Fix ── */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
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

        /* Prevent unstyled Laravel Tailwind pagination SVGs from expanding to 100% */
        nav[role="navigation"] svg,
        .pagination svg,
        svg.w-5,
        svg.h-5 {
            width: 18px !important;
            height: 18px !important;
            max-width: 18px !important;
            max-height: 18px !important;
            min-width: 18px !important;
            display: inline-block !important;
            vertical-align: middle;
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
                <span class="mobile-brand-title">WAKA KURIKULUM</span>
            </div>
        </div>
        <div class="mobile-topbar-right">
            <div id="mobileNotifTrigger" class="notif-bell" onclick="toggleNotifDropdown()" role="button" aria-label="Notifikasi">
                <i class="fa-solid fa-bell" style="font-size: 16px;"></i>
                <span class="notif-badge" id="mobileNotifBadge" style="display: none;">0</span>
            </div>
            <div id="mobileProfileTrigger" onclick="toggleProfileDropdown()" style="cursor:pointer;" role="button" aria-label="Profil">
                <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'Waka K').'&background=2b43b9&color=ffffff&bold=true' }}" alt="Avatar" style="width:36px;height:36px;border-radius:50%;border:2px solid #2b43b9;">
            </div>
        </div>
    </div>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

    <!-- Logout Form -->
    <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none;">
        @csrf
    </form>

    <!-- Animated Logout Modal -->
    <div class="logout-modal-backdrop" id="logoutModal" onclick="if(event.target === this) closeLogoutModal()">
        <div class="logout-modal-card">
            <div class="logout-icon-wrap">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <h3 class="logout-modal-title">Konfirmasi Keluar</h3>
            <p class="logout-modal-desc">Apakah Anda yakin ingin keluar dari portal Waka Kurikulum SMKN 1 Boyolangu?</p>
            <div class="logout-modal-actions">
                <button type="button" class="btn-cancel-logout" onclick="closeLogoutModal()">Batal</button>
                <button type="button" class="btn-confirm-logout" onclick="document.getElementById('logout-form').submit()">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Ya, Keluar
                </button>
            </div>
        </div>
    </div>

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
                {{-- Kategori: Menu Utama --}}
                <li class="sidebar-category-header">
                    <span>Menu Utama</span>
                </li>
                <li class="{{ request()->routeIs('waka-kurikulum.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('waka-kurikulum.dashboard') }}">
                        <i class="fa-solid fa-border-all"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- Kategori: Manajemen Kurikulum --}}
                <li class="sidebar-category-header">
                    <span>Manajemen Kurikulum</span>
                </li>
                <li class="{{ request()->routeIs('waka-kurikulum.mapel.*') ? 'active' : '' }}">
                    <a href="{{ route('waka-kurikulum.mapel.index') }}">
                        <i class="fa-solid fa-book-bookmark"></i>
                        <span>Mata Pelajaran</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('waka-kurikulum.guru-mengajar.*') ? 'active' : '' }}">
                    <a href="{{ route('waka-kurikulum.guru-mengajar.index') }}">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        <span>Guru &amp; Beban Mengajar</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('waka-kurikulum.jadwal*') ? 'active' : '' }}">
                    <a href="{{ route('waka-kurikulum.jadwal') }}">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Jadwal Pelajaran</span>
                    </a>
                </li>

                {{-- Kategori: Monitoring Pembelajaran --}}
                <li class="sidebar-category-header">
                    <span>Monitoring Pembelajaran</span>
                </li>
                <li class="{{ request()->routeIs('waka-kurikulum.jurnal*') ? 'active' : '' }}">
                    <a href="{{ route('waka-kurikulum.jurnal') }}">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Jurnal Mengajar</span>
                    </a>
                </li>

                @if(Auth::user()->role === 'admin')
                <li class="sidebar-category-header">
                    <span>Akses Administrator</span>
                </li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" style="color: #2b43b9;">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali ke Admin</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ route('admin.help') }}" style="font-size: 12px; color: #94a3b8;">
                <i class="fa-solid fa-circle-question"></i>
                <span>Pusat Bantuan</span>
            </a>
            <a href="#" class="logout" onclick="openLogoutModal(); return false;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Log out</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="main-wrapper">
        <!-- Top Header Card -->
        <header class="top-header-banner">
            <div class="header-title-box">
                <h1>@yield('header_title', 'Portal Waka Kurikulum')</h1>
                <p>@yield('header_subtitle', 'Monitoring Pembelajaran & Kurikulum SMKN 1 BOYOLANGU')</p>
            </div>
            <div class="header-user-nav">
                @yield('header_extra')

                {{-- Interactive Notification Dropdown --}}
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
                                <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 24px; color: #2b43b9;"></i>
                                <span>Memuat notifikasi...</span>
                            </div>
                        </div>
                        <div class="notif-footer">
                            <span style="color: #94a3b8; font-size: 11px; font-weight: 600;">
                                <i class="fa-solid fa-shield-halved" style="color: #10b981; margin-right: 4px;"></i> Notifikasi Terpadu Kurikulum SMKN 1 Boyolangu
                            </span>
                        </div>
                    </div>
                </div>

                {{-- User Profile Dropdown --}}
                <div class="profile-dropdown-wrap" id="profileDropdownWrap">
                    <div class="user-profile-badge" onclick="toggleProfileDropdown()">
                        <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'Waka Kurikulum').'&background=ffffff&color=2b43b9&bold=true' }}" alt="Avatar" class="avatar-img">
                        <div class="user-info-text">
                            <span class="user-name">{{ Auth::user()->name ?? 'Waka Kurikulum' }}</span>
                            <span class="user-email">{{ Auth::user()->email ?? 'kurikulum@smkn1boyolangu.sch.id' }}</span>
                        </div>
                    </div>

                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">
                            <div class="d-name">{{ Auth::user()->name ?? 'Waka Kurikulum' }}</div>
                            <div class="d-email">{{ Auth::user()->email ?? 'kurikulum@smkn1boyolangu.sch.id' }}</div>
                            <span class="d-role">WAKA KURIKULUM</span>
                        </div>

                        <a href="{{ route('admin.profil') }}" class="dropdown-item">
                            <i class="fa-solid fa-user-pen"></i> Profil Saya
                        </a>

                        <div class="dropdown-divider"></div>

                        <button type="button" onclick="closeProfileDropdown(); openLogoutModal();" class="dropdown-item danger" style="width:100%;background:none;border:none;font-family:inherit;text-align:left;">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="alert-box success">
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-circle-check" style="font-size:18px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert-box danger">
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size:18px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';

        function toggleProfileDropdown() {
            closeNotifDropdown();
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) dropdown.classList.toggle('show');
        }

        function closeProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) dropdown.classList.remove('show');
        }

        function toggleNotifDropdown() {
            closeProfileDropdown();
            const dropdown = document.getElementById('notifDropdown');
            if (!dropdown) return;
            const isShown = dropdown.classList.contains('show');
            if (!isShown) {
                dropdown.classList.add('show');
                loadNotifications();
            } else {
                dropdown.classList.remove('show');
            }
        }

        function closeNotifDropdown() {
            const dropdown = document.getElementById('notifDropdown');
            if (dropdown) dropdown.classList.remove('show');
        }

        function loadNotifications() {
            fetch('{{ route("admin.notifikasi") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderNotifications(data.items, data.unread_count);
                }
            })
            .catch(err => {
                console.error('Error loading notifications:', err);
            });
        }

        function renderNotifications(items, unreadCount) {
            const badge = document.getElementById('notifBadge');
            const pill = document.getElementById('notifCountPill');
            const container = document.getElementById('notifListContainer');

            const mobileBadge = document.getElementById('mobileNotifBadge');
            if (unreadCount > 0) {
                if (badge) {
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    badge.style.display = 'flex';
                }
                if (mobileBadge) {
                    mobileBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    mobileBadge.style.display = 'flex';
                }
                if (pill) {
                    pill.textContent = unreadCount + ' Baru';
                    pill.style.display = 'inline-block';
                }
            } else {
                if (badge) badge.style.display = 'none';
                if (mobileBadge) mobileBadge.style.display = 'none';
                if (pill) {
                    pill.textContent = 'Semua Dibaca';
                    pill.style.background = '#e2e8f0';
                    pill.style.color = '#64748b';
                }
            }

            if (!container) return;

            if (!items || items.length === 0) {
                container.innerHTML = `
                    <div class="notif-empty">
                        <i class="fa-regular fa-bell-slash"></i>
                        <span>Belum ada notifikasi baru</span>
                    </div>
                `;
                return;
            }

            let html = '';
            items.forEach(n => {
                const meta = n.meta || {};
                const isUnreadClass = !n.is_read ? 'unread' : '';
                const unreadDot = !n.is_read ? '<span class="notif-unread-dot" title="Belum dibaca"></span>' : '';
                
                html += `
                    <div class="notif-item ${isUnreadClass}" onclick="handleNotifClick(${n.id}, '${meta.action_url || '#'}')">
                        <div class="notif-icon-box" style="background: ${meta.gradient || '#2b43b9'};">
                            <i class="${meta.icon || 'fa-solid fa-bell'}"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">${escapeHtml(n.judul)}</div>
                            <div class="notif-desc">${escapeHtml(n.pesan)}</div>
                            <div class="notif-meta-bar">
                                <span class="notif-badge-tag" style="background: ${meta.bg_light || '#f1f5f9'}; color: ${meta.text_color || '#334155'};">
                                    ${meta.badge || 'Info'}
                                </span>
                                <span class="notif-time-txt">
                                    <i class="fa-regular fa-clock" style="font-size: 10px; margin-right: 2px;"></i>${n.time_ago}
                                </span>
                            </div>
                        </div>
                        ${unreadDot}
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function handleNotifClick(notifId, actionUrl) {
            fetch(`/admin/notifikasi/${notifId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => {
                if (actionUrl && actionUrl !== '#' && actionUrl !== '') {
                    window.location.href = actionUrl;
                } else {
                    loadNotifications();
                }
            })
            .catch(() => {
                if (actionUrl && actionUrl !== '#') window.location.href = actionUrl;
            });
        }

        function markAllNotificationsRead() {
            const btn = document.getElementById('btnMarkAllRead');
            if (btn) btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';

            fetch('{{ route("admin.notifikasi.read-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => {
                if (btn) btn.innerHTML = '<i class="fa-solid fa-check-double"></i> Tandai Dibaca';
                loadNotifications();
            })
            .catch(err => {
                console.error('Error marking all as read:', err);
                if (btn) btn.innerHTML = '<i class="fa-solid fa-check-double"></i> Tandai Dibaca';
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
        });

        function openLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) modal.classList.add('show');
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) modal.classList.remove('show');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const profileWrap = document.getElementById('profileDropdownWrap');
            const mobileProfileTrigger = document.getElementById('mobileProfileTrigger');
            if (profileWrap && !profileWrap.contains(e.target) && (!mobileProfileTrigger || !mobileProfileTrigger.contains(e.target))) {
                closeProfileDropdown();
            }

            const notifWrap = document.getElementById('notifDropdownWrap');
            const mobileNotifTrigger = document.getElementById('mobileNotifTrigger');
            if (notifWrap && !notifWrap.contains(e.target) && (!mobileNotifTrigger || !mobileNotifTrigger.contains(e.target))) {
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
    @yield('scripts')
</body>
</html>
