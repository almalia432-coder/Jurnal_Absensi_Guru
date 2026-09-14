@extends('layouts.waka_kurikulum')
@section('title', 'Mata Pelajaran - Waka Kurikulum')
@section('header_title', 'Mata Pelajaran')
@section('header_subtitle', 'Kelola struktur mata pelajaran, kelompok kurikulum, dan distribusi pengajar')

@section('header_extra')
    <button type="button" class="btn-primary-action" onclick="openTambahMapelModal()">
        <i class="fa-solid fa-plus"></i> Tambah Mapel Baru
    </button>
@endsection

@section('styles')
<style>
    .btn-primary-action {
        background: #ffffff; color: #0284c7; border: none; padding: 10px 18px;
        border-radius: 12px; font-weight: 700; font-size: 13.5px; cursor: pointer;
        display: flex; align-items: center; gap: 8px; transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .btn-primary-action:hover { background: #f0f9ff; transform: translateY(-1px); }

    /* KPI Cards */
    .kpi-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 24px; }
    .kpi-card {
        background: white; border-radius: 18px; border: 1px solid #e2e8f0;
        padding: 18px 20px; display: flex; align-items: center; gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .kpi-icon {
        width: 48px; height: 48px; border-radius: 14px; display: flex;
        align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
    }
    .kpi-icon.all { background: #e0f2fe; color: #0284c7; }
    .kpi-icon.normatif { background: #dcfce7; color: #16a34a; }
    .kpi-icon.adaptif { background: #fef3c7; color: #d97706; }
    .kpi-icon.produktif { background: #f3e8ff; color: #9333ea; }
    .kpi-icon.mulok { background: #ffe4e6; color: #e11d48; }
    .kpi-info h4 { font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1.1; }
    .kpi-info p { font-size: 11.5px; font-weight: 600; color: #64748b; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Filter Bar */
    .filter-bar {
        background: white; border-radius: 18px; border: 1px solid #e2e8f0;
        padding: 16px 22px; margin-bottom: 22px; display: flex; align-items: center;
        justify-content: space-between; gap: 16px; flex-wrap: wrap;
    }
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-tab {
        padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 700;
        cursor: pointer; text-decoration: none; background: #f8fafc; color: #64748b;
        border: 1.5px solid #e2e8f0; transition: all 0.15s; display: inline-flex; align-items: center; gap: 8px;
    }
    .filter-tab.active { background: #0284c7; color: white; border-color: #0284c7; }
    .filter-tab:hover:not(.active) { background: #f0f9ff; color: #0284c7; border-color: #bae6fd; }
    .badge-count { font-size: 11px; padding: 1px 7px; border-radius: 6px; background: rgba(0,0,0,0.08); }
    .filter-tab.active .badge-count { background: rgba(255,255,255,0.25); color: white; }

    .search-wrap { display: flex; align-items: center; position: relative; width: 280px; }
    .search-input {
        width: 100%; padding: 9px 14px 9px 36px; border-radius: 10px;
        border: 1.5px solid #e2e8f0; font-size: 13px; font-family: inherit;
    }
    .search-input:focus { outline: none; border-color: #0284c7; }
    .search-icon { position: absolute; left: 12px; color: #94a3b8; font-size: 14px; }

    /* Table Card */
    .table-card { background: white; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; }
    .table-header-custom {
        padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex;
        align-items: center; justify-content: space-between;
    }
    .table-title { font-size: 15px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
        padding: 12px 20px; text-align: left; font-size: 11.5px; font-weight: 700;
        text-transform: uppercase; color: #94a3b8; border-bottom: 1px solid #f1f5f9; background: #fafcff;
    }
    .data-table td {
        padding: 15px 20px; font-size: 13.5px; border-bottom: 1px solid #f8fafc;
        color: #334155; vertical-align: middle;
    }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #f8fafc; }

    /* Kelompok Badge */
    .badge-kelompok {
        padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .badge-kelompok.Normatif { background: #dcfce7; color: #15803d; }
    .badge-kelompok.Adaptif { background: #fef3c7; color: #b45309; }
    .badge-kelompok.Produktif { background: #f3e8ff; color: #7e22ce; }
    .badge-kelompok.Muatan_Lokal { background: #ffe4e6; color: #be123c; }

    /* Teacher Avatar Stack */
    .teacher-avatars { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .teacher-pill {
        display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px 3px 6px;
        border-radius: 20px; background: #f1f5f9; border: 1px solid #e2e8f0; font-size: 12px; font-weight: 600;
    }
    .teacher-avatar-sm { width: 20px; height: 20px; border-radius: 50%; object-fit: cover; }
    .no-teacher { font-size: 12px; color: #94a3b8; font-style: italic; }

    /* Action Buttons */
    .btn-action-icon {
        width: 32px; height: 32px; border-radius: 9px; border: 1px solid #e2e8f0;
        background: #ffffff; color: #64748b; display: inline-flex; align-items: center;
        justify-content: center; cursor: pointer; font-size: 13px; transition: all 0.15s; text-decoration: none;
    }
    .btn-action-icon:hover { background: #f1f5f9; color: #0f172a; }
    .btn-action-icon.edit:hover { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
    .btn-action-icon.delete:hover { background: #fee2e2; color: #ef4444; border-color: #fecaca; }
    .btn-action-icon.info:hover { background: #f3e8ff; color: #9333ea; border-color: #e9d5ff; }

    .btn-import-csv {
        background: #f1f5f9;
        color: #1e293b;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 9px 16px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-import-csv:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .btn-add-primary {
        background: linear-gradient(135deg, #2b43b9 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(43,67,185,0.25);
        white-space: nowrap;
    }
    .btn-add-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(43,67,185,0.35);
        color: #ffffff;
    }

    /* Modal Form Styles */
    .custom-modal-backdrop {
        display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px); z-index: 10000; align-items: center; justify-content: center; padding: 20px;
    }
    .custom-modal-backdrop.open { display: flex; }
    .custom-modal-box {
        background: white; border-radius: 22px; width: 100%; max-width: 520px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.25); overflow: hidden; animation: popIn 0.25s ease-out;
    }
    @keyframes popIn {
        from { opacity: 0; transform: translateY(15px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-hdr {
        padding: 20px 24px; background: linear-gradient(135deg, #3d56b2, #2b3a8c);
        color: white; display: flex; align-items: center; justify-content: space-between;
    }
    .modal-hdr h3 { font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .modal-hdr-close {
        width: 32px; height: 32px; border-radius: 50%; border: none;
        background: rgba(255,255,255,0.2); color: white; cursor: pointer; font-size: 15px;
    }
    .modal-bdy { padding: 24px; }
    .modal-ftr { padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px; }
    .form-group-custom { margin-bottom: 18px; }
    .form-group-custom label { display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control-custom {
        width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid #e2e8f0;
        font-size: 14px; font-family: inherit; transition: border-color 0.2s;
    }
    .form-control-custom:focus { outline: none; border-color: #0284c7; box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1); }
    .btn-cancel-modal {
        padding: 9px 18px; border-radius: 10px; border: 1px solid #cbd5e1;
        background: white; color: #475569; font-weight: 700; font-size: 13.5px; cursor: pointer;
    }
    .btn-submit-modal {
        padding: 9px 22px; border-radius: 10px; border: none;
        background: #0284c7; color: white; font-weight: 700; font-size: 13.5px; cursor: pointer;
    }
    .btn-submit-modal:hover { background: #0369a1; }

    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }
    .pag-text {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }
    .pag-pills {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .pag-pills a, .pag-pills span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .pag-pills a {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .pag-pills a:hover {
        background: #e0f2fe;
        color: #0284c7;
        border-color: #bae6fd;
    }
    .pag-pills span.active {
        background: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }
    .pag-pills span.disabled {
        background: #f8fafc;
        color: #cbd5e1;
        border: 1px solid #f1f5f9;
        cursor: not-allowed;
    }

    @media (max-width: 1100px) {
        .kpi-row { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 680px) {
        .kpi-row { grid-template-columns: 1fr; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .search-wrap { width: 100%; }
        .pagination-container { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection

@section('content')
<div>
    <!-- KPI Row -->
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-icon all"><i class="fa-solid fa-book"></i></div>
            <div class="kpi-info">
                <h4>{{ $totalMapel }}</h4>
                <p>Total Mapel</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon normatif"><i class="fa-solid fa-scale-balanced"></i></div>
            <div class="kpi-info">
                <h4>{{ $totalNormatif }}</h4>
                <p>Normatif</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon adaptif"><i class="fa-solid fa-calculator"></i></div>
            <div class="kpi-info">
                <h4>{{ $totalAdaptif }}</h4>
                <p>Adaptif</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon produktif"><i class="fa-solid fa-laptop-code"></i></div>
            <div class="kpi-info">
                <h4>{{ $totalProduktif }}</h4>
                <p>Produktif Kejuruan</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon mulok"><i class="fa-solid fa-seedling"></i></div>
            <div class="kpi-info">
                <h4>{{ $totalMulok }}</h4>
                <p>Muatan Lokal</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-bar">
        <div class="filter-tabs">
            @foreach(['all' => 'Semua Kelompok', 'Normatif' => 'Normatif', 'Adaptif' => 'Adaptif', 'Produktif' => 'Produktif', 'Muatan_Lokal' => 'Muatan Lokal'] as $key => $label)
                <a href="{{ route('waka-kurikulum.mapel.index', array_merge(request()->except('kelompok'), ['kelompok' => $key])) }}"
                   class="filter-tab {{ ($kelompok === $key || (!$kelompok && $key === 'all')) ? 'active' : '' }}">
                    <span>{{ $label }}</span>
                    <span class="badge-count">{{ $kelompokCounts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <form action="{{ route('waka-kurikulum.mapel.index') }}" method="GET" class="search-wrap">
            @if($kelompok)
                <input type="hidden" name="kelompok" value="{{ $kelompok }}">
            @endif
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" name="search" class="search-input" placeholder="Cari kode atau nama mapel..." value="{{ $search }}">
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-header-custom">
            <div class="table-title">
                <i class="fa-solid fa-layer-group" style="color:#0284c7;"></i>
                <span>Daftar Mata Pelajaran Kurikulum</span>
                <span class="badge-count" style="font-size:12px;background:#e0f2fe;color:#0284c7;padding:2px 10px;border-radius:8px;">
                    {{ $mapelList->total() }} Mata Pelajaran
                </span>
            </div>

            <div style="display:flex;align-items:center;gap:10px;">
                <button type="button" class="btn-import-csv" onclick="openImportModal()">
                    <i class="fa-solid fa-file-arrow-up"></i> Import CSV
                </button>
                <button type="button" class="btn-add-primary" onclick="openTambahMapelModal()">
                    <i class="fa-solid fa-plus"></i> Tambah Mapel Baru
                </button>
            </div>
        </div>

        <div class="table-responsive-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Kelompok</th>
                        <th>Guru Pengampu</th>
                        <th>Terjadwal di Kelas</th>
                        <th style="text-align: center; width: 110px;">Total JP</th>
                        <th style="text-align: center; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mapelList as $index => $mapel)
                        <tr>
                            <td>{{ $mapelList->firstItem() + $index }}</td>
                            <td>
                                <strong style="color: #0284c7; font-family: monospace; font-size: 13.5px;">
                                    {{ $mapel->kode_mapel }}
                                </strong>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a;">{{ $mapel->nama_mapel }}</div>
                            </td>
                            <td>
                                <span class="badge-kelompok {{ $mapel->kelompok }}">
                                    {{ str_replace('_', ' ', $mapel->kelompok) }}
                                </span>
                            </td>
                            <td>
                                @if($mapel->assigned_guru && $mapel->assigned_guru->count() > 0)
                                    <div class="teacher-avatars">
                                        @foreach($mapel->assigned_guru->take(2) as $guru)
                                            <span class="teacher-pill" title="{{ $guru->nama_lengkap }} (NIP: {{ $guru->nip }})">
                                                <i class="fa-solid fa-chalkboard-user" style="color:#0284c7;font-size:11px;"></i>
                                                <span>{{ Str::limit($guru->nama_lengkap, 16) }}</span>
                                            </span>
                                        @endforeach
                                        @if($mapel->assigned_guru->count() > 2)
                                            <span class="teacher-pill" style="background:#e0f2fe;color:#0284c7;cursor:pointer;" onclick="openDetailPengampu({{ $mapel->id_mapel }})">
                                                +{{ $mapel->assigned_guru->count() - 2 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="no-teacher">Belum ada pengampu</span>
                                @endif
                            </td>
                            <td>
                                @if($mapel->assigned_kelas && $mapel->assigned_kelas->count() > 0)
                                    <span style="font-weight: 600; color: #334155;">
                                        {{ $mapel->assigned_kelas->count() }} Rombel
                                    </span>
                                    <span style="font-size: 11px; color: #64748b;">
                                        ({{ $mapel->assigned_kelas->pluck('nama_kelas')->take(2)->implode(', ') }}{{ $mapel->assigned_kelas->count() > 2 ? '...' : '' }})
                                    </span>
                                @else
                                    <span class="no-teacher">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 800; color: #0284c7; font-size: 14px;">
                                    {{ $mapel->total_jp }} JP
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" class="btn-action-icon info" title="Lihat Pengampu & Kelas" onclick="openDetailPengampu({{ $mapel->id_mapel }})">
                                        <i class="fa-solid fa-users"></i>
                                    </button>
                                    <button type="button" class="btn-action-icon edit" title="Edit Mapel" onclick="openEditMapelModal({{ json_encode($mapel) }})">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn-action-icon delete" title="Hapus Mapel" onclick="openDeleteMapelModal({{ $mapel->id_mapel }}, '{{ addslashes($mapel->nama_mapel) }}')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                                <i class="fa-solid fa-book-open" style="font-size: 40px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                                <span style="font-size: 14px; font-weight: 600;">Tidak ada mata pelajaran yang ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mapelList->hasPages())
        <div class="pagination-container">
            <span class="pag-text">
                Menampilkan <b>{{ $mapelList->firstItem() ?? 0 }}</b>–<b>{{ $mapelList->lastItem() ?? 0 }}</b> dari <b>{{ $mapelList->total() }}</b> mata pelajaran
            </span>
            <div class="pag-pills">
                @if($mapelList->onFirstPage())
                    <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                @else
                    <a href="{{ $mapelList->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                @endif

                @foreach($mapelList->getUrlRange(max(1, $mapelList->currentPage() - 2), min($mapelList->lastPage(), $mapelList->currentPage() + 2)) as $page => $url)
                    @if($page == $mapelList->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($mapelList->hasMorePages())
                    <a href="{{ $mapelList->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                @else
                    <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- ================= MODAL TAMBAH MAPEL ================= -->
<div class="custom-modal-backdrop" id="modalTambahMapel">
    <div class="custom-modal-box">
        <div class="modal-hdr">
            <h3><i class="fa-solid fa-book-medical"></i> Tambah Mata Pelajaran Baru</h3>
            <button type="button" class="modal-hdr-close" onclick="closeTambahMapelModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('waka-kurikulum.mapel.store') }}" method="POST">
            @csrf
            <div class="modal-bdy">
                <div class="form-group-custom">
                    <label for="tambah_kode_mapel">Kode Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="kode_mapel" id="tambah_kode_mapel" class="form-control-custom" placeholder="Contoh: MTK-01, INF-10, BIND-01" required>
                </div>
                <div class="form-group-custom">
                    <label for="tambah_nama_mapel">Nama Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama_mapel" id="tambah_nama_mapel" class="form-control-custom" placeholder="Contoh: Matematika, Pemrograman Web" required>
                </div>
                <div class="form-group-custom">
                    <label for="tambah_kelompok">Kelompok Kurikulum <span style="color:#ef4444;">*</span></label>
                    <select name="kelompok" id="tambah_kelompok" class="form-control-custom" required>
                        <option value="">-- Pilih Kelompok --</option>
                        <option value="Normatif">Normatif (Kelompok A / Wajib)</option>
                        <option value="Adaptif">Adaptif (Kelompok B / Kejuruan Umum)</option>
                        <option value="Produktif">Produktif (Kelompok C / Kejuruan Spesialis)</option>
                        <option value="Muatan_Lokal">Muatan Lokal</option>
                    </select>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-cancel-modal" onclick="closeTambahMapelModal()">Batal</button>
                <button type="submit" class="btn-submit-modal">Simpan Mata Pelajaran</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT MAPEL ================= -->
<div class="custom-modal-backdrop" id="modalEditMapel">
    <div class="custom-modal-box">
        <div class="modal-hdr">
            <h3><i class="fa-solid fa-pen-to-square"></i> Edit Mata Pelajaran</h3>
            <button type="button" class="modal-hdr-close" onclick="closeEditMapelModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditMapel" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-bdy">
                <div class="form-group-custom">
                    <label for="edit_kode_mapel">Kode Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="kode_mapel" id="edit_kode_mapel" class="form-control-custom" required>
                </div>
                <div class="form-group-custom">
                    <label for="edit_nama_mapel">Nama Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama_mapel" id="edit_nama_mapel" class="form-control-custom" required>
                </div>
                <div class="form-group-custom">
                    <label for="edit_kelompok">Kelompok Kurikulum <span style="color:#ef4444;">*</span></label>
                    <select name="kelompok" id="edit_kelompok" class="form-control-custom" required>
                        <option value="Normatif">Normatif (Kelompok A / Wajib)</option>
                        <option value="Adaptif">Adaptif (Kelompok B / Kejuruan Umum)</option>
                        <option value="Produktif">Produktif (Kelompok C / Kejuruan Spesialis)</option>
                        <option value="Muatan_Lokal">Muatan Lokal</option>
                    </select>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-cancel-modal" onclick="closeEditMapelModal()">Batal</button>
                <button type="submit" class="btn-submit-modal">Perbarui Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL DETAIL PENGAMPU ================= -->
<div class="custom-modal-backdrop" id="modalDetailPengampu">
    <div class="custom-modal-box" style="max-width: 600px;">
        <div class="modal-hdr">
            <h3 id="modalDetailTitle"><i class="fa-solid fa-chalkboard-user"></i> Daftar Guru Pengampu</h3>
            <button type="button" class="modal-hdr-close" onclick="closeDetailPengampu()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-bdy" id="modalDetailContent" style="max-height: 400px; overflow-y: auto;">
            <div style="text-align:center; padding: 20px; color:#94a3b8;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size:24px;"></i>
                <p style="margin-top:8px;">Memuat rincian guru pengajar...</p>
            </div>
        </div>
        <div class="modal-ftr">
            <button type="button" class="btn-cancel-modal" onclick="closeDetailPengampu()">Tutup</button>
            <a href="{{ route('waka-kurikulum.guru-mengajar.index') }}" class="btn-submit-modal" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-sliders"></i> Atur Plotting Guru
            </a>
        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI HAPUS ================= -->
<div class="custom-modal-backdrop" id="modalDeleteMapel">
    <div class="custom-modal-box" style="max-width: 440px; text-align: center;">
        <div style="padding: 30px 24px 20px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 16px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h4 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Hapus Mata Pelajaran?</h4>
            <p style="font-size: 13.5px; color: #64748b; line-height: 1.5;" id="deleteMapelPrompt"></p>
        </div>
        <form id="formDeleteMapel" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-ftr" style="justify-content: center; gap: 12px; padding: 18px 24px;">
                <button type="button" class="btn-cancel-modal" onclick="closeDeleteMapelModal()" style="flex:1;">Batal</button>
                <button type="submit" class="btn-submit-modal" style="background:#ef4444;flex:1;">Hapus Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL IMPORT MAPEL ================= -->
<div class="custom-modal-backdrop" id="modalImportMapel">
    <div class="custom-modal-box">
        <div class="modal-hdr">
            <h3><i class="fa-solid fa-file-csv"></i> Import Mata Pelajaran dari CSV</h3>
            <button type="button" class="modal-hdr-close" onclick="closeImportModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('waka-kurikulum.mapel.import-csv') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-bdy">
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;margin-bottom:18px;font-size:12.5px;color:#475569;line-height:1.5;">
                    <div style="font-weight:800;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                        <i class="fa-solid fa-circle-info" style="color:#3b82f6;"></i> Petunjuk Format Kolom:
                    </div>
                    Gunakan format kolom: <b>kode_mapel, nama_mapel, kelompok</b>.<br>
                    Kelompok: <i>Normatif, Adaptif, Produktif, Muatan_Lokal</i>.
                    <div style="margin-top:10px;">
                        <a href="{{ route('waka-kurikulum.import.template', 'mapel') }}" class="btn-import-csv" style="font-size:11.5px;padding:6px 12px;text-decoration:none;">
                            <i class="fa-solid fa-download"></i> Unduh Template CSV Mapel
                        </a>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label>Pilih File CSV (.csv / .txt) <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="file_csv" accept=".csv, .txt, text/csv" class="form-control-custom" required style="padding:8px 10px;">
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-cancel-modal" onclick="closeImportModal()">Batal</button>
                <button type="submit" class="btn-submit-modal">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Unggah &amp; Proses Impor
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Import Modal
    function openImportModal() {
        document.getElementById('modalImportMapel').classList.add('open');
    }
    function closeImportModal() {
        document.getElementById('modalImportMapel').classList.remove('open');
    }

    // Tambah Modal
    function openTambahMapelModal() {
        document.getElementById('modalTambahMapel').classList.add('open');
    }
    function closeTambahMapelModal() {
        document.getElementById('modalTambahMapel').classList.remove('open');
    }

    // Edit Modal
    function openEditMapelModal(mapel) {
        document.getElementById('edit_kode_mapel').value = mapel.kode_mapel;
        document.getElementById('edit_nama_mapel').value = mapel.nama_mapel;
        document.getElementById('edit_kelompok').value   = mapel.kelompok;

        document.getElementById('formEditMapel').action = `/waka-kurikulum/mapel/${mapel.id_mapel}`;
        document.getElementById('modalEditMapel').classList.add('open');
    }
    function closeEditMapelModal() {
        document.getElementById('modalEditMapel').classList.remove('open');
    }

    // Delete Modal
    function openDeleteMapelModal(id, nama) {
        document.getElementById('deleteMapelPrompt').innerText = `Apakah Anda yakin ingin menghapus mata pelajaran "${nama}" dari kurikulum? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('formDeleteMapel').action = `/waka-kurikulum/mapel/${id}`;
        document.getElementById('modalDeleteMapel').classList.add('open');
    }
    function closeDeleteMapelModal() {
        document.getElementById('modalDeleteMapel').classList.remove('open');
    }

    // Detail Pengampu Modal via AJAX
    function openDetailPengampu(idMapel) {
        const modal = document.getElementById('modalDetailPengampu');
        const content = document.getElementById('modalDetailContent');
        const title = document.getElementById('modalDetailTitle');

        modal.classList.add('open');
        content.innerHTML = `
            <div style="text-align:center; padding: 30px; color:#94a3b8;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size:24px;"></i>
                <p style="margin-top:8px;">Memuat rincian guru pengajar...</p>
            </div>
        `;

        fetch(`/waka-kurikulum/mapel/${idMapel}/detail`)
            .then(res => res.json())
            .then(data => {
                title.innerHTML = `<i class="fa-solid fa-chalkboard-user"></i> Pengajar: ${data.mapel.nama_mapel} (${data.mapel.kode_mapel})`;

                if (!data.pengajar || data.pengajar.length === 0) {
                    content.innerHTML = `
                        <div style="text-align:center; padding: 40px 20px; color:#94a3b8;">
                            <i class="fa-solid fa-user-slash" style="font-size:36px; margin-bottom:10px; display:block; opacity:0.5;"></i>
                            <p style="font-weight:600;">Belum ada guru yang dijadwalkan mengampu mata pelajaran ini.</p>
                        </div>
                    `;
                    return;
                }

                let html = `
                    <div style="margin-bottom: 14px; font-size: 12.5px; color: #64748b; font-weight: 600;">
                        Total ${data.total_guru} guru pengampu dengan total ${data.total_jp} jam pelajaran (JP) terjadwal:
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                `;

                data.pengajar.forEach(guru => {
                    html += `
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-weight:700; color:#0f172a; font-size:14px;">${guru.nama_lengkap}</div>
                                <div style="font-size:12px; color:#64748b; margin-top:2px;">NIP: ${guru.nip}</div>
                                <div style="margin-top:6px; display:flex; gap:4px; flex-wrap:wrap;">
                                    ${guru.kelas.map(k => `<span style="background:#e0f2fe; color:#0284c7; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:700;">${k}</span>`).join('')}
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:16px; font-weight:800; color:#0284c7;">${guru.total_jp} JP</div>
                                <div style="font-size:11px; color:#94a3b8;">per minggu</div>
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
                content.innerHTML = html;
            })
            .catch(err => {
                content.innerHTML = `
                    <div style="text-align:center; padding: 30px; color:#ef4444;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size:24px;"></i>
                        <p style="margin-top:8px;">Gagal memuat data pengampu.</p>
                    </div>
                `;
            });
    }
    function closeDetailPengampu() {
        document.getElementById('modalDetailPengampu').classList.remove('open');
    }
</script>
@endsection
