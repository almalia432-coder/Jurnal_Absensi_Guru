<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jurnal Mengajar — SMK Negeri 1 Boyolangu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
</head>
<body>

<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="school-badge">
        <i class="bi bi-award-fill" style="font-size:10px"></i>
        SMK Negeri 1 Boyolangu
      </div>
      <h1>Sistem <em>Jurnal</em><br>Mengajar Guru</h1>
      <p>Catat aktivitas pembelajaran harian secara tertib dan akurat.</p>
    </div>

    <div class="steps">
      <div class="step-item active" id="step-nav-1" onclick="goToStep(1)">
        <div class="step-dot">1</div>
        <div class="step-info">
          <div class="step-num">Langkah 01</div>
          <div class="step-label">Data Pengajar</div>
        </div>
      </div>
      <div class="step-item" id="step-nav-2" onclick="goToStep(2)">
        <div class="step-dot">2</div>
        <div class="step-info">
          <div class="step-num">Langkah 02</div>
          <div class="step-label">Materi Pembelajaran</div>
        </div>
      </div>
      <div class="step-item" id="step-nav-3" onclick="goToStep(3)">
        <div class="step-dot">3</div>
        <div class="step-info">
          <div class="step-num">Langkah 03</div>
          <div class="step-label">Kehadiran & Status</div>
        </div>
      </div>
    </div>

    <div class="sidebar-footer">
      <p>Tahun Pelajaran 2024/2025<br>Sistem Informasi Akademik</p>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">
    <div class="form-topbar">
      <div class="breadcrumb">
        <i class="bi bi-house" style="font-size:13px"></i>
        <i class="bi bi-chevron-right" style="font-size:10px"></i>
        <span>Jurnal Mengajar</span>
        <i class="bi bi-chevron-right" style="font-size:10px"></i>
        Input Baru
      </div>
      <div class="date-badge">
        <i class="bi bi-calendar3" style="font-size:12px"></i>
        <span id="today-date"></span>
      </div>
    </div>

    <div class="progress-bar-wrap">
      <div class="progress-track">
        <div class="progress-fill" id="progress-fill" style="width: 33%"></div>
      </div>
      <div class="progress-label">
        <span id="step-label-text">Langkah 1 dari 3</span>
        <span id="step-pct-text">33%</span>
      </div>
    </div>

    <div class="form-container">

      <form action="{{ route('jurnal.store') }}" method="POST" id="mainForm">
        @csrf

        <!-- STEP 1 -->
        <div class="form-section active" id="section-1">
          <div class="section-heading">
            <div class="eyebrow">Langkah 01 / 03</div>
            <h2>Data Pengajar</h2>
            <p>Isi informasi dasar sesi mengajar — guru, kelas, dan waktu pelaksanaan.</p>
          </div>

          <div class="field-grid">
            <div class="field">
              <label>Nama Guru <span class="req">*</span></label>
              <div class="select-wrap">
                <select name="id_guru" required>
                  <option value="">Pilih guru pengampu</option>
                  <!-- @foreach($guru as $g) -->
                  <option value="1">Drs. Ahmad Fauzi, M.Pd</option>
                  <option value="2">Siti Rahayu, S.Kom</option>
                  <!-- @endforeach -->
                </select>
              </div>
            </div>

            <div class="field">
              <label>Kelas <span class="req">*</span></label>
              <div class="select-wrap">
                <select name="id_kelas" required>
                  <option value="">Pilih kelas</option>
                  <!-- @foreach($kelas as $k) -->
                  <option value="1">XII RPL 1</option>
                  <option value="2">XI TKJ 2</option>
                  <!-- @endforeach -->
                </select>
              </div>
            </div>

            <div class="field">
              <label>Tanggal Mengajar <span class="req">*</span></label>
              <input type="date" name="tanggal" id="date-input" required>
            </div>

            <div class="field">
              <label>Jam Ke <span class="req">*</span></label>
              <div class="counter-field">
                <button type="button" class="counter-btn" onclick="changeJam(-1)">−</button>
                <input type="number" name="jam_ke" id="jam-ke" min="1" max="12" value="1" required>
                <button type="button" class="counter-btn" onclick="changeJam(1)">+</button>
              </div>
              <span class="field-hint">Jam pelajaran ke berapa (1–12)</span>
            </div>
          </div>

          <div class="form-nav">
            <a href="#" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button type="button" class="btn-next" onclick="nextStep(2)">
              Lanjut ke Materi <i class="bi bi-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- STEP 2 -->
        <div class="form-section" id="section-2">
          <div class="section-heading">
            <div class="eyebrow">Langkah 02 / 03</div>
            <h2>Materi Pembelajaran</h2>
            <p>Uraikan topik dan materi yang disampaikan pada sesi ini secara ringkas dan jelas.</p>
          </div>

          <div class="field-grid full">
            <div class="field">
              <label>Uraian Materi <span class="req">*</span></label>
              <textarea name="materi" rows="5" placeholder="Contoh: Pengantar jaringan komputer — konsep OSI layer dan implementasi TCP/IP dalam konteks LAN sekolah..." required></textarea>
              <span class="field-hint">Tuliskan secara singkat topik utama yang dibahas</span>
            </div>
          </div>

          <div class="form-nav">
            <button type="button" class="btn-back" onclick="nextStep(1)"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
            <button type="button" class="btn-next" onclick="nextStep(3)">
              Lanjut ke Kehadiran <i class="bi bi-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- STEP 3 -->
        <div class="form-section" id="section-3">
          <div class="section-heading">
            <div class="eyebrow">Langkah 03 / 03</div>
            <h2>Kehadiran & Status</h2>
            <p>Catat jumlah siswa dan status kehadiran guru pada sesi ini.</p>
          </div>

          <div class="field-grid">
            <div class="field">
              <label>Siswa Hadir <span class="req">*</span></label>
              <div class="counter-field">
                <button type="button" class="counter-btn" onclick="changeCount('hadir',-1)">−</button>
                <input type="number" name="jumlah_siswa_hadir" id="count-hadir" min="0" value="0" required>
                <button type="button" class="counter-btn" onclick="changeCount('hadir',1)">+</button>
              </div>
            </div>

            <div class="field">
              <label>Siswa Tidak Hadir <span class="req">*</span></label>
              <div class="counter-field">
                <button type="button" class="counter-btn" onclick="changeCount('absen',-1)">−</button>
                <input type="number" name="jumlah_siswa_tidak_hadir" id="count-absen" min="0" value="0" required>
                <button type="button" class="counter-btn" onclick="changeCount('absen',1)">+</button>
              </div>
            </div>
          </div>

          <div class="ornament-divider"><span>Status Guru</span></div>

          <div class="field">
            <label>Status Kehadiran Guru <span class="req">*</span></label>
            <div class="status-group" id="status-group">
              <label class="status-card" id="sc-hadir">
                <input type="radio" name="status_guru" value="Hadir" required onchange="selectStatus('hadir')">
                <span class="status-icon">✅</span>
                <div class="status-text">Hadir</div>
                <div class="status-sub">Mengajar langsung</div>
              </label>
              <label class="status-card" id="sc-izin">
                <input type="radio" name="status_guru" value="Izin" onchange="selectStatus('izin')">
                <span class="status-icon">📋</span>
                <div class="status-text">Izin</div>
                <div class="status-sub">Ada keperluan</div>
              </label>
              <label class="status-card" id="sc-sakit">
                <input type="radio" name="status_guru" value="Sakit" onchange="selectStatus('sakit')">
                <span class="status-icon">🏥</span>
                <div class="status-text">Sakit</div>
                <div class="status-sub">Tidak dapat hadir</div>
              </label>
              <label class="status-card" id="sc-dinas">
                <input type="radio" name="status_guru" value="Dinas" onchange="selectStatus('dinas')">
                <span class="status-icon">🏛️</span>
                <div class="status-text">Dinas</div>
                <div class="status-sub">Tugas dinas luar</div>
              </label>
            </div>
          </div>

          <div class="ornament-divider"><span>Catatan Tambahan</span></div>

          <div class="field-grid full">
            <div class="field">
              <label>Catatan</label>
              <input type="text" name="catatan" placeholder="Catatan khusus, tugas pengganti, dll. (opsional)">
              <span class="field-hint">Kosongkan jika tidak ada catatan</span>
            </div>
          </div>

          <div class="form-nav">
            <div style="display:flex;gap:10px;">
              <button type="button" class="btn-back" onclick="nextStep(2)"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
              <button type="reset" class="btn-reset-sm"><i class="bi bi-arrow-clockwise"></i> Reset</button>
            </div>
            <div class="btn-group-right">
              <button type="submit" class="btn-next gold">
                <i class="bi bi-floppy-fill"></i> Simpan Jurnal
              </button>
            </div>
          </div>
        </div>

      </form>
    </div>
  </main>
</div>

<script src="{{ asset('js/create.js') }}"></script>
</body>
</html>