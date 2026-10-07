@extends('layouts.guru_piket')

@section('title', 'Persetujuan Izin Siswa Terlambat — Waka Piket SMKN 1 Boyolangu')
@section('header_title', 'Persetujuan Izin Siswa Terlambat')
@section('header_subtitle', 'Tinjau dan konfirmasi permohonan izin masuk kelas bagi siswa yang datang terlambat hari ini')

@section('styles')
<style>
    .banner-waka-duty {
        background: linear-gradient(135deg, #1e3a8a 0%, #2b43b9 50%, #3b82f6 100%);
        border-radius: 18px;
        padding: 22px 26px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 8px 24px rgba(43, 67, 185, 0.2);
        flex-wrap: wrap;
        gap: 16px;
    }
    .banner-waka-duty.not-authorized {
        background: linear-gradient(135deg, #475569 0%, #64748b 100%);
        box-shadow: 0 8px 24px rgba(100, 116, 139, 0.2);
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .metric-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .metric-icon.warning { background: #fff7ed; color: #f97316; }
    .metric-icon.success { background: #e6f9f0; color: #10b981; }
    .metric-icon.danger  { background: #fef2f2; color: #ef4444; }
    .metric-icon.blue    { background: #eef2ff; color: #2b43b9; }

    .metric-info h4 {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .metric-info .metric-num {
        font-size: 24px;
        font-weight: 800;
        color: #1b2559;
        line-height: 1.1;
    }

    .card-filter {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px 22px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }
    .filter-input {
        padding: 9px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        font-family: inherit;
        font-weight: 600;
        color: #1b2559;
        outline: none;
        background: #ffffff;
    }
    .filter-input:focus { border-color: #2b43b9; }

    .nav-tabs-custom {
        display: flex;
        gap: 8px;
        border-bottom: 2px solid #eef2f7;
        margin-bottom: 20px;
    }
    .tab-item {
        padding: 12px 20px;
        font-size: 13.5px;
        font-weight: 700;
        color: #64748b;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .tab-item:hover { color: #2b43b9; }
    .tab-item.active {
        color: #2b43b9;
        border-bottom-color: #2b43b9;
    }
    .tab-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 800;
    }
    .tab-badge.warning { background: #ffedd5; color: #c2410c; }
    .tab-badge.neutral { background: #f1f5f9; color: #475569; }

    .section-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
    }
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
        padding: 14px;
        border-bottom: 1px solid #f4f7fe;
        color: #2b3674;
        vertical-align: middle;
    }
    .custom-table tr:hover td { background-color: #f8fafc; }

    .btn-approve {
        background: #10b981;
        color: #ffffff;
        border: none;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-approve:hover { background: #059669; }

    .btn-reject {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-reject:hover { background: #dc2626; color: #ffffff; }

    .btn-print-slip {
        background: #2b43b9;
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-print-slip:hover { background: #1e3a8a; }

    /* Modal Styling */
    .custom-modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none; align-items: center; justify-content: center;
        padding: 20px;
    }
    .custom-modal-card {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalScaleIn 0.2s ease-out;
    }
    @keyframes modalScaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .modal-head {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-head h3 { font-size: 16px; font-weight: 800; color: #1b2559; margin: 0; }
    .modal-body { padding: 22px 24px; }
    .modal-foot {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        background: #fafbfc;
    }

    @media (max-width: 900px) {
        .metrics-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 500px) {
        .metrics-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="content-container">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div style="background: #e6f9f0; border: 1.5px solid #86efac; border-radius: 14px; padding: 14px 18px; color: #166534; font-weight: 600; font-size: 13.5px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 14px; padding: 14px 18px; color: #991b1b; font-weight: 600; font-size: 13.5px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-xmark" style="font-size: 18px; color: #ef4444;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Waka Duty Banner --}}
    <div class="banner-waka-duty {{ $isAuthorized ? '' : 'not-authorized' }}">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 54px; height: 54px; border-radius: 16px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 26px;">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <h3 style="font-size: 18px; font-weight: 800; margin: 0 0 4px 0; letter-spacing: -0.2px;">
                    Meja Persetujuan Waka Piket KBM
                </h3>
                <p style="font-size: 13px; margin: 0; opacity: 0.9;">
                    {{ $todayFormatted }} &bull; 
                    @if($isAuthorized)
                        <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 11px;">
                            <i class="fa-solid fa-check"></i> Wewenang Anda Aktif Hari Ini
                        </span>
                    @else
                        <span style="background: #e2e8f0; color: #334155; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 11px;">
                            <i class="fa-solid fa-eye"></i> Mode Peninjauan
                        </span>
                    @endif
                </p>
            </div>
        </div>
        <div>
            @if(!$isAuthorized)
                <div style="font-size: 12px; background: rgba(0,0,0,0.2); padding: 8px 14px; border-radius: 10px; max-width: 320px; line-height: 1.4;">
                    <i class="fa-solid fa-circle-info"></i> Anda tidak tercatat dalam jadwal Waka Piket pada tanggal ini. Aksi persetujuan hanya dapat dilakukan oleh Waka bertugas atau Administrator.
                </div>
            @else
                <div style="font-size: 12.5px; background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 10px; font-weight: 600;">
                    <i class="fa-solid fa-stamp"></i> Siap memberikan persetujuan izin masuk kelas
                </div>
            @endif
        </div>
    </div>

    {{-- Metrics Grid --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon blue">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="metric-info">
                <h4>Total Permohonan</h4>
                <div class="metric-num">{{ $metrics['total'] }}</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon warning">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="metric-info">
                <h4>Menunggu Persetujuan</h4>
                <div class="metric-num">{{ $metrics['menunggu'] }}</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon success">
                <i class="fa-solid fa-file-circle-check"></i>
            </div>
            <div class="metric-info">
                <h4>Telah Disetujui</h4>
                <div class="metric-num">{{ $metrics['disetujui'] }}</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon danger">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div class="metric-info">
                <h4>Ditolak / Batal</h4>
                <div class="metric-num">{{ $metrics['ditolak'] }}</div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <form action="{{ route('waka-piket.terlambat.index') }}" method="GET" class="card-filter">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Tanggal Piket</span>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="filter-input" onchange="this.form.submit()">
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Cari Siswa</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NISN..." class="filter-input">
            </div>
            <div style="align-self: flex-end;">
                <button type="submit" class="btn-approve" style="background: #2b43b9; padding: 10px 16px;">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                @if($search || $tanggal !== now()->format('Y-m-d'))
                    <a href="{{ route('waka-piket.terlambat.index', ['tab' => $tab]) }}" style="background: #f1f5f9; color: #475569; padding: 10px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Navigation Tabs --}}
    <div class="nav-tabs-custom">
        <a href="{{ route('waka-piket.terlambat.index', ['tab' => 'menunggu', 'tanggal' => $tanggal, 'search' => $search]) }}" class="tab-item {{ $tab === 'menunggu' ? 'active' : '' }}">
            <i class="fa-solid fa-inbox"></i>
            <span>Antrean Menunggu Persetujuan</span>
            <span class="tab-badge warning">{{ $metrics['menunggu'] }}</span>
        </a>
        <a href="{{ route('waka-piket.terlambat.index', ['tab' => 'riwayat', 'tanggal' => $tanggal, 'search' => $search]) }}" class="tab-item {{ $tab === 'riwayat' ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i>
            <span>Riwayat Keputusan</span>
            <span class="tab-badge neutral">{{ $metrics['disetujui'] + $metrics['ditolak'] }}</span>
        </a>
    </div>

    {{-- Content Table --}}
    <div class="section-card">
        @if($tab === 'menunggu')
            <div style="margin-bottom: 16px;">
                <h3 style="font-size: 15px; font-weight: 800; color: #1b2559; margin: 0;">
                    Antrean Permohonan Izin Masuk Siswa Terlambat
                </h3>
                <p style="font-size: 12.5px; color: #707e94; margin: 3px 0 0 0;">
                    Permohonan yang disetujui akan otomatis mendapatkan Nomor Surat Resmi dan status presensi siswa diatur ke "Terlambat".
                </p>
            </div>

            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th>Siswa & Kelas</th>
                            <th>Waktu Tiba</th>
                            <th>Izin Masuk</th>
                            <th>Alasan Keterlambatan</th>
                            <th>Guru Piket Pelapor</th>
                            <th style="text-align: right; min-width: 170px;">Keputusan Waka</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terlambatList as $idx => $t)
                            <tr>
                                <td>{{ $terlambatList->firstItem() + $idx }}</td>
                                <td>
                                    <strong style="color: #1b2559; font-size: 13.5px;">{{ $t->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                                    <div style="font-size: 11.5px; color: #6b7a99; margin-top: 2px;">
                                        <span style="background: #eef2ff; color: #2b43b9; padding: 1px 7px; border-radius: 4px; font-weight: 700;">
                                            {{ $t->kelas->nama_kelas ?? ($t->siswa->kelas->nama_kelas ?? '-') }}
                                        </span>
                                        <span style="margin-left: 6px;">NISN: {{ $t->siswa->nisn ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 800; color: #1e293b; font-size: 13px;">
                                        <i class="fa-regular fa-clock" style="color: #64748b; margin-right: 4px;"></i>
                                        {{ \Carbon\Carbon::parse($t->jam_masuk)->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td>
                                    <span style="background: #f1f5f9; color: #1e293b; font-weight: 800; padding: 3px 8px; border-radius: 6px; font-size: 12px; border: 1px solid #e2e8f0;">
                                        Jam Ke-{{ $t->jam_ke_mulai }}
                                    </span>
                                </td>
                                <td>
                                    <div style="max-width: 250px; font-size: 12.5px; color: #334155; line-height: 1.3;">
                                        {{ $t->alasan }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 12px; color: #64748b;">
                                        <i class="fa-solid fa-user-pen" style="margin-right: 4px;"></i>
                                        {{ $t->diinputOlehUser->name ?? '-' }}
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    @if($isAuthorized)
                                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                            <button type="button" class="btn-approve" onclick="openApprovalModal({{ $t->id }}, '{{ $t->siswa->nama_lengkap ?? '' }}', '{{ $t->kelas->nama_kelas ?? '' }}', '{{ $t->jam_ke_mulai }}')">
                                                <i class="fa-solid fa-check"></i> Setujui
                                            </button>
                                            <button type="button" class="btn-reject" onclick="openRejectModal({{ $t->id }}, '{{ $t->siswa->nama_lengkap ?? '' }}')">
                                                <i class="fa-solid fa-xmark"></i> Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span style="font-size: 11.5px; color: #94a3b8; font-style: italic;">
                                            Tidak bertugas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <i class="fa-solid fa-check-double" style="font-size: 38px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                    <span style="font-size: 14px; font-weight: 700; color: #64748b;">Tidak ada antrean persetujuan</span>
                                    <p style="font-size: 12px; margin-top: 4px;">Semua permohonan siswa terlambat pada tanggal ini telah diproses.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            {{-- TAB RIWAYAT --}}
            <div style="margin-bottom: 16px;">
                <h3 style="font-size: 15px; font-weight: 800; color: #1b2559; margin: 0;">
                    Riwayat Keputusan Izin Siswa Terlambat
                </h3>
            </div>

            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th>Siswa & Kelas</th>
                            <th>Waktu Datang</th>
                            <th>Izin Masuk</th>
                            <th>Nomor Surat</th>
                            <th>Status</th>
                            <th>Konfirmasi Waka</th>
                            <th style="text-align: right; min-width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terlambatList as $idx => $t)
                            <tr>
                                <td>{{ $terlambatList->firstItem() + $idx }}</td>
                                <td>
                                    <strong style="color: #1b2559; font-size: 13.5px;">{{ $t->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                                    <div style="font-size: 11.5px; color: #6b7a99; margin-top: 2px;">
                                        {{ $t->kelas->nama_kelas ?? ($t->siswa->kelas->nama_kelas ?? '-') }} &bull; NISN: {{ $t->siswa->nisn ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #1e293b;">
                                        {{ \Carbon\Carbon::parse($t->jam_masuk)->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td>Jam Ke-{{ $t->jam_ke_mulai }}</td>
                                <td>
                                    @if($t->nomor_surat)
                                        <code style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 7px; font-size: 11px; font-weight: 700; color: #0f172a;">
                                            {{ $t->nomor_surat }}
                                        </code>
                                    @else
                                        <span style="color: #94a3b8; font-style: italic;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($t->status === 'Disetujui')
                                        <span style="background: #e6f9f0; color: #10b981; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">
                                            Disetujui
                                        </span>
                                    @elseif($t->status === 'Ditolak')
                                        <span style="background: #fef2f2; color: #ef4444; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">
                                            Ditolak
                                        </span>
                                    @elseif($t->status === 'Dibatalkan')
                                        <span style="background: #f1f5f9; color: #64748b; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 11.5px; color: #64748b;">
                                        <div><i class="fa-solid fa-user-shield"></i> {{ $t->dikonfirmasiOlehUser->name ?? '-' }}</div>
                                        @if($t->catatan_konfirmasi)
                                            <div style="color: #334155; font-style: italic; margin-top: 2px;">
                                                "{{ Str::limit($t->catatan_konfirmasi, 35) }}"
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    @if($t->status === 'Disetujui')
                                        <a href="{{ route('guru-piket.terlambat.cetak', $t->id) }}" target="_blank" class="btn-print-slip" title="Cetak Surat Izin">
                                            <i class="fa-solid fa-print"></i> Cetak
                                        </a>
                                    @else
                                        <span style="color: #94a3b8; font-size: 12px;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    Tidak ada riwayat keputusan pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $terlambatList->links() }}
        </div>
    </div>
</div>

{{-- MODAL PERSETUJUAN --}}
<div class="custom-modal-backdrop" id="approvalModal">
    <div class="custom-modal-card">
        <div class="modal-head" style="background: #f0fdf4; border-bottom-color: #dcfce7;">
            <h3 style="color: #166534;"><i class="fa-solid fa-stamp"></i> Setujui Izin Masuk Siswa</h3>
            <button type="button" class="modal-close-btn" onclick="closeApprovalModal()">&times;</button>
        </div>

        <form id="approvalForm" method="POST">
            @csrf
            <input type="hidden" name="action" value="setujui">

            <div class="modal-body">
                <p style="font-size: 13.5px; color: #334155; line-height: 1.4; margin-top: 0;">
                    Anda akan menyetujui izin masuk kelas bagi siswa berikut:
                </p>

                <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px;">
                    <div id="approveSiswaName" style="font-weight: 800; font-size: 14.5px; color: #1e293b;"></div>
                    <div id="approveSiswaMeta" style="font-size: 12px; color: #64748b; margin-top: 2px;"></div>
                </div>

                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 10px 14px; font-size: 12px; color: #1d4ed8; margin-bottom: 14px; line-height: 1.4;">
                    <i class="fa-solid fa-circle-check"></i> <strong>Sistem Otomatis:</strong> Nomor surat resmi akan langsung diterbitkan secara otomatis dan presensi siswa akan disinkronkan menjadi "Terlambat".
                </div>

                <div class="form-group">
                    <label style="font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                        Catatan Persetujuan (Opsional)
                    </label>
                    <textarea name="catatan" rows="2" class="filter-input" style="width: 100%; box-sizing: border-box;" placeholder="Tambahkan catatan khusus untuk guru mata pelajaran atau wali kelas bila diperlukan..."></textarea>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" style="background: #f1f5f9; color: #475569; border: none; padding: 9px 16px; border-radius: 8px; font-weight: 700; font-size: 12.5px; cursor: pointer;" onclick="closeApprovalModal()">Batal</button>
                <button type="submit" class="btn-approve" style="padding: 9px 18px; font-size: 13px;">
                    <i class="fa-solid fa-stamp"></i> Konfirmasi Persetujuan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PENOLAKAN --}}
<div class="custom-modal-backdrop" id="rejectModal">
    <div class="custom-modal-card">
        <div class="modal-head" style="background: #fef2f2; border-bottom-color: #fee2e2;">
            <h3 style="color: #b91c1c;"><i class="fa-solid fa-ban"></i> Tolak Permohonan Izin Masuk</h3>
            <button type="button" class="modal-close-btn" onclick="closeRejectModal()">&times;</button>
        </div>

        <form id="rejectForm" method="POST">
            @csrf
            <input type="hidden" name="action" value="tolak">

            <div class="modal-body">
                <p style="font-size: 13.5px; color: #334155; line-height: 1.4; margin-top: 0;">
                    Tolak izin masuk kelas untuk siswa:
                </p>

                <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px;">
                    <div id="rejectSiswaName" style="font-weight: 800; font-size: 14.5px; color: #9f1239;"></div>
                </div>

                <div class="form-group">
                    <label style="font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                        Alasan Penolakan <span style="color:#ef4444;">*</span>
                    </label>
                    <textarea name="catatan" rows="3" class="filter-input" style="width: 100%; box-sizing: border-box;" placeholder="Jelaskan alasan penolakan izin (misal: keterlambatan melebihi batas toleransi / tanpa alasan sah)..." required></textarea>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" style="background: #f1f5f9; color: #475569; border: none; padding: 9px 16px; border-radius: 8px; font-weight: 700; font-size: 12.5px; cursor: pointer;" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn-reject" style="padding: 9px 18px; font-size: 13px;">
                    <i class="fa-solid fa-xmark"></i> Tolak Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openApprovalModal(id, nama, kelas, jamKe) {
        const form = document.getElementById('approvalForm');
        form.action = `/waka-piket/terlambat/${id}/konfirmasi`;
        document.getElementById('approveSiswaName').innerText = nama;
        document.getElementById('approveSiswaMeta').innerText = `Kelas: ${kelas} • Izin Masuk: Jam Ke-${jamKe}`;
        document.getElementById('approvalModal').style.display = 'flex';
    }

    function closeApprovalModal() {
        document.getElementById('approvalModal').style.display = 'none';
    }

    function openRejectModal(id, nama) {
        const form = document.getElementById('rejectForm');
        form.action = `/waka-piket/terlambat/${id}/konfirmasi`;
        document.getElementById('rejectSiswaName').innerText = nama;
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('custom-modal-backdrop')) {
            event.target.style.display = 'none';
        }
    };
</script>
@endsection
