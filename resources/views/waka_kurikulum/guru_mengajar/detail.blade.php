@extends('layouts.waka_kurikulum')
@section('title', 'Detail Mengajar - ' . $guru->nama_lengkap)
@section('header_title', 'Profil Beban Mengajar Guru')
@section('header_subtitle', $guru->nama_lengkap . ' (NIP: ' . ($guru->nip ?? '-') . ')')

@section('header_extra')
    <div style="display:flex;gap:10px;align-items:center;">
        <button type="button" onclick="openClearJadwalModal()"
            style="background:rgba(239,68,68,0.15);color:white;border:1px solid rgba(239,68,68,0.3);padding:10px 16px;border-radius:12px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;"
            title="Hapus semua slot jadwal guru ini">
            <i class="fa-solid fa-trash-can"></i> Reset Semua Jadwal
        </button>
        <a href="{{ route('waka-kurikulum.guru-mengajar.index') }}" class="btn-back-action">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Guru
        </a>
    </div>
@endsection

@section('styles')
<style>
    .btn-back-action {
        background: #ffffff; color: #0284c7; border: none; padding: 10px 18px;
        border-radius: 12px; font-weight: 700; font-size: 13.5px; cursor: pointer;
        display: flex; align-items: center; gap: 8px; text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); transition: all 0.2s;
    }
    .btn-back-action:hover { background: #f0f9ff; transform: translateY(-1px); }

    /* Profile Header Card */
    .profile-hero-card {
        background: white; border-radius: 20px; border: 1px solid #e2e8f0;
        padding: 24px 28px; margin-bottom: 24px; display: flex; align-items: center;
        justify-content: space-between; gap: 20px; flex-wrap: wrap;
    }
    .profile-info-wrap { display: flex; align-items: center; gap: 18px; }
    .profile-avatar-lg {
        width: 68px; height: 68px; border-radius: 18px; background: #e0f2fe;
        color: #0284c7; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 26px; border: 2px solid #bae6fd;
    }
    .profile-name { font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .profile-sub { font-size: 13px; color: #64748b; margin-top: 4px; display: flex; gap: 14px; flex-wrap: wrap; }
    .profile-sub span { display: flex; align-items: center; gap: 6px; }

    .stats-strip { display: flex; gap: 18px; flex-wrap: wrap; }
    .stat-box {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 12px 18px; text-align: center; min-width: 110px;
    }
    .stat-val { font-size: 20px; font-weight: 800; color: #0284c7; }
    .stat-lbl { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-top: 2px; }

    /* Content Layout */
    .detail-grid { display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px; }
    .content-card { background: white; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 24px; }
    .content-hdr {
        padding: 18px 22px; border-bottom: 1px solid #f1f5f9; display: flex;
        align-items: center; justify-content: space-between;
    }
    .content-title { font-size: 15px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .content-bdy { padding: 20px 22px; }

    /* Schedule Day Pill */
    .schedule-table { width: 100%; border-collapse: collapse; }
    .schedule-table th {
        padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase;
        color: #94a3b8; border-bottom: 1px solid #f1f5f9; background: #fafcff; text-align: left;
    }
    .schedule-table td {
        padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f8fafc; color: #334155;
    }
    .schedule-table tr:hover td { background: #f8fafc; }

    .day-badge {
        display: inline-block; padding: 4px 10px; border-radius: 8px;
        font-weight: 700; font-size: 12px; background: #e0f2fe; color: #0284c7;
    }

    .btn-del-jadwal {
        width: 28px; height: 28px; border-radius: 7px; border: 1px solid #fecaca;
        background: #fff; color: #ef4444; cursor: pointer; font-size: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.15s;
    }
    .btn-del-jadwal:hover { background: #fee2e2; border-color: #ef4444; }

    /* Delete / Clear Modal */
    .confirm-modal-backdrop {
        display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6);
        backdrop-filter: blur(4px); z-index: 10000; align-items: center; justify-content: center; padding: 20px;
    }
    .confirm-modal-backdrop.open { display: flex; }
    .confirm-modal-box {
        background: white; border-radius: 20px; width: 100%; max-width: 420px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25); overflow: hidden;
        animation: popIn 0.2s ease-out;
    }
    @keyframes popIn {
        from { opacity: 0; transform: scale(0.96) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    @media (max-width: 992px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div>
    <!-- Profile Hero Card -->
    <div class="profile-hero-card">
        <div class="profile-info-wrap">
            <div class="profile-avatar-lg">
                {{ substr($guru->nama_lengkap, 0, 1) }}
            </div>
            <div>
                <div class="profile-name">{{ $guru->nama_lengkap }}</div>
                <div class="profile-sub">
                    <span><i class="fa-solid fa-id-card"></i> NIP: {{ $guru->nip ?? '-' }}</span>
                    <span><i class="fa-solid fa-phone"></i> {{ $guru->no_hp ?? '-' }}</span>
                    <span><i class="fa-solid fa-location-dot"></i> {{ Str::limit($guru->alamat ?? 'Alamat belum diisi', 28) }}</span>
                </div>
            </div>
        </div>

        <div class="stats-strip">
            <div class="stat-box">
                <div class="stat-val">{{ $totalJp }}</div>
                <div class="stat-lbl">Total Beban JP</div>
            </div>
            <div class="stat-box">
                <div class="stat-val">{{ count($mapelSummary) }}</div>
                <div class="stat-lbl">Mapel Diampu</div>
            </div>
            <div class="stat-box">
                <div class="stat-val">{{ count($kelasSummary) }}</div>
                <div class="stat-lbl">Kelas Rombel</div>
            </div>
            <div class="stat-box">
                <div class="stat-val" style="color: {{ $totalJp >= 24 ? '#16a34a' : '#d97706' }};">
                    {{ $totalJp >= 24 ? 'STANDAR' : 'KURANG' }}
                </div>
                <div class="stat-lbl">{{ $totalJp >= 24 ? '>= 24 JP Terpenuhi' : '< 24 JP Perlu Jam' }}</div>
            </div>
        </div>
    </div>

    <!-- 2 Column Details -->
    <div class="detail-grid">
        <!-- Left: Summary Mapel & Kelas -->
        <div>
            <!-- Mapel Taught Card -->
            <div class="content-card">
                <div class="content-hdr">
                    <div class="content-title">
                        <i class="fa-solid fa-book" style="color:#0284c7;"></i>
                        <span>Mata Pelajaran yang Diampu</span>
                    </div>
                </div>
                <div class="content-bdy" style="padding:14px 18px;">
                    @forelse($mapelSummary as $mapel)
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; margin-bottom:10px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <div style="font-weight:700; color:#0f172a; font-size:14px;">{{ $mapel['mapel'] }}</div>
                                <span style="background:#e0f2fe; color:#0284c7; padding:3px 8px; border-radius:6px; font-weight:800; font-size:12px;">
                                    {{ $mapel['jp'] }} JP
                                </span>
                            </div>
                            <div style="font-size:12px; color:#64748b; margin-top:2px;">Kode: {{ $mapel['kode'] }}</div>
                            <div style="margin-top:8px; display:flex; gap:4px; flex-wrap:wrap;">
                                @foreach($mapel['kelas'] as $k)
                                    <span style="background:#e2e8f0; color:#475569; padding:2px 7px; border-radius:6px; font-size:11px; font-weight:600;">
                                        {{ $k }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p style="text-align:center; color:#94a3b8; padding:20px; font-size:13px;">Belum ada mata pelajaran yang dijadwalkan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Kelas Taught Card -->
            <div class="content-card">
                <div class="content-hdr">
                    <div class="content-title">
                        <i class="fa-solid fa-users-rectangle" style="color:#0284c7;"></i>
                        <span>Distribusi Rombongan Belajar</span>
                    </div>
                </div>
                <div class="content-bdy" style="padding:14px 18px;">
                    @forelse($kelasSummary as $kelas)
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; border-bottom:1px solid #f1f5f9;">
                            <div>
                                <span style="font-weight:700; color:#0f172a; font-size:13.5px;">{{ $kelas['kelas'] }}</span>
                                <div style="font-size:11.5px; color:#64748b;">{{ is_array($kelas['mapel']) ? implode(', ', $kelas['mapel']) : $kelas['mapel']->implode(', ') }}</div>
                            </div>
                            <span style="font-weight:700; color:#0284c7; font-size:13px;">{{ $kelas['jp'] }} JP</span>
                        </div>
                    @empty
                        <p style="text-align:center; color:#94a3b8; padding:20px; font-size:13px;">Belum ada kelas yang diajar.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Weekly Schedule Matrix & Recent Journals -->
        <div>
            <!-- Weekly Schedule -->
            <div class="content-card">
                <div class="content-hdr">
                    <div class="content-title">
                        <i class="fa-solid fa-calendar-days" style="color:#0284c7;"></i>
                        <span>Jadwal Mengajar Mingguan</span>
                    </div>
                    <span style="font-size:12px;color:#64748b;font-weight:600;">Tahun Ajaran: {{ $tahunAjaranAktif->nama ?? 'Aktif' }}</span>
                </div>
                <div class="table-responsive-wrap">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th style="width:90px;">Hari</th>
                                <th style="width:70px;">Jam Ke</th>
                                <th>Waktu</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th style="width:50px;text-align:center;">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalList as $jadwal)
                                <tr>
                                    <td><span class="day-badge">{{ $jadwal->hari }}</span></td>
                                    <td><strong>Jam {{ $jadwal->jam_ke }}</strong></td>
                                    <td style="color:#64748b;font-family:monospace;">
                                        {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                    </td>
                                    <td><strong style="color:#0f172a;">{{ $jadwal->mapel->nama_mapel ?? '-' }}</strong></td>
                                    <td><span style="font-weight:700;color:#0284c7;">{{ $jadwal->kelas->nama_kelas ?? '-' }}</span></td>
                                    <td style="text-align:center;">
                                        <button type="button" class="btn-del-jadwal"
                                            onclick="openDeleteJadwalModal({{ $jadwal->id_jadwal }}, '{{ addslashes($jadwal->mapel->nama_mapel ?? '') }}', '{{ addslashes($jadwal->kelas->nama_kelas ?? '') }}', '{{ $jadwal->hari }}', {{ $jadwal->jam_ke }})"
                                            title="Hapus slot jadwal ini">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:40px 20px; color:#94a3b8;">
                                        Belum ada jadwal mengajar yang tersusun untuk guru ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Journals -->
            <div class="content-card">
                <div class="content-hdr">
                    <div class="content-title">
                        <i class="fa-solid fa-book-open" style="color:#0284c7;"></i>
                        <span>Riwayat Jurnal Mengajar Terkini</span>
                    </div>
                </div>
                <div class="content-bdy" style="padding:10px 18px;">
                    @forelse($recentJurnal as $jurnal)
                        <div style="padding:12px 0; border-bottom:1px solid #f8fafc; display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <div style="font-weight:700; color:#0f172a; font-size:13.5px;">{{ $jurnal->mapel->nama_mapel ?? '-' }} — {{ $jurnal->kelas->nama_kelas ?? '-' }}</div>
                                <div style="font-size:12px; color:#64748b; margin-top:2px;">Materi: {{ Str::limit($jurnal->materi, 45) }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:12px; font-weight:700; color:#0284c7;">{{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d M Y') }}</div>
                                <div style="font-size:11px; color:#94a3b8;">Jam ke-{{ $jurnal->jam_ke ?? '-' }}</div>
                            </div>
                        </div>
                    @empty
                        <p style="text-align:center; color:#94a3b8; padding:20px; font-size:13px;">Belum ada riwayat pengisian jurnal mengajar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL KONFIRMASI HAPUS SATU JADWAL ===== --}}
<div class="confirm-modal-backdrop" id="modalDeleteJadwal">
    <div class="confirm-modal-box">
        <div style="text-align:center;padding:28px 24px 20px;">
            <div style="width:58px;height:58px;border-radius:50%;background:#fee2e2;color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px;">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <h3 style="font-size:17px;font-weight:800;color:#0f172a;margin-bottom:8px;">Hapus Slot Jadwal?</h3>
            <p style="font-size:13.5px;color:#64748b;line-height:1.5;" id="deleteJadwalPrompt"></p>
        </div>
        <form id="formDeleteJadwal" method="POST">
            @csrf
            @method('DELETE')
            <div style="display:flex;gap:10px;padding:16px 24px;background:#f8fafc;border-top:1px solid #f1f5f9;">
                <button type="button" onclick="closeDeleteJadwalModal()" style="flex:1;padding:10px;border-radius:10px;border:1px solid #cbd5e1;background:white;color:#475569;font-weight:700;font-size:13.5px;cursor:pointer;">Batal</button>
                <button type="submit" style="flex:1;padding:10px;border-radius:10px;border:none;background:#ef4444;color:white;font-weight:700;font-size:13.5px;cursor:pointer;">Hapus Jadwal</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL KONFIRMASI RESET SEMUA JADWAL ===== --}}
<div class="confirm-modal-backdrop" id="modalClearJadwal">
    <div class="confirm-modal-box">
        <div style="text-align:center;padding:28px 24px 20px;">
            <div style="width:58px;height:58px;border-radius:50%;background:#fee2e2;color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px;">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h3 style="font-size:17px;font-weight:800;color:#0f172a;margin-bottom:8px;">Reset Semua Jadwal Guru?</h3>
            <p style="font-size:13.5px;color:#64748b;line-height:1.5;">
                Seluruh slot jadwal <strong>{{ $guru->nama_lengkap }}</strong> yang belum memiliki rekaman jurnal akan dihapus.
                Jadwal yang sudah ada jurnalnya tetap dipertahankan.
            </p>
        </div>
        <form action="{{ route('waka-kurikulum.guru-mengajar.clear-jadwal', $guru->id_guru) }}" method="POST">
            @csrf
            @method('DELETE')
            <div style="display:flex;gap:10px;padding:16px 24px;background:#f8fafc;border-top:1px solid #f1f5f9;">
                <button type="button" onclick="closeClearJadwalModal()" style="flex:1;padding:10px;border-radius:10px;border:1px solid #cbd5e1;background:white;color:#475569;font-weight:700;font-size:13.5px;cursor:pointer;">Batal</button>
                <button type="submit" style="flex:1;padding:10px;border-radius:10px;border:none;background:#ef4444;color:white;font-weight:700;font-size:13.5px;cursor:pointer;"><i class="fa-solid fa-trash-can"></i> Reset Jadwal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Delete single jadwal modal
    function openDeleteJadwalModal(idJadwal, namaMapel, namaKelas, hari, jamKe) {
        document.getElementById('deleteJadwalPrompt').innerHTML =
            `Hapus jadwal <strong>${namaMapel}</strong> di kelas <strong>${namaKelas}</strong> (${hari}, Jam ke-${jamKe})?<br><span style="color:#ef4444;font-size:12px;">Hanya bisa dihapus jika belum ada rekaman jurnal.</span>`;
        document.getElementById('formDeleteJadwal').action = `/waka-kurikulum/jadwal/${idJadwal}`;
        document.getElementById('modalDeleteJadwal').classList.add('open');
    }
    function closeDeleteJadwalModal() {
        document.getElementById('modalDeleteJadwal').classList.remove('open');
    }

    // Clear all jadwal modal
    function openClearJadwalModal() {
        document.getElementById('modalClearJadwal').classList.add('open');
    }
    function closeClearJadwalModal() {
        document.getElementById('modalClearJadwal').classList.remove('open');
    }

    // Close modals on backdrop click
    document.querySelectorAll('.confirm-modal-backdrop').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });
    });
</script>
@endsection
