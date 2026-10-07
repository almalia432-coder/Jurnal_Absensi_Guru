<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Waka Kesiswaan & Kedisiplinan - SMKN 1 BOYOLANGU')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
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
            font-size: 11.5px;
            color: #1d4ed8;
            font-weight: 800;
            margin-top: 4px;
            letter-spacing: 0.5px;
            background: #dbeafe;
            padding: 2px 8px;
            border-radius: 6px;
            display: inline-block;
            width: fit-content;
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

        .sidebar-category-header:first-child { padding-top: 4px; }

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

        .sidebar-bottom a.logout { color: #e63946; }
        .sidebar-bottom a:hover { background-color: #eaeff8; }
        .sidebar-bottom a.logout:hover { background-color: #ffeef0; }

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

        .main-wrapper::-webkit-scrollbar { width: 6px; }
        .main-wrapper::-webkit-scrollbar-track { background: transparent; }
        .main-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        /* Top Header Banner */
        .top-header-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-radius: 20px;
            padding: 24px 32px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.18);
        }

        .header-title-box h1 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
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

        /* ── Exact Wali Kelas Portal Switcher CSS ── */
        .btn-portal-switch {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 15px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.08);
            white-space: nowrap;
        }
        .btn-portal-switch:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .btn-portal-switch i { font-size: 13px; }
        @media (max-width: 640px) {
            .btn-portal-switch span { display: none; }
            .btn-portal-switch { padding: 7px 10px; }
        }

        .notif-bell {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            cursor: pointer;
            position: relative;
            transition: background 0.2s;
        }
        .notif-bell:hover { background: rgba(255, 255, 255, 0.25); }

        .user-profile-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 16px;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
        }
        .avatar-img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.8);
        }
        .user-info-text { display: flex; flex-direction: column; }
        .user-name { font-weight: 700; font-size: 14.5px; color: white; }
        .user-email { font-size: 11.5px; opacity: 0.75; }

        /* Responsive Mobile Styles */
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
        }
        .btn-sidebar-hamburger {
            width: 40px; height: 40px; border-radius: 12px;
            border: 1px solid #e2e8f0; background: #f8fafc;
            font-size: 18px; cursor: pointer;
        }

        @media (max-width: 991px) {
            body { flex-direction: column; height: auto; overflow-y: auto; }
            .mobile-topbar { display: flex; }
            .sidebar {
                position: fixed; top: 0; left: 0; bottom: 0;
                width: 280px; z-index: 9999;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                background: #ffffff;
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-wrapper { height: auto; padding: 16px 14px; }
            .top-header-banner { flex-direction: column; align-items: flex-start; gap: 14px; }
            .header-user-nav { width: 100%; justify-content: space-between; }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Mobile Topbar -->
    <div class="mobile-topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button type="button" class="btn-sidebar-hamburger" onclick="document.querySelector('.sidebar').classList.toggle('mobile-open')">
                <i class="fa-solid fa-bars"></i>
            </button>
            <span style="font-weight:800;font-size:14px;color:#1e3a8a;">WAKA KESISWAAN</span>
        </div>
        <div>
            <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=1e40af&color=ffffff&bold=true' }}" alt="Avatar" style="width:36px;height:36px;border-radius:50%;">
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo" class="sidebar-logo-img">
                <div class="sidebar-brand-text">
                    <span class="sidebar-title">JURNAL ABSENSI</span>
                    <span class="sidebar-subtitle"><i class="fa-solid fa-users-rays"></i> WAKA KESISWAAN</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-category-header">
                    <span>Menu Kesiswaan</span>
                </li>
                <li class="{{ request()->routeIs('waka-kesiswaan.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('waka-kesiswaan.dashboard') }}">
                        <i class="fa-solid fa-border-all"></i>
                        <span>Dashboard Kesiswaan</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('waka-kesiswaan.presensi') ? 'active' : '' }}">
                    <a href="{{ route('waka-kesiswaan.presensi') }}">
                        <i class="fa-solid fa-clipboard-user"></i>
                        <span>Presensi Siswa</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('waka-kesiswaan.dispensasi') ? 'active' : '' }}">
                    <a href="{{ route('waka-kesiswaan.dispensasi') }}">
                        <i class="fa-solid fa-ticket-simple"></i>
                        <span>Dispensasi Siswa</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('waka-kesiswaan.kedisiplinan') ? 'active' : '' }}">
                    <a href="{{ route('waka-kesiswaan.kedisiplinan') }}">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Rekap Kedisiplinan</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('waka-kesiswaan.rekap-terlambat*') ? 'active' : '' }}">
                    <a href="{{ route('waka-kesiswaan.rekap-terlambat') }}">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Siswa Terlambat</span>
                    </a>
                </li>


                @if(Auth::user()->role === 'admin')
                <li class="sidebar-category-header">
                    <span>Akses Administrator</span>
                </li>
                <li>
                    <a href="{{ route('admin.dashboard') }}" style="color: #4f46e5;">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Kembali ke Admin</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="sidebar-bottom">
            <a href="#" class="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Log out</span>
            </a>
        </div>
    </aside>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Main Content -->
    <main class="main-wrapper">
        <!-- Top Header Card -->
        <header class="top-header-banner">
            <div class="header-title-box">
                <h1>@yield('header_title', 'Dashboard Kesiswaan & Kedisiplinan')</h1>
                <p>@yield('header_subtitle', 'Monitoring Ketertiban & Presensi Siswa SMKN 1 Boyolangu')</p>
            </div>
            <div class="header-user-nav">
                @yield('header_extra')

                {{-- Portal Switcher Terpadu --}}
                <x-portal-switcher />

                <div class="user-profile-badge">
                    <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=dbeafe&color=1d4ed8&bold=true' }}" alt="Avatar" class="avatar-img">
                    <div class="user-info-text">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-email">{{ Auth::user()->waka->bidang ?? 'Waka Kesiswaan' }}</span>
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:14px 18px;border-radius:14px;margin-bottom:20px;font-weight:600;display:flex;align-items:center;gap:10px;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:14px 18px;border-radius:14px;margin-bottom:20px;font-weight:600;display:flex;align-items:center;gap:10px;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
