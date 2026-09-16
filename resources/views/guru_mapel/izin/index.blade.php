@extends('layouts.guru_mapel')

@section('title', 'Pengajuan Izin Mengajar - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Pengajuan Izin Tidak Mengajar')
@section('header_subtitle', 'Ajukan izin berhalangan hadir mengajar dan pantau alur persetujuan bertingkat dari sekolah')

@section('styles')
<style>
    /* Alert Rejection Box */
    .alert-rejected-kbm {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border: 2px solid #ef4444;
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        gap: 18px;
        align-items: flex-start;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.12);
        animation: pulseWarning 2s infinite ease-in-out;
    }
    @keyframes pulseWarning {
        0%, 100% { box-shadow: 0 8px 24px rgba(239, 68, 68, 0.12); }
        50% { box-shadow: 0 8px 30px rgba(239, 68, 68, 0.25); }
    }
    .alert-rejected-kbm .alert-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #ef4444;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .alert-rejected-kbm h4 {
        margin: 0 0 6px 0;
        font-size: 16px;
        font-weight: 800;
        color: #991b1b;
    }
    .alert-rejected-kbm p {
        margin: 0 0 10px 0;
        font-size: 13.5px;
        color: #7f1d1d;
        line-height: 1.5;
    }
    .kbm-obligation-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #dc2626;
        color: #ffffff;
        font-weight: 800;
        font-size: 12.5px;
        padding: 6px 14px;
        border-radius: 10px;
        letter-spacing: 0.3px;
    }

    .two-cols-layout {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 24px;
    }

    .form-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
    }

    .card-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .card-head h3 {
        font-size: 16px;
        font-weight: 800;
        color: #1b2559;
        margin: 0;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #2b3674;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        font-size: 13.5px;
        font-family: inherit;
        color: #1b2559;
        outline: none;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: #2b43b9;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .btn-submit {
        background: #2b43b9;
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(43, 67, 185, 0.25);
        transition: all 0.2s ease;
    }
    .btn-submit:hover {
        background: #1e35a0;
        transform: translateY(-2px);
    }

    /* Table */
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
        border-bottom: 1.5px solid #e2e8f0;
    }
    .custom-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f4f7fe;
        color: #2b3674;
        vertical-align: middle;
    }
    .custom-table tr:hover td { background-color: #f8fafc; }

    /* Stepper Mini Inline */
    .stepper-mini {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
    }
    .step-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 7px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #64748b;
    }
    .step-item.approved { background: #d1fae5; color: #065f46; }
    .step-item.pending  { background: #fef3c7; color: #92400e; }
    .step-item.rejected { background: #fee2e2; color: #991b1b; }

    .status-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-badge.menunggu  { background: #fff7ed; color: #f97316; }
    .status-badge.disetujui { background: #e6f9f0; color: #10b981; }
    .status-badge.ditolak   { background: #fef2f2; color: #ef4444; }

    /* Modal Detail Timeline */
    .modal-backdrop-custom {
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
    .modal-backdrop-custom.active { display: flex; }
    .modal-box-custom {
        background: #ffffff;
        width: 100%;
        max-width: 520px;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    .modal-head-custom {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body-custom { padding: 24px; }
    .timeline-chain {
        position: relative;
        padding-left: 28px;
        margin-top: 14px;
    }
    .timeline-chain::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-step {
        position: relative;
        margin-bottom: 18px;
    }
    .timeline-dot {
        position: absolute;
        left: -28px;
        top: 2px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }
    .timeline-dot.approved { border-color: #10b981; color: #10b981; background: #ecfdf5; }
    .timeline-dot.pending  { border-color: #f59e0b; color: #f59e0b; background: #fffbeb; }
    .timeline-dot.rejected { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    @media (max-width: 1024px) {
        .two-cols-layout { grid-template-columns: 1fr; }
    }
    @media (max-width: 576px) {
        .form-card { padding: 16px; border-radius: 14px; }
        .form-row { grid-template-columns: 1fr; gap: 10px; }
        .btn-submit { width: 100%; justify-content: center; }
    }
</style>
@endsection

@section('content')

{{-- ALERT BANNER: Jika Izin Ditolak dan Guru Harus Melanjutkan KBM --}}
@if($recentRejected)
@php
    $rejectNoticeKey = 'dismissed_reject_banner_' . $recentRejected->id . '_' . ($recentRejected->updated_at ? $recentRejected->updated_at->timestamp : '0');
@endphp
<div class="alert-rejected-kbm" id="bannerRejectedIzin">
    <div class="alert-icon">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <div style="flex: 1;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
            <h4>Pemberitahuan: Permintaan Izin Anda Tidak Disetujui!</h4>
            <button type="button" onclick="dismissRejectBanner('{{ $rejectNoticeKey }}')" title="Tutup Pemberitahuan" style="background: rgba(239, 68, 68, 0.15); border: none; width: 30px; height: 30px; border-radius: 8px; color: #991b1b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; transition: all 0.2s ease;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <p>
            Pengajuan izin kategori <strong>{{ $recentRejected->jenis_izin }}</strong> untuk tanggal 
            <strong>{{ Carbon\Carbon::parse($recentRejected->tanggal_mulai)->translatedFormat('d M Y') }}</strong> 
            telah <strong>DITOLAK</strong> oleh 
            <strong>{{ $recentRejected->penolak_label }}</strong>.
            @if($recentRejected->ditolak_catatan)
                <br><em>Catatan penolakan: "{{ $recentRejected->ditolak_catatan }}"</em>
            @endif
        </p>
        <div class="kbm-obligation-badge">
            <i class="fa-solid fa-chalkboard-user"></i> Harap Tetap Melanjutkan KBM di Kelas Sesuai Jadwal
        </div>
    </div>
</div>
<script>
    (function() {
        if (localStorage.getItem('{{ $rejectNoticeKey }}') === '1') {
            const el = document.getElementById('bannerRejectedIzin');
            if (el) el.style.display = 'none';
        }
    })();
    function dismissRejectBanner(key) {
        localStorage.setItem(key, '1');
        const el = document.getElementById('bannerRejectedIzin');
        if (el) {
            el.style.opacity = '0';
            el.style.transform = 'scale(0.98)';
            el.style.transition = 'all 0.25s ease';
            setTimeout(() => el.remove(), 250);
        }
    }
</script>
@endif

<div class="two-cols-layout">
    <!-- Left: Form Pengajuan Izin -->
    <div class="form-card">
        <div class="card-head">
            <i class="fa-solid fa-file-signature" style="color: #2b43b9; font-size: 18px;"></i>
            <h3>Formulir Izin Tidak Mengajar</h3>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 18px; font-size: 12px; color: #475569; line-height: 1.5;">
            <i class="fa-solid fa-circle-info" style="color: #2b43b9; margin-right: 4px;"></i>
            Pengajuan Anda akan diverifikasi secara berjenjang oleh <strong>Guru Piket &rarr; Waka SDM &rarr; Kepala Sekolah</strong>. Izin resmi berlaku setelah Kepala Sekolah memberikan persetujuan final.
        </div>

        <form action="{{ route('guru-mapel.izin.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Mulai <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="tanggal_mulai" value="{{ $today }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Selesai <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="tanggal_selesai" value="{{ $today }}" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>Jenis Izin <span style="color:#ef4444;">*</span></label>
                <select name="jenis_izin" class="form-control" required>
                    <option value="Sakit">Sakit</option>
                    <option value="Izin" selected>Izin Urusan Pribadi / Keluarga</option>
                    <option value="Dinas_Luar">Tugas Dinas Luar Sekolah</option>
                    <option value="Cuti">Cuti Resmi</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label>Alasan & Titipan Tugas Siswa <span style="color:#ef4444;">*</span></label>
                <textarea name="alasan" rows="5" class="form-control" placeholder="Tuliskan alasan izin serta instruksi materi/tugas mandiri untuk kelas yang ditinggalkan..." required></textarea>
            </div>

            <div class="form-group">
                <label>Lampiran Bukti (Surat Dokter / Surat Tugas - Opsional)</label>
                <input type="file" name="bukti_file" class="form-control" accept="image/*,application/pdf">
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan Izin
                </button>
            </div>
        </form>
    </div>

    <!-- Right: Riwayat Izin -->
    <div class="form-card">
        <div class="card-head">
            <i class="fa-solid fa-clock-rotate-left" style="color: #2b43b9; font-size: 18px;"></i>
            <h3>Riwayat Pengajuan Izin Saya</h3>
        </div>

        <div class="table-responsive-wrap">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tanggal & Jenis</th>
                        <th>Alasan & Bukti</th>
                        <th>Alur 3 Tingkat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinList as $iz)
                    <tr>
                        <td>
                            <strong style="color: #1b2559;">{{ Carbon\Carbon::parse($iz->tanggal_mulai)->translatedFormat('d M Y') }}</strong>
                            @if($iz->tanggal_mulai != $iz->tanggal_selesai)
                                <div style="font-size: 11px; color: #707e94;">s/d {{ Carbon\Carbon::parse($iz->tanggal_selesai)->translatedFormat('d M Y') }}</div>
                            @endif
                            <div style="margin-top: 4px;">
                                <span class="status-badge" style="background: #f1f5f9; color: #334155; font-size: 10.5px;">{{ str_replace('_', ' ', $iz->jenis_izin) }}</span>
                            </div>
                        </td>
                        <td style="max-width: 150px;">
                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 12.5px;" title="{{ $iz->alasan }}">
                                {{ $iz->alasan }}
                            </div>
                            @if($iz->bukti_file)
                                <a href="{{ Storage::url($iz->bukti_file) }}" target="_blank" style="font-size: 11px; color: #2b43b9; font-weight: 700; text-decoration: none;">
                                    <i class="fa-solid fa-paperclip"></i> Bukti
                                </a>
                            @endif
                        </td>
                        <td>
                            {{-- Visual Stepper Progress 3 Tingkat --}}
                            <div class="stepper-mini">
                                {{-- Step 1: Piket --}}
                                @if($iz->piket_status === 'Disetujui')
                                    <span class="step-item approved" title="Disetujui Piket"><i class="fa-solid fa-check"></i> Piket</span>
                                @elseif($iz->piket_status === 'Ditolak')
                                    <span class="step-item rejected" title="Ditolak Piket"><i class="fa-solid fa-xmark"></i> Piket</span>
                                @else
                                    <span class="step-item pending" title="Menunggu Piket"><i class="fa-solid fa-clock"></i> Piket</span>
                                @endif

                                <i class="fa-solid fa-chevron-right" style="font-size: 8px; color: #cbd5e1;"></i>

                                {{-- Step 2: Waka SDM --}}
                                @if($iz->waka_status === 'Disetujui')
                                    <span class="step-item approved" title="Disetujui Waka SDM"><i class="fa-solid fa-check"></i> Waka</span>
                                @elseif($iz->waka_status === 'Ditolak')
                                    <span class="step-item rejected" title="Ditolak Waka SDM"><i class="fa-solid fa-xmark"></i> Waka</span>
                                @else
                                    <span class="step-item {{ $iz->tahap_approval === 'waka_sdm' ? 'pending' : '' }}" title="Menunggu Waka SDM"><i class="fa-solid fa-clock"></i> Waka</span>
                                @endif

                                <i class="fa-solid fa-chevron-right" style="font-size: 8px; color: #cbd5e1;"></i>

                                {{-- Step 3: Kepsek --}}
                                @if($iz->kepsek_status === 'Disetujui')
                                    <span class="step-item approved" title="Disetujui Kepala Sekolah"><i class="fa-solid fa-check"></i> Kepsek</span>
                                @elseif($iz->kepsek_status === 'Ditolak')
                                    <span class="step-item rejected" title="Ditolak Kepala Sekolah"><i class="fa-solid fa-xmark"></i> Kepsek</span>
                                @else
                                    <span class="step-item {{ $iz->tahap_approval === 'kepsek' ? 'pending' : '' }}" title="Menunggu Kepala Sekolah"><i class="fa-solid fa-clock"></i> Kepsek</span>
                                @endif
                            </div>

                            <button type="button" onclick="showDetailModal({{ $iz->toJson() }})" style="background:none; border:none; color:#2b43b9; font-size:11px; font-weight:700; cursor:pointer; padding:2px 0; margin-top:4px; display:inline-flex; align-items:center; gap:3px;">
                                <i class="fa-solid fa-circle-info"></i> Detail Alur
                            </button>
                        </td>
                        <td>
                            @if($iz->status === 'Disetujui')
                                <span class="status-badge disetujui"><i class="fa-solid fa-circle-check"></i> Disetujui</span>
                            @elseif($iz->status === 'Ditolak')
                                <span class="status-badge ditolak"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>
                                <div style="font-size: 10px; color: #ef4444; font-weight: 700; margin-top: 2px;">
                                    Wajib Lanjut KBM
                                </div>
                            @else
                                <span class="status-badge menunggu"><i class="fa-solid fa-spinner fa-spin"></i> Proses</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #707e94; padding: 36px;">
                            <i class="fa-solid fa-calendar-check" style="font-size: 32px; margin-bottom: 8px; color: #cbd5e1; display: block;"></i>
                            <div>Belum ada riwayat pengajuan izin yang tercatat.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $izinList->links() }}
        </div>
    </div>
</div>

<!-- Modal Detail Alur & Catatan Persetujuan -->
<div id="modalDetailIzin" class="modal-backdrop-custom">
    <div class="modal-box-custom">
        <div class="modal-head-custom">
            <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                <i class="fa-solid fa-diagram-project" style="color: #2b43b9; margin-right: 6px;"></i> Detail Status Alur Persetujuan
            </h4>
            <button type="button" onclick="closeDetailModal()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #94a3b8;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <div id="detailRejectBanner" style="display: none; background: #fef2f2; border: 1.5px solid #f87171; border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                <div style="font-weight: 800; color: #991b1b; font-size: 13.5px; margin-bottom: 4px;">
                    <i class="fa-solid fa-circle-xmark"></i> Pengajuan Izin Ditolak
                </div>
                <div style="font-size: 12.5px; color: #7f1d1d;" id="detailRejectText"></div>
                <div style="margin-top: 8px; font-size: 12px; font-weight: 800; color: #b91c1c; background: #fee2e2; padding: 6px 10px; border-radius: 8px;">
                    <i class="fa-solid fa-chalkboard-user"></i> Instruksi: Anda diwajibkan untuk tetap melaksanakan / melanjutkan KBM di kelas.
                </div>
            </div>

            <div id="detailApproveBanner" style="display: none; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                <div style="font-weight: 800; color: #166534; font-size: 13.5px;">
                    <i class="fa-solid fa-circle-check"></i> Izin Telah Disetujui Penuh
                </div>
                <div style="font-size: 12.5px; color: #15803d; margin-top: 2px;">
                    Pengajuan izin Anda telah disetujui resmi oleh Guru Piket, Waka SDM, dan Kepala Sekolah.
                </div>
            </div>

            <div style="font-size: 13px; color: #334155; margin-bottom: 12px;">
                <strong>Tahapan Persetujuan Berjenjang:</strong>
            </div>

            <div class="timeline-chain">
                <!-- Tahap 1: Guru Piket -->
                <div class="timeline-step">
                    <div class="timeline-dot" id="dotPiket">1</div>
                    <div style="font-weight: 800; font-size: 13px; color: #0f172a;">Tahap 1: Guru Piket</div>
                    <div style="font-size: 12px; color: #64748b;" id="textPiketStatus">Status: Menunggu</div>
                    <div style="font-size: 12px; color: #475569; font-style: italic;" id="catatanPiket"></div>
                </div>

                <!-- Tahap 2: Waka SDM -->
                <div class="timeline-step">
                    <div class="timeline-dot" id="dotWaka">2</div>
                    <div style="font-weight: 800; font-size: 13px; color: #0f172a;">Tahap 2: Waka SDM</div>
                    <div style="font-size: 12px; color: #64748b;" id="textWakaStatus">Status: Menunggu</div>
                    <div style="font-size: 12px; color: #475569; font-style: italic;" id="catatanWaka"></div>
                </div>

                <!-- Tahap 3: Kepala Sekolah -->
                <div class="timeline-step" style="margin-bottom: 0;">
                    <div class="timeline-dot" id="dotKepsek">3</div>
                    <div style="font-weight: 800; font-size: 13px; color: #0f172a;">Tahap 3: Kepala Sekolah (Final)</div>
                    <div style="font-size: 12px; color: #64748b;" id="textKepsekStatus">Status: Menunggu</div>
                    <div style="font-size: 12px; color: #475569; font-style: italic;" id="catatanKepsek"></div>
                </div>
            </div>
        </div>
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: right;">
            <button type="button" onclick="closeDetailModal()" style="padding: 8px 18px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function showDetailModal(iz) {
        const modal = document.getElementById('modalDetailIzin');
        const rejectBanner = document.getElementById('detailRejectBanner');
        const rejectText = document.getElementById('detailRejectText');
        const approveBanner = document.getElementById('detailApproveBanner');

        // Banners
        if (iz.status === 'Ditolak') {
            rejectBanner.style.display = 'block';
            approveBanner.style.display = 'none';
            const role = iz.ditolak_oleh_role === 'guru_piket' ? 'Guru Piket' : (iz.ditolak_oleh_role === 'waka_sdm' ? 'Waka SDM' : 'Kepala Sekolah');
            rejectText.innerHTML = `Ditolak pada tahap <strong>${role}</strong>.` + (iz.ditolak_catatan ? ` Catatan: "${iz.ditolak_catatan}"` : '');
        } else if (iz.status === 'Disetujui') {
            rejectBanner.style.display = 'none';
            approveBanner.style.display = 'block';
        } else {
            rejectBanner.style.display = 'none';
            approveBanner.style.display = 'none';
        }

        // Piket
        const dotPiket = document.getElementById('dotPiket');
        const textPiket = document.getElementById('textPiketStatus');
        const catPiket = document.getElementById('catatanPiket');
        updateStepUI(dotPiket, textPiket, catPiket, iz.piket_status, iz.piket_approver?.name, iz.piket_catatan, iz.piket_at);

        // Waka SDM
        const dotWaka = document.getElementById('dotWaka');
        const textWaka = document.getElementById('textWakaStatus');
        const catWaka = document.getElementById('catatanWaka');
        updateStepUI(dotWaka, textWaka, catWaka, iz.waka_status, iz.waka_approver?.name, iz.waka_catatan, iz.waka_at);

        // Kepsek
        const dotKepsek = document.getElementById('dotKepsek');
        const textKepsek = document.getElementById('textKepsekStatus');
        const catKepsek = document.getElementById('catatanKepsek');
        updateStepUI(dotKepsek, textKepsek, catKepsek, iz.kepsek_status, iz.kepsek_approver?.name, iz.kepsek_catatan, iz.kepsek_at);

        modal.classList.add('active');
    }

    function updateStepUI(dot, text, cat, status, approverName, catatan, timestamp) {
        dot.className = 'timeline-dot';
        if (status === 'Disetujui') {
            dot.classList.add('approved');
            dot.innerHTML = '<i class="fa-solid fa-check"></i>';
            text.innerHTML = `<span style="color:#059669; font-weight:700;">Disetujui</span> ${approverName ? 'oleh ' + approverName : ''}`;
        } else if (status === 'Ditolak') {
            dot.classList.add('rejected');
            dot.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            text.innerHTML = `<span style="color:#dc2626; font-weight:700;">Ditolak</span> ${approverName ? 'oleh ' + approverName : ''}`;
        } else {
            dot.classList.add('pending');
            dot.innerHTML = '<i class="fa-solid fa-clock"></i>';
            text.innerHTML = `<span style="color:#b45309; font-weight:600;">Menunggu Peninjauan</span>`;
        }
        cat.innerHTML = catatan ? `Catatan: "${catatan}"` : '';
    }

    function closeDetailModal() {
        document.getElementById('modalDetailIzin').classList.remove('active');
    }

    document.getElementById('modalDetailIzin').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailModal();
        }
    });
</script>
@endsection
