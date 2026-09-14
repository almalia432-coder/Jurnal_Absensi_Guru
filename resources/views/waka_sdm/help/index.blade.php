@extends('layouts.waka_sdm')

@section('title', 'Help Centre & SOP Waka SDM')
@section('header_title', 'Pusat Bantuan & SOP Waka SDM')
@section('header_subtitle', 'Panduan operasional dan standar prosedur persetujuan dispensasi siswa & izin guru')

@section('styles')
<style>
    .help-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    .help-card {
        background: white; border-radius: 18px; padding: 26px 28px;
        border: 1px solid #e5e9f2; box-shadow: 0 4px 16px rgba(0,0,0,0.02); margin-bottom: 24px;
    }
    .help-title {
        font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 16px;
        display: flex; align-items: center; gap: 10px;
    }
    .sop-step {
        display: flex; gap: 16px; margin-bottom: 20px;
    }
    .step-num {
        width: 32px; height: 32px; border-radius: 50%; background: #2563eb; color: white;
        font-weight: 800; font-size: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .step-num.green { background: #16a34a; }
    .step-content h4 { font-size: 14.5px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .step-content p { font-size: 13px; color: #64748b; line-height: 1.5; }

    .faq-item { border-bottom: 1px solid #f1f5f9; padding: 14px 0; }
    .faq-item:last-child { border-bottom: none; }
    .faq-q { font-weight: 700; font-size: 14px; color: #0f172a; margin-bottom: 6px; }
    .faq-a { font-size: 13px; color: #64748b; line-height: 1.5; }

    .info-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe; border-radius: 18px; padding: 22px;
    }

    @media (max-width: 1024px) {
        .help-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="help-grid">
    <div>
        {{-- SOP Dispensasi Siswa --}}
        <div class="help-card">
            <div class="help-title">
                <i class="fa-solid fa-graduation-cap" style="color:#16a34a;"></i>
                <span>SOP Persetujuan Dispensasi Siswa</span>
            </div>
            <div class="sop-step">
                <div class="step-num green">1</div>
                <div class="step-content">
                    <h4>Pengajuan oleh Guru Piket / Wali Kelas</h4>
                    <p>Siswa yang membutuhkan izin keluar sekolah (mengikuti lomba, tes kesehatan, atau dinas sekolah) mengajukan izin melalui Guru Piket dengan melampirkan surat tugas atau surat keterangan.</p>
                </div>
            </div>
            <div class="sop-step">
                <div class="step-num green">2</div>
                <div class="step-content">
                    <h4>Verifikasi & Persetujuan Waka SDM / Kesiswaan</h4>
                    <p>Waka SDM meninjau berkas permohonan melalui menu <strong>Persetujuan Dispensasi</strong> atau modal cepat di Dashboard. Berikan keputusan <em>Setujui</em> atau <em>Tolak</em> beserta catatan pendukung.</p>
                </div>
            </div>
            <div class="sop-step">
                <div class="step-num green">3</div>
                <div class="step-content">
                    <h4>Validasi Gerbang oleh Satpam (Live Gate Tracking)</h4>
                    <p>Setelah disetujui Waka SDM, status otomatis tersinkronisasi ke tablet / komputer Pos Satpam. Satpam mengonfirmasi jam keluar siswa saat melewati gerbang sekolah.</p>
                </div>
            </div>
            <div class="sop-step">
                <div class="step-num green">4</div>
                <div class="step-content">
                    <h4>Konfirmasi Kepulangan Siswa</h4>
                    <p>Saat siswa kembali ke sekolah, Satpam mencatat jam kepulangan sehingga dispensasi berstatus <em>Selesai</em> dan jam kembali tercatat secara akurat.</p>
                </div>
            </div>
        </div>

        {{-- SOP Izin Guru --}}
        <div class="help-card">
            <div class="help-title">
                <i class="fa-solid fa-user-check" style="color:#2563eb;"></i>
                <span>SOP Persetujuan Izin Guru</span>
            </div>
            <div class="sop-step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <h4>Pengajuan Izin Mandiri oleh Guru</h4>
                    <p>Guru yang berhalangan hadir mengajar karena sakit, cuti, atau penugasan luar kota mengirimkan formulir izin melalui portal Guru Mapel disertai surat dokter atau surat tugas resmi.</p>
                </div>
            </div>
            <div class="sop-step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h4>Pemeriksaan Berkas oleh Waka SDM</h4>
                    <p>Waka SDM memverifikasi jenis izin, rentang tanggal, serta berkas lampiran pendukung melalui menu <strong>Persetujuan Izin Guru</strong>.</p>
                </div>
            </div>
            <div class="sop-step">
                <div class="step-num">3</div>
                <div class="step-content">
                    <h4>Pengalihan Jadwal & Notifikasi Kelas Terdampak</h4>
                    <p>Setelah izin disetujui, sistem secara otomatis memberi label kelas terdampak di dashboard Guru Piket agar piket dapat segera mengarahkan guru pengganti atau penugasan mandiri.</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="info-card" style="margin-bottom: 24px;">
            <div style="font-weight: 800; font-size: 15px; color: #1e40af; margin-bottom: 8px;">
                <i class="fa-solid fa-lightbulb"></i> Tips Efisiensi Persetujuan
            </div>
            <p style="font-size: 13px; color: #1e3a8a; line-height: 1.5;">
                Anda dapat menyetujui atau menolak permohonan yang mendesak secara instan langsung dari halaman <strong>Dashboard</strong> dengan menekan tombol <strong>Detail</strong> pada tabel Pengajuan Menunggu Persetujuan.
            </p>
        </div>

        <div class="help-card">
            <div class="help-title">
                <i class="fa-solid fa-circle-question" style="color:#64748b;"></i>
                <span>Tanya Jawab (FAQ)</span>
            </div>
            <div class="faq-item">
                <div class="faq-q">Apakah Waka SDM bisa membatalkan izin yang sudah disetujui?</div>
                <div class="faq-a">Ya, Anda dapat membuka halaman Persetujuan Izin Guru, pilih tab Disetujui, lalu lakukan perubahan status kembali jika terdapat kekeliruan data.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Bagaimana jika siswa tidak kembali melebihi jam estimasi dispensasi?</div>
                <div class="faq-a">Di dashboard Satpam dan Guru Piket, status siswa akan ditandai dengan peringatan keterlambatan (overdue) dan wali kelas akan diberitahukan.</div>
            </div>
            <div class="faq-item">
                <div class="faq-q">Apakah data persetujuan bisa dicetak untuk arsip fisik?</div>
                <div class="faq-a">Tentu saja. Anda dapat menuju menu <strong>Laporan</strong>, pilih rentang tanggal laporan, lalu klik tombol <em>Cetak</em> atau <em>Ekspor CSV</em>.</div>
            </div>
        </div>
    </div>
</div>
@endsection
