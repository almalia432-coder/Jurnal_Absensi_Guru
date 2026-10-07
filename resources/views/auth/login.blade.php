<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Jurnal Mengajar Guru | SMKN 1 Boyolangu</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('asset/logo_smea.png') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-master-wrapper">

    <!-- ================= LEFT HERO PANEL ================= -->
    <div class="hero-panel">
        
        <!-- Desktop Vertical Layered Cloud Wave Divider -->
        <svg class="cloud-divider-desktop" viewBox="0 0 100 600" preserveAspectRatio="none" aria-hidden="true">
            <!-- Layer 1: Deep Royal Blue Shadow -->
            <path d="M 100 0 
                     L 60 0 
                     A 42 42 0 0 0 62 80 
                     A 58 55 0 0 0 58 185 
                     A 54 50 0 0 0 63 285 
                     A 62 58 0 0 0 57 395 
                     A 56 52 0 0 0 61 500 
                     A 52 48 0 0 0 60 600 
                     L 100 600 Z" 
                  fill="#1b63ef" opacity="0.65" />

            <!-- Layer 2: Soft Sky Blue Tint -->
            <path d="M 100 0 
                     L 75 0 
                     A 38 40 0 0 0 78 80 
                     A 52 52 0 0 0 74 185 
                     A 48 48 0 0 0 79 285 
                     A 56 54 0 0 0 72 395 
                     A 50 50 0 0 0 77 500 
                     A 46 46 0 0 0 76 600 
                     L 100 600 Z" 
                  fill="#75b0fb" opacity="0.85" />

            <!-- Layer 3: Solid White Cloud (Connects seamlessly into white form) -->
            <path d="M 100 0 
                     L 88 0 
                     A 36 38 0 0 0 90 80 
                     A 48 48 0 0 0 86 185 
                     A 44 45 0 0 0 91 285 
                     A 50 50 0 0 0 84 395 
                     A 46 47 0 0 0 89 500 
                     A 42 44 0 0 0 88 600 
                     L 100 600 Z" 
                  fill="#ffffff" />
        </svg>

        <!-- Mobile Horizontal Layered Cloud Wave Divider -->
        <svg class="cloud-divider-mobile" viewBox="0 0 600 70" preserveAspectRatio="none" aria-hidden="true">
            <!-- Mobile Layer 1 -->
            <path d="M 0 70 
                     L 0 32 
                     A 45 35 0 0 0 85 30 
                     A 60 40 0 0 0 205 34 
                     A 55 38 0 0 0 315 28 
                     A 62 42 0 0 0 435 32 
                     A 50 36 0 0 0 540 30 
                     A 40 32 0 0 0 600 34 
                     L 600 70 Z" 
                  fill="#1b63ef" opacity="0.65" />

            <!-- Mobile Layer 2 -->
            <path d="M 0 70 
                     L 0 44 
                     A 40 30 0 0 0 85 42 
                     A 55 35 0 0 0 205 46 
                     A 50 32 0 0 0 315 40 
                     A 56 36 0 0 0 435 44 
                     A 45 30 0 0 0 540 42 
                     A 35 28 0 0 0 600 46 
                     L 600 70 Z" 
                  fill="#75b0fb" opacity="0.85" />

            <!-- Mobile Layer 3: Solid White -->
            <path d="M 0 70 
                     L 0 56 
                     A 35 25 0 0 0 85 54 
                     A 50 30 0 0 0 205 58 
                     A 45 28 0 0 0 315 52 
                     A 50 30 0 0 0 435 56 
                     A 40 25 0 0 0 540 54 
                     A 30 22 0 0 0 600 58 
                     L 600 70 Z" 
                  fill="#ffffff" />
        </svg>

        <!-- Hero Content Inside -->
        <div class="hero-inner">
            <!-- Top Subtitle -->
            <div class="hero-top">
                <div class="welcome-label">Welcome to</div>
            </div>

            <!-- Central Brand Identity -->
            <div class="hero-center">
                <div class="hero-logo-badge">
                    <img src="{{ asset('asset/logo_smea.png') }}" alt="Logo SMKN 1 Boyolangu">
                </div>
                <h1 class="brand-title">Jurnal Mengajar</h1>
                <div class="brand-school">SMKN 1 BOYOLANGU</div>
                <p class="brand-desc">
                    Sistem digitalisasi presensi siswa, monitoring agenda kelas harian, dan rekapitulasi jurnal mengajar guru terpadu.
                </p>
            </div>

            <!-- Bottom Badge / Credits -->
            <div class="hero-bottom">
                <span>SMKN 1 BOYOLANGU</span>
                <span class="separator">|</span>
                <span>E-DISIPLIN GURU</span>
            </div>
        </div>

    </div>

    <!-- ================= RIGHT FORM PANEL ================= -->
    <div class="form-panel">
        
        <div class="form-panel-container">
            <!-- Form Header -->
            <div class="form-header">
                <h2 class="form-title">Masuk ke Akun</h2>
                <p class="form-subtitle">Masukkan NIP, NISN, atau Email beserta password untuk mengakses sistem jurnal.</p>
            </div>

            <!-- Validation Error Alert -->
            @if ($errors->any())
                <div class="alert-error" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="auth-form" id="loginForm">
                @csrf

                <!-- Username / NIP / Email Input -->
                <div class="input-field-group">
                    <label class="input-field-label" for="username">NIP / NISN / Email</label>
                    <div class="input-box-wrapper">
                        <i class="bi bi-person field-icon-left"></i>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="field-control" 
                            placeholder="Masukkan NIP, NISN atau Email" 
                            value="{{ old('username') }}" 
                            required 
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="input-field-group">
                    <label class="input-field-label" for="password">Password</label>
                    <div class="input-box-wrapper">
                        <i class="bi bi-lock field-icon-left"></i>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="field-control" 
                            placeholder="••••••••••••" 
                            required
                        >
                        <button type="button" class="btn-toggle-eye" id="togglePasswordBtn" onclick="togglePasswordVisibility()" title="Lihat/Sembunyikan Password">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Options: Remember Me & Forgot Link -->
                <div class="form-options-row">
                    <label class="checkbox-label" for="remember">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat Saya</span>
                    </label>
                    <a href="javascript:void(0)" class="link-forgot" onclick="openHelpModal()">Lupa Password?</a>
                </div>

                <!-- Submit Button (full width) -->
                <div class="form-actions-row">
                    <button type="submit" class="btn-pill btn-pill-primary btn-pill-full">
                        <span>Masuk Sekarang</span>
                        <i class="bi bi-arrow-right-short" style="font-size: 1.3rem;"></i>
                    </button>
                </div>
            </form>

            <!-- Card Bottom Information -->
            <div class="card-bottom-info">
                <span><i class="bi bi-shield-check" style="color: var(--primary-blue);"></i> Portal Resmi SMKN 1 Boyolangu</span>
                <span>Bantuan IT: <a href="javascript:void(0)" onclick="openHelpModal()">Hubungi Tim</a></span>
            </div>
        </div>

    </div>

