@extends('layouts.admin')

@section('title', 'Manajemen Siswa — SMKN 1 BOYOLANGU')
@section('header_title', 'Manajemen Siswa')
@section('header_subtitle', 'Kelola data induk siswa — tambah, ubah, dan hapus data')

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

    /* Page Top Action Bar */
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

    .btn-add-siswa {
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
    .btn-add-siswa:hover {
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
    .kpi-icon.rose    { background: linear-gradient(135deg, #ffe4e6, #fecdd3); color: #9f1239; }
    .kpi-icon.amber   { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    
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
        padding: 16px; font-size: 13.5px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; transition: background 0.15s ease;
    }
    .data-table tbody tr:hover td { background: #f8fafc; }

    /* Student Cell */
    .student-cell { display: flex; align-items: center; gap: 14px; }
    .student-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: linear-gradient(135deg, #c7d2fe, #a5b4fc); color: #3730a3;
        display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 900; flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(199,210,254,0.5);
    }
    .student-avatar.female { background: linear-gradient(135deg, #fce7f3, #f9a8d4); color: #9d174d; box-shadow: 0 2px 8px rgba(252,231,243,0.5); }
    
    .student-name { font-weight: 700; color: #0f172a; font-size: 14px; }
    .student-nis  { font-size: 11.5px; color: #64748b; font-weight: 600; margin-top: 1px; }

    .kelas-pill { background: #e0e7ff; color: #3730a3; font-weight: 800; font-size: 12px; padding: 5px 12px; border-radius: 8px; display: inline-block; }
    
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
        background: #ffffff; border-radius: 24px; width: 92%; max-width: 580px; max-height: 90vh; overflow-y: auto; padding: 28px;
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
    }
    .form-field-ctrl:focus { border-color: #2b43b9; background: #ffffff; box-shadow: 0 0 0 3px rgba(43,67,185,0.1); }

    .form-btn-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-modal-cancel { background: #f1f5f9; border: none; border-radius: 12px; padding: 11px 22px; font-size: 13.5px; font-weight: 700; color: #475569; cursor: pointer; }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-submit { background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%); color: #ffffff; border: none; border-radius: 12px; padding: 11px 24px; font-size: 13.5px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(43,67,185,0.25); }

    /* Delete modal body */
    .delete-confirm-box { text-align: center; padding: 12px 0 20px; }
    .delete-icon-wrap { font-size: 46px; color: #ef4444; margin-bottom: 14px; }
    .delete-target-name { font-weight: 800; color: #0f172a; font-size: 18px; margin-bottom: 8px; }
    .btn-modal-delete-confirm { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; border: none; border-radius: 12px; padding: 11px 24px; font-size: 13.5px; font-weight: 700; cursor: pointer; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px; }
        .kpi-icon { width: 42px; height: 42px; font-size: 18px; }
        .kpi-val { font-size: 24px; }
        .kpi-lbl { font-size: 11px; }
        .kpi-sub { font-size: 10px; }
        .table-card { padding: 16px; }
        .form-btn-actions { flex-direction: column; }
        .form-btn-actions .btn-modal-cancel,
        .form-btn-actions .btn-modal-submit { width: 100%; text-align: center; }
    }
    @media (max-width: 480px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .kpi-card { flex-direction: row; gap: 14px; }
        .count-badge { font-size: 11px; padding: 4px 10px; }
    }
</style>
@endsection

@section('content')

{{-- Toast Notification --}}
<div class="toast-container" id="toastContainer">
    @if(session('success'))
        <div class="toast"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error') || $errors->any())
        <div class="toast error"><i class="fa-solid fa-circle-xmark"></i><span>{{ session('error') ?? $errors->first() }}</span></div>
    @endif
</div>

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon indigo"><i class="fa-solid fa-users"></i></div>
        <div>
            <div class="kpi-val">{{ number_format($totalSiswa, 0, ',', '.') }}</div>
            <div class="kpi-lbl">Total Siswa Terdaftar</div>
            <div class="kpi-sub">Seluruh rombel sekolah</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon emerald"><i class="fa-solid fa-user-check"></i></div>
        <div>
            <div class="kpi-val">{{ number_format($aktif, 0, ',', '.') }}</div>
            <div class="kpi-lbl">Siswa Status Aktif</div>
            <div class="kpi-sub">Mengikuti pembelajaran</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon rose"><i class="fa-solid fa-user-xmark"></i></div>
        <div>
            <div class="kpi-val">{{ $nonAktif }}</div>
            <div class="kpi-lbl">Status Tidak Aktif</div>
            <div class="kpi-sub">Nonaktif / Lulus / Pindah</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon amber"><i class="fa-solid fa-school"></i></div>
        <div>
            <div class="kpi-val">{{ $kelasList->count() }}</div>
            <div class="kpi-lbl">Total Rombongan Belajar</div>
            <div class="kpi-sub">Kelas X, XI, &amp; XII</div>
        </div>
    </div>
</div>

{{-- Filter & Action Bar Card --}}
<form action="{{ route('admin.master.siswa') }}" method="GET" id="filterForm">
<div class="page-action-card">
    <div class="filter-group">
        <div class="filter-item">
            <label class="filter-label">Pencarian Siswa</label>
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $search }}" class="filter-input" placeholder="Cari Nama, NIS, NISN…" id="searchInput">
            </div>
        </div>
        <div class="filter-item">
            <label class="filter-label">Kelas / Rombel</label>
            <select name="id_kelas" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id_kelas }}" {{ $kelasId == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-item">
            <label class="filter-label">Status Siswa</label>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $status === '0' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        @if($search || $kelasId || ($status !== null && $status !== ''))
            <a href="{{ route('admin.master.siswa') }}" class="btn-filter-action" style="align-self:flex-end;">
                <i class="fa-solid fa-xmark"></i> Reset Filter
            </a>
        @endif
    </div>

    <div style="display:flex;align-items:center;gap:10px;">
        <button type="button" class="btn-import-csv" onclick="openImportModal()">
            <i class="fa-solid fa-file-arrow-up"></i> Import CSV
        </button>

        <button type="button" class="btn-add-siswa" onclick="openAddModal()">
            <i class="fa-solid fa-user-plus"></i> Tambah Siswa Baru
        </button>
    </div>
</div>
</form>

{{-- Table Card --}}
<div class="table-card">
    <div class="table-hdr">
        <div class="table-title">
            <i class="fa-solid fa-address-book"></i>
            <span>Daftar Data Induk Siswa</span>
        </div>
        <span class="count-badge"><i class="fa-solid fa-user-group"></i> {{ $siswaList->total() }} Siswa Ditemukan</span>
    </div>

    <div class="table-responsive-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:48px;">#</th>
                <th>Nama Siswa &amp; Identitas (NISN / NISS)</th>
                <th>Kelas</th>
                <th>Jenis Kelamin</th>
                <th>No. HP Ortu</th>
                <th>Status</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaList as $i => $s)
            <tr>
                <td style="color:#94a3b8;font-weight:700;font-size:12px;">{{ $siswaList->firstItem() + $i }}</td>
                <td>
                    <div class="student-cell">
                        <div class="student-avatar {{ $s->jenis_kelamin === 'P' ? 'female' : '' }}">
                            {{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <div class="student-name">{{ $s->nama_lengkap }}</div>
                            <div class="student-nis">
                                @if($s->nisn) <span>NISN: {{ $s->nisn }}</span> @endif
                                @if($s->niss)
                                    <span>· NISS: {{ $s->niss }}</span>
                                @elseif($s->nis && $s->nis !== $s->nisn)
                                    <span>· NIS: {{ $s->nis }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
                <td><span class="kelas-pill">{{ $s->kelas->nama_kelas ?? '-' }}</span></td>
                <td>
                    @if($s->jenis_kelamin === 'L')
                        <span style="color:#1d4ed8;font-weight:700;"><i class="fa-solid fa-mars"></i> Laki-laki</span>
                    @else
                        <span style="color:#be185d;font-weight:700;"><i class="fa-solid fa-venus"></i> Perempuan</span>
                    @endif
                </td>
                <td style="color:#334155;font-weight:600;">{{ $s->no_hp_ortu ?? '-' }}</td>
                <td>
                    @if($s->status_aktif)
                        <span class="badge-status-aktif"><span class="status-dot green"></span> Aktif</span>
                    @else
                        <span class="badge-status-nonaktif"><span class="status-dot red"></span> Tidak Aktif</span>
                    @endif
                </td>
                <td>
                    <div class="action-btns-group" style="justify-content:center;">
                        <button class="btn-table-edit" title="Edit Data" onclick="openEditModal({{ $s->id_siswa }}, '{{ addslashes($s->nama_lengkap) }}', '{{ $s->nis }}', '{{ $s->nisn }}', '{{ $s->jenis_kelamin }}', '{{ $s->id_kelas }}', '{{ $s->no_hp_ortu }}', '{{ addslashes($s->alamat) }}', {{ $s->status_aktif ? 'true' : 'false' }}, '{{ $s->niss }}')">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn-table-delete" title="Hapus Data" onclick="openDeleteModal({{ $s->id_siswa }}, '{{ addslashes($s->nama_lengkap) }}')">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:54px 20px;color:#94a3b8;">
                    <i class="fa-solid fa-users-slash" style="font-size:46px;margin-bottom:14px;color:#cbd5e1;"></i>
                    <p style="font-size:16px;font-weight:800;color:#0f172a;">Tidak ada data siswa ditemukan</p>
                    <p style="font-size:13px;margin-top:4px;color:#64748b;">Coba ubah kata kunci pencarian atau tambah siswa baru.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>{{-- end table-responsive-wrap --}}

    {{-- Pagination --}}
    <div class="pagination-container">
        <span class="pag-text">
            Menampilkan {{ $siswaList->firstItem() ?? 0 }}–{{ $siswaList->lastItem() ?? 0 }} dari {{ $siswaList->total() }} siswa
        </span>
        <div class="pag-pills">
            @if($siswaList->onFirstPage())
                <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $siswaList->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            @foreach($siswaList->getUrlRange(max(1,$siswaList->currentPage()-2), min($siswaList->lastPage(),$siswaList->currentPage()+2)) as $page => $url)
                @if($page == $siswaList->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($siswaList->hasMorePages())
                <a href="{{ $siswaList->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
</div>

{{-- Add / Edit Modal --}}
<div class="modal-bd" id="siswaModal">
    <div class="modal-bx">
        <div class="modal-hdr">
            <h4 class="modal-ttl" id="modalTitle">Tambah Siswa Baru</h4>
            <button class="btn-modal-close" onclick="closeModal('siswaModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="siswaForm" method="POST" action="{{ route('admin.master.siswa.store') }}">
            @csrf
            <span id="methodField"></span>

            <div class="form-grid-layout">
                <div class="form-group-item">
                    <label class="form-field-lbl">NISN (Nomor Induk Siswa Nasional)</label>
                    <input type="text" name="nisn" id="f_nisn" class="form-field-ctrl" placeholder="Contoh: 0105292765">
                </div>
                <div class="form-group-item">
                    <label class="form-field-lbl">N I S S (NIS Lokal Sekolah) <span style="font-size:11px;color:#94a3b8;">(Opsional)</span></label>
                    <input type="text" name="niss" id="f_niss" class="form-field-ctrl" placeholder="Nomor induk sekolah jika ada">
                    <input type="hidden" name="nis" id="f_nis">
                </div>
                <div class="form-group-item full-width">
                    <label class="form-field-lbl">Nama Lengkap <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama_lengkap" id="f_nama" class="form-field-ctrl" placeholder="Nama siswa lengkap" required>
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
                    <label class="form-field-lbl">Kelas / Rombel <span style="color:#ef4444">*</span></label>
                    <select name="id_kelas" id="f_kelas" class="form-field-ctrl" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group-item">
                    <label class="form-field-lbl">No. HP Orang Tua</label>
                    <input type="text" name="no_hp_ortu" id="f_hp" class="form-field-ctrl" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group-item">
                    <label class="form-field-lbl">Status Siswa</label>
                    <select name="status_aktif" id="f_status" class="form-field-ctrl">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
                <div class="form-group-item full-width">
                    <label class="form-field-lbl">Alamat Tempat Tinggal</label>
                    <textarea name="alamat" id="f_alamat" class="form-field-ctrl" rows="2" placeholder="Alamat lengkap siswa"></textarea>
                </div>
            </div>

            <div class="form-btn-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('siswaModal')">Batal</button>
                <button type="submit" class="btn-modal-submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal-bd" id="deleteModal">
    <div class="modal-bx" style="max-width:440px;">
        <div class="modal-hdr">
            <h4 class="modal-ttl">Hapus Data Siswa</h4>
            <button class="btn-modal-close" onclick="closeModal('deleteModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="delete-confirm-box">
            <div class="delete-icon-wrap"><i class="fa-solid fa-trash-can"></i></div>
            <div class="delete-target-name" id="deleteName"></div>
            <div style="font-size:13px;color:#64748b;line-height:1.5;">Data siswa ini akan dihapus (*Soft Delete*). Riwayat presensi &amp; jurnal terdahulu akan tetap tersimpan aman di sistem.</div>
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
</div>

{{-- Import CSV Modal --}}
<div class="modal-bd" id="importModal">
    <div class="modal-bx" style="max-width:540px;">
        <div class="modal-hdr">
            <h4 class="modal-ttl"><i class="fa-solid fa-file-csv" style="color:#2b43b9;margin-right:8px;"></i>Import Data Siswa dari File CSV</h4>
            <button class="btn-modal-close" onclick="closeModal('importModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;margin-bottom:18px;font-size:12.5px;color:#475569;line-height:1.5;">
            <div style="font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-circle-info" style="color:#3b82f6;"></i> Format Daftar Presensi SMKN 1 Boyolangu:
            </div>
            Gunakan format kolom: <b>NO, NISN, NAMA, N I S S, L/P, KELAS</b>.<br>
            <ul style="margin:6px 0 6px 18px;padding:0;font-size:12px;color:#64748b;">
                <li>Kolom <b>NISN</b>, <b>NAMA</b>, dan <b>L/P</b> adalah data utama siswa.</li>
                <li>Kolom <b>N I S S</b> (NIS Lokal) bersifat opsional (jika kosong, sistem otomatis menyamakan dengan NISN).</li>
                <li>Kolom <b>KELAS</b> dapat dicantumkan di CSV (misal: <code>X TKI 1</code>) atau ditentukan lewat pilihan kelas di bawah.</li>
            </ul>
            <div style="margin-top:10px;">
                <a href="{{ route('admin.import.template', 'siswa') }}" class="btn-import-csv" style="font-size:11.5px;padding:6px 12px;text-decoration:none;">
                    <i class="fa-solid fa-download"></i> Unduh Template CSV Sesuai Presensi Boyolangu
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.master.siswa.import-csv') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:14px;">
                <label class="form-field-lbl">Pilih Kelas Target <span style="font-size:11px;color:#94a3b8;font-weight:normal;">(Opsional, jika di dalam file belum ada kolom kelas)</span></label>
                <select name="id_kelas" class="form-field-ctrl" style="padding:9px 12px;">
                    <option value="">-- Otomatis dari File CSV / Pilih Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

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

    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Siswa Baru';
        document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-plus"></i> Simpan Data';
        const form = document.getElementById('siswaForm');
        form.action = '{{ route("admin.master.siswa.store") }}';
        document.getElementById('methodField').innerHTML = '';
        form.reset();
        document.getElementById('f_nis').value = '';
        document.getElementById('f_nisn').value = '';
        document.getElementById('f_niss').value = '';
        document.getElementById('siswaModal').classList.add('show');
    }

    function openEditModal(id, nama, nis, nisn, jk, kelasId, hp, alamat, aktif, niss) {
        document.getElementById('modalTitle').innerText = 'Edit Data Siswa';
        document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Perbarui Data';
        const form = document.getElementById('siswaForm');
        form.action = '/admin/master/siswa/' + id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';

        document.getElementById('f_nis').value    = nis || '';
        document.getElementById('f_nisn').value   = nisn || '';
        document.getElementById('f_niss').value   = niss || '';
        document.getElementById('f_nama').value   = nama;
        document.getElementById('f_jk').value     = jk;
        document.getElementById('f_kelas').value  = kelasId;
        document.getElementById('f_hp').value     = hp;
        document.getElementById('f_alamat').value = alamat;
        document.getElementById('f_status').value = aktif ? '1' : '0';

        document.getElementById('siswaModal').classList.add('show');
    }

    function openDeleteModal(id, nama) {
        document.getElementById('deleteName').innerText = nama;
        document.getElementById('deleteForm').action = '/admin/master/siswa/' + id;
        document.getElementById('deleteModal').classList.add('show');
    }

    ['siswaModal','deleteModal','importModal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if(e.target === this) closeModal(id);
        });
    });

    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if(e.key === 'Enter') document.getElementById('filterForm').submit();
    });
</script>
@endsection
