@extends('layouts.waka_sdm')

@section('title', 'Dashboard Waka SDM - SMKN 1 BOYOLANGU')
@section('header_title', 'Dashboard Wakil Kepala Sekolah')
@section('header_subtitle', 'Ringkasan operasional dan persetujuan hari ini, ' . $todayFormatted)

@section('styles')
<style>
    /* ── 4 KPI CARDS (Presisi Sesuai Gambar) ── */
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    .kpi-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px 22px;
        border: 1px solid #e5e9f2;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }
    .kpi-box-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }
    .kpi-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #64748b;
        line-height: 1.35;
    }
    .kpi-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .kpi-icon-wrap.amber {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .kpi-icon-wrap.green {
        background: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .kpi-icon-wrap.rose {
        background: #ffe4e6;
        color: #e11d48;
        border: 1px solid #fecdd3;
    }
    .kpi-number {
        font-size: 38px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin: 14px 0 6px 0;
        letter-spacing: -0.5px;
    }
    .kpi-sub {
        font-size: 11.5px;
        color: #94a3b8;
        font-weight: 600;
    }

    /* ── TABEL UTAMA "PENGAJUAN MENUNGGU PERSETUJUAN" ── */
    .section-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e5e9f2;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .section-header {
        padding: 22px 24px 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .section-title-wrap h2 {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.2px;
    }
    .section-title-wrap p {
        font-size: 13px;
        color: #64748b;
        margin-top: 3px;
        font-weight: 500;
    }
    .link-view-all {
        font-size: 13.5px;
        font-weight: 700;
        color: #2563eb;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.15s;
    }
    .link-view-all:hover { color: #1d4ed8; text-decoration: underline; }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    .approval-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .approval-table th {
        padding: 12px 24px;
        background: #ffffff;
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        border-bottom: 1px solid #f1f5f9;
        text-transform: capitalize;
    }
    .approval-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f8fafc;
        font-size: 13.5px;
        color: #334155;
        vertical-align: middle;
    }
    .approval-table tr:hover td {
        background-color: #fafbfd;
    }
    .approval-table tr:last-child td {
        border-bottom: none;
    }

    /* Type Dot & Name */
    .type-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 13px;
    }
    .type-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .type-dot.blue { background-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,0.2); }
    .type-dot.green { background-color: #16a34a; box-shadow: 0 0 0 2px rgba(22,163,74,0.2); }

    .applicant-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
    }
    .applicant-date {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
    }

    /* Badges */
    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 16px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
    }
    .status-pill.menunggu {
        background-color: #ffedd5;
        color: #ea580c;
    }
    .status-pill.disetujui {
        background-color: #d1fae5;
        color: #059669;
    }
    .status-pill.ditolak {
        background-color: #fee2e2;
        color: #dc2626;
    }

    /* Detail Button */
    .btn-detail {
        padding: 6px 18px;
        border-radius: 20px;
        background-color: #274fd8;
        color: white;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-block;
        transition: background 0.15s ease, transform 0.15s;
    }
    .btn-detail:hover {
        background-color: #1e40af;
        transform: scale(1.02);
    }

    /* ── DUA KARTU BERSEBELAHAN (Izin Guru & Dispensasi Siswa) ── */
    .two-col-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    .sub-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e5e9f2;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        padding: 20px 22px;
    }
    .sub-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .sub-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }
    .sub-card-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .sub-card-icon.blue { background: #dbeafe; color: #2563eb; }
    .sub-card-icon.green { background: #dcfce7; color: #16a34a; }

    .sub-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .sub-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 12px;
        transition: background 0.15s;
    }
    .sub-list-item:hover {
        background: #f8fafc;
    }
    .sub-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar-bubble {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        color: white;
        flex-shrink: 0;
    }
    .avatar-bubble.blue { background-color: #3b82f6; }
    .avatar-bubble.green { background-color: #10b981; }

    .sub-item-info {
        display: flex;
        flex-direction: column;
    }
    .sub-item-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
    }
    .sub-item-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 1px;
    }
    .sub-item-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }
    .sub-item-date {
        font-size: 11.5px;
        color: #94a3b8;
        font-weight: 600;
    }

    /* ── ROW BAWAH: STATISTIK & MONITORING DISPENSASI HARI INI ── */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
    }
    .chart-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e5e9f2;
        padding: 22px 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }
    .chart-header {
        margin-bottom: 16px;
    }
    .chart-header h3 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }
    .chart-header p {
        font-size: 12.5px;
        color: #64748b;
        margin-top: 2px;
    }
    .chart-legend {
        display: flex;
        align-items: center;
        gap: 24px;
        margin-top: 18px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
        font-size: 12.5px;
        font-weight: 600;
        color: #475569;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .legend-dot.blue { background-color: #2563eb; }
    .legend-dot.green { background-color: #10b981; }

    /* Monitoring 4 Kotak 2x2 */
    .monitoring-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e5e9f2;
        padding: 22px 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
    }
    .monitoring-header {
        margin-bottom: 16px;
    }
    .monitoring-header h3 {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }
    .monitoring-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        flex: 1;
    }
    .monitoring-tile {
        border-radius: 14px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .monitoring-tile.green { background-color: #d1fae5; }
    .monitoring-tile.amber { background-color: #ffedd5; }
    .monitoring-tile.purple { background-color: #e0e7ff; }
    .monitoring-tile.rose { background-color: #ffe4e6; }

    .tile-num {
        font-size: 26px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
    }
    .tile-num.green { color: #059669; }
    .tile-num.amber { color: #d97706; }
    .tile-num.purple { color: #4f46e5; }
    .tile-num.rose { color: #e11d48; }

    .tile-label {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
    }

    /* ── MODAL REVIEW & APPROVAL ── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.6);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(3px);
        padding: 16px;
    }
    .modal-overlay.open { display: flex; }
    .modal-dialog {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 540px;
        box-shadow: 0 24px 60px rgba(0,0,0,0.18);
        overflow: hidden;
        animation: modalScale 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes modalScale {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .modal-head {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-head h3 {
        font-size: 16.5px;
        font-weight: 800;
        color: #0f172a;
    }
    .btn-modal-close {
        background: #f1f5f9;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-body {
        padding: 22px 24px;
        max-height: 70vh;
        overflow-y: auto;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: 130px 1fr;
        gap: 12px 16px;
        margin-bottom: 20px;
        font-size: 13.5px;
    }
    .detail-label { color: #64748b; font-weight: 600; }
    .detail-val { color: #0f172a; font-weight: 700; }
    .catatan-textarea {
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        padding: 10px 14px;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        resize: vertical;
        margin-top: 6px;
    }
    .catatan-textarea:focus { border-color: #2563eb; }
    .modal-foot {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
    }
    .btn-action-reject {
        padding: 10px 18px;
        border-radius: 10px;
        background: #fee2e2;
        color: #dc2626;
        font-weight: 700;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-action-reject:hover { background: #fecaca; }
    .btn-action-approve {
        padding: 10px 20px;
        border-radius: 10px;
        background: #059669;
        color: white;
        font-weight: 700;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-action-approve:hover { background: #047857; }

    @media (max-width: 1024px) {
        .kpi-row { grid-template-columns: repeat(2, 1fr); }
        .two-col-grid { grid-template-columns: 1fr; }
        .bottom-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .kpi-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div>
    {{-- 1. EMPAT KARTU METRIK KPI (BARIS TERATAS) --}}
    <div class="kpi-row">
        {{-- Card 1: Menunggu persetujuan Izin Guru --}}
        <div class="kpi-box">
            <div class="kpi-box-top">
                <span class="kpi-title">Menunggu persetujuan<br>Izin guru</span>
                <div class="kpi-icon-wrap amber">
                    <i class="fa-regular fa-clock"></i>
                </div>
            </div>
            <div>
                <div class="kpi-number">{{ $menungguIzinCount }}</div>
                <div class="kpi-sub">Pengajuan Perlu Diperiksa</div>
            </div>
        </div>

        {{-- Card 2: Menunggu persetujuan Dispensasi --}}
        <div class="kpi-box">
            <div class="kpi-box-top">
                <span class="kpi-title">Menunggu persetujuan<br>Dispensasi</span>
                <div class="kpi-icon-wrap amber">
                    <i class="fa-regular fa-clock"></i>
                </div>
            </div>
            <div>
                <div class="kpi-number">{{ $menungguDispensasiCount }}</div>
                <div class="kpi-sub">Pengajuan Perlu Diperiksa</div>
            </div>
        </div>

        {{-- Card 3: Ditolak Hari Ini --}}
        <div class="kpi-box">
            <div class="kpi-box-top">
                <span class="kpi-title">Ditolak Hari Ini</span>
                <div class="kpi-icon-wrap green">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>
            <div>
                <div class="kpi-number">{{ $ditolakHariIniCount }}</div>
                <div class="kpi-sub">Pengajuan ditolak</div>
            </div>
        </div>

        {{-- Card 4: Disetujui Hari Ini --}}
        <div class="kpi-box">
            <div class="kpi-box-top">
                <span class="kpi-title">Disetujui Hari Ini</span>
                <div class="kpi-icon-wrap rose">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div>
                <div class="kpi-number">{{ $disetujuiHariIniCount }}</div>
                <div class="kpi-sub">Pengajuan Telah Disetujui</div>
            </div>
        </div>
    </div>

    {{-- 2. TABEL "PENGAJUAN MENUNGGU PERSETUJUAN" --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-title-wrap">
                <h2>Pengajuan Menunggu Persetujuan</h2>
                <p>Pengajuan yang perlu segera ditinjau.</p>
            </div>
            <a href="{{ route('waka-sdm.izin') }}" class="link-view-all">
                <span>Lihat Semua</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="table-container">
            <table class="approval-table">
                <thead>
                    <tr>
                        <th>Pengajuan</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingApprovals as $p)
                        <tr>
                            <td>
                                <div class="type-pill">
                                    <span class="type-dot {{ $p->type_class }}"></span>
                                    <span>{{ $p->type_label }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="applicant-name">{{ $p->nama }}</div>
                            </td>
                            <td>
                                <div class="applicant-date">{{ $p->tanggal }}</div>
                            </td>
                            <td>
                                <span class="status-pill menunggu">Menunggu</span>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn-detail" onclick="openApprovalModal({{ json_encode($p) }})">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 36px; color:#94a3b8;">
                                <i class="fa-solid fa-circle-check" style="font-size: 28px; color: #10b981; margin-bottom: 8px; display:block;"></i>
                                Tidak ada pengajuan yang sedang menunggu persetujuan saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. DUA KARTU BERSEBELAHAN: PERSETUJUAN IZIN GURU & DISPENSASI SISWA --}}
    <div class="two-col-grid">
        {{-- Card Kiri: Persetujuan Izin Guru --}}
        <div class="sub-card">
            <div class="sub-card-header">
                <div class="sub-card-title">
                    <div class="sub-card-icon blue">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <span>Persetujuan Izin Guru</span>
                </div>
                <a href="{{ route('waka-sdm.izin') }}" class="link-view-all">Lihat Semua</a>
            </div>
            <div class="sub-list">
                @forelse($recentIzinGuru as $iz)
                    <div class="sub-list-item">
                        <div class="sub-item-left">
                            <div class="avatar-bubble blue">{{ $iz->initials }}</div>
                            <div class="sub-item-info">
                                <span class="sub-item-name">{{ $iz->nama }}</span>
                                <span class="sub-item-sub">{{ $iz->sub_info }}</span>
                            </div>
                        </div>
                        <div class="sub-item-right">
                            <span class="sub-item-date">{{ $iz->tanggal }}</span>
                            @if($iz->status === 'Disetujui')
                                <span class="status-pill disetujui" style="font-size:11px; padding:3px 12px;">Disetujui</span>
                            @elseif($iz->status === 'Ditolak')
                                <span class="status-pill ditolak" style="font-size:11px; padding:3px 12px;">Ditolak</span>
                            @else
                                <span class="status-pill menunggu" style="font-size:11px; padding:3px 12px;">Menunggu</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:24px; color:#94a3b8; font-size:13px;">Belum ada riwayat izin guru.</div>
                @endforelse
            </div>
        </div>

        {{-- Card Kanan: Persetujuan Dispensasi Siswa --}}
        <div class="sub-card">
            <div class="sub-card-header">
                <div class="sub-card-title">
                    <div class="sub-card-icon green">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span>Persetujuan Dispensasi Siswa</span>
                </div>
                <a href="{{ route('waka-sdm.dispensasi') }}" class="link-view-all">Lihat Semua</a>
            </div>
            <div class="sub-list">
                @forelse($recentDispensasi as $ds)
                    <div class="sub-list-item">
                        <div class="sub-item-left">
                            <div class="avatar-bubble green">{{ $ds->initials }}</div>
                            <div class="sub-item-info">
                                <span class="sub-item-name">{{ $ds->nama }}</span>
                                <span class="sub-item-sub">{{ $ds->sub_info }}</span>
                            </div>
                        </div>
                        <div class="sub-item-right">
                            <span class="sub-item-date">{{ $ds->tanggal }}</span>
                            @if(in_array($ds->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai']))
                                <span class="status-pill disetujui" style="font-size:11px; padding:3px 12px;">Disetujui</span>
                            @elseif($ds->status === 'Ditolak')
                                <span class="status-pill ditolak" style="font-size:11px; padding:3px 12px;">Ditolak</span>
                            @else
                                <span class="status-pill menunggu" style="font-size:11px; padding:3px 12px;">Menunggu</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:24px; color:#94a3b8; font-size:13px;">Belum ada riwayat dispensasi siswa.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 4. ROW BAWAH: STATISTIK & MONITORING DISPENSASI HARI INI --}}
    <div class="bottom-grid">
        {{-- Statistik Persetujuan 7 Hari --}}
        <div class="chart-card">
            <div class="chart-header">
                <h3>Statistik Persetujuan</h3>
                <p>Jumlah pengajuan dalam 7 hari terakhir</p>
            </div>
            <div style="position:relative; height: 190px; width: 100%;">
                <canvas id="approvalChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-dot blue"></span>
                    <span>Izin Guru</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot green"></span>
                    <span>Dispensasi Siswa</span>
                </div>
            </div>
        </div>

        {{-- Monitoring Dispensasi Hari Ini (Grid 2x2) --}}
        <div class="monitoring-card">
            <div class="monitoring-header">
                <h3>Monitoring Dispensasi Hari Ini</h3>
            </div>
            <div class="monitoring-grid">
                {{-- Box 1: Disetujui Waka --}}
                <div class="monitoring-tile green">
                    <div class="tile-num green">{{ $monitoringDispensasi->disetujui_waka }}</div>
                    <div class="tile-label">Disetujui Waka</div>
                </div>
                {{-- Box 2: Menunggu Keluar --}}
                <div class="monitoring-tile amber">
                    <div class="tile-num amber">{{ $monitoringDispensasi->menunggu_keluar }}</div>
                    <div class="tile-label">Menunggu Keluar</div>
                </div>
                {{-- Box 3: Sudah Keluar --}}
                <div class="monitoring-tile purple">
                    <div class="tile-num purple">{{ $monitoringDispensasi->sudah_keluar }}</div>
                    <div class="tile-label">Sudah Keluar</div>
                </div>
                {{-- Box 4: Ditolak --}}
                <div class="monitoring-tile rose">
                    <div class="tile-num rose">{{ $monitoringDispensasi->ditolak }}</div>
                    <div class="tile-label">Ditolak</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL & REVIEW PENGAJUAN --}}
<div class="modal-overlay" id="approvalModal">
    <div class="modal-dialog">
        <div class="modal-head">
            <h3 id="modalTitle">Detail Pengajuan</h3>
            <button type="button" class="btn-modal-close" onclick="closeApprovalModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="approvalForm" method="POST">
            @csrf
            <input type="hidden" name="status" id="modalActionStatus" value="Disetujui">
            
            <div class="modal-body">
                <div class="detail-grid">
                    <span class="detail-label">Tipe Pengajuan</span>
                    <span class="detail-val" id="modalTipeLabel">-</span>

                    <span class="detail-label">Nama Pemohon</span>
                    <span class="detail-val" id="modalNamaPemohon">-</span>

                    <span class="detail-label">NIP / NISN</span>
                    <span class="detail-val" id="modalNipNisn">-</span>

                    <span class="detail-label">Tanggal / Waktu</span>
                    <span class="detail-val" id="modalRentangWaktu">-</span>

                    <span class="detail-label">Alasan</span>
                    <span class="detail-val" id="modalAlasan" style="color:#334155; font-weight:500;">-</span>

                    <span class="detail-label">Berkas Lampiran</span>
                    <span class="detail-val" id="modalLampiran">-</span>
                </div>

                <div style="margin-top: 14px;">
                    <label for="catatanInput" style="font-size: 13px; font-weight: 700; color: #1e293b;">
                        Catatan Waka SDM (Opsional):
                    </label>
                    <textarea name="catatan" id="catatanInput" class="catatan-textarea" rows="2" placeholder="Tuliskan catatan atau instruksi jika diperlukan..."></textarea>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="btn-modal cancel" onclick="closeApprovalModal()">Tutup</button>
                <button type="button" class="btn-action-reject" onclick="submitDecision('Ditolak')">
                    <i class="fa-solid fa-xmark"></i> Tolak
                </button>
                <button type="button" class="btn-action-approve" onclick="submitDecision('Disetujui')">
                    <i class="fa-solid fa-check"></i> Setujui Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Inisialisasi Chart.js Statistik Persetujuan 7 Hari
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('approvalChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const izinData = {!! json_encode($chartIzinData) !!};
        const dispData = {!! json_encode($chartDispData) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Izin Guru',
                        data: izinData,
                        backgroundColor: '#2563eb',
                        borderRadius: 6,
                        barPercentage: 0.55,
                        categoryPercentage: 0.65
                    },
                    {
                        label: 'Dispensasi Siswa',
                        data: dispData,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        barPercentage: 0.55,
                        categoryPercentage: 0.65
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    });

    // Handle Approval Modal
    function openApprovalModal(item) {
        document.getElementById('modalTitle').innerText = 'Persetujuan ' + item.type_label;
        document.getElementById('modalTipeLabel').innerText = item.type_label;
        document.getElementById('modalNamaPemohon').innerText = item.nama;
        document.getElementById('modalNipNisn').innerText = item.nip_nisn || '-';
        document.getElementById('modalRentangWaktu').innerText = item.rentang || item.tanggal;
        document.getElementById('modalAlasan').innerText = item.alasan || '-';

        const lampiranEl = document.getElementById('modalLampiran');
        if (item.bukti_file) {
            lampiranEl.innerHTML = `<a href="/storage/${item.bukti_file}" target="_blank" style="color:#2563eb; font-weight:700; text-decoration:none;"><i class="fa-solid fa-paperclip"></i> Lihat Lampiran Surat</a>`;
        } else {
            lampiranEl.innerHTML = '<span style="color:#94a3b8;">Tidak ada lampiran</span>';
        }

        document.getElementById('approvalForm').action = item.detail_url;
        document.getElementById('catatanInput').value = '';
        document.getElementById('approvalModal').classList.add('open');
    }

    function closeApprovalModal() {
        document.getElementById('approvalModal').classList.remove('open');
    }

    function submitDecision(status) {
        document.getElementById('modalActionStatus').value = status;
        if (confirm(`Apakah Anda yakin ingin memberikan keputusan "${status}" pada pengajuan ini?`)) {
            document.getElementById('approvalForm').submit();
        }
    }
</script>
@endsection
