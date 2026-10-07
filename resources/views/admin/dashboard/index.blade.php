@extends('layouts.admin')

@section('title', 'Dashboard - Jurnal Absensi SMKN 1 BOYOLANGU')

@section('styles')
<style>
    /* Section Greeting */
    .greeting-section {
        margin-bottom: 22px;
    }

    .greeting-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
        letter-spacing: -0.3px;
    }

    .greeting-subtitle {
        font-size: 13.5px;
        color: #64748b;
        font-weight: 500;
    }

    /* Top 5 Stat Cards Grid (Image 2 style) */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
        border: 1px solid #e5e9f2;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        line-height: 1.35;
    }

    .stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .stat-icon.blue {
        background-color: #e0e7ff;
        color: #2563eb;
        border: 1px solid #c7d2fe;
    }

    .stat-icon.green {
        background-color: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .stat-icon.orange {
        background-color: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .stat-icon.red {
        background-color: #ffe4e6;
        color: #e11d48;
        border: 1px solid #fecdd3;
    }

    .stat-icon.dark {
        background-color: #ede9fe;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
    }

    .stat-value {
        font-size: 36px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin: 12px 0 6px 0;
        letter-spacing: -0.5px;
    }

    .stat-footer {
        font-size: 11.5px;
        color: #94a3b8;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .stat-badge-green {
        color: #059669;
        font-weight: 700;
    }

    .stat-badge-red {
        color: #e11d48;
        font-weight: 700;
    }

    /* Middle Row Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 18px;
        margin-bottom: 24px;
    }

    .chart-card {
        background-color: #ffffff;
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        border: 1px solid #e5e9f2;
    }

    .chart-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .chart-card-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.2px;
    }

    .class-select {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        outline: none;
        cursor: pointer;
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
        color: #0f172a;
        line-height: 1;
    }

    .donut-center-lbl {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
    }

    /* Bottom Row Widgets */
    .widgets-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .widget-card {
        background-color: #ffffff;
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        border: 1px solid #e5e9f2;
    }

    .widget-title {
        font-size: 16.5px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.2px;
    }

    .attention-list, .activity-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .attention-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 14px;
        border-radius: 12px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
        background: #ffffff;
    }

    .attention-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
        transform: translateX(4px);
    }

    .attention-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .attention-icon.danger {
        background: #ffe4e6;
        color: #e11d48;
        border: 1px solid #fecdd3;
    }

    .attention-icon.warning {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .attention-icon.info {
        background: #e0e7ff;
        color: #2563eb;
        border: 1px solid #c7d2fe;
    }

    .attention-icon.success {
        background: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
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
        border-radius: 10px;
        background: #e0e7ff;
        color: #2563eb;
        border: 1px solid #c7d2fe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
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

    /* Responsive Media Queries */
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
            font-size: 24px;
            margin: 8px 0 4px 0;
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
        .chart-card, .widget-card {
            padding: 16px;
            border-radius: 14px;
        }
        .chart-card-header {
            margin-bottom: 14px;
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
            font-size: 22px;
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
    <h2 class="greeting-title">Selamat pagi, Admin</h2>
    <p class="greeting-subtitle">Berikut ringkasan aktivitas absensi & jurnal hari ini — {{ $dateFormatted }}</p>
</div>

<!-- 5 Stat Cards Grid -->
<div class="stats-grid">
    <!-- Card 1: TOTAL SISWA -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">TOTAL SISWA</span>
            <div class="stat-icon blue">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalSiswa, 0, ',', '.') }}</div>
        <div class="stat-footer">
            <span>{{ $totalRombel }} rombel aktif</span>
        </div>
    </div>

    <!-- Card 2: HADIR HARI INI -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">HADIR HARI INI</span>
            <div class="stat-icon green">
                <i class="fa-solid fa-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($hadirHariIni, 0, ',', '.') }}</div>
        <div class="stat-footer">
            <span class="stat-badge-green">▲ {{ $pctHadir }}% dari total siswa</span>
        </div>
    </div>

    <!-- Card 3: Izin / Sakit -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">IZIN / SAKIT</span>
            <div class="stat-icon orange">
                <i class="fa-regular fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $izinSakitHariIni }}</div>
        <div class="stat-footer">
            <span>{{ $pctIzinSakit }}% dari total siswa</span>
        </div>
    </div>

    <!-- Card 4: Alpa Hari Ini -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">ALPA HARI INI</span>
            <div class="stat-icon red">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
        <div class="stat-value">{{ $alpaHariIni }}</div>
        <div class="stat-footer">
            <span class="stat-badge-red">▲ naik dari kemarin ({{ $alpaKemarin }})</span>
        </div>
    </div>

    <!-- Card 5: Jurnal Terisi -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">JURNAL TERISI</span>
            <div class="stat-icon dark">
                <i class="fa-solid fa-bookmark"></i>
            </div>
        </div>
        <div class="stat-value">{{ $jurnalTerisiCount }}/{{ $jurnalTargetCount }}</div>
        <div class="stat-footer">
            <span>jam pelajaran hari ini</span>
        </div>
    </div>
