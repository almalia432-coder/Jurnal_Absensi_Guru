<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Wali Murid - Jurnal Absensi SMKN 1 BOYOLANGU')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background-color: #f2f4f8; color: #2b3674; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px; background-color: #f6f7fb; border-right: 1px solid #e3e8f0;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 28px 20px; flex-shrink: 0;
        }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 30px; }
        .sidebar-logo-img { width: 46px; height: 46px; object-fit: contain; }
        .sidebar-brand-text { display: flex; flex-direction: column; }
        .sidebar-title { font-weight: 800; font-size: 15px; line-height: 1.2; color: #1b2559; letter-spacing: -0.2px; }
        .sidebar-subtitle { font-size: 13px; color: #707e94; font-weight: 600; margin-top: 8px; letter-spacing: 0.5px; }

        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; margin-top: 10px; }
        .sidebar-category-header {
            font-size: 10.5px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.8px; color: #94a3b8; padding: 14px 14px 4px 14px;
        }
        .sidebar-category-header:first-child { padding-top: 4px; }

        .sidebar-menu li a {
            display: flex; align-items: center; gap: 14px; padding: 12px 16px;
            border-radius: 12px; color: #6b7a99; text-decoration: none;
            font-weight: 600; font-size: 14px; transition: all 0.2s ease; position: relative;
        }
        .sidebar-menu li.active a {
            color: #2b43b9; background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(43,67,185,0.08); font-weight: 700;
        }
        .sidebar-menu li.active a::before {
            content: ''; position: absolute; right: -20px; top: 50%; transform: translateY(-50%);
            width: 4px; height: 24px; background-color: #2b43b9; border-radius: 4px 0 0 4px;
        }
        .sidebar-menu li a:hover:not(.active) { background-color: #eaeff8; color: #2b43b9; }
        .sidebar-menu li a i { font-size: 18px; width: 22px; text-align: center; }

        .sidebar-bottom {
            border-top: 1px solid #e3e8f0; padding-top: 20px; margin-top: auto;
            display: flex; flex-direction: column; gap: 8px;
        }
        .sidebar-bottom a {
            display: flex; align-items: center; gap: 14px; padding: 10px 16px;
            border-radius: 12px; color: #6b7a99; text-decoration: none; font-weight: 600; font-size: 14px;
        }
        .sidebar-bottom a.logout { color: #e63946; }
        .sidebar-bottom a:hover { background-color: #eaeff8; }
        .sidebar-bottom a.logout:hover { background-color: #ffeef0; }

        /* ── Main Wrapper ── */
        .main-wrapper { flex: 1; padding: 24px 32px; overflow-y: auto; min-width: 0; }

        /* ── Header Banner ── */
        .top-header-banner {
            background: linear-gradient(135deg, #3d56b2 0%, #2b3a8c 100%);
            border-radius: 20px; padding: 24px 32px; color: white;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 10px 24px rgba(43,67,185,0.2); margin-bottom: 24px;
            animation: fadeInSlideDown 0.4s cubic-bezier(0.16,1,0.3,1);
        }
        .header-title-box h1 { font-size: 28px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 4px; }
        .header-title-box p { font-size: 14px; opacity: 0.85; font-weight: 500; }
        .header-user-nav { display: flex; align-items: center; gap: 20px; }

        /* ── Notification Bell ── */
        .notif-bell-wrap { position: relative; }
        .notif-bell {
            width: 44px; height: 44px; background: rgba(255,255,255,0.95);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #1b2559; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .notif-bell:hover { transform: scale(1.08); box-shadow: 0 6px 16px rgba(0,0,0,0.15); background: #ffffff; color: #2b43b9; }
        .notif-dropdown {
            position: absolute; top: 56px; right: 0; width: 320px;
            background: #ffffff; border-radius: 20px;
            box-shadow: 0 20px 40px -8px rgba(15,23,42,0.18), 0 0 0 1px rgba(226,232,240,0.8);
            display: none; flex-direction: column; overflow: hidden; z-index: 9999;
            animation: fadeInSlideDown 0.25s cubic-bezier(0.16,1,0.3,1);
        }
        .notif-dropdown.show { display: flex; }
        .notif-header {
            padding: 16px 20px; background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .notif-header-title { display: flex; align-items: center; gap: 8px; font-size: 15px; font-weight: 800; color: #0f172a; }
        .notif-body { padding: 16px 20px; display: flex; flex-direction: column; gap: 10px; }
        .notif-info-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #e8edf8;
        }
        .notif-info-icon {
            width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 15px; color: white;
        }
        .notif-info-text .notif-info-title { font-size: 13px; font-weight: 700; color: #0f172a; }
        .notif-info-text .notif-info-desc  { font-size: 12px; color: #64748b; margin-top: 2px; line-height: 1.4; }
        .notif-footer-txt { padding: 10px 16px; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; font-size: 11px; color: #94a3b8; font-weight: 600; }

        /* ── Profile Dropdown ── */
        .profile-dropdown-wrap { position: relative; }
        .user-profile-badge {
            display: flex; align-items: center; gap: 12px;
            padding-left: 16px; border-left: 1px solid rgba(255,255,255,0.2);
            cursor: pointer; transition: opacity 0.2s ease; user-select: none;
        }
        .avatar-img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.8); transition: transform 0.2s ease; }
        .user-profile-badge:hover .avatar-img { transform: scale(1.06); }
        .user-info-text { display: flex; flex-direction: column; }
        .user-name  { font-weight: 700; font-size: 15px; color: white; }
        .user-email { font-size: 12px; opacity: 0.75; color: white; }

        .profile-dropdown {
            position: absolute; top: calc(100% + 14px); right: 0;
            background: #ffffff; border-radius: 16px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.14); border: 1px solid #e2e8f0;
            min-width: 220px; padding: 8px; display: none; z-index: 9999;
            animation: dropIn 0.2s cubic-bezier(0.16,1,0.3,1);
        }
        .profile-dropdown.show { display: block; }
        @keyframes dropIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

        .dropdown-header { padding: 12px 14px 10px; border-bottom: 1px solid #f1f5f9; margin-bottom: 6px; }
        .dropdown-header .d-name { font-size: 14px; font-weight: 800; color: #0f172a; }
        .dropdown-header .d-email { font-size: 12px; color: #64748b; font-weight: 600; margin-top: 2px; }
        .dropdown-header .d-role {
            display: inline-block; background: #eef2ff; color: #3730a3;
            font-size: 11px; font-weight: 800; padding: 2px 10px; border-radius: 20px; margin-top: 6px; text-transform: uppercase;
        }
        .dropdown-item {
            display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 10px;
            font-size: 13.5px; font-weight: 700; color: #334155; text-decoration: none; transition: all 0.15s ease; cursor: pointer;
        }
        .dropdown-item:hover { background: #f8fafc; color: #2b43b9; }
        .dropdown-item i { width: 18px; text-align: center; font-size: 14px; }
        .dropdown-item.danger { color: #dc2626; }
        .dropdown-item.danger:hover { background: #fff5f5; color: #b91c1c; }
        .dropdown-divider { height: 1px; background: #f1f5f9; margin: 6px 0; }

        /* ── Logout Modal ── */
        .logout-modal-backdrop {
            position: fixed; inset: 0; background: rgba(15,23,42,0.65);
            backdrop-filter: blur(8px); z-index: 99999;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden; transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        }
        .logout-modal-backdrop.show { opacity: 1; visibility: visible; }
        .logout-modal-card {
            background: #ffffff; border-radius: 24px; padding: 32px 28px 28px;
            width: 100%; max-width: 400px; text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2); border: 1px solid #e2e8f0;
            transform: scale(0.85) translateY(10px); transition: transform 0.3s cubic-bezier(0.16,1,0.3,1);
        }
        .logout-modal-backdrop.show .logout-modal-card { transform: scale(1) translateY(0); }
        .logout-icon-wrap {
            width: 64px; height: 64px; border-radius: 50%;
            background: linear-gradient(135deg, #fee2e2 0%, #fecdd3 100%);
            color: #ef4444; font-size: 26px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px; box-shadow: 0 8px 20px rgba(239,68,68,0.2);
            animation: logoutPulse 2s infinite;
        }
        @keyframes logoutPulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(239,68,68,0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0); }
        }
        .logout-modal-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .logout-modal-desc { font-size: 13.5px; color: #64748b; font-weight: 500; line-height: 1.5; margin-bottom: 24px; }
        .logout-modal-actions { display: flex; gap: 12px; }
        .btn-cancel-logout {
            flex: 1; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;
            border-radius: 12px; padding: 12px 18px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s ease;
        }
        .btn-cancel-logout:hover { background: #e2e8f0; color: #0f172a; }
        .btn-confirm-logout {
            flex: 1; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff; border: none; border-radius: 12px; padding: 12px 18px;
            font-size: 14px; font-weight: 700; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s ease; box-shadow: 0 4px 14px rgba(239,68,68,0.3); font-family: inherit;
        }
        .btn-confirm-logout:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239,68,68,0.4); }

        /* ── Siswa Info Card ── */
        .siswa-info-card {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border: 1px solid #c7d2fe; border-radius: 16px;
            padding: 18px 22px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 18px;
        }
        .siswa-avatar {
            width: 56px; height: 56px; border-radius: 16px;
            background: linear-gradient(135deg, #3d56b2, #2b3a8c);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: white; font-weight: 800; flex-shrink: 0;
        }
        .siswa-detail { flex: 1; }
        .siswa-name { font-size: 16px; font-weight: 800; color: #1e3a8a; }
        .siswa-meta { font-size: 12.5px; color: #3730a3; margin-top: 2px; }

        /* ── Alert Boxes ── */
        .alert-box { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 13.5px; font-weight: 600; }
        .alert-box.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-box.danger  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        /* ── Mobile Topbar ── */
        .mobile-topbar {
            display: none; background: #ffffff; border-bottom: 1px solid #e2e8f0;
            height: 62px; padding: 0 16px; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 9980; box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .mobile-topbar-left { display: flex; align-items: center; gap: 12px; }
        .btn-sidebar-hamburger {
            width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0;
            background: #f8fafc; color: #1e293b; display: flex; align-items: center;
            justify-content: center; font-size: 18px; cursor: pointer; transition: all 0.2s ease;
        }
        .btn-sidebar-hamburger:hover { background: #eaeff8; color: #2b43b9; }
        .mobile-brand { display: flex; align-items: center; gap: 8px; }
        .mobile-logo-img { width: 32px; height: 32px; object-fit: contain; }
        .mobile-brand-title { font-size: 15px; font-weight: 800; color: #1b2559; letter-spacing: -0.2px; }
        .sidebar-header-mobile { display: none; justify-content: flex-end; margin-bottom: 8px; }
        .btn-close-sidebar {
            width: 34px; height: 34px; border-radius: 10px; border: none;
            background: #f1f5f9; color: #64748b; font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: all 0.15s ease;
        }
        .btn-close-sidebar:hover { background: #fee2e2; color: #ef4444; }
        .sidebar-backdrop {
            position: fixed; inset: 0; background: rgba(15,23,42,0.6);
            backdrop-filter: blur(4px); z-index: 9988;
            opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        .sidebar-backdrop.active { opacity: 1; pointer-events: auto; }
        body.no-scroll { overflow: hidden; }

        .table-responsive-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 12px 12px; }
        .table-responsive-wrap::-webkit-scrollbar { height: 4px; }
        .table-responsive-wrap::-webkit-scrollbar-track { background: #f1f5f9; }
        .table-responsive-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        /* ── Animations ── */
        @keyframes fadeInSlideDown { from { opacity: 0; transform: translateY(-16px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            body { flex-direction: column; min-height: 100vh; overflow-x: hidden; }
            .mobile-topbar { display: flex; }
            .sidebar {
                position: fixed; top: 0; left: 0; bottom: 0;
                width: 290px; max-width: 86vw; height: 100vh; z-index: 9999;
                transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.16,1,0.3,1);
                box-shadow: 0 20px 40px rgba(0,0,0,0.25); background: #ffffff;
                overflow-y: auto; padding: 20px 16px; display: flex; flex-direction: column;
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-header-mobile { display: flex; }
            .main-wrapper { padding: 16px 14px; width: 100%; overflow-x: hidden; }
            .top-header-banner { padding: 18px 20px; border-radius: 16px; flex-direction: column; align-items: flex-start; gap: 14px; margin-bottom: 18px; }
            .header-title-box h1 { font-size: 22px; }
            .header-title-box p { font-size: 13px; }
            .header-user-nav { width: 100%; justify-content: space-between; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.18); }
            .profile-dropdown { width: calc(100vw - 32px); max-width: 320px; right: 0; }
        }
        @media (max-width: 768px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 12px !important; }
        }
        @media (max-width: 480px) {
            .main-wrapper { padding: 12px 10px; }
            .top-header-banner { padding: 14px 16px; border-radius: 14px; gap: 12px; margin-bottom: 14px; }
            .header-title-box h1 { font-size: 18px; }
            .kpi-grid { grid-template-columns: 1fr !important; }
            .siswa-info-card { flex-direction: column; text-align: center; }
            .profile-dropdown { width: calc(100vw - 20px); right: -10px; max-width: unset; }
            .logout-modal-card { margin: 0 12px; padding: 24px 20px 20px; max-width: calc(100vw - 24px); }
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
                <span class="mobile-brand-title">PORTAL SISWA</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="notif-bell" onclick="toggleNotifDropdown()" style="width:36px;height:36px;box-shadow:none;background:#f1f5f9;" title="Info Portal">
                <i class="fa-solid fa-circle-info" style="font-size:16px;"></i>
            </div>
            <div onclick="toggleProfileDropdown()" style="cursor:pointer;">
                <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'Wali Murid').'&background=ffffff&color=2b43b9&bold=true' }}"
                     alt="Avatar" style="width:36px;height:36px;border-radius:50%;border:2px solid #3d56b2;">
            </div>
        </div>
    </div>

    <!-- Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

    <!-- Logout Form -->
    <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none;">@csrf</form>

    <!-- Logout Modal -->
    <div class="logout-modal-backdrop" id="logoutModal" onclick="if(event.target===this)closeLogoutModal()">
        <div class="logout-modal-card">
            <div class="logout-icon-wrap"><i class="fa-solid fa-right-from-bracket"></i></div>
            <h3 class="logout-modal-title">Konfirmasi Keluar</h3>
            <p class="logout-modal-desc">Apakah Anda yakin ingin keluar dari Portal Wali Murid?</p>
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
                <button type="button" class="btn-close-sidebar" onclick="closeMobileSidebar()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="sidebar-brand">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo" class="sidebar-logo-img">
                <div class="sidebar-brand-text">
                    <span class="sidebar-title">JURNAL<br>ABSENSI</span>
                    <span class="sidebar-subtitle">SMKN 1 BOYOLANGU</span>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-category-header">Menu Utama</li>
                <li class="{{ request()->routeIs('wali-murid.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('wali-murid.dashboard') }}">
                        <i class="fa-solid fa-house"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li class="sidebar-category-header">Informasi Siswa</li>
                <li class="{{ request()->routeIs('wali-murid.presensi') ? 'active' : '' }}">
                    <a href="{{ route('wali-murid.presensi') }}">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span>Riwayat Presensi</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('wali-murid.jurnal') ? 'active' : '' }}">
                    <a href="{{ route('wali-murid.jurnal') }}">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Materi Pelajaran</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('wali-murid.jadwal') ? 'active' : '' }}">
                    <a href="{{ route('wali-murid.jadwal') }}">
                        <i class="fa-solid fa-calendar-week"></i>
                        <span>Jadwal Kelas</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('wali-murid.dispensasi') ? 'active' : '' }}">
                    <a href="{{ route('wali-murid.dispensasi') }}">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>Surat Dispensasi</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="sidebar-bottom">
            <a href="#" class="logout" onclick="openLogoutModal(); return false;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Log out</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-wrapper">
        <header class="top-header-banner">
            <div class="header-title-box">
                <h1>@yield('header_title', 'Portal Wali Murid')</h1>
                <p>@yield('header_subtitle', 'Pantau kehadiran, materi pelajaran, dan jadwal putra-putri Anda')</p>
            </div>
            <div class="header-user-nav">

                {{-- Info Bell Dropdown --}}
                <div class="notif-bell-wrap" id="notifBellWrap">
                    <div class="notif-bell" id="notifBellBtn" onclick="toggleNotifDropdown()" title="Info Portal">
                        <i class="fa-solid fa-circle-info" style="font-size: 19px;"></i>
                    </div>
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <div class="notif-header-title">
                                <i class="fa-solid fa-circle-info" style="color:#2b43b9;"></i>
                                <span>Info Portal Siswa</span>
                            </div>
                        </div>
                        <div class="notif-body">
                            <div class="notif-info-item">
                                <div class="notif-info-icon" style="background:linear-gradient(135deg,#3d56b2,#2b3a8c);">
                                    <i class="fa-solid fa-school"></i>
                                </div>
                                <div class="notif-info-text">
                                    <div class="notif-info-title">SMKN 1 Boyolangu</div>
                                    <div class="notif-info-desc">Sistem Jurnal Absensi Guru &amp; Siswa Terpadu</div>
                                </div>
                            </div>
                            <div class="notif-info-item">
                                <div class="notif-info-icon" style="background:linear-gradient(135deg,#059669,#047857);">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </div>
                                <div class="notif-info-text">
                                    <div class="notif-info-title">Pantau Kehadiran</div>
                                    <div class="notif-info-desc">Lihat riwayat presensi lengkap dan statistik kehadiran</div>
                                </div>
                            </div>
                            <div class="notif-info-item">
                                <div class="notif-info-icon" style="background:linear-gradient(135deg,#d97706,#b45309);">
                                    <i class="fa-solid fa-book-open"></i>
                                </div>
                                <div class="notif-info-text">
                                    <div class="notif-info-title">Materi Pelajaran</div>
                                    <div class="notif-info-desc">Pantau jurnal mengajar dan materi yang dipelajari</div>
                                </div>
                            </div>
                        </div>
                        <div class="notif-footer-txt">
                            <i class="fa-solid fa-shield-halved" style="color:#10b981;margin-right:4px;"></i>
                            Portal Wali Murid SMKN 1 Boyolangu
                        </div>
                    </div>
                </div>

                {{-- Portal Switcher --}}
                <x-portal-switcher />

                {{-- User Profile Dropdown --}}
                <div class="profile-dropdown-wrap" id="profileDropdownWrap">
                    <div class="user-profile-badge" onclick="toggleProfileDropdown()">
                        <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'Wali Murid').'&background=ffffff&color=2b43b9&bold=true' }}"
                             alt="Avatar" class="avatar-img">
                        <div class="user-info-text">
                            <span class="user-name">{{ Auth::user()->name ?? 'Wali Murid' }}</span>
                            <span class="user-email">{{ Auth::user()->email }}</span>
                        </div>
                    </div>

                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">
                            <div class="d-name">{{ Auth::user()->name ?? 'Wali Murid' }}</div>
                            <div class="d-email">{{ Auth::user()->email }}</div>
                            <span class="d-role">{{ str_replace('_', ' ', Auth::user()->role ?? 'wali murid') }}</span>
                        </div>

                        <div class="dropdown-divider"></div>

                        <button type="button" onclick="closeProfileDropdown(); openLogoutModal();"
                            class="dropdown-item danger" style="width:100%;background:none;border:none;font-family:inherit;text-align:left;cursor:pointer;">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </div>
                </div>

            </div>
        </header>

        @if(session('success'))
            <div class="alert-box success">
                <div style="display:flex;align-items:center;gap:10px;"><i class="fa-solid fa-circle-check" style="font-size:18px;"></i><span>{{ session('success') }}</span></div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert-box danger">
                <div style="display:flex;align-items:center;gap:10px;"><i class="fa-solid fa-triangle-exclamation" style="font-size:18px;"></i><span>{{ session('error') }}</span></div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        function toggleProfileDropdown() {
            closeNotifDropdown();
            document.getElementById('profileDropdown').classList.toggle('show');
        }
        function closeProfileDropdown() {
            const d = document.getElementById('profileDropdown');
            if (d) d.classList.remove('show');
        }
        function toggleNotifDropdown() {
            closeProfileDropdown();
            document.getElementById('notifDropdown').classList.toggle('show');
        }
        function closeNotifDropdown() {
            const d = document.getElementById('notifDropdown');
            if (d) d.classList.remove('show');
        }
        document.addEventListener('click', function(e) {
            const profWrap  = document.getElementById('profileDropdownWrap');
            const notifWrap = document.getElementById('notifBellWrap');
            if (profWrap  && !profWrap.contains(e.target))  closeProfileDropdown();
            if (notifWrap && !notifWrap.contains(e.target)) closeNotifDropdown();
        });
        function openLogoutModal()  { document.getElementById('logoutModal').classList.add('show'); }
        function closeLogoutModal() { document.getElementById('logoutModal').classList.remove('show'); }
        function toggleMobileSidebar() {
            document.querySelector('.sidebar').classList.toggle('mobile-open');
            document.getElementById('sidebarBackdrop').classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        }
        function closeMobileSidebar() {
            document.querySelector('.sidebar').classList.remove('mobile-open');
            document.getElementById('sidebarBackdrop').classList.remove('active');
            document.body.classList.remove('no-scroll');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { closeLogoutModal(); closeProfileDropdown(); closeNotifDropdown(); closeMobileSidebar(); }
        });
    </script>
    @yield('scripts')
</body>
</html>
