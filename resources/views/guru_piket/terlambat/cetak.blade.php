<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Masuk Kelas Siswa Terlambat — {{ $terlambat->nomor_surat ?? 'SMKN 1 Boyolangu' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Times New Roman', Times, serif; }
        body { background: #f1f5f9; padding: 30px; display: flex; justify-content: center; }
        .ticket-card {
            background: white;
            width: 620px;
            padding: 32px 38px;
            border: 2px solid #0f172a;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
            gap: 16px;
        }
        .header img { width: 64px; height: 64px; object-fit: contain; }
        .header-text { text-align: center; flex: 1; }
        .header-text h3 { font-size: 13.5px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        .header-text h2 { font-size: 17px; text-transform: uppercase; font-weight: bold; margin: 2px 0; }
        .header-text p { font-size: 11px; }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .no-surat {
            text-align: center;
            font-size: 12.5px;
            margin-bottom: 16px;
            font-weight: bold;
        }

        .content-table {
            width: 100%;
            margin-bottom: 16px;
            font-size: 13px;
        }
        .content-table td { padding: 4px 3px; vertical-align: top; }
        .label { width: 180px; font-weight: bold; }

        .instruction-box {
            border: 1px dashed #475569;
            background: #f8fafc;
            padding: 10px 14px;
            font-size: 11.5px;
            margin-bottom: 20px;
            line-height: 1.4;
            color: #1e293b;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            font-size: 12px;
            text-align: center;
        }
        .sign-box { width: 200px; }
        .sign-space { height: 55px; }

        @media print {
            body { background: white; padding: 0; }
            .ticket-card { box-shadow: none; border: 1.5px solid #000; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket-card">
        <div class="no-print" style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
            <a href="javascript:history.back()" style="font-size: 12px; color: #475569; text-decoration: none; font-weight: bold;">
                &larr; Kembali
            </a>
            <button onclick="window.print()" style="padding: 7px 16px; background: #2b43b9; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px;">
                Cetak / Print Slip Izin
            </button>
        </div>

        <div class="header">
            <img src="{{ file_exists(public_path('asset/logo_smea.png')) ? asset('asset/logo_smea.png') : asset('asset/logo.png') }}" alt="Logo">
            <div class="header-text">
                <h3>Pemerintah Provinsi Jawa Timur - Dinas Pendidikan</h3>
                <h2>SMK NEGERI 1 BOYOLANGU</h2>
                <p>Jl. Ki Mangunsarkoro VI/3, Boyolangu, Tulungagung &bull; Telp. (0355) 323354</p>
            </div>
        </div>

        <div class="title">SURAT IZIN MASUK KELAS SISWA TERLAMBAT</div>
        <div class="no-surat">Nomor: {{ $terlambat->nomor_surat ?? '-' }}</div>

        <p style="font-size: 13px; margin-bottom: 12px; text-align: justify; line-height: 1.4;">
            Petugas Piket KBM SMKN 1 Boyolangu dengan ini menerangkan bahwa siswa di bawah ini telah melapor atas keterlambatannya dan diberikan izin untuk masuk mengikuti Kegiatan Belajar Mengajar (KBM):
        </p>

        <table class="content-table">
            <tr>
                <td class="label">Nama Siswa</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ $terlambat->siswa->nama_lengkap ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Siswa (NISN)</td>
                <td>:</td>
                <td>{{ $terlambat->siswa->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas / Kompetensi</td>
                <td>:</td>
                <td>{{ $terlambat->kelas->nama_kelas ?? ($terlambat->siswa->kelas->nama_kelas ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">Hari / Tanggal</td>
                <td>:</td>
                <td>{{ $tanggalFormatted }}</td>
            </tr>
            <tr>
                <td class="label">Jam Tiba di Sekolah</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($terlambat->jam_masuk)->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Izin Masuk Kelas Mulai</td>
                <td>:</td>
                <td><strong>Jam Pelajaran Ke-{{ $terlambat->jam_ke_mulai }}</strong></td>
            </tr>
            <tr>
                <td class="label">Alasan Keterlambatan</td>
                <td>:</td>
                <td>{{ $terlambat->alasan }}</td>
            </tr>
            @if($terlambat->catatan_konfirmasi)
            <tr>
                <td class="label">Catatan Waka Piket</td>
                <td>:</td>
                <td><em>{{ $terlambat->catatan_konfirmasi }}</em></td>
            </tr>
            @endif
        </table>

        <div class="instruction-box">
            <strong>Pemberitahuan kepada Bapak/Ibu Guru Mata Pelajaran:</strong><br>
            Siswa yang bersangkutan telah mendapatkan pembinaan awal dan pengesahan dari Tim Piket. Mohon berkenan mengizinkan siswa mengikuti pembelajaran di kelas mulai <strong>Jam Ke-{{ $terlambat->jam_ke_mulai }}</strong> dan mencatat kehadiran siswa sebagai <strong>Terlambat</strong>.
        </div>

        <div class="signatures">
            <div class="sign-box">
                <div>Tulungagung, {{ \Carbon\Carbon::parse($terlambat->tanggal)->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 2px;">Guru Piket Pencatat,</div>
                <div class="sign-space"></div>
                <div style="font-weight: bold; text-decoration: underline;">
                    {{ $terlambat->diinputOlehUser->name ?? '(..........................................)' }}
                </div>
                <div style="font-size: 11px;">Petugas Guru Piket</div>
            </div>

            <div class="sign-box">
                <div>Mengetahui & Mengesahkan,</div>
                <div style="font-weight: bold; margin-top: 2px;">Waka Piket KBM,</div>
                <div class="sign-space"></div>
                <div style="font-weight: bold; text-decoration: underline;">
                    {{ $terlambat->dikonfirmasiOlehUser->name ?? '(..........................................)' }}
                </div>
                <div style="font-size: 11px;">
                    NIP. {{ $terlambat->dikonfirmasiOlehUser->guru->nip ?? ($terlambat->dikonfirmasiOlehUser->waka->nip ?? '-') }}
                </div>
            </div>
        </div>

        <div style="margin-top: 24px; font-size: 10px; color: #64748b; text-align: center; border-top: 1px dotted #cbd5e1; padding-top: 6px;">
            Dicetak secara digital oleh Sistem Jurnal Mengajar & Absensi Siswa SMKN 1 Boyolangu pada {{ now()->translatedFormat('d F Y • H:i') }} WIB
        </div>
    </div>
</body>
</html>
