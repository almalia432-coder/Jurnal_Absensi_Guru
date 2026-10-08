@extends('layouts.waka_sdm')

@section('title', 'Monitoring Dispensasi Siswa - Waka SDM')
@section('header_title', 'Monitoring Dispensasi Siswa')
@section('header_subtitle', 'Rekapitulasi dan pemantauan izin dispensasi siswa KBM (Persetujuan operasional harian diproses oleh Waka Piket KBM)')

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
        background: #16a34a; color: white; border-color: #16a34a; box-shadow: 0 4px 12px rgba(22,163,74,0.2);
    }
    .tab-badge {
        font-size: 11px; padding: 1px 7px; border-radius: 20px; background: rgba(0,0,0,0.08);
    }
    .tab-btn.active .tab-badge { background: rgba(255,255,255,0.25); color: white; }

    .search-group { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .filter-input {
        padding: 8px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1;
        font-size: 13px; font-weight: 600; color: #1e293b; outline: none; font-family: inherit;
    }
    .filter-input:focus { border-color: #16a34a; }
    .btn-search {
        padding: 8px 16px; border-radius: 10px; background: #16a34a; color: white;
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
        width: 36px; height: 36px; border-radius: 50%; background: #10b981; color: white;
        font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center;
    }

    .status-pill {
        display: inline-flex; align-items: center; padding: 4px 14px; border-radius: 20px;
        font-size: 12px; font-weight: 700;
    }
    .status-pill.menunggu { background: #ffedd5; color: #ea580c; }
    .status-pill.disetujui { background: #d1fae5; color: #059669; }
    .status-pill.ditolak { background: #fee2e2; color: #dc2626; }

    .action-btns { display: flex; align-items: center; gap: 6px; }
    .btn-act {
        padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;
        border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s;
    }
    .btn-act.approve { background: #d1fae5; color: #059669; }
    .btn-act.approve:hover { background: #a7f3d0; }
    .btn-act.reject { background: #fee2e2; color: #dc2626; }
    .btn-act.reject:hover { background: #fecaca; }

    .pagination-wrap { padding: 18px 24px; border-top: 1px solid #f1f5f9; }
</style>
@endsection

@section('content')
<div>
    {{-- Info SOP Notice Banner --}}
    <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-left: 5px solid #16a34a; border-radius: 14px; padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.05);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-circle-info" style="font-size: 20px; color: #16a34a;"></i>
            <div style="font-size: 13px; color: #166534; line-height: 1.45;">
                <strong>Ketentuan SOP Sekolah:</strong> Persetujuan operasional harian dispensasi siswa diproses langsung oleh <strong>Waka Piket KBM</strong> yang bertugas hari itu. Halaman ini berfungsi sebagai arsip monitoring, rekapitulasi pelaporan, dan audit ketertiban KBM sekolah.
            </div>
        </div>
        <a href="{{ route('waka-sdm.izin') }}" style="background: #16a34a; color: white; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-user-check"></i> Kelola Izin Guru
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-card">
        <div class="status-tabs">
            <a href="{{ route('waka-sdm.dispensasi', ['status' => 'semua', 'search' => $search, 'id_kelas' => $id_kelas]) }}" class="tab-btn {{ empty($status) || $status === 'semua' ? 'active' : '' }}">
                <span>Semua</span>
                <span class="tab-badge">{{ $counts->semua }}</span>
            </a>
            <a href="{{ route('waka-sdm.dispensasi', ['status' => 'Menunggu', 'search' => $search, 'id_kelas' => $id_kelas]) }}" class="tab-btn {{ $status === 'Menunggu' ? 'active' : '' }}">
                <span>Menunggu</span>
                <span class="tab-badge">{{ $counts->menunggu }}</span>
            </a>
            <a href="{{ route('waka-sdm.dispensasi', ['status' => 'Disetujui', 'search' => $search, 'id_kelas' => $id_kelas]) }}" class="tab-btn {{ $status === 'Disetujui' ? 'active' : '' }}">
                <span>Disetujui</span>
                <span class="tab-badge">{{ $counts->disetujui }}</span>
            </a>
            <a href="{{ route('waka-sdm.dispensasi', ['status' => 'Ditolak', 'search' => $search, 'id_kelas' => $id_kelas]) }}" class="tab-btn {{ $status === 'Ditolak' ? 'active' : '' }}">
                <span>Ditolak</span>
                <span class="tab-badge">{{ $counts->ditolak }}</span>
            </a>
        </div>

        <form action="{{ route('waka-sdm.dispensasi') }}" method="GET" class="search-group">
            <input type="hidden" name="status" value="{{ $status }}">
            <select name="id_kelas" class="filter-input" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $kls)
                    <option value="{{ $kls->id_kelas }}" {{ $id_kelas == $kls->id_kelas ? 'selected' : '' }}>{{ $kls->nama_kelas }}</option>
                @endforeach
            </select>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="filter-input" onchange="this.form.submit()">
            <input type="text" name="search" value="{{ $search }}" class="filter-input" placeholder="Cari nama siswa / NISN...">
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
            @if($search || ($status && $status !== 'semua') || $id_kelas || $tanggal)
                <a href="{{ route('waka-sdm.dispensasi') }}" class="tab-btn" style="background:#f1f5f9;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Dispensasi Siswa --}}
    <div class="section-card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal</th>
                        <th>Waktu (Jam)</th>
                        <th>Alasan Dispensasi</th>
                        <th>Lampiran</th>
                        <th>Status</th>
                        <th>Persetujuan</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispensasiList as $ds)
                        @php
                            $nama = $ds->siswa->nama_lengkap ?? 'Siswa';
                            $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $nama)));
                            $init = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : 'SW';
                        @endphp
                        <tr>
                            <td>
                                <div class="guru-cell">
                                    <div class="avatar-sm">{{ $init }}</div>
                                    <div>
                                        <div style="font-weight:700; color:#0f172a;">{{ $nama }}</div>
                                        <div style="font-size:12px; color:#64748b;">NISN. {{ $ds->siswa->nisn ?? ($ds->siswa->nis ?? '-') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#1e293b; background:#f1f5f9; padding:4px 10px; border-radius:6px; font-size:12px; white-space:nowrap; display:inline-block;">
                                    {{ $ds->siswa->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#334155;">
                                    {{ \Carbon\Carbon::parse($ds->tanggal)->translatedFormat('d M Y') }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size:13px; font-weight:700; color:#0f172a;">
                                    {{ substr($ds->jam_keluar, 0, 5) }}
                                    @if($ds->jam_kembali)
                                        <span style="color:#64748b; font-weight:500;">- {{ substr($ds->jam_kembali, 0, 5) }}</span>
                                    @else
                                        <span style="color:#94a3b8; font-weight:500;">(Belum kembali)</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="max-width:220px; font-size:13px; color:#475569;">
                                    {{ $ds->alasan }}
                                </div>
                            </td>
                            <td>
                                @if($ds->bukti_file)
                                    <a href="{{ asset('storage/' . $ds->bukti_file) }}" target="_blank" style="color:#16a34a; font-weight:700; text-decoration:none; font-size:12.5px;">
                                        <i class="fa-solid fa-paperclip"></i> Surat Tugas
                                    </a>
                                @else
                                    <span style="color:#cbd5e1; font-size:12px;">-</span>
                                @endif
                            </td>
                            <td>
                                @if(in_array($ds->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai']))
                                    <span class="status-pill disetujui">Disetujui</span>
                                @elseif($ds->status === 'Disetujui_Piket')
                                    <span class="status-pill menunggu" style="background:#dbeafe; color:#1e40af;">Disetujui Piket</span>
                                @elseif($ds->status === 'Ditolak')
                                    <span class="status-pill ditolak">Ditolak</span>
                                @elseif($ds->status === 'Dibatalkan')
                                    <span class="status-pill ditolak" style="background:#fce7f3; color:#9d174d;">Dibatalkan</span>
                                @else
                                    <span class="status-pill menunggu">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if($ds->disetujuiOlehUser)
                                    <div style="font-size:12.5px; font-weight:700; color:#0f172a;">{{ $ds->disetujuiOlehUser->name }}</div>
                                    <div style="font-size:11px; color:#94a3b8;">{{ $ds->tanggal_persetujuan ? \Carbon\Carbon::parse($ds->tanggal_persetujuan)->format('H:i, d M') : '-' }}</div>
                                @else
                                    <span style="color:#94a3b8; font-size:12px;">Menunggu Waka</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
<<<<<<< HEAD
                                    @if(in_array($ds->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai']))
=======
                                    @if($ds->status === 'Disetujui' || $ds->status === 'Disetujui_Waka' || $ds->status === 'Selesai')
>>>>>>> 15462279a3ce11dce17010ba8b2e624622fc525f
                                        <a href="{{ route('guru-piket.dispensasi.cetak', $ds->id) }}" target="_blank" class="btn-act approve" style="text-decoration:none;" title="Cetak Surat Izin Keluar">
                                            <i class="fa-solid fa-print"></i> Slip
                                        </a>
                                    @endif
                                    <button type="button" class="btn-act" style="background:#f1f5f9; color:#475569;" onclick="openDispensasiActionModal({{ $ds->id }}, '{{ addslashes($nama) }}', '{{ $ds->status }}')" title="Detail / Tinjau">
                                        <i class="fa-solid fa-eye"></i> Tinjau
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:40px; color:#94a3b8;">
                                <i class="fa-solid fa-graduation-cap" style="font-size:32px; margin-bottom:8px; display:block;"></i>
                                Tidak ada data pengajuan dispensasi siswa yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $dispensasiList->links() }}
        </div>
    </div>
</div>

{{-- Detail Modal Dispensasi (Read-Only) --}}
<div class="modal-overlay" id="actionDispensasiModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:white; border-radius:20px; width:100%; max-width:440px; box-shadow:0 24px 60px rgba(0,0,0,0.18); overflow:hidden; padding:24px;">
        <h3 id="modalDispActionTitle" style="font-size:17px; font-weight:800; color:#0f172a; margin-bottom:8px;">Detail Dispensasi</h3>
        <p id="modalDispActionDesc" style="font-size:13px; color:#64748b; margin-bottom:16px;"></p>
        <div style="padding:12px; background:#f8fafc; border-radius:12px; margin-bottom:16px;">
            <p style="font-size:12px; color:#94a3b8; margin:0;">Persetujuan dispensasi kini melalui Guru Piket (Tahap 1) dan Waka Piket (Tahap 2).</p>
        </div>
        <div style="display:flex; justify-content:flex-end;">
            <button type="button" class="tab-btn" onclick="closeDispensasiActionModal()">Tutup</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openDispensasiActionModal(id, siswaName, status) {
        document.getElementById('modalDispActionTitle').innerText = `Detail Dispensasi: ${siswaName}`;
        document.getElementById('modalDispActionDesc').innerText = `Status saat ini: ${status}`;

        const modal = document.getElementById('actionDispensasiModal');
        modal.style.display = 'flex';
    }

    function closeDispensasiActionModal() {
        document.getElementById('actionDispensasiModal').style.display = 'none';
    }
</script>
@endsection
