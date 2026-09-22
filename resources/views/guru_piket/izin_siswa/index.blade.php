@extends('layouts.guru_piket')

@section('title', 'Perizinan & Sakit Siswa - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Perizinan & Sakit Siswa')
@section('header_subtitle', 'Pencatatan siswa sakit, izin keperluan keluarga, atau dispensasi oleh Guru Piket yang otomatis terhubung ke jurnal guru mapel')

@section('header_extra')
    <button type="button" class="btn-action-primary" onclick="openIzinModal()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; box-shadow: none;">
        <i class="fa-solid fa-plus"></i>
        <span>Catat Izin Siswa Baru</span>
    </button>
@endsection

@section('styles')
<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px 22px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease;
    }
    .kpi-card:hover { transform: translateY(-2px); }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .kpi-icon.total { background: #eef2ff; color: #2b43b9; }
    .kpi-icon.sakit { background: #e0f2fe; color: #0284c7; }
    .kpi-icon.izin  { background: #fff7ed; color: #ea580c; }
    .kpi-icon.disp  { background: #fdf4ff; color: #a855f7; }

    .kpi-info h4 {
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        color: #707e94;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .kpi-info .num {
        font-size: 24px;
        font-weight: 800;
        color: #1b2559;
        line-height: 1;
    }

    .info-banner-card {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 1.5px solid #c7d2fe;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }
    .info-banner-icon {
        background: #4318ff;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

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
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-badge.sakit { background: #e0f2fe; color: #0284c7; }
    .status-badge.izin  { background: #fff7ed; color: #ea580c; }
    .status-badge.disp  { background: #fdf4ff; color: #a855f7; }

    .btn-action-delete {
        background: #fee2e2;
        color: #ef4444;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-action-delete:hover {
        background: #ef4444;
        color: white;
    }

    /* Modal Styling */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 580px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalSlide 0.25s ease-out;
    }
    @keyframes modalSlide {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        padding: 20px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-header h3 {
        font-size: 17px;
        font-weight: 800;
        color: #1b2559;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-body {
        padding: 24px;
        max-height: 80vh;
        overflow-y: auto;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #1b2559;
        margin-bottom: 6px;
    }
    .form-group label span.req { color: #ef4444; }
    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        font-size: 13.5px;
        font-family: inherit;
        color: #1b2559;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-control-custom:focus { border-color: #2b43b9; }

    .modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-cancel {
        padding: 10px 18px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        background: white;
        color: #64748b;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
    }
    .btn-save {
        padding: 10px 22px;
        border-radius: 10px;
        border: none;
        background: #2b43b9;
        color: white;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(43, 67, 185, 0.25);
    }
    .btn-save:hover { background: #1e35a0; }

    @media (max-width: 768px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')
<div class="content-container">

    {{-- Notifikasi Info Alur Kerja Otomatis --}}
    <div class="info-banner-card">
        <div class="info-banner-icon">
            <i class="fa-solid fa-bolt-lightning"></i>
        </div>
        <div style="flex: 1;">
            <strong style="color: #1b2559; font-size: 14px;">Integrasi Otomatis dengan Jurnal Mengajar Guru Mapel</strong>
            <p style="font-size: 12.5px; color: #4b5563; margin-top: 4px; line-height: 1.5;">
                Data perizinan (Sakit, Izin, Dispensasi) yang Anda catat di sini akan <strong>langsung terisi otomatis</strong> pada presensi form jurnal mengajar yang dibuka oleh guru mapel di kelas hari ini. Guru mapel cukup mengecek fisik kelas dan menandai siswa yang <strong>Alpha</strong> jika ada.
            </p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon total">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="kpi-info">
                <h4>Total Izin Siswa</h4>
                <div class="num">{{ $kpi['total'] }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon sakit">
                <i class="fa-solid fa-notes-medical"></i>
            </div>
            <div class="kpi-info">
                <h4>Sakit (S)</h4>
                <div class="num">{{ $kpi['sakit'] }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon izin">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <div class="kpi-info">
                <h4>Izin (I)</h4>
                <div class="num">{{ $kpi['izin'] }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon disp">
                <i class="fa-solid fa-ticket-simple"></i>
            </div>
            <div class="kpi-info">
                <h4>Dispensasi (D)</h4>
                <div class="num">{{ $kpi['dispensasi'] }}</div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('guru-piket.izin-siswa') }}" class="filter-group">
            <div>
                <span style="font-size: 11.5px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Tanggal</span>
                <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="filter-input">
            </div>

            <div>
                <span style="font-size: 11.5px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Kelas</span>
                <select name="id_kelas" onchange="this.form.submit()" class="filter-select">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id_kelas }}" {{ (string)$idKelas === (string)$k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <span style="font-size: 11.5px; font-weight: 800; color: #707e94; text-transform: uppercase; display: block; margin-bottom: 4px;">Jenis Izin</span>
                <select name="jenis_izin" onchange="this.form.submit()" class="filter-select">
                    <option value="">Semua Jenis</option>
                    <option value="Sakit" {{ $jenis === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Izin" {{ $jenis === 'Izin' ? 'selected' : '' }}>Izin (Acara / Keluarga)</option>
                    <option value="Dispensasi" {{ $jenis === 'Dispensasi' ? 'selected' : '' }}>Dispensasi</option>
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
            <button type="button" class="btn-action-primary" onclick="openIzinModal()">
                <i class="fa-solid fa-plus"></i> + Catat Izin Baru
            </button>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="section-card">
        <div class="table-responsive-wrap">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa & Rombel</th>
                        <th>Jenis Izin</th>
                        <th>Periode Tanggal</th>
                        <th>Alasan / Keterangan</th>
                        <th>Dicatat Oleh</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($izinSiswaList as $idx => $item)
                    <tr>
                        <td>{{ $izinSiswaList->firstItem() + $idx }}</td>
                        <td>
                            <strong style="color: #1b2559; font-size: 13.5px;">{{ $item->siswa->nama_lengkap ?? 'Siswa' }}</strong>
                            <div style="font-size: 11.5px; color: #6b7a99;">
                                {{ $item->siswa->kelas->nama_kelas ?? '-' }} &bull; NISN: {{ $item->siswa->nisn ?? '-' }}
                            </div>
                        </td>
                        <td>
                            @if($item->jenis_izin === 'Sakit')
                                <span class="status-badge sakit"><i class="fa-solid fa-notes-medical"></i> Sakit</span>
                            @elseif($item->jenis_izin === 'Izin')
                                <span class="status-badge izin"><i class="fa-solid fa-envelope-open-text"></i> Izin</span>
                            @else
                                <span class="status-badge disp"><i class="fa-solid fa-ticket-simple"></i> Dispensasi</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #1b2559; font-size: 12.5px;">
                                {{ Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                                @if($item->tanggal_mulai != $item->tanggal_selesai)
                                    <span style="font-weight: normal; color: #707e94;">s/d</span> {{ Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y') }}
                                @endif
                            </div>
                        </td>
                        <td style="max-width: 250px;">
                            <div style="font-size: 13px; color: #2b3674;">{{ $item->alasan }}</div>
                            @if($item->bukti_file)
                                <a href="{{ Storage::url($item->bukti_file) }}" target="_blank" style="font-size: 11px; color: #2b43b9; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin-top: 3px;">
                                    <i class="fa-solid fa-paperclip"></i> Lihat Lampiran Surat
                                </a>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 12px; color: #2b3674;">{{ $item->diinputOlehUser->name ?? '-' }}</div>
                            <div style="font-size: 11px; color: #94a3b8;">{{ $item->created_at->format('H:i, d M Y') }}</div>
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('guru-piket.izin-siswa.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus catatan perizinan siswa ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action-delete" title="Hapus Catatan Izin">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #6b7a99; padding: 36px;">
                            <i class="fa-solid fa-hospital-user" style="font-size: 32px; margin-bottom: 8px; color: #cbd5e1;"></i>
                            <div>Belum ada data perizinan siswa pada tanggal atau filter yang dipilih.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $izinSiswaList->links() }}
        </div>
    </div>

</div>

{{-- Modal Catat Izin Siswa Baru --}}
<div class="modal-overlay" id="modalIzinSiswa">
    <div class="modal-box">
        <div class="modal-header">
            <h3>
                <i class="fa-solid fa-notes-medical" style="color: #2b43b9;"></i>
                Catat Izin / Sakit Siswa
            </h3>
            <button type="button" onclick="closeIzinModal()" style="background: none; border: none; font-size: 18px; color: #64748b; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('guru-piket.izin-siswa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                {{-- Filter Kelas Cepat untuk Mempermudah Memilih Siswa --}}
                <div class="form-group">
                    <label>Pilih Kelas Siswa <span class="req">*</span></label>
                    <select id="modalSelectKelas" class="form-control-custom" onchange="filterSiswaByKelas(this.value)">
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }} ({{ $k->tingkat }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Siswa --}}
                <div class="form-group">
                    <label>Nama Siswa <span class="req">*</span></label>
                    <select name="id_siswa" id="modalSelectSiswa" required class="form-control-custom">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswaSelectOption as $s)
                            <option value="{{ $s->id_siswa }}" data-kelas="{{ $s->id_kelas }}">
                                {{ $s->nama_lengkap }} ({{ $s->kelas->nama_kelas ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis Izin --}}
                <div class="form-group">
                    <label>Jenis Perizinan <span class="req">*</span></label>
                    <select name="jenis_izin" required class="form-control-custom">
                        <option value="Sakit">🏥 Sakit</option>
                        <option value="Izin">✉️ Izin (Acara / Kepentingan Keluarga)</option>
                        <option value="Dispensasi">⚡ Dispensasi (Tugas / Lomba)</option>
                    </select>
                </div>

                {{-- Rentang Tanggal --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;" class="form-group">
                    <div>
                        <label>Tanggal Mulai <span class="req">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ $tanggal }}" required class="form-control-custom">
                    </div>
                    <div>
                        <label>Tanggal Selesai <span class="req">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ $tanggal }}" required class="form-control-custom">
                    </div>
                </div>

                {{-- Alasan / Keterangan --}}
                <div class="form-group">
                    <label>Alasan / Keterangan Lengkap <span class="req">*</span></label>
                    <textarea name="alasan" rows="3" required placeholder="Contoh: Demam tinggi disertai surat dokter, atau izin acara keluarga di luar kota..." class="form-control-custom"></textarea>
                </div>

                {{-- Upload Bukti / Surat (Opsional) --}}
                <div class="form-group">
                    <label>Lampiran Surat / Foto Bukti (Opsional)</label>
                    <input type="file" name="bukti_file" accept=".jpg,.jpeg,.png,.pdf" class="form-control-custom">
                    <small style="color: #707e94; font-size: 11px;">Maksimal 2MB (JPG, PNG, PDF)</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeIzinModal()">Batal</button>
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan & Hubungkan ke Jurnal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openIzinModal() {
        document.getElementById('modalIzinSiswa').classList.add('active');
    }

    function closeIzinModal() {
        document.getElementById('modalIzinSiswa').classList.remove('active');
    }

    // Filter dropdown siswa berdasarkan kelas yang dipilih di modal
    function filterSiswaByKelas(idKelas) {
        const selectSiswa = document.getElementById('modalSelectSiswa');
        const options = selectSiswa.querySelectorAll('option');

        selectSiswa.value = "";

        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = 'block';
                return;
            }
            if (!idKelas || opt.getAttribute('data-kelas') === idKelas) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    // Tutup modal jika klik di luar box
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalIzinSiswa');
        if (e.target === modal) {
            closeIzinModal();
        }
    });
</script>
@endsection
