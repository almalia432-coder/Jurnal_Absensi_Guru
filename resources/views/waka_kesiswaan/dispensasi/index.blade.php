@extends('layouts.waka_kesiswaan')

@section('title', 'Monitoring Dispensasi Siswa — Waka Kesiswaan')
@section('header_title', 'Monitoring Dispensasi Siswa')
@section('header_subtitle', 'Pantau seluruh perizinan dan dispensasi keluar gerbang siswa')

@section('styles')
<style>
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px 20px;
        border: 1px solid #eef2f7;
        margin-bottom: 20px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr) auto;
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
    }

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
<<<<<<< HEAD
    .badge-status.Disetujui, .badge-status.Disetujui_Waka, .badge-status.Disetujui_KS { background: #dcfce7; color: #166534; }
    .badge-status.Disetujui_Piket { background: #dbeafe; color: #1e40af; }
=======
    .badge-status.Disetujui, .badge-status.Disetujui_Waka { background: #dcfce7; color: #166534; }
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
    .badge-status.Menunggu { background: #fef3c7; color: #b45309; }
    .badge-status.Ditolak { background: #fee2e2; color: #991b1b; }
    .badge-status.Selesai { background: #e0e7ff; color: #3730a3; }
</style>
@endsection

@section('content')
<div class="filter-card">
    <form action="{{ route('waka-kesiswaan.dispensasi') }}" method="GET" class="filter-grid">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control-custom">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control-custom">
                <option value="all">Semua Status</option>
                <option value="Menunggu" {{ $status == 'Menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="Disetujui" {{ $status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>Selesai Kembali</option>
                <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
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

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Keperluan / Alasan</th>
                <th>Rentang Jam</th>
                <th>Status</th>
                <th>Verifikator</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dispensasiList as $idx => $d)
            <tr>
                <td style="color:#94a3b8;">{{ $dispensasiList->firstItem() + $idx }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d M Y') }}</td>
                <td>
                    <strong>{{ $d->siswa->nama_lengkap ?? '-' }}</strong>
                    <div style="font-size:11px;color:#64748b;">{{ $d->siswa->kelas->nama_kelas ?? '-' }} • {{ $d->siswa->nisn ?? '-' }}</div>
                </td>
                <td>
                    <div style="max-width:240px;line-height:1.4;">{{ $d->alasan ?? '-' }}</div>
                </td>
                <td>
                    {{ $d->jam_keluar ? \Carbon\Carbon::parse($d->jam_keluar)->format('H:i') : '-' }} s/d {{ $d->jam_kembali ? \Carbon\Carbon::parse($d->jam_kembali)->format('H:i') : 'Selesai KBM' }}
                </td>
                <td>
                    <span class="badge-status {{ $d->status }}">{{ $d->status }}</span>
                </td>
                <td>
                    <div style="font-size:12px;font-weight:700;">{{ $d->disetujuiOlehUser->name ?? '-' }}</div>
                    <div style="font-size:10.5px;color:#94a3b8;">Diinput: {{ $d->diinputOlehUser->name ?? 'Piket' }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                    Tidak ada catatan dispensasi siswa sesuai filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 16px 20px;">
        {{ $dispensasiList->links() }}
    </div>
</div>
@endsection
