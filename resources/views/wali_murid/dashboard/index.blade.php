@extends('layouts.wali_murid')

@section('title', 'Beranda - Portal Wali Murid')
@section('header_title', 'Selamat Datang 👋')
@section('header_subtitle', 'Pantau kehadiran dan aktivitas belajar putra-putri Anda')

@section('styles')
<style>
    /* ── KPI Grid ── */
    .kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 24px; }
    .kpi-card {
        background: white; border-radius: 18px; padding: 20px 18px;
        display: flex; flex-direction: column; gap: 10px;
        box-shadow: 0 2px 12px rgba(43,67,185,0.06); border: 1px solid #e8edf8;
        transition: transform 0.2s, box-shadow 0.2s; animation: fadeIn 0.4s ease-out;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(43,67,185,0.12); }
    .kpi-icon { width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .kpi-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-value { font-size: 28px; font-weight: 800; color: #1b2559; line-height: 1; }
    .kpi-sub { font-size: 11.5px; color: #94a3b8; font-weight: 500; }

    /* ── Section Grid ── */
    .section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    .card-box {
        background: white; border-radius: 18px; padding: 22px;
        box-shadow: 0 2px 12px rgba(43,67,185,0.06); border: 1px solid #e8edf8;
        animation: fadeIn 0.5s ease-out;
    }
    .card-box-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .card-box-title { font-size: 15px; font-weight: 800; color: #1b2559; display: flex; align-items: center; gap: 8px; }
    .card-box-title .icon-badge { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
    .view-all-link { font-size: 12.5px; font-weight: 700; color: #2b43b9; text-decoration: none; }
    .view-all-link:hover { text-decoration: underline; }

    /* ── Kehadiran Ring ── */
    .progress-ring-wrap { display: flex; align-items: center; gap: 20px; padding: 16px; background: #f8fafc; border-radius: 14px; margin-bottom: 12px; }
    .ring-container { position: relative; width: 80px; height: 80px; flex-shrink: 0; }
    .ring-container svg { transform: rotate(-90deg); }
    .ring-label { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: 14px; font-weight: 800; color: #1b2559; }
    .attendance-legend { flex: 1; display: flex; flex-direction: column; gap: 8px; }
    .legend-item { display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-label { flex: 1; margin-left: 8px; color: #475569; font-weight: 500; }
    .legend-count { font-weight: 700; color: #1b2559; }

    /* ── Presensi Item ── */
    .presensi-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .presensi-item:last-child { border-bottom: none; }
    .presensi-mapel { flex: 1; }
    .presensi-mapel-name { font-size: 13.5px; font-weight: 700; color: #1b2559; }
    .presensi-guru { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }

    /* ── Status Badges ── */
    .badge-status { padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; }
    .badge-hadir      { background: #d1fae5; color: #065f46; }
    .badge-alpha      { background: #fee2e2; color: #991b1b; }
    .badge-sakit      { background: #fef3c7; color: #92400e; }
    .badge-izin       { background: #dbeafe; color: #1e40af; }
    .badge-dispensasi { background: #ede9fe; color: #5b21b6; }
    .badge-default    { background: #f1f5f9; color: #64748b; }

    /* ── Jadwal Items ── */
    .jadwal-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .jadwal-item:last-child { border-bottom: none; }
    .jadwal-time { font-size: 11.5px; font-weight: 700; color: #2b43b9; background: #eef2ff; padding: 4px 8px; border-radius: 8px; white-space: nowrap; }
    .jadwal-detail { flex: 1; }
    .jadwal-mapel { font-size: 13.5px; font-weight: 700; color: #1b2559; }
    .jadwal-guru { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }
    .jadwal-jam-ke { width: 28px; height: 28px; border-radius: 8px; background: #f8fafc; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #64748b; flex-shrink: 0; }

    /* ── Materi Items ── */
    .materi-item { padding: 12px; background: #f8fafc; border-radius: 12px; margin-bottom: 8px; border-left: 3px solid #3d56b2; }
    .materi-item:last-child { margin-bottom: 0; }
    .materi-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
    .materi-mapel { font-size: 13px; font-weight: 700; color: #1b2559; }
    .materi-jam { font-size: 11px; color: #94a3b8; }
    .materi-text { font-size: 12.5px; color: #475569; line-height: 1.5; }

    /* ── Dispensasi ── */
    .disp-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .disp-item:last-child { border-bottom: none; }
    .disp-date { font-size: 11.5px; color: #94a3b8; font-weight: 600; min-width: 80px; }
    .disp-info { flex: 1; }
    .disp-alasan { font-size: 13px; font-weight: 600; color: #1b2559; }
    .badge-disp-pending  { background: #fef3c7; color: #92400e; }
    .badge-disp-approved { background: #d1fae5; color: #065f46; }
    .badge-disp-rejected { background: #fee2e2; color: #991b1b; }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 30px 10px; color: #94a3b8; }
    .empty-state i { font-size: 32px; margin-bottom: 10px; display: block; }
    .empty-state p { font-size: 13px; font-weight: 500; }

    /* ── Info Banner ── */
    .alert-info {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 1px solid #c7d2fe; border-radius: 14px;
        padding: 14px 18px; font-size: 13px; color: #3730a3; font-weight: 600;
        margin-bottom: 6px; display: flex; align-items: center; gap: 10px;
    }
    .kehadiran-notice { padding: 10px 14px; border-radius: 10px; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .kehadiran-notice.good   { background: #d1fae5; color: #065f46; }
    .kehadiran-notice.medium { background: #fef3c7; color: #92400e; }
    .kehadiran-notice.bad    { background: #fee2e2; color: #991b1b; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 900px)  { .section-grid { grid-template-columns: 1fr; } }
    @media (max-width: 640px)  { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection

@section('content')
{{-- Siswa Info Card --}}
<div class="siswa-info-card">
    <div class="siswa-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
    <div class="siswa-detail">
        <div class="siswa-name">{{ $siswa->nama_lengkap }}</div>
        <div class="siswa-meta">
            NIS: {{ $siswa->nis ?? '-' }}
            &nbsp;•&nbsp; Kelas: {{ $siswa->kelas?->nama_kelas ?? '-' }}
            @if($siswa->kelas?->waliKelas?->nama_guru)
                &nbsp;•&nbsp; Wali Kelas: {{ $siswa->kelas->waliKelas->nama_guru }}
            @endif
        </div>
    </div>
    <div style="font-size:12px;color:#3730a3;font-weight:700;background:#e0e7ff;padding:6px 14px;border-radius:10px;white-space:nowrap;">
        {{ $todayFormatted }}
    </div>
</div>

{{-- KPI Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#d1fae5;color:#059669;"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-label">Total Hadir</div>
        <div class="kpi-value">{{ $hadirCount }}</div>
        <div class="kpi-sub">sesi kehadiran</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-bed"></i></div>
        <div class="kpi-label">Sakit</div>
        <div class="kpi-value">{{ $sakitCount }}</div>
        <div class="kpi-sub">sesi ketidakhadiran</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-envelope-open-text"></i></div>
        <div class="kpi-label">Izin</div>
        <div class="kpi-value">{{ $izinCount }}</div>
        <div class="kpi-sub">sesi ketidakhadiran</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fee2e2;color:#ef4444;"><i class="fa-solid fa-circle-xmark"></i></div>
        <div class="kpi-label">Alpha</div>
        <div class="kpi-value">{{ $alphaCount }}</div>
        <div class="kpi-sub">tanpa keterangan</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eef2ff;color:#4338ca;"><i class="fa-solid fa-percent"></i></div>
        <div class="kpi-label">% Kehadiran</div>
        <div class="kpi-value">{{ $pctKehadiran }}<span style="font-size:16px;">%</span></div>
        <div class="kpi-sub">dari {{ $totalSesi }} total sesi</div>
    </div>
</div>

{{-- Statistik + Presensi Hari Ini --}}
<div class="section-grid">
    {{-- Statistik Kehadiran --}}
    <div class="card-box">
        <div class="card-box-header">
            <div class="card-box-title">
                <div class="icon-badge" style="background:#eef2ff;color:#4338ca;"><i class="fa-solid fa-chart-pie"></i></div>
                Statistik Kehadiran
            </div>
            <a href="{{ route('wali-murid.presensi') }}" class="view-all-link">Lihat Semua →</a>
        </div>
        @php $circumference = 2 * M_PI * 32; $dashOffset = $circumference * (1 - $pctKehadiran / 100); @endphp
        <div class="progress-ring-wrap">
            <div class="ring-container">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="32" fill="none" stroke="#e8edf8" stroke-width="10"/>
                    <circle cx="40" cy="40" r="32" fill="none" stroke="#3d56b2" stroke-width="10"
                        stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $dashOffset }}"
                        stroke-linecap="round"/>
                </svg>
                <div class="ring-label">{{ $pctKehadiran }}%</div>
            </div>
            <div class="attendance-legend">
                <div class="legend-item"><span class="legend-dot" style="background:#059669;"></span><span class="legend-label">Hadir</span><span class="legend-count">{{ $hadirCount }}</span></div>
                <div class="legend-item"><span class="legend-dot" style="background:#f59e0b;"></span><span class="legend-label">Sakit</span><span class="legend-count">{{ $sakitCount }}</span></div>
                <div class="legend-item"><span class="legend-dot" style="background:#3b82f6;"></span><span class="legend-label">Izin</span><span class="legend-count">{{ $izinCount }}</span></div>
                <div class="legend-item"><span class="legend-dot" style="background:#ef4444;"></span><span class="legend-label">Alpha</span><span class="legend-count">{{ $alphaCount }}</span></div>
                <div class="legend-item"><span class="legend-dot" style="background:#8b5cf6;"></span><span class="legend-label">Dispensasi</span><span class="legend-count">{{ $dispCount }}</span></div>
            </div>
        </div>
        @if($pctKehadiran >= 85)
            <div class="kehadiran-notice good"><i class="fa-solid fa-star"></i> Kehadiran sangat baik! Pertahankan terus 🎉</div>
        @elseif($pctKehadiran >= 75)
            <div class="kehadiran-notice medium"><i class="fa-solid fa-triangle-exclamation"></i> Tingkatkan kehadiran agar tidak tertinggal pelajaran</div>
        @else
            <div class="kehadiran-notice bad"><i class="fa-solid fa-circle-exclamation"></i> Perhatian! Kehadiran di bawah batas minimum (75%)</div>
        @endif
    </div>

    {{-- Presensi Hari Ini --}}
    <div class="card-box">
        <div class="card-box-header">
            <div class="card-box-title">
                <div class="icon-badge" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-clipboard-check"></i></div>
                Presensi Hari Ini
            </div>
            <span style="font-size:11.5px;background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:8px;font-weight:600;">{{ $presensiHariIni->count() }} sesi</span>
        </div>
        @forelse($presensiHariIni as $p)
            <div class="presensi-item">
                <div class="presensi-mapel">
                    <div class="presensi-mapel-name">{{ $p->jurnal?->mapel?->nama_mapel ?? 'Mata Pelajaran' }}</div>
                    <div class="presensi-guru">{{ $p->jurnal?->guru?->nama_guru ?? 'Guru' }} &bull; Jam ke-{{ $p->jurnal?->jam_ke ?? '-' }}</div>
                </div>
                <span class="badge-status badge-{{ strtolower($p->status ?? 'default') }}">{{ $p->status }}</span>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-regular fa-calendar-xmark"></i>
                <p>Belum ada data presensi hari ini</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Jadwal + Materi Hari Ini --}}
<div class="section-grid">
    {{-- Jadwal Hari Ini --}}
    <div class="card-box">
        <div class="card-box-header">
            <div class="card-box-title">
                <div class="icon-badge" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-calendar-day"></i></div>
                Jadwal Hari Ini
            </div>
            <a href="{{ route('wali-murid.jadwal') }}" class="view-all-link">Semua Jadwal →</a>
        </div>
        @forelse($jadwalHariIni as $j)
            <div class="jadwal-item">
                <div class="jadwal-jam-ke">{{ $j->jam_ke }}</div>
                <div class="jadwal-detail">
                    <div class="jadwal-mapel">{{ $j->mapel?->nama_mapel ?? '-' }}</div>
                    <div class="jadwal-guru">{{ $j->guru?->nama_guru ?? '-' }}</div>
                </div>
                <div class="jadwal-time">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-regular fa-calendar"></i>
                <p>Tidak ada jadwal hari ini</p>
            </div>
        @endforelse
    </div>

    {{-- Materi & Jurnal Hari Ini --}}
    <div class="card-box">
        <div class="card-box-header">
            <div class="card-box-title">
                <div class="icon-badge" style="background:#ede9fe;color:#7c3aed;"><i class="fa-solid fa-book-open"></i></div>
                Materi Hari Ini
            </div>
            <a href="{{ route('wali-murid.jurnal') }}" class="view-all-link">Riwayat →</a>
        </div>
        @forelse($jurnalKelasHariIni as $j)
            <div class="materi-item">
                <div class="materi-header">
                    <div class="materi-mapel">{{ $j->mapel?->nama_mapel ?? 'Mata Pelajaran' }}</div>
                    <div class="materi-jam">Jam {{ $j->jam_ke }}</div>
                </div>
                <div class="materi-text">{{ Str::limit($j->materi ?? 'Belum ada materi yang dicatat', 100) }}</div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-regular fa-file-lines"></i>
                <p>Belum ada materi tercatat hari ini</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Riwayat Dispensasi Terbaru --}}
@if($dispensasiTerbaru->count() > 0)
<div class="card-box" style="margin-bottom:24px;">
    <div class="card-box-header">
        <div class="card-box-title">
            <div class="icon-badge" style="background:#fce7f3;color:#be185d;"><i class="fa-solid fa-file-circle-check"></i></div>
            Riwayat Dispensasi Terbaru
        </div>
        <a href="{{ route('wali-murid.dispensasi') }}" class="view-all-link">Lihat Semua →</a>
    </div>
    @foreach($dispensasiTerbaru as $d)
        <div class="disp-item">
            <div class="disp-date">{{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}</div>
            <div class="disp-info">
                <div class="disp-alasan">{{ Str::limit($d->alasan ?? '-', 60) }}</div>
                <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Jam {{ $d->jam_keluar ?? '-' }} – {{ $d->jam_kembali ?? '-' }}</div>
            </div>
            @php $dStatus = strtolower($d->status ?? 'pending'); @endphp
            <span class="badge-status {{ match($dStatus) { 'disetujui' => 'badge-disp-approved', 'ditolak' => 'badge-disp-rejected', default => 'badge-disp-pending' } }}">
                {{ ucfirst($d->status ?? 'Pending') }}
            </span>
        </div>
    @endforeach
</div>
@endif
@endsection
