@extends('layouts.waka_sdm')

@section('title', 'Persetujuan Izin Guru - Waka SDM')
@section('header_title', 'Persetujuan Izin Guru')
@section('header_subtitle', 'Verifikasi, tinjau berkas pendukung, dan kelola persetujuan izin ketidakhadiran guru')

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
        background: #2563eb; color: white; border-color: #2563eb; box-shadow: 0 4px 12px rgba(37,99,235,0.2);
    }
    .tab-badge {
        font-size: 11px; padding: 1px 7px; border-radius: 20px; background: rgba(0,0,0,0.08);
    }
    .tab-btn.active .tab-badge { background: rgba(255,255,255,0.25); color: white; }

    .search-group { display: flex; align-items: center; gap: 10px; }
    .filter-input {
        padding: 8px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1;
        font-size: 13px; font-weight: 600; color: #1e293b; outline: none; font-family: inherit;
    }
    .filter-input:focus { border-color: #2563eb; }
    .btn-search {
        padding: 8px 16px; border-radius: 10px; background: #2563eb; color: white;
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
        width: 36px; height: 36px; border-radius: 50%; background: #3b82f6; color: white;
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
    {{-- Filter Bar --}}
    <div class="filter-card">
        <div class="status-tabs">
            <a href="{{ route('waka-sdm.izin', ['status' => 'semua', 'search' => $search]) }}" class="tab-btn {{ empty($status) || $status === 'semua' ? 'active' : '' }}">
                <span>Semua</span>
                <span class="tab-badge">{{ $counts->semua }}</span>
            </a>
            <a href="{{ route('waka-sdm.izin', ['status' => 'Menunggu', 'search' => $search]) }}" class="tab-btn {{ $status === 'Menunggu' ? 'active' : '' }}">
                <span>Menunggu</span>
                <span class="tab-badge">{{ $counts->menunggu }}</span>
            </a>
            <a href="{{ route('waka-sdm.izin', ['status' => 'Disetujui', 'search' => $search]) }}" class="tab-btn {{ $status === 'Disetujui' ? 'active' : '' }}">
                <span>Disetujui</span>
                <span class="tab-badge">{{ $counts->disetujui }}</span>
            </a>
            <a href="{{ route('waka-sdm.izin', ['status' => 'Ditolak', 'search' => $search]) }}" class="tab-btn {{ $status === 'Ditolak' ? 'active' : '' }}">
                <span>Ditolak</span>
                <span class="tab-badge">{{ $counts->ditolak }}</span>
            </a>
        </div>

        <form action="{{ route('waka-sdm.izin') }}" method="GET" class="search-group">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}" class="filter-input" placeholder="Cari nama guru atau alasan...">
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
            @if($search || ($status && $status !== 'semua'))
                <a href="{{ route('waka-sdm.izin') }}" class="tab-btn" style="background:#f1f5f9;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Izin Guru --}}
    <div class="section-card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Guru Pengajar</th>
                        <th>Jenis Izin</th>
                        <th>Periode Tanggal</th>
                        <th>Alasan</th>
                        <th>Berkas</th>
                        <th>Status</th>
                        <th>Persetujuan</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinList as $iz)
                        @php
                            $nama = $iz->guru->nama_lengkap ?? 'Guru';
                            $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $nama)));
                            $init = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : 'GR';
                        @endphp
                        <tr>
                            <td>
                                <div class="guru-cell">
                                    <div class="avatar-sm">{{ $init }}</div>
                                    <div>
                                        <div style="font-weight:700; color:#0f172a;">{{ $nama }}</div>
                                        <div style="font-size:12px; color:#64748b;">NIP. {{ $iz->guru->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#1e293b;">{{ $iz->jenis_izin }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#334155;">
                                    {{ \Carbon\Carbon::parse($iz->tanggal_mulai)->translatedFormat('d M Y') }}
                                </div>
                                <div style="font-size:12px; color:#94a3b8;">
                                    s/d {{ \Carbon\Carbon::parse($iz->tanggal_selesai)->translatedFormat('d M Y') }}
                                </div>
                            </td>
                            <td>
                                <div style="max-width:240px; font-size:13px; color:#475569;">
                                    {{ $iz->alasan }}
                                </div>
                            </td>
                            <td>
                                @if($iz->bukti_file)
                                    <a href="{{ asset('storage/' . $iz->bukti_file) }}" target="_blank" style="color:#2563eb; font-weight:700; text-decoration:none; font-size:12.5px;">
                                        <i class="fa-solid fa-paperclip"></i> Lampiran
                                    </a>
                                @else
                                    <span style="color:#cbd5e1; font-size:12px;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($iz->status === 'Disetujui')
                                    <span class="status-pill disetujui">Disetujui</span>
                                @elseif($iz->status === 'Ditolak')
                                    <span class="status-pill ditolak">Ditolak</span>
                                @else
                                    <span class="status-pill menunggu">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if($iz->disetujuiOlehUser)
                                    <div style="font-size:12.5px; font-weight:700; color:#0f172a;">{{ $iz->disetujuiOlehUser->name }}</div>
                                    @if($iz->catatan_persetujuan)
                                        <div style="font-size:11.5px; color:#64748b; font-style:italic;">"{{ $iz->catatan_persetujuan }}"</div>
                                    @endif
                                @else
                                    <span style="color:#94a3b8; font-size:12px;">Belum diproses</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
                                    <button type="button" class="btn-act approve" onclick="openActionModal({{ $iz->id }}, '{{ addslashes($nama) }}', 'Disetujui')">
                                        <i class="fa-solid fa-check"></i> Setujui
                                    </button>
                                    <button type="button" class="btn-act reject" onclick="openActionModal({{ $iz->id }}, '{{ addslashes($nama) }}', 'Ditolak')">
                                        <i class="fa-solid fa-xmark"></i> Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:40px; color:#94a3b8;">
                                <i class="fa-solid fa-inbox" style="font-size:32px; margin-bottom:8px; display:block;"></i>
                                Tidak ada data permohonan izin guru yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $izinList->links() }}
        </div>
    </div>
