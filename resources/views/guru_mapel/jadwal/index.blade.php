@extends('layouts.guru_mapel')

@section('title', 'Jadwal Mengajar - Jurnal Absensi SMKN 1 BOYOLANGU')
@section('header_title', 'Jadwal Mengajar Mingguan')
@section('header_subtitle', 'Daftar alokasi jam mengajar Anda di setiap kelas dan mata pelajaran')

@section('header_extra')
    <button type="button" onclick="window.print()" class="btn-action-primary" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; box-shadow: none;">
        <i class="fa-solid fa-print"></i> Cetak Jadwal
    </button>
@endsection

@section('styles')
<style>
    /* Stats Summary Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .summary-icon.blue   { background: #eef2ff; color: #2b43b9; }
    .summary-icon.green  { background: #e6f9f0; color: #10b981; }
    .summary-icon.orange { background: #fff7ed; color: #f97316; }

    .summary-val { font-size: 24px; font-weight: 800; color: #1b2559; line-height: 1.1; }
    .summary-label { font-size: 12px; font-weight: 700; color: #707e94; text-transform: uppercase; margin-top: 2px; }

    /* Day Tabs */
    .days-tab-container {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 24px;
    }

    .day-tab-btn {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 13.5px;
        font-weight: 700;
        color: #6b7a99;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .day-tab-btn:hover {
        border-color: #2b43b9;
        color: #2b43b9;
        background: #f8fafc;
    }

    .day-tab-btn.active {
        background: #2b43b9;
        color: #ffffff;
        border-color: #2b43b9;
        box-shadow: 0 4px 14px rgba(43, 67, 185, 0.25);
    }

    .badge-count {
        background: rgba(0, 0, 0, 0.08);
        padding: 2px 7px;
        border-radius: 20px;
        font-size: 11px;
    }
    .day-tab-btn.active .badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Day Schedule Cards */
    .schedule-day-panel {
        display: none;
    }
    .schedule-day-panel.active {
        display: block;
    }

    .schedule-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
    }

    .schedule-item-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .schedule-item-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .time-badge {
        background: #eef2ff;
        color: #2b43b9;
        font-size: 12px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .class-title {
        font-size: 17px;
        font-weight: 800;
        color: #1b2559;
    }

    .mapel-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #2b43b9;
        margin-top: 2px;
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        color: #2b3674;
    }
    .info-row i {
        color: #707e94;
        width: 16px;
    }

    /* Buttons */
    .btn-action-primary {
        background: #2b43b9;
        color: white;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-action-primary:hover {
        background: #1e35a0;
    }

    @media (max-width: 900px) {
        .summary-grid { grid-template-columns: 1fr; }
        .schedule-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<!-- 1. Summary Pills -->
<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-icon blue"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="summary-val">{{ $totalSesiMingguan }} Jam</div>
            <div class="summary-label">Total Beban Mengajar / Minggu</div>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-icon green"><i class="fa-solid fa-school"></i></div>
        <div>
            <div class="summary-val">{{ $totalKelasDiajar }} Kelas</div>
            <div class="summary-label">Rombongan Belajar Diampu</div>
        </div>
    </div>
    <div class="summary-card">
        <div class="summary-icon orange"><i class="fa-solid fa-book-open"></i></div>
        <div>
            <div class="summary-val">{{ $totalMapelDiajar }} Mapel</div>
            <div class="summary-label">Mata Pelajaran Aktif</div>
        </div>
    </div>
</div>

<!-- 2. Day Tabs Selector -->
<div class="days-tab-container">
    @foreach($hariList as $idx => $h)
        @php
            $countSesi = isset($allJadwal[$h]) ? $allJadwal[$h]->count() : 0;
            $isActive = (Carbon\Carbon::today()->translatedFormat('l') === $h) || ($loop->first && !in_array(Carbon\Carbon::today()->translatedFormat('l'), $hariList));
        @endphp
        <button type="button" class="day-tab-btn {{ $isActive ? 'active' : '' }}" onclick="switchDayTab('{{ $h }}', this)">
            <i class="fa-regular fa-calendar"></i>
            <span>{{ $h }}</span>
            <span class="badge-count">{{ $countSesi }} Sesi</span>
        </button>
    @endforeach
</div>

<!-- 3. Day Schedules List -->
@foreach($hariList as $h)
    @php
        $jadwals = $allJadwal->get($h, collect());
        $isActive = (Carbon\Carbon::today()->translatedFormat('l') === $h) || ($loop->first && !in_array(Carbon\Carbon::today()->translatedFormat('l'), $hariList));
    @endphp
    <div class="schedule-day-panel {{ $isActive ? 'active' : '' }}" id="panel-{{ $h }}">
        <div class="schedule-grid">
            @forelse($jadwals as $j)
            <div class="schedule-item-card">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span class="time-badge">
                            <i class="fa-solid fa-clock"></i> Jam ke-{{ $j->jam_ke }} ({{ Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} WIB)
                        </span>
                        @if(isset($j->total_jp) && $j->total_jp > 1)
                            <span style="background: #eef2ff; color: #2b43b9; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px; border: 1px solid #dbeafe;">
                                {{ $j->total_jp }} JP
                            </span>
                        @endif
                    </div>

                    <div class="class-title">{{ $j->kelas->nama_kelas ?? '-' }}</div>
                    <div class="mapel-name">{{ $j->mapel->nama_mapel ?? '-' }}</div>

                    <div style="margin-top: 14px; display: flex; flex-direction: column; gap: 6px; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                        <div class="info-row">
                            <i class="fa-solid fa-layer-group"></i>
                            <span>Jurusan: <strong>{{ $j->kelas->jurusan ?? ($j->kelas->jurusanRelation->nama_jurusan ?? '-') }}</strong></span>
                        </div>
                        <div class="info-row">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>Wali Kelas: {{ $j->kelas->waliKelas->nama_lengkap ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div style="border-top: 1px solid #f1f5f9; padding-top: 12px;">
                    <a href="{{ route('guru-mapel.jurnal.create', ['id_jadwal' => $j->id_jadwal]) }}" class="btn-action-primary" style="width: 100%;">
                        <i class="fa-solid fa-pen-to-square"></i> Buat Jurnal Sesi Ini
                    </a>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; padding: 48px; text-align: center; background: white; border-radius: 16px; border: 1px solid #eef2f7;">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 38px; color: #cbd5e1; margin-bottom: 10px;"></i>
                <h4 style="color: #1b2559; margin-bottom: 4px;">Tidak Ada Jadwal Mengajar</h4>
                <p style="color: #6b7a99; font-size: 13px;">Anda tidak memiliki alokasi jam mengajar pada hari {{ $h }}.</p>
            </div>
            @endforelse
        </div>
    </div>
@endforeach
@endsection

@section('scripts')
<script>
    function switchDayTab(day, btn) {
        document.querySelectorAll('.day-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.schedule-day-panel').forEach(p => p.classList.remove('active'));
        const targetPanel = document.getElementById('panel-' + day);
        if (targetPanel) {
            targetPanel.classList.add('active');
        }
    }
</script>
@endsection
