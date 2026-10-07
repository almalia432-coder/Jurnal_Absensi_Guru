@extends('layouts.admin')

@section('title', 'Pusat Rekap & Laporan — SMKN 1 BOYOLANGU')
@section('header_title', 'Pusat Laporan & Rekapitulasi')
@section('header_subtitle', 'Pusat rekap data historis absensi, jurnal mengajar, dispensasi, & kehadiran guru')

@section('styles')
<style>


    /* ── KPI Cards ── */
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
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
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.07); border-color: #cbd5e1; }

    .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .kpi-icon.indigo  { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #3730a3; }
    .kpi-icon.emerald { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .kpi-icon.rose    { background: linear-gradient(135deg, #ffe4e6, #fecdd3); color: #9f1239; }
    .kpi-icon.amber   { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }

    .kpi-val { font-size: 30px; font-weight: 900; color: #0f172a; line-height: 1.1; letter-spacing: -0.5px; }
    .kpi-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 3px; }
    .kpi-sub { font-size: 11px; font-weight: 600; color: #94a3b8; margin-top: 2px; }

    /* ── Tab Navigation ── */
    .laporan-tabs-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        padding: 6px;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        overflow-x: auto;
    }
    .tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 11px;
        font-size: 13.5px;
        font-weight: 700;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
    }
    .tab-btn:hover { background: #f8fafc; color: #2b43b9; }
    .tab-btn.active {
        background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(43,67,185,0.22);
    }
    .tab-btn i { font-size: 14px; }

    /* ── Filter & Action Bar ── */
    .filter-action-card {
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
    .filter-item { display: flex; flex-direction: column; gap: 5px; }
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
        min-width: 160px;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: #2b43b9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(43,67,185,0.1);
    }

    .action-btn-group { display: flex; align-items: center; gap: 10px; }

    .btn-print {
        background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 11px 20px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(43,67,185,0.25);
    }
    .btn-print:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(43,67,185,0.35); color: #ffffff; }

    .btn-export {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 11px 20px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(16,185,129,0.25);
    }
    .btn-export:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.35); color: #ffffff; }

    /* ── Table Card ── */
    .table-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .table-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-title { font-size: 17px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .table-title i { color: #2b43b9; }
    .count-badge { font-size: 12px; font-weight: 700; color: #2b43b9; background: #eaeff8; padding: 5px 14px; border-radius: 20px; }

    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .data-table th {
        font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.6px;
        padding: 12px 16px; text-align: left; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    }
    .data-table th:first-child { border-radius: 10px 0 0 10px; }
    .data-table th:last-child  { border-radius: 0 10px 10px 0; }
    .data-table td {
        padding: 14px 16px; font-size: 13.5px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; transition: background 0.15s ease;
    }
    .data-table tbody tr:hover td { background: #f8fafc; }
    .data-table tbody tr:last-child td { border-bottom: none; }

    /* Pill Badges */
    .kelas-pill  { background: #e0e7ff; color: #3730a3; font-weight: 800; font-size: 12px; padding: 4px 10px; border-radius: 8px; display: inline-block; }
    .jam-pill    { background: #f1f5f9; color: #334155;  font-weight: 700; font-size: 11.5px; padding: 4px 10px; border-radius: 6px; display: inline-block; }
    .jenis-pill  { background: #fef3c7; color: #92400e; font-weight: 800; font-size: 11.5px; padding: 4px 10px; border-radius: 6px; display: inline-block; }

    .badge-ok      { background: #d1fae5; color: #065f46; font-weight: 800; font-size: 11.5px; padding: 4px 10px; border-radius: 20px; display:inline-block; }
    .badge-pending { background: #fef3c7; color: #92400e; font-weight: 800; font-size: 11.5px; padding: 4px 10px; border-radius: 20px; display:inline-block; }
    .badge-reject  { background: #fee2e2; color: #991b1b; font-weight: 800; font-size: 11.5px; padding: 4px 10px; border-radius: 20px; display:inline-block; }

    .pct-badge   { font-weight: 800; font-size: 12.5px; padding: 4px 12px; border-radius: 20px; display: inline-block; }
    .pct-high    { background: #d1fae5; color: #065f46; }
    .pct-mid     { background: #fef3c7; color: #92400e; }
    .pct-low     { background: #fee2e2; color: #991b1b; }

    /* Semester Summary Cards */
    .semester-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
    .sem-card {
        background: #f8fafc; border-radius: 16px; padding: 22px 20px; border: 1px solid #e2e8f0;
        display: flex; align-items: center; gap: 16px;
        transition: all 0.25s ease;
    }
    .sem-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .sem-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .sem-val { font-size: 28px; font-weight: 900; color: #0f172a; line-height: 1.1; }
    .sem-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 3px; }

    .empty-state { text-align: center; padding: 56px 20px; color: #94a3b8; }
    .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }
    .empty-state p { font-size: 14px; font-weight: 600; }
</style>
@endsection

@section('content')

{{-- Tab Navigation --}}
<div class="laporan-tabs-wrap">
    <a href="{{ route('admin.laporan', ['tab' => 'absensi_siswa', 'tanggal_mulai' => $startDate, 'tanggal_selesai' => $endDate, 'id_kelas' => $selectedKelas]) }}"
       class="tab-btn {{ $activeTab === 'absensi_siswa' ? 'active' : '' }}">
        <i class="fa-solid fa-chart-pie"></i> Absensi Siswa
    </a>
    <a href="{{ route('admin.laporan', ['tab' => 'jurnal_mengajar', 'tanggal_mulai' => $startDate, 'tanggal_selesai' => $endDate, 'id_kelas' => $selectedKelas, 'id_guru' => $selectedGuru]) }}"
       class="tab-btn {{ $activeTab === 'jurnal_mengajar' ? 'active' : '' }}">
        <i class="fa-solid fa-book-bookmark"></i> Jurnal Mengajar
    </a>
    <a href="{{ route('admin.laporan', ['tab' => 'dispensasi_siswa', 'tanggal_mulai' => $startDate, 'tanggal_selesai' => $endDate, 'id_kelas' => $selectedKelas]) }}"
       class="tab-btn {{ $activeTab === 'dispensasi_siswa' ? 'active' : '' }}">
        <i class="fa-solid fa-door-open"></i> Dispensasi Siswa
    </a>
    <a href="{{ route('admin.laporan', ['tab' => 'izin_guru', 'tanggal_mulai' => $startDate, 'tanggal_selesai' => $endDate, 'id_guru' => $selectedGuru]) }}"
       class="tab-btn {{ $activeTab === 'izin_guru' ? 'active' : '' }}">
        <i class="fa-solid fa-user-clock"></i> Kehadiran & Izin Guru
    </a>
    <a href="{{ route('admin.laporan', ['tab' => 'ringkasan_semester', 'tanggal_mulai' => $startDate, 'tanggal_selesai' => $endDate]) }}"
       class="tab-btn {{ $activeTab === 'ringkasan_semester' ? 'active' : '' }}">
        <i class="fa-solid fa-chart-simple"></i> Ringkasan Semester
    </a>
</div>

{{-- Filter & Action Bar --}}
<form action="{{ route('admin.laporan') }}" method="GET" id="laporanFilterForm">
<input type="hidden" name="tab" value="{{ $activeTab }}">
<div class="filter-action-card">
    <div class="filter-group">
        <div class="filter-item">
            <label class="filter-label">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" value="{{ $startDate }}" class="filter-input" onchange="this.form.submit()">
        </div>
        <div class="filter-item">
            <label class="filter-label">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" value="{{ $endDate }}" class="filter-input" onchange="this.form.submit()">
        </div>
        @if(in_array($activeTab, ['absensi_siswa', 'jurnal_mengajar', 'dispensasi_siswa']))
        <div class="filter-item">
            <label class="filter-label">Kelas / Rombel</label>
            <select name="id_kelas" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id_kelas }}" {{ $selectedKelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        @endif
        @if(in_array($activeTab, ['jurnal_mengajar', 'izin_guru']))
        <div class="filter-item">
            <label class="filter-label">Guru Pengajar</label>
            <select name="id_guru" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Guru</option>
                @foreach($guruList as $g)
                    <option value="{{ $g->id_guru }}" {{ $selectedGuru == $g->id_guru ? 'selected' : '' }}>{{ $g->nama_lengkap }}</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>
    <div class="action-btn-group">
        <a href="{{ route('admin.laporan.print', ['tab' => $activeTab, 'tanggal_mulai' => $startDate, 'tanggal_selesai' => $endDate, 'id_kelas' => $selectedKelas, 'id_guru' => $selectedGuru]) }}"
           target="_blank" class="btn-print">
            <i class="fa-solid fa-print"></i> Cetak / Print
        </a>
        <a href="{{ route('admin.laporan.export', ['tab' => $activeTab, 'tanggal_mulai' => $startDate, 'tanggal_selesai' => $endDate, 'id_kelas' => $selectedKelas, 'id_guru' => $selectedGuru]) }}"
           class="btn-export">
            <i class="fa-solid fa-file-export"></i> Export CSV
        </a>
    </div>
</div>
</form>

{{-- ═══════════════════════════════════════ TAB CONTENT ═══════════════════════════════════════ --}}

@if($activeTab === 'absensi_siswa')

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon indigo"><i class="fa-solid fa-users"></i></div>
        <div>
            <div class="kpi-val">{{ $absensiSiswaData->count() }}</div>
            <div class="kpi-lbl">Total Siswa Direkap</div>
            <div class="kpi-sub">Periode ini</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="kpi-val">{{ $absensiSiswaData->where('persentase', '>=', 90)->count() }}</div>
            <div class="kpi-lbl">Kehadiran Baik ≥90%</div>
            <div class="kpi-sub">Persentase tinggi</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
            <div class="kpi-val">{{ $absensiSiswaData->where('persentase', '<', 90)->where('persentase', '>=', 75)->count() }}</div>
            <div class="kpi-lbl">Perlu Perhatian 75–89%</div>
            <div class="kpi-sub">Perlu monitoring</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon rose"><i class="fa-solid fa-circle-xmark"></i></div>
        <div>
            <div class="kpi-val">{{ $absensiSiswaData->where('persentase', '<', 75)->count() }}</div>
            <div class="kpi-lbl">Kritis &lt;75%</div>
            <div class="kpi-sub">Perlu tindak lanjut</div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Rekapitulasi Kehadiran Siswa</span>
        </div>
        <span class="count-badge">{{ $absensiSiswaData->count() }} Siswa Direkap</span>
    </div>
    <div class="table-responsive-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th style="text-align:center;">Hadir</th>
                <th style="text-align:center;">Sakit</th>
                <th style="text-align:center;">Izin</th>
                <th style="text-align:center;">Alpa</th>
                <th style="text-align:center;">Dispen</th>
                <th style="text-align:center;">Total</th>
                <th style="text-align:center;">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensiSiswaData as $row)
            @php $pctClass = $row['persentase'] >= 90 ? 'pct-high' : ($row['persentase'] >= 75 ? 'pct-mid' : 'pct-low'); @endphp
            <tr>
                <td style="color:#64748b;font-weight:700;font-size:12.5px;">{{ $row['nis'] }}</td>
                <td style="font-weight:700;color:#0f172a;">{{ $row['nama_lengkap'] }}</td>
                <td><span class="kelas-pill">{{ $row['nama_kelas'] }}</span></td>
                <td style="text-align:center;font-weight:700;color:#10b981;">{{ $row['hadir'] }}</td>
                <td style="text-align:center;font-weight:700;color:#f59e0b;">{{ $row['sakit'] }}</td>
                <td style="text-align:center;font-weight:700;color:#3b82f6;">{{ $row['izin'] }}</td>
                <td style="text-align:center;font-weight:700;color:#ef4444;">{{ $row['alpa'] }}</td>
                <td style="text-align:center;font-weight:700;color:#8b5cf6;">{{ $row['dispen'] }}</td>
                <td style="text-align:center;font-weight:700;">{{ $row['total_sesi'] }}</td>
                <td style="text-align:center;"><span class="pct-badge {{ $pctClass }}">{{ $row['persentase'] }}%</span></td>
            </tr>
            @empty
            <tr><td colspan="10"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>Tidak ada data rekapitulasi absensi siswa pada periode ini.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@elseif($activeTab === 'jurnal_mengajar')

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon indigo"><i class="fa-solid fa-book-open"></i></div>
        <div>
            <div class="kpi-val">{{ $jurnalData->count() }}</div>
            <div class="kpi-lbl">Total Sesi Jurnal</div>
            <div class="kpi-sub">Periode ini</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald"><i class="fa-solid fa-chalkboard-user"></i></div>
        <div>
            <div class="kpi-val">{{ $jurnalData->unique('id_guru')->count() }}</div>
            <div class="kpi-lbl">Guru Mengajar</div>
            <div class="kpi-sub">Aktif di periode ini</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber"><i class="fa-solid fa-school"></i></div>
        <div>
            <div class="kpi-val">{{ $jurnalData->unique('id_kelas')->count() }}</div>
            <div class="kpi-lbl">Kelas Tercakup</div>
            <div class="kpi-sub">Rombel aktif</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon rose"><i class="fa-solid fa-book"></i></div>
        <div>
            <div class="kpi-val">{{ $jurnalData->unique('id_mapel')->count() }}</div>
            <div class="kpi-lbl">Mata Pelajaran</div>
            <div class="kpi-sub">Mapel direkap</div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-book-bookmark"></i>
            <span>Rekapitulasi Jurnal Mengajar</span>
        </div>
        <span class="count-badge">{{ $jurnalData->count() }} Sesi Tercatat</span>
    </div>
    <div class="table-responsive-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Kelas</th>
                <th>Guru Pengajar</th>
                <th>Mata Pelajaran</th>
                <th>Materi Diajarkan</th>
                <th style="text-align:center;">Hadir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jurnalData as $j)
            <tr>
                <td style="font-weight:700;color:#0f172a;">{{ $j->tanggal->format('d/m/Y') }}</td>
                <td><span class="jam-pill">{{ $j->jam_ke }}</span></td>
                <td><span class="kelas-pill">{{ $j->kelas->nama_kelas ?? '-' }}</span></td>
                <td style="font-weight:700;color:#0f172a;">{{ $j->guru->nama_lengkap ?? '-' }}</td>
                <td style="font-weight:600;color:#475569;">{{ $j->mapel->nama_mapel ?? '-' }}</td>
                <td style="max-width:220px;color:#334155;font-weight:500;" title="{{ $j->materi }}">{{ Str::limit($j->materi, 60) }}</td>
                <td style="text-align:center;font-weight:800;color:#10b981;">{{ $j->jumlah_siswa_hadir }}</td>
                <td><span class="badge-ok">{{ $j->status_guru }}</span></td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>Tidak ada data jurnal mengajar pada periode ini.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@elseif($activeTab === 'dispensasi_siswa')

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon indigo"><i class="fa-solid fa-door-open"></i></div>
        <div>
            <div class="kpi-val">{{ $dispensasiData->count() }}</div>
            <div class="kpi-lbl">Total Dispensasi</div>
            <div class="kpi-sub">Periode ini</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="kpi-val">{{ $dispensasiData->filter(fn($d) => in_array($d->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai']))->count() }}</div>
            <div class="kpi-lbl">Disetujui</div>
            <div class="kpi-sub">Sudah terverifikasi</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="kpi-val">{{ $dispensasiData->where('status', 'Menunggu')->count() }}</div>
            <div class="kpi-lbl">Menunggu</div>
            <div class="kpi-sub">Belum disetujui</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon rose"><i class="fa-solid fa-users"></i></div>
        <div>
            <div class="kpi-val">{{ $dispensasiData->unique('id_siswa')->count() }}</div>
            <div class="kpi-lbl">Siswa Berbeda</div>
            <div class="kpi-sub">Yang mengajukan dispen</div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-door-open"></i>
            <span>Riwayat Dispensasi Siswa</span>
        </div>
        <span class="count-badge">{{ $dispensasiData->count() }} Data Dispen</span>
    </div>
    <div class="table-responsive-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Jam Keluar – Kembali</th>
                <th>Alasan</th>
                <th>Diinput Oleh</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dispensasiData as $d)
            <tr>
                <td style="font-weight:700;">{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                <td style="font-weight:700;color:#0f172a;">{{ $d->siswa->nama_lengkap ?? '-' }}</td>
                <td><span class="kelas-pill">{{ $d->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                <td style="font-weight:700;color:#2b43b9;">{{ substr($d->jam_keluar, 0, 5) }} – {{ $d->jam_kembali ? substr($d->jam_kembali, 0, 5) : 'Belum Kembali' }}</td>
                <td style="color:#334155;font-weight:500;">{{ $d->alasan }}</td>
                <td style="font-size:12.5px;color:#64748b;font-weight:600;">{{ $d->diinputOlehUser->name ?? 'Guru Piket' }}</td>
                <td>
                    @php $st = $d->status; @endphp
                    @if(str_contains($st, 'Disetujui'))
                        <span class="badge-ok">{{ str_replace('_', ' ', $st) }}</span>
                    @elseif($st === 'Menunggu')
                        <span class="badge-pending">Menunggu</span>
                    @else
                        <span class="badge-reject">{{ $st }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>Belum ada riwayat dispensasi siswa pada periode ini.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@elseif($activeTab === 'izin_guru')

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon indigo"><i class="fa-solid fa-user-clock"></i></div>
        <div>
            <div class="kpi-val">{{ $izinGuruData->count() }}</div>
            <div class="kpi-lbl">Total Pengajuan Izin</div>
            <div class="kpi-sub">Periode ini</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="kpi-val">{{ $izinGuruData->where('status', 'Disetujui')->count() }}</div>
            <div class="kpi-lbl">Disetujui</div>
            <div class="kpi-sub">Sudah diverifikasi</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="kpi-val">{{ $izinGuruData->where('status', 'Menunggu')->count() }}</div>
            <div class="kpi-lbl">Menunggu Persetujuan</div>
            <div class="kpi-sub">Perlu ditindaklanjuti</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon rose"><i class="fa-solid fa-chalkboard-user"></i></div>
        <div>
            <div class="kpi-val">{{ $izinGuruData->unique('id_guru')->count() }}</div>
            <div class="kpi-lbl">Guru Berbeda</div>
            <div class="kpi-sub">Yang mengajukan izin</div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-user-clock"></i>
            <span>Rekapitulasi Kehadiran & Izin Guru</span>
        </div>
        <span class="count-badge">{{ $izinGuruData->count() }} Data Izin</span>
    </div>
    <div class="table-responsive-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Guru</th>
                <th>Tanggal Mulai – Selesai</th>
                <th>Jenis Izin</th>
                <th>Alasan</th>
                <th>Disetujui Oleh</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($izinGuruData as $ig)
            <tr>
                <td style="font-weight:700;color:#0f172a;">{{ $ig->guru->nama_lengkap ?? '-' }}</td>
                <td style="font-weight:700;">{{ \Carbon\Carbon::parse($ig->tanggal_mulai)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($ig->tanggal_selesai)->format('d/m/Y') }}</td>
                <td><span class="jenis-pill">{{ $ig->jenis_izin }}</span></td>
                <td style="color:#334155;font-weight:500;">{{ $ig->alasan }}</td>
                <td style="font-size:12.5px;color:#64748b;font-weight:600;">{{ $ig->disetujuiOlehUser->name ?? 'Kepsek / Waka' }}</td>
                <td>
                    @if($ig->status === 'Disetujui') <span class="badge-ok">Disetujui</span>
                    @elseif($ig->status === 'Menunggu') <span class="badge-pending">Menunggu</span>
                    @else <span class="badge-reject">{{ $ig->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>Tidak ada rekapitulasi izin guru pada periode ini.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@elseif($activeTab === 'ringkasan_semester')

<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-chart-simple"></i>
            <span>Ringkasan Semester & Arsip Resmi Sekolah</span>
        </div>
        <span class="count-badge">Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
    </div>

    <p style="font-size:13.5px;color:#475569;font-weight:600;margin-bottom:20px;">
        Gabungan data eksekutif untuk keperluan rapat dinas, evaluasi kurikulum, dan arsip semesteran SMKN 1 Boyolangu.
    </p>

    <div class="semester-grid">
        <div class="sem-card">
            <div class="sem-icon indigo"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="sem-val">{{ $ringkasanSemester['total_siswa'] ?? 0 }}</div>
                <div class="sem-lbl">Total Siswa Aktif</div>
            </div>
        </div>
        <div class="sem-card">
            <div class="sem-icon emerald"><i class="fa-solid fa-chalkboard-user"></i></div>
            <div>
                <div class="sem-val">{{ $ringkasanSemester['total_guru'] ?? 0 }}</div>
                <div class="sem-lbl">Total Guru Pengajar</div>
            </div>
        </div>
        <div class="sem-card">
            <div class="sem-icon amber"><i class="fa-solid fa-percent"></i></div>
            <div>
                <div class="sem-val">{{ $ringkasanSemester['pct_kehadiran'] ?? 100 }}%</div>
                <div class="sem-lbl">Tingkat Kehadiran Siswa</div>
            </div>
        </div>
        <div class="sem-card">
            <div class="sem-icon rose"><i class="fa-solid fa-book-open"></i></div>
            <div>
                <div class="sem-val">{{ $ringkasanSemester['total_jurnal'] ?? 0 }}</div>
                <div class="sem-lbl">Sesi Jurnal Mengajar</div>
            </div>
        </div>
    </div>
</div>

@endif

@endsection
