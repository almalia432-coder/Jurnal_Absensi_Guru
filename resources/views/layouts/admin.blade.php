<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Jurnal Absensi SMKN 1 BOYOLANGU')</title>
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
            background-color: #f4f6fa;
            color: #1e293b;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Sidebar Styling (Fixed, Image 2 style) ── */
        .sidebar {
            width: 255px;
            height: 100vh;
            background-color: #f8fafc;
            border-right: 1px solid #e5e9f2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 18px;
            flex-shrink: 0;
            overflow-y: auto;
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
            padding: 4px 6px 20px 6px;
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
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: 6px;
        }

        .sidebar-category-header {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            padding: 14px 12px 6px 12px;
            display: flex;
            align-items: center;
            user-select: none;
        }

        .sidebar-category-header:first-child {
            padding-top: 0;
        }

        .sidebar-menu li a, .sidebar-accordion-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            font-size: 13.5px;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-menu li a i, .sidebar-accordion-toggle i.menu-icon {
            font-size: 17px;
            width: 20px;
            text-align: center;
            color: #64748b;
            transition: color 0.2s;
        }

        .sidebar-menu li.active > a {
            color: #1d4ed8;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08);
            font-weight: 700;
        }

        .sidebar-menu li.active > a i {
            color: #1d4ed8;
        }

        .sidebar-menu li a:hover:not(.active), .sidebar-accordion-toggle:hover:not(.open) {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .sidebar-menu li a:hover:not(.active) i, .sidebar-accordion-toggle:hover:not(.open) i {
            color: #1e293b;
        }

        /* Accordion Sub-menu */
        .sidebar-accordion-toggle {
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
        }

        .sidebar-accordion-toggle.open {
            background-color: #f1f5f9;
            color: #1d4ed8;
            font-weight: 700;
        }

        .sidebar-accordion-toggle.open i.menu-icon {
            color: #1d4ed8;
        }

        .sidebar-accordion-toggle .chevron {
            margin-left: auto;
            font-size: 11px;
            transition: transform 0.25s ease;
            color: #94a3b8;
        }

        .sidebar-accordion-toggle.open .chevron {
            transform: rotate(180deg);
            color: #1d4ed8;
        }

        .sidebar-submenu {
            list-style: none;
            display: none;
            flex-direction: column;
            gap: 2px;
            margin-top: 4px;
            padding-left: 10px;
        }

        .sidebar-submenu.open {
            display: flex;
        }

        .sidebar-submenu li a {
            padding: 8px 14px;
            font-size: 12.5px;
            border-radius: 10px;
            color: #64748b;
        }

        .sidebar-submenu li.active a {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            color: #1d4ed8;
            font-weight: 700;
        }

        .sidebar-submenu li a:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .sidebar-bottom {
            border-top: 1px solid #e5e9f2;
            padding-top: 16px;
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-bottom a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            font-size: 13.5px;
            transition: all 0.15s ease;
        }

        .sidebar-bottom a i {
            font-size: 17px;
            width: 20px;
            text-align: center;
        }

        .sidebar-bottom a:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .sidebar-bottom a.active {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            color: #1d4ed8;
            font-weight: 700;
        }

        .sidebar-bottom a.logout {
            color: #ef4444;
        }

        .sidebar-bottom a.logout i {
            color: #ef4444;
        }

        .sidebar-bottom a.logout:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }

        /* ── Main Content Wrapper (ONLY THIS SCROLLS!) ── */
        .main-wrapper {
            flex: 1;
            height: 100vh;
            padding: 24px 28px;
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

        /* Top Header Card Banner (Image 2 Syle) */
        .top-header-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 55%, #2563eb 100%);
            border-radius: 18px;
            padding: 22px 28px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.18);
            margin-bottom: 24px;
            position: relative;
        }

        .header-title-box h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .header-title-box p {
            font-size: 13.5px;
            opacity: 0.9;
            color: #e0e7ff;
            font-weight: 500;
        }

        .header-user-nav {
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 2;
        }

        /* School Mini Card */
        .header-badge-school {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 7px 16px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: white;
            text-align: left;
        }
        .header-badge-school .badge-tag {
            font-weight: 800;
            font-size: 9.5px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #bfdbfe;
        }
        .header-badge-school .badge-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
        }
        .header-badge-school .badge-role {
            font-size: 10px;
            color: #dbeafe;
            font-weight: 500;
        }

        /* Notification Dropdown & Bell (Translucent circle button like Image 2) */
        .notif-dropdown-wrap {
            position: relative;
        }

        .header-bell-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            position: relative;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .header-bell-btn:hover {
            background: rgba(255, 255, 255, 0.24);
            transform: scale(1.04);
        }

        .header-bell-btn .bell-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #1e3a8a;
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
            animation: fadeInSlideDown 0.25s cubic-bezier(0.16, 1, 0.3, 1);
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
            scrollbar-width: thin;
        }

        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 18px;
            border-bottom: 1px solid #f8fafc;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s ease;
            position: relative;
            cursor: pointer;
        }

        .notif-item:hover {
            background: #f8fafc;
        }

        .notif-item.unread {
            background: #f0f4ff;
        }

        .notif-item.unread:hover {
            background: #e6edff;
        }

        .notif-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 15px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        }

        .notif-content {
            flex: 1;
            min-width: 0;
        }

        .notif-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }

        .notif-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
            margin-top: 3px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .notif-meta-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 6px;
        }

        .notif-badge-tag {
            font-size: 10px;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .notif-time-txt {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        .notif-unread-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2b43b9;
            flex-shrink: 0;
            align-self: center;
            box-shadow: 0 0 0 3px rgba(43, 67, 185, 0.2);
        }

        .notif-empty {
            padding: 36px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .notif-empty i {
            font-size: 36px;
            color: #cbd5e1;
        }

        .notif-empty span {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }

        .notif-footer {
            padding: 10px 16px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            text-align: center;
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
            user-select: none;
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
            flex-shrink: 0;
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            color: white;
            white-space: nowrap;
        }

        .user-email {
            font-size: 11px;
            opacity: 0.8;
            line-height: 1.2;
            color: white;
            white-space: nowrap;
        }

        /* Micro Animations */
        @keyframes fadeInSlideDown {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pulseGlow {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 59, 48, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(255, 59, 48, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 59, 48, 0); }
        }

        /* Profile Dropdown */
        .profile-dropdown-wrap {
            position: relative;
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.14);
            border: 1px solid #e2e8f0;
            min-width: 220px;
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
            animation: logoutPulse 2s infinite;
        }

        @keyframes logoutPulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
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

        /* Responsive Media Queries (<= 991px) */
        @media (max-width: 991px) {
            body {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
                overflow: visible;
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
                overflow: visible;
                padding: 16px 14px;
                width: 100%;
                overflow-x: hidden;
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
            .notif-dropdown .notif-body {
                max-height: 300px;
            }
        }

        /* ── Global Responsive Utility Classes ── */
        .table-responsive-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0 0 12px 12px;
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

        @media (max-width: 768px) {
            /* Grid layouts */
            .kpi-grid, .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }
            .charts-grid, .widgets-grid {
                grid-template-columns: 1fr !important;
                gap: 14px !important;
            }
            /* Action bars */
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
            /* Tables */
            .data-table th, .data-table td {
                padding: 10px 12px !important;
                font-size: 12.5px !important;
            }
            /* Modals */
            .modal-bx, .modal-form-card {
                width: 94% !important;
                padding: 20px !important;
                border-radius: 18px !important;
            }
            .form-grid-layout {
                grid-template-columns: 1fr !important;
            }
            /* Pagination */
            .pagination-container {
                flex-direction: column !important;
                gap: 10px !important;
                align-items: flex-start !important;
            }
            /* Table header/action bars */
            .table-hdr {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px !important;
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
            .btn-add-siswa, .btn-add-guru, .btn-add-user, .btn-add-mapel, .btn-add-kelas, .btn-add-jurusan {
                width: 100% !important;
                justify-content: center !important;
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
                <span class="mobile-brand-title">E-JURNAL</span>
            </div>
        </div>
        <div class="mobile-topbar-right">
            <div id="mobileNotifTrigger" class="notif-bell" onclick="toggleNotifDropdown()" role="button" aria-label="Notifikasi">
                <i class="fa-solid fa-bell" style="font-size: 16px;"></i>
                <span class="notif-badge" id="mobileNotifBadge" style="display: none;">0</span>
            </div>
            <div id="mobileProfileTrigger" onclick="toggleProfileDropdown()" style="cursor:pointer;" role="button" aria-label="Profil">
                <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'Admin').'&background=2b43b9&color=ffffff&bold=true' }}" alt="Avatar" style="width:36px;height:36px;border-radius:50%;border:2px solid #2b43b9;">
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
            <p class="logout-modal-desc">Apakah Anda yakin ingin keluar dari aplikasi Jurnal Absensi Guru SMKN 1 Boyolangu?</p>
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
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-table-cells-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- Kategori: Aktivitas Presensi --}}
                <li class="sidebar-category-header">
                    <span>Presensi &amp; Akademik</span>
                </li>
                <li class="{{ request()->is('admin/absensi*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/absensi') }}">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span>Absensi &amp; Jurnal Mengajar</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/jadwal') || request()->is('admin/jadwal/*')) && !request()->is('admin/jadwal-piket*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/jadwal') }}">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Jadwal Pelajaran</span>
                    </a>
                </li>
                <li class="{{ request()->is('admin/jadwal-piket*') ? 'active' : '' }}">
                    <a href="{{ route('admin.jadwal-piket') }}">
                        <i class="fa-solid fa-clipboard-user"></i>
                        <span>Jadwal Guru Piket</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('guru-piket.terlambat*') ? 'active' : '' }}">
                    <a href="{{ route('guru-piket.terlambat.index') }}">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Izin Siswa Terlambat</span>
                    </a>
                </li>

                {{-- Kategori: Master Data --}}
                <li class="sidebar-category-header">
                    <span>Data Master</span>
                </li>
                <li>
                    <button
                        class="sidebar-accordion-toggle {{ request()->is('admin/master*') ? 'open' : '' }}"
                        id="masterAccordionBtn"
                        type="button"
                        onclick="toggleMasterAccordion()"
                    >
                        <i class="fa-solid fa-database menu-icon"></i>
                        <span>Master Data</span>
                        <i class="fa-solid fa-chevron-down chevron"></i>
                    </button>
                    <ul class="sidebar-submenu {{ request()->is('admin/master*') ? 'open' : '' }}" id="masterSubmenu">
                        <li class="{{ request()->is('admin/master/user*') ? 'active' : '' }}">
                            <a href="{{ url('/admin/master/user') }}">
                                <i class="fa-solid fa-user-gear"></i>
                                <span>Manajemen User</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('admin/master/siswa*') ? 'active' : '' }}">
                            <a href="{{ url('/admin/master/siswa') }}">
                                <i class="fa-solid fa-users"></i>
                                <span>Manajemen Siswa</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('admin/master/guru*') ? 'active' : '' }}">
                            <a href="{{ url('/admin/master/guru') }}">
                                <i class="fa-solid fa-chalkboard-user"></i>
                                <span>Manajemen Guru</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('admin/master/jurusan*') ? 'active' : '' }}">
                            <a href="{{ url('/admin/master/jurusan') }}">
                                <i class="fa-solid fa-layer-group"></i>
                                <span>Manajemen Jurusan</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('admin/master/kelas*') ? 'active' : '' }}">
                            <a href="{{ url('/admin/master/kelas') }}">
                                <i class="fa-solid fa-door-open"></i>
                                <span>Manajemen Kelas</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('admin/master/mapel*') ? 'active' : '' }}">
                            <a href="{{ url('/admin/master/mapel') }}">
                                <i class="fa-solid fa-book-open"></i>
                                <span>Manajemen Mapel</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Kategori: Laporan --}}
                <li class="sidebar-category-header">
                    <span>Pusat Laporan</span>
                </li>
                <li class="{{ request()->is('admin/laporan*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/laporan') }}">
                        <i class="fa-solid fa-book"></i>
                        <span>Laporan Rekap</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ route('admin.help') }}" class="{{ request()->routeIs('admin.help') ? 'active' : '' }}">
                <i class="fa-regular fa-circle-question"></i>
                <span>Help Centre</span>
            </a>
            <a href="#" class="logout" onclick="openLogoutModal(); return false;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
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
                <p>@yield('header_subtitle', 'Jurnal Absensi Guru & Siswa SMKN 1 BOYOLANGU')</p>
            </div>
            <div class="header-user-nav">
                {{-- School / Role Badge Card (Image 2 style) --}}
                <div class="header-badge-school">
                    <span class="badge-tag">ADMIN AKTIF HARI INI</span>
                    <span class="badge-name">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    <span class="badge-role">Administrator Utama</span>
                </div>

                {{-- Interactive Notification Dropdown --}}
                <div class="notif-dropdown-wrap" id="notifDropdownWrap">
                    <div class="header-bell-btn" id="notifBellBtn" onclick="toggleNotifDropdown()" title="Notifikasi">
                        <i class="fa-solid fa-bell"></i>
                        <span class="bell-badge" id="notifBadge" style="display: none;">0</span>
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
                                <i class="fa-solid fa-shield-halved" style="color: #10b981; margin-right: 4px;"></i> Notifikasi Terpadu SMKN 1 Boyolangu
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Portal Switcher --}}
                <x-portal-switcher />

                {{-- User Profile Dropdown --}}
                <div class="profile-dropdown-wrap" id="profileDropdownWrap">
                    <div class="user-profile-badge" onclick="toggleProfileDropdown()">
                        <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'Administrator').'&background=e0e7ff&color=2b43b9&bold=true' }}" 
                             alt="Avatar" 
                             class="avatar-img"
                             onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Administrator') }}&background=e0e7ff&color=2b43b9&bold=true';">
                        <div class="user-info-text">
                            <span class="user-name">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            <span class="user-email">{{ Auth::user()->email ?? 'admin@smkn1boyolangu.sch.id' }}</span>
                        </div>
                    </div>

                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">
                            <div class="d-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
                            <div class="d-email">{{ Auth::user()->email ?? 'admin@smkn1boyolangu.sch.id' }}</div>
                            <span class="d-role">{{ strtoupper(str_replace('_', ' ', Auth::user()->role ?? 'ADMIN')) }}</span>
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

        @yield('content')
    </main>

    @yield('scripts')
    <script src="{{ asset('js/mobile-layout.js') }}"></script>
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';

    function toggleMasterAccordion() {
        const btn = document.getElementById('masterAccordionBtn');
        const menu = document.getElementById('masterSubmenu');
        btn.classList.toggle('open');
        menu.classList.toggle('open');
    }

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

    /* Notification System Logic */
    function toggleNotifDropdown() {
        closeProfileDropdown();
        const dropdown = document.getElementById('notifDropdown');
        const isShown = dropdown.classList.contains('show');
        if (!isShown) {
            dropdown.classList.add('show');
            loadNotifications();
            if (typeof positionMobileDropdowns === 'function') positionMobileDropdowns();
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

        // Update Badge
        const mobileBadge = document.getElementById('mobileNotifBadge');
        if (unreadCount > 0) {
            badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
            badge.style.display = 'flex';
            if (mobileBadge) {
                mobileBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                mobileBadge.style.display = 'flex';
            }
            pill.textContent = unreadCount + ' Baru';
            pill.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
            if (mobileBadge) mobileBadge.style.display = 'none';
            pill.textContent = 'Semua Dibaca';
            pill.style.background = '#e2e8f0';
            pill.style.color = '#64748b';
        }

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

    // Initialize notification badge on page load
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

    // ESC key closes modals & dropdowns & mobile sidebar
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
