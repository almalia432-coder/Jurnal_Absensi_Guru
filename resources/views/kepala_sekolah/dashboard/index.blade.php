@extends('layouts.kepala_sekolah')

@section('title', 'Dashboard Monitoring - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Dashboard Monitoring')
@section('header_subtitle', 'Pantau kehadiran guru izin & dispensasi siswa SMKN 1 BOYOLANGU')

@section('styles')
<style>
    /* ── Section Greeting ── */
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

    /* ── Stats Grid (Admin 1:1) ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .stat-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 22px;
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
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #707e94;
        letter-spacing: 0.5px;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .stat-icon.blue   { background-color: #eef2ff; color: #2b43b9; }
    .stat-icon.orange { background-color: #fff7ed; color: #f97316; }
    .stat-icon.green  { background-color: #e6f9f0; color: #10b981; }
    .stat-icon.red    { background-color: #fef2f2; color: #ef4444; }

    .stat-value {
        font-size: 30px;
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
        gap: 6px;
    }
    .stat-badge-green { color: #10b981; font-weight: 700; }
    .stat-badge-orange { color: #f97316; font-weight: 700; }

    /* ── Middle Row Charts Grid (Admin Style) ── */
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
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        background: #eef2ff;
        color: #2b43b9;
    }

    /* ── Monitoring Section Grid ── */
    .monitoring-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .list-item-person {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #eef2f7;
        margin-bottom: 10px;
        transition: all 0.15s ease;
    }
    .list-item-person:last-child { margin-bottom: 0; }
    .list-item-person:hover { background: #f1f5f9; }

    .person-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        flex-shrink: 0;
    }
    .avatar-blue   { background: #e0e7ff; color: #2b43b9; }
    .avatar-green  { background: #d1fae5; color: #065f46; }
    .avatar-purple { background: #ede9fe; color: #6b21a8; }
    .avatar-orange { background: #ffedd5; color: #c2410c; }

    .person-details { flex: 1; min-width: 0; }
    .person-name { font-weight: 700; font-size: 13.5px; color: #1b2559; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .person-sub  { font-size: 12px; color: #707e94; margin-top: 2px; }

    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        white-space: nowrap;
    }
    .status-badge.disetujui { background: #d1fae5; color: #065f46; }
    .status-badge.menunggu  { background: #fef3c7; color: #92400e; }
    .status-badge.ditolak   { background: #fee2e2; color: #b91c1c; }
    .status-badge.selesai   { background: #e0e7ff; color: #3730a3; }

    .empty-placeholder {
        padding: 36px 16px;
        text-align: center;
        color: #94a3b8;
    }
    .empty-placeholder i { font-size: 32px; margin-bottom: 8px; }
    .empty-placeholder p { font-size: 13px; font-weight: 600; }

    .btn-view-all {
        font-size: 12.5px;
        font-weight: 700;
        color: #2b43b9;
        text-decoration: none;
        padding: 6px 14px;
        border-radius: 8px;
        background: #eef2ff;
        transition: all 0.2s ease;
    }
    .btn-view-all:hover { background: #e0e7ff; }

    /* Legend Donut */
    .donut-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
    .donut-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12.5px;
        font-weight: 600;
        color: #475569;
    }
    .donut-list .dot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; margin-right: 8px; }
    .donut-list .num { font-weight: 800; color: #1b2559; }

    @media (max-width: 991px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-grid, .monitoring-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<!-- Greeting Section (Admin Identical) -->
<div class="greeting-section">
    <div class="greeting-title">
        Selamat Datang, Bapak/Ibu Kepala Sekolah 👋
    </div>
    <div class="greeting-subtitle">
        {{ $today->locale('id')->isoFormat('dddd, D MMMM YYYY') }} — Monitoring operasional perizinan guru dan dispensasi siswa SMKN 1 Boyolangu.
    </div>
</div>

<!-- 4 Stat Cards Grid (Admin 1:1) -->
<div class="stats-grid">
    <!-- Guru Izin Hari Ini -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Guru Izin Hari Ini</span>
            <div class="stat-icon blue">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $izinHariIni }}</div>
        <div class="stat-footer">
            @if($izinHariIni > 0)
                <span class="stat-badge-orange"><i class="fa-solid fa-triangle-exclamation"></i> {{ $izinHariIni }} guru izin/cuti</span>
            @else
                <span class="stat-badge-green"><i class="fa-solid fa-circle-check"></i> Semua guru hadir</span>
            @endif
        </div>
    </div>

    <!-- Izin Guru Menunggu -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Izin Perlu Verifikasi</span>
            <div class="stat-icon orange">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>
        <div class="stat-value">{{ $izinMenunggu }}</div>
        <div class="stat-footer">
            <span><i class="fa-solid fa-calendar-week"></i> {{ $izinMingguan }} total izin pekan ini</span>
        </div>
    </div>

    <!-- Siswa Dispensasi Hari Ini -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Dispensasi Hari Ini</span>
            <div class="stat-icon green">
                <i class="fa-solid fa-person-walking-arrow-right"></i>
            </div>
        </div>
        <div class="stat-value">{{ $dispHariIni }}</div>
        <div class="stat-footer">
            <span><i class="fa-solid fa-calendar-check"></i> {{ $dispMingguan }} dispensasi pekan ini</span>
        </div>
    </div>

    <!-- Dispensasi Menunggu -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Dispensasi Menunggu</span>
            <div class="stat-icon red">
                <i class="fa-solid fa-clipboard-question"></i>
            </div>
        </div>
        <div class="stat-value">{{ $dispMenunggu }}</div>
        <div class="stat-footer">
            <span><i class="fa-solid fa-calendar-days"></i> {{ $dispBulanIni }} total bulan ini</span>
        </div>
    </div>
</div>

<!-- Charts Grid (Admin Identical) -->
<div class="charts-grid">
    <!-- Tren 7 Hari Terakhir -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="chart-card-title">
                <i class="fa-solid fa-chart-line" style="color: #2b43b9;"></i>
                Tren Aktivitas 7 Hari Terakhir
            </div>
            <span class="chart-badge">Izin vs Dispensasi</span>
        </div>
        <canvas id="trendChart" height="150"></canvas>
    </div>

    <!-- Donut Jenis Izin Bulan Ini -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="chart-card-title">
                <i class="fa-solid fa-chart-pie" style="color: #6b21a8;"></i>
                Komposisi Izin Guru
            </div>
            <span class="chart-badge">Bulan Ini</span>
        </div>
        @if(!empty($izinByType))
            <div style="display:flex; align-items:center; gap:20px;">
                <div style="width:125px; height:125px; flex-shrink:0;">
                    <canvas id="donutChart"></canvas>
                </div>
                <ul class="donut-list" style="flex:1;">
                    @php
                        $colors = ['#2b43b9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                        $i = 0;
                    @endphp
                    @foreach($izinByType as $jenis => $total)
                    <li>
                        <span>
                            <span class="dot" style="background:{{ $colors[$i % count($colors)] }};"></span>
                            {{ str_replace('_', ' ', $jenis) }}
                        </span>
                        <span class="num">{{ $total }}</span>
                    </li>
                    @php $i++; @endphp
                    @endforeach
                </ul>
            </div>
        @else
            <div class="empty-placeholder">
                <i class="fa-solid fa-chart-pie"></i>
                <p>Belum ada data izin bulan ini</p>
            </div>
        @endif
    </div>
</div>

<!-- Live Monitoring Grid (Admin Identical Cards) -->
<div class="monitoring-grid">
    <!-- Guru Izin Hari Ini -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="chart-card-title">
                <i class="fa-solid fa-chalkboard-user" style="color: #2b43b9;"></i>
                Guru Izin Hari Ini
            </div>
            <a href="{{ route('kepala-sekolah.izin-guru') }}" class="btn-view-all">Lihat Lengkap →</a>
        </div>

        @if($guruIzinHariIni->isEmpty())
            <div class="empty-placeholder">
                <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                <p>Seluruh guru hadir bertugas hari ini.</p>
            </div>
        @else
            @foreach($guruIzinHariIni as $item)
            @php
                $nama = $item->guru->nama_lengkap ?? 'Guru';
                $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $nama)));
                $init = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : strtoupper(substr($nama,0,2));
            @endphp
            <div class="list-item-person">
                <div class="person-avatar avatar-blue">{{ $init }}</div>
                <div class="person-details">
                    <div class="person-name">{{ $nama }}</div>
                    <div class="person-sub">
                        {{ str_replace('_', ' ', $item->jenis_izin) }} • {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m') }}
                        @if($item->tanggal_mulai != $item->tanggal_selesai)
                            – {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m') }}
                        @endif
                    </div>
                </div>
                <span class="status-badge {{ strtolower($item->status) }}">{{ $item->status }}</span>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Siswa Dispensasi Hari Ini -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="chart-card-title">
                <i class="fa-solid fa-users-line" style="color: #10b981;"></i>
                Siswa Dispensasi Hari Ini
            </div>
            <a href="{{ route('kepala-sekolah.dispensasi') }}" class="btn-view-all">Lihat Lengkap →</a>
        </div>

        @if($siswaDispHariIni->isEmpty())
            <div class="empty-placeholder">
                <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                <p>Tidak ada siswa dengan dispensasi keluar hari ini.</p>
            </div>
        @else
            @foreach($siswaDispHariIni as $item)
            @php
                $nama = $item->siswa->nama_lengkap ?? 'Siswa';
                $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $nama)));
                $init = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : strtoupper(substr($nama,0,2));
                $kelas = optional($item->siswa->kelas)->nama_kelas ?? '—';
            @endphp
            <div class="list-item-person">
                <div class="person-avatar avatar-green">{{ $init }}</div>
                <div class="person-details">
                    <div class="person-name">{{ $nama }}</div>
                    <div class="person-sub">
                        {{ $kelas }} • Pkl {{ \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') }}
                        @if($item->jam_kembali) – {{ \Carbon\Carbon::parse($item->jam_kembali)->format('H:i') }} @endif
                    </div>
                </div>
                <span class="status-badge {{ strtolower($item->status) }}">{{ $item->status }}</span>
            </div>
            @endforeach
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
// Line Chart Trend
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($trendLabels) !!},
        datasets: [
            {
                label: 'Guru Izin',
                data: {!! json_encode($trendIzin) !!},
                borderColor: '#2b43b9',
                backgroundColor: 'rgba(43, 67, 185, 0.08)',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#2b43b9',
                pointRadius: 4,
            },
            {
                label: 'Siswa Dispensasi',
                data: {!! json_encode($trendDisp) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.06)',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#10b981',
                pointRadius: 4,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { font: { family: 'Plus Jakarta Sans', weight: '700', size: 12 }, boxWidth: 12, padding: 14 }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#94a3b8' } },
            y: { grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#94a3b8', stepSize: 1 }, beginAtZero: true }
        }
    }
});

// Donut Chart
@if(!empty($izinByType))
const donutCtx = document.getElementById('donutChart').getContext('2d');
new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($izinByType)) !!},
        datasets: [{
            data: {!! json_encode(array_values($izinByType)) !!},
            backgroundColor: ['#2b43b9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: { legend: { display: false } }
    }
});
@endif
</script>
@endsection
