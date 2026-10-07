@extends('layouts.waka_kesiswaan')

@section('title', 'Monitoring Presensi Siswa — Waka Kesiswaan')
@section('header_title', 'Monitoring Presensi Siswa')
@section('header_subtitle', 'Pantau rekapitulasi kehadiran siswa KBM se-sekolah')

@section('styles')
<style>
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px 20px;
        border: 1px solid #eef2f7;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.02);
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr) auto;
        gap: 12px;
        align-items: flex-end;
    }
    .form-group label {
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-control-custom {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13px;
        outline: none;
    }
    .btn-filter {
        padding: 9px 18px;
        background: #2563eb;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .metrics-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .metric-pill {
        background: #ffffff;
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .metric-pill strong { font-size: 16px; color: #0f172a; }
    .metric-pill span { font-size: 12px; color: #64748b; font-weight: 600; }

    .table-container {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eef2f7;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .data-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .data-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }

    .badge-status {
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        display: inline-block;
    }
    .badge-status.Hadir { background: #dcfce7; color: #166534; }
    .badge-status.Izin { background: #e0f2fe; color: #0369a1; }
    .badge-status.Sakit { background: #fef3c7; color: #b45309; }
    .badge-status.Alpha { background: #fee2e2; color: #991b1b; }
    .badge-status.Dispensasi { background: #f3e8ff; color: #6b21a8; }
</style>
@endsection

@section('content')
<div class="filter-card">
    <form action="{{ route('waka-kesiswaan.presensi') }}" method="GET" class="filter-grid">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control-custom">
        </div>
        <div class="form-group">
            <label>Kelas</label>
            <select name="id_kelas" class="form-control-custom">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id_kelas }}" {{ $kelasId == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Status Kehadiran</label>
            <select name="status" class="form-control-custom">
                <option value="all">Semua Status</option>
                <option value="Alpha" {{ $statusFilter == 'Alpha' ? 'selected' : '' }}>Alpha (Tanpa Keterangan)</option>
                <option value="Sakit" {{ $statusFilter == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Izin" {{ $statusFilter == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Hadir" {{ $statusFilter == 'Hadir' ? 'selected' : '' }}>Hadir</option>
            </select>
        </div>
        <div class="form-group">
            <label>Cari Siswa</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NISN..." class="form-control-custom">
        </div>
        <div>
            <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
        </div>
    </form>
</div>

<div class="metrics-summary">
    <div class="metric-pill">
        <span>Hadir</span>
        <strong style="color: #166534;">{{ $summary['hadir'] }} Siswa</strong>
    </div>
    <div class="metric-pill">
        <span>Sakit</span>
        <strong style="color: #b45309;">{{ $summary['sakit'] }} Siswa</strong>
    </div>
    <div class="metric-pill">
        <span>Izin</span>
        <strong style="color: #0369a1;">{{ $summary['izin'] }} Siswa</strong>
    </div>
    <div class="metric-pill">
        <span>Alpha</span>
        <strong style="color: #991b1b;">{{ $summary['alpha'] }} Siswa</strong>
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Mata Pelajaran / Guru</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presensiList as $idx => $p)
            <tr>
                <td style="color:#94a3b8;">{{ $presensiList->firstItem() + $idx }}</td>
                <td>
                    <strong>{{ $p->siswa->nama_lengkap ?? '-' }}</strong>
                    <div style="font-size:11px;color:#64748b;">NISN: {{ $p->siswa->nisn ?? '-' }}</div>
                </td>
                <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
                <td>
                    <div>{{ $p->jurnal->mapel->nama_mapel ?? '-' }}</div>
                    <div style="font-size:11px;color:#64748b;">Guru: {{ $p->jurnal->guru->nama_lengkap ?? '-' }}</div>
                </td>
                <td>
                    <span class="badge-status {{ $p->status }}">{{ $p->status }}</span>
                </td>
                <td style="color:#64748b;">{{ $p->keterangan ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                    Tidak ada catatan presensi siswa sesuai filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 16px 20px;">
        {{ $presensiList->links() }}
    </div>
</div>
@endsection
