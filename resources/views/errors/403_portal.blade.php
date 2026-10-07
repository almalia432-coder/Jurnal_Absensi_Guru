<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Portal Ditolak | SMKN 1 Boyolangu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .error-card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07);
            border: 1px solid var(--border-color);
            max-width: 620px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        .icon-wrap {
            width: 80px;
            height: 80px;
            background: #fee2e2;
            color: #ef4444;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 24px;
            animation: pulse-badge 2s infinite;
        }

        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .error-code {
            display: inline-block;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ef4444;
            background: #fef2f2;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 12px;
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 12px;
        }

        p.desc {
            font-size: 14.5px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .user-pill {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 10px 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            color: #334155;
            margin-bottom: 28px;
        }

        .user-pill strong {
            color: #0f172a;
        }

        .portal-section {
            border-top: 1px solid var(--border-color);
            padding-top: 24px;
            margin-top: 10px;
            text-align: left;
        }

        .portal-section h3 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }

        .portal-item-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .portal-item-btn:hover {
            border-color: var(--primary-light);
            background: #f0f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .portal-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            color: #ffffff;
        }

        .portal-info {
            flex: 1;
            min-width: 0;
        }

        .portal-info .title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .portal-info .badge {
            font-size: 11px;
            font-weight: 600;
            color: #d97706;
            margin-top: 2px;
        }

        .btn-action-row {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 10px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--primary);
            color: #ffffff;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #1e40af;
            box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: transparent;
            color: #64748b;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-wrap">
            <i class="fa-solid fa-lock"></i>
        </div>

        <div>
            <span class="error-code">403 Forbidden</span>
            <h1>Akses Portal Ditolak</h1>
            <p class="desc">
                Anda tidak memiliki wewenang untuk membuka portal atau halaman ini. Sistem mendeteksi akun Anda tidak memiliki tugas atau wewenang aktif pada portal yang dituju.
            </p>
        </div>

        <div class="user-pill">
            <i class="fa-solid fa-user-circle"></i>
            <span>Login sebagai: <strong>{{ $user->name ?? 'Pengguna' }}</strong> ({{ ucfirst(str_replace('_', ' ', $user->role ?? 'user')) }})</span>
        </div>

        @if(!empty($availablePortals))
            <div class="portal-section">
                <h3><i class="fa-solid fa-compass" style="margin-right: 6px;"></i> Portal Yang Tersedia Untuk Anda</h3>
                <div class="portal-grid">
                    @foreach($availablePortals as $portal)
                        <a href="{{ route($portal['route']) }}" class="portal-item-btn">
                            <div class="portal-icon" style="background-color: {{ $portal['color'] ?? '#3b82f6' }};">
                                <i class="{{ $portal['icon'] ?? 'fa-solid fa-door-open' }}"></i>
                            </div>
                            <div class="portal-info">
                                <div class="title">{{ $portal['name'] }}</div>
                                @if(!empty($portal['badge']))
                                    <div class="badge"><i class="fa-solid fa-circle-info"></i> {{ $portal['badge'] }}</div>
                                @endif
                            </div>
                            <i class="fa-solid fa-chevron-right" style="color: #94a3b8; font-size: 12px;"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <div class="portal-section" style="text-align: center;">
                <p style="font-size: 13.5px; color: #ef4444; margin-bottom: 20px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Tidak ada portal aktif yang terhubung dengan akun Anda saat ini. Silakan hubungi Administrator.
                </p>
            </div>
        @endif

        <div class="btn-action-row">
            @php
                $defaultKey = $user->defaultPortal();
                $defaultRoute = config("portal.portals.{$defaultKey}.route", 'login');
            @endphp
            @if(!empty($availablePortals))
                <a href="{{ route($defaultRoute) }}" class="btn-primary">
                    <i class="fa-solid fa-house"></i> Portal Utama Saya
                </a>
            @endif
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                </button>
            </form>
        </div>
    </div>
</body>
</html>
