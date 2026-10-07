@extends('layouts.satpam')

@section('title', 'Monitoring Siswa Terlambat — Satpam SMKN 1 Boyolangu')
@section('header_title', 'Monitoring Siswa Terlambat')
@section('header_subtitle', 'Pantauan read-only siswa yang datang terlambat hari ini dan status izin masuk kelas dari meja piket')

@section('styles')
<style>
    .banner-gate {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        border-radius: 18px;
        padding: 22px 26px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.2);
        flex-wrap: wrap;
        gap: 16px;
    }

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
        font-size: 13.5px;
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

    .status-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-badge.menunggu {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }
    .status-badge.disetujui {
        background: #e6f9f0;
        color: #10b981;
        border: 1px solid #bbf7d0;
    }
    .status-badge.ditolak {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }
    .status-badge.dibatalkan {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
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

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        background: rgba(255,255,255,0.15);
    }
</style>
@endsection

@section('content')
<div class="content-container">

    {{-- Banner Gerbang Satpam --}}
    <div class="banner-gate">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 52px; height: 52px; border-radius: 16px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i>
            </div>
            <div>
                <h3 style="font-size: 18px; font-weight: 800; margin: 0 0 4px 0;">
                    Monitoring Siswa Terlambat Hari Ini
                </h3>
                <p style="font-size: 13px; margin: 0; opacity: 0.85;">
                    {{ $todayFormatted }} &bull; Pos Jaga Gerbang Utama SMKN 1 Boyolangu
                </p>
            </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <div class="stat-pill">
                <i class="fa-solid fa-users"></i>
                <span>Total: {{ count($terlambatList) }} Siswa</span>
            </div>
            <div class="stat-pill" style="background: rgba(16, 185, 129, 0.25);">
                <i class="fa-solid fa-check"></i>
                <span>Disetujui: {{ $terlambatList->where('status', 'Disetujui')->count() }}</span>
            </div>
            <div class="stat-pill" style="background: rgba(249, 115, 22, 0.25);">
                <i class="fa-solid fa-hourglass-half"></i>
                <span>Menunggu: {{ $terlambatList->where('status', 'Menunggu')->count() }}</span>
            </div>
        </div>
    </div>

    {{-- Search Card --}}
    <form action="{{ route('satpam.terlambat') }}" method="GET" style="background: #ffffff; border-radius: 16px; padding: 16px 20px; border: 1px solid #eef2f7; box-shadow: 0 4px 14px rgba(0,0,0,0.03); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        <div style="font-size: 13px; font-weight: 700; color: #475569;">
            <i class="fa-solid fa-shield-halved" style="color: #2b43b9;"></i> Informasi Resmi: Siswa yang tercantum telah melapor ke Meja Guru Piket.
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / NISN..." class="filter-input" style="min-width: 240px;">
            <button type="submit" style="background: #2b43b9; color: #ffffff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
            @if($search)
                <a href="{{ route('satpam.terlambat') }}" style="background: #f1f5f9; color: #475569; padding: 9px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none;">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Read-Only Table --}}
    <div class="section-card">
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Siswa & Kelas</th>
                        <th>Jam Tiba</th>
                        <th>Izin Masuk</th>
                        <th>Alasan Keterlambatan</th>
                        <th>Nomor Surat</th>
                        <th>Status Piket</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terlambatList as $idx => $t)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <strong style="color: #1b2559; font-size: 14px;">{{ $t->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                    <span style="background: #eef2ff; color: #2b43b9; padding: 1px 7px; border-radius: 4px; font-weight: 700;">
                                        {{ $t->kelas->nama_kelas ?? ($t->siswa->kelas->nama_kelas ?? '-') }}
                                    </span>
                                    <span style="margin-left: 6px;">NISN: {{ $t->siswa->nisn ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 800; color: #0f172a;">
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
                                <div style="max-width: 280px; font-size: 12.5px; color: #334155;">
                                    {{ $t->alasan }}
                                </div>
                            </td>
                            <td>
                                @if($t->nomor_surat)
                                    <code style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 7px; font-size: 11px; font-weight: 700; color: #0f172a;">
                                        {{ $t->nomor_surat }}
                                    </code>
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-size: 12px;">Menunggu Waka</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status === 'Disetujui')
                                    <span class="status-badge disetujui">
                                        <i class="fa-solid fa-circle-check"></i> Izin Diberikan
                                    </span>
                                @elseif($t->status === 'Menunggu')
                                    <span class="status-badge menunggu">
                                        <i class="fa-solid fa-spinner fa-spin"></i> Proses Waka
                                    </span>
                                @elseif($t->status === 'Ditolak')
                                    <span class="status-badge ditolak">
                                        <i class="fa-solid fa-circle-xmark"></i> Ditolak
                                    </span>
                                @else
                                    <span class="status-badge dibatalkan">
                                        {{ $t->status }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fa-solid fa-circle-check" style="font-size: 38px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                <span style="font-size: 14px; font-weight: 700; color: #64748b;">Belum ada siswa terlambat yang tercatat hari ini</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
