@extends('layouts.guru_piket')

@section('title', 'Dashboard - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Dashboard Guru Piket')
@section('header_subtitle', 'Pantau kelancaran KBM, izin guru, dispensasi siswa, dan kelas secara real-time')

@section('header_extra')
    <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 14px; padding: 8px 16px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-user-shield" style="font-size: 20px; color: #a5b4fc;"></i>
        <div>
            <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; font-weight: 700;">Pos Meja Piket</div>
            <div style="font-size: 13.5px; font-weight: 800; color: #ffffff;">Petugas Piket SMKN 1 Boyolangu</div>
        </div>
    </div>
@endsection

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
        font-size: 12px;
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
        font-size: 18px;
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
        font-size: 28px;
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

    /* Action Buttons */
    .btn-action-primary {
        background: #2b43b9;
        color: white;
        padding: 9px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(43, 67, 185, 0.2);
    }
    .btn-action-primary:hover {
        background: #1e35a0;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(43, 67, 185, 0.3);
    }

    .btn-action-secondary {
        background: white;
        color: #1b2559;
        padding: 9px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        border: 1.5px solid #e2e8f0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-action-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
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

    .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: #1b2559;
    }

    /* Monitoring Grid */
    .monitoring-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 14px;
    }
    .class-status-card {
        border-radius: 14px;
        padding: 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .class-status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .class-name { font-size: 14.5px; font-weight: 800; color: #1b2559; }
    .class-jurusan { font-size: 11px; color: #6b7a99; font-weight: 600; }
    
    .status-badge {
        font-size: 10.5px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-badge.success { background: #e6f9f0; color: #10b981; }
    .status-badge.warning { background: #fff7ed; color: #f97316; }
    .status-badge.danger  { background: #fef2f2; color: #ef4444; }
    .status-badge.secondary { background: #f1f5f9; color: #64748b; }
    .status-badge.light   { background: #ffffff; color: #94a3b8; border: 1px solid #e2e8f0; }

    /* Custom Table Styling */
    .table-responsive { overflow-x: auto; }
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }
    .custom-table th {
        background-color: #f8fafc;
        padding: 12px 14px;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        color: #707e94;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .custom-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f4f7fe;
        color: #2b3674;
        vertical-align: middle;
    }
    .custom-table tr:hover td { background-color: #f8fafc; }

    /* Two Columns Grid */
    .two-cols-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    /* Modal Styling */
    .custom-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px 16px;
        overflow-y: auto;
    }
    .custom-modal-backdrop.show { display: flex; }
    .custom-modal-card {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 560px;
        max-height: calc(100vh - 40px);
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        margin: auto;
    }
    .custom-modal-card form {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
    .modal-head {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        flex-shrink: 0;
    }
    .modal-head h3 { font-size: 16px; font-weight: 800; color: #1b2559; display: flex; align-items: center; gap: 8px; }
    .modal-close-btn { background: none; border: none; font-size: 22px; color: #94a3b8; cursor: pointer; padding: 2px 6px; line-height: 1; border-radius: 6px; transition: all 0.15s ease; }
    .modal-close-btn:hover { color: #1e293b; background: #e2e8f0; }
    .modal-body {
        padding: 20px 24px;
        overflow-y: auto;
        flex: 1;
    }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 12.5px; font-weight: 700; color: #2b3674; margin-bottom: 6px; }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        font-size: 13.5px;
        font-family: inherit;
        color: #1b2559;
        outline: none;
        transition: border-color 0.2s ease;
    }
    .form-control:focus { border-color: #2b43b9; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .modal-footer {
        padding: 14px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
        flex-shrink: 0;
    }

    .dispensasi-siswa-item:hover {
        background-color: #f1f5f9 !important;
    }
    .dispensasi-siswa-item:hover .siswa-name {
        color: #2b43b9 !important;
    }
    .dispensasi-siswa-item:hover i.fa-chevron-right {
        color: #2b43b9 !important;
        transform: translateX(3px);
    }

    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); }
        .two-cols-grid, .charts-grid { grid-template-columns: 1fr; }
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
        .two-cols-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .form-row {
            grid-template-columns: 1fr;
        }
        .btn-action-primary {
            width: 100%;
            justify-content: center;
        }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<!-- 1. Greeting Section & Controls (Matches Admin Dashboard) -->
<div class="greeting-section" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 class="greeting-title">Halo, Bapak/Ibu Guru Petugas Piket 👋</h2>
        <p class="greeting-subtitle">Berikut adalah ringkasan operasional KBM, kehadiran guru & dispensasi siswa hari ini ({{ $todayFormatted }}).</p>
    </div>

    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <form method="GET" action="{{ route('guru-piket.dashboard') }}" style="display: flex; align-items: center; background: white; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 6px 12px; gap: 8px;">
            <i class="fa-regular fa-calendar" style="color: #2b43b9;"></i>
            <input type="date" name="tanggal" value="{{ $today }}" onchange="this.form.submit()" style="border: none; outline: none; font-size: 13px; font-weight: 700; color: #1b2559; background: transparent; cursor: pointer;">
        </form>

        <button type="button" class="btn-action-primary" onclick="openDispensasiModal()">
            <i class="fa-solid fa-plus"></i> Dispensasi Siswa
        </button>

        <button type="button" class="btn-action-secondary" onclick="openLaporanModal()">
            <i class="fa-solid fa-pen-to-square"></i> Catatan Piket
        </button>

        <a href="{{ route('guru-piket.laporan.cetak', ['tanggal' => $today]) }}" target="_blank" class="btn-action-secondary">
            <i class="fa-solid fa-print"></i> Cetak Laporan Piket
        </a>
    </div>
</div>

<!-- Widget Kontrol Status KBM (Upacara / Pembiasaan) -->
@php
    $hariLower = strtolower($hariIni);
    $isSenin = $hariLower === 'senin';
    $isJumat = $hariLower === 'jumat';
@endphp

@if($isSenin || $isJumat)
<div style="background: {{ $isMaju ? 'linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)' : 'linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%)' }}; border: 1.5px solid {{ $isMaju ? '#fde68a' : '#bbf7d0' }}; border-radius: 16px; padding: 18px 22px; margin-bottom: 24px; box-shadow: 0 4px 14px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 14px; background: {{ $isMaju ? '#fef08a' : '#dcfce7' }}; color: {{ $isMaju ? '#b45309' : '#15803d' }}; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
            <i class="fa-solid {{ $isMaju ? 'fa-forward-fast' : ($isSenin ? 'fa-flag' : 'fa-hands-praying') }}"></i>
        </div>
        <div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 4px;">
                <span style="font-size: 15px; font-weight: 800; color: #1b2559;">
                    {{ $isSenin ? 'Status Upacara Hari Senin' : 'Status Pembiasaan Hari Jumat' }}
                </span>
                @if($isMaju)
                    <span style="background: #fef08a; color: #854d0e; font-size: 11.5px; font-weight: 800; padding: 3px 10px; border-radius: 8px; border: 1px solid #facc15; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-clock"></i> KBM Maju ke 07:00 ({{ $isSenin ? 'Upacara Ditiadakan' : 'Pembiasaan Ditiadakan' }})
                    </span>
                @else
                    <span style="background: #dcfce7; color: #166534; font-size: 11.5px; font-weight: 800; padding: 3px 10px; border-radius: 8px; border: 1px solid #86efac; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-check-circle"></i> Normal (KBM Mulai {{ $isSenin ? '07:40' : '07:30' }})
                    </span>
                @endif
            </div>
            <p style="font-size: 13px; color: #475569; margin: 0;">
                @if($isMaju)
                    <i class="fa-solid fa-circle-info" style="color: #b45309;"></i> 
                    <strong>Mapel Jam ke-2 maju ke 07:00</strong>. Jam istirahat tetap sama ({{ $isSenin ? '09:40 & 11:55' : '09:30 & 11:45' }}). Jadwal di Guru Mapel otomatis disesuaikan.
                @else
                    <i class="fa-solid fa-circle-info" style="color: #166534;"></i> 
                    KBM dimulai setelah {{ $isSenin ? 'Upacara Bendera (07:40 WIB)' : 'Pembiasaan (07:30 WIB)' }}. Jam istirahat tetap sama ({{ $isSenin ? '09:40' : '09:30' }}).
                @endif
                @if(!empty($statusKbmHariIni->catatan))
                    <span style="color: #64748b; font-style: italic;">— Catatan: "{{ $statusKbmHariIni->catatan }}"</span>
                @endif
            </p>
        </div>
    </div>

    <div>
        <form method="POST" action="{{ route('guru-piket.status-kbm.toggle') }}" style="display: inline-flex; align-items: center; gap: 10px;">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $today }}">
            @if($isSenin)
                <input type="hidden" name="ada_upacara" value="{{ $statusKbmHariIni->ada_upacara ? '0' : '1' }}">
            @else
                <input type="hidden" name="ada_pembiasaan_jumat" value="{{ $statusKbmHariIni->ada_pembiasaan_jumat ? '0' : '1' }}">
            @endif

            @if(!$isMaju)
                <button type="submit" class="btn-action-secondary" style="background: #ffffff; border-color: #f59e0b; color: #b45309; font-weight: 700; gap: 8px; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.15);" onclick="return confirm('Apakah Anda yakin ingin menandai {{ $isSenin ? 'Upacara' : 'Pembiasaan' }} ditiadakan hari ini? Jam KBM di seluruh guru mapel akan otomatis maju ke 07:00.')">
                    <i class="fa-solid fa-forward"></i> Tandai {{ $isSenin ? 'Upacara' : 'Pembiasaan' }} Ditiadakan (KBM Maju)
                </button>
            @else
                <button type="submit" class="btn-action-secondary" style="background: #ffffff; border-color: #10b981; color: #047857; font-weight: 700; gap: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15);" onclick="return confirm('Kembalikan status KBM hari ini ke Normal?')">
                    <i class="fa-solid fa-rotate-left"></i> Kembalikan ke Normal
                </button>
            @endif
        </form>
    </div>
</div>
@endif

<!-- Widget Roster Petugas Piket KBM Hari Ini -->
@if(isset($rosterToday))
<div style="background: #ffffff; border-radius: 18px; border: 1.5px solid #e2e8f0; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: #eef2ff; color: #2b43b9; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #1b2559;">Petugas Piket KBM Hari Ini</h3>
                    <span style="background: {{ $rosterToday['siklus'] === 'A' ? '#dbeafe' : '#fef3c7' }}; color: {{ $rosterToday['siklus'] === 'A' ? '#1e40af' : '#92400e' }}; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                        Siklus {{ $rosterToday['siklus'] }} (Minggu {{ $rosterToday['siklus'] === 'A' ? 'Ganjil' : 'Genap' }})
                    </span>
                </div>
                <span style="font-size: 12.5px; color: #64748b;">{{ $todayFormatted }} &bull; Roster penugasan resmi semester ganjil</span>
            </div>
        </div>

        <a href="{{ route('guru-piket.jadwal') }}" class="btn-action-secondary" style="font-size: 12px; padding: 6px 14px; text-decoration: none;">
            <i class="fa-solid fa-calendar-days"></i> Lihat Jadwal Lengkap 1 Semester &rarr;
        </a>
    </div>

    @if($rosterToday['is_libur'])
        <div style="background: #f8fafc; border-radius: 12px; padding: 14px; text-align: center; color: #64748b; font-size: 13px;">
            <i class="fa-solid fa-calendar-xmark" style="color: #94a3b8; margin-right: 6px;"></i>
            Hari Libur Akhir Pekan ({{ $rosterToday['hari'] }}). Tidak ada jadwal piket KBM aktif hari ini.
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;">
            <!-- Kolom Waka -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 14px 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #166534; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-user-shield"></i> Piket Waka
                    </span>
                    <span style="background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 6px;">Seharian</span>
                </div>
                <strong style="font-size: 13.5px; color: #1b2559; display: block; margin-bottom: 2px;">
                    {{ $rosterToday['waka']['nama'] ?? 'Belum Ditentukan' }}
                </strong>
                <span style="font-size: 11px; color: #475569;">Penanggung jawab pimpinan</span>
            </div>

            <!-- Kolom Shift Pagi -->
            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; padding: 14px 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #92400e; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-sun" style="color: #f59e0b;"></i> Shift Pagi
                    </span>
                    <span style="background: #fef3c7; color: #b45309; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 6px;">07.00 - 11.00</span>
                </div>
                <div style="font-size: 12.5px; margin-bottom: 4px;">
                    <span style="color: #78350f; font-weight: 700;">Koord:</span>
                    <strong style="color: #1b2559;">{{ $rosterToday['pagi_koordinator']->nama_guru ?? '-' }}</strong>
                </div>
                <div style="font-size: 11.5px; color: #475569; line-height: 1.4;">
                    <span style="color: #78350f; font-weight: 600;">Petugas:</span>
                    {{ $rosterToday['pagi_petugas']->pluck('nama_guru')->implode(', ') }}
                </div>
            </div>

            <!-- Kolom Shift Siang -->
            <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 14px; padding: 14px 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #3730a3; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-cloud-sun" style="color: #3b82f6;"></i> Shift Siang
                    </span>
                    <span style="background: #e0e7ff; color: #3730a3; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 6px;">11.00 - 15.00</span>
                </div>
                <div style="font-size: 12.5px; margin-bottom: 4px;">
                    <span style="color: #3730a3; font-weight: 700;">Koord:</span>
                    <strong style="color: #1b2559;">{{ $rosterToday['siang_koordinator']->nama_guru ?? '-' }}</strong>
                </div>
                <div style="font-size: 11.5px; color: #475569; line-height: 1.4;">
                    <span style="color: #3730a3; font-weight: 600;">Petugas:</span>
                    {{ $rosterToday['siang_petugas']->pluck('nama_guru')->implode(', ') }}
                </div>
            </div>
        </div>
    @endif
</div>
@endif

<!-- 2. Top 5 Stat Cards Grid (Matches Admin Dashboard) -->
<div class="stats-grid">
    <!-- Card 1: Kelas Berjalan -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Kelas Berjalan</span>
            <div class="stat-icon blue"><i class="fa-solid fa-chalkboard-user"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $totalKelasTerisi }} <span style="font-size: 15px; color: #6b7a99; font-weight: 600;">/ {{ $totalKelasTerjadwal }}</span></div>
            <div class="stat-footer">
                @if($totalKelasKosong > 0)
                    <span class="stat-badge-red">{{ $totalKelasKosong }} kelas</span> belum ada jurnal
                @else
                    <span class="stat-badge-green">100%</span> kelas aktif terisi
                @endif
            </div>
        </div>
    </div>

    <!-- Card 2: Kehadiran Guru -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Guru Hadir</span>
            <div class="stat-icon green"><i class="fa-solid fa-user-check"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $guruHadirCount }} <span style="font-size: 14px; color: #6b7a99; font-weight: 600;">Hadir</span></div>
            <div class="stat-footer">
                @if($guruIzinCount > 0)
                    <span class="stat-badge-red">{{ $guruIzinCount }} Guru</span> Izin / Sakit
                @else
                    <span class="stat-badge-green">Seluruh Guru</span> bertugas
                @endif
            </div>
        </div>
    </div>

    <!-- Card 3: Dispensasi Siswa -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Dispensasi Siswa</span>
            <div class="stat-icon orange"><i class="fa-solid fa-ticket-simple"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $totalDispensasi }} <span style="font-size: 14px; color: #6b7a99; font-weight: 600;">Izin Keluar</span></div>
            <div class="stat-footer">
                @if($dispensasiPending > 0)
                    <span class="stat-badge-red">{{ $dispensasiPending }} Menunggu</span> persetujuan
                @else
                    <span class="stat-badge-green">{{ $dispensasiAktif }} Tercatat</span> hari ini
                @endif
            </div>
        </div>
    </div>

    <!-- Card 4: Siswa Tidak Masuk -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Siswa Tidak Masuk</span>
            <div class="stat-icon red"><i class="fa-solid fa-user-xmark"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $totalTidakMasuk }} <span style="font-size: 14px; color: #6b7a99; font-weight: 600;">Siswa</span></div>
            <div class="stat-footer">
                <span class="stat-badge-red">{{ $alphaCount }} Alpha</span>, {{ $sakitCount }} Sakit, {{ $izinCount }} Izin
            </div>
        </div>
    </div>

    <!-- Card 5: Tingkat Presensi Sekolah -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Tingkat Presensi</span>
            <div class="stat-icon dark"><i class="fa-solid fa-chart-pie"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $pctHadirSekolah }}%</div>
            <div class="stat-footer">
                <span class="stat-badge-green">{{ $hadirCount }} Siswa</span> Hadir Hari Ini
            </div>
        </div>
    </div>
