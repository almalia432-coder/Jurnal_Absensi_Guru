<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Piket KBM Semester Ganjil 2026/2027 - SMKN 1 Boyolangu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 24px;
            font-size: 12px;
        }

        .print-container {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        /* Header Title */
        .doc-header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
        }

        .doc-header h2 {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .doc-header h3 {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Section Subtitle */
        .section-badge {
            display: inline-block;
            background: #0f172a;
            color: white;
            font-size: 11.5px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 6px;
            text-transform: uppercase;
            margin-bottom: 8px;
            margin-top: 16px;
        }

        /* Matrix Table */
        table.matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table.matrix-table th, table.matrix-table td {
            border: 1px solid #334155;
            padding: 6px 8px;
            vertical-align: top;
        }

        table.matrix-table th {
            background-color: #f1f5f9;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
        }

        table.matrix-table ol {
            margin-left: 16px;
            padding-left: 0;
            line-height: 1.4;
        }

        /* Signature block */
        .sign-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .sign-box {
            width: 320px;
            text-align: left;
        }

        .sign-date {
            margin-bottom: 6px;
            font-size: 11.5px;
        }

        .sign-role {
            font-weight: 700;
            margin-bottom: 60px;
        }

        .sign-name {
            font-weight: 800;
            text-decoration: underline;
            font-size: 12px;
        }

        .sign-nip {
            font-size: 11px;
            margin-top: 2px;
        }

        /* Floating Print Bar (Screen only) */
        .floating-print-bar {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #1e293b;
            padding: 12px 20px;
            border-radius: 30px;
            display: flex;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            z-index: 1000;
        }

        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-close {
            background: transparent;
            color: #94a3b8;
            border: 1px solid #475569;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        @media print {
            @page {
                size: landscape;
                margin: 12mm 15mm;
            }
            body {
                background: white;
                padding: 0;
            }
            .print-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .floating-print-bar {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Button -->
    <div class="floating-print-bar">
        <button class="btn-print" onclick="window.print()">
            Cetak / Simpan PDF
        </button>
        <button class="btn-close" onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="doc-header">
            <h2>Jadwal Piket KBM Semester Ganjil</h2>
            <h3>SMK Negeri 1 Boyolangu Tahun Pelajaran 2026 - 2027</h3>
        </div>

        <!-- SIKLUS A -->
        <div class="section-badge">SIKLUS A (Minggu Ganjil : Minggu Ke-1, 3, 5)</div>
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Hari</th>
                    <th style="width: 280px;">Petugas Piket KBM Pagi<br>( 07.00 s.d 11.00 )</th>
                    <th style="width: 170px;">Koordinator Piket Pagi<br>( 07.00 s.d 11.00 )</th>
                    <th style="width: 280px;">Petugas Piket KBM Siang<br>( 11.00 s.d 15.00 )</th>
                    <th style="width: 170px;">Koordinator Piket Siang<br>( 11.00 s.d 15.00 )</th>
                    <th style="width: 160px;">Piket Waka</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hariOrder as $h)
                @php $row = $siklusAData[$h] ?? null; @endphp
                <tr>
                    <td style="text-align: center; font-weight: 800; vertical-align: middle;">{{ $h }}</td>
                    <td>
                        <ol>
                            @foreach($row['pagi_petugas'] as $p)
                                <li>{{ $p->nama_guru }}</li>
                            @endforeach
                        </ol>
                    </td>
                    <td style="vertical-align: middle;"><strong>{{ $row['pagi_koordinator']->nama_guru ?? '-' }}</strong></td>
                    <td>
                        <ol>
                            @foreach($row['siang_petugas'] as $p)
                                <li>{{ $p->nama_guru }}</li>
                            @endforeach
                        </ol>
                    </td>
                    <td style="vertical-align: middle;"><strong>{{ $row['siang_koordinator']->nama_guru ?? '-' }}</strong></td>
                    <td style="vertical-align: middle; text-align: center;"><strong>{{ $row['waka']->piket_waka_nama ?? '-' }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- SIKLUS B -->
        <div class="section-badge">SIKLUS B (Minggu Genap : Minggu Ke-2, 4)</div>
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Hari</th>
                    <th style="width: 280px;">Petugas Piket KBM Pagi<br>( 07.00 s.d 11.00 )</th>
                    <th style="width: 170px;">Koordinator Piket Pagi<br>( 07.00 s.d 11.00 )</th>
                    <th style="width: 280px;">Petugas Piket KBM Siang<br>( 11.00 s.d 15.00 )</th>
                    <th style="width: 170px;">Koordinator Piket Siang<br>( 11.00 s.d 15.00 )</th>
                    <th style="width: 160px;">Piket Waka</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hariOrder as $h)
                @php $row = $siklusBData[$h] ?? null; @endphp
                <tr>
                    <td style="text-align: center; font-weight: 800; vertical-align: middle;">{{ $h }}</td>
                    <td>
                        <ol>
                            @foreach($row['pagi_petugas'] as $p)
                                <li>{{ $p->nama_guru }}</li>
                            @endforeach
                        </ol>
                    </td>
                    <td style="vertical-align: middle;"><strong>{{ $row['pagi_koordinator']->nama_guru ?? '-' }}</strong></td>
                    <td>
                        <ol>
                            @foreach($row['siang_petugas'] as $p)
                                <li>{{ $p->nama_guru }}</li>
                            @endforeach
                        </ol>
                    </td>
                    <td style="vertical-align: middle;"><strong>{{ $row['siang_koordinator']->nama_guru ?? '-' }}</strong></td>
                    <td style="vertical-align: middle; text-align: center;"><strong>{{ $row['waka']->piket_waka_nama ?? '-' }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signature -->
        <div class="sign-wrapper">
            <div class="sign-box">
                <div class="sign-date">Tulungagung, 10 Juli 2026</div>
                <div class="sign-role">Kepala SMK Negeri 1 Boyolangu,</div>
                <div class="sign-name">{{ $namaKepalaSekolah }}</div>
                <div class="sign-nip">NIP. {{ $nipKepalaSekolah }}</div>
            </div>
        </div>
    </div>

</body>
</html>