</div>

<!-- Middle Row Charts -->
<div class="charts-grid">
    <!-- Tren Kehadiran Siswa Line Chart -->
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title">Tren Kehadiran Siswa — 7 Hari Terakhir</h3>
            <select class="class-select" id="selectJurusanTrend" onchange="updateTrendChart(this.value)">
                <option value="all">Semua Jurusan</option>
                @foreach($jurusanList as $jur)
                    <option value="{{ $jur->kode_jurusan }}">{{ $jur->kode_jurusan }} ({{ $jur->nama_jurusan }})</option>
                @endforeach
            </select>
        </div>
        <div class="line-chart-container">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Komposisi Hari Ini Donut Chart -->
    <div class="chart-card">
        <div class="chart-card-header">
            <h3 class="chart-card-title">Komposisi Hari Ini</h3>
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

<!-- Bottom Row Widgets -->
<div class="widgets-grid">
    <!-- Perlu Perhatian -->
    <div class="widget-card">
        <h3 class="widget-title">
            <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i>
            <span>Perlu Perhatian</span>
        </h3>
        <div class="attention-list">
            @foreach($perluPerhatian as $item)
            <a href="{{ $item['url'] ?? url('/admin/absensi') }}" class="attention-item" style="text-decoration: none;">
                <div class="attention-icon {{ $item['type'] ?? 'info' }}">
                    <i class="{{ $item['icon'] ?? 'fa-solid fa-bell' }}"></i>
                </div>
                <div class="attention-content">
                    <span class="attention-head">{{ $item['title'] }}</span>
                    <span class="attention-sub">{{ $item['subtitle'] }}</span>
                </div>
                <div style="color: #cbd5e1; font-size: 12px; align-self: center;">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="widget-card">
        <h3 class="widget-title">
            <i class="fa-solid fa-clock-rotate-left" style="color: #2b43b9;"></i>
            <span>Aktivitas Terbaru</span>
        </h3>
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
            <div style="padding: 24px 12px; text-align: center; color: #94a3b8; font-size: 13px;">
                <i class="fa-solid fa-inbox" style="font-size: 24px; margin-bottom: 6px; display: block;"></i>
                Belum ada catatan aktivitas baru.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Line Chart - Tren Kehadiran Siswa
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        
        const gradient = ctxTrend.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(43, 67, 185, 0.25)');
        gradient.addColorStop(1, 'rgba(43, 67, 185, 0.0)');

        window.trendDatasets = {!! json_encode($trendDatasets) !!};

        window.trendChartInstance = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {!! json_encode($trend7Hari['labels']) !!},
                datasets: [{
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
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { weight: '600', size: 11 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#94a3b8', font: { weight: '600', size: 10 } },
                        beginAtZero: true
                    }
                }
            }
        });

        // Interaktif: Update data chart saat jurusan dipilih
        window.updateTrendChart = function (kodeJurusan) {
            if (window.trendChartInstance && window.trendDatasets) {
                const newData = window.trendDatasets[kodeJurusan] || window.trendDatasets['all'];
                window.trendChartInstance.data.datasets[0].data = newData;
                window.trendChartInstance.update();
            }
        };

        // Donut Chart - Komposisi Hari Ini
        const ctxDonut = document.getElementById('donutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin/Sakit', 'Alpa'],
                datasets: [{
                    data: [{{ $pctHadir }}, {{ $pctIzinSakit }}, {{ $pctAlpa }}],
                    backgroundColor: [
                        '#1b2559',
                        '#10b981',
                        '#ef4444'
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
                    tooltip: { enabled: true }
                }
            }
        });
    });
</script>
@endsection
