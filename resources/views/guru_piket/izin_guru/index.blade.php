@extends('layouts.guru_piket')

@section('title', 'Monitoring & Persetujuan Izin Guru - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Persetujuan & Monitoring Izin Guru')
@section('header_subtitle', 'Verifikasi pengajuan izin berhalangan mengajar (Tahap 1) dan pantau jadwal kelas terdampak')

@section('styles')
<style>
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 24px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
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

    .two-cols-layout {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .section-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
        margin-bottom: 24px;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #1b2559;
        display: flex;
        align-items: center;
        gap: 10px;
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
        padding: 14px;
        border-bottom: 1px solid #f4f7fe;
        color: #2b3674;
        vertical-align: middle;
    }
    .custom-table tr:hover td { background-color: #f8fafc; }

    /* Badges */
    .status-badge {
        font-size: 10.5px;
        font-weight: 800;
        padding: 4px 9px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-badge.warning { background: #fff7ed; color: #f97316; }
    .status-badge.danger  { background: #fef2f2; color: #ef4444; }
    .status-badge.success { background: #e6f9f0; color: #10b981; }
    .status-badge.info    { background: #e0e7ff; color: #3b82f6; }
    .status-badge.purple  { background: #f3e8ff; color: #8b5cf6; }

    /* Stage Pill */
    .stage-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
        background: #f1f5f9;
        color: #475569;
    }

    /* Action Buttons */
    .btn-action-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-approve {
        background: #10b981;
        color: #ffffff;
        border: none;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-approve:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
    }
    .btn-reject {
        background: #ef4444;
        color: #ffffff;
        border: none;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-reject:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
    }

    /* Modal Backdrop & Container */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-backdrop.active { display: flex; }
    .modal-box {
        background: #ffffff;
        width: 100%;
        max-width: 480px;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        animation: modalFadeIn 0.2s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(12px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-head {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-head h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-foot {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    @media (max-width: 1024px) {
        .two-cols-layout { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .filter-card { flex-direction: column; align-items: stretch; padding: 16px; }
        .filter-input { width: 100%; }
        .section-card { padding: 16px; border-radius: 14px; }
    }
</style>
@endsection

@section('content')
<!-- Filter Tanggal & Info -->
<div class="filter-card">
    <form method="GET" action="{{ route('guru-piket.izin-guru') }}" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <span style="font-size: 13px; font-weight: 700; color: #707e94;">Pilih Tanggal:</span>
        <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="filter-input">
        <noscript><button type="submit" class="btn-approve" style="padding: 8px 12px;">Terapkan</button></noscript>
    </form>

    <div style="font-size: 13.5px; font-weight: 700; color: #2b43b9; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-calendar-day"></i> {{ $todayFormatted }}
    </div>
</div>

<!-- SECTION 1: Permohonan Izin Menunggu Persetujuan Guru Piket (Tahap 1) -->
<div class="section-card" style="border-left: 5px solid #2b43b9;">
    <div class="section-header">
        <div class="section-title">
            <i class="fa-solid fa-user-clock" style="color: #2b43b9; font-size: 18px;"></i>
            <span>Persetujuan Izin Guru Baru (Tahap 1 - Guru Piket)</span>
            <span class="status-badge {{ $pendingPiketList->count() > 0 ? 'warning' : 'success' }}" style="border-radius: 12px; font-size: 11.5px;">
                {{ $pendingPiketList->count() }} Menunggu
            </span>
        </div>
        <div style="font-size: 12px; color: #64748b; font-weight: 600;">
            Alur: <strong>Guru Piket</strong> &rarr; Waka SDM &rarr; Kepala Sekolah
        </div>
    </div>

    <div class="table-responsive-wrap">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Guru Pengampu</th>
                    <th>Kategori</th>
                    <th>Rentang Tanggal</th>
                    <th>Alasan & Keterangan</th>
                    <th>Berkas</th>
                    <th style="text-align: right;">Aksi Keputusan Piket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingPiketList as $pIzin)
                <tr>
                    <td>
                        <strong style="color: #1b2559; font-size: 13.5px;">{{ $pIzin->guru->nama_lengkap ?? 'Guru' }}</strong>
                        <div style="font-size: 11.5px; color: #6b7a99;">
                            NIP: {{ $pIzin->guru->nip ?? '-' }} &bull; {{ $pIzin->guru->no_hp ?? '-' }}
                        </div>
                    </td>
                    <td>
                        <span class="status-badge warning">{{ str_replace('_', ' ', $pIzin->jenis_izin) }}</span>
                    </td>
                    <td>
                        <strong style="color: #0f172a;">{{ Carbon\Carbon::parse($pIzin->tanggal_mulai)->translatedFormat('d M Y') }}</strong>
                        @if($pIzin->tanggal_mulai != $pIzin->tanggal_selesai)
                            <div style="font-size: 11.5px; color: #64748b;">
                                s/d {{ Carbon\Carbon::parse($pIzin->tanggal_selesai)->translatedFormat('d M Y') }}
                            </div>
                        @endif
                    </td>
                    <td style="max-width: 240px;">
                        <div style="font-size: 13px; color: #334155;">{{ $pIzin->alasan }}</div>
                    </td>
                    <td>
                        @if($pIzin->bukti_file)
                            <a href="{{ Storage::url($pIzin->bukti_file) }}" target="_blank" style="font-size: 11.5px; color: #2b43b9; font-weight: 700; text-decoration: none;">
                                <i class="fa-solid fa-paperclip"></i> Lihat Lampiran
                            </a>
                        @else
                            <span style="color: #94a3b8; font-size: 12px;">-</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="btn-action-group" style="justify-content: flex-end;">
                            <button type="button" class="btn-approve" onclick="openApprovalModal({{ $pIzin->id }}, '{{ addslashes($pIzin->guru->nama_lengkap ?? 'Guru') }}', 'Disetujui')">
                                <i class="fa-solid fa-check"></i> Setujui
                            </button>
                            <button type="button" class="btn-reject" onclick="openApprovalModal({{ $pIzin->id }}, '{{ addslashes($pIzin->guru->nama_lengkap ?? 'Guru') }}', 'Ditolak')">
                                <i class="fa-solid fa-xmark"></i> Tolak
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7a99; padding: 28px;">
                        <i class="fa-solid fa-circle-check" style="font-size: 28px; color: #10b981; margin-bottom: 6px; display: block;"></i>
                        <div>Tidak ada pengajuan izin guru yang sedang menunggu persetujuan Guru Piket.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- SECTION 2: Dua Kolom (Guru Izin Hari Ini & Jadwal Terdampak) -->
<div class="two-cols-layout">
    <!-- Left: Daftar Guru Izin Hari Ini -->
    <div class="section-card" style="margin-bottom: 0;">
        <div class="section-header">
            <div class="section-title">
                <i class="fa-solid fa-user-xmark" style="color: #f97316;"></i>
                <span>Guru Izin pada Tanggal Ini ({{ $izinGuruList->count() }})</span>
            </div>
        </div>

        <div class="table-responsive-wrap">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Guru</th>
                        <th>Kategori</th>
                        <th>Status Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinGuruList as $iz)
                    <tr>
                        <td>
                            <strong style="color: #1b2559; font-size: 13.5px;">{{ $iz->guru->nama_lengkap ?? 'Guru' }}</strong>
                            <div style="font-size: 11.5px; color: #6b7a99;">
                                NIP: {{ $iz->guru->nip ?? '-' }} &bull; {{ $iz->alasan }}
                            </div>
                        </td>
                        <td>
                            <span class="status-badge warning">{{ str_replace('_', ' ', $iz->jenis_izin) }}</span>
                        </td>
                        <td>
                            @if($iz->status === 'Disetujui')
                                <span class="status-badge success"><i class="fa-solid fa-circle-check"></i> Disetujui Penuh</span>
                            @elseif($iz->status === 'Ditolak')
                                <span class="status-badge danger"><i class="fa-solid fa-circle-xmark"></i> Ditolak ({{ $iz->ditolak_oleh_role ?? 'Sekolah' }})</span>
                            @else
                                <span class="status-badge info">
                                    <i class="fa-solid fa-spinner fa-spin"></i> {{ $iz->tahap_label }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #6b7a99; padding: 28px;">
                            <i class="fa-solid fa-circle-check" style="font-size: 26px; color: #10b981; margin-bottom: 6px; display: block;"></i>
                            <div>Tidak ada guru yang mengajukan izin pada tanggal terpilih.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Jadwal & Kelas Terdampak -->
    <div class="section-card" style="margin-bottom: 0;">
        <div class="section-header">
            <div class="section-title">
                <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i>
                <span>Jadwal Kelas Terdampak (Perlu Ditindaklanjuti)</span>
            </div>
        </div>

        <div class="table-responsive-wrap">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Kelas & Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Berhalangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalTerdampak as $jt)
                    @php
                        $iz = $jt->izin_guru;
                        $hasTugas = $iz && $iz->menitipkan_tugas;
                        $isJurnalFilled = (bool) $jt->jurnal_terisi;
                    @endphp
                    <tr>
                        <td>
                            <strong style="color: #1b2559; font-size: 13.5px;">{{ $jt->kelas->nama_kelas ?? '-' }}</strong>
                            <div style="font-size: 11.5px; color: #ef4444; font-weight: 700;">
                                <i class="fa-solid fa-clock"></i> Jam ke-{{ $jt->jam_ke }} ({{ Carbon\Carbon::parse($jt->jam_mulai)->format('H:i') }} - {{ Carbon\Carbon::parse($jt->jam_selesai)->format('H:i') }})
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #2b3674;">{{ $jt->mapel->nama_mapel ?? '-' }}</div>
                            <div style="font-size: 11px; color: #6b7a99;">Kode: {{ $jt->mapel->kode_mapel ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="color: #1b2559; font-weight: 700;">{{ $jt->guru->nama_lengkap ?? '-' }}</div>
                            <div style="margin-top: 4px;">
                                @if($hasTugas)
                                    <span class="status-badge" style="background: #dcfce7; color: #166534; border: 1px solid #86efac; font-size: 11px; padding: 4px 8px;">
                                        <i class="fa-solid fa-circle-check"></i> Sudah Ada Tugas
                                    </span>
                                @else
                                    <span class="status-badge" style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; font-size: 11px; padding: 4px 8px;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Butuh Pantauan / Pengganti
                                    </span>
                                @endif
                            </div>

                            <div style="margin-top: 8px; display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                                @if($hasTugas)
                                    <button type="button" onclick="showTugasModal({{ json_encode([
                                        'guru'       => $jt->guru->nama_lengkap ?? '-',
                                        'kelas'      => $jt->kelas->nama_kelas ?? '-',
                                        'mapel'      => $jt->mapel->nama_mapel ?? '-',
                                        'jam'        => 'Jam ke-' . $jt->jam_ke,
                                        'keterangan' => $iz->keterangan_tugas,
                                        'file'       => $iz->lampiran_tugas ? Storage::url($iz->lampiran_tugas) : null
                                    ]) }})" style="padding: 5px 10px; border-radius: 8px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 11px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-file-lines"></i> Lihat Tugas
                                    </button>
                                @endif

                                @if($isJurnalFilled)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 5px 10px; border-radius: 8px;">
                                        <i class="fa-solid fa-clipboard-check"></i> Jurnal Terisi
                                    </span>
                                @else
                                    <a href="{{ route('guru-piket.jurnal.pendampingan', ['id_jadwal' => $jt->id_jadwal, 'tanggal' => $tanggal]) }}" style="padding: 5px 10px; border-radius: 8px; background: #2b43b9; border: none; color: #ffffff; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 6px rgba(43,67,185,0.25);">
                                        <i class="fa-solid fa-clipboard-user"></i> Isi Jurnal Piket
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #6b7a99; padding: 28px;">
                            <i class="fa-solid fa-circle-check" style="font-size: 26px; color: #10b981; margin-bottom: 6px; display: block;"></i>
                            <div>Tidak ada jadwal kelas yang berhalangan hadir pada hari ini.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Persetujuan / Penolakan -->
<div id="modalApprovalPiket" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-head">
            <h4 id="modalTitle">Konfirmasi Persetujuan Izin</h4>
            <button type="button" onclick="closeApprovalModal()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #94a3b8;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="formApprovalPiket" method="POST" action="">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="status" id="modalStatusInput" value="">
                
                <p id="modalDesc" style="font-size: 13.5px; color: #334155; margin-bottom: 16px;">
                    Apakah Anda yakin ingin menyetujui pengajuan izin ini?
                </p>

                <div id="alertRejectWarning" style="display: none; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px; margin-bottom: 14px; font-size: 12.5px; color: #991b1b;">
                    <i class="fa-solid fa-circle-exclamation"></i> <strong>Peringatan Penolakan:</strong> Pengajuan izin akan dibatalkan, dan sistem akan langsung menotifikasi guru pengampu untuk <strong>tetap hadir dan melanjutkan KBM</strong>.
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                        Catatan Piket (Opsional untuk Setujui, Disarankan untuk Tolak):
                    </label>
                    <textarea name="catatan" id="modalCatatanInput" rows="3" class="filter-input" style="width: 100%; box-sizing: border-box;" placeholder="Tambahkan catatan atau alasan keputusan..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" onclick="closeApprovalModal()" style="padding: 8px 16px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" id="modalSubmitBtn" class="btn-approve">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Tugas Mandiri untuk Piket -->
<div id="modalTugasPiket" class="modal-backdrop">
    <div class="modal-box" style="max-width: 520px;">
        <div class="modal-head">
            <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                <i class="fa-solid fa-book-open-reader" style="color: #2b43b9; margin-right: 6px;"></i> Detail Tugas Mandiri Siswa
            </h4>
            <button type="button" onclick="closeTugasModal()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #94a3b8;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" style="padding: 20px 24px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px;">
                <div style="font-size: 13.5px; color: #1e293b; font-weight: 800;" id="tugasModalKelasMapel"></div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;" id="tugasModalGuru"></div>
            </div>

            <label style="display: block; font-size: 12.5px; font-weight: 800; color: #1e293b; margin-bottom: 6px;">
                Instruksi / Catatan Tugas untuk Siswa:
            </label>
            <div id="tugasModalKeterangan" style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 12px 14px; font-size: 13px; color: #334155; line-height: 1.5; white-space: pre-wrap; margin-bottom: 16px; max-height: 200px; overflow-y: auto;"></div>

            <div id="tugasModalFileWrap" style="display: none; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px; margin-bottom: 12px;">
                <div style="font-size: 12px; font-weight: 700; color: #1e40af; margin-bottom: 6px;">
                    <i class="fa-solid fa-paperclip"></i> Dokumen / Soal Tugas Terlampir:
                </div>
                <a id="tugasModalFileLink" href="#" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; color: #ffffff; background: #2b43b9; padding: 7px 14px; border-radius: 8px; text-decoration: none;">
                    <i class="fa-solid fa-download"></i> Unduh File Tugas
                </a>
            </div>
        </div>
        <div class="modal-foot" style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: right;">
            <button type="button" onclick="closeTugasModal()" style="padding: 8px 18px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function showTugasModal(data) {
        document.getElementById('tugasModalKelasMapel').textContent = data.kelas + ' • ' + data.mapel + ' (' + data.jam + ')';
        document.getElementById('tugasModalGuru').textContent = 'Guru Pengampu: ' + data.guru;
        document.getElementById('tugasModalKeterangan').textContent = data.keterangan || 'Tidak ada catatan teks tambahan.';
        const fileWrap = document.getElementById('tugasModalFileWrap');
        const fileLink = document.getElementById('tugasModalFileLink');
        if (data.file) {
            fileWrap.style.display = 'block';
            fileLink.href = data.file;
        } else {
            fileWrap.style.display = 'none';
        }
        document.getElementById('modalTugasPiket').classList.add('active');
    }

    function closeTugasModal() {
        document.getElementById('modalTugasPiket').classList.remove('active');
    }

    document.getElementById('modalTugasPiket').addEventListener('click', function(e) {
        if (e.target === this) {
            closeTugasModal();
        }
    });

    function openApprovalModal(id, namaGuru, status) {
        const form = document.getElementById('formApprovalPiket');
        form.action = `/guru-piket/izin-guru/${id}/status`;

        const statusInput = document.getElementById('modalStatusInput');
        statusInput.value = status;

        const title = document.getElementById('modalTitle');
        const desc = document.getElementById('modalDesc');
        const warning = document.getElementById('alertRejectWarning');
        const submitBtn = document.getElementById('modalSubmitBtn');
        const catatan = document.getElementById('modalCatatanInput');
        catatan.value = '';

        if (status === 'Disetujui') {
            title.textContent = 'Setujui Izin Guru (Lanjut ke Waka SDM)';
            desc.innerHTML = `Setujui permohonan izin dari <strong>${namaGuru}</strong>? Pengajuan ini akan diteruskan ke sistem Waka SDM untuk persetujuan tahap 2.`;
            warning.style.display = 'none';
            submitBtn.className = 'btn-approve';
            submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> Ya, Setujui & Teruskan';
        } else {
            title.textContent = 'Tolak Izin Guru';
            desc.innerHTML = `Tolak permohonan izin dari <strong>${namaGuru}</strong>?`;
            warning.style.display = 'block';
            submitBtn.className = 'btn-reject';
            submitBtn.innerHTML = '<i class="fa-solid fa-xmark"></i> Tolak Izin (Wajib Lanjut KBM)';
        }

        document.getElementById('modalApprovalPiket').classList.add('active');
    }

    function closeApprovalModal() {
        document.getElementById('modalApprovalPiket').classList.remove('active');
    }

    // Close when click backdrop
    document.getElementById('modalApprovalPiket').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApprovalModal();
        }
    });
</script>
@endsection
