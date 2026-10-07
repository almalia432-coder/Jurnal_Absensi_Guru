@extends('layouts.guru_piket')

@section('title', 'Persetujuan Dispensasi Siswa — Waka Piket SMKN 1 Boyolangu')
@section('header_title', 'Persetujuan Dispensasi Siswa')
@section('header_subtitle', 'Tinjau dan konfirmasi permohonan izin dispensasi siswa KBM hari ini')

@section('styles')
<style>
    .banner-waka-duty {
        background: linear-gradient(135deg, #1e3a8a 0%, #2b43b9 50%, #3b82f6 100%);
        border-radius: 18px;
        padding: 22px 26px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 8px 24px rgba(43, 67, 185, 0.2);
        flex-wrap: wrap;
        gap: 16px;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .metric-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .metric-icon.warning { background: #fff7ed; color: #f97316; }
    .metric-icon.success { background: #e6f9f0; color: #10b981; }
    .metric-icon.danger  { background: #fef2f2; color: #ef4444; }
    .metric-icon.blue    { background: #eef2ff; color: #2b43b9; }

    .metric-info h4 {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .metric-info .metric-num {
        font-size: 24px;
        font-weight: 800;
        color: #1b2559;
        line-height: 1.1;
    }

    .card-filter {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px 22px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .dispen-table-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .dispen-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
        text-align: left;
    }
    .dispen-table th {
        background: #f8fafc;
        padding: 14px 18px;
        font-weight: 800;
        font-size: 11.5px;
        text-transform: uppercase;
        color: #475569;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .dispen-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        vertical-align: middle;
    }
    .dispen-table tr:hover td { background: #fbfcfe; }

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
    .status-badge.warning { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
    .status-badge.success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .status-badge.danger  { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .status-badge.info    { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }

    .btn-approve {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
    }
    .btn-approve:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); }

    .btn-reject {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-reject:hover { background: #dc2626; color: #ffffff; }

    .btn-print-slip {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-print-slip:hover { background: #e2e8f0; color: #0f172a; }

    /* Modal Styling */
    .modal-backdrop-custom {
        position: fixed; inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none; align-items: center; justify-content: center;
        padding: 20px;
    }
    .modal-backdrop-custom.show { display: flex; }
    .modal-card-custom {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 480px;
        padding: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    @media (max-width: 900px) {
        .metrics-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 500px) {
        .metrics-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

{{-- Banner Penugasan Waka Piket --}}
<div class="banner-waka-duty">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div style="width: 54px; height: 54px; border-radius: 16px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 26px;">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <div>
            <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #bfdbfe;">
                Piket Pimpinan KBM Harian
            </span>
            <h2 style="font-size: 18px; font-weight: 800; margin: 3px 0; color: #ffffff;">
                {{ $wakaPiketInfo['nama'] ?? 'Waka Piket SMKN 1 Boyolangu' }}
            </h2>
            <div style="font-size: 12.5px; color: #e0e7ff;">
                NIP: {{ $wakaPiketInfo['nip'] ?? '-' }} &bull; Tanggal: <strong>{{ $todayFormatted }}</strong>
            </div>
        </div>
    </div>
    <div>
        @if($isWakaPiketToday)
            <span style="background: rgba(16, 185, 129, 0.25); border: 1.5px solid #34d399; color: #ffffff; font-size: 12.5px; font-weight: 800; padding: 8px 16px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-check" style="color: #34d399;"></i> Anda Bertugas Hari Ini
            </span>
        @else
            <span style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255,255,255,0.3); color: #ffffff; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px;">
                <i class="fa-solid fa-shield-halved"></i> Akses Wewenang Pimpinan
            </span>
        @endif
    </div>
</div>

@if(!$isWakaPiketToday && Auth::user()->email !== 'waka.piket@smkn1boyolangu.sch.id' && Auth::user()->role !== 'admin')
<div style="background: #fffbeb; border: 1.5px solid #fef3c7; border-left: 5px solid #f59e0b; border-radius: 16px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.08);">
    <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <div style="font-weight: 800; font-size: 15px; color: #92400e;">Mode Pantau (Read-Only) — Anda Tidak Bertugas Sebagai Waka Piket Hari Ini</div>
            <div style="font-size: 13px; color: #b45309; margin-top: 2px;">
                Penanggung jawab piket KBM hari ini ({{ $todayFormatted }}) adalah: <strong>{{ $wakaPiketInfo['nama'] ?? '-' }}</strong>. Hak persetujuan dan penolakan hanya dapat dilakukan oleh Waka Piket bertugas atau melalui akun dinas meja piket.
            </div>
        </div>
    </div>
    @php
        $backRoute = route('guru-mapel.dashboard');
        if (Auth::user()->isWakaKurikulum()) {
            $backRoute = route('waka-kurikulum.dashboard');
        } elseif (Auth::user()->isWakaSdm()) {
            $backRoute = route('waka-sdm.dashboard');
        } elseif (Auth::user()->isWakaKesiswaan()) {
            $backRoute = route('waka-kesiswaan.dashboard');
        }
    @endphp
    <a href="{{ $backRoute }}" style="background: #d97706; color: #ffffff; padding: 10px 20px; border-radius: 12px; font-weight: 800; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Utama
    </a>
</div>
@endif

{{-- Metrics Summary --}}
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon warning">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div class="metric-info">
            <h4>Menunggu Persetujuan</h4>
            <div class="metric-num" style="color: #ea580c;">{{ $metrics['menunggu'] }}</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon success">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="metric-info">
            <h4>Disetujui Hari Ini</h4>
            <div class="metric-num" style="color: #059669;">{{ $metrics['disetujui'] }}</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon danger">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <div class="metric-info">
            <h4>Ditolak</h4>
            <div class="metric-num" style="color: #dc2626;">{{ $metrics['ditolak'] }}</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon blue">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="metric-info">
            <h4>Total Permohonan</h4>
            <div class="metric-num">{{ $metrics['total'] }}</div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card-filter">
    <form method="GET" action="{{ route('waka-piket.dispensasi') }}" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <div>
            <label style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 4px;">Pilih Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" style="padding: 7px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 600; font-size: 13px;" onchange="this.form.submit()">
        </div>

        <div>
            <label style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 4px;">Status Permohonan</label>
            <select name="status" class="form-control" style="padding: 7px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 600; font-size: 13px;" onchange="this.form.submit()">
                <option value="Menunggu" {{ $status === 'Menunggu' ? 'selected' : '' }}>⏳ Menunggu Persetujuan</option>
                <option value="Disetujui" {{ $status === 'Disetujui' ? 'selected' : '' }}>✓ Sudah Disetujui</option>
                <option value="Ditolak" {{ $status === 'Ditolak' ? 'selected' : '' }}>✕ Ditolak</option>
                <option value="Semua" {{ $status === 'Semua' ? 'selected' : '' }}>Semua Status</option>
            </select>
        </div>

        <div>
            <label style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 4px;">Cari Siswa</label>
            <div style="display: flex; gap: 6px;">
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama / NISN siswa..." class="form-control" style="padding: 7px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 13px;">
                <button type="submit" class="btn" style="background: #2b43b9; color: white; border-radius: 10px; padding: 7px 14px; font-size: 13px; font-weight: 700;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>

        @if($tanggal !== Carbon\Carbon::today()->format('Y-m-d') || $status !== 'Menunggu' || $search)
            <div style="align-self: flex-end;">
                <a href="{{ route('waka-piket.dispensasi') }}" style="color: #64748b; font-size: 12.5px; font-weight: 700; text-decoration: none; padding: 8px 12px;">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            </div>
        @endif
    </form>
</div>

{{-- Table Permohonan Dispensasi --}}
<div class="dispen-table-card">
    <div style="padding: 16px 20px; border-bottom: 1px solid #eef2f7; display: flex; align-items: center; justify-content: space-between;">
        <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-list-check" style="color: #2b43b9;"></i>
            <span>Daftar Permohonan Dispensasi ({{ $dispensasiList->total() }})</span>
        </h3>
        <span style="font-size: 12px; color: #64748b;">
            Menampilkan tanggal: <strong>{{ $todayFormatted }}</strong>
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table class="dispen-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Siswa &amp; Kelas</th>
                    <th>Waktu Izin</th>
                    <th>Keperluan / Alasan</th>
                    <th>Bukti Surat</th>
                    <th>Status &amp; Persetujuan</th>
                    <th style="width: 170px; text-align: center;">Aksi Waka Piket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dispensasiList as $idx => $item)
                <tr>
                    <td style="font-weight: 700; color: #64748b;">
                        {{ $dispensasiList->firstItem() + $idx }}
                    </td>
                    <td>
                        <strong style="color: #0f172a; font-size: 14px; display: block;">
                            {{ $item->siswa->nama_lengkap ?? '-' }}
                        </strong>
                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                            {{ $item->siswa->kelas->nama_kelas ?? '-' }} &bull; NISN: {{ $item->siswa->nisn ?? '-' }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #2b43b9;">
                            <i class="fa-regular fa-clock"></i> {{ Carbon\Carbon::parse($item->jam_keluar)->format('H:i') }} WIB
                        </div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                            s.d {{ $item->jam_kembali ? Carbon\Carbon::parse($item->jam_kembali)->format('H:i').' WIB' : 'Selesai KBM' }}
                        </div>
                    </td>
                    <td>
                        <div style="max-width: 260px; line-height: 1.4; color: #334155;">
                            {{ $item->alasan }}
                        </div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                            Petugas input: {{ $item->diinputOlehUser->name ?? 'Meja Piket' }}
                        </div>
                    </td>
                    <td>
                        @if($item->bukti_file)
                            <a href="{{ Storage::url($item->bukti_file) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 700; color: #2b43b9; background: #eef2ff; padding: 4px 10px; border-radius: 6px; text-decoration: none;">
                                <i class="fa-solid fa-paperclip"></i> Lihat Berkas
                            </a>
                        @else
                            <span style="font-size: 12px; color: #94a3b8;">- Tidak Ada -</span>
                        @endif
                    </td>
                    <td>
                        @if($item->status === 'Menunggu')
                            <span class="status-badge warning"><i class="fa-solid fa-hourglass-half"></i> Menunggu Waka</span>
                        @elseif($item->status === 'Disetujui')
                            <span class="status-badge success"><i class="fa-solid fa-circle-check"></i> Disetujui</span>
                            <div style="font-size: 11px; color: #059669; margin-top: 3px;">
                                oleh: {{ $item->disetujuiOlehUser->name ?? 'Waka Piket' }}
                            </div>
                        @elseif($item->status === 'Ditolak')
                            <span class="status-badge danger"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>
                        @elseif($item->status === 'Selesai')
                            <span class="status-badge info"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sudah Kembali</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($item->status === 'Menunggu')
                            @if($isWakaPiketToday || Auth::user()->email === 'waka.piket@smkn1boyolangu.sch.id' || Auth::user()->role === 'admin')
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <form action="{{ route('waka-piket.dispensasi.status', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI dispensasi untuk {{ addslashes($item->siswa->nama_lengkap ?? 'Siswa') }}?');">
                                        @csrf
                                        <input type="hidden" name="action" value="setujui">
                                        <button type="submit" class="btn-approve">
                                            <i class="fa-solid fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <button type="button" class="btn-reject" onclick="openRejectModal('{{ route('waka-piket.dispensasi.status', $item->id) }}', '{{ addslashes($item->siswa->nama_lengkap ?? 'Siswa') }}')">
                                        <i class="fa-solid fa-xmark"></i> Tolak
                                    </button>
                                </div>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; padding: 5px 11px; border-radius: 8px; font-weight: 700;">
                                    <i class="fa-solid fa-lock" style="color: #cbd5e1;"></i> Khusus Waka Piket
                                </span>
                            @endif
                        @elseif($item->status === 'Disetujui')
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                <a href="{{ route('guru-piket.dispensasi.cetak', $item->id) }}" target="_blank" class="btn-print-slip" title="Cetak Surat Izin">
                                    <i class="fa-solid fa-print"></i> Slip Izin
                                </a>
                            </div>
                        @else
                            <span style="font-size: 12px; color: #94a3b8;">Telah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 48px; color: #94a3b8;">
                        <i class="fa-solid fa-inbox" style="font-size: 32px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                        Tidak ada permohonan dispensasi siswa pada tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($dispensasiList->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid #eef2f7;">
            {{ $dispensasiList->links() }}
        </div>
    @endif
</div>

{{-- Modal Penolakan Dispensasi --}}
<div class="modal-backdrop-custom" id="modalReject">
    <div class="modal-card-custom">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">
            <i class="fa-solid fa-circle-xmark" style="color: #ef4444;"></i> Tolak Permohonan Dispensasi
        </h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">
            Siswa: <strong id="rejectStudentName" style="color: #0f172a;"></strong>
        </p>
        <form id="formReject" method="POST">
            @csrf
            <input type="hidden" name="action" value="tolak">
            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">
                    Alasan Penolakan:
                </label>
                <textarea name="catatan" rows="3" class="form-control" style="width: 100%; border-radius: 10px; border: 1.5px solid #cbd5e1; padding: 10px; font-size: 13px;" placeholder="Tuliskan alasan penolakan dispensasi..." required></textarea>
            </div>
            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn" style="background: #f1f5f9; color: #475569; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 12.5px;" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn" style="background: #ef4444; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 12.5px;">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openRejectModal(actionUrl, studentName) {
        document.getElementById('formReject').action = actionUrl;
        document.getElementById('rejectStudentName').innerText = studentName;
        document.getElementById('modalReject').classList.add('show');
    }
    function closeRejectModal() {
        document.getElementById('modalReject').classList.remove('show');
    }
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop-custom')) {
            e.target.classList.remove('show');
        }
    });
</script>
@endsection
