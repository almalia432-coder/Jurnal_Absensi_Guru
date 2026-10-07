@extends('layouts.waka_kesiswaan')

@section('title', 'Rekap Kedisiplinan Siswa — Waka Kesiswaan')
@section('header_title', 'Rekap Kedisiplinan & Pembinaan Siswa')
@section('header_subtitle', 'Identifikasi siswa dengan akumulasi ketidakhadiran untuk koordinasi Wali Kelas & BK')

@section('styles')
<style>
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px 20px;
        border: 1px solid #eef2f7;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .form-control-custom {
        padding: 8px 12px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13px;
    }
    .btn-filter {
        padding: 8px 16px;
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

    .count-pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
    }
    .count-pill.red { background: #fee2e2; color: #991b1b; }
    .count-pill.amber { background: #fef3c7; color: #b45309; }
    .count-pill.blue { background: #e0f2fe; color: #0369a1; }
</style>
@endsection

@section('content')
<div class="filter-card">
    <form action="{{ route('waka-kesiswaan.kedisiplinan') }}" method="GET" style="display:flex;align-items:center;gap:10px;">
        <label style="font-size:13px;font-weight:700;color:#475569;">Pilih Periode Bulan:</label>
        <input type="month" name="bulan" value="{{ $bulan }}" class="form-control-custom">
        <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Tampilkan</button>
    </form>
    <div style="font-size:12.5px;color:#64748b;">
        <i class="fa-solid fa-circle-info" style="color:#2563eb;"></i> Data dihitung dari rekap presensi KBM bulan terpilih.
    </div>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Total Alpha</th>
                <th>Total Izin</th>
                <th>Total Sakit</th>
                <th>Status Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaIndisipliner as $idx => $s)
            <tr>
                <td style="color:#94a3b8;">{{ $siswaIndisipliner->firstItem() + $idx }}</td>
                <td>
                    <strong>{{ $s->siswa->nama_lengkap ?? '-' }}</strong>
                    <div style="font-size:11px;color:#64748b;">NISN: {{ $s->siswa->nisn ?? '-' }}</div>
                </td>
                <td>{{ $s->siswa->kelas->nama_kelas ?? '-' }}</td>
                <td>
                    <span class="count-pill red">{{ $s->count_alpha }}x Alpha</span>
                </td>
                <td>
                    <span class="count-pill blue">{{ $s->count_izin }}x Izin</span>
                </td>
                <td>
                    <span class="count-pill amber">{{ $s->count_sakit }}x Sakit</span>
                </td>
                <td>
                    @if($s->count_alpha >= 3)
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:800;color:#dc2626;background:#fef2f2;padding:4px 8px;border-radius:6px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Perlu Surat Peringatan / BK
                        </span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;color:#d97706;background:#fffbeb;padding:4px 8px;border-radius:6px;">
                            <i class="fa-solid fa-circle-exclamation"></i> Pantau Wali Kelas
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                    Tidak ada siswa dengan catatan Alpha pada bulan ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 16px 20px;">
        {{ $siswaIndisipliner->links() }}
    </div>
</div>
@endsection
