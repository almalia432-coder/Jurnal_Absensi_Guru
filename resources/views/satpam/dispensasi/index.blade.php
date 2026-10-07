@extends('layouts.satpam')

@section('title', 'Verifikasi Dispensasi - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Verifikasi Slip Dispensasi Siswa')
@section('header_subtitle', 'Cari dan validasi keabsahan izin keluar siswa yang diterbitkan Guru Piket')

@section('styles')
<style>
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 24px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }
    .filter-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .filter-input {
        padding: 8px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        font-family: inherit;
        font-weight: 600;
        color: #1b2559;
        outline: none;
    }
    .filter-input:focus { border-color: #2b43b9; }

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
        padding: 12px 14px;
        border-bottom: 1px solid #f4f7fe;
        color: #2b3674;
        vertical-align: middle;
    }
    .custom-table tr:hover td { background-color: #f8fafc; }

    .status-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-badge.disetujui { background: #fff7ed; color: #f97316; }
    .status-badge.selesai   { background: #e6f9f0; color: #10b981; }
    .status-badge.menunggu  { background: #e0f2fe; color: #0369a1; }

    .btn-action-primary {
        background: #2b43b9;
        color: white;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    @media (max-width: 768px) {
        .filter-card { padding: 16px; }
        .filter-form { flex-direction: column; align-items: stretch; }
        .filter-input { width: 100%; }
        .section-card { padding: 16px; border-radius: 14px; }
    }
</style>
@endsection

@section('content')
<!-- Filter & Search Bar -->
<div class="filter-card">
    <form method="GET" action="{{ route('satpam.dispensasi') }}" class="filter-form">
        <div>
            <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Pilih Tanggal</span>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="filter-input">
        </div>

        <div style="flex: 1; min-width: 240px;">
            <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Cari Siswa / NISN / Alasan</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama siswa atau NISN..." class="filter-input" style="width: 100%;">
        </div>

        <div style="align-self: flex-end;">
            <button type="submit" class="btn-action-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Cari Data
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="section-card">
    <div class="table-responsive-wrap">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Waktu Izin</th>
                    <th>Nama Siswa & NISN</th>
                    <th>Kelas & Rombel</th>
                    <th>Keperluan / Alasan</th>
                    <th>Petugas Penerbit</th>
                    <th>Status Gerbang</th>
                    <th>Konfirmasi Gate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dispensasiList as $d)
                <tr>
                    <td>
                        <strong style="color: #1b2559;">Rencana: {{ Carbon\Carbon::parse($d->jam_keluar)->format('H:i') }} WIB</strong>
                        @if($d->jam_keluar_aktual)
                            <div style="font-size: 11px; color: #f97316;">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar: <strong>{{ Carbon\Carbon::parse($d->jam_keluar_aktual)->format('H:i') }}</strong>
                            </div>
                        @endif
                        @if($d->jam_kembali_aktual || $d->jam_kembali)
                            <div style="font-size: 11px; color: #10b981;">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Kembali: <strong>{{ Carbon\Carbon::parse($d->jam_kembali_aktual ?? $d->jam_kembali)->format('H:i') }}</strong>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong style="color: #1b2559;">{{ $d->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                        <div style="font-size: 11px; color: #707e94;">NISN: {{ $d->siswa->nisn ?? '-' }}</div>
                    </td>
                    <td>
                        <strong style="color: #2b3674;">{{ $d->siswa->kelas->nama_kelas ?? '-' }}</strong>
                    </td>
                    <td style="max-width: 220px;">
                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $d->alasan }}">
                            {{ $d->alasan }}
                        </div>
                    </td>
                    <td>
                        <span style="font-size: 12px; font-weight: 700; color: #2b43b9;">
                            {{ $d->diinputOlehUser->name ?? 'Guru Piket' }}
                        </span>
                    </td>
                    <td>
                        @if(!$d->jam_keluar_aktual && $d->status === 'Disetujui')
                            <span class="status-badge menunggu" style="background:#fef3c7; color:#b45309;">Belum Keluar</span>
                        @elseif($d->jam_keluar_aktual && !$d->jam_kembali_aktual && $d->status === 'Disetujui')
                            <span class="status-badge disetujui">Sedang di Luar</span>
                        @elseif($d->status === 'Selesai' || $d->jam_kembali_aktual)
                            <span class="status-badge selesai">Selesai Kembali</span>
                        @else
                            <span class="status-badge disetujui">{{ $d->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            @if(!$d->jam_keluar_aktual && $d->status === 'Disetujui')
                                <form action="{{ route('satpam.dispensasi.status', $d->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="keluar">
                                    <button type="submit" class="btn-action-primary" style="background: #f97316; padding: 4px 10px; font-size: 11.5px;">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar Gate
                                    </button>
                                </form>
                            @elseif($d->jam_keluar_aktual && !$d->jam_kembali_aktual && $d->status === 'Disetujui')
                                <form action="{{ route('satpam.dispensasi.status', $d->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="kembali">
                                    <button type="submit" class="btn-action-primary" style="background: #10b981; padding: 4px 10px; font-size: 11.5px;">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Gate
                                    </button>
                                </form>
                            @else
                                <span style="color: #94a3b8; font-size: 12px;"><i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Selesai</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #707e94; padding: 36px;">
                        <i class="fa-solid fa-ticket-simple" style="font-size: 32px; margin-bottom: 8px; color: #cbd5e1;"></i>
                        <div>Tidak ada data dispensasi pada tanggal ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $dispensasiList->links() }}
    </div>
</div>
@endsection