</div>

<!-- 3. Middle Row: Live Monitoring Kelas & Presensi Charts -->
<div class="charts-grid">
    <!-- Chart 1: Distribusi Siswa Absen per Jurusan -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <h3 class="chart-title">Distribusi Siswa Absen per Jurusan</h3>
                <span style="font-size: 12px; color: #6b7a99;">Jumlah siswa Sakit, Izin, Alpha, & Dispensasi hari ini</span>
            </div>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="jurusanChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Komposisi Presensi Siswa -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <h3 class="chart-title">Komposisi Presensi Hari Ini</h3>
                <span style="font-size: 12px; color: #6b7a99;">Proporsi status kehadiran seluruh siswa</span>
            </div>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="presensiPieChart"></canvas>
        </div>
    </div>
</div>

<!-- 4. Matriks Pemantauan Kelas Hari Ini -->
<div class="chart-card" style="margin-bottom: 24px;">
    <div class="chart-card-header">
        <div>
            <h3 class="chart-title">Matriks Pemantauan Kelas Hari Ini</h3>
            <span style="font-size: 12px; color: #6b7a99;">Status pembelajaran real-time pada jam dinas aktif</span>
        </div>
        <a href="{{ route('guru-piket.monitoring', ['tanggal' => $today]) }}" style="font-size: 13px; font-weight: 700; color: #2b43b9; text-decoration: none;">
            Lihat Semua Kelas <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="monitoring-grid">
        @forelse($kelasMonitoring->take(12) as $km)
        <div class="class-status-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="class-name">{{ $km->nama_kelas }}</div>
                    <div class="class-jurusan">{{ $km->jurusan ?? 'Umum' }}</div>
                </div>
                <span class="status-badge {{ $km->badge }}">{{ $km->status }}</span>
            </div>
            <div style="font-size: 12px; color: #2b3674; display: flex; flex-direction: column; gap: 3px; border-top: 1px dashed #e2e8f0; padding-top: 6px;">
                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $km->guru }}">
                    <i class="fa-solid fa-user-tie" style="color: #6b7a99; font-size: 11px; width: 14px;"></i> {{ $km->guru }}
                </div>
                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $km->mapel }}">
                    <i class="fa-solid fa-book" style="color: #6b7a99; font-size: 11px; width: 14px;"></i> {{ $km->mapel }}
                </div>
                <div>
                    <i class="fa-solid fa-clock" style="color: #6b7a99; font-size: 11px; width: 14px;"></i> {{ $km->jam }} ({{ $km->total_jurnal }}/{{ $km->total_jadwal }})
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; padding: 20px; text-align: center; color: #6b7a99;">
            Belum ada jadwal atau kelas aktif hari ini.
        </div>
        @endforelse
    </div>