</div>

{{-- Action Modal --}}
<div class="modal-overlay" id="actionModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:white; border-radius:20px; width:100%; max-width:440px; box-shadow:0 24px 60px rgba(0,0,0,0.18); overflow:hidden; padding:24px;">
        <h3 id="modalActionTitle" style="font-size:17px; font-weight:800; color:#0f172a; margin-bottom:8px;">Konfirmasi Persetujuan</h3>
        <p id="modalActionDesc" style="font-size:13px; color:#64748b; margin-bottom:16px;"></p>
        
        <form id="actionForm" method="POST">
            @csrf
            <input type="hidden" name="status" id="actionStatusInput">
            <div style="margin-bottom:18px;">
                <label style="font-size:12.5px; font-weight:700; color:#1e293b; display:block; margin-bottom:6px;">
                    Catatan Persetujuan (Opsional):
                </label>
                <textarea name="catatan" class="filter-input" style="width:100%; height:70px; resize:vertical;" placeholder="Tambahkan catatan jika ada..."></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="tab-btn" onclick="closeActionModal()">Batal</button>
                <button type="submit" id="btnActionSubmit" class="btn-search">Konfirmasi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openActionModal(id, guruName, status) {
        document.getElementById('actionForm').action = `/waka-sdm/izin/${id}/status`;
        document.getElementById('actionStatusInput').value = status;
        document.getElementById('modalActionTitle').innerText = `${status} Izin Guru`;
        document.getElementById('modalActionDesc').innerText = `Apakah Anda yakin ingin mengubah status pengajuan izin untuk ${guruName} menjadi ${status}?`;
        
        const btn = document.getElementById('btnActionSubmit');
        if (status === 'Disetujui') {
            btn.style.background = '#059669';
            btn.innerText = 'Ya, Setujui';
        } else {
            btn.style.background = '#dc2626';
            btn.innerText = 'Ya, Tolak';
        }

        const modal = document.getElementById('actionModal');
        modal.style.display = 'flex';
    }

    function closeActionModal() {
        document.getElementById('actionModal').style.display = 'none';
    }
</script>
@endsection
