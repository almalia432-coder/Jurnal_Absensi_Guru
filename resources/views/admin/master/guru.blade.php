@extends('layouts.admin')

@section('title', 'Manajemen Guru — SMKN 1 BOYOLANGU')
@section('header_title', 'Manajemen Guru')
@section('header_subtitle', 'Kelola data induk guru, jabatan/peran (Guru Mapel, Wali Kelas, Guru Piket), dan akun akses')

@section('styles')
<style>
    /* Toast Notification */
    .toast-container { position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; }
    .toast {
        display: flex; align-items: center; gap: 12px; background: #ffffff; border-radius: 14px; padding: 14px 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12); border-left: 4px solid #10b981; font-size: 14px; font-weight: 600; color: #0f172a;
        min-width: 320px; animation: toastSlideIn .3s ease;
    }
    .toast.error { border-left-color: #ef4444; }
    .toast i { font-size: 18px; color: #10b981; }
    .toast.error i { color: #ef4444; }
    @keyframes toastSlideIn { from { opacity:0; transform:translateX(40px);} to { opacity:1; transform:translateX(0);} }

    /* Page Action Card */
    .page-action-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-add-guru {
        background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 11px 22px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(43,67,185,0.25);
    }
    .btn-add-guru:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(43,67,185,0.35);
        color: #ffffff;
    }

    .btn-import-csv {
        background: #f1f5f9;
        color: #1e293b;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 11px 18px;
        font-size: 13.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-import-csv:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #94a3b8;
    }

    /* KPI Summary Cards */
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
        transition: all 0.25s ease;
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
    .kpi-icon.sky     { background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0369a1; }
    .kpi-icon.violet  { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #5b21b6; }
    
    .kpi-val { font-size: 30px; font-weight: 900; color: #0f172a; line-height: 1.1; letter-spacing: -0.5px; }
    .kpi-lbl { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 3px; }
    .kpi-sub { font-size: 11px; font-weight: 600; color: #94a3b8; margin-top: 2px; }

    /* Filter Bar Inputs */
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
        min-width: 170px;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: #2b43b9;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(43,67,185,0.1);
    }

    .search-input-wrap { position: relative; }
    .search-input-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
    .search-input-wrap input { padding-left: 36px; }

    .btn-filter-action {
        background: #f1f5f9;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-filter-action:hover { background: #e2e8f0; color: #0f172a; }

    /* Table Section */
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

    /* Teacher Cell */
    .guru-cell { display: flex; align-items: center; gap: 14px; }
    .guru-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: linear-gradient(135deg, #c7d2fe, #818cf8); color: #3730a3;
        display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 900; flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(199,210,254,0.5);
    }
    .guru-avatar.female { background: linear-gradient(135deg, #fce7f3, #f9a8d4); color: #9d174d; box-shadow: 0 2px 8px rgba(252,231,243,0.5); }
    
    .guru-name { font-weight: 700; color: #0f172a; font-size: 14px; }
    .guru-nip  { font-size: 11.5px; color: #64748b; font-weight: 600; margin-top: 1px; }

    /* Role Badges */
    .badge-role {
        display: inline-flex; align-items: center; gap: 6px;
        font-weight: 800; font-size: 11.5px; padding: 5px 12px; border-radius: 20px;
    }
    .badge-role.wali-kelas  { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .badge-role.guru-mapel  { background: #f1f5f9; color: #334155; }
    .badge-role.guru-piket  { background: #fef3c7; color: #92400e; }

    .badge-status-aktif {
        display: inline-flex; align-items: center; gap: 6px;
        background: #d1fae5; color: #065f46; font-weight: 800; font-size: 11.5px; padding: 5px 12px; border-radius: 20px;
    }
    .badge-status-nonaktif {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fee2e2; color: #991b1b; font-weight: 800; font-size: 11.5px; padding: 5px 12px; border-radius: 20px;
    }
    .status-dot { width: 7px; height: 7px; border-radius: 50%; }
    .status-dot.green { background: #10b981; box-shadow: 0 0 6px #10b981; }
    .status-dot.red   { background: #ef4444; box-shadow: 0 0 6px #ef4444; }

    /* Action Buttons */
    .action-btns-group { display: flex; align-items: center; gap: 8px; }
    .btn-table-edit {
        width: 36px; height: 36px; border-radius: 10px; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: 14px;
        background: #eaeff8; color: #2b43b9; transition: all 0.2s ease;
    }
    .btn-table-edit:hover { background: #2b43b9; color: #ffffff; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(43,67,185,0.25); }

    .btn-table-delete {
        width: 36px; height: 36px; border-radius: 10px; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: 14px;
        background: #fee2e2; color: #991b1b; transition: all 0.2s ease;
    }
    .btn-table-delete:hover { background: #ef4444; color: #ffffff; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(239,68,68,0.25); }

    /* Pagination */
    .pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
    .pag-text { font-size: 13px; color: #64748b; font-weight: 600; }
    .pag-pills { display: flex; gap: 6px; }
    .pag-pills a, .pag-pills span {
        display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px;
        font-size: 13px; font-weight: 700; text-decoration: none; transition: all 0.2s ease;
    }
    .pag-pills a { background: #f1f5f9; color: #475569; }
    .pag-pills a:hover { background: #eaeff8; color: #2b43b9; }
    .pag-pills span.active { background: #2b43b9; color: #ffffff; box-shadow: 0 4px 12px rgba(43,67,185,0.25); }
    .pag-pills span.disabled { background: #f8fafc; color: #cbd5e1; cursor: default; }

    /* Modal Form Styles */
    .modal-bd { position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(5px); display: none; align-items: center; justify-content: center; z-index: 9990; }
    .modal-bd.show { display: flex; }
    .modal-bx {
        background: #ffffff; border-radius: 24px; width: 92%; max-width: 600px; max-height: 90vh; overflow-y: auto; padding: 28px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2); animation: modalSlideUp 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes modalSlideUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }

    .modal-hdr { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; }
    .modal-ttl { font-size: 19px; font-weight: 800; color: #0f172a; }
    .btn-modal-close { background: #f1f5f9; border: none; border-radius: 10px; width: 34px; height: 34px; cursor: pointer; font-size: 16px; color: #64748b; display: flex; align-items: center; justify-content: center; transition: all 0.15s ease; }
    .btn-modal-close:hover { background: #fee2e2; color: #ef4444; }

    .form-grid-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-group-item { display: flex; flex-direction: column; gap: 6px; }
    .form-group-item.full-width { grid-column: 1 / -1; }
    .form-field-lbl { font-size: 12px; font-weight: 700; color: #475569; }
    .form-field-ctrl {
        background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 10px 14px;
        font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; width: 100%; transition: all 0.2s ease;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .form-field-ctrl:focus { border-color: #2b43b9; background: #ffffff; box-shadow: 0 0 0 3px rgba(43,67,185,0.1); }

    .form-btn-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-modal-cancel { background: #f1f5f9; border: none; border-radius: 12px; padding: 11px 22px; font-size: 13.5px; font-weight: 700; color: #475569; cursor: pointer; }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-submit { background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%); color: #ffffff; border: none; border-radius: 12px; padding: 11px 24px; font-size: 13.5px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(43,67,185,0.25); }

    .delete-confirm-box { text-align: center; padding: 12px 0 20px; }
    .delete-icon-wrap { font-size: 46px; color: #ef4444; margin-bottom: 14px; }
    .delete-target-name { font-weight: 800; color: #0f172a; font-size: 18px; margin-bottom: 8px; }
    .btn-modal-delete-confirm { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; border: none; border-radius: 12px; padding: 11px 24px; font-size: 13.5px; font-weight: 700; cursor: pointer; }

    .form-divider { grid-column: 1/-1; border: none; border-top: 1px solid #f1f5f9; margin: 4px 0; }
    .form-section-title { grid-column: 1/-1; font-size: 12px; font-weight: 800; color: #2b43b9; text-transform: uppercase; letter-spacing: .5px; }
    .form-field-hint { font-size: 11px; color: #94a3b8; margin-top: 2px; }
</style>
@endsection

@section('content')

{{-- Toast Notification --}}
<div class="toast-container">
    @if(session('success'))
        <div class="toast"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error') || (isset($errors) && $errors->any()))
        <div class="toast error"><i class="fa-solid fa-circle-xmark"></i><span>{{ session('error') ?? ($errors->first() ?? '') }}</span></div>
    @endif
</div>

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon indigo"><i class="fa-solid fa-chalkboard-user"></i></div>
        <div>
            <div class="kpi-val">{{ number_format($totalGuru, 0, ',', '.') }}</div>
            <div class="kpi-lbl">Total Guru Terdaftar</div>
            <div class="kpi-sub">Seluruh pengajar sekolah</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon violet"><i class="fa-solid fa-school-flag"></i></div>
        <div>
            <div class="kpi-val">{{ $totalWaliKelas }}</div>
            <div class="kpi-lbl">Guru Wali Kelas</div>
            <div class="kpi-sub">Penanggung jawab rombel</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald"><i class="fa-solid fa-book-open-reader"></i></div>
        <div>
            <div class="kpi-val">{{ $totalGuruMapel }}</div>
            <div class="kpi-lbl">Guru Mata Pelajaran</div>
            <div class="kpi-sub">Pengajar reguler</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon sky"><i class="fa-solid fa-user-clock"></i></div>
        <div>
            <div class="kpi-val">{{ $totalGuruPiket }}</div>
            <div class="kpi-lbl">Guru Piket</div>
            <div class="kpi-sub">Petugas piket harian</div>
        </div>
    </div>
</div>

{{-- Filter & Action Bar Card --}}
<form action="{{ route('admin.master.guru') }}" method="GET" id="filterForm">
<div class="page-action-card">
    <div class="filter-group">
        <div class="filter-item">
            <label class="filter-label">Pencarian Guru</label>
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $search }}" class="filter-input" placeholder="Cari Nama Guru atau NIP…" id="searchInput">
            </div>
        </div>
        <div class="filter-item">
            <label class="filter-label">Jabatan / Status Peran</label>
            <select name="role" class="filter-select" onchange="this.form.submit()">
                <option value="all">Semua Jabatan Guru</option>
                <option value="guru_mapel" {{ $role === 'guru_mapel' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                <option value="wali_kelas" {{ $role === 'wali_kelas' ? 'selected' : '' }}>Guru Wali Kelas</option>
                <option value="guru_piket" {{ $role === 'guru_piket' ? 'selected' : '' }}>Guru Piket</option>
            </select>
        </div>
        <div class="filter-item">
            <label class="filter-label">Status Keaktifan</label>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $status === '0' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        @if($search || ($status !== null && $status !== '') || ($role && $role !== 'all'))
            <a href="{{ route('admin.master.guru') }}" class="btn-filter-action" style="align-self:flex-end;">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
        @endif
    </div>

    <div>
        <button type="button" class="btn-import-csv" onclick="openImportModal()">
            <i class="fa-solid fa-file-arrow-up"></i> Import CSV
        </button>

        <button type="button" class="btn-add-guru" onclick="openAddModal()">
            <i class="fa-solid fa-user-plus"></i> Tambah Guru Baru
        </button>
    </div>
</div>
</form>

{{-- Table Card --}}
<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-id-card"></i>
            <span>Daftar Data Induk Guru Pengajar</span>
        </div>
        <span class="count-badge"><i class="fa-solid fa-chalkboard-user"></i> {{ $guruList->total() }} Guru Ditemukan</span>
    </div>

    <div class="table-responsive-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:48px;">#</th>
                <th>Nama Guru &amp; NIP</th>
                <th>Jabatan / Peran</th>
                <th>Jenis Kelamin</th>
                <th>No. HP</th>
                <th>Akun Login Sistem</th>
                <th>Status</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guruList as $i => $g)
            @php
                $userRole = $g->user->role ?? 'guru_mapel';
                $kelasBinaan = ($userRole === 'wali_kelas' && $g->user && $g->user->waliKelas && $g->user->waliKelas->kelas)
                    ? $g->user->waliKelas->kelas
                    : null;
                $assignedKelasId = $kelasBinaan ? $kelasBinaan->id_kelas : null;
            @endphp
            <tr>
                <td style="color:#94a3b8;font-weight:700;font-size:12px;">{{ $guruList->firstItem() + $i }}</td>
                <td>
                    <div class="guru-cell">
                        <div class="guru-avatar {{ $g->jenis_kelamin === 'P' ? 'female' : '' }}">
                            {{ strtoupper(substr($g->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div class="guru-name">{{ $g->nama_lengkap }}</div>
                            <div class="guru-nip">NIP: {{ $g->nip }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($userRole === 'wali_kelas')
                        <span class="badge-role wali-kelas">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            Wali Kelas {{ $kelasBinaan ? '— ' . $kelasBinaan->nama_kelas : '' }}
                        </span>
                    @elseif($userRole === 'guru_piket')
                        <span class="badge-role guru-piket">
                            <i class="fa-solid fa-user-clock"></i> Guru Piket
                        </span>
                    @else
                        <span class="badge-role guru-mapel">
                            <i class="fa-solid fa-book-open"></i> Guru Mapel
                        </span>
                    @endif
                </td>
                <td>
                    @if($g->jenis_kelamin === 'L')
                        <span style="color:#1d4ed8;font-weight:700;"><i class="fa-solid fa-mars"></i> Laki-laki</span>
                    @else
                        <span style="color:#be185d;font-weight:700;"><i class="fa-solid fa-venus"></i> Perempuan</span>
                    @endif
                </td>
                <td style="font-weight:600;color:#334155;">{{ $g->no_hp ?? '-' }}</td>
                <td style="font-size:12.5px;color:#64748b;">
                    @if($g->user)
                        <span style="display:inline-flex;align-items:center;gap:6px;font-weight:600;color:#0f172a;">
                            <i class="fa-solid fa-circle-check" style="color:#10b981;"></i>
                            {{ $g->user->email }}
                        </span>
                    @else
                        <span style="color:#94a3b8;"><i class="fa-solid fa-circle-minus"></i> Belum Ada Akun</span>
                    @endif
                </td>
                <td>
                    @if($g->status_aktif)
                        <span class="badge-status-aktif"><span class="status-dot green"></span> Aktif</span>
                    @else
                        <span class="badge-status-nonaktif"><span class="status-dot red"></span> Tidak Aktif</span>
                    @endif
                </td>
                <td>
                    <div class="action-btns-group" style="justify-content:center;">
                        <button class="btn-table-edit" title="Edit Data" onclick="openEditModal({{ $g->id_guru }}, '{{ addslashes($g->nama_lengkap) }}', '{{ $g->nip }}', '{{ $g->jenis_kelamin }}', '{{ $g->no_hp }}', '{{ addslashes($g->alamat ?? '') }}', {{ $g->status_aktif ? 'true' : 'false' }}, '{{ $userRole }}', '{{ $assignedKelasId }}', '{{ $g->user->email ?? '' }}')">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn-table-delete" title="Hapus Data" onclick="openDeleteModal({{ $g->id_guru }}, '{{ addslashes($g->nama_lengkap) }}')">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:54px 20px;color:#94a3b8;">
                    <i class="fa-solid fa-chalkboard-user" style="font-size:46px;margin-bottom:14px;color:#cbd5e1;"></i>
                    <p style="font-size:16px;font-weight:800;color:#0f172a;">Tidak ada data guru ditemukan</p>
                    <p style="font-size:13px;margin-top:4px;color:#64748b;">Coba ubah kata kunci pencarian atau tambah guru baru.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>{{-- end table-responsive-wrap --}}

    {{-- Custom Pagination Pills --}}
    @if($guruList->hasPages())
    <div class="pagination-container">
        <span class="pag-text">
            Menampilkan {{ $guruList->firstItem() ?? 0 }}–{{ $guruList->lastItem() ?? 0 }} dari {{ $guruList->total() }} guru
        </span>
        <div class="pag-pills">
            @if($guruList->onFirstPage())
                <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $guruList->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            @foreach($guruList->getUrlRange(max(1,$guruList->currentPage()-2), min($guruList->lastPage(),$guruList->currentPage()+2)) as $page => $url)
                @if($page == $guruList->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($guruList->hasMorePages())
                <a href="{{ $guruList->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Add / Edit Modal --}}
<div class="modal-bd" id="guruModal">
    <div class="modal-bx">
        <div class="modal-hdr">
            <h4 class="modal-ttl" id="modalTitle">Tambah Guru Baru</h4>
            <button class="btn-modal-close" onclick="closeModal('guruModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="guruForm" method="POST" action="{{ route('admin.master.guru.store') }}">
            @csrf
            <span id="methodField"></span>

            <div class="form-grid-layout">
                <div class="form-section-title">Informasi Identitas Guru</div>

                <div class="form-group-item full-width">
                    <label class="form-field-lbl">Nama Lengkap &amp; Gelar <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama_lengkap" id="f_nama" class="form-field-ctrl" placeholder="Contoh: Drs. Ahmad Fauzi, M.Pd" required>
                </div>
                <div class="form-group-item">
                    <label class="form-field-lbl">NIP <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nip" id="f_nip" class="form-field-ctrl" placeholder="Nomor Induk Pegawai" required>
                </div>
                <div class="form-group-item">
                    <label class="form-field-lbl">Jenis Kelamin <span style="color:#ef4444">*</span></label>
                    <select name="jenis_kelamin" id="f_jk" class="form-field-ctrl" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group-item">
                    <label class="form-field-lbl">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" id="f_hp" class="form-field-ctrl" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group-item">
                    <label class="form-field-lbl">Status Keaktifan</label>
                    <select name="status_aktif" id="f_status" class="form-field-ctrl">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-group-item full-width">
                    <label class="form-field-lbl">Alamat Tempat Tinggal</label>
                    <textarea name="alamat" id="f_alamat" class="form-field-ctrl" rows="2" placeholder="Alamat rumah guru"></textarea>
                </div>

                {{-- Role / Status Jabatan Guru --}}
                <hr class="form-divider">
                <div class="form-section-title">Jabatan / Status Peran Guru</div>

                <div class="form-group-item full-width">
                    <label class="form-field-lbl">Status Jabatan Guru <span style="color:#ef4444">*</span></label>
                    <select name="role" id="f_role" class="form-field-ctrl" required onchange="handleGuruRoleChange(this.value)">
                        <option value="guru_mapel">Guru Mata Pelajaran</option>
                        <option value="wali_kelas">Guru Wali Kelas</option>
                        <option value="guru_piket">Guru Piket</option>
                    </select>
                    <span class="form-field-hint">Pilih peran guru untuk menentukan hak akses dashboard dan tugas mengajar.</span>
                </div>

                <div class="form-group-item full-width" id="kelasField" style="display:none;">
                    <label class="form-field-lbl">Tugaskan Sebagai Wali Kelas Untuk: <span style="color:#ef4444">*</span></label>
                    <select name="id_kelas" id="f_id_kelas" class="form-field-ctrl">
                        <option value="">-- Pilih Kelas Binaan --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }} ({{ $k->jurusan }})</option>
                        @endforeach
                    </select>
                    <span class="form-field-hint">Pilih kelas yang dibina oleh guru ini.</span>
                </div>

                {{-- Account login section --}}
                <hr class="form-divider" id="loginDivider">
                <div class="form-section-title" id="loginSectionLabel">Akun Akses Sistem (Opsional)</div>
                <div class="form-group-item" id="emailField">
                    <label class="form-field-lbl">Email Login</label>
                    <input type="email" name="email" id="f_email" class="form-field-ctrl" placeholder="email@smkn1boyolangu.sch.id">
                    <span class="form-field-hint">Kosongkan untuk auto-generate dari NIP.</span>
                </div>
                <div class="form-group-item" id="passField">
                    <label class="form-field-lbl">Password Akses</label>
                    <input type="password" name="password" id="f_pass" class="form-field-ctrl" placeholder="Min. 6 karakter">
                    <span class="form-field-hint">Default password: password</span>
                </div>
            </div>

            <div class="form-btn-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('guruModal')">Batal</button>
                <button type="submit" class="btn-modal-submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal-bd" id="deleteModal">
    <div class="modal-bx" style="max-width:440px;">
        <div class="modal-hdr">
            <h4 class="modal-ttl">Hapus Data Guru</h4>
            <button class="btn-modal-close" onclick="closeModal('deleteModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="delete-confirm-box">
            <div class="delete-icon-wrap"><i class="fa-solid fa-trash-can"></i></div>
            <div class="delete-target-name" id="deleteName"></div>
            <div style="font-size:13px;color:#64748b;line-height:1.5;">Data guru ini akan dihapus (*Soft Delete*). Riwayat mengajar &amp; jurnal terdahulu tetap aman tersimpan di sistem.</div>
        </div>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="form-btn-actions" style="justify-content:center;">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('deleteModal')">Batal</button>
                <button type="submit" class="btn-modal-delete-confirm"><i class="fa-solid fa-trash"></i> Ya, Hapus Data</button>
            </div>
        </form>
    </div>
{{-- Import CSV Modal --}}
<div class="modal-bd" id="importModal">
    <div class="modal-bx" style="max-width:540px;">
        <div class="modal-hdr">
            <h4 class="modal-ttl"><i class="fa-solid fa-file-csv" style="color:#2b43b9;margin-right:8px;"></i>Import Data Guru dari File CSV</h4>
            <button class="btn-modal-close" onclick="closeModal('importModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;margin-bottom:18px;font-size:12.5px;color:#475569;line-height:1.5;">
            <div style="font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-circle-info" style="color:#3b82f6;"></i> Petunjuk Format Kolom:
            </div>
            Gunakan format kolom: <b>nip, nama_lengkap, jenis_kelamin, role, email, no_hp, alamat</b>.<br>
            Sistem otomatis membuatkan akun login sistem (password default: <code>password123</code>).
            <div style="margin-top:10px;">
                <a href="{{ route('admin.import.template', 'guru') }}" class="btn-import-csv" style="font-size:11.5px;padding:6px 12px;text-decoration:none;">
                    <i class="fa-solid fa-download"></i> Unduh Template CSV Guru
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.master.guru.import-csv') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:18px;">
                <label class="form-field-lbl">Pilih File CSV (.csv / .txt) <span style="color:#ef4444">*</span></label>
                <input type="file" name="file_csv" accept=".csv, .txt, text/csv" class="form-field-ctrl" required style="padding:8px 10px;">
            </div>

            <div class="form-btn-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('importModal')">Batal</button>
                <button type="submit" class="btn-modal-submit">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Unggah &amp; Proses Impor
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.querySelectorAll('.toast').forEach(t => {
        setTimeout(() => t.style.opacity = '0', 3500);
        setTimeout(() => t.remove(), 3800);
    });

    function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    function openImportModal() { document.getElementById('importModal').classList.add('show'); }

    function handleGuruRoleChange(role) {
        const kelasField = document.getElementById('kelasField');
        if (role === 'wali_kelas') {
            kelasField.style.display = 'flex';
            document.getElementById('f_id_kelas').setAttribute('required', 'required');
        } else {
            kelasField.style.display = 'none';
            document.getElementById('f_id_kelas').removeAttribute('required');
        }
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Guru Baru';
        document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-plus"></i> Simpan Data';
        const form = document.getElementById('guruForm');
        form.action = '{{ route("admin.master.guru.store") }}';
        document.getElementById('methodField').innerHTML = '';
        form.reset();
        document.getElementById('f_role').value = 'guru_mapel';
        handleGuruRoleChange('guru_mapel');
        document.getElementById('guruModal').classList.add('show');
    }

    function openEditModal(id, nama, nip, jk, hp, alamat, aktif, role, idKelas, email) {
        document.getElementById('modalTitle').innerText = 'Edit Data Guru';
        document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Perbarui Data';
        const form = document.getElementById('guruForm');
        form.action = '/admin/master/guru/' + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';

        document.getElementById('f_nama').value   = nama;
        document.getElementById('f_nip').value    = nip;
        document.getElementById('f_jk').value     = jk;
        document.getElementById('f_hp').value     = hp;
        document.getElementById('f_alamat').value = alamat;
        document.getElementById('f_status').value = aktif ? '1' : '0';
        document.getElementById('f_role').value   = role || 'guru_mapel';
        document.getElementById('f_email').value  = email || '';
        document.getElementById('f_pass').value   = '';

        handleGuruRoleChange(role || 'guru_mapel');
        if (idKelas) {
            document.getElementById('f_id_kelas').value = idKelas;
        }

        document.getElementById('guruModal').classList.add('show');
    }

    function openDeleteModal(id, nama) {
        document.getElementById('deleteName').innerText = nama;
        document.getElementById('deleteForm').action = '/admin/master/guru/' + id;
        document.getElementById('deleteModal').classList.add('show');
    }

    ['guruModal','deleteModal','importModal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if(e.target === this) closeModal(id);
        });
    });

    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if(e.key === 'Enter') document.getElementById('filterForm').submit();
    });
</script>
@endsection
