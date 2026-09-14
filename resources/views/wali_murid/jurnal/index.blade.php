@extends('layouts.wali_murid')

@section('title', 'Materi Pelajaran - Portal Wali Murid')
@section('header_title', 'Materi Pelajaran')
@section('header_subtitle', 'Catatan jurnal dan materi belajar di kelas {{ $siswa->kelas?->nama_kelas ?? "" }}')

@section('styles')
<style>
    .page-action-card {
        background: white; border-radius: 16px; padding: 18px 22px;
        margin-bottom: 22px; box-shadow: 0 2px 10px rgba(43,67,185,0.06);
        border: 1px solid #e8edf8; display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;
    }
    .filter-group { display: flex; flex-direction: column; gap: 6px; }
    .filter-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .filter-input {
        padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-size: 13.5px; color: #1b2559; font-family: inherit; font-weight: 600;
        outline: none; background: #f8fafc; transition: border-color 0.2s; min-width: 160px;
    }
    .filter-input:focus { border-color: #3d56b2; background: white; }
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

    /* ── Jurnal Cards ── */
    .jurnal-list { display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px; }
    .jurnal-card {
        background: white; border-radius: 16px; padding: 22px 24px;
        box-shadow: 0 2px 10px rgba(43,67,185,0.06); border: 1px solid #e8edf8;
        transition: box-shadow 0.2s, transform 0.2s; animation: fadeInUp 0.3s ease-out;
    }
    .jurnal-card:hover { box-shadow: 0 6px 24px rgba(43,67,185,0.12); transform: translateY(-2px); }

    @keyframes fadeInUp { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }

    .jurnal-card-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; gap: 12px; }
    .jurnal-meta-left { display: flex; align-items: center; gap: 14px; }
    .jurnal-jam-badge {
        width: 46px; height: 46px; border-radius: 14px; flex-shrink: 0;
        background: linear-gradient(135deg, #3d56b2, #2b3a8c);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 800;
    }
    .jurnal-mapel-name { font-size: 15px; font-weight: 800; color: #1b2559; }
    .jurnal-guru { font-size: 12.5px; color: #94a3b8; margin-top: 3px; display: flex; align-items: center; gap: 6px; }
    .jurnal-time-badge { font-size: 11.5px; font-weight: 700; color: #2b43b9; background: #eef2ff; padding: 5px 12px; border-radius: 8px; white-space: nowrap; border: 1px solid #c7d2fe; }

    .jurnal-divider { height: 1px; background: #f1f5f9; margin: 12px 0; }
    .jurnal-materi-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #94a3b8; margin-bottom: 6px; }
    .jurnal-materi-text { font-size: 14px; color: #334155; line-height: 1.7; }

    .jurnal-catatan { margin-top: 12px; padding: 10px 14px; background: #fefce8; border-radius: 10px; border-left: 3px solid #eab308; font-size: 13px; color: #713f12; }
    .jurnal-catatan-label { font-weight: 700; margin-right: 6px; }

    .jurnal-footer { display: flex; align-items: center; gap: 12px; margin-top: 14px; padding-top: 12px; border-top: 1px solid #f1f5f9; flex-wrap: wrap; }
    .jurnal-hadir-info { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #64748b; font-weight: 600; background: #f8fafc; padding: 4px 10px; border-radius: 8px; }
    .jurnal-status-badge { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 7px; }
    .status-mengajar { background: #d1fae5; color: #065f46; }
    .status-tidak    { background: #fee2e2; color: #991b1b; }

    .empty-state { background: white; border-radius: 16px; padding: 60px 20px; text-align: center; color: #94a3b8; box-shadow: 0 2px 10px rgba(43,67,185,0.06); border: 1px solid #e8edf8; }
    .empty-state i { font-size: 44px; display: block; margin-bottom: 14px; color: #cbd5e1; }
    .empty-state h3 { font-size: 16px; font-weight: 700; color: #64748b; margin-bottom: 6px; }
    .empty-state p { font-size: 13px; }

    .pagination-wrap { display: flex; justify-content: center; padding: 16px 0; }

    @media (max-width: 640px) { .page-action-card { flex-direction: column; align-items: stretch; } .jurnal-card-header { flex-direction: column; } }
</style>
@endsection

@section('content')
<div class="siswa-info-card">
    <div class="siswa-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
    <div class="siswa-detail">
        <div class="siswa-name">{{ $siswa->nama_lengkap }}</div>
        <div class="siswa-meta">Kelas: {{ $siswa->kelas?->nama_kelas ?? '-' }}</div>
    </div>
</div>

{{-- Filter --}}
<div class="page-action-card">
    <form method="GET" action="{{ route('wali-murid.jurnal') }}" style="display:contents;">
        <div class="filter-group">
            <label class="filter-label"><i class="fa-solid fa-calendar" style="margin-right:4px;"></i>Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" max="{{ now()->format('Y-m-d') }}" class="filter-input">
        </div>
        <div class="filter-group" style="flex:1;min-width:200px;">
            <label class="filter-label"><i class="fa-solid fa-magnifying-glass" style="margin-right:4px;"></i>Cari Materi</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari materi atau nama mapel..." class="filter-input" style="width:100%;">
        </div>
        <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass" style="margin-right:6px;"></i>Cari</button>
        <a href="{{ route('wali-murid.jurnal') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
    </form>
</div>

{{-- Jurnal List --}}
<div class="jurnal-list">
    @forelse($jurnalList as $j)
        <div class="jurnal-card">
            <div class="jurnal-card-header">
                <div class="jurnal-meta-left">
                    <div class="jurnal-jam-badge">{{ $j->jam_ke }}</div>
                    <div>
                        <div class="jurnal-mapel-name">{{ $j->mapel?->nama_mapel ?? 'Mata Pelajaran' }}</div>
                        <div class="jurnal-guru">
                            <i class="fa-solid fa-chalkboard-user" style="font-size:11px;"></i>
                            {{ $j->guru?->nama_guru ?? '-' }}
                            <span style="color:#e2e8f0;">|</span>
                            <i class="fa-solid fa-calendar" style="font-size:11px;"></i>
                            {{ $j->tanggal ? \Carbon\Carbon::parse($j->tanggal)->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                </div>
                <div class="jurnal-time-badge">
                    {{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '--:--' }}
                    –
                    {{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '--:--' }}
                </div>
            </div>

            <div class="jurnal-divider"></div>

            <div class="jurnal-materi-label">Materi Pembelajaran</div>
            <div class="jurnal-materi-text">{{ $j->materi ?: 'Belum ada catatan materi' }}</div>

            @if($j->catatan)
                <div class="jurnal-catatan">
                    <span class="jurnal-catatan-label"><i class="fa-solid fa-note-sticky" style="margin-right:4px;"></i>Catatan:</span>{{ $j->catatan }}
                </div>
            @endif

            <div class="jurnal-footer">
                @if($j->jumlah_siswa_hadir !== null)
                    <span class="jurnal-hadir-info">
                        <i class="fa-solid fa-users"></i>
                        Hadir: {{ $j->jumlah_siswa_hadir }} &bull; Tidak: {{ $j->jumlah_siswa_tidak_hadir ?? 0 }}
                    </span>
                @endif
                @if($j->status_guru)
                    <span class="jurnal-status-badge {{ str_contains(strtolower($j->status_guru), 'tidak') ? 'status-tidak' : 'status-mengajar' }}">
                        {{ $j->status_guru }}
                    </span>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fa-regular fa-file-lines"></i>
            <h3>Tidak ada catatan materi</h3>
            <p>Belum ada jurnal yang tercatat untuk tanggal atau pencarian yang dipilih</p>
        </div>
    @endforelse
</div>

@if($jurnalList->hasPages())
    <div class="pagination-wrap">{{ $jurnalList->appends(request()->query())->links() }}</div>
@endif
@endsection
