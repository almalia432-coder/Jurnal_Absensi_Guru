@extends('layouts.wali_murid')

@section('title', 'Jadwal Kelas - Portal Wali Murid')
@section('header_title', 'Jadwal Pelajaran')
@section('header_subtitle', 'Jadwal pelajaran mingguan kelas {{ $siswa->kelas?->nama_kelas ?? "" }}')

@section('styles')
<style>
    /* ── Hari Tabs ── */
    .hari-tabs { display: flex; gap: 8px; margin-bottom: 22px; overflow-x: auto; padding-bottom: 4px; }
    .hari-tab {
        padding: 9px 22px; border-radius: 12px; font-size: 13.5px; font-weight: 700;
        cursor: pointer; border: none; font-family: inherit; transition: all 0.2s; white-space: nowrap; text-decoration: none;
    }
    .hari-tab.active {
        background: linear-gradient(135deg, #3d56b2, #2b3a8c); color: white;
        box-shadow: 0 4px 14px rgba(43,67,185,0.3);
    }
    .hari-tab:not(.active) { background: white; color: #64748b; border: 1.5px solid #e2e8f0; }
    .hari-tab:not(.active):hover { border-color: #3d56b2; color: #3d56b2; background: #eef2ff; }

    /* ── Panel ── */
    .jadwal-panel { display: none; }
    .jadwal-panel.active { display: block; animation: fadeIn 0.25s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    .jadwal-day-header {
        background: linear-gradient(135deg, #3d56b2 0%, #2b3a8c 100%);
        border-radius: 16px; padding: 18px 24px; margin-bottom: 16px;
        display: flex; align-items: center; justify-content: space-between; color: white;
        box-shadow: 0 8px 20px rgba(43,67,185,0.2);
    }
    .jadwal-day-title { font-size: 18px; font-weight: 800; }
    .jadwal-day-count { font-size: 13px; opacity: 0.9; background: rgba(255,255,255,0.2); padding: 4px 14px; border-radius: 8px; font-weight: 700; }

    /* ── Jadwal Cards ── */
    .jadwal-list { display: flex; flex-direction: column; gap: 10px; }
    .jadwal-card {
        background: white; border-radius: 14px; padding: 16px 20px;
        display: flex; align-items: center; gap: 16px;
        box-shadow: 0 2px 8px rgba(43,67,185,0.06); border: 1px solid #e8edf8;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .jadwal-card:hover { transform: translateX(4px); box-shadow: 0 6px 20px rgba(43,67,185,0.12); border-color: #c7d2fe; }

    .jadwal-no {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        background: linear-gradient(135deg, #3d56b2, #2b3a8c);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 800;
    }
    .jadwal-time-block {
        min-width: 100px; text-align: center; padding: 8px 12px;
        background: #eef2ff; border-radius: 10px; border: 1px solid #c7d2fe;
    }
    .jadwal-time-text { font-size: 13px; font-weight: 800; color: #1e3a8a; }
    .jadwal-time-end  { font-size: 11px; color: #6366f1; font-weight: 600; margin-top: 1px; }

    .jadwal-info { flex: 1; }
    .jadwal-mapel { font-size: 14.5px; font-weight: 800; color: #1b2559; }
    .jadwal-guru-info { font-size: 12.5px; color: #94a3b8; margin-top: 3px; display: flex; align-items: center; gap: 8px; }
    .kode-badge { font-size: 10.5px; font-weight: 700; background: #eef2ff; color: #3d56b2; padding: 2px 7px; border-radius: 6px; border: 1px solid #c7d2fe; }

    .empty-day {
        background: white; border-radius: 14px; padding: 40px 20px; text-align: center;
        color: #94a3b8; box-shadow: 0 2px 8px rgba(43,67,185,0.06); border: 1px solid #e8edf8;
    }
    .empty-day i { font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1; }
    .empty-day p { font-size: 13px; font-weight: 500; }

    @media (max-width: 640px) { .jadwal-card { flex-wrap: wrap; } .jadwal-time-block { min-width: 80px; } }
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

@php
    $activeHari = request('hari', \Carbon\Carbon::today()->locale('id')->isoFormat('dddd'));
    $activeHari = ucfirst($activeHari);
    if (!in_array($activeHari, $hariList)) $activeHari = $hariList[0];
@endphp

<div class="hari-tabs">
    @foreach($hariList as $h)
        <a href="?hari={{ $h }}" class="hari-tab {{ $activeHari === $h ? 'active' : '' }}">{{ $h }}</a>
    @endforeach
</div>

@foreach($hariList as $h)
    <div class="jadwal-panel {{ $activeHari === $h ? 'active' : '' }}">
        <div class="jadwal-day-header">
            <div class="jadwal-day-title"><i class="fa-solid fa-calendar-day" style="margin-right:10px;"></i>{{ $h }}</div>
            <div class="jadwal-day-count">{{ $jadwalSeminggu[$h]->count() }} mata pelajaran</div>
        </div>
        <div class="jadwal-list">
            @forelse($jadwalSeminggu[$h] as $j)
                <div class="jadwal-card">
                    <div class="jadwal-no">{{ $j->jam_ke }}</div>
                    <div class="jadwal-time-block">
                        <div class="jadwal-time-text">{{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '--:--' }}</div>
                        <div class="jadwal-time-end">{{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '--:--' }}</div>
                    </div>
                    <div class="jadwal-info">
                        <div class="jadwal-mapel">{{ $j->mapel?->nama_mapel ?? 'Mata Pelajaran' }}</div>
                        <div class="jadwal-guru-info">
                            <i class="fa-solid fa-chalkboard-user" style="font-size:11px;"></i>
                            {{ $j->guru?->nama_guru ?? '-' }}
                            @if($j->mapel?->kode_mapel)
                                <span class="kode-badge">{{ $j->mapel->kode_mapel }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-day">
                    <i class="fa-regular fa-calendar"></i>
                    <p>Tidak ada jadwal pelajaran pada hari {{ $h }}</p>
                </div>
            @endforelse
        </div>
    </div>
@endforeach
@endsection