</div>

<!-- 5. Two Columns Grid: Guru Izin & Permohonan Dispensasi -->
<div class="two-cols-grid">
    <!-- Left: Guru Izin & Jadwal Terdampak -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <h3 class="chart-title">Guru Izin & Jadwal Terdampak</h3>
                <span style="font-size: 12px; color: #6b7a99;">Daftar guru yang berhalangan hadir hari ini</span>
            </div>
            <a href="{{ route('guru-piket.izin-guru', ['tanggal' => $today]) }}" style="font-size: 13px; font-weight: 700; color: #2b43b9; text-decoration: none;">
                Detail Izin
            </a>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Guru</th>
                        <th>Jenis</th>
                        <th>Alasan</th>
                        <th>Kelas Terdampak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guruIzinRecords as $iz)
                    @php
                        $terdampak = $jadwalTerdampakIzin->where('id_guru', $iz->id_guru);
                    @endphp
                    <tr>
                        <td>
                            <strong style="color: #1b2559;">{{ $iz->guru->nama_lengkap ?? 'Guru' }}</strong>
                            <div style="font-size: 11px; color: #6b7a99;">NIP: {{ $iz->guru->nip ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="status-badge warning">{{ $iz->jenis_izin }}</span>
                        </td>
                        <td style="max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $iz->alasan }}
                        </td>
                        <td>
                            @if($terdampak->count() > 0)
                                @foreach($terdampak as $jt)
                                    <span class="status-badge danger" style="margin-bottom: 2px; display: inline-block;">
                                        {{ $jt->kelas->nama_kelas ?? 'Kelas' }} (Jam ke-{{ $jt->jam_ke }})
                                    </span>
                                @endforeach
                            @else
                                <span style="color: #94a3b8; font-size: 11.5px;">Tidak ada jadwal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #6b7a99; padding: 24px;">
                            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 20px; margin-bottom: 4px;"></i>
                            <div>Tidak ada guru yang izin/sakit hari ini.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Permohonan Dispensasi Siswa -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <h3 class="chart-title">Dispensasi Siswa Hari Ini</h3>
                <span style="font-size: 12px; color: #6b7a99;">Izin siswa keluar lingkungan sekolah</span>
            </div>
            <a href="{{ route('guru-piket.dispensasi', ['tanggal' => $today]) }}" style="font-size: 13px; font-weight: 700; color: #2b43b9; text-decoration: none;">
                Semua Dispensasi
            </a>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Waktu</th>
                        <th>Keperluan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispensasiToday->take(6) as $dp)
                    <tr>
                        <td>
                            <strong style="color: #1b2559;">{{ $dp->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                            <div style="font-size: 11px; color: #6b7a99;">{{ $dp->siswa->kelas->nama_kelas ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #1b2559;">{{ Carbon\Carbon::parse($dp->jam_keluar)->format('H:i') }}</div>
                            @if($dp->jam_kembali)
                                <div style="font-size: 11px; color: #10b981;">Kembali: {{ Carbon\Carbon::parse($dp->jam_kembali)->format('H:i') }}</div>
                            @endif
                        </td>
                        <td style="max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $dp->alasan }}
                        </td>
                        <td>
                            @if($dp->status === 'Menunggu')
                                <form action="{{ route('guru-piket.dispensasi.status', $dp->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="action" value="setujui">
                                    <button type="submit" class="btn-action-primary" style="padding: 4px 8px; font-size: 11px;">
                                        Setujui
                                    </button>
                                </form>
                            @elseif($dp->status === 'Disetujui' && !$dp->jam_kembali)
                                <form action="{{ route('guru-piket.dispensasi.status', $dp->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="action" value="kembali">
                                    <button type="submit" class="btn-action-secondary" style="padding: 4px 8px; font-size: 11px;">
                                        Kembali
                                    </button>
                                </form>
                            @else
                                <span class="status-badge success">{{ $dp->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #6b7a99; padding: 24px;">
                            <i class="fa-solid fa-id-card-clip" style="font-size: 20px; margin-bottom: 4px;"></i>
                            <div>Belum ada siswa yang meminta dispensasi hari ini.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Quick Dispensasi Siswa -->
<div class="custom-modal-backdrop" id="dispensasiModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <h3><i class="fa-solid fa-ticket-simple" style="color: #2b43b9;"></i> Terbitkan Surat Dispensasi Siswa</h3>
            <button type="button" class="modal-close-btn" onclick="closeDispensasiModal()">&times;</button>
        </div>
        <form action="{{ route('guru-piket.dispensasi.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateDispensasiForm(this);">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Pilih Siswa <span style="color:#ef4444;">*</span></span>
                        <span id="dispensasiSiswaCount" style="font-size: 11px; font-weight: 600; color: #64748b;">
                            Menampilkan {{ count($siswaSelectOption) }} siswa
                        </span>
                    </label>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="id_siswa" id="dispensasiIdSiswa" required>

                    <!-- Selected Student Preview Card -->
                    <div id="dispensasiSelectedCard" style="display: none; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 10px 14px; margin-bottom: 8px; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <div id="dispensasiSelectedName" style="font-weight: 800; color: #166534; font-size: 13px;"></div>
                                <div id="dispensasiSelectedMeta" style="font-size: 11.5px; color: #15803d; margin-top: 1px;"></div>
                            </div>
                        </div>
                        <button type="button" onclick="changeSiswaDispensasi()" style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 8px; padding: 5px 10px; font-size: 11.5px; font-weight: 700; color: #dc2626; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <i class="fa-solid fa-rotate-left"></i> Ganti
                        </button>
                    </div>

                    <!-- Search & Results Section -->
                    <div id="dispensasiSearchSection">

                        <!-- Filter Baris: Tingkat & Kelas -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                            <div style="position: relative;">
                                <select id="dispensasiFilterTingkat" class="form-control" onchange="filterSiswaDispensasi()" style="font-size: 12.5px; padding: 8px 30px 8px 12px; border-color: #cbd5e1; appearance: none; -webkit-appearance: none; cursor: pointer; background: #ffffff;">
                                    <option value="">Semua Tingkat</option>
                                    <option value="X">Tingkat X</option>
                                    <option value="XI">Tingkat XI</option>
                                    <option value="XII">Tingkat XII</option>
                                </select>
                                <i class="fa-solid fa-chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; font-size: 11px;"></i>
                            </div>
                            <div style="position: relative;">
                                <select id="dispensasiFilterKelas" class="form-control" onchange="filterSiswaDispensasi()" style="font-size: 12.5px; padding: 8px 30px 8px 12px; border-color: #cbd5e1; appearance: none; -webkit-appearance: none; cursor: pointer; background: #ffffff;">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k->id_kelas }}" data-tingkat="{{ $k->tingkat }}">{{ $k->nama_kelas }}{{ $k->jurusan ? ' ('.$k->jurusan.')' : '' }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; font-size: 11px;"></i>
                            </div>
                        </div>

                        <!-- Search Text -->
                        <div style="position: relative; margin-bottom: 8px;">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none;"></i>
                            <input type="text" id="dispensasiSearchInput" class="form-control" placeholder="Cari nama atau NISN..." style="padding-left: 38px; padding-right: 36px; border-color: #cbd5e1;" oninput="filterSiswaDispensasi()">
                            <button type="button" id="dispensasiClearSearchBtn" onclick="resetAllDispensasiFilters()" title="Reset semua filter" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; display: none; padding: 4px;">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Scrollable Student List -->
                        <div id="dispensasiSiswaListWrapper" style="max-height: 160px; overflow-y: auto; border: 1.5px solid #e2e8f0; border-radius: 12px; background: #ffffff; padding: 4px;">
                            <div id="dispensasiSiswaEmpty" style="display: none; padding: 18px; text-align: center; color: #94a3b8; font-size: 12.5px;">
                                <i class="fa-solid fa-user-slash" style="font-size: 20px; margin-bottom: 6px; display: block; color: #cbd5e1;"></i>
                                Tidak ada siswa yang cocok dengan filter.
                            </div>
                            <div id="dispensasiSiswaOptions">
                                @foreach($siswaSelectOption as $s)
                                    <div class="dispensasi-siswa-item" 
                                         data-id="{{ $s->id_siswa }}" 
                                         data-name="{{ $s->nama_lengkap }}" 
                                         data-kelas="{{ $s->kelas->nama_kelas ?? '-' }}" 
                                         data-id-kelas="{{ $s->id_kelas }}"
                                         data-tingkat="{{ $s->kelas->tingkat ?? '' }}"
                                         data-nisn="{{ $s->nisn }}"
                                         data-jurusan="{{ strtolower($s->kelas->jurusan ?? '') }}"
                                         data-search="{{ strtolower($s->nama_lengkap . ' ' . ($s->kelas->nama_kelas ?? '') . ' ' . $s->nisn) }}"
                                         onclick="selectSiswaDispensasi(this)"
                                         style="padding: 9px 12px; border-radius: 8px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.15s ease; border-bottom: 1px solid #f8fafc;">
                                        <div>
                                            <div class="siswa-name" style="font-weight: 700; font-size: 13px; color: #1b2559;">{{ $s->nama_lengkap }}</div>
                                            <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                                                <span style="background: #eef2ff; color: #2b43b9; padding: 1px 7px; border-radius: 4px; font-weight: 700; font-size: 10.5px;">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                                                @if($s->kelas->jurusan ?? null)
                                                    <span style="background: #fff7ed; color: #c2410c; padding: 1px 7px; border-radius: 4px; font-weight: 700; font-size: 10px; margin-left: 4px;">{{ $s->kelas->jurusan }}</span>
                                                @endif
                                                <span style="margin-left: 6px;">NISN: {{ $s->nisn }}</span>
                                            </div>
                                        </div>
                                        <i class="fa-solid fa-chevron-right" style="font-size: 11px; color: #cbd5e1; transition: all 0.15s ease;"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tanggal Dispensasi <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="tanggal" value="{{ $today }}" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jam Keluar <span style="color:#ef4444;">*</span></label>
                        <input type="time" name="jam_keluar" value="{{ now()->format('H:i') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Perkiraan Jam Kembali</label>
                        <input type="time" name="jam_kembali" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Alasan / Keperluan Keluar <span style="color:#ef4444;">*</span></label>
                    <textarea name="alasan" rows="3" class="form-control" placeholder="Contoh: Mengikuti perwakilan lomba / Izin ke Puskesmas..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Bukti Surat / Foto (Opsional)</label>
                    <input type="file" name="bukti_file" class="form-control" accept="image/*,application/pdf">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action-secondary" onclick="closeDispensasiModal()">Batal</button>
                <button type="submit" class="btn-action-primary">Terbitkan Surat</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Quick Catatan Laporan Piket -->
<div class="custom-modal-backdrop" id="laporanModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <h3><i class="fa-solid fa-file-pen" style="color: #2b43b9;"></i> Catatan Laporan Piket Harian</h3>
            <button type="button" class="modal-close-btn" onclick="closeLaporanModal()">&times;</button>
        </div>
        <form action="{{ route('guru-piket.laporan.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Tanggal Piket</label>
                    <input type="date" name="tanggal" value="{{ $today }}" class="form-control" readonly style="background: #f1f5f9;">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jam Mulai Piket</label>
                        <input type="time" name="jam_mulai_piket" value="{{ $laporanPiketToday->jam_mulai_piket ?? '06:45' }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Jam Selesai Piket</label>
                        <input type="time" name="jam_selesai_piket" value="{{ $laporanPiketToday->jam_selesai_piket ?? '15:30' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Uraian Kejadian / Catatan Khusus Selama Piket</label>
                    <textarea name="catatan_kejadian" rows="5" class="form-control" placeholder="Tuliskan catatan kejadian penting, seperti penertiban seragam di gerbang, monitoring kelas kosong, siswa sakit di UKS, dsb...">{{ $laporanPiketToday->catatan_kejadian ?? '' }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action-secondary" onclick="closeLaporanModal()">Batal</button>
                <button type="submit" class="btn-action-primary">Simpan Catatan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openDispensasiModal() {
        document.getElementById('dispensasiModal').classList.add('show');
        setTimeout(() => {
            const searchInput = document.getElementById('dispensasiSearchInput');
            if (searchInput && document.getElementById('dispensasiSearchSection').style.display !== 'none') {
                searchInput.focus();
            }
        }, 150);
    }
    function closeDispensasiModal() {
        document.getElementById('dispensasiModal').classList.remove('show');
    }
    function openLaporanModal() {
        document.getElementById('laporanModal').classList.add('show');
    }
    function closeLaporanModal() {
        document.getElementById('laporanModal').classList.remove('show');
    }

    function filterSiswaDispensasi() {
        const term = (document.getElementById('dispensasiSearchInput')?.value || '').toLowerCase().trim();
        const filterTingkat = document.getElementById('dispensasiFilterTingkat')?.value || '';
        const filterKelas = document.getElementById('dispensasiFilterKelas')?.value || '';

        const items = document.querySelectorAll('.dispensasi-siswa-item');
        const emptyMsg = document.getElementById('dispensasiSiswaEmpty');
        const counter = document.getElementById('dispensasiSiswaCount');
        const clearBtn = document.getElementById('dispensasiClearSearchBtn');

        const hasAnyFilter = term || filterTingkat || filterKelas;
        if (clearBtn) clearBtn.style.display = hasAnyFilter ? 'block' : 'none';

        // Cascade-filter kelas dropdown saat tingkat dipilih
        const kelasSelect = document.getElementById('dispensasiFilterKelas');
        if (kelasSelect && filterTingkat) {
            Array.from(kelasSelect.options).forEach(opt => {
                if (!opt.value) return;
                opt.style.display = (opt.getAttribute('data-tingkat') === filterTingkat) ? '' : 'none';
            });
            if (kelasSelect.value) {
                const selOpt = kelasSelect.options[kelasSelect.selectedIndex];
                if (selOpt && selOpt.getAttribute('data-tingkat') !== filterTingkat) {
                    kelasSelect.value = '';
                }
            }
        } else if (kelasSelect && !filterTingkat) {
            Array.from(kelasSelect.options).forEach(opt => opt.style.display = '');
        }

        const activeKelas = document.getElementById('dispensasiFilterKelas')?.value || '';

        let matchCount = 0;
        items.forEach(item => {
            const searchText  = item.getAttribute('data-search') || '';
            const itemTingkat = item.getAttribute('data-tingkat') || '';
            const itemIdKelas = item.getAttribute('data-id-kelas') || '';

            const matchText    = !term          || searchText.includes(term);
            const matchTingkat = !filterTingkat || itemTingkat === filterTingkat;
            const matchKelas   = !activeKelas   || itemIdKelas === activeKelas;

            if (matchText && matchTingkat && matchKelas) {
                item.style.display = 'flex';
                matchCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (emptyMsg) emptyMsg.style.display = matchCount === 0 ? 'block' : 'none';
        if (counter) {
            counter.innerText = hasAnyFilter
                ? `Ditemukan ${matchCount} siswa`
                : `Menampilkan ${matchCount} siswa`;
        }
    }

    function selectSiswaDispensasi(el) {
        const id      = el.getAttribute('data-id');
        const name    = el.getAttribute('data-name');
        const kelas   = el.getAttribute('data-kelas');
        const nisn    = el.getAttribute('data-nisn');
        const jurusan = el.getAttribute('data-jurusan') || '';

        document.getElementById('dispensasiIdSiswa').value = id;
        document.getElementById('dispensasiSelectedName').innerText = name;
        document.getElementById('dispensasiSelectedMeta').innerText =
            `${kelas}${jurusan ? ' • ' + jurusan.toUpperCase() : ''} • NISN: ${nisn}`;

        document.getElementById('dispensasiSelectedCard').style.display = 'flex';
        document.getElementById('dispensasiSearchSection').style.display = 'none';
    }

    function changeSiswaDispensasi() {
        document.getElementById('dispensasiIdSiswa').value = '';
        document.getElementById('dispensasiSelectedCard').style.display = 'none';
        document.getElementById('dispensasiSearchSection').style.display = 'block';
        resetAllDispensasiFilters();
    }

    function resetAllDispensasiFilters() {
        const searchInput   = document.getElementById('dispensasiSearchInput');
        const filterTingkat = document.getElementById('dispensasiFilterTingkat');
        const filterKelas   = document.getElementById('dispensasiFilterKelas');
        if (searchInput)   searchInput.value = '';
        if (filterTingkat) filterTingkat.value = '';
        if (filterKelas) {
            filterKelas.value = '';
            Array.from(filterKelas.options).forEach(o => o.style.display = '');
        }
        filterSiswaDispensasi();
        if (searchInput) searchInput.focus();
    }

    function validateDispensasiForm(form) {
        const idSiswa = document.getElementById('dispensasiIdSiswa').value;
        if (!idSiswa) {
            alert('Silakan cari dan pilih siswa terlebih dahulu dari daftar pencarian!');
            const searchInput = document.getElementById('dispensasiSearchInput');
            if (searchInput) searchInput.focus();
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Chart 1: Bar Chart Ketidakhadiran per Jurusan
        const ctxJurusan = document.getElementById('jurusanChart');
        if (ctxJurusan) {
            new Chart(ctxJurusan, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($jurusanLabels) !!},
                    datasets: [{
                        label: 'Siswa Absen',
                        data: {!! json_encode($jurusanAbsen) !!},
                        backgroundColor: '#2b43b9',
                        borderRadius: 6,
                        barThickness: 22,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1b2559',
                            padding: 10,
                            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 12 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { family: 'Plus Jakarta Sans' }, color: '#707e94' },
                            grid: { color: '#f4f7fe' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Plus Jakarta Sans', weight: 'bold' }, color: '#707e94' }
                        }
                    }
                }
            });
        }

        // Chart 2: Doughnut Presensi
        const ctxPie = document.getElementById('presensiPieChart');
        if (ctxPie) {
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi'],
                    datasets: [{
                        data: [
                            {{ $piePresensi['Hadir'] }},
                            {{ $piePresensi['Sakit'] }},
                            {{ $piePresensi['Izin'] }},
                            {{ $piePresensi['Alpha'] }},
                            {{ $piePresensi['Dispensasi'] }}
                        ],
                        backgroundColor: ['#10b981', '#0ea5e9', '#f97316', '#ef4444', '#6366f1'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 14,
                                font: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' },
                                color: '#707e94'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
