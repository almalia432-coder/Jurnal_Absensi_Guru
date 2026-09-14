<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Waka SDM - JURNAL ABSENSI SMKN 1 BOYOLANGU')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: #f4f6fa; color: #1e293b; display: flex; min-height: 100vh; }

        /* ── SIDEBAR STYLING (Presisi Sesuai Gambar) ── */
        .sidebar {
            width: 255px; background-color: #f8fafc; border-right: 1px solid #e5e9f2;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 24px 18px; flex-shrink: 0; min-height: 100vh;
        }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; padding: 6px 10px 24px 10px; }
        .sidebar-logo-img { width: 44px; height: 44px; object-fit: contain; }
        .sidebar-brand-text { display: flex; flex-direction: column; }
        .sidebar-title { font-weight: 800; font-size: 14.5px; line-height: 1.15; color: #1e3a8a; letter-spacing: -0.2px; }
        .sidebar-subtitle { font-size: 11px; color: #64748b; font-weight: 700; margin-top: 2px; }
        
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; margin-top: 6px; }
        .sidebar-category-header { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; padding: 14px 12px 6px 12px; }
        .sidebar-category-header:first-child { padding-top: 0; }
        
        .sidebar-menu li a {
            display: flex; align-items: center; gap: 12px; padding: 10px 14px;
            border-radius: 12px; color: #475569; text-decoration: none;
            font-weight: 600; font-size: 13.5px; transition: all 0.2s ease; position: relative;
        }
        .sidebar-menu li a i { font-size: 17px; width: 22px; text-align: center; color: #64748b; transition: color 0.2s; }
        .sidebar-menu li.active a {
            color: #1d4ed8; background-color: #ffffff;
            box-shadow: 0 4px 14px rgba(37,99,235,0.08); font-weight: 700;
        }
        .sidebar-menu li.active a i { color: #2563eb; }
        .sidebar-menu li a:hover:not(.active) { background-color: #f1f5f9; color: #1e293b; }

        .sidebar-bottom {
            border-top: 1px solid #e5e9f2; padding-top: 16px; margin-top: auto;
            display: flex; flex-direction: column; gap: 4px;
        }
        .sidebar-bottom a {
            display: flex; align-items: center; gap: 12px; padding: 10px 14px;
            border-radius: 12px; color: #64748b; text-decoration: none;
            font-weight: 600; font-size: 13.5px; transition: all 0.15s;
        }
        .sidebar-bottom a i { font-size: 17px; width: 22px; text-align: center; }
        .sidebar-bottom a:hover { background-color: #f1f5f9; color: #1e293b; }
        .sidebar-bottom a.logout { color: #ef4444; }
        .sidebar-bottom a.logout i { color: #ef4444; }
        .sidebar-bottom a.logout:hover { background-color: #fef2f2; color: #dc2626; }

        /* ── MAIN CONTENT & TOP BANNER (Sesuai Screenshot) ── */
        .main-wrapper { flex: 1; padding: 24px 28px; overflow-x: hidden; }

        .top-header-banner {
            background: linear-gradient(135deg, #2554d7 0%, #1e40af 100%);
            border-radius: 18px; padding: 22px 28px; margin-bottom: 24px;
            display: flex; align-items: center; justify-content: space-between;
            color: white; position: relative; overflow: hidden;
            box-shadow: 0 8px 24px rgba(37,84,215,0.18);
        }
        .top-header-banner::before {
            content: ''; position: absolute; top: -50px; right: -50px; width: 180px; height: 180px;
            border-radius: 50%; background: rgba(255,255,255,0.07); pointer-events: none;
        }
        .header-title-box h1 { font-size: 22px; font-weight: 800; letter-spacing: -0.3px; color: #ffffff; }
        .header-title-box p { font-size: 13.5px; opacity: 0.9; margin-top: 4px; font-weight: 500; color: #e0e7ff; }

        .header-user-nav { display: flex; align-items: center; gap: 14px; z-index: 2; }

        /* School Mini Card */
        .header-badge-school {
            display: flex; align-items: center; gap: 10px; padding: 7px 14px;
            border-radius: 12px; background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.22); color: white;
        }
        .header-badge-school i { font-size: 18px; opacity: 0.9; }
        .header-badge-text { display: flex; flex-direction: column; }
        .badge-title { font-weight: 800; font-size: 10px; letter-spacing: 0.6px; text-transform: uppercase; }
        .badge-sub { font-size: 10px; opacity: 0.85; font-weight: 600; }

        /* Notification Bell */
        .header-bell-btn {
            width: 42px; height: 42px; border-radius: 50%; background: #ffffff;
            color: #1e3a8a; display: flex; align-items: center; justify-content: center;
            font-size: 17px; position: relative; text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.2s;
        }
        .header-bell-btn:hover { transform: scale(1.05); }
        .bell-badge {
            position: absolute; top: -2px; right: -2px; width: 18px; height: 18px;
            background: #ef4444; color: white; font-size: 10px; font-weight: 800;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            border: 2px solid #2554d7;
        }

        /* Profile Pill & Dropdown */
        .profile-dropdown-wrap { position: relative; }
        .user-profile-badge {
            display: flex; align-items: center; gap: 10px; padding: 6px 14px 6px 8px;
            border-radius: 40px; background: rgba(255,255,255,0.18);
            cursor: pointer; border: 1px solid rgba(255,255,255,0.28); transition: all 0.2s;
        }
        .user-profile-badge:hover { background: rgba(255,255,255,0.26); }
        .avatar-circle {
            width: 34px; height: 34px; border-radius: 50%; background: #3b82f6;
            color: white; font-weight: 800; font-size: 12.5px; display: flex;
            align-items: center; justify-content: center; border: 2px solid white;
        }
        .user-info-text { display: flex; flex-direction: column; text-align: left; }
        .user-name { font-weight: 700; font-size: 12.5px; color: white; }
        .user-role-label { font-size: 10.5px; color: #bfdbfe; font-weight: 600; }

        .profile-dropdown {
            display: none; position: absolute; top: calc(100% + 10px); right: 0; width: 250px;
            background: white; border-radius: 16px; box-shadow: 0 16px 40px rgba(0,0,0,0.12);
            z-index: 9999; border: 1px solid #f1f5f9; overflow: hidden;
        }
        .profile-dropdown.open { display: block; }
        .dropdown-header { padding: 14px 16px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
        .d-name { font-weight: 800; font-size: 13.5px; color: #0f172a; }
        .d-email { font-size: 11.5px; color: #64748b; margin-top: 1px; }
        .d-role {
            display: inline-block; margin-top: 6px; font-size: 10px; font-weight: 800;
            background: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;
        }
        .dropdown-item {
            display: flex; align-items: center; gap: 10px; padding: 11px 16px;
            color: #475569; text-decoration: none; font-size: 13px; font-weight: 600; transition: background 0.15s;
        }
        .dropdown-item:hover { background: #f8fafc; color: #0f172a; }
        .dropdown-item.danger { color: #ef4444; }
        .dropdown-item.danger:hover { background: #fef2f2; }
        .dropdown-divider { height: 1px; background: #f1f5f9; margin: 4px 0; }

        /* Alert notifications */
        .alert-box {
            display: flex; align-items: center; justify-content: space-between;
            padding: 13px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 600;
        }
        .alert-box.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-box.danger  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        /* Logout Confirmation Modal */
        .logout-modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.5);
            z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(2px);
        }
        .logout-modal-overlay.open { display: flex; }
        .logout-modal-card {
            background: white; border-radius: 20px; padding: 28px 24px 24px;
            max-width: 360px; width: 90%; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }
        .logout-modal-icon {
            width: 54px; height: 54px; border-radius: 16px; background: #fef2f2;
            color: #ef4444; display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin: 0 auto 14px auto;
        }
        .logout-modal-title { font-size: 17px; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
        .logout-modal-desc { font-size: 13px; color: #64748b; margin-bottom: 20px; }
        .logout-modal-actions { display: flex; gap: 10px; }
        .btn-modal {
            flex: 1; padding: 9px 14px; border-radius: 10px; font-size: 13px; font-weight: 700;
            cursor: pointer; border: none; transition: all 0.2s ease;
        }
        .btn-modal.cancel { background: #f1f5f9; color: #475569; }
        .btn-modal.confirm { background: #ef4444; color: white; }

        /* Mobile responsiveness */
        .mobile-topbar {
            display: none; background: #ffffff; border-bottom: 1px solid #e2e8f0;
            height: 60px; padding: 0 16px; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 9980;
        }
        .btn-sidebar-hamburger {
            width: 38px; height: 38px; border-radius: 10px; border: 1px solid #e2e8f0;
            background: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; font-size: 17px; cursor: pointer;
        }
        .sidebar-header-mobile { display: none; justify-content: flex-end; margin-bottom: 8px; }
        .btn-close-sidebar {
            width: 32px; height: 32px; border-radius: 8px; border: none; background: #f1f5f9; color: #64748b; font-size: 15px; cursor: pointer;
        }
        .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9989; }
        .sidebar-backdrop.open { display: block; }

        @media (max-width: 1024px) {
            .mobile-topbar { display: flex; }
            .sidebar {
                position: fixed; top: 0; left: 0; height: 100vh; z-index: 9990;
                transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                box-shadow: 0 20px 40px rgba(0,0,0,0.25); background: #ffffff; overflow-y: auto; padding: 20px 16px;
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-header-mobile { display: flex; }
            .main-wrapper { padding: 16px 14px; width: 100%; }
            .top-header-banner { padding: 18px 20px; flex-direction: column; align-items: flex-start; gap: 14px; }
            .header-user-nav { width: 100%; justify-content: space-between; }
        }
    </style>
    @yield('styles')
</head>
<body>
    {{-- Mobile Topbar --}}
    <div class="mobile-topbar">
        <div style="display:flex; align-items:center; gap:12px;">
            <button type="button" class="btn-sidebar-hamburger" onclick="toggleMobileSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div style="display:flex; align-items:center; gap:8px;">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo" style="width:30px; height:30px; object-fit:contain;">
                <span style="font-weight:800; font-size:14px; color:#1e3a8a;">WAKA SDM</span>
            </div>
        </div>
        <a href="{{ route('waka-sdm.dashboard') }}" class="header-bell-btn" style="width:36px; height:36px; font-size:15px;">
            <i class="fa-solid fa-bell"></i>
        </a>
    </div>

    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

    {{-- SIDEBAR UTAMA --}}
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
                <li class="sidebar-category-header">MENU UTAMA</li>
                <li class="{{ request()->routeIs('waka-sdm.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('waka-sdm.dashboard') }}">
                        <i class="fa-solid fa-table-cells-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-category-header">PERSETUJUAN</li>
                <li class="{{ request()->routeIs('waka-sdm.izin*') ? 'active' : '' }}">
                    <a href="{{ route('waka-sdm.izin') }}">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Persetujuan Izin Guru</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('waka-sdm.dispensasi*') ? 'active' : '' }}">
                    <a href="{{ route('waka-sdm.dispensasi') }}">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>Persetujuan Dispensasi</span>
                    </a>
                </li>

                <li class="sidebar-category-header">LAPORAN</li>
                <li class="{{ request()->routeIs('waka-sdm.laporan*') ? 'active' : '' }}">
                    <a href="{{ route('waka-sdm.laporan') }}">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Laporan</span>
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
            <a href="{{ route('waka-sdm.help') }}" class="{{ request()->routeIs('waka-sdm.help') ? 'active' : '' }}">
                <i class="fa-regular fa-circle-question"></i>
                <span>Help Centre</span>
            </a>
            <a href="#" class="logout" onclick="openLogoutModal(); return false;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Log out</span>
            </a>
        </div>
    </aside>

    {{-- MAIN WRAPPER --}}
    <main class="main-wrapper">
        {{-- TOP BANNER HEADER --}}
        <header class="top-header-banner">
            <div class="header-title-box">
                <h1>@yield('header_title', 'Dashboard Wakil Kepala Sekolah')</h1>
                <p>@yield('header_subtitle', 'Ringkasan operasional dan persetujuan hari ini, ' . \Carbon\Carbon::now()->translatedFormat('d F Y'))</p>
            </div>
            <div class="header-user-nav">
                <div class="header-badge-school">
                    <i class="fa-solid fa-school"></i>
                    <div class="header-badge-text">
                        <span class="badge-title">SMKN 1 BOYOLANGU</span>
                        <span class="badge-sub">Waka Bidang SDM</span>
                    </div>
                </div>

                <a href="{{ route('waka-sdm.dashboard') }}" class="header-bell-btn" title="Notifikasi Pengajuan">
                    <i class="fa-solid fa-bell"></i>
                    @php
                        $notifCount = ($menungguIzinCount ?? 0) + ($menungguDispensasiCount ?? 0);
                    @endphp
                    @if($notifCount > 0)
                        <span class="bell-badge">{{ $notifCount }}</span>
                    @endif
                </a>

                @php
                    $initials = 'WS';
                    $authName = Auth::user()->name ?? 'Waka SDM';
                    $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $authName)));
                    if (count($words) >= 2) {
                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                    }
                @endphp
                <div class="profile-dropdown-wrap" id="profileDropdownWrap">
                    <div class="user-profile-badge" onclick="toggleProfileDropdown()">
                        <div class="avatar-circle">{{ $initials }}</div>
                        <div class="user-info-text">
                            <span class="user-name">{{ $authName }}</span>
                            <span class="user-role-label">Waka SDM</span>
                        </div>
                    </div>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">
                            <div class="d-name">{{ $authName }}</div>
                            <div class="d-email">{{ Auth::user()->email }}</div>
                            <span class="d-role">WAKA SDM / KEPEGAWAIAN</span>
                        </div>
                        <a href="{{ route('admin.profil') }}" class="dropdown-item">
                            <i class="fa-solid fa-user-pen"></i> Profil Saya
                        </a>
                        <div class="dropdown-divider"></div>
                        <button type="button" onclick="closeProfileDropdown(); openLogoutModal();" class="dropdown-item danger" style="width:100%;background:none;border:none;font-family:inherit;text-align:left;cursor:pointer;">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                        </button>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="alert-box success">
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-circle-check" style="font-size:18px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert-box danger">
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size:18px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        {{-- Page Content --}}
        @yield('content')
    </main>

    {{-- Logout Modal --}}
    <div class="logout-modal-overlay" id="logoutModalOverlay">
        <div class="logout-modal-card">
            <div class="logout-modal-icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></div>
            <div class="logout-modal-title">Konfirmasi Keluar</div>
            <div class="logout-modal-desc">Apakah Anda yakin ingin keluar dari sistem Jurnal Absensi?</div>
            <div class="logout-modal-actions">
                <button type="button" class="btn-modal cancel" onclick="closeLogoutModal()">Batal</button>
                <form action="{{ route('logout') }}" method="POST" style="flex:1;">
                    @csrf
                    <button type="submit" class="btn-modal confirm" style="width:100%;">Ya, Keluar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleProfileDropdown() {
            document.getElementById('profileDropdown').classList.toggle('open');
        }
        function closeProfileDropdown() {
            document.getElementById('profileDropdown').classList.remove('open');
        }
        document.addEventListener('click', function(e) {
            const w = document.getElementById('profileDropdownWrap');
            if (w && !w.contains(e.target)) closeProfileDropdown();
        });

        function openLogoutModal() { document.getElementById('logoutModalOverlay').classList.add('open'); }
        function closeLogoutModal() { document.getElementById('logoutModalOverlay').classList.remove('open'); }

        function toggleMobileSidebar() {
            document.querySelector('.sidebar').classList.toggle('mobile-open');
            document.getElementById('sidebarBackdrop').classList.toggle('open');
        }
        function closeMobileSidebar() {
            document.querySelector('.sidebar').classList.remove('mobile-open');
            document.getElementById('sidebarBackdrop').classList.remove('open');
        }
    </script>
    @yield('scripts')
</body>
</html>
