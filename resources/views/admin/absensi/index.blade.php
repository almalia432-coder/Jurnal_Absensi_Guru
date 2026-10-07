@extends('layouts.admin')

@section('title', 'Absensi & Jurnal Mengajar — SMKN 1 BOYOLANGU')
@section('header_title', 'Absensi & Jurnal Mengajar')
@section('header_subtitle', 'Jurnal Absensi Guru & Siswa SMKN 1 BOYOLANGU')

@section('styles')
<style>
    /* ── Absensi Hero Banner ── */
    .absensi-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 24px 28px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(15,23,42,0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }
    .absensi-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-text-group h2 {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .hero-text-group p {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
    }
    .hero-date-badge {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.15);
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 700;
        color: #38bdf8;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* ── KPI Summary Cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.07);
        border-color: #cbd5e1;
    }
    .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .kpi-icon.indigo  { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #3730a3; }
    .kpi-icon.emerald { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .kpi-icon.amber   { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .kpi-icon.rose    { background: linear-gradient(135deg, #ffe4e6, #fecdd3); color: #9f1239; }
    
    .kpi-val { font-size: 30px; font-weight: 900; color: #0f172a; line-height: 1.1; letter-spacing: -0.5px; }
    .kpi-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 3px; }
    .kpi-sub { font-size: 11px; font-weight: 600; color: #94a3b8; margin-top: 2px; }

    /* ── Filter Bar ── */
    .filter-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .filter-group { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; flex: 1; }
    .filter-item { display: flex; flex-direction: column; gap: 6px; }
    .filter-label { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: .5px; }
    
    .filter-input, .filter-select {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
        min-width: 170px;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: #2b43b9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(43,67,185,0.1);
    }

    .btn-export {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 11px 20px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(16,185,129,0.25);
    }
    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16,185,129,0.35);
        color: #ffffff;
    }

    /* ── Table Card ── */
    .table-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }
    .table-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-title {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .table-title i { color: #2b43b9; }
    .count-badge {
        font-size: 12px;
        font-weight: 700;
        color: #2b43b9;
        background: #eaeff8;
        padding: 5px 14px;
        border-radius: 20px;
    }

    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .data-table th {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.6px;
        padding: 12px 16px;
        text-align: left;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .data-table th:first-child { border-radius: 10px 0 0 10px; }
    .data-table th:last-child  { border-radius: 0 10px 10px 0; }
    
    .data-table td {
        padding: 16px;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        transition: background 0.15s ease;
    }
    .data-table tbody tr:hover td { background: #f8fafc; }

    /* Teacher Cell */
    .teacher-cell { display: flex; align-items: center; gap: 12px; }
    .teacher-avatar {
        width: 38px; height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #c7d2fe, #a5b4fc);
        color: #3730a3;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 800;
        flex-shrink: 0;
    }
    .teacher-name { font-weight: 700; color: #0f172a; font-size: 14px; }

    /* Badges */
    .jam-pill {
        background: #f1f5f9; color: #334155;
        font-weight: 700; font-size: 12px;
        padding: 5px 12px; border-radius: 8px;
        display: inline-block; white-space: nowrap;
    }
    .kelas-pill {
        background: #e0e7ff; color: #3730a3;
        font-weight: 800; font-size: 12px;
        padding: 5px 12px; border-radius: 8px;
        display: inline-block; white-space: nowrap;
    }
    .mapel-pill {
        background: #f0fdf4; color: #166534;
        font-weight: 700; font-size: 12px;
        padding: 4px 10px; border-radius: 6px;
        border: 1px solid #bbf7d0; display: inline-block;
    }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        font-size: 11.5px;
        padding: 5px 12px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .status-pill.status-terisi {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .status-pill.status-berlangsung {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }
    .status-pill.status-pending {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 6px #10b981;
    }

    /* Progress bar */
    .progress-bar-container { width: 110px; }
    .progress-bar-track {
        height: 7px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 4px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.4s ease;
    }
    .fill-emerald { background: #10b981; }
    .fill-amber   { background: #f59e0b; }
    .fill-rose    { background: #ef4444; }

    /* Action button */
    .btn-action-detail, .btn-detail-view {
        background: #eaeff8;
        color: #2b43b9;
        border: 1px solid #c7d2fe;
        border-radius: 10px;
        padding: 7px 14px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-action-detail:hover, .btn-detail-view:hover {
        background: #2b43b9;
        color: #ffffff;
        border-color: #2b43b9;
        box-shadow: 0 4px 12px rgba(43,67,185,0.25);
        transform: translateY(-1px);
    }

    /* ── Modal Design ── */
    .modal-bd {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.65);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 16px;
    }
    .modal-bd.show { display: flex !important; }
    
    .modal-bx {
        background: #ffffff;
        border-radius: 24px;
        width: 92%; max-width: 680px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 28px;
        box-shadow: 0 25px 60px rgba(15,23,42,0.25);
        animation: modalSlideUp 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    @keyframes modalSlideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .modal-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .modal-ttl { font-size: 19px; font-weight: 800; color: #0f172a; }
    
    .btn-modal-close {
        background: #f1f5f9;
        border: none;
        border-radius: 10px;
        width: 34px; height: 34px;
        cursor: pointer;
        font-size: 16px;
        color: #64748b;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.15s ease;
    }
    .btn-modal-close:hover { background: #fee2e2; color: #ef4444; }

    /* Modal Grid & Cards */
    .modal-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 20px;
        background: #f8fafc;
        padding: 18px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }
    .modal-info-item { display: flex; flex-direction: column; gap: 3px; }
    .modal-info-lbl  { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: .4px; }
    .modal-info-val  { font-size: 14px; font-weight: 700; color: #0f172a; }

    .materi-card {
        background: #eef2ff;
        border-left: 4px solid #4f46e5;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }
    .materi-card-title { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #4338ca; margin-bottom: 4px; }
    .materi-card-body  { font-size: 13.5px; font-weight: 600; color: #1e1b4b; line-height: 1.5; }

    .catatan-card {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #78350f;
    }

    /* Student presensi status chips */
    .presensi-chip {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 800;
    }
    .chip-hadir { background: #d1fae5; color: #065f46; }
    .chip-sakit { background: #fef3c7; color: #92400e; }
    .chip-izin  { background: #e0f2fe; color: #0369a1; }
    .chip-dispensasi { background: #ede9fe; color: #5b21b6; }
    .chip-alpha { background: #fee2e2; color: #991b1b; }

    @media (max-width: 768px) {
        .absensi-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 18px;
        }
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .filter-card {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-group {
            flex-direction: column;
            width: 100%;
        }
        .filter-input, .filter-select {
            min-width: unset;
            width: 100%;
        }
        .btn-export {
            width: 100%;
            justify-content: center;
        }
        .table-card {
            padding: 16px;
        }
        .modal-bx {
            width: 94%;
            padding: 20px;
        }
    }

    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

{{-- Filter Card --}}
<form action="{{ route('admin.absensi') }}" method="GET" id="absensiFilterForm">
<div class="filter-card">
    <div class="filter-group">
        <div class="filter-item">
            <label class="filter-label">Tanggal Sesi</label>
            <input type="date" name="tanggal" value="{{ $selectedTanggal }}" class="filter-input" onchange="this.form.submit()">
        </div>
        <div class="filter-item">
            <label class="filter-label">Kelas / Rombel</label>
            <select name="id_kelas" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id_kelas }}" {{ $selectedKelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-item">
            <label class="filter-label">Jam Pelajaran</label>
            <select name="jam_ke" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Jam</option>
                @foreach($jamList as $val => $lbl)
                    <option value="{{ $val }}" {{ $selectedJam == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <a href="{{ route('admin.absensi.export', ['tanggal' => $selectedTanggal]) }}" class="btn-export">
            <i class="fa-solid fa-file-csv"></i> Export CSV Report
        </a>
    </div>
</div>
</form>

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon indigo"><i class="fa-solid fa-calendar-check"></i></div>
        <div>
            <div class="kpi-val">{{ $totalSesi }}</div>
            <div class="kpi-lbl">Total Sesi Terdaftar</div>
            <div class="kpi-sub">Sesi pembelajaran hari ini</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="kpi-val">{{ $jurnalSudahDiisi }}</div>
            <div class="kpi-lbl">Jurnal Sudah Diisi</div>
            <div class="kpi-sub">Terverifikasi oleh guru</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber"><i class="fa-solid fa-hourglass-half"></i></div>
        <div>
            <div class="kpi-val">{{ $belumDiisiGuru }}</div>
            <div class="kpi-lbl">Belum Diisi Guru</div>
            <div class="kpi-sub">Menunggu input jurnal</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon rose"><i class="fa-solid fa-user-slash"></i></div>
        <div>
            <div class="kpi-val">{{ $siswaTidakMasuk }}</div>
            <div class="kpi-lbl">Siswa Tidak Masuk</div>
            <div class="kpi-sub">Izin, Sakit, &amp; Alpa</div>
        </div>
    </div>
</div>

{{-- Teaching Sessions Table Card --}}
<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-table-list"></i>
            <span>Daftar Jurnal Mengajar &amp; Presensi</span>
        </div>
        <span class="count-badge"><i class="fa-solid fa-layer-group"></i> {{ $jurnalList->count() }} Sesi Mengajar</span>
    </div>
    <div class="table-responsive-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Jam Ke</th>
                <th>Kelas</th>
                <th>Guru Pengajar</th>
                <th>Mata Pelajaran</th>
                <th>Materi Pembelajaran</th>
                <th>Tingkat Kehadiran</th>
                <th>Status Jurnal</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jurnalList as $j)
            @php
                $tot = $j->jumlah_siswa_hadir + $j->jumlah_siswa_tidak_hadir;
                $pct = $tot > 0 ? round(($j->jumlah_siswa_hadir / $tot) * 100) : 100;
                $fillClass = $pct >= 90 ? 'fill-emerald' : ($pct >= 75 ? 'fill-amber' : 'fill-rose');
            @endphp
            <tr>
                <td><span class="jam-pill">{{ $j->jam_ke }}</span></td>
                <td><span class="kelas-pill">{{ $j->kelas->nama_kelas ?? '-' }}</span></td>
                <td>
                    <div class="teacher-cell">
                        <div class="teacher-avatar">
                            {{ strtoupper(substr($j->guru->nama_lengkap ?? 'G', 0, 1)) }}
                        </div>
                        <div class="teacher-name">{{ $j->guru->nama_lengkap ?? '-' }}</div>
                    </div>
                </td>
                <td style="font-weight:700;color:#0f172a;">{{ $j->mapel->nama_mapel ?? '-' }}</td>
                <td style="max-width:240px;color:#64748b;font-weight:500;">
                    {{ Str::limit($j->materi ?? 'Tidak ada materi tertulis', 55) }}
                </td>
                <td>
                    <div class="attendance-progress-wrap">
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill {{ $fillClass }}" style="width: {{ $pct }}%;"></div>
                        </div>
                        <span class="progress-pct-lbl">{{ $pct }}%</span>
                    </div>
                </td>
                <td>
                    @if($j->status_jurnal === 'Selesai')
                        <span class="status-pill status-terisi"><i class="fa-solid fa-circle-check"></i> Selesai</span>
                    @elseif($j->status_jurnal === 'Sedang Berlangsung')
                        <span class="status-pill status-berlangsung"><i class="fa-solid fa-spinner fa-spin"></i> Berlangsung</span>
                    @else
                        <span class="status-pill status-pending"><i class="fa-solid fa-clock"></i> Belum Selesai</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;justify-content:center;">
                        <button type="button" class="btn-action-detail" onclick="viewDetail({{ $j->id_jurnal }})">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:50px 20px;color:#94a3b8;">
                    <i class="fa-solid fa-clipboard-question" style="font-size:42px;margin-bottom:12px;color:#cbd5e1;"></i>
                    <p style="font-size:15px;font-weight:800;color:#0f172a;">Tidak ada sesi mengajar hari ini</p>
                    <p style="font-size:13px;margin-top:4px;color:#64748b;">Gunakan filter tanggal atau kelas di atas untuk melihat riwayat terdahulu.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>{{-- end table-responsive-wrap --}}
</div>

{{-- Modern Detail Modal --}}
<div class="modal-bd" id="detailModal">
    <div class="modal-bx">
        <div class="modal-hdr">
            <div class="modal-ttl">
                <i class="fa-solid fa-receipt" style="color:#2b43b9;margin-right:8px;"></i>
                Detail Sesi &amp; Presensi Siswa
            </div>
            <button type="button" class="btn-modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div id="modalLoading" style="text-align:center;padding:36px;color:#64748b;">
            <i class="fa-solid fa-circle-notch fa-spin" style="font-size:28px;color:#2b43b9;"></i>
            <p style="margin-top:10px;font-weight:700;font-size:14px;">Memuat detail jurnal pembelajaran...</p>
        </div>

        <div id="modalError" style="display:none;text-align:center;padding:32px 20px;color:#dc2626;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size:36px;margin-bottom:10px;color:#ef4444;"></i>
            <p id="modalErrorMessage" style="font-weight:700;font-size:14px;color:#1e293b;">Gagal memuat detail jurnal pembelajaran.</p>
            <button type="button" class="btn-action-detail" style="margin-top:14px;" onclick="closeModal()">Tutup</button>
        </div>

        <div id="modalContent" style="display:none;">
            <div class="modal-info-grid">
                <div class="modal-info-item"><span class="modal-info-lbl">Waktu Pembelajaran</span><span class="modal-info-val" id="m_jam"></span></div>
                <div class="modal-info-item"><span class="modal-info-lbl">Kelas / Rombel</span><span class="modal-info-val" id="m_kelas"></span></div>
                <div class="modal-info-item"><span class="modal-info-lbl">Guru Pengajar</span><span class="modal-info-val" id="m_guru"></span></div>
                <div class="modal-info-item"><span class="modal-info-lbl">Mata Pelajaran</span><span class="modal-info-val" id="m_mapel"></span></div>
            </div>

            <div class="materi-card">
                <div class="materi-card-title"><i class="fa-solid fa-book-open"></i> Materi Yang Diajarkan</div>
                <div class="materi-card-body" id="m_materi"></div>
            </div>

            <div class="catatan-card" id="m_catatan_box" style="display:none;">
                <strong><i class="fa-solid fa-note-sticky"></i> Catatan Guru:</strong> <span id="m_catatan"></span>
            </div>

            <div style="font-size:14px;font-weight:800;margin-bottom:12px;color:#0f172a;display:flex;align-items:center;justify-content:space-between;">
                <span><i class="fa-solid fa-users-viewfinder" style="color:#2b43b9;margin-right:6px;"></i> Rekapitulasi Presensi Siswa</span>
                <span style="font-size:12px;font-weight:700;color:#64748b;" id="m_stat_hadir"></span>
            </div>

            <div class="table-responsive-wrap" style="max-height: 280px; overflow-y: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>NIS</th>
                        <th>Status Presensi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="m_presensi_body">
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function closeModal() {
        const modal = document.getElementById('detailModal');
        if (modal) modal.classList.remove('show');
    }

    function viewDetail(id) {
        const modal = document.getElementById('detailModal');
        const loading = document.getElementById('modalLoading');
        const content = document.getElementById('modalContent');
        const errorBox = document.getElementById('modalError');

        if (!modal) return;

        modal.classList.add('show');
        if (loading) loading.style.display = 'block';
        if (content) content.style.display = 'none';
        if (errorBox) errorBox.style.display = 'none';

        // URL dinamis yang valid di semua environment (artisan serve maupun Laragon subfolder)
        const detailUrl = "{{ url('/admin/absensi') }}/" + id;

        fetch(detailUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('HTTP ' + res.status + ': Gagal mengambil data jurnal.');
            }
            return res.json();
        })
        .then(res => {
            if (res.status === 'success' && res.data) {
                const d = res.data;
                document.getElementById('m_jam').innerText = (d.tanggal || '') + ' (' + (d.jam_ke || '') + ')';
                document.getElementById('m_kelas').innerText = d.kelas || '-';
                document.getElementById('m_guru').innerText = d.guru || '-';
                document.getElementById('m_mapel').innerText = d.mapel || '-';
                document.getElementById('m_materi').innerText = d.materi || 'Tidak ada materi tertulis';

                if (d.catatan && d.catatan !== '-' && d.catatan.trim() !== '') {
                    document.getElementById('m_catatan').innerText = d.catatan;
                    document.getElementById('m_catatan_box').style.display = 'block';
                } else {
                    document.getElementById('m_catatan_box').style.display = 'none';
                }

                document.getElementById('m_stat_hadir').innerText = 'Hadir: ' + (d.jumlah_hadir ?? 0) + ' Siswa | Tidak Hadir: ' + (d.jumlah_tidak_hadir ?? 0) + ' Siswa';

                const tbody = document.getElementById('m_presensi_body');
                tbody.innerHTML = '';

                if (d.presensi && d.presensi.length > 0) {
                    d.presensi.forEach(p => {
                        let chipClass = 'chip-hadir';
                        if (p.status === 'Sakit') chipClass = 'chip-sakit';
                        else if (p.status === 'Izin') chipClass = 'chip-izin';
                        else if (p.status === 'Dispensasi') chipClass = 'chip-dispensasi';
                        else if (p.status === 'Alpha') chipClass = 'chip-alpha';

                        tbody.innerHTML += `
                            <tr>
                                <td style="font-weight:700;color:#0f172a;">${escapeHtml(p.nama_siswa)}</td>
                                <td style="color:#64748b;font-weight:600;">${escapeHtml(p.nis)}</td>
                                <td><span class="presensi-chip ${chipClass}">${escapeHtml(p.status)}</span></td>
                                <td style="color:#475569;font-weight:500;">${escapeHtml(p.keterangan || '-')}</td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;font-weight:600;">Semua siswa hadir pada sesi pembelajaran ini.</td></tr>`;
                }

                if (loading) loading.style.display = 'none';
                if (content) content.style.display = 'block';
            } else {
                throw new Error(res.message || 'Respon data tidak valid.');
            }
        })
        .catch(err => {
            console.error('Error viewDetail:', err);
            if (loading) loading.style.display = 'none';
            if (errorBox) {
                document.getElementById('modalErrorMessage').innerText = err.message || 'Gagal memuat detail jurnal pembelajaran.';
                errorBox.style.display = 'block';
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endsection
