@extends('layouts.waka_kurikulum')

@section('title', 'Dashboard Kurikulum - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Dashboard Waka Kurikulum')
@section('header_subtitle', 'Monitoring Pembelajaran, Presensi KBM & Jurnal Mengajar SMKN 1 Boyolangu')

@section('styles')
<style>
    /* Section Greeting */
    .greeting-section {
        margin-bottom: 24px;
    }

    .greeting-title {
        font-size: 22px;
        font-weight: 700;
        color: #1b2559;
        margin-bottom: 4px;
    }

    .greeting-subtitle {
        font-size: 14px;
        color: #6b7a99;
        font-weight: 500;
    }

    /* Top 5 Stat Cards Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .stat-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #707e94;
        letter-spacing: 0.5px;
    }

    .stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .stat-icon.blue {
        background-color: #eef2ff;
        color: #2b43b9;
    }

    .stat-icon.green {
        background-color: #e6f9f0;
        color: #10b981;
    }

    .stat-icon.orange {
        background-color: #fff7ed;
        color: #f97316;
    }

    .stat-icon.red {
        background-color: #fef2f2;
        color: #ef4444;
    }

    .stat-icon.dark {
        background-color: #f1f5f9;
        color: #334155;
    }

    .stat-value {
        font-size: 26px;
        font-weight: 800;
        color: #1b2559;
        line-height: 1.1;
        margin-bottom: 8px;
    }

    .stat-footer {
        font-size: 12px;
        color: #707e94;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .stat-badge-green {
        color: #10b981;
        font-weight: 700;
    }

    .stat-badge-red {
        color: #ef4444;
        font-weight: 700;
    }

    /* Middle Row Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .chart-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
    }

    .chart-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1b2559;
    }

    .line-chart-container {
        position: relative;
        height: 220px;
        width: 100%;
    }

    .donut-chart-container {
        position: relative;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .donut-center-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }

    .donut-center-pct {
        font-size: 22px;
        font-weight: 800;
        color: #1b2559;
        line-height: 1;
    }

    .donut-center-lbl {
        font-size: 11px;
        color: #6b7a99;
        font-weight: 600;
        margin-top: 2px;
    }

    /* Tingkat KBM Section */
    .tingkat-card-wrap {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
        margin-bottom: 24px;
    }

    .tingkat-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .tingkat-box {
        background: #f8fafc;
        border-radius: 14px;
        padding: 16px 18px;
        border: 1px solid #e2e8f0;
    }

    .tingkat-box-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tingkat-box-val {
        font-size: 20px;
        font-weight: 800;
        color: #1b2559;
    }

    .tingkat-box-sub {
        font-size: 11.5px;
        color: #94a3b8;
        font-weight: 600;
        margin-top: 3px;
    }

    .progress-bar-wrap {
        background: #e2e8f0;
        border-radius: 8px;
        height: 7px;
        margin-top: 10px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        border-radius: 8px;
        transition: width 0.6s ease;
    }

    /* Bottom Row Widgets */
    .widgets-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .widget-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
    }

    .widget-title {
        font-size: 16px;
        font-weight: 700;
        color: #1b2559;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .attention-list, .activity-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .attention-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 12px 14px;
        border-radius: 14px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        background: #ffffff;
        text-decoration: none;
    }

    .attention-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
        transform: translateX(4px);
    }

    .attention-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        color: #ffffff;
    }

    .attention-icon.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .attention-icon.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .attention-icon.info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .attention-icon.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .attention-content {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .attention-head {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .attention-sub {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        margin-top: 3px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 12px;
        border-radius: 12px;
        transition: background 0.15s ease;
    }

    .activity-item:hover {
        background: #f8fafc;
    }

    .activity-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(43, 67, 185, 0.12);
    }

    .activity-content {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .activity-desc {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .activity-time {
        font-size: 11.5px;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .activity-tag {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        background: #f1f5f9;
        color: #475569;
        text-transform: uppercase;
    }

    /* Responsive Queries */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .greeting-title { font-size: 18px; }
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
        .stat-label {
            font-size: 10.5px;
        }
        .stat-footer {
            font-size: 10.5px;
        }
        .charts-grid, .widgets-grid {
            grid-template-columns: 1fr;
            gap: 14px;
            margin-bottom: 16px;
        }
        .tingkat-stats {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .chart-card, .widget-card, .tingkat-card-wrap {
            padding: 16px;
            border-radius: 14px;
        }
        .line-chart-container, .donut-chart-container {
            height: 190px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .stat-card {
            padding: 12px;
        }
        .stat-card:last-child {
            grid-column: span 2;
        }
        .stat-value {
            font-size: 20px;
        }
        .stat-icon {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
        .attention-item, .activity-item {
            padding: 8px;
            gap: 10px;
        }
    }
</style>
@endsection

@section('content')
<!-- Greeting Section -->
<div class="greeting-section">
    <h2 class="greeting-title">{{ $greetingText }}, {{ $namaUser }}</h2>
    <p class="greeting-subtitle">Berikut ringkasan aktivitas KBM, jurnal mengajar &amp; presensi siswa hari ini — {{ $todayFormatted }}</p>
</div>

<!-- 5 Stat Cards Grid -->
<div class="stats-grid">
    <!-- Card 1: TOTAL ROMBEL -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">TOTAL ROMBEL</span>
            <div class="stat-icon blue">
                <i class="fa-solid fa-door-open"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalKelas, 0, ',', '.') }}</div>
        <div class="stat-footer">
            <span>{{ $totalMapel }} mata pelajaran aktif</span>
        </div>
    </div>

    <!-- Card 2: JURNAL HARI INI -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">JURNAL HARI INI</span>
            <div class="stat-icon green">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ $jurnalTerisiCount }}/{{ $totalJadwalHariIni }}</div>
        <div class="stat-footer">
            <span class="stat-badge-green">▲ {{ $pctKbmBerjalan }}% KBM terisi hari ini</span>
        </div>
    </div>

    <!-- Card 3: KEHADIRAN SISWA -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">KEHADIRAN SISWA</span>
            <div class="stat-icon orange">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ $pctHadir }}%</div>
        <div class="stat-footer">
            <span>{{ $hadirCount }} hadir dari {{ $totalPresensi }} sesi</span>
        </div>
    </div>

    <!-- Card 4: GURU MENGAJAR HARI INI -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">GURU MENGAJAR</span>
            <div class="stat-icon dark">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>
        <div class="stat-value">{{ $guruMengajarHariIniCount }}/{{ $guruTerjadwalHariIniCount }}</div>
        <div class="stat-footer">
            <span>terjadwal hari {{ $hariIni }}</span>
        </div>
    </div>

    <!-- Card 5: BEBAN KBM SEKOLAH -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">BEBAN KBM</span>
            <div class="stat-icon red">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalJpSekolah }} JP</div>
        <div class="stat-footer">
            <span class="stat-badge-green">{{ $guruMemenuhiBeban }} guru ≥ 24 JP</span>
        </div>
    </div>
