@extends('layouts.wali_murid')

@section('title', 'Riwayat Presensi - Portal Wali Murid')
@section('header_title', 'Riwayat Presensi')
@section('header_subtitle', 'Catatan lengkap kehadiran siswa')

@section('styles')
<style>
    /* ── Page Action Card (filter bar) ── */
    .page-action-card {
        background: white; border-radius: 16px; padding: 18px 22px;
        margin-bottom: 22px; box-shadow: 0 2px 10px rgba(43,67,185,0.06);
        border: 1px solid #e8edf8; display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;
    }
    .filter-group { display: flex; flex-direction: column; gap: 6px; }
    .filter-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .filter-input, .filter-select {
        padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-size: 13.5px; color: #1b2559; font-family: inherit; font-weight: 600;
        outline: none; background: #f8fafc; transition: border-color 0.2s; min-width: 160px;
    }
    .filter-input:focus, .filter-select:focus { border-color: #3d56b2; background: white; }
    .btn-filter {
        padding: 9px 20px; background: linear-gradient(135deg, #3d56b2, #2b3a8c);
        color: white; border: none; border-radius: 10px; font-size: 13.5px;
        font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(43,67,185,0.25);
    }
    .btn-filter:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(43,67,185,0.35); }
    .btn-reset {
        padding: 9px 16px; background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0;
        border-radius: 10px; font-size: 13.5px; font-weight: 700; cursor: pointer;
        font-family: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
    }
    .btn-reset:hover { background: #e2e8f0; color: #334155; }

    /* ── Stat Row ── */
    .stat-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 22px; }
    .stat-mini {
        background: white; border-radius: 14px; padding: 14px 16px; text-align: center;
        box-shadow: 0 2px 8px rgba(43,67,185,0.06); border: 1px solid #e8edf8;
        transition: transform 0.2s;
    }
    .stat-mini:hover { transform: translateY(-2px); }
    .stat-mini-value { font-size: 24px; font-weight: 800; color: #1b2559; }
    .stat-mini-label { font-size: 11.5px; color: #94a3b8; font-weight: 600; margin-top: 2px; }

    /* ── Data Table Card ── */
    .data-card { background: white; border-radius: 16px; box-shadow: 0 2px 10px rgba(43,67,185,0.06); border: 1px solid #e8edf8; overflow: hidden; }
    .data-card-header { padding: 18px 22px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
    .data-card-title { font-size: 15px; font-weight: 800; color: #1b2559; display: flex; align-items: center; gap: 8px; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        padding: 12px 16px; text-align: left; font-size: 11.5px; font-weight: 700;
        color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;
        background: #f8fafc; border-bottom: 1px solid #f1f5f9; white-space: nowrap;
    }
    .data-table tbody tr { border-bottom: 1px solid #f8fafc; transition: background 0.15s; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody td { padding: 12px 16px; font-size: 13.5px; color: #475569; }
    .data-table tbody td .cell-main { font-weight: 700; color: #1b2559; font-size: 14px; }
    .data-table tbody td .cell-sub  { font-size: 12px; color: #94a3b8; margin-top: 1px; }

    .badge-status { padding: 4px 12px; border-radius: 8px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
    .badge-hadir      { background: #d1fae5; color: #065f46; }
    .badge-alpha      { background: #fee2e2; color: #991b1b; }
    .badge-sakit      { background: #fef3c7; color: #92400e; }
    .badge-izin       { background: #dbeafe; color: #1e40af; }
    .badge-dispensasi { background: #ede9fe; color: #5b21b6; }
    .badge-default    { background: #f1f5f9; color: #64748b; }

    .pagination-container { padding: 16px 20px; border-top: 1px solid #f1f5f9; display: flex; justify-content: center; }

    .empty-state { padding: 60px 20px; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 44px; display: block; margin-bottom: 14px; color: #cbd5e1; }
    .empty-state h3 { font-size: 16px; font-weight: 700; color: #64748b; margin-bottom: 6px; }
    .empty-state p { font-size: 13px; }

    @media (max-width: 768px) { .stat-row { grid-template-columns: repeat(3, 1fr); } .page-action-card { flex-direction: column; align-items: stretch; } }
    @media (max-width: 480px) { .stat-row { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection

@section('content')
<div class="siswa-info-card">
    <div class="siswa-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
    <div class="siswa-detail">
        <div class="siswa-name">{{ $siswa->nama_lengkap }}</div>
        <div class="siswa-meta">NIS: {{ $siswa->nis ?? '-' }} &nbsp;•&nbsp; Kelas: {{ $siswa->kelas?->nama_kelas ?? '-' }}</div>
    </div>
</div>

{{-- Filter --}}
<div class="page-action-card">
    <form method="GET" action="{{ route('wali-murid.presensi') }}" style="display:contents;">
        <div class="filter-group">
            <label class="filter-label"><i class="fa-solid fa-calendar" style="margin-right:4px;"></i>Bulan</label>
            <input type="month" name="bulan" value="{{ $bulanFilter }}" max="{{ now()->format('Y-m') }}" class="filter-input">
        </div>
        <div class="filter-group">
            <label class="filter-label"><i class="fa-solid fa-filter" style="margin-right:4px;"></i>Status</label>
            <select name="status" class="filter-select">
                <option value="">Semua Status</option>
                @foreach(['Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi'] as $s)
                    <option value="{{ $s }}" {{ $statusFilter == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass" style="margin-right:6px;"></i>Tampilkan</button>
        <a href="{{ route('wali-murid.presensi') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
    </form>
</div>

{{-- Summary Stats --}}
@php $allInPage = $presensiList->getCollection(); @endphp
<div class="stat-row">
    <div class="stat-mini"><div class="stat-mini-value" style="color:#059669;">{{ $allInPage->where('status','Hadir')->count() }}</div><div class="stat-mini-label">Hadir</div></div>
    <div class="stat-mini"><div class="stat-mini-value" style="color:#f59e0b;">{{ $allInPage->where('status','Sakit')->count() }}</div><div class="stat-mini-label">Sakit</div></div>
    <div class="stat-mini"><div class="stat-mini-value" style="color:#3b82f6;">{{ $allInPage->where('status','Izin')->count() }}</div><div class="stat-mini-label">Izin</div></div>
    <div class="stat-mini"><div class="stat-mini-value" style="color:#ef4444;">{{ $allInPage->where('status','Alpha')->count() }}</div><div class="stat-mini-label">Alpha</div></div>
    <div class="stat-mini"><div class="stat-mini-value" style="color:#8b5cf6;">{{ $allInPage->where('status','Dispensasi')->count() }}</div><div class="stat-mini-label">Dispensasi</div></div>
</div>

{{-- Table --}}
<div class="data-card">
    <div class="data-card-header">
        <div class="data-card-title"><i class="fa-solid fa-clipboard-list" style="color:#3d56b2;"></i> Riwayat Presensi</div>
        <span style="font-size:12.5px;color:#94a3b8;font-weight:600;">Total: {{ $presensiList->total() }} data</span>
    </div>
    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th style="text-align:center;">Jam Ke</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensiList as $i => $p)
                    <tr>
                        <td>{{ $presensiList->firstItem() + $i }}</td>
                        <td style="white-space:nowrap;">
                            {{ $p->jurnal?->tanggal ? \Carbon\Carbon::parse($p->jurnal->tanggal)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td><div class="cell-main">{{ $p->jurnal?->mapel?->nama_mapel ?? '-' }}</div></td>
                        <td><div class="cell-sub">{{ $p->jurnal?->guru?->nama_guru ?? '-' }}</div></td>
                        <td style="text-align:center;">
                            <span style="display:inline-flex;width:30px;height:30px;background:#eef2ff;color:#3d56b2;border-radius:8px;align-items:center;justify-content:center;font-weight:700;font-size:12px;">
                                {{ $p->jurnal?->jam_ke ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @php $st = strtolower($p->status ?? ''); @endphp
                            <span class="badge-status badge-{{ in_array($st, ['hadir','alpha','sakit','izin','dispensasi']) ? $st : 'default' }}">
                                {{ $p->status ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $p->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <i class="fa-regular fa-folder-open"></i>
                            <h3>Tidak ada data presensi</h3>
                            <p>Coba ubah filter bulan atau status di atas</p>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($presensiList->hasPages())
        <div class="pagination-container">{{ $presensiList->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
