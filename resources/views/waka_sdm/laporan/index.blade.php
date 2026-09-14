@extends('layouts.waka_sdm')

@section('title', 'Laporan Rekapitulasi Persetujuan - Waka SDM')
@section('header_title', 'Laporan Persetujuan Waka SDM')
@section('header_subtitle', 'Rekapitulasi lengkap persetujuan izin guru dan dispensasi siswa')

@section('styles')
<style>
    .filter-card {
        background: white; border-radius: 16px; padding: 20px 24px;
        border: 1px solid #e5e9f2; margin-bottom: 24px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap; box-shadow: 0 4px 14px rgba(0,0,0,0.02);
    }
    .filter-form { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .filter-input {
        padding: 8px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1;
        font-size: 13px; font-weight: 600; color: #1e293b; outline: none; font-family: inherit;
    }
    .filter-input:focus { border-color: #2563eb; }
    .btn-filter {
        padding: 8px 18px; border-radius: 10px; background: #2563eb; color: white;
        font-size: 13px; font-weight: 700; border: none; cursor: pointer;
    }
    .btn-export {
        padding: 8px 18px; border-radius: 10px; background: #059669; color: white;
        font-size: 13px; font-weight: 700; border: none; cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-print {
        padding: 8px 18px; border-radius: 10px; background: #f1f5f9; color: #334155;
        font-size: 13px; font-weight: 700; border: 1px solid #cbd5e1; cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px;
    }

    .summary-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;
    }
    .summary-card {
        background: white; border-radius: 16px; padding: 18px 20px;
        border: 1px solid #e5e9f2; box-shadow: 0 4px 14px rgba(0,0,0,0.02);
    }
    .summary-num { font-size: 28px; font-weight: 800; color: #0f172a; margin-top: 6px; }
    .summary-lbl { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; }

    .section-card {
        background: white; border-radius: 18px; border: 1px solid #e5e9f2;
        box-shadow: 0 4px 16px rgba(0,0,0,0.02); margin-bottom: 24px; overflow: hidden;
    }
    .section-head {
        padding: 18px 22px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
    }
    .section-head h3 { font-size: 15px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
    .data-table { width: 100%; border-collapse: collapse; text-align: left; }
    .data-table th {
        padding: 12px 18px; background: #f8fafc; font-size: 11px; font-weight: 800;
        color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;
    }
    .data-table td {
        padding: 14px 18px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #334155; vertical-align: middle;
    }

    .status-pill {
        display: inline-flex; align-items: center; padding: 3px 12px; border-radius: 20px;
        font-size: 11.5px; font-weight: 700;
    }
    .status-pill.disetujui { background: #d1fae5; color: #059669; }
    .status-pill.ditolak { background: #fee2e2; color: #dc2626; }
    .status-pill.menunggu { background: #ffedd5; color: #ea580c; }

    @media print {
        .sidebar, .top-header-banner, .filter-card, .btn-print, .btn-export, .mobile-topbar { display: none !important; }
        .main-wrapper { padding: 0 !important; }
        .section-card { border: none !important; box-shadow: none !important; }
    }
    @media (max-width: 1024px) {
        .summary-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')
<div>
    {{-- Filter Bar --}}
    <div class="filter-card">
        <form action="{{ route('waka-sdm.laporan') }}" method="GET" class="filter-form">
            <div>
                <label style="font-size:11.5px; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Tipe Laporan</label>
                <select name="jenis" class="filter-input">
                    <option value="semua" {{ $jenis === 'semua' ? 'selected' : '' }}>Semua Pengajuan</option>
                    <option value="izin_guru" {{ $jenis === 'izin_guru' ? 'selected' : '' }}>Hanya Izin Guru</option>
                    <option value="dispensasi" {{ $jenis === 'dispensasi' ? 'selected' : '' }}>Hanya Dispensasi Siswa</option>
                </select>
            </div>
            <div>
                <label style="font-size:11.5px; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Dari Tanggal</label>
                <input type="date" name="tgl_awal" value="{{ $tglAwal }}" class="filter-input">
            </div>
            <div>
                <label style="font-size:11.5px; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Sampai Tanggal</label>
                <input type="date" name="tgl_akhir" value="{{ $tglAkhir }}" class="filter-input">
            </div>
            <div style="align-self: flex-end;">
                <button type="submit" class="btn-filter">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </div>
        </form>

        <div style="display:flex; align-items:center; gap:10px;">
            <button type="button" class="btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak
            </button>
            <a href="{{ route('waka-sdm.laporan.export', ['jenis' => $jenis, 'tgl_awal' => $tglAwal, 'tgl_akhir' => $tglAkhir]) }}" class="btn-export">
                <i class="fa-solid fa-file-excel"></i> Ekspor CSV
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-lbl">Total Izin Guru</div>
            <div class="summary-num">{{ $summary->total_izin }}</div>
            <div style="font-size:12px; color:#10b981; font-weight:600; margin-top:4px;">{{ $summary->izin_disetujui }} Disetujui</div>
        </div>
        <div class="summary-card">
            <div class="summary-lbl">Izin Guru Ditolak</div>
            <div class="summary-num" style="color:#dc2626;">{{ $summary->izin_ditolak }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:600; margin-top:4px;">Pengajuan tidak disetujui</div>
        </div>
        <div class="summary-card">
            <div class="summary-lbl">Total Dispensasi Siswa</div>
            <div class="summary-num">{{ $summary->total_dispensasi }}</div>
            <div style="font-size:12px; color:#10b981; font-weight:600; margin-top:4px;">{{ $summary->disp_disetujui }} Disetujui</div>
        </div>
        <div class="summary-card">
            <div class="summary-lbl">Dispensasi Ditolak</div>
            <div class="summary-num" style="color:#dc2626;">{{ $summary->disp_ditolak }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:600; margin-top:4px;">Pengajuan tidak disetujui</div>
        </div>
    </div>

    {{-- Tabel Laporan Izin Guru --}}
    @if($jenis === 'semua' || $jenis === 'izin_guru')
    <div class="section-card">
        <div class="section-head">
            <h3><i class="fa-solid fa-user-check" style="color:#2563eb;"></i> Rekapitulasi Izin Guru</h3>
            <span style="font-size:12.5px; font-weight:700; color:#64748b;">Total: {{ $izinList->count() }} Data</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Guru Pengajar</th>
                        <th>Jenis Izin</th>
                        <th>Periode Tanggal</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Disetujui Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinList as $idx => $iz)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <div style="font-weight:700; color:#0f172a;">{{ $iz->guru->nama_lengkap ?? 'Guru' }}</div>
                                <div style="font-size:11.5px; color:#64748b;">NIP. {{ $iz->guru->nip ?? '-' }}</div>
                            </td>
                            <td><strong>{{ $iz->jenis_izin }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($iz->tanggal_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($iz->tanggal_selesai)->format('d/m/Y') }}</td>
                            <td>{{ $iz->alasan }}</td>
                            <td>
                                <span class="status-pill {{ strtolower($iz->status) }}">{{ $iz->status }}</span>
                            </td>
                            <td>
                                <div>{{ $iz->disetujuiOlehUser->name ?? '-' }}</div>
                                <div style="font-size:11px; color:#94a3b8;">{{ $iz->tanggal_persetujuan ? \Carbon\Carbon::parse($iz->tanggal_persetujuan)->format('d/m/Y H:i') : '' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:30px; color:#94a3b8;">Tidak ada data izin guru pada rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Tabel Laporan Dispensasi Siswa --}}
    @if($jenis === 'semua' || $jenis === 'dispensasi')
    <div class="section-card">
        <div class="section-head">
            <h3><i class="fa-solid fa-graduation-cap" style="color:#16a34a;"></i> Rekapitulasi Dispensasi Siswa</h3>
            <span style="font-size:12.5px; font-weight:700; color:#64748b;">Total: {{ $dispensasiList->count() }} Data</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal</th>
                        <th>Jam Keluar - Kembali</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Disetujui Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispensasiList as $idx => $ds)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <div style="font-weight:700; color:#0f172a;">{{ $ds->siswa->nama_lengkap ?? 'Siswa' }}</div>
                                <div style="font-size:11.5px; color:#64748b;">NISN. {{ $ds->siswa->nisn ?? '-' }}</div>
                            </td>
                            <td>{{ $ds->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($ds->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ substr($ds->jam_keluar, 0, 5) }} - {{ $ds->jam_kembali ? substr($ds->jam_kembali, 0, 5) : 'Belum kembali' }}</td>
                            <td>{{ $ds->alasan }}</td>
                            <td>
                                <span class="status-pill {{ strtolower($ds->status) }}">{{ $ds->status }}</span>
                            </td>
                            <td>
                                <div>{{ $ds->disetujuiOlehUser->name ?? '-' }}</div>
                                <div style="font-size:11px; color:#94a3b8;">{{ $ds->tanggal_persetujuan ? \Carbon\Carbon::parse($ds->tanggal_persetujuan)->format('d/m/Y H:i') : '' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:30px; color:#94a3b8;">Tidak ada data dispensasi siswa pada rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
