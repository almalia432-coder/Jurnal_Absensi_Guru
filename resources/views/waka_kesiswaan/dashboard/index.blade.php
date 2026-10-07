@extends('layouts.waka_kesiswaan')

@section('title', 'Dashboard Waka Kesiswaan & Kedisiplinan')
@section('header_title', 'Dashboard Kesiswaan & Kedisiplinan')
@section('header_subtitle', 'Pantau ketertiban siswa, presensi harian, dan izin dispensasi ' . $todayFormatted)

@section('styles')
<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    .kpi-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .kpi-icon.blue { background: #eef2ff; color: #2b43b9; }
    .kpi-icon.green { background: #ecfdf5; color: #10b981; }
    .kpi-icon.amber { background: #fffbeb; color: #f59e0b; }
    .kpi-icon.red { background: #fef2f2; color: #ef4444; }

    .kpi-info h4 { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .kpi-info .val { font-size: 24px; font-weight: 900; color: #0f172a; line-height: 1; }
    .kpi-info .sub { font-size: 11px; color: #64748b; font-weight: 600; margin-top: 4px; }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .card-box {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .card-box-header {
        padding: 18px 22px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-box-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-box-body { padding: 20px 22px; }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .custom-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        padding: 10px 14px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .custom-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }

    .badge-status {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        display: inline-block;
    }
    .badge-status.Disetujui { background: #dcfce7; color: #166534; }
    .badge-status.Menunggu { background: #fef3c7; color: #b45309; }
    .badge-status.Ditolak { background: #fee2e2; color: #991b1b; }
    .badge-status.Selesai { background: #e0e7ff; color: #3730a3; }

    @media (max-width: 991px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .content-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon blue"><i class="fa-solid fa-users"></i></div>
        <div class="kpi-info">
            <h4>Total Siswa</h4>
            <div class="val">{{ number_format($totalSiswa, 0, ',', '.') }}</div>
            <div class="sub">Terdaftar Aktif</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon green"><i class="fa-solid fa-user-check"></i></div>
        <div class="kpi-info">
            <h4>Hadir Hari Ini</h4>
            <div class="val">{{ number_format($hadirToday, 0, ',', '.') }}</div>
            <div class="sub">Tercatat di KBM</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber"><i class="fa-solid fa-ticket-simple"></i></div>
        <div class="kpi-info">
            <h4>Dispensasi Hari Ini</h4>
            <div class="val">{{ $dispensasiCount }}</div>
            <div class="sub">{{ $dispensasiDisetujui }} Disetujui / {{ $dispensasiMenunggu }} Menunggu</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon red"><i class="fa-solid fa-user-xmark"></i></div>
        <div class="kpi-info">
            <h4>Alpha Hari Ini</h4>
            <div class="val">{{ $alphaToday }}</div>
            <div class="sub">Siswa Tanpa Keterangan</div>
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Kolom Kiri: Pemantauan Dispensasi Siswa Hari Ini -->
    <div class="card-box">
        <div class="card-box-header">
            <div class="card-box-title">
                <i class="fa-solid fa-ticket" style="color: #2563eb;"></i>
                <span>Dispensasi Siswa Hari Ini</span>
            </div>
            <a href="{{ route('waka-kesiswaan.dispensasi') }}" style="font-size: 12.5px; font-weight: 700; color: #2563eb; text-decoration: none;">
                Lihat Semua <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="card-box-body" style="padding: 0;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Siswa / Kelas</th>
                        <th>Keperluan</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispensasiList as $d)
                    <tr>
                        <td>
                            <strong style="color:#0f172a;">{{ $d->siswa->nama_lengkap ?? '-' }}</strong>
                            <div style="font-size: 11px; color:#64748b;">{{ $d->siswa->kelas->nama_kelas ?? '-' }} • NISN: {{ $d->siswa->nisn ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="max-width:200px;line-height:1.3;">{{ $d->alasan ?? '-' }}</div>
                        </td>
                        <td>
                            {{ $d->jam_keluar ? \Carbon\Carbon::parse($d->jam_keluar)->format('H:i') : '-' }} s/d {{ $d->jam_kembali ? \Carbon\Carbon::parse($d->jam_kembali)->format('H:i') : 'Selesai' }}
                        </td>
                        <td>
                            <span class="badge-status {{ $d->status }}">{{ $d->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 36px 20px;">
                            <i class="fa-regular fa-calendar-check" style="font-size: 28px; margin-bottom: 8px; display: block;"></i>
                            Tidak ada pengajuan dispensasi siswa hari ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kolom Kanan: Siswa Butuh Pembinaan (Alpha Tertinggi Bulan Ini) -->
    <div class="card-box">
        <div class="card-box-header">
            <div class="card-box-title">
                <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i>
                <span>Perhatian Kedisiplinan</span>
            </div>
            <a href="{{ route('waka-kesiswaan.kedisiplinan') }}" style="font-size: 12px; font-weight: 700; color: #2563eb; text-decoration: none;">
                Rekap <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
        <div class="card-box-body" style="padding: 16px;">
            <p style="font-size: 12px; color: #64748b; margin-bottom: 14px;">
                Siswa dengan akumulasi Alpha tertinggi bulan ini yang memerlukan koordinasi dengan Wali Kelas &amp; BK:
            </p>
            @forelse($siswaAlphaTop as $sw)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8fafc;border-radius:12px;margin-bottom:8px;border:1px solid #eef2f7;">
                <div>
                    <div style="font-weight:700;font-size:13px;color:#0f172a;">{{ $sw->siswa->nama_lengkap ?? 'Siswa' }}</div>
                    <div style="font-size:11px;color:#64748b;">{{ $sw->siswa->kelas->nama_kelas ?? '-' }}</div>
                </div>
                <span style="background:#fee2e2;color:#991b1b;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:800;">
                    {{ $sw->total_alpha }}x Alpha
                </span>
            </div>
            @empty
            <div style="text-align:center;padding:24px 10px;color:#94a3b8;font-size:12.5px;">
                <i class="fa-solid fa-circle-check" style="font-size:24px;color:#10b981;margin-bottom:6px;display:block;"></i>
                Kedisiplinan siswa terjaga baik bulan ini.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
