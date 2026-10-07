@extends('layouts.waka_sdm')

@section('title', 'Rekapitulasi Izin Guru - Waka SDM')
@section('header_title', 'Rekapitulasi Izin Guru')
@section('header_subtitle', 'Pantau data perizinan guru, ketidakhadiran, dan berkas pendukung')

@section('styles')
<style>
    .filter-card {
        background: white; border-radius: 16px; padding: 18px 24px;
        border: 1px solid #e5e9f2; margin-bottom: 24px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap; box-shadow: 0 4px 14px rgba(0,0,0,0.02);
    }
    .status-tabs {
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    }
    .tab-btn {
        padding: 8px 16px; border-radius: 30px; font-size: 12.5px; font-weight: 700;
        text-decoration: none; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0;
        transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px;
    }
    .tab-btn.active {
        background: #2563eb; color: white; border-color: #2563eb; box-shadow: 0 4px 12px rgba(37,99,235,0.2);
    }
    .tab-badge {
        font-size: 11px; padding: 1px 7px; border-radius: 20px; background: rgba(0,0,0,0.08);
    }
    .tab-btn.active .tab-badge { background: rgba(255,255,255,0.25); color: white; }

    .search-group { display: flex; align-items: center; gap: 10px; }
    .filter-input {
        padding: 8px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1;
        font-size: 13px; font-weight: 600; color: #1e293b; outline: none; font-family: inherit;
    }
    .filter-input:focus { border-color: #2563eb; }
    .btn-search {
        padding: 8px 16px; border-radius: 10px; background: #2563eb; color: white;
        font-size: 13px; font-weight: 700; border: none; cursor: pointer;
    }

    .section-card {
        background: white; border-radius: 18px; border: 1px solid #e5e9f2;
        box-shadow: 0 4px 16px rgba(0,0,0,0.02); overflow: hidden;
    }
    .data-table { width: 100%; border-collapse: collapse; text-align: left; }
    .data-table th {
        padding: 14px 20px; background: #f8fafc; font-size: 11.5px; font-weight: 800;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;
    }
    .data-table td {
        padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: #334155; vertical-align: middle;
    }
    .data-table tr:hover td { background-color: #fafbfd; }

    .guru-cell { display: flex; align-items: center; gap: 12px; }
    .avatar-sm {
        width: 36px; height: 36px; border-radius: 50%; background: #3b82f6; color: white;
        font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center;
    }

    .status-pill {
        display: inline-flex; align-items: center; padding: 4px 14px; border-radius: 20px;
        font-size: 12px; font-weight: 700;
    }
    .status-pill.tercatat  { background: #e0f2fe; color: #0369a1; }
    .status-pill.disetujui { background: #d1fae5; color: #059669; }
    .status-pill.dibatalkan{ background: #f1f5f9; color: #64748b; }
    .status-pill.ditolak   { background: #fee2e2; color: #dc2626; }

    .pagination-wrap { padding: 18px 24px; border-top: 1px solid #f1f5f9; }
</style>
@endsection

@section('content')
<div>
    {{-- Filter Bar --}}
    <div class="filter-card">
        <div class="status-tabs">
            <a href="{{ route('waka-sdm.izin', ['status' => 'semua', 'search' => $search]) }}" class="tab-btn {{ empty($status) || $status === 'semua' ? 'active' : '' }}">
                <span>Semua</span>
                <span class="tab-badge">{{ $counts->semua }}</span>
            </a>
            <a href="{{ route('waka-sdm.izin', ['status' => 'Tercatat', 'search' => $search]) }}" class="tab-btn {{ $status === 'Tercatat' ? 'active' : '' }}">
                <span>Tercatat</span>
                <span class="tab-badge" style="background:#e0f2fe; color:#0369a1;">{{ $counts->tercatat }}</span>
            </a>
            <a href="{{ route('waka-sdm.izin', ['status' => 'Disetujui', 'search' => $search]) }}" class="tab-btn {{ $status === 'Disetujui' ? 'active' : '' }}">
                <span>Disetujui</span>
                <span class="tab-badge" style="background:#d1fae5; color:#065f46;">{{ $counts->disetujui }}</span>
            </a>
            <a href="{{ route('waka-sdm.izin', ['status' => 'Dibatalkan', 'search' => $search]) }}" class="tab-btn {{ $status === 'Dibatalkan' ? 'active' : '' }}">
                <span>Dibatalkan</span>
                <span class="tab-badge" style="background:#f1f5f9; color:#475569;">{{ $counts->dibatalkan }}</span>
            </a>
            <a href="{{ route('waka-sdm.izin', ['status' => 'Ditolak', 'search' => $search]) }}" class="tab-btn {{ $status === 'Ditolak' ? 'active' : '' }}">
                <span>Ditolak</span>
                <span class="tab-badge" style="background:#fee2e2; color:#b91c1c;">{{ $counts->ditolak }}</span>
            </a>
        </div>

        <form action="{{ route('waka-sdm.izin') }}" method="GET" class="search-group">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}" class="filter-input" placeholder="Cari nama guru atau alasan...">
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
            @if($search || ($status && $status !== 'semua'))
                <a href="{{ route('waka-sdm.izin') }}" class="tab-btn" style="background:#f1f5f9;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Izin Guru --}}
    <div class="section-card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Guru Pengajar</th>
                        <th>Jenis Izin</th>
                        <th>Periode Tanggal</th>
                        <th>Alasan</th>
                        <th>Berkas</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinList as $iz)
                        @php
                            $nama = $iz->guru->nama_lengkap ?? 'Guru';
                            $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $nama)));
                            $init = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : 'GR';
                        @endphp
                        <tr>
                            <td>
                                <div class="guru-cell">
                                    <div class="avatar-sm">{{ $init }}</div>
                                    <div>
                                        <div style="font-weight:700; color:#0f172a;">{{ $nama }}</div>
                                        <div style="font-size:12px; color:#64748b;">NIP. {{ $iz->guru->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#1e293b;">{{ $iz->jenis_izin }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#334155;">
                                    {{ \Carbon\Carbon::parse($iz->tanggal_mulai)->translatedFormat('d M Y') }}
                                </div>
                                <div style="font-size:12px; color:#94a3b8;">
                                    s/d {{ \Carbon\Carbon::parse($iz->tanggal_selesai)->translatedFormat('d M Y') }}
                                </div>
                            </td>
                            <td>
                                <div style="max-width:240px; font-size:13px; color:#475569;">
                                    {{ $iz->alasan }}
                                </div>
                            </td>
                            <td>
                                @if($iz->bukti_file)
                                    <a href="{{ asset('storage/' . $iz->bukti_file) }}" target="_blank" style="color:#2563eb; font-weight:700; text-decoration:none; font-size:12.5px;">
                                        <i class="fa-solid fa-paperclip"></i> Lampiran
                                    </a>
                                @else
                                    <span style="color:#cbd5e1; font-size:12px;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($iz->status === 'Tercatat')
                                    <span class="status-pill" style="background:#dbeafe; color:#1e40af; font-weight:700;">
                                        <i class="fa-solid fa-circle-check"></i> Tercatat
                                    </span>
                                @elseif($iz->status === 'Disetujui')
                                    <span class="status-pill disetujui">
                                        <i class="fa-solid fa-circle-check"></i> Disetujui
                                    </span>
                                @elseif($iz->status === 'Dibatalkan')
                                    <span class="status-pill" style="background:#f1f5f9; color:#64748b; font-weight:700;">
                                        <i class="fa-solid fa-ban"></i> Dibatalkan
                                    </span>
                                @elseif($iz->status === 'Ditolak')
                                    <span class="status-pill ditolak">
                                        <i class="fa-solid fa-circle-xmark"></i> Ditolak
                                    </span>
                                @else
                                    <span class="status-pill">{{ $iz->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($iz->status === 'Dibatalkan' && $iz->alasan_batal)
                                    <div style="font-size:12px; color:#ef4444;">
                                        <strong>Alasan Batal:</strong> {{ $iz->alasan_batal }}
                                    </div>
                                    @if($iz->dibatalkanOlehUser)
                                        <div style="font-size:11px; color:#94a3b8;">
                                            Oleh: {{ $iz->dibatalkanOlehUser->name }} ({{ \Carbon\Carbon::parse($iz->dibatalkan_at)->format('d/m/Y H:i') }})
                                        </div>
                                    @endif
                                @elseif($iz->catatan)
                                    <div style="font-size:12px; color:#64748b; font-style:italic;">
                                        {{ $iz->catatan }}
                                    </div>
                                @else
                                    <span style="color:#94a3b8; font-size:12px;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:40px; color:#94a3b8;">
                                <i class="fa-solid fa-inbox" style="font-size:32px; margin-bottom:8px; display:block;"></i>
                                Tidak ada data permohonan izin guru yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $izinList->links() }}
        </div>
    </div>
</div>
@endsection
