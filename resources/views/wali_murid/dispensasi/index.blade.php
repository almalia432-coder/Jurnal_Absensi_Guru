@extends('layouts.wali_murid')

@section('title', 'Surat Dispensasi - Portal Wali Murid')
@section('header_title', 'Surat Dispensasi')
@section('header_subtitle', 'Riwayat surat dispensasi keluar/kembali siswa')

@section('styles')
<style>
    .info-banner {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 1px solid #c7d2fe; border-radius: 14px;
        padding: 14px 18px; font-size: 13px; color: #3730a3; font-weight: 600;
        margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
    }

    /* ── Dispensasi Cards ── */
    .disp-list { display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px; }
    .disp-card {
        background: white; border-radius: 16px; padding: 22px 24px;
        box-shadow: 0 2px 10px rgba(43,67,185,0.06); border: 1px solid #e8edf8;
        transition: box-shadow 0.2s, transform 0.2s; animation: fadeInUp 0.3s ease-out;
    }
    .disp-card:hover { box-shadow: 0 6px 24px rgba(43,67,185,0.12); transform: translateY(-2px); }
    @keyframes fadeInUp { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }

    .disp-card-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .disp-date-block { display: flex; align-items: center; gap: 14px; }
    .disp-date-icon {
        width: 48px; height: 48px; border-radius: 14px; flex-shrink: 0;
        background: linear-gradient(135deg, #3d56b2, #2b3a8c);
        color: white; display: flex; align-items: center; justify-content: center; font-size: 20px;
    }
    .disp-date-text { font-size: 15px; font-weight: 800; color: #1b2559; }
    .disp-jam { font-size: 12.5px; color: #94a3b8; margin-top: 3px; display: flex; align-items: center; gap: 6px; }

    /* Status Badges */
    .badge-disp { padding: 5px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px; white-space: nowrap; }
    .badge-disp-pending   { background: #fef3c7; color: #92400e; }
    .badge-disp-disetujui { background: #d1fae5; color: #065f46; }
    .badge-disp-ditolak   { background: #fee2e2; color: #991b1b; }

    .disp-divider { height: 1px; background: #f1f5f9; margin: 14px 0; }
    .disp-alasan-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #94a3b8; margin-bottom: 6px; }
    .disp-alasan-text { font-size: 14px; color: #334155; line-height: 1.65; }

    .disp-footer { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; font-size: 12.5px; color: #64748b; margin-top: 14px; padding-top: 12px; border-top: 1px solid #f1f5f9; }
    .disp-footer-item { display: flex; align-items: center; gap: 6px; background: #f8fafc; padding: 4px 10px; border-radius: 8px; font-weight: 600; }

    .disp-bukti-link {
        display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 700;
        color: #2b43b9; text-decoration: none; background: #eef2ff; padding: 5px 12px; border-radius: 8px;
        border: 1px solid #c7d2fe; transition: all 0.2s;
    }
    .disp-bukti-link:hover { background: #c7d2fe; color: #1e3a8a; }

    .empty-state { background: white; border-radius: 16px; padding: 60px 20px; text-align: center; color: #94a3b8; box-shadow: 0 2px 10px rgba(43,67,185,0.06); border: 1px solid #e8edf8; }
    .empty-state i { font-size: 44px; display: block; margin-bottom: 14px; color: #cbd5e1; }
    .empty-state h3 { font-size: 16px; font-weight: 700; color: #64748b; margin-bottom: 6px; }
    .empty-state p { font-size: 13px; }

    .pagination-wrap { display: flex; justify-content: center; padding: 16px 0; }

    @media (max-width: 640px) { .disp-card-header { flex-direction: column; } .disp-date-block { width: 100%; } }
</style>
@endsection

@section('content')
<div class="siswa-info-card">
    <div class="siswa-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
    <div class="siswa-detail">
        <div class="siswa-name">{{ $siswa->nama_lengkap }}</div>
        <div class="siswa-meta">NIS: {{ $siswa->nis ?? '-' }} &nbsp;•&nbsp; Kelas: {{ $siswa->kelas?->nama_kelas ?? '-' }}</div>
    </div>
</div>

<div class="info-banner">
    <i class="fa-solid fa-circle-info" style="font-size:18px;flex-shrink:0;"></i>
    Surat dispensasi adalah izin resmi bagi siswa untuk keluar dari lingkungan sekolah pada jam belajar dengan alasan tertentu yang telah disetujui.
</div>

<div class="disp-list">
    @forelse($dispensasiList as $d)
        @php
            $status = strtolower($d->status ?? 'pending');
            $badgeClass = match($status) { 'disetujui' => 'badge-disp-disetujui', 'ditolak' => 'badge-disp-ditolak', default => 'badge-disp-pending' };
            $statusLabel = match($status) { 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', default => 'Menunggu' };
            $statusIcon  = match($status) { 'disetujui' => 'fa-circle-check', 'ditolak' => 'fa-circle-xmark', default => 'fa-clock' };
        @endphp
        <div class="disp-card">
            <div class="disp-card-header">
                <div class="disp-date-block">
                    <div class="disp-date-icon"><i class="fa-solid fa-file-circle-check"></i></div>
                    <div>
                        <div class="disp-date-text">
                            {{ $d->tanggal ? \Carbon\Carbon::parse($d->tanggal)->translatedFormat('l, d F Y') : '-' }}
                        </div>
                        <div class="disp-jam">
                            <i class="fa-regular fa-clock" style="font-size:11px;"></i>
                            Keluar: {{ $d->jam_keluar ?? '-' }}
                            &nbsp;→&nbsp;
                            Kembali: {{ $d->jam_kembali ?? '-' }}
                        </div>
                    </div>
                </div>
                <span class="badge-disp {{ $badgeClass }}">
                    <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusLabel }}
                </span>
            </div>

            <div class="disp-divider"></div>

            <div class="disp-alasan-label">Alasan Dispensasi</div>
            <div class="disp-alasan-text">{{ $d->alasan ?: '-' }}</div>

            <div class="disp-footer">
                @if($d->diinputOlehUser)
                    <div class="disp-footer-item">
                        <i class="fa-solid fa-user-pen" style="color:#3d56b2;"></i>
                        Diinput: {{ $d->diinputOlehUser->name }}
                    </div>
                @endif
                @if($status === 'disetujui' && $d->disetujuiOlehUser)
                    <div class="disp-footer-item">
                        <i class="fa-solid fa-user-check" style="color:#059669;"></i>
                        Disetujui: {{ $d->disetujuiOlehUser->name }}
                        @if($d->tanggal_persetujuan)
                            ({{ \Carbon\Carbon::parse($d->tanggal_persetujuan)->translatedFormat('d M Y') }})
                        @endif
                    </div>
                @endif
                @if($d->bukti_file)
                    <a href="{{ Storage::url($d->bukti_file) }}" target="_blank" class="disp-bukti-link">
                        <i class="fa-solid fa-paperclip"></i> Lihat Bukti
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fa-solid fa-file-circle-xmark"></i>
            <h3>Belum ada riwayat dispensasi</h3>
            <p>Surat dispensasi yang pernah dibuat akan tampil di sini</p>
        </div>
    @endforelse
</div>

@if($dispensasiList->hasPages())
    <div class="pagination-wrap">{{ $dispensasiList->links() }}</div>
@endif
@endsection
