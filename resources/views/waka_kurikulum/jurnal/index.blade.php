@extends('layouts.waka_kurikulum')

@section('title', 'Monitoring Jurnal Mengajar - Waka Kurikulum')
@section('header_title', 'Monitoring Jurnal Mengajar')
@section('header_subtitle', 'Pantau seluruh aktivitas jurnal mengajar guru se-sekolah')

@section('styles')
<style>
    .page-action-card {
        background: white; border-radius: 16px; border: 1px solid #e2e8f0;
        padding: 18px 22px; margin-bottom: 22px;
        display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;
    }
    .filter-item { display: flex; flex-direction: column; gap: 6px; }
    .filter-label { font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; }
    .filter-input, .filter-select {
        padding: 9px 14px; border-radius: 10px; border: 1.5px solid #e2e8f0;
        font-size: 13.5px; color: #0f172a; background: #f8fafc;
        min-width: 150px; font-family: inherit;
    }
    .filter-input:focus, .filter-select:focus { outline: none; border-color: #2b43b9; background: white; }
    .btn-filter { padding: 9px 18px; background: #2b43b9; color: white; border: none; border-radius: 10px; font-size: 13.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-filter:hover { background: #1e3a8a; }

    .table-card { background: white; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; }
    .table-hdr { padding: 18px 22px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
    .table-title { font-size: 15px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .table-title i { color: #2b43b9; }
    .count-badge { font-size: 12px; background: #eef2ff; color: #2b43b9; padding: 2px 10px; border-radius: 8px; font-weight: 700; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; border-bottom: 1px solid #f1f5f9; letter-spacing: 0.5px; }
    .data-table td { padding: 14px 16px; font-size: 13.5px; border-bottom: 1px solid #f8fafc; color: #334155; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #f8fafc; }

    .materi-text { font-size: 13px; color: #475569; margin-top: 3px; }
    .guru-name { font-weight: 700; font-size: 13px; color: #0f172a; }
    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state i { font-size: 48px; color: #cbd5e1; margin-bottom: 14px; }
    .empty-state h3 { font-size: 16px; font-weight: 700; color: #475569; }
    .empty-state p { font-size: 13.5px; color: #94a3b8; margin-top: 4px; }
</style>
@endsection

@section('content')
<div>
    {{-- Filter Bar --}}
    <div class="page-action-card">
        <form action="{{ route('waka-kurikulum.jurnal') }}" method="GET" style="display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; width: 100%;">
            <div class="filter-item">
                <span class="filter-label">Tanggal</span>
                <input type="date" name="tanggal" class="filter-input" value="{{ $tanggal }}">
            </div>
            <div class="filter-item">
                <span class="filter-label">Kelas</span>
                <select name="id_kelas" class="filter-select">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id_kelas }}" {{ $idKelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <span class="filter-label">Mapel</span>
                <select name="id_mapel" class="filter-select">
                    <option value="">Semua Mapel</option>
                    @foreach($mapelList as $m)
                        <option value="{{ $m->id_mapel }}" {{ $idMapel == $m->id_mapel ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <span class="filter-label">Guru</span>
                <select name="id_guru" class="filter-select">
                    <option value="">Semua Guru</option>
                    @foreach($guruList as $g)
                        <option value="{{ $g->id_guru }}" {{ $idGuru == $g->id_guru ? 'selected' : '' }}>{{ $g->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <span class="filter-label">Cari</span>
                <input type="text" name="search" class="filter-input" placeholder="Nama guru / materi..." value="{{ $search }}">
            </div>
            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if($tanggal !== \Carbon\Carbon::today()->format('Y-m-d') || $idKelas || $idMapel || $idGuru || $search)
                <a href="{{ route('waka-kurikulum.jurnal') }}" style="padding: 9px 14px; background: #f1f5f9; color: #475569; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-hdr">
            <div class="table-title">
                <i class="fa-solid fa-book-open"></i>
                <span>Daftar Jurnal Mengajar</span>
                <span class="count-badge">{{ $jurnalList->total() }} Entri</span>
            </div>
        </div>
        @if($jurnalList->isEmpty())
            <div class="empty-state">
                <div><i class="fa-solid fa-book-bookmark"></i></div>
                <h3>Belum ada jurnal ditemukan</h3>
                <p>Coba ubah filter tanggal atau pilihan lainnya.</p>
            </div>
        @else
            <div class="table-responsive-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Guru Pengajar</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Jam Ke</th>
                            <th>Materi</th>
                            <th>Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnalList as $i => $j)
                        <tr>
                            <td style="color: #94a3b8; font-weight: 700;">{{ $jurnalList->firstItem() + $i }}</td>
                            <td>
                                <div class="guru-name">{{ $j->guru->nama_lengkap ?? '-' }}</div>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; font-weight: 700; background: #e0f2fe; color: #0369a1; padding: 3px 10px; border-radius: 8px;">
                                    {{ $j->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $j->mapel->nama_mapel ?? '-' }}</td>
                            <td>
                                <span style="font-size: 12.5px; font-weight: 700; background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 6px;">
                                    J{{ $j->jam_ke }}
                                </span>
                            </td>
                            <td>
                                <div style="max-width: 220px; font-size: 13px; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $j->materi }}
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #065f46; background: #d1fae5; padding: 3px 10px; border-radius: 8px; font-size: 12.5px;">
                                    {{ $j->jumlah_siswa_hadir }}/{{ ($j->jumlah_siswa_hadir ?? 0) + ($j->jumlah_siswa_tidak_hadir ?? 0) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($jurnalList->hasPages())
                <div style="padding: 16px 22px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-size: 13px; color: #64748b;">
                        Menampilkan <strong>{{ $jurnalList->firstItem() }}</strong> – <strong>{{ $jurnalList->lastItem() }}</strong> dari <strong>{{ $jurnalList->total() }}</strong> entri
                    </div>
                    {{ $jurnalList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