</div>

<!-- Middle Row Charts Grid (1.6fr 1fr) -->
<div class="charts-grid">
    <!-- Tren Ketercapaian Jurnal Mengajar Line Chart -->
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title">Tren Ketercapaian Jurnal Mengajar — 7 Hari Terakhir</h3>
            <span style="font-size: 11.5px; font-weight: 700; color: #2b43b9; background: #eef2ff; padding: 4px 10px; border-radius: 8px;">
                <i class="fa-solid fa-chart-line" style="margin-right: 4px;"></i>Real-time KBM
            </span>
        </div>
        <div class="line-chart-container">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Komposisi Presensi Siswa Donut Chart -->
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title">Komposisi Presensi Siswa Hari Ini</h3>
        </div>
        <div class="donut-chart-container">
            <canvas id="donutChart"></canvas>
            <div class="donut-center-text">
                <div class="donut-center-pct">{{ $pctHadir }}%</div>
                <div class="donut-center-lbl">Kehadiran</div>
            </div>
        </div>
    </div>
</div>

<!-- Ketercapaian Jurnal per Tingkat Kelas Card -->
<div class="tingkat-card-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <h3 class="chart-card-title" style="display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-layer-group" style="color: #2b43b9;"></i>
            <span>Ketercapaian Jurnal Pembelajaran per Tingkat</span>
        </h3>
        <span style="font-size: 12px; color: #64748b; font-weight: 600;">Target jadwal hari {{ $hariIni }}</span>
    </div>

    <div class="tingkat-stats">
        @foreach($tingkatStats as $tk => $stat)
        <div class="tingkat-box">
            <div class="tingkat-box-label">TINGKAT {{ $tk }}</div>
            <div class="tingkat-box-val">{{ $stat['terisi'] }} <span style="font-size:14px;font-weight:600;color:#94a3b8;">/ {{ $stat['target'] }} Sesi</span></div>
            <div class="tingkat-box-sub">{{ $stat['pct'] }}% jurnal terisi hari ini</div>
            <div class="progress-bar-wrap">
                <div class="progress-bar" style="width: {{ $stat['pct'] }}%; background: {{ $stat['pct'] >= 80 ? '#10b981' : ($stat['pct'] >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Presensi Breakdown Pills --}}
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-top: 20px;">
        @foreach([
            ['Hadir', $hadirCount, '#10b981', '#ecfdf5'],
            ['Sakit', $sakitCount, '#f59e0b', '#fffbeb'],
            ['Izin', $izinCount, '#0284c7', '#f0f9ff'],
            ['Alpha', $alphaCount, '#ef4444', '#fef2f2'],
            ['Dispensasi', $dispCount, '#7c3aed', '#f5f3ff'],
        ] as $ps)
        <div style="text-align: center; background: {{ $ps[3] }}; border-radius: 12px; padding: 12px 8px; border: 1px solid rgba(0,0,0,0.04);">
            <div style="font-size: 20px; font-weight: 800; color: {{ $ps[2] }};">{{ $ps[1] }}</div>
            <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-top: 3px;">{{ $ps[0] }}</div>
        </div>
        @endforeach
    </div>
