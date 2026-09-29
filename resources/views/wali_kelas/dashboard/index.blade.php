@extends('layouts.wali_kelas')

@section('title', 'Dashboard Wali Kelas - SMKN 1 BOYOLANGU')
@section('header_title', 'Dashboard Wali Kelas')
@section('header_subtitle', 'Pantau kehadiran harian siswa & jurnal pembelajaran kelas binaan')

@section('header_extra')
    @if($kelas)
    <div class="header-kelas-pill">
        <i class="fa-solid fa-graduation-cap"></i>
        <div>
            <div class="header-kelas-title">Kelas Binaan</div>
            <div class="header-kelas-name">{{ $kelas->nama_kelas }} — {{ $kelas->jurusan }}</div>
        </div>
    </div>
    @endif
@endsection

@section('styles')
<style>
    /* Greeting Card */
    .greeting-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
        border-radius: 20px;
        padding: 24px 28px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }
    .greeting-left { display: flex; align-items: center; gap: 18px; }
    .greeting-avatar-wrap {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.3);
    }
    .greeting-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
    .greeting-subtitle { font-size: 13.5px; color: #64748b; font-weight: 500; }
    .greeting-meta { display: flex; align-items: center; gap: 12px; margin-top: 6px; flex-wrap: wrap; }
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        background: #e0e7ff;
        color: #3730a3;
    }
    .meta-pill.date { background: #f1f5f9; color: #475569; }

    /* Admin Class Switcher */
    .class-switcher-select {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 9px 14px;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        outline: none;
        cursor: pointer;
    }

    /* 5 Stat Cards Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background-color: #ffffff;
        border-radius: 18px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .stat-label {
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
    }
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }
    .stat-icon.indigo  { background-color: #e0e7ff; color: #3730a3; }
    .stat-icon.emerald { background-color: #d1fae5; color: #065f46; }
    .stat-icon.sky     { background-color: #e0f2fe; color: #0369a1; }
    .stat-icon.amber   { background-color: #fef3c7; color: #92400e; }
    .stat-icon.rose    { background-color: #ffe4e6; color: #9f1239; }
    .stat-icon.violet  { background-color: #ede9fe; color: #5b21b6; }

    .stat-value {
        font-size: 28px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }
    .stat-footer {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pulse-danger {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ef4444;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: pulseRed 1.8s infinite;
    }
    @keyframes pulseRed {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    /* Critical Alert Card */
    .alert-card-warning {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1.5px solid #fde68a;
        border-radius: 18px;
        padding: 16px 22px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.08);
    }
    .alert-warning-left { display: flex; align-items: center; gap: 14px; }
    .alert-warning-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #fef08a;
        color: #854d0e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .alert-warning-title { font-size: 14.5px; font-weight: 800; color: #78350f; }
    .alert-warning-desc  { font-size: 12.5px; color: #92400e; margin-top: 2px; font-weight: 500; }
    .btn-alert-action {
        background: #78350f;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-alert-action:hover { background: #451a03; color: #ffffff; }

    /* Middle Row Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    .chart-card {
        background-color: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
    }
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }
    .chart-title { font-size: 16px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
    .chart-title i { color: #2b43b9; }
    .chart-subtitle { font-size: 12px; color: #64748b; font-weight: 600; }

    /* Bottom 2 Column Section */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .section-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }
    .section-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .section-ttl { font-size: 16px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
    .section-ttl i { color: #2b43b9; }

    /* Session Jurnal List */
    .jurnal-item-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }
    .jurnal-item-card:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .jurnal-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .jurnal-jam-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        background: #e2e8f0;
        color: #334155;
    }
    .jurnal-mapel-name { font-size: 14px; font-weight: 800; color: #0f172a; }
    .jurnal-guru-name  { font-size: 12px; font-weight: 600; color: #64748b; margin-top: 2px; }
    .jurnal-materi-box {
        font-size: 12.5px;
        color: #334155;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 8px 12px;
        margin-top: 8px;
        line-height: 1.4;
    }
    .jurnal-catatan-box {
        font-size: 11.5px;
        color: #b45309;
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 8px;
        padding: 6px 10px;
        margin-top: 6px;
    }

    /* Absent Students List */
    .absent-student-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
    }
    .absent-student-card:hover { background: #f8fafc; border-radius: 10px; }
    .absent-student-left { display: flex; align-items: center; gap: 12px; }
    .student-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #3730a3;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .student-name { font-size: 13.5px; font-weight: 700; color: #0f172a; }
    .student-nisn { font-size: 11px; color: #64748b; font-weight: 600; }
    .badge-status-sakit { font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 12px; background: #fef3c7; color: #92400e; }
    .badge-status-izin  { font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 12px; background: #e0f2fe; color: #0369a1; }
    .badge-status-alpha { font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 12px; background: #fee2e2; color: #991b1b; }
    .badge-status-disp  { font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 12px; background: #ede9fe; color: #5b21b6; }

    .btn-wa-contact {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #dcfce7;
        color: #15803d;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .btn-wa-contact:hover { background: #22c55e; color: #ffffff; transform: scale(1.08); }

    /* Empty States */
    .empty-box {
        text-align: center;
        padding: 36px 20px;
        color: #94a3b8;
    }
    .empty-box i { font-size: 38px; color: #cbd5e1; margin-bottom: 10px; display: block; }
    .empty-box p { font-size: 13.5px; font-weight: 700; color: #64748b; }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); }
        .charts-grid, .bottom-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .greeting-card {
            padding: 16px;
            border-radius: 16px;
            gap: 14px;
        }
        .greeting-title { font-size: 17px; }
        .greeting-subtitle { font-size: 12.5px; }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .stat-card {
            padding: 14px 16px;
            border-radius: 14px;
        }
        .stat-value {
            font-size: 22px;
            margin-bottom: 4px;
        }
        .stat-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }
        .charts-grid, .bottom-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

@if(!$kelas)
    {{-- No class assigned state --}}
    <div style="background:#ffffff;border-radius:24px;padding:60px 24px;text-align:center;border:1px solid #e2e8f0;">
        <i class="fa-solid fa-school-circle-xmark" style="font-size:56px;color:#cbd5e1;margin-bottom:16px;display:block;"></i>
        <h3 style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:8px;">Belum Ada Kelas Binaan Ditugaskan</h3>
        <p style="font-size:14px;color:#64748b;max-width:480px;margin:0 auto 20px;">Akun Anda belum terhubung dengan kelas binaan manapun. Hubungi administrator sekolah untuk menetapkan kelas binaan Anda.</p>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.master.kelas') }}" class="btn-add-jadwal" style="display:inline-flex;">
            <i class="fa-solid fa-door-open"></i> Kelola Data Kelas &amp; Wali Kelas
        </a>
        @endif
    </div>
@else

    {{-- Greeting & Identity Card --}}
    <div class="greeting-card">
        <div class="greeting-left">
            <div class="greeting-avatar-wrap">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div>
                <div class="greeting-title">
                    Selamat Datang, {{ $waliKelas->nama_lengkap ?? Auth::user()->name }}!
                </div>
                <div class="greeting-subtitle">
                    Wali Kelas <strong>{{ $kelas->nama_kelas }}</strong> — Jurusan {{ $kelas->jurusan }}
                </div>
                <div class="greeting-meta">
                    <span class="meta-pill"><i class="fa-solid fa-graduation-cap"></i> {{ $tahunAjaranAktif->nama ?? '2024/2025' }} ({{ $tahunAjaranAktif->semester ?? 'Ganjil' }})</span>
                    <span class="meta-pill date"><i class="fa-regular fa-calendar"></i> {{ $todayFormatted }}</span>
                </div>
            </div>
        </div>

        @if(Auth::user()->role === 'admin' && count($allKelasList) > 1)
        <div>
            <form action="{{ route('wali-kelas.dashboard') }}" method="GET">
                <label style="font-size:11px;font-weight:800;color:#64748b;display:block;margin-bottom:4px;text-transform:uppercase;">Pratinjau Kelas Lain:</label>
                <select name="kelas_id" class="class-switcher-select" onchange="this.form.submit()">
                    @foreach($allKelasList as $k)
                        <option value="{{ $k->id_kelas }}" {{ $k->id_kelas == $kelas->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }} (Wali: {{ $k->waliKelas->nama_lengkap ?? 'Belum ada' }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
    </div>

    {{-- Top 5 KPI Cards --}}
    <div class="stats-grid">
        {{-- Card 1: Total Siswa --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Siswa</span>
                <div class="stat-icon indigo"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="stat-value">{{ $totalSiswa }}</div>
            <div class="stat-footer">
                <span><i class="fa-solid fa-mars" style="color:#2b43b9;"></i> {{ $totalSiswaL }} L</span>
                <span style="margin: 0 4px;">•</span>
                <span><i class="fa-solid fa-venus" style="color:#db2777;"></i> {{ $totalSiswaP }} P</span>
            </div>
        </div>

        {{-- Card 2: Kehadiran Hari Ini --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Hadir Hari Ini</span>
                <div class="stat-icon emerald"><i class="fa-solid fa-user-check"></i></div>
            </div>
            <div class="stat-value" style="color:#065f46;">{{ $hadirCount }}</div>
            <div class="stat-footer">
                <span style="color:#059669;font-weight:800;"><i class="fa-solid fa-arrow-trend-up"></i> {{ $pctHadir }}%</span>
                <span>dari total siswa</span>
            </div>
        </div>

        {{-- Card 3: Izin & Sakit --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Izin &amp; Sakit</span>
                <div class="stat-icon amber"><i class="fa-solid fa-notes-medical"></i></div>
            </div>
            <div class="stat-value" style="color:#b45309;">{{ $sakitCount + $izinCount }}</div>
            <div class="stat-footer">
                <span>{{ $sakitCount }} Sakit</span>
                <span style="margin: 0 4px;">•</span>
                <span>{{ $izinCount }} Izin</span>
            </div>
        </div>

        {{-- Card 4: Alpha / Tanpa Ket --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Alpha / Tanpa Ket.</span>
                <div class="stat-icon rose"><i class="fa-solid fa-user-xmark"></i></div>
            </div>
            <div class="stat-value" style="color:#dc2626;">{{ $alphaCount }}</div>
            <div class="stat-footer">
                @if($alphaCount > 0)
                    <span class="pulse-danger"></span>
                    <span style="color:#dc2626;font-weight:700;">Perlu dicek!</span>
                @else
                    <span style="color:#10b981;font-weight:700;"><i class="fa-solid fa-circle-check"></i> Nihil (Bagus)</span>
                @endif
            </div>
        </div>

        {{-- Card 5: Jurnal Mengajar Terisi --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Jurnal Terisi</span>
                <div class="stat-icon violet"><i class="fa-solid fa-book-open-reader"></i></div>
            </div>
            <div class="stat-value" style="color:#5b21b6;">{{ $totalJurnalHariIni }}</div>
            <div class="stat-footer">
                <span>Sesi pembelajaran hari ini</span>
            </div>
        </div>
    </div>

    {{-- Critical Alert Section: Siswa Perlu Perhatian --}}
    @if($siswaPerluPerhatian->count() > 0)
    <div class="alert-card-warning">
        <div class="alert-warning-left">
            <div class="alert-warning-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="alert-warning-title">
                    {{ $siswaPerluPerhatian->count() }} Siswa Perlu Perhatian Khusus (Sering Alpha)
                </div>
                <div class="alert-warning-desc">
                    Terdapat siswa di kelas {{ $kelas->nama_kelas }} dengan akumulasi $\ge 2$ kali Alpha dalam 30 hari terakhir.
                </div>
            </div>
        </div>
        <div>
            <a href="{{ route('wali-kelas.siswa') }}" class="btn-alert-action">
                <i class="fa-solid fa-eye"></i> Lihat &amp; Tindak Lanjuti
            </a>
        </div>
    </div>
    @endif

    {{-- Middle Charts Grid --}}
    <div class="charts-grid">
        {{-- Chart 1: Tren Kehadiran --}}
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <div class="chart-title">
                        <i class="fa-solid fa-chart-line"></i> Tren Kehadiran Siswa (7 Hari Terakhir)
                    </div>
                    <div class="chart-subtitle">Fluktuasi kehadiran siswa di kelas {{ $kelas->nama_kelas }}</div>
                </div>
            </div>
            <div style="height: 240px; position: relative;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Komposisi Bulan Ini --}}
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <div class="chart-title">
                        <i class="fa-solid fa-chart-pie"></i> Komposisi Presensi Bulan Ini
                    </div>
                    <div class="chart-subtitle">Distribusi status kehadiran siswa</div>
                </div>
            </div>
            <div style="height: 240px; position: relative; display:flex; align-items:center; justify-content:center;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bottom Two Column Section --}}
    <div class="bottom-grid">
        {{-- Left: Aktivitas Jurnal Mengajar Hari Ini --}}
        <div class="section-card">
            <div class="section-hdr">
                <div class="section-ttl">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Jurnal Mengajar Guru di Kelas Hari Ini</span>
                </div>
                <span style="font-size:12px;font-weight:700;color:#2b43b9;background:#eaeff8;padding:4px 12px;border-radius:20px;">
                    {{ $todayJurnals->count() }} Sesi Terisi
                </span>
            </div>

            @forelse($todayJurnals as $j)
                <div class="jurnal-item-card">
                    <div class="jurnal-top">
                        <span class="jurnal-jam-badge">
                            <i class="fa-regular fa-clock"></i> {{ $j->jam_ke ?? 'Jam Pelajaran' }} ({{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }})
                        </span>
                        <span style="font-size:11px;font-weight:800;color:#065f46;background:#d1fae5;padding:2px 8px;border-radius:6px;">
                            <i class="fa-solid fa-user-check"></i> {{ $j->jumlah_siswa_hadir }} Hadir
                        </span>
                    </div>
                    <div class="jurnal-mapel-name">{{ $j->mapel->nama_mapel ?? 'Mata Pelajaran' }}</div>
                    <div class="jurnal-guru-name">
                        <i class="fa-solid fa-chalkboard-user" style="color:#2b43b9;"></i> {{ $j->guru->nama_lengkap ?? '-' }}
                    </div>

                    @if($j->materi)
                    <div class="jurnal-materi-box">
                        <strong>Materi:</strong> {{ $j->materi }}
                    </div>
                    @endif

                    @if($j->catatan)
                    <div class="jurnal-catatan-box">
                        <i class="fa-solid fa-note-sticky"></i> <strong>Catatan Guru:</strong> {{ $j->catatan }}
                    </div>
                    @endif
                </div>
            @empty
                <div class="empty-box">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <p>Belum ada jurnal mengajar yang diisi oleh guru hari ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Right: Siswa Tidak Hadir Hari Ini --}}
        <div class="section-card">
            <div class="section-hdr">
                <div class="section-ttl">
                    <i class="fa-solid fa-user-clock"></i>
                    <span>Siswa Tidak Hadir Hari Ini</span>
                </div>
                <span style="font-size:12px;font-weight:700;color:#ef4444;background:#fee2e2;padding:4px 12px;border-radius:20px;">
                    {{ $siswaTidakMasukToday->count() }} Siswa
                </span>
            </div>

            @forelse($siswaTidakMasukToday as $p)
                <div class="absent-student-card">
                    <div class="absent-student-left">
                        <div class="student-avatar">
                            {{ strtoupper(substr($p->siswa->nama_lengkap ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <div class="student-name">{{ $p->siswa->nama_lengkap ?? 'Siswa' }}</div>
                            <div class="student-nisn">
                                NISN: {{ $p->siswa->nisn ?? '-' }}
                                @if($p->keterangan)
                                    • <span style="color:#64748b;font-style:italic;">"{{ $p->keterangan }}"</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($p->status === 'Sakit')
                            <span class="badge-status-sakit">Sakit</span>
                        @elseif($p->status === 'Izin')
                            <span class="badge-status-izin">Izin</span>
                        @elseif($p->status === 'Alpha')
                            <span class="badge-status-alpha">Alpha</span>
                        @else
                            <span class="badge-status-disp">Dispensasi</span>
                        @endif

                        @if(!empty($p->siswa->no_hp_ortu))
                            @php
                                $phone = preg_replace('/[^0-9]/', '', $p->siswa->no_hp_ortu);
                                if (str_starts_with($phone, '0')) {
                                    $phone = '62' . substr($phone, 1);
                                }
                                $msg = urlencode("Halo Bapak/Ibu Wali Murid dari ananda {$p->siswa->nama_lengkap}, kami dari pihak Wali Kelas {$kelas->nama_kelas} SMKN 1 Boyolangu ingin mengonfirmasi kehadiran siswa pada hari ini ({$p->status}). Terima kasih.");
                            @endphp
                            <a href="https://wa.me/{{ $phone }}?text={{ $msg }}" target="_blank" class="btn-wa-contact" title="Hubungi Orang Tua via WhatsApp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-box">
                    <i class="fa-solid fa-circle-check" style="color:#10b981;"></i>
                    <p style="color:#065f46;">Luar biasa! Seluruh siswa hadir hari ini.</p>
                </div>
            @endforelse
        </div>
    </div>

@endif

@endsection

@section('scripts')
@if($kelas)
<script>
    // ── 1. Trend Kehadiran Chart ───────────────────────────
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [
                {
                    label: 'Siswa Hadir',
                    data: {!! json_encode($trendHadir) !!},
                    borderColor: '#2b43b9',
                    backgroundColor: 'rgba(43, 67, 185, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2b43b9',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Tidak Hadir (S/I/A)',
                    data: {!! json_encode($trendAbsen) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.04)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.35,
                    pointBackgroundColor: '#ef4444',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: { font: { family: 'Plus Jakarta Sans', size: 12, weight: '700' }, boxWidth: 12, usePointStyle: true }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '800' },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: {{ max($totalSiswa, 10) }},
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#94a3b8' }
                }
            }
        }
    });

    // ── 2. Komposisi Presensi Chart ────────────────────────
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieValues = [
        {{ $pieData['Hadir'] }},
        {{ $pieData['Sakit'] }},
        {{ $pieData['Izin'] }},
        {{ $pieData['Alpha'] }},
        {{ $pieData['Dispensasi'] }}
    ];
    const totalPie = pieValues.reduce((a, b) => a + b, 0);

    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi'],
            datasets: [{
                data: totalPie > 0 ? pieValues : [1, 0, 0, 0, 0],
                backgroundColor: ['#10b981', '#f59e0b', '#0ea5e9', '#ef4444', '#8b5cf6'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { font: { family: 'Plus Jakarta Sans', size: 11.5, weight: '700' }, boxWidth: 12, usePointStyle: true, padding: 12 }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                    cornerRadius: 8,
                }
            },
            cutout: '70%'
        }
    });
</script>
@endif
@endsection
