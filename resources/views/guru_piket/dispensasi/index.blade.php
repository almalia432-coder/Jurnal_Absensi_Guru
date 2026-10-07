@extends('layouts.guru_piket')

@section('title', 'Dispensasi Siswa - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Manajemen Dispensasi Siswa')
@section('header_subtitle', 'Kelola izin keluar siswa, terbitkan surat dispensasi, dan pantau status kepulangan')

@section('header_extra')
    <button type="button" class="btn-action-primary" onclick="openDispensasiModal()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; box-shadow: none;">
        <i class="fa-solid fa-plus"></i>
        <span>Terbitkan Dispensasi Baru</span>
    </button>
@endsection

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
    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .filter-select, .filter-input {
        padding: 8px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        font-family: inherit;
        font-weight: 600;
        color: #1b2559;
        outline: none;
    }
    .filter-select:focus, .filter-input:focus { border-color: #2b43b9; }

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

    .status-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-badge.success { background: #e6f9f0; color: #10b981; }
    .status-badge.warning { background: #fff7ed; color: #f97316; }
    .status-badge.info    { background: #e0f2fe; color: #0369a1; }
    .status-badge.danger  { background: #fef2f2; color: #ef4444; }

    .action-btn-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-action {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }
    .btn-action.approve { background: #10b981; color: white; }
    .btn-action.return  { background: #0ea5e9; color: white; }
    .btn-action.print   { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .btn-action.print:hover { background: #e2e8f0; }

    .btn-action-primary {
        background: #2b43b9;
        color: white;
        padding: 9px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-action-secondary {
        background: #f1f5f9;
        color: #334155;
        padding: 9px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
    }

    /* Modal Styling */
    .custom-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px 16px;
        overflow-y: auto;
    }
    .custom-modal-backdrop.show { display: flex; }
    .custom-modal-card {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 560px;
        max-height: calc(100vh - 40px);
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        margin: auto;
    }
    .custom-modal-card form {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
    .modal-head {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        flex-shrink: 0;
    }
    .modal-head h3 { font-size: 16px; font-weight: 800; color: #1b2559; display: flex; align-items: center; gap: 8px; }
    .modal-close-btn { background: none; border: none; font-size: 22px; color: #94a3b8; cursor: pointer; padding: 2px 6px; line-height: 1; border-radius: 6px; transition: all 0.15s ease; }
    .modal-close-btn:hover { color: #1e293b; background: #e2e8f0; }
    .modal-body {
        padding: 20px 24px;
        overflow-y: auto;
        flex: 1;
    }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 12.5px; font-weight: 700; color: #2b3674; margin-bottom: 6px; }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        font-size: 13.5px;
        font-family: inherit;
        color: #1b2559;
        outline: none;
    }
    .form-control:focus { border-color: #2b43b9; }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .dispensasi-siswa-item:hover {
        background-color: #f1f5f9 !important;
    }
    .dispensasi-siswa-item:hover .siswa-name {
        color: #2b43b9 !important;
    }
    .dispensasi-siswa-item:hover i.fa-chevron-right {
        color: #2b43b9 !important;
        transform: translateX(3px);
    }

    @media (max-width: 768px) {
        .filter-card { flex-direction: column; align-items: stretch; padding: 16px; }
        .filter-group { flex-direction: column; align-items: stretch; }
        .filter-select, .filter-input { width: 100%; }
        .form-row { grid-template-columns: 1fr; }
        .section-card { padding: 16px; border-radius: 14px; }
        .custom-modal-card { width: 100%; max-width: 100%; margin: 8px auto; max-height: calc(100vh - 20px); }
    }
    .modal-footer {
        padding: 14px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<!-- Filter Bar -->
<div class="filter-card">
    <form method="GET" action="{{ route('guru-piket.dispensasi') }}" class="filter-group">
        <div>
            <span style="font-size: 11.5px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Tanggal</span>
            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="filter-input">
        </div>

        <div>
            <span style="font-size: 11.5px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Status</span>
            <select name="status" onchange="this.form.submit()" class="filter-select">
                <option value="">Semua Status</option>
                <option value="Menunggu" {{ $status === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="Disetujui" {{ $status === 'Disetujui' ? 'selected' : '' }}>Disetujui / Keluar</option>
                <option value="Selesai" {{ $status === 'Selesai' ? 'selected' : '' }}>Selesai / Kembali</option>
                <option value="Ditolak" {{ $status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div>
            <span style="font-size: 11.5px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Cari Siswa</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NISN..." class="filter-input">
        </div>

        <div style="align-self: flex-end;">
            <button type="submit" class="btn-action-primary" style="padding: 8px 14px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
        </div>
    </form>

    <div>
        <button type="button" class="btn-action-primary" onclick="openDispensasiModal()">
            <i class="fa-solid fa-plus"></i> + Tambah Dispensasi
        </button>
    </div>
</div>

<!-- Table Card -->
<div class="section-card">
    <div class="table-responsive-wrap">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa & Kelas</th>
                    <th>Waktu Izin</th>
                    <th>Alasan / Keperluan</th>
                    <th>Status</th>
                    <th>Diterbitkan Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dispensasiList as $idx => $d)
                <tr>
                    <td>{{ $dispensasiList->firstItem() + $idx }}</td>
                    <td>
                        <strong style="color: #1b2559; font-size: 13.5px;">{{ $d->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                        <div style="font-size: 11.5px; color: #6b7a99;">
                            {{ $d->siswa->kelas->nama_kelas ?? '-' }} &bull; NISN: {{ $d->siswa->nisn ?? '-' }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #1b2559;">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="color: #ef4444; font-size: 11px;"></i> {{ Carbon\Carbon::parse($d->jam_keluar)->format('H:i') }}
                        </div>
                        <div style="font-size: 11.5px; color: #6b7a99;">
                            @if($d->jam_kembali)
                                <i class="fa-solid fa-arrow-right-to-bracket" style="color: #10b981; font-size: 11px;"></i> Kembali: {{ Carbon\Carbon::parse($d->jam_kembali)->format('H:i') }}
                            @else
                                <span style="color: #f97316;">Belum Kembali</span>
                            @endif
                        </div>
                    </td>
                    <td style="max-width: 200px;">
                        <div>{{ $d->alasan }}</div>
                        @if($d->bukti_file)
                            <a href="{{ Storage::url($d->bukti_file) }}" target="_blank" style="font-size: 11px; color: #2b43b9; font-weight: 700;">
                                <i class="fa-solid fa-paperclip"></i> Lihat Lampiran
                            </a>
                        @endif
                    </td>
                    <td>
                        @if($d->status === 'Menunggu')
                            <span class="status-badge warning">Menunggu</span>
                        @elseif($d->status === 'Disetujui' && !$d->jam_kembali)
                            <span class="status-badge info">Sedang Keluar</span>
                        @elseif($d->status === 'Selesai' || ($d->status === 'Disetujui' && $d->jam_kembali))
                            <span class="status-badge success">Kembali / Selesai</span>
                        @else
                            <span class="status-badge danger">{{ $d->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 600; font-size: 12px; color: #2b3674;">{{ $d->diinputOlehUser->name ?? '-' }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $d->created_at->format('H:i, d M') }}</div>
                    </td>
                    <td>
                        <div class="action-btn-wrap">
                            @if($d->status === 'Menunggu')
                                <form action="{{ route('guru-piket.dispensasi.status', $d->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="setujui">
                                    <button type="submit" class="btn-action approve" title="Setujui Izin">
                                        <i class="fa-solid fa-check"></i> Setujui
                                    </button>
                                </form>
                            @elseif($d->status === 'Disetujui' && !$d->jam_kembali)
                                <form action="{{ route('guru-piket.dispensasi.status', $d->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="kembali">
                                    <button type="submit" class="btn-action return" title="Konfirmasi Siswa Kembali">
                                        <i class="fa-solid fa-rotate-left"></i> Kembali
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('guru-piket.dispensasi.cetak', $d->id) }}" target="_blank" class="btn-action print" title="Cetak Surat Izin Satpam">
                                <i class="fa-solid fa-print"></i> Cetak Slip
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7a99; padding: 36px;">
                        <i class="fa-solid fa-ticket-simple" style="font-size: 32px; margin-bottom: 8px;"></i>
                        <div>Belum ada data dispensasi pada tanggal atau filter ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $dispensasiList->links() }}
    </div>
</div>

<!-- Modal Quick Dispensasi Siswa -->
<div class="custom-modal-backdrop" id="dispensasiModal">
    <div class="custom-modal-card">
        <div class="modal-head">
            <h3><i class="fa-solid fa-ticket-simple" style="color: #2b43b9;"></i> Terbitkan Surat Dispensasi Siswa</h3>
            <button type="button" class="modal-close-btn" onclick="closeDispensasiModal()">&times;</button>
        </div>
        <form action="{{ route('guru-piket.dispensasi.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateDispensasiForm(this);">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Pilih Siswa <span style="color:#ef4444;">*</span></span>
                        <span id="dispensasiSiswaCount" style="font-size: 11px; font-weight: 600; color: #64748b;">
                            Menampilkan {{ count($siswaSelectOption) }} siswa
                        </span>
                    </label>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="id_siswa" id="dispensasiIdSiswa" required>

                    <!-- Selected Student Preview Card -->
                    <div id="dispensasiSelectedCard" style="display: none; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 10px 14px; margin-bottom: 8px; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <div id="dispensasiSelectedName" style="font-weight: 800; color: #166534; font-size: 13px;"></div>
                                <div id="dispensasiSelectedMeta" style="font-size: 11.5px; color: #15803d; margin-top: 1px;"></div>
                            </div>
                        </div>
                        <button type="button" onclick="changeSiswaDispensasi()" style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 8px; padding: 5px 10px; font-size: 11.5px; font-weight: 700; color: #dc2626; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <i class="fa-solid fa-rotate-left"></i> Ganti
                        </button>
                    </div>

                    <!-- Search & Results Section -->
                    <div id="dispensasiSearchSection">

                        <!-- Filter Baris: Tingkat & Kelas -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                            <div style="position: relative;">
                                <select id="dispensasiFilterTingkat" class="form-control" onchange="filterSiswaDispensasi()" style="font-size: 12.5px; padding: 8px 30px 8px 12px; border-color: #cbd5e1; appearance: none; -webkit-appearance: none; cursor: pointer; background: #ffffff;">
                                    <option value="">Semua Tingkat</option>
                                    <option value="X">Tingkat X</option>
                                    <option value="XI">Tingkat XI</option>
                                    <option value="XII">Tingkat XII</option>
                                </select>
                                <i class="fa-solid fa-chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; font-size: 11px;"></i>
                            </div>
                            <div style="position: relative;">
                                <select id="dispensasiFilterKelas" class="form-control" onchange="filterSiswaDispensasi()" style="font-size: 12.5px; padding: 8px 30px 8px 12px; border-color: #cbd5e1; appearance: none; -webkit-appearance: none; cursor: pointer; background: #ffffff;">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k->id_kelas }}" data-tingkat="{{ $k->tingkat }}">{{ $k->nama_kelas }}{{ $k->jurusan ? ' ('.$k->jurusan.')' : '' }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; font-size: 11px;"></i>
                            </div>
                        </div>

                        <!-- Search Text -->
                        <div style="position: relative; margin-bottom: 8px;">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none;"></i>
                            <input type="text" id="dispensasiSearchInput" class="form-control" placeholder="Cari nama atau NISN..." style="padding-left: 38px; padding-right: 36px; border-color: #cbd5e1;" oninput="filterSiswaDispensasi()">
                            <button type="button" id="dispensasiClearSearchBtn" onclick="resetAllDispensasiFilters()" title="Reset semua filter" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; display: none; padding: 4px;">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Scrollable Student List -->
                        <div id="dispensasiSiswaListWrapper" style="max-height: 160px; overflow-y: auto; border: 1.5px solid #e2e8f0; border-radius: 12px; background: #ffffff; padding: 4px;">
                            <div id="dispensasiSiswaEmpty" style="display: none; padding: 18px; text-align: center; color: #94a3b8; font-size: 12.5px;">
                                <i class="fa-solid fa-user-slash" style="font-size: 20px; margin-bottom: 6px; display: block; color: #cbd5e1;"></i>
                                Tidak ada siswa yang cocok dengan filter.
                            </div>
                            <div id="dispensasiSiswaOptions">
                                <!-- Rendered dynamically via JavaScript from allSiswaDispensasi -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tanggal Dispensasi <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jam Keluar <span style="color:#ef4444;">*</span></label>
                        <input type="time" name="jam_keluar" value="{{ now()->format('H:i') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Perkiraan Jam Kembali</label>
                        <input type="time" name="jam_kembali" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Alasan / Keperluan Keluar <span style="color:#ef4444;">*</span></label>
                    <textarea name="alasan" rows="3" class="form-control" placeholder="Contoh: Mengikuti perwakilan lomba / Izin ke Puskesmas..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Bukti Surat / Foto (Opsional)</label>
                    <input type="file" name="bukti_file" class="form-control" accept="image/*,application/pdf">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action-secondary" onclick="closeDispensasiModal()">Batal</button>
                <button type="submit" class="btn-action-primary">Terbitkan Surat</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const allSiswaDispensasi = {!! json_encode($siswaSelectOption->map(fn($s) => [
        'id'   => $s->id_siswa,
        'name' => $s->nama_lengkap,
        'k'    => $s->kelas->nama_kelas ?? '-',
        'ik'   => (string)$s->id_kelas,
        't'    => (string)($s->kelas->tingkat ?? ''),
        'n'    => (string)($s->nisn ?? ''),
        'j'    => strtolower($s->kelas->jurusan ?? ''),
        's'    => strtolower($s->nama_lengkap . ' ' . ($s->kelas->nama_kelas ?? '') . ' ' . ($s->nisn ?? ''))
    ])) !!};

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function openDispensasiModal() {
        document.getElementById('dispensasiModal').classList.add('show');
        filterSiswaDispensasi();
        setTimeout(() => {
            const searchInput = document.getElementById('dispensasiSearchInput');
            if (searchInput && document.getElementById('dispensasiSearchSection').style.display !== 'none') {
                searchInput.focus();
            }
        }, 150);
    }

    function closeDispensasiModal() {
        document.getElementById('dispensasiModal').classList.remove('show');
    }

    function filterSiswaDispensasi() {
        const term = (document.getElementById('dispensasiSearchInput')?.value || '').toLowerCase().trim();
        const filterTingkat = document.getElementById('dispensasiFilterTingkat')?.value || '';
        const filterKelas = document.getElementById('dispensasiFilterKelas')?.value || '';

        const container = document.getElementById('dispensasiSiswaOptions');
        const emptyMsg = document.getElementById('dispensasiSiswaEmpty');
        const counter = document.getElementById('dispensasiSiswaCount');
        const clearBtn = document.getElementById('dispensasiClearSearchBtn');

        const hasAnyFilter = term || filterTingkat || filterKelas;
        if (clearBtn) clearBtn.style.display = hasAnyFilter ? 'block' : 'none';

        // When tingkat changes, cascade-filter the kelas dropdown
        const kelasSelect = document.getElementById('dispensasiFilterKelas');
        if (kelasSelect && filterTingkat) {
            Array.from(kelasSelect.options).forEach(opt => {
                if (!opt.value) return; // skip "Semua Kelas"
                opt.style.display = (opt.getAttribute('data-tingkat') === filterTingkat) ? '' : 'none';
            });
            // Reset kelas filter if selected kelas doesn't match chosen tingkat
            if (kelasSelect.value) {
                const selOpt = kelasSelect.options[kelasSelect.selectedIndex];
                if (selOpt && selOpt.getAttribute('data-tingkat') !== filterTingkat) {
                    kelasSelect.value = '';
                }
            }
        } else if (kelasSelect && !filterTingkat) {
            Array.from(kelasSelect.options).forEach(opt => opt.style.display = '');
        }

        const activeKelas = document.getElementById('dispensasiFilterKelas')?.value || '';

        let matchCount = 0;
        const matches = [];

        for (let i = 0; i < allSiswaDispensasi.length; i++) {
            const item = allSiswaDispensasi[i];
            const matchText   = !term        || item.s.includes(term);
            const matchTingkat = !filterTingkat || item.t === filterTingkat;
            const matchKelas  = !activeKelas  || item.ik === activeKelas;

            if (matchText && matchTingkat && matchKelas) {
                matchCount++;
                if (matches.length < 50) {
                    matches.push(item);
                }
            }
        }

        if (emptyMsg) emptyMsg.style.display = matchCount === 0 ? 'block' : 'none';

        if (counter) {
            counter.innerText = hasAnyFilter
                ? `Ditemukan ${matchCount} siswa`
                : `Menampilkan ${matchCount} siswa`;
        }

        if (container) {
            container.innerHTML = matches.map(s => `
                <div class="dispensasi-siswa-item" 
                     data-id="${s.id}" 
                     data-name="${escapeHtml(s.name)}" 
                     data-kelas="${escapeHtml(s.k)}" 
                     data-nisn="${escapeHtml(s.n)}" 
                     data-jurusan="${escapeHtml(s.j)}" 
                     onclick="selectSiswaDispensasi(this)"
                     style="padding: 9px 12px; border-radius: 8px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.15s ease; border-bottom: 1px solid #f8fafc;">
                    <div>
                        <div class="siswa-name" style="font-weight: 700; font-size: 13px; color: #1b2559;">${escapeHtml(s.name)}</div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                            <span style="background: #eef2ff; color: #2b43b9; padding: 1px 7px; border-radius: 4px; font-weight: 700; font-size: 10.5px;">${escapeHtml(s.k)}</span>
                            ${s.j ? `<span style="background: #fff7ed; color: #c2410c; padding: 1px 7px; border-radius: 4px; font-weight: 700; font-size: 10px; margin-left: 4px;">${escapeHtml(s.j.toUpperCase())}</span>` : ''}
                            <span style="margin-left: 6px;">NISN: ${escapeHtml(s.n)}</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size: 11px; color: #cbd5e1; transition: all 0.15s ease;"></i>
                </div>
            `).join('');
        }
    }

    function selectSiswaDispensasi(el) {
        const id      = el.getAttribute('data-id');
        const name    = el.getAttribute('data-name');
        const kelas   = el.getAttribute('data-kelas');
        const nisn    = el.getAttribute('data-nisn');
        const jurusan = el.getAttribute('data-jurusan') || '';

        document.getElementById('dispensasiIdSiswa').value = id;
        document.getElementById('dispensasiSelectedName').innerText = name;
        document.getElementById('dispensasiSelectedMeta').innerText =
            `${kelas}${jurusan ? ' • ' + jurusan.toUpperCase() : ''} • NISN: ${nisn}`;

        document.getElementById('dispensasiSelectedCard').style.display = 'flex';
        document.getElementById('dispensasiSearchSection').style.display = 'none';
    }

    function changeSiswaDispensasi() {
        document.getElementById('dispensasiIdSiswa').value = '';
        document.getElementById('dispensasiSelectedCard').style.display = 'none';
        document.getElementById('dispensasiSearchSection').style.display = 'block';
        resetAllDispensasiFilters();
    }

    function resetAllDispensasiFilters() {
        const searchInput = document.getElementById('dispensasiSearchInput');
        const filterTingkat = document.getElementById('dispensasiFilterTingkat');
        const filterKelas = document.getElementById('dispensasiFilterKelas');
        if (searchInput) searchInput.value = '';
        if (filterTingkat) filterTingkat.value = '';
        if (filterKelas) {
            filterKelas.value = '';
            Array.from(filterKelas.options).forEach(o => o.style.display = '');
        }
        filterSiswaDispensasi();
        if (searchInput) searchInput.focus();
    }

    function validateDispensasiForm(form) {
        const idSiswa = document.getElementById('dispensasiIdSiswa').value;
        if (!idSiswa) {
            alert('Silakan cari dan pilih siswa terlebih dahulu dari daftar pencarian!');
            const searchInput = document.getElementById('dispensasiSearchInput');
            if (searchInput) {
                searchInput.focus();
            }
            return false;
        }
        return true;
    }
</script>
@endsection
