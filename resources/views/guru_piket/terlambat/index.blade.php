@extends('layouts.guru_piket')

@section('title', 'Izin Siswa Terlambat - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Izin Masuk Kelas Siswa Terlambat')
@section('header_subtitle', 'Pencatatan siswa terlambat, penerbitan surat izin masuk kelas, dan pemantauan status persetujuan Waka Piket')

@section('header_extra')
    <button type="button" class="btn-action-primary" onclick="openTerlambatModal()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; box-shadow: none; font-weight: 700; padding: 10px 18px; border-radius: 12px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease;">
        <i class="fa-solid fa-user-clock"></i>
        <span>Catat Siswa Terlambat</span>
    </button>
@endsection

@section('styles')
<style>
    /* Metric Cards */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .metric-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
    .metric-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .metric-val {
        font-size: 22px;
        font-weight: 800;
        color: #1b2559;
        line-height: 1.1;
    }
    .metric-lbl {
        font-size: 12px;
        font-weight: 600;
        color: #707e94;
        margin-top: 4px;
    }

    /* Filter Card */
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
    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .filter-select, .filter-input {
        padding: 9px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        font-family: inherit;
        font-weight: 600;
        color: #1b2559;
        outline: none;
        background: #ffffff;
    }
    .filter-select:focus, .filter-input:focus { border-color: #2b43b9; }

    /* Section Card & Table */
    .section-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid #eef2f7;
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

    /* Status Badges */
    .status-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-badge.menunggu {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }
    .status-badge.disetujui {
        background: #e6f9f0;
        color: #10b981;
        border: 1px solid #bbf7d0;
    }
    .status-badge.ditolak {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }
    .status-badge.dibatalkan {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    /* Action Buttons */
    .action-btn-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .btn-action {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .btn-action.print { background: #2b43b9; color: #ffffff; }
    .btn-action.print:hover { background: #1e3a8a; color: #ffffff; }
    .btn-action.detail { background: #f1f5f9; color: #334155; }
    .btn-action.detail:hover { background: #e2e8f0; }
    .btn-action.edit { background: #fef3c7; color: #b45309; }
    .btn-action.edit:hover { background: #fde68a; }
    .btn-action.delete { background: #fee2e2; color: #b91c1c; }
    .btn-action.delete:hover { background: #fca5a5; }
    .btn-action.cancel-admin { background: #dc2626; color: #ffffff; }
    .btn-action.cancel-admin:hover { background: #b91c1c; }

    /* Quick Chips */
    .chip-item {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-block;
        margin-right: 6px;
        margin-bottom: 6px;
    }
    .chip-item:hover {
        background: #2b43b9;
        color: #ffffff;
        border-color: #2b43b9;
    }

    /* Modal Styling */
    .custom-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .custom-modal-card {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 620px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalScaleIn 0.2s ease-out;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    @keyframes modalScaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .modal-head {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
    }
    .modal-head h3 {
        font-size: 16px;
        font-weight: 800;
        color: #1b2559;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-close-btn {
        background: none;
        border: none;
        font-size: 20px;
        color: #94a3b8;
        cursor: pointer;
        line-height: 1;
        padding: 4px;
    }
    .modal-close-btn:hover { color: #0f172a; }
    .modal-body {
        padding: 22px 24px;
        overflow-y: auto;
    }
    .modal-foot {
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        background: #fafbfc;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13.5px;
        font-family: inherit;
        font-weight: 600;
        color: #1b2559;
        outline: none;
        box-sizing: border-box;
    }
    .form-control:focus { border-color: #2b43b9; }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .btn-submit {
        background: #2b43b9;
        color: #ffffff;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-submit:hover { background: #1e3a8a; }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 18px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
    }
    .btn-cancel:hover { background: #e2e8f0; }

    /* Student Search Item */
    .terlambat-siswa-item:hover {
        background-color: #f1f5f9 !important;
    }
    .terlambat-siswa-item:hover .siswa-name {
        color: #2b43b9 !important;
    }
</style>
@endsection

@section('content')
<div class="content-container">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div style="background: #e6f9f0; border: 1.5px solid #86efac; border-radius: 14px; padding: 14px 18px; color: #166534; font-weight: 600; font-size: 13.5px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 14px; padding: 14px 18px; color: #991b1b; font-weight: 600; font-size: 13.5px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-xmark" style="font-size: 18px; color: #ef4444;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 14px; padding: 14px 18px; color: #991b1b; font-weight: 600; font-size: 13px; margin-bottom: 20px;">
            <div style="font-weight: 800; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> Terdapat kendala pengisian:</div>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Metric Grid --}}
    <div class="metric-grid">
        <div class="metric-card">
            <div class="metric-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="metric-val">{{ $metrics['total'] }}</div>
                <div class="metric-lbl">Total Siswa Terlambat</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon-wrap" style="background: #fff7ed; color: #ea580c;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <div class="metric-val">{{ $metrics['menunggu'] }}</div>
                <div class="metric-lbl">Menunggu Konfirmasi Waka</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-file-circle-check"></i>
            </div>
            <div>
                <div class="metric-val">{{ $metrics['disetujui'] }}</div>
                <div class="metric-lbl">Disetujui Masuk Kelas</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                <i class="fa-solid fa-file-circle-xmark"></i>
            </div>
            <div>
                <div class="metric-val">{{ $metrics['ditolak'] }}</div>
                <div class="metric-lbl">Ditolak / Dibatalkan</div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <form action="{{ route('guru-piket.terlambat.index') }}" method="GET" class="filter-card">
        <div class="filter-group">
            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Tanggal</span>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="filter-input" onchange="this.form.submit()">
            </div>

            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Status</span>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="all" {{ $status == 'all' || !$status ? 'selected' : '' }}>Semua Status</option>
                    <option value="Menunggu" {{ $status == 'Menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="Disetujui" {{ $status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Dibatalkan" {{ $status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Kelas</span>
                <select name="id_kelas" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id_kelas }}" {{ $kelasId == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filter-group">
            <div>
                <span style="font-size: 11px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Cari Siswa</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NISN..." class="filter-input">
            </div>
            <div style="align-self: flex-end;">
                <button type="submit" class="btn-action print" style="padding: 10px 16px;">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                @if($search || $kelasId || ($status && $status !== 'all') || $tanggal !== now()->format('Y-m-d'))
                    <a href="{{ route('guru-piket.terlambat.index') }}" class="btn-action detail" style="padding: 10px 14px;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Main Table Section --}}
    <div class="section-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #1b2559; margin: 0;">
                    Daftar Siswa Terlambat — {{ $todayFormatted }}
                </h3>
                <p style="font-size: 12.5px; color: #707e94; margin: 3px 0 0 0;">
                    Menampilkan total {{ $terlambatList->total() }} catatan keterlambatan
                </p>
            </div>
            <button type="button" class="btn-action print" onclick="openTerlambatModal()">
                <i class="fa-solid fa-plus"></i> Catat Siswa Terlambat
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Siswa & Kelas</th>
                        <th>Waktu Datang</th>
                        <th>Izin Masuk</th>
                        <th>Alasan Keterlambatan</th>
                        <th>No. Surat Resmi</th>
                        <th>Status</th>
                        <th>Petugas</th>
                        <th style="text-align: right; min-width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terlambatList as $idx => $t)
                        <tr>
                            <td>{{ $terlambatList->firstItem() + $idx }}</td>
                            <td>
                                <strong style="color: #1b2559; font-size: 13.5px;">{{ $t->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                                <div style="font-size: 11.5px; color: #6b7a99; margin-top: 2px;">
                                    <span style="background: #eef2ff; color: #2b43b9; padding: 1px 7px; border-radius: 4px; font-weight: 700; font-size: 11px;">
                                        {{ $t->kelas->nama_kelas ?? ($t->siswa->kelas->nama_kelas ?? '-') }}
                                    </span>
                                    <span style="margin-left: 6px;">NISN: {{ $t->siswa->nisn ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 800; color: #1e293b; font-size: 13px;">
                                    <i class="fa-regular fa-clock" style="color: #64748b; margin-right: 4px;"></i>
                                    {{ \Carbon\Carbon::parse($t->jam_masuk)->format('H:i') }} WIB
                                </div>
                            </td>
                            <td>
                                <span style="background: #f1f5f9; color: #1e293b; font-weight: 800; padding: 3px 8px; border-radius: 6px; font-size: 12px; border: 1px solid #e2e8f0;">
                                    Jam Ke-{{ $t->jam_ke_mulai }}
                                </span>
                            </td>
                            <td>
                                <div style="max-width: 220px; font-size: 12.5px; color: #334155; line-height: 1.3;" title="{{ $t->alasan }}">
                                    {{ Str::limit($t->alasan, 45) }}
                                </div>
                            </td>
                            <td>
                                @if($t->nomor_surat)
                                    <code style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 7px; font-size: 11px; font-weight: 700; color: #0f172a;">
                                        {{ $t->nomor_surat }}
                                    </code>
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-size: 12px;">Belum terbit</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status === 'Menunggu')
                                    <span class="status-badge menunggu">
                                        <i class="fa-solid fa-spinner fa-spin"></i> Menunggu
                                    </span>
                                @elseif($t->status === 'Disetujui')
                                    <span class="status-badge disetujui">
                                        <i class="fa-solid fa-circle-check"></i> Disetujui
                                    </span>
                                @elseif($t->status === 'Ditolak')
                                    <span class="status-badge ditolak">
                                        <i class="fa-solid fa-circle-xmark"></i> Ditolak
                                    </span>
                                @elseif($t->status === 'Dibatalkan')
                                    <span class="status-badge dibatalkan">
                                        <i class="fa-solid fa-ban"></i> Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 11.5px; color: #64748b;">
                                    <div><i class="fa-solid fa-user-pen" style="width: 14px;"></i> {{ Str::limit($t->diinputOlehUser->name ?? '-', 14) }}</div>
                                    @if($t->dikonfirmasiOlehUser)
                                        <div style="color: #0284c7; margin-top: 2px;">
                                            <i class="fa-solid fa-user-shield" style="width: 14px;"></i> {{ Str::limit($t->dikonfirmasiOlehUser->name ?? '-', 14) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btn-wrap" style="justify-content: flex-end;">
                                    {{-- Detail Modal Trigger --}}
                                    <button type="button" class="btn-action detail" onclick="showDetailModal({{ json_encode($t) }}, '{{ $t->siswa->nama_lengkap ?? '' }}', '{{ $t->kelas->nama_kelas ?? '' }}', '{{ $t->siswa->nisn ?? '' }}', '{{ $t->diinputOlehUser->name ?? '' }}', '{{ $t->dikonfirmasiOlehUser->name ?? '' }}')" title="Lihat Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    {{-- Cetak Slip bila Disetujui --}}
                                    @if($t->status === 'Disetujui')
                                        <a href="{{ route('guru-piket.terlambat.cetak', $t->id) }}" target="_blank" class="btn-action print" title="Cetak Surat Izin">
                                            <i class="fa-solid fa-print"></i> Cetak
                                        </a>

                                        {{-- Admin Pembatalan Button --}}
                                        @if(Auth::user()->role === 'admin')
                                            <button type="button" class="btn-action cancel-admin" onclick="openAdminCancelModal({{ $t->id }}, '{{ $t->siswa->nama_lengkap ?? '' }}', '{{ $t->nomor_surat ?? '' }}')" title="Batalkan Izin (Khusus Administrator)">
                                                <i class="fa-solid fa-ban"></i> Batal
                                            </button>
                                        @endif
                                    @endif

                                    {{-- Edit & Hapus bila masih Menunggu --}}
                                    @if($t->status === 'Menunggu')
                                        <button type="button" class="btn-action edit" onclick="openEditModal({{ json_encode($t) }}, '{{ $t->siswa->nama_lengkap ?? '' }}', '{{ $t->kelas->nama_kelas ?? '' }}', '{{ $t->siswa->nisn ?? '' }}')" title="Edit Pengajuan">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form action="{{ route('guru-piket.terlambat.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan siswa {{ $t->siswa->nama_lengkap }}?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action delete" title="Hapus Pengajuan">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fa-solid fa-user-check" style="font-size: 38px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                <span style="font-size: 14px; font-weight: 700; color: #64748b;">Tidak ada data siswa terlambat</span>
                                <p style="font-size: 12px; margin-top: 4px;">Semua siswa hadir tepat waktu atau belum ada data yang diinput untuk filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $terlambatList->links() }}
        </div>
    </div>
</div>

{{-- MODAL TAMBAH: Catat Siswa Terlambat --}}
<div class="custom-modal-backdrop" id="terlambatModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <h3><i class="fa-solid fa-user-clock" style="color: #2b43b9;"></i> Catat Izin Siswa Terlambat</h3>
            <button type="button" class="modal-close-btn" onclick="closeTerlambatModal()">&times;</button>
        </div>

        <form action="{{ route('guru-piket.terlambat.store') }}" method="POST" onsubmit="return validateTerlambatForm(this);">
            @csrf
            <div class="modal-body">
                {{-- Pemilih Siswa Interaktif --}}
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Pilih Siswa <span style="color:#ef4444;">*</span></span>
                        <span id="terlambatSiswaCount" style="font-size: 11px; font-weight: 600; color: #64748b;">
                            Menampilkan {{ count($siswaList) }} siswa
                        </span>
                    </label>

                    <input type="hidden" name="id_siswa" id="terlambatIdSiswa" required>

                    {{-- Selected Preview Card --}}
                    <div id="terlambatSelectedCard" style="display: none; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 10px 14px; margin-bottom: 8px; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <div id="terlambatSelectedName" style="font-weight: 800; color: #166534; font-size: 13.5px;"></div>
                                <div id="terlambatSelectedMeta" style="font-size: 11.5px; color: #15803d; margin-top: 1px;"></div>
                            </div>
                        </div>
                        <button type="button" onclick="changeSiswaTerlambat()" style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 8px; padding: 5px 10px; font-size: 11.5px; font-weight: 700; color: #dc2626; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <i class="fa-solid fa-rotate-left"></i> Ganti
                        </button>
                    </div>

                    {{-- Search & Filter Section --}}
                    <div id="terlambatSearchSection">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                            <div style="position: relative;">
                                <select id="terlambatFilterTingkat" class="form-control" onchange="filterSiswaTerlambat()" style="font-size: 12.5px; padding: 8px 12px; border-color: #cbd5e1;">
                                    <option value="">Semua Tingkat</option>
                                    <option value="X">Tingkat X</option>
                                    <option value="XI">Tingkat XI</option>
                                    <option value="XII">Tingkat XII</option>
                                </select>
                            </div>
                            <div style="position: relative;">
                                <select id="terlambatFilterKelas" class="form-control" onchange="filterSiswaTerlambat()" style="font-size: 12.5px; padding: 8px 12px; border-color: #cbd5e1;">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k->id_kelas }}" data-tingkat="{{ $k->tingkat }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div style="position: relative; margin-bottom: 8px;">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none;"></i>
                            <input type="text" id="terlambatSearchInput" class="form-control" placeholder="Cari nama atau NISN siswa..." style="padding-left: 38px; border-color: #cbd5e1;" oninput="filterSiswaTerlambat()">
                        </div>

                        <div id="terlambatSiswaListWrapper" style="max-height: 160px; overflow-y: auto; border: 1.5px solid #e2e8f0; border-radius: 12px; background: #ffffff; padding: 4px;">
                            <div id="terlambatSiswaEmpty" style="display: none; padding: 16px; text-align: center; color: #94a3b8; font-size: 12px;">
                                Tidak ada siswa yang sesuai filter.
                            </div>
                            <div id="terlambatSiswaOptions"></div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Masuk <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Datang Sebenarnya <span style="color:#ef4444;">*</span></label>
                        <input type="time" name="jam_masuk" value="{{ now()->format('H:i') }}" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mulai Masuk Jam Pelajaran Ke- <span style="color:#ef4444;">*</span></label>
                    <select name="jam_ke_mulai" class="form-control" required>
                        <option value="">-- Pilih Jam Pelajaran Ke --</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>
                                Jam Ke-{{ $i }}
                            </option>
                        @endfor
                    </select>
                    <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">
                        * Jam pelajaran ke- saat siswa diizinkan mulai mengikuti kegiatan belajar di kelas.
                    </small>
                </div>

                <div class="form-group">
                    <label>Alasan Keterlambatan <span style="color:#ef4444;">*</span> (Minimal 5 karakter)</label>
                    <textarea name="alasan" id="terlambatAlasanInput" rows="3" class="form-control" placeholder="Tuliskan alasan keterlambatan siswa..." required minlength="5"></textarea>
                    
                    {{-- Quick Reason Chips --}}
                    <div style="margin-top: 8px;">
                        <span style="font-size: 11px; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px;">Pilihan Cepat Alasan:</span>
                        <div class="chip-item" onclick="setQuickAlasan('Ban bocor di perjalanan')">Ban Bocor</div>
                        <div class="chip-item" onclick="setQuickAlasan('Macet total di jalan raya')">Macet Jalan</div>
                        <div class="chip-item" onclick="setQuickAlasan('Ketinggalan angkutan umum')">Ketinggalan Angkot</div>
                        <div class="chip-item" onclick="setQuickAlasan('Hujan lebat di jalan')">Hujan Lebat</div>
                        <div class="chip-item" onclick="setQuickAlasan('Rantai sepeda / motor putus')">Kendaraan Rusak</div>
                        <div class="chip-item" onclick="setQuickAlasan('Mengantar keluarga berobat darurat')">Keluarga Sakit</div>
                        <div class="chip-item" onclick="setQuickAlasan('Bangun kesiangan karena sakit semalam')">Bangun Kesiangan</div>
                    </div>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="btn-cancel" onclick="closeTerlambatModal()">Batal</button>
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan Izin
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT: Perbarui Data Izin Terlambat --}}
<div class="custom-modal-backdrop" id="editModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <h3><i class="fa-solid fa-pen-to-square" style="color: #d97706;"></i> Perbarui Pengajuan Izin Terlambat</h3>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()">&times;</button>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_siswa" id="editIdSiswa">

            <div class="modal-body">
                <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Siswa Terpilih</div>
                    <div id="editNamaSiswa" style="font-size: 14px; font-weight: 800; color: #1e293b; margin-top: 2px;"></div>
                    <div id="editMetaSiswa" style="font-size: 12px; color: #64748b;"></div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Masuk <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="tanggal" id="editTanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Datang Sebenarnya <span style="color:#ef4444;">*</span></label>
                        <input type="time" name="jam_masuk" id="editJamMasuk" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mulai Masuk Jam Pelajaran Ke- <span style="color:#ef4444;">*</span></label>
                    <select name="jam_ke_mulai" id="editJamKeMulai" class="form-control" required>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">Jam Ke-{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label>Alasan Keterlambatan <span style="color:#ef4444;">*</span></label>
                    <textarea name="alasan" id="editAlasan" rows="3" class="form-control" required minlength="5"></textarea>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit" style="background: #d97706;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETAIL: Detail Lengkap Izin Terlambat --}}
<div class="custom-modal-backdrop" id="detailModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <h3><i class="fa-solid fa-circle-info" style="color: #2b43b9;"></i> Rincian Surat Izin Masuk Kelas</h3>
            <button type="button" class="modal-close-btn" onclick="closeDetailModal()">&times;</button>
        </div>

        <div class="modal-body" id="detailModalContent">
            {{-- Content rendered dynamically via JS --}}
        </div>

        <div class="modal-foot">
            <button type="button" class="btn-cancel" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL PEMBATALAN ADMIN --}}
@if(Auth::user()->role === 'admin')
<div class="custom-modal-backdrop" id="adminCancelModal">
    <div class="custom-modal-card" style="max-width: 500px;">
        <div class="modal-head" style="background: #fef2f2; border-bottom-color: #fee2e2;">
            <h3 style="color: #b91c1c;"><i class="fa-solid fa-triangle-exclamation"></i> Pembatalan Izin Resmi (Admin)</h3>
            <button type="button" class="modal-close-btn" onclick="closeAdminCancelModal()">&times;</button>
        </div>

        <form id="adminCancelForm" method="POST">
            @csrf
            <input type="hidden" name="action" value="batalkan">

            <div class="modal-body">
                <p style="font-size: 13px; color: #475569; line-height: 1.4; margin-top: 0;">
                    Anda akan membatalkan surat izin masuk terlambat untuk:
                </p>
                <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 10px; padding: 10px 14px; margin-bottom: 14px;">
                    <div id="adminCancelNama" style="font-weight: 800; color: #9f1239; font-size: 14px;"></div>
                    <div id="adminCancelNoSurat" style="font-size: 12px; color: #be123c; margin-top: 2px;"></div>
                </div>

                <div class="form-group">
                    <label>Alasan Pembatalan Resmi <span style="color:#ef4444;">*</span> (Wajib minimal 5 karakter)</label>
                    <textarea name="catatan" id="adminCancelCatatan" rows="3" class="form-control" placeholder="Tuliskan alasan pembatalan surat izin ini secara jelas..." required minlength="5"></textarea>
                    <small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">
                        Tindakan pembatalan ini akan dicatat secara permanen di buku log aktivitas sistem.
                    </small>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="btn-cancel" onclick="closeAdminCancelModal()">Batal</button>
                <button type="submit" class="btn-submit" style="background: #dc2626;">
                    <i class="fa-solid fa-ban"></i> Konfirmasi Pembatalan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    // Data Siswa Mentah dari Controller
    const allSiswaTerlambat = [
        @foreach($siswaList as $s)
        {
            id:   {{ $s->id_siswa }},
            ik:   "{{ $s->id_kelas }}",
            t:    "{{ $s->kelas->tingkat ?? '' }}",
            k:    "{{ addslashes($s->kelas->nama_kelas ?? '') }}",
            name: "{{ addslashes($s->nama_lengkap) }}",
            n:    "{{ $s->nisn ?? '-' }}",
            s:    "{{ strtolower(addslashes($s->nama_lengkap)) }} {{ strtolower($s->nisn ?? '') }}"
        },
        @endforeach
    ];

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function openTerlambatModal() {
        document.getElementById('terlambatModal').style.display = 'flex';
        resetAllTerlambatFilters();
    }

    function closeTerlambatModal() {
        document.getElementById('terlambatModal').style.display = 'none';
    }

    function setQuickAlasan(text) {
        document.getElementById('terlambatAlasanInput').value = text;
    }

    function filterSiswaTerlambat() {
        const searchInput   = document.getElementById('terlambatSearchInput');
        const filterTingkat = document.getElementById('terlambatFilterTingkat')?.value || '';
        const kelasSelect   = document.getElementById('terlambatFilterKelas');
        const container     = document.getElementById('terlambatSiswaOptions');
        const emptyMsg      = document.getElementById('terlambatSiswaEmpty');
        const counter       = document.getElementById('terlambatSiswaCount');

        const term = searchInput ? searchInput.value.trim().toLowerCase() : '';

        // Cascade Tingkat -> Kelas
        if (kelasSelect && filterTingkat) {
            Array.from(kelasSelect.options).forEach(opt => {
                if (!opt.value) return;
                const optTingkat = opt.getAttribute('data-tingkat');
                opt.style.display = (!optTingkat || optTingkat === filterTingkat) ? '' : 'none';
            });
            const selOpt = kelasSelect.options[kelasSelect.selectedIndex];
            if (selOpt && selOpt.value && selOpt.getAttribute('data-tingkat') !== filterTingkat) {
                kelasSelect.value = '';
            }
        } else if (kelasSelect && !filterTingkat) {
            Array.from(kelasSelect.options).forEach(opt => opt.style.display = '');
        }

        const activeKelas = document.getElementById('terlambatFilterKelas')?.value || '';

        let matchCount = 0;
        const matches = [];

        for (let i = 0; i < allSiswaTerlambat.length; i++) {
            const item = allSiswaTerlambat[i];
            const matchText    = !term         || item.s.includes(term);
            const matchTingkat = !filterTingkat || item.t === filterTingkat;
            const matchKelas   = !activeKelas   || item.ik === activeKelas;

            if (matchText && matchTingkat && matchKelas) {
                matchCount++;
                if (matches.length < 50) {
                    matches.push(item);
                }
            }
        }

        if (emptyMsg) emptyMsg.style.display = matchCount === 0 ? 'block' : 'none';
        if (counter) counter.innerText = `Ditemukan ${matchCount} siswa`;

        if (container) {
            container.innerHTML = matches.map(s => `
                <div class="terlambat-siswa-item" 
                     data-id="${s.id}" 
                     data-name="${escapeHtml(s.name)}" 
                     data-kelas="${escapeHtml(s.k)}" 
                     data-nisn="${escapeHtml(s.n)}" 
                     onclick="selectSiswaTerlambat(this)"
                     style="padding: 9px 12px; border-radius: 8px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.15s ease; border-bottom: 1px solid #f8fafc;">
                    <div>
                        <div class="siswa-name" style="font-weight: 700; font-size: 13px; color: #1b2559;">${escapeHtml(s.name)}</div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                            <span style="background: #eef2ff; color: #2b43b9; padding: 1px 7px; border-radius: 4px; font-weight: 700; font-size: 10.5px;">${escapeHtml(s.k)}</span>
                            <span style="margin-left: 6px;">NISN: ${escapeHtml(s.n)}</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size: 11px; color: #cbd5e1;"></i>
                </div>
            `).join('');
        }
    }

    function selectSiswaTerlambat(el) {
        const id    = el.getAttribute('data-id');
        const name  = el.getAttribute('data-name');
        const kelas = el.getAttribute('data-kelas');
        const nisn  = el.getAttribute('data-nisn');

        document.getElementById('terlambatIdSiswa').value = id;
        document.getElementById('terlambatSelectedName').innerText = name;
        document.getElementById('terlambatSelectedMeta').innerText = `${kelas} • NISN: ${nisn}`;

        document.getElementById('terlambatSelectedCard').style.display = 'flex';
        document.getElementById('terlambatSearchSection').style.display = 'none';
    }

    function changeSiswaTerlambat() {
        document.getElementById('terlambatIdSiswa').value = '';
        document.getElementById('terlambatSelectedCard').style.display = 'none';
        document.getElementById('terlambatSearchSection').style.display = 'block';
        resetAllTerlambatFilters();
    }

    function resetAllTerlambatFilters() {
        const searchInput = document.getElementById('terlambatSearchInput');
        const filterTingkat = document.getElementById('terlambatFilterTingkat');
        const filterKelas = document.getElementById('terlambatFilterKelas');
        if (searchInput) searchInput.value = '';
        if (filterTingkat) filterTingkat.value = '';
        if (filterKelas) {
            filterKelas.value = '';
            Array.from(filterKelas.options).forEach(o => o.style.display = '');
        }
        filterSiswaTerlambat();
    }

    function validateTerlambatForm(form) {
        const idSiswa = document.getElementById('terlambatIdSiswa').value;
        if (!idSiswa) {
            alert('Silakan pilih siswa yang terlambat terlebih dahulu!');
            return false;
        }
        const alasan = form.alasan.value.trim();
        if (alasan.length < 5) {
            alert('Alasan keterlambatan minimal 5 karakter!');
            return false;
        }
        return true;
    }

    // Modal Edit
    function openEditModal(record, nama, kelas, nisn) {
        const form = document.getElementById('editForm');
        form.action = `/guru-piket/terlambat/${record.id}`;

        document.getElementById('editIdSiswa').value = record.id_siswa;
        document.getElementById('editNamaSiswa').innerText = nama;
        document.getElementById('editMetaSiswa').innerText = `${kelas} • NISN: ${nisn}`;
        document.getElementById('editTanggal').value = record.tanggal.split('T')[0];
        document.getElementById('editJamMasuk').value = record.jam_masuk.substring(0, 5);
        document.getElementById('editJamKeMulai').value = record.jam_ke_mulai;
        document.getElementById('editAlasan').value = record.alasan;

        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Modal Detail
    function showDetailModal(record, nama, kelas, nisn, guruPiket, wakaPiket) {
        const content = document.getElementById('detailModalContent');
        const statusBadge = record.status === 'Disetujui'
            ? '<span class="status-badge disetujui"><i class="fa-solid fa-circle-check"></i> Disetujui</span>'
            : (record.status === 'Menunggu'
                ? '<span class="status-badge menunggu"><i class="fa-solid fa-spinner fa-spin"></i> Menunggu</span>'
                : (record.status === 'Ditolak'
                    ? '<span class="status-badge ditolak"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>'
                    : '<span class="status-badge dibatalkan"><i class="fa-solid fa-ban"></i> Dibatalkan</span>'));

        content.innerHTML = `
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-weight: 800; font-size: 15px; color: #1e293b;">${escapeHtml(nama)}</div>
                    ${statusBadge}
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                    ${escapeHtml(kelas)} • NISN: ${escapeHtml(nisn)}
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div style="background: #ffffff; border: 1px solid #f1f5f9; padding: 10px 12px; border-radius: 8px;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Jam Kedatangan</div>
                    <div style="font-size: 13.5px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                        ${record.jam_masuk.substring(0, 5)} WIB
                    </div>
                </div>
                <div style="background: #ffffff; border: 1px solid #f1f5f9; padding: 10px 12px; border-radius: 8px;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Izin Masuk Jam Ke</div>
                    <div style="font-size: 13.5px; font-weight: 800; color: #2563eb; margin-top: 2px;">
                        Jam Ke-${record.jam_ke_mulai}
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Nomor Surat Resmi</div>
                <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-top: 2px;">
                    ${record.nomor_surat ? `<code>${record.nomor_surat}</code>` : '<em style="color:#94a3b8;">Belum diterbitkan</em>'}
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Alasan Keterlambatan</div>
                <div style="font-size: 13px; color: #334155; margin-top: 3px; background: #fafbfc; border: 1px solid #f1f5f9; padding: 10px; border-radius: 8px; line-height: 1.4;">
                    ${escapeHtml(record.alasan)}
                </div>
            </div>

            ${record.catatan_konfirmasi ? `
                <div style="margin-bottom: 14px;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Catatan Konfirmasi / Pembatalan</div>
                    <div style="font-size: 12.5px; color: #0f172a; margin-top: 3px; background: #fff7ed; border: 1px solid #fed7aa; padding: 10px; border-radius: 8px;">
                        ${escapeHtml(record.catatan_konfirmasi)}
                    </div>
                </div>
            ` : ''}

            <div style="border-top: 1px solid #f1f5f9; padding-top: 10px; font-size: 11.5px; color: #64748b;">
                <div><i class="fa-solid fa-user-pen" style="width: 14px;"></i> Dicatat oleh: <strong>${escapeHtml(guruPiket || '-')}</strong></div>
                ${wakaPiket ? `<div style="margin-top: 3px;"><i class="fa-solid fa-user-shield" style="width: 14px;"></i> Dikonfirmasi oleh: <strong>${escapeHtml(wakaPiket)}</strong></div>` : ''}
            </div>
        `;

        document.getElementById('detailModal').style.display = 'flex';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    // Modal Pembatalan Admin
    function openAdminCancelModal(id, nama, noSurat) {
        const form = document.getElementById('adminCancelForm');
        form.action = `/guru-piket/terlambat/${id}/konfirmasi`;
        document.getElementById('adminCancelNama').innerText = nama;
        document.getElementById('adminCancelNoSurat').innerText = `Nomor Surat: ${noSurat || '-'}`;
        document.getElementById('adminCancelCatatan').value = '';
        document.getElementById('adminCancelModal').style.display = 'flex';
    }

    function closeAdminCancelModal() {
        const modal = document.getElementById('adminCancelModal');
        if (modal) modal.style.display = 'none';
    }

    // Close on backdrop click
    window.onclick = function(event) {
        if (event.target.classList.contains('custom-modal-backdrop')) {
            event.target.style.display = 'none';
        }
    };
</script>
@endsection