</div>

<!-- Bottom Row Widgets Grid (1fr 1fr) -->
<div class="widgets-grid">
    <!-- Perlu Perhatian Kurikulum -->
    <div class="widget-card">
        <h3 class="widget-title">
            <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i>
            <span>Perlu Perhatian Kurikulum</span>
        </h3>
        <div class="attention-list">
            @foreach($perluPerhatian as $item)
            <a href="{{ $item['url'] ?? route('waka-kurikulum.jurnal') }}" class="attention-item">
                <div class="attention-icon {{ $item['type'] ?? 'info' }}">
                    <i class="{{ $item['icon'] ?? 'fa-solid fa-bell' }}"></i>
                </div>
                <div class="attention-content">
                    <span class="attention-head">{{ $item['title'] }}</span>
                    <span class="attention-sub">{{ $item['subtitle'] }}</span>
                </div>
                <div style="color: #cbd5e1; font-size: 13px; align-self: center;">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Aktivitas Pembelajaran Terbaru -->
    <div class="widget-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="widget-title" style="margin-bottom: 0;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #2b43b9;"></i>
                <span>Aktivitas KBM Terbaru</span>
            </h3>
            <a href="{{ route('waka-kurikulum.jurnal') }}" style="font-size: 12.5px; color: #2b43b9; font-weight: 700; text-decoration: none;">
                Lihat Jurnal
            </a>
        </div>
        <div class="activity-list">
            @forelse($aktivitasTerbaru as $act)
            <div class="activity-item">
                <div class="activity-avatar" style="background: {{ $act['icon_bg'] ?? 'linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%)' }}; color: {{ $act['icon_color'] ?? '#3730a3' }};">
                    <i class="{{ $act['icon'] ?? 'fa-solid fa-bolt' }}"></i>
                </div>
                <div class="activity-content">
                    <span class="activity-desc">{{ $act['deskripsi'] }}</span>
                    <div class="activity-time">
                        <span><i class="fa-regular fa-clock" style="font-size: 10px;"></i> {{ $act['waktu'] }}</span>
                        <span>·</span>
                        <span class="activity-tag">{{ $act['tag'] }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div style="padding: 30px 12px; text-align: center; color: #94a3b8; font-size: 13px;">
                <i class="fa-solid fa-inbox" style="font-size: 26px; margin-bottom: 8px; display: block; color: #cbd5e1;"></i>
                Belum ada aktivitas pembelajaran baru hari ini.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Line Chart - Tren Ketercapaian Jurnal Mengajar 7 Hari
        const canvasTrend = document.getElementById('trendChart');
        if (canvasTrend) {
            const ctxTrend = canvasTrend.getContext('2d');
            
            const gradient = ctxTrend.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(43, 67, 185, 0.25)');
            gradient.addColorStop(1, 'rgba(43, 67, 185, 0.0)');

            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: {!! json_encode($trend7Hari['labels']) !!},
                    datasets: [{
                        label: 'Jurnal Terisi',
                        data: {!! json_encode($trend7Hari['data']) !!},
                        borderColor: '#2b43b9',
                        borderWidth: 3,
                        fill: true,
                        backgroundColor: gradient,
                        tension: 0.45,
                        pointBackgroundColor: '#2b43b9',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' jurnal KBM terisi';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { weight: '600', size: 11 } }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            ticks: { color: '#94a3b8', font: { weight: '600', size: 10 }, stepSize: 1 },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Donut Chart - Komposisi Presensi Siswa Hari Ini
        const canvasDonut = document.getElementById('donutChart');
        if (canvasDonut) {
            const ctxDonut = canvasDonut.getContext('2d');
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi'],
                    datasets: [{
                        data: [{{ $pctHadir }}, {{ $pctSakit }}, {{ $pctIzin }}, {{ $pctAlpha }}, {{ $pctDisp }}],
                        backgroundColor: [
                            '#2b43b9',
                            '#f59e0b',
                            '#0284c7',
                            '#ef4444',
                            '#7c3aed'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