</div>

<!-- ================= MODAL BANTUAN / LUPA PASSWORD ================= -->
<div class="modal-backdrop" id="helpModalBackdrop" onclick="handleHelpBackdropClick(event)">
    <div class="modal-dialog" style="max-width: 440px;">
        <div class="modal-header">
            <h3><i class="bi bi-headset"></i> Layanan Bantuan IT</h3>
            <button type="button" class="modal-close-btn" onclick="closeHelpModal()" title="Tutup Modal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body" style="text-align: center; padding: 2rem 1.75rem;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #eff6ff; color: var(--primary-blue); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 1.25rem;">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h4 style="font-size: 1.15rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem;">Reset Kredensial Akun</h4>
            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                Untuk keamanan data presensi siswa dan guru, pengaturan ulang password atau perubahan NIP/NISN dapat diajukan melalui administrator IT SMKN 1 Boyolangu.
            </p>
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 1rem; text-align: left; margin-bottom: 1.5rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.35rem;">Kontak Administrator IT</div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-dark);"><i class="bi bi-envelope-fill" style="color: var(--primary-blue);"></i> it-support@smkn1boyolangu.sch.id</div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-dark); margin-top: 0.25rem;"><i class="bi bi-geo-alt-fill" style="color: var(--primary-blue);"></i> Ruang Server &amp; IT SMKN 1 Boyolangu</div>
            </div>
            <button type="button" class="btn-pill btn-pill-primary btn-pill-full" onclick="closeHelpModal()">
                <span>Saya Mengerti</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        }
    }

    // Modal Help Handlers
    function openHelpModal(e) {
        if (e) e.preventDefault();
        document.getElementById('helpModalBackdrop').classList.add('active');
    }

    function closeHelpModal() {
        document.getElementById('helpModalBackdrop').classList.remove('active');
    }

    function handleHelpBackdropClick(e) {
        if (e.target.id === 'helpModalBackdrop') {
            closeHelpModal();
        }
    }
</script>

</body>
</html>
