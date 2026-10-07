<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Dispensasi Siswa - SMKN 1 BOYOLANGU</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Times New Roman', Times, serif; }
        body { background: #f1f5f9; padding: 30px; display: flex; justify-content: center; }
        .ticket-card {
            background: white;
            width: 600px;
            padding: 30px 36px;
            border: 2px solid #0f172a;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
            gap: 16px;
        }
        .header img { width: 60px; height: 60px; object-fit: contain; }
        .header-text { text-align: center; flex: 1; }
        .header-text h3 { font-size: 14px; text-transform: uppercase; font-weight: bold; }
        .header-text h2 { font-size: 16px; text-transform: uppercase; font-weight: bold; margin: 2px 0; }
        .header-text p { font-size: 11px; }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .no-surat {
            text-align: center;
            font-size: 12px;
            margin-bottom: 16px;
        }

        .content-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .content-table td { padding: 5px 4px; vertical-align: top; }
        .label { width: 160px; font-weight: bold; }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
        }
        .sign-box { width: 170px; }
        .sign-space { height: 50px; }

        .satpam-cut {
            border-top: 1px dashed #64748b;
            margin-top: 24px;
            padding-top: 10px;
            font-size: 11px;
            color: #475569;
            text-align: center;
        }

        @media print {
            body { background: white; padding: 0; }
            .ticket-card { box-shadow: none; border: 1.5px solid #000; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket-card">
        <div class="no-print" style="margin-bottom: 16px; text-align: right;">
            <button onclick="window.print()" style="padding: 6px 14px; background: #2b43b9; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                Cetak / Print Slip
            </button>
        </div>

        <div class="header">
            <img src="{{ asset('asset/logo_smea.png') }}" alt="Logo">
            <div class="header-text">
                <h3>Pemerintah Provinsi Jawa Timur - Dinas Pendidikan</h3>
                <h2>SMK NEGERI 1 BOYOLANGU</h2>
                <p>Jl. Ki Mangunsarkoro VI/3, Boyolangu, Tulungagung &bull; Telp. (0355) 323354</p>
            </div>
        </div>

        <div class="title">SURAT IZIN DISPENSASI SISWA</div>
        <div class="no-surat">Nomor: {{ $dispensasi->id }}/DISP/PIKET/{{ Carbon\Carbon::parse($dispensasi->tanggal)->format('m/Y') }}</div>

        <p style="font-size: 13px; margin-bottom: 12px;">Guru Piket SMKN 1 Boyolangu dengan ini memberikan izin dispensasi kepada siswa di bawah ini untuk meninggalkan lingkungan/jam pelajaran sekolah:</p>

        <table class="content-table">
            <tr>
                <td class="label">Nama Siswa</td>
                <td>: <strong>{{ $dispensasi->siswa->nama_lengkap ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label">NISN / NIS</td>
                <td>: {{ $dispensasi->siswa->nisn ?? '-' }} / {{ $dispensasi->siswa->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas / Jurusan</td>
                <td>: {{ $dispensasi->siswa->kelas->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Hari / Tanggal</td>
                <td>: {{ Carbon\Carbon::parse($dispensasi->tanggal)->translatedFormat('l, j F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Izin Keluar</td>
                <td>: <strong>{{ Carbon\Carbon::parse($dispensasi->jam_keluar)->format('H:i') }} WIB</strong></td>
            </tr>
            <tr>
                <td class="label">Batas Kembali</td>
                <td>: {{ $dispensasi->jam_kembali ? Carbon\Carbon::parse($dispensasi->jam_kembali)->format('H:i').' WIB' : 'Sampai Jam Pelajaran Selesai' }}</td>
            </tr>
            <tr>
                <td class="label">Keperluan / Alasan</td>
                <td>: {{ $dispensasi->alasan }}</td>
            </tr>
        </table>

        <div class="signatures">
            <div class="sign-box">
                <div>Siswa Pemohon,</div>
                <div class="sign-space" style="height: 40px;"></div>
                <div><strong>( {{ $dispensasi->siswa->nama_lengkap ?? 'Siswa' }} )</strong></div>
                <div style="font-size: 10.5px; color: #64748b;">NISN: {{ $dispensasi->siswa->nisn ?? '-' }}</div>
            </div>

            <div class="sign-box">
                <div>Guru Piket (Pemeriksa),</div>
                <div class="sign-space" style="height: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <span style="border: 1.5px solid #0284c7; color: #0284c7; padding: 2px 6px; font-size: 9px; font-weight: bold; border-radius: 4px; font-family: sans-serif;">
                        ✓ DISETUJUI PIKET
                    </span>
                    <div style="font-size: 9.5px; color: #64748b; margin-top: 2px;">
                        {{ $dispensasi->piket_at ? \Carbon\Carbon::parse($dispensasi->piket_at)->format('H:i, d/m/Y') : ($dispensasi->created_at ? $dispensasi->created_at->format('H:i, d/m/Y') : '-') }}
                    </div>
                </div>
                <div><strong>{{ $dispensasi->piketApprovedByUser->name ?? ($dispensasi->diinputOlehUser->name ?? 'Guru Piket') }}</strong></div>
                <div style="font-size: 10.5px; color: #64748b;">NIP: {{ $dispensasi->piketApprovedByUser->guru->nip ?? ($dispensasi->diinputOlehUser->guru->nip ?? '-') }}</div>
            </div>

            <div class="sign-box">
                <div>Tulungagung, {{ Carbon\Carbon::parse($dispensasi->tanggal)->translatedFormat('d F Y') }}</div>
                <div>Menyetujui,<br><strong>Waka Piket (Pimpinan KBM)</strong></div>
                <div class="sign-space" style="height: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    @if(in_array($dispensasi->status, ['Disetujui', 'Disetujui_KS', 'Disetujui_Waka', 'Selesai']))
                        <span style="border: 1.5px solid #16a34a; color: #16a34a; padding: 2px 6px; font-size: 9px; font-weight: bold; border-radius: 4px; font-family: sans-serif;">
                            ✓ DISETUJUI WAKA
                        </span>
                        <div style="font-size: 9.5px; color: #64748b; margin-top: 2px;">
                            {{ $dispensasi->tanggal_persetujuan ? \Carbon\Carbon::parse($dispensasi->tanggal_persetujuan)->format('H:i, d/m/Y') : '-' }}
                        </div>
                    @endif
                </div>
                <div>
                    <strong>{{ $dispensasi->disetujuiOlehUser->name ?? ($wakaPiket['nama'] ?? 'Waka Piket') }}</strong>
                    <div style="font-size: 10.5px; color: #64748b;">NIP: {{ $dispensasi->disetujuiOlehUser->guru->nip ?? ($dispensasi->disetujuiOlehUser->waka->nip ?? ($wakaPiket['nip'] ?? '-')) }}</div>
                </div>
            </div>
        </div>

        <div class="satpam-cut">
            ✂️ <em>Tunjukkan slip ini kepada Petugas Satpam di gerbang utama saat keluar dan masuk kembali.</em>
        </div>
    </div>
</body>
</html>
