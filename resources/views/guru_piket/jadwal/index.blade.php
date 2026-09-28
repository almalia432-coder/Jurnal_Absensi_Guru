@extends('layouts.guru_piket')

@section('title', 'Jadwal Petugas Piket KBM - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Jadwal Petugas Piket KBM')
@section('header_subtitle', 'Semester Ganjil SMK Negeri 1 Boyolangu Tahun Pelajaran 2026/2027')

@section('header_extra')
    <a href="{{ route('guru-piket.jadwal.cetak') }}" target="_blank" class="btn-action-primary" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; text-decoration: none;">
        <i class="fa-solid fa-print"></i> Cetak Jadwal Resmi
    </a>
@endsection

@section('styles')
<style>
    /* Tabs Header */
    .tab-nav-container {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        padding: 8px 12px;
        border-radius: 16px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 10px 18px;
        border-radius: 12px;
        border: none;
        background: transparent;
        font-size: 13.5px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .tab-btn:hover {
        background: #f1f5f9;
        color: #2b43b9;
    }

    .tab-btn.active {
        background: #2b43b9;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(43, 67, 185, 0.25);
    }

    /* Date Filter Card */
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 24px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .filter-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .filter-input {
        padding: 8px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        font-weight: 600;
        color: #1b2559;
        outline: none;
    }
    .filter-input:focus { border-color: #2b43b9; }

    /* Roster Grid */
    .roster-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .roster-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .roster-header {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
    }
    .roster-header.pagi { background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%); }
    .roster-header.siang { background: linear-gradient(135deg, #e0e7ff 0%, #eef2ff 100%); }
    .roster-header.waka { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); }

    .roster-header-title {
        font-size: 15px;
        font-weight: 800;
        color: #1b2559;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .roster-time-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(0, 0, 0, 0.05);
        color: #1e293b;
    }

    .roster-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        flex: 1;
    }

    .koor-badge-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .user-avatar-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .user-avatar-circle.gold { background: #fef3c7; color: #b45309; }
    .user-avatar-circle.blue { background: #e0e7ff; color: #3730a3; }
    .user-avatar-circle.green { background: #dcfce7; color: #166534; }

    .petugas-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .petugas-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        transition: all 0.15s ease;
    }
    .petugas-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    /* Table Matrix */
    .matrix-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        padding: 24px;
        margin-bottom: 24px;
    }

    .matrix-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }

    .matrix-table th {
        background: #f8fafc;
        padding: 12px 14px;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        color: #475569;
        letter-spacing: 0.5px;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .matrix-table td {
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        vertical-align: top;
    }

    .cycle-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .cycle-badge.a { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .cycle-badge.b { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

    @media (max-width: 900px) {
        .roster-grid { grid-template-columns: 1fr; }
        .filter-card { flex-direction: column; align-items: stretch; }
    }
</style>
@endsection

@section('content')
<!-- Navigation Tabs -->
<div class="tab-nav-container">
    <a href="{{ route('guru-piket.jadwal', ['tab' => 'hari_ini', 'tanggal' => $tanggalInput]) }}" 
       class="tab-btn {{ $activeTab === 'hari_ini' ? 'active' : '' }}">
        <i class="fa-solid fa-clock"></i>
        <span>Petugas Hari Ini (Live)</span>
    </a>
    <a href="{{ route('guru-piket.jadwal', ['tab' => 'siklus_a', 'tanggal' => $tanggalInput]) }}" 
       class="tab-btn {{ $activeTab === 'siklus_a' ? 'active' : '' }}">
        <i class="fa-solid fa-repeat"></i>
        <span>Matriks Siklus A (Minggu Ganjil)</span>
    </a>
    <a href="{{ route('guru-piket.jadwal', ['tab' => 'siklus_b', 'tanggal' => $tanggalInput]) }}" 
       class="tab-btn {{ $activeTab === 'siklus_b' ? 'active' : '' }}">
        <i class="fa-solid fa-repeat"></i>
        <span>Matriks Siklus B (Minggu Genap)</span>
    </a>
</div>

{{-- TAB 1: ROSTER HARI INI / TARGET TANGGAL --}}
@if($activeTab === 'hari_ini')
    <!-- Filter Date Card -->
    <div class="filter-card">
        <form method="GET" action="{{ route('guru-piket.jadwal') }}" class="filter-left">
            <input type="hidden" name="tab" value="hari_ini">
            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Pilih Tanggal Roster</span>
                <div style="display: flex; gap: 8px;">
                    <input type="date" name="tanggal" value="{{ $tanggalInput }}" class="filter-input">
                    <button type="submit" class="btn-action-primary" style="padding: 8px 14px; font-size: 12.5px;">
                        <i class="fa-solid fa-magnifying-glass"></i> Cek Tanggal
                    </button>
                    @if($tanggalInput !== Carbon\Carbon::today()->format('Y-m-d'))
                        <a href="{{ route('guru-piket.jadwal', ['tab' => 'hari_ini']) }}" class="btn-action-primary" style="background: #f1f5f9; color: #475569; padding: 8px 12px; font-size: 12.5px; text-decoration: none;">
                            Kembali ke Hari Ini
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div>
            <div style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; margin-bottom: 4px;">Status Rotasi Tanggal Ini</div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="cycle-badge {{ strtolower($rosterTarget['siklus']) }}">
                    <i class="fa-solid fa-rotate"></i> Siklus {{ $rosterTarget['siklus'] }} (Minggu {{ $rosterTarget['siklus'] === 'A' ? 'Ganjil' : 'Genap' }})
                </span>
                <span style="font-size: 13px; font-weight: 700; color: #1b2559;">{{ $todayFormatted }}</span>
            </div>
        </div>
    </div>

    @if($rosterTarget['is_libur'])
        <div style="background: white; border-radius: 18px; padding: 48px; text-align: center; border: 1px solid #eef2f7; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
            <i class="fa-solid fa-calendar-xmark" style="font-size: 40px; color: #94a3b8; margin-bottom: 12px;"></i>
            <h3 style="font-size: 18px; font-weight: 800; color: #1b2559; margin-bottom: 4px;">Hari Libur Akhir Pekan ({{ $rosterTarget['hari'] }})</h3>
            <p style="font-size: 13.5px; color: #64748b;">Tidak ada jadwal kegiatan belajar mengajar dan piket KBM pada hari ini.</p>
        </div>
    @else
        <!-- Waka Piket Card Full Width -->
        <div style="background: #ffffff; border-radius: 18px; border: 1px solid #eef2f7; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03); padding: 18px 22px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div class="user-avatar-circle green">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #16a34a; letter-spacing: 0.5px;">Piket Pimpinan Waka (Seharian Penuh)</span>
                    <h3 style="font-size: 16px; font-weight: 800; color: #1b2559; margin-top: 2px;">
                        {{ $rosterTarget['waka']['nama'] ?? 'Belum Ditentukan' }}
                    </h3>
                    <div style="font-size: 12px; color: #64748b;">NIP: {{ $rosterTarget['waka']['nip'] ?? '-' }}</div>
                </div>
            </div>
            <span style="background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px;">
                <i class="fa-solid fa-check-circle"></i> Penanggung Jawab Harian
            </span>
        </div>

        <!-- Shift Grid (Pagi & Siang) -->
        <div class="roster-grid">
            <!-- Shift Pagi -->
            <div class="roster-card">
                <div class="roster-header pagi">
                    <div class="roster-header-title">
                        <i class="fa-solid fa-sun" style="color: #f59e0b;"></i>
                        <span>Shift Pagi</span>
                    </div>
                    <span class="roster-time-badge">07.00 s.d 11.00 WIB</span>
                </div>
                <div class="roster-body">
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #b45309; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">
                            Koordinator Piket KBM Pagi
                        </span>
                        <div class="koor-badge-box">
                            <div class="user-avatar-circle gold">
                                <i class="fa-solid fa-crown"></i>
                            </div>
                            <div style="flex: 1;">
                                <strong style="font-size: 14px; color: #1b2559; display: block;">
                                    {{ $rosterTarget['pagi_koordinator']->nama_guru ?? '-' }}
                                </strong>
                                <span style="font-size: 11.5px; color: #64748b;">
                                    NIP: {{ $rosterTarget['pagi_koordinator']->nip ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">
                            Anggota Petugas Piket KBM (3 Orang)
                        </span>
                        <div class="petugas-list">
                            @forelse($rosterTarget['pagi_petugas'] as $idx => $p)
                            <div class="petugas-item">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 24px; height: 24px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 800;">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <div style="font-size: 13px; font-weight: 700; color: #1b2559;">{{ $p->nama_guru }}</div>
                                        <div style="font-size: 11px; color: #64748b;">NIP: {{ $p->nip ?? '-' }}</div>
                                    </div>
                                </div>
                                <span style="font-size: 11px; font-weight: 700; color: #2b43b9; background: #eef2ff; padding: 2px 8px; border-radius: 6px;">
                                    Petugas {{ $idx + 1 }}
                                </span>
                            </div>
                            @empty
                            <div style="text-align: center; color: #94a3b8; font-size: 12px; padding: 10px;">Belum ada data petugas.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shift Siang -->
            <div class="roster-card">
                <div class="roster-header siang">
                    <div class="roster-header-title">
                        <i class="fa-solid fa-cloud-sun" style="color: #3b82f6;"></i>
                        <span>Shift Siang</span>
                    </div>
                    <span class="roster-time-badge">11.00 s.d 15.00 WIB</span>
                </div>
                <div class="roster-body">
                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #2563eb; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">
                            Koordinator Piket KBM Siang
                        </span>
                        <div class="koor-badge-box">
                            <div class="user-avatar-circle blue">
                                <i class="fa-solid fa-crown"></i>
                            </div>
                            <div style="flex: 1;">
                                <strong style="font-size: 14px; color: #1b2559; display: block;">
                                    {{ $rosterTarget['siang_koordinator']->nama_guru ?? '-' }}
                                </strong>
                                <span style="font-size: 11.5px; color: #64748b;">
                                    NIP: {{ $rosterTarget['siang_koordinator']->nip ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">
                            Anggota Petugas Piket KBM (3 Orang)
                        </span>
                        <div class="petugas-list">
                            @forelse($rosterTarget['siang_petugas'] as $idx => $p)
                            <div class="petugas-item">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="width: 24px; height: 24px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 800;">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <div style="font-size: 13px; font-weight: 700; color: #1b2559;">{{ $p->nama_guru }}</div>
                                        <div style="font-size: 11px; color: #64748b;">NIP: {{ $p->nip ?? '-' }}</div>
                                    </div>
                                </div>
                                <span style="font-size: 11px; font-weight: 700; color: #2b43b9; background: #eef2ff; padding: 2px 8px; border-radius: 6px;">
                                    Petugas {{ $idx + 1 }}
                                </span>
                            </div>
                            @empty
                            <div style="text-align: center; color: #94a3b8; font-size: 12px; padding: 10px;">Belum ada data petugas.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

{{-- TAB 2: MATRIKS SIKLUS A --}}
@elseif($activeTab === 'siklus_a')
    <div class="matrix-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 17px; font-weight: 800; color: #1b2559; display: flex; align-items: center; gap: 8px;">
                    <span class="cycle-badge a">Siklus A</span>
                    Matriks Jadwal Piket KBM (Minggu Ganjil)
                </h3>
                <p style="font-size: 13px; color: #64748b; margin-top: 2px;">
                    Berlaku pada Minggu 1, Minggu 3, dan Minggu 5 setiap bulannya
                </p>
            </div>
            <a href="{{ route('guru-piket.jadwal.cetak') }}" target="_blank" class="btn-action-primary" style="padding: 8px 14px; font-size: 12.5px; text-decoration: none;">
                <i class="fa-solid fa-print"></i> Cetak Format Dokumen SK
            </a>
        </div>

        <div class="table-responsive-wrap">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Hari</th>
                        <th>Petugas Piket Pagi<br><small style="text-transform:none; font-weight:600;">( 07.00 s.d 11.00 )</small></th>
                        <th>Koordinator Pagi<br><small style="text-transform:none; font-weight:600;">( 07.00 s.d 11.00 )</small></th>
                        <th>Petugas Piket Siang<br><small style="text-transform:none; font-weight:600;">( 11.00 s.d 15.00 )</small></th>
                        <th>Koordinator Siang<br><small style="text-transform:none; font-weight:600;">( 11.00 s.d 15.00 )</small></th>
                        <th style="width: 160px;">Piket Waka</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hariOrder as $h)
                    @php $row = $siklusAData[$h] ?? null; @endphp
                    <tr>
                        <td style="text-align: center; vertical-align: middle; font-weight: 800; background: #f8fafc; color: #1b2559;">
                            {{ $h }}
                        </td>
                        <td>
                            <ol style="margin-left: 18px; margin-bottom: 0; line-height: 1.6;">
                                @foreach($row['pagi_petugas'] as $p)
                                    <li><strong>{{ $p->nama_guru }}</strong></li>
                                @endforeach
                            </ol>
                        </td>
                        <td style="background: #fffbeb; font-weight: 700; color: #92400e; vertical-align: middle;">
                            {{ $row['pagi_koordinator']->nama_guru ?? '-' }}
                        </td>
                        <td>
                            <ol style="margin-left: 18px; margin-bottom: 0; line-height: 1.6;">
                                @foreach($row['siang_petugas'] as $p)
                                    <li><strong>{{ $p->nama_guru }}</strong></li>
                                @endforeach
                            </ol>
                        </td>
                        <td style="background: #eef2ff; font-weight: 700; color: #1e40af; vertical-align: middle;">
                            {{ $row['siang_koordinator']->nama_guru ?? '-' }}
                        </td>
                        <td style="background: #f0fdf4; font-weight: 700; color: #166534; vertical-align: middle; text-align: center;">
                            {{ $row['waka']->piket_waka_nama ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

{{-- TAB 3: MATRIKS SIKLUS B --}}
@elseif($activeTab === 'siklus_b')
    <div class="matrix-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 17px; font-weight: 800; color: #1b2559; display: flex; align-items: center; gap: 8px;">
                    <span class="cycle-badge b">Siklus B</span>
                    Matriks Jadwal Piket KBM (Minggu Genap)
                </h3>
                <p style="font-size: 13px; color: #64748b; margin-top: 2px;">
                    Berlaku pada Minggu 2 dan Minggu 4 setiap bulannya
                </p>
            </div>
            <a href="{{ route('guru-piket.jadwal.cetak') }}" target="_blank" class="btn-action-primary" style="padding: 8px 14px; font-size: 12.5px; text-decoration: none;">
                <i class="fa-solid fa-print"></i> Cetak Format Dokumen SK
            </a>
        </div>

        <div class="table-responsive-wrap">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Hari</th>
                        <th>Petugas Piket Pagi<br><small style="text-transform:none; font-weight:600;">( 07.00 s.d 11.00 )</small></th>
                        <th>Koordinator Pagi<br><small style="text-transform:none; font-weight:600;">( 07.00 s.d 11.00 )</small></th>
                        <th>Petugas Piket Siang<br><small style="text-transform:none; font-weight:600;">( 11.00 s.d 15.00 )</small></th>
                        <th>Koordinator Siang<br><small style="text-transform:none; font-weight:600;">( 11.00 s.d 15.00 )</small></th>
                        <th style="width: 160px;">Piket Waka</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hariOrder as $h)
                    @php $row = $siklusBData[$h] ?? null; @endphp
                    <tr>
                        <td style="text-align: center; vertical-align: middle; font-weight: 800; background: #f8fafc; color: #1b2559;">
                            {{ $h }}
                        </td>
                        <td>
                            <ol style="margin-left: 18px; margin-bottom: 0; line-height: 1.6;">
                                @foreach($row['pagi_petugas'] as $p)
                                    <li><strong>{{ $p->nama_guru }}</strong></li>
                                @endforeach
                            </ol>
                        </td>
                        <td style="background: #fffbeb; font-weight: 700; color: #92400e; vertical-align: middle;">
                            {{ $row['pagi_koordinator']->nama_guru ?? '-' }}
                        </td>
                        <td>
                            <ol style="margin-left: 18px; margin-bottom: 0; line-height: 1.6;">
                                @foreach($row['siang_petugas'] as $p)
                                    <li><strong>{{ $p->nama_guru }}</strong></li>
                                @endforeach
                            </ol>
                        </td>
                        <td style="background: #eef2ff; font-weight: 700; color: #1e40af; vertical-align: middle;">
                            {{ $row['siang_koordinator']->nama_guru ?? '-' }}
                        </td>
                        <td style="background: #f0fdf4; font-weight: 700; color: #166534; vertical-align: middle; text-align: center;">
                            {{ $row['waka']->piket_waka_nama ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection
