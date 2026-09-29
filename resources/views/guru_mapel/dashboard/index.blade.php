@extends('layouts.guru_mapel')

@section('title', 'Dashboard - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Dashboard Guru Mata Pelajaran')
@section('header_subtitle', 'Kelola jadwal mengajar, isi jurnal pembelajaran, dan pantau presensi siswa harian')

@section('header_extra')
    <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 14px; padding: 8px 16px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-chalkboard-user" style="font-size: 20px; color: #a5b4fc;"></i>
        <div>
            <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; font-weight: 700;">Guru Pengampu</div>
            <div style="font-size: 13.5px; font-weight: 800; color: #ffffff;">{{ $guru->nama_lengkap ?? (Auth::user()->name ?? 'Guru Mapel') }}</div>
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

    .stat-icon.blue   { background-color: #eef2ff; color: #2b43b9; }
    .stat-icon.green  { background-color: #e6f9f0; color: #10b981; }
    .stat-icon.orange { background-color: #fff7ed; color: #f97316; }
    .stat-icon.red    { background-color: #fef2f2; color: #ef4444; }
    .stat-icon.dark   { background-color: #f1f5f9; color: #334155; }

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

    .stat-badge-green { color: #10b981; font-weight: 700; }
    .stat-badge-red   { color: #ef4444; font-weight: 700; }

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

    /* Section Cards */
    .section-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
        margin-bottom: 24px;
    }

    .section-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #1b2559;
    }

    /* Jadwal Cards Grid */
    .jadwal-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .jadwal-card {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        transition: all 0.2s ease;
    }

    .jadwal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    .jadwal-card.filled {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .jadwal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .jadwal-class {
        font-size: 16px;
        font-weight: 800;
        color: #1b2559;
    }

    .jadwal-mapel {
        font-size: 13px;
        font-weight: 700;
        color: #2b43b9;
        margin-top: 2px;
    }

    .status-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-badge.success { background: #e6f9f0; color: #10b981; }
    .status-badge.warning { background: #fff7ed; color: #f97316; }
    .status-badge.info    { background: #e0f2fe; color: #0369a1; }
    .status-badge.danger  { background: #fef2f2; color: #ef4444; }

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

    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); }
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
        .btn-action-primary, .btn-action-secondary {
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

@if(isset($latestIzinNotice) && $latestIzinNotice)
    @php
        $noticeKey = 'dismissed_izin_' . $latestIzinNotice->id . '_' . ($latestIzinNotice->updated_at ? $latestIzinNotice->updated_at->timestamp : '0');
    @endphp
    @if($latestIzinNotice->status === 'Ditolak')
    <div id="noticeBanner_{{ $latestIzinNotice->id }}" class="izin-notice-banner" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 2px solid #ef4444; border-radius: 16px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.15); position: relative;">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1; min-width: 260px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ef4444; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 15px; color: #991b1b; margin-bottom: 2px;">
                    Pemberitahuan: Permintaan Izin Anda Tidak Disetujui!
                </div>
                <div style="font-size: 13px; color: #7f1d1d;">
                    Pengajuan izin kategori <strong>{{ $latestIzinNotice->jenis_izin }}</strong> untuk tanggal <strong>{{ Carbon\Carbon::parse($latestIzinNotice->tanggal_mulai)->translatedFormat('d M Y') }}</strong> telah ditolak oleh <strong>{{ $latestIzinNotice->penolak_label }}</strong>.
                    @if($latestIzinNotice->ditolak_catatan)
                        <br><em>Catatan: "{{ $latestIzinNotice->ditolak_catatan }}"</em>
                    @endif
                </div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="background: #dc2626; color: #ffffff; padding: 7px 14px; border-radius: 10px; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-chalkboard-user"></i> Harap Melanjutkan KBM
            </span>
            <a href="{{ route('guru-mapel.izin') }}" style="background: #ffffff; border: 1.5px solid #fca5a5; color: #991b1b; padding: 7px 14px; border-radius: 10px; font-weight: 700; font-size: 12px; text-decoration: none;">
                Lihat Detail Izin
            </a>
            <button type="button" onclick="dismissIzinNotice('{{ $latestIzinNotice->id }}', '{{ $noticeKey }}')" title="Tutup Pemberitahuan" style="background: rgba(239, 68, 68, 0.15); border: none; width: 34px; height: 34px; border-radius: 10px; color: #991b1b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: all 0.2s ease;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
    @elseif($latestIzinNotice->status === 'Disetujui')
    <div id="noticeBanner_{{ $latestIzinNotice->id }}" class="izin-notice-banner" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1.5px solid #22c55e; border-radius: 16px; padding: 16px 22px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; position: relative;">
        <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 260px;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: #16a34a; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 14px; color: #166534; margin-bottom: 2px;">
                    Izin Mengajar Telah Disetujui Penuh
                </div>
                <div style="font-size: 12.5px; color: #15803d;">
                    Pengajuan izin <strong>{{ $latestIzinNotice->jenis_izin }}</strong> Anda untuk tanggal <strong>{{ Carbon\Carbon::parse($latestIzinNotice->tanggal_mulai)->translatedFormat('d M Y') }}</strong> telah disetujui resmi oleh Guru Piket, Waka SDM, dan Kepala Sekolah.
                </div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('guru-mapel.izin') }}" style="background: #ffffff; border: 1.5px solid #86efac; color: #15803d; padding: 6px 12px; border-radius: 10px; font-weight: 700; font-size: 12px; text-decoration: none;">
                Lihat Riwayat
            </a>
            <button type="button" onclick="dismissIzinNotice('{{ $latestIzinNotice->id }}', '{{ $noticeKey }}')" title="Tutup Pemberitahuan" style="background: rgba(22, 163, 74, 0.15); border: none; width: 34px; height: 34px; border-radius: 10px; color: #166534; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: all 0.2s ease;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
    @endif
    <script>
        (function() {
            if (localStorage.getItem('{{ $noticeKey }}') === '1') {
                const el = document.getElementById('noticeBanner_{{ $latestIzinNotice->id }}');
                if (el) el.style.display = 'none';
            }
        })();
        function dismissIzinNotice(id, key) {
            localStorage.setItem(key, '1');
            const el = document.getElementById('noticeBanner_' + id);
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'scale(0.98)';
                el.style.transition = 'all 0.25s ease';
                setTimeout(() => el.remove(), 250);
            }
        }
    </script>
@endif

@php
    $rosterDuty = \App\Models\JadwalPiketKbm::getRosterForDate(\Carbon\Carbon::today());
    $wakaNamaToday = $rosterDuty['waka']['nama'] ?? '';
    $wakaNipToday = $rosterDuty['waka']['nip'] ?? '';
    $userNipGuru = Auth::user()->guru->nip ?? '';
    $isWakaPiketToday = ($userNipGuru && $userNipGuru === $wakaNipToday) || ($wakaNamaToday && str_contains(Auth::user()->name, explode(',', $wakaNamaToday)[0]));
    $pendingDispenCount = \App\Models\DispensasiSiswa::where('status', 'Menunggu')->whereDate('tanggal', \Carbon\Carbon::today())->count();
@endphp

@if($isWakaPiketToday || (Auth::user()->waka && $pendingDispenCount > 0))
<div style="background: linear-gradient(135deg, #1e3a8a 0%, #2b43b9 55%, #1d4ed8 100%); border-radius: 18px; padding: 20px 24px; color: white; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; box-shadow: 0 8px 24px rgba(43, 67, 185, 0.22); border: 1px solid rgba(255,255,255,0.2);">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 24px; color: #ffffff; flex-shrink: 0;">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <div>
            <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #bfdbfe;">
                Penugasan Pimpinan Hari Ini
            </span>
            <h3 style="font-size: 17px; font-weight: 800; margin: 2px 0 4px 0; color: #ffffff;">
                Anda Bertugas Sebagai Waka Piket KBM
            </h3>
            <div style="font-size: 13px; color: #e0e7ff;">
                @if($pendingDispenCount > 0)
                    ⚠️ Terdapat <strong>{{ $pendingDispenCount }} permohonan dispensasi siswa</strong> yang sedang menunggu persetujuan Anda.
                @else
                    Semua permohonan dispensasi siswa hari ini telah ditinjau.
                @endif
            </div>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
        <a href="{{ route('waka-piket.dispensasi') }}" style="background: #ffffff; color: #1e3a8a; padding: 10px 18px; border-radius: 12px; font-weight: 800; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.2s;">
            <i class="fa-solid fa-clipboard-check" style="color: #2b43b9;"></i>
            <span>Tinjau Dispensasi Siswa</span>
            @if($pendingDispenCount > 0)
                <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 20px; font-size: 11px;">{{ $pendingDispenCount }}</span>
            @endif
        </a>
    </div>
</div>
@endif

<!-- 1. Greeting Section & Quick Actions -->
<div class="greeting-section" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 class="greeting-title">Halo, {{ $guru->nama_lengkap ?? (Auth::user()->name ?? 'Guru Mapel') }} 👋</h2>
        <p class="greeting-subtitle">Berikut adalah ringkasan jadwal mengajar dan status pengisian jurnal pembelajaran Anda hari ini ({{ $todayFormatted }}).</p>
    </div>

    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('guru-mapel.jurnal.create') }}" class="btn-action-primary">
            <i class="fa-solid fa-pen-to-square"></i> + Isi Jurnal Mengajar
        </a>
        <a href="{{ route('guru-mapel.jadwal') }}" class="btn-action-secondary">
            <i class="fa-solid fa-calendar-week"></i> Jadwal Mingguan
        </a>
        <a href="{{ route('guru-mapel.izin') }}" class="btn-action-secondary">
            <i class="fa-solid fa-user-clock"></i> Ajukan Izin
        </a>
        @if(Auth::user()->isWaliKelas())
        <a href="{{ route('wali-kelas.dashboard') }}" class="btn-action-secondary" style="border-color:#c7d2fe;background:#eef2ff;color:#4338ca;font-weight:700;">
            <i class="fa-solid fa-chalkboard-user"></i> Kelas Binaan {{ Auth::user()->kelas_binaan ? '('.Auth::user()->kelas_binaan->nama_kelas.')' : '' }}
        </a>
        @endif
    </div>
</div>

<!-- 2. Top 5 Stat Cards Grid -->
<div class="stats-grid">
    <!-- Card 1: Jadwal Hari Ini -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Jadwal Hari Ini</span>
            <div class="stat-icon blue"><i class="fa-solid fa-calendar-day"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $metrics['jadwal_hari_ini'] }} <span style="font-size: 14px; color: #6b7a99; font-weight: 600;">Sesi</span></div>
            <div class="stat-footer">
                @if($metrics['jadwal_hari_ini'] > 0)
                    <span class="stat-badge-green">{{ $hariIni }}</span> KBM Terjadwal
                @else
                    <span style="color: #6b7a99;">Tidak ada jadwal hari ini</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 2: Jurnal Terisi Hari Ini -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Jurnal Terisi</span>
            <div class="stat-icon green"><i class="fa-solid fa-file-circle-check"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $metrics['jurnal_terisi_hari_ini'] }} <span style="font-size: 14px; color: #6b7a99; font-weight: 600;">/ {{ $metrics['jadwal_hari_ini'] }}</span></div>
            <div class="stat-footer">
                @if($metrics['jadwal_hari_ini'] > 0 && $metrics['jurnal_terisi_hari_ini'] >= $metrics['jadwal_hari_ini'])
                    <span class="stat-badge-green">100% Selesai</span> diisi
                @elseif($metrics['jadwal_hari_ini'] > 0)
                    <span class="stat-badge-red">{{ $metrics['jadwal_hari_ini'] - $metrics['jurnal_terisi_hari_ini'] }} Sesi</span> belum diisi
                @else
                    <span class="stat-badge-green">Selesai</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 3: Total Jam Mengajar Mingguan -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Beban Mingguan</span>
            <div class="stat-icon orange"><i class="fa-solid fa-clock"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $metrics['total_jam_mingguan'] }} <span style="font-size: 14px; color: #6b7a99; font-weight: 600;">Jam/Sesi</span></div>
            <div class="stat-footer">
                <span class="stat-badge-green">Aktif</span> di semester berjalan
            </div>
        </div>
    </div>

    <!-- Card 4: Tingkat Kehadiran Siswa -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Kehadiran Siswa</span>
            <div class="stat-icon dark"><i class="fa-solid fa-user-check"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $metrics['tingkat_presensi'] }}%</div>
            <div class="stat-footer">
                <span class="stat-badge-green">Rata-rata</span> di kelas Anda
            </div>
        </div>
    </div>

    <!-- Card 5: Total Jurnal Pembelajaran -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Arsip Jurnal</span>
            <div class="stat-icon blue"><i class="fa-solid fa-book-open"></i></div>
        </div>
        <div>
            <div class="stat-value">{{ $metrics['total_jurnal_semester'] }} <span style="font-size: 14px; color: #6b7a99; font-weight: 600;">Pertemuan</span></div>
            <div class="stat-footer">
                <span class="stat-badge-green">Terekam</span> di database
            </div>
        </div>
    </div>
</div>

@if($isMaju)
@php
    $hariLower = strtolower($hariIni);
    $jenisPembiasaan = $hariLower === 'senin' ? 'Upacara Bendera' : 'Pembiasaan Pagi';
@endphp
<div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1.5px solid #fde68a; border-radius: 16px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.08);">
    <div style="width: 44px; height: 44px; border-radius: 12px; background: #fef08a; color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
        <i class="fa-solid fa-forward-fast"></i>
    </div>
    <div style="flex: 1;">
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 3px;">
            <span style="font-size: 14.5px; font-weight: 800; color: #92400e;">
                Pemberitahuan Guru Piket: {{ $jenisPembiasaan }} Ditiadakan
            </span>
            <span style="background: #fef08a; color: #854d0e; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px; border: 1px solid #facc15;">
                Jam KBM Maju ke 07:00
            </span>
        </div>
        <p style="font-size: 13px; color: #475569; margin: 0; line-height: 1.4;">
            Karena {{ strtolower($jenisPembiasaan) }} ditiadakan, jam pelajaran KBM langsung dimulai pukul <strong>07:00 WIB</strong> (Mapel jam ke-2 maju 1 slot). Jam istirahat tetap sama. Waktu pada kartu jadwal Anda di bawah telah otomatis disesuaikan.
            @if(!empty($statusKbmHariIni->catatan))
                <br><span style="color: #64748b; font-style: italic;">Catatan piket: "{{ $statusKbmHariIni->catatan }}"</span>
            @endif
        </p>
    </div>
</div>
@endif

<!-- 3. Jadwal Mengajar Hari Ini (Actionable Cards) -->
<div class="section-card">
    <div class="section-card-header">
        <div>
            <h3 class="section-title">Jadwal Mengajar Hari Ini ({{ $hariIni }})</h3>
            <span style="font-size: 12px; color: #6b7a99;">Pilih sesi mengajar untuk mengisi jurnal & absensi siswa secara langsung</span>
        </div>
        <a href="{{ route('guru-mapel.jadwal') }}" style="font-size: 13px; font-weight: 700; color: #2b43b9; text-decoration: none;">
            Lihat Jadwal Lengkap <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="jadwal-grid">
        @forelse($jadwalCards as $jd)
        <div class="jadwal-card {{ $jd->is_filled ? 'filled' : '' }}">
            <div>
                <div class="jadwal-header">
                    <div>
                        <div class="jadwal-class">{{ $jd->kelas->nama_kelas ?? '-' }}</div>
                        <div class="jadwal-mapel">{{ $jd->mapel->nama_mapel ?? '-' }}</div>
                    </div>
                    @if($jd->is_filled)
                        <span class="status-badge success"><i class="fa-solid fa-check"></i> Jurnal Terisi</span>
                    @else
                        <span class="status-badge warning"><i class="fa-solid fa-clock"></i> Belum Diisi</span>
                    @endif
                </div>

                <div style="margin-top: 12px; font-size: 12.5px; color: #2b3674; display: flex; flex-direction: column; gap: 4px; border-top: 1px dashed #cbd5e1; padding-top: 8px;">
                    <div>
                        <i class="fa-solid fa-hourglass-half" style="color: #6b7a99; width: 16px;"></i>
                        <strong>Jam ke-{{ $jd->jam_ke }}</strong> ({{ Carbon\Carbon::parse($jd->jam_mulai)->format('H:i') }} - {{ Carbon\Carbon::parse($jd->jam_selesai)->format('H:i') }} WIB)
                        @if(isset($jd->total_jp) && $jd->total_jp > 1)
                            <span style="background: #eef2ff; color: #2b43b9; font-size: 10.5px; font-weight: 800; padding: 2px 6px; border-radius: 6px; margin-left: 6px; border: 1px solid #dbeafe;">
                                {{ $jd->total_jp }} JP
                            </span>
                        @endif
                    </div>
                    <div>
                        <i class="fa-solid fa-layer-group" style="color: #6b7a99; width: 16px;"></i>
                        Jurusan: {{ $jd->kelas->jurusan ?? ($jd->kelas->jurusanRelation->nama_jurusan ?? '-') }}
                    </div>
                </div>
            </div>

            <div style="padding-top: 8px; border-top: 1px solid #f1f5f9;">
                @if($jd->is_filled)
                    <a href="{{ route('guru-mapel.jurnal.show', $jd->jurnal->id_jurnal) }}" class="btn-action-secondary" style="width: 100%; justify-content: center; padding: 7px 12px; font-size: 12px;">
                        <i class="fa-solid fa-eye"></i> Lihat Detail Jurnal
                    </a>
                @else
                    <a href="{{ route('guru-mapel.jurnal.create', ['id_jadwal' => $jd->id_jadwal]) }}" class="btn-action-primary" style="width: 100%; justify-content: center; padding: 7px 12px; font-size: 12px;">
                        <i class="fa-solid fa-pen-to-square"></i> Isi Jurnal Sekarang
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; padding: 36px; text-align: center; color: #6b7a99; background: #f8fafc; border-radius: 12px;">
            <i class="fa-solid fa-mug-hot" style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px;"></i>
            <div style="font-weight: 700; color: #1b2559; font-size: 14px;">Tidak ada jadwal mengajar pada hari {{ $hariIni }}.</div>
            <div style="font-size: 12.5px; color: #6b7a99; margin-top: 2px;">Anda dapat memeriksa jadwal hari lain atau mengisi jurnal manual di menu Isi Jurnal.</div>
        </div>
        @endforelse
    </div>
</div>

<!-- 4. Riwayat Jurnal Terakhir -->
<div class="section-card">
    <div class="section-card-header">
        <div>
            <h3 class="section-title">Riwayat Jurnal Mengajar Terakhir</h3>
            <span style="font-size: 12px; color: #6b7a99;">Arsip sesi pembelajaran yang telah Anda rekam sebelumnya</span>
        </div>
        <a href="{{ route('guru-mapel.jurnal.riwayat') }}" style="font-size: 13px; font-weight: 700; color: #2b43b9; text-decoration: none;">
            Semua Riwayat <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tanggal & Waktu</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Materi Pokok</th>
                    <th>Kehadiran Siswa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatJurnal as $rj)
                <tr>
                    <td>
                        <strong style="color: #1b2559;">{{ Carbon\Carbon::parse($rj->tanggal)->translatedFormat('d M Y') }}</strong>
                        <div style="font-size: 11px; color: #6b7a99;">Jam ke-{{ $rj->jam_ke }} ({{ $rj->jam_mulai ? Carbon\Carbon::parse($rj->jam_mulai)->format('H:i') : '-' }})</div>
                    </td>
                    <td>
                        <strong style="color: #2b3674;">{{ $rj->kelas->nama_kelas ?? '-' }}</strong>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #1b2559;">{{ $rj->mapel->nama_mapel ?? '-' }}</div>
                    </td>
                    <td style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $rj->materi }}">
                        {{ $rj->materi }}
                    </td>
                    <td>
                        <span class="status-badge success">{{ $rj->jumlah_siswa_hadir }} Hadir</span>
                        @if($rj->jumlah_siswa_tidak_hadir > 0)
                            <span class="status-badge danger">{{ $rj->jumlah_siswa_tidak_hadir }} Absen</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('guru-mapel.jurnal.show', $rj->id_jurnal) }}" class="btn-action-secondary" style="padding: 4px 10px; font-size: 11.5px;">
                            <i class="fa-solid fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7a99; padding: 32px;">
                        <i class="fa-solid fa-book-open" style="font-size: 28px; margin-bottom: 6px; color: #cbd5e1;"></i>
                        <div>Belum ada jurnal pembelajaran yang tersimpan.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
