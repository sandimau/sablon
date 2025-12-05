<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Pembayaran - {{ $freelance->nama }} - {{ $namaBulan }} {{ $thn }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #f5f5f5;
        }
        .slip-container {
            width: 210mm;
            min-height: 148mm;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        .period-badge {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-left, .info-right {
            width: 48%;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background: #e9ecef;
        }
        .summary-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-row.total {
            border-top: 2px solid #333;
            padding-top: 5px;
            margin-top: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-top: 60px;
            margin-bottom: 5px;
        }
        .attendance-summary {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        @media print {
            body {
                background: white;
            }
            .slip-container {
                margin: 0;
                padding: 15px;
                box-shadow: none;
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; padding: 10px; background: #333; color: white;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px;">
            🖨️ Print Slip
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            ✖️ Tutup
        </button>
    </div>

    <div class="slip-container">
        <!-- Header -->
        <div class="header">
            <h1>Slip Pembayaran Freelance</h1>
            <h2>Periode: <span class="period-badge">{{ $namaBulan }} {{ $thn }}</span></h2>
        </div>

        <!-- Info Freelance -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-row">
                    <span class="info-label">Nama</span>
                    <span class="info-value">: {{ $freelance->nama }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Bank</span>
                    <span class="info-value">: {{ $freelance->bank ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Rekening</span>
                    <span class="info-value">: {{ $freelance->nomor_rekening ?? '-' }}</span>
                </div>
            </div>
            <div class="info-right">
                <div class="info-row">
                    <span class="info-label">Tanggal Cetak</span>
                    <span class="info-value">: {{ now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Upah/Hari</span>
                    <span class="info-value">: Rp {{ number_format($freelance->upah, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Rate Lembur/Jam</span>
                    <span class="info-value">: Rp {{ number_format($freelance->rate_lembur_per_jam, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Kehadiran Summary -->
        <div class="attendance-summary">
            <strong>📅 Total Kehadiran:</strong> {{ $absensis->count() }} hari kerja di bulan {{ $namaBulan }} {{ $thn }}
        </div>

        <!-- Detail Pendapatan -->
        <table>
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-right">Total Dibayar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Upah</strong> (Rp {{ number_format($freelance->upah, 0, ',', '.') }}/hari)</td>
                    <td class="text-center">{{ $jumlahHariKerja }} hari</td>
                    <td class="text-right text-success">Rp {{ number_format($totalBayarUpah, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Lembur</strong> (Rp {{ number_format($freelance->rate_lembur_per_jam, 0, ',', '.') }}/jam)</td>
                    <td class="text-center">{{ $totalJamLembur }} jam</td>
                    <td class="text-right text-success">Rp {{ number_format($totalBayarLembur, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2"><strong>Sub Total</strong></td>
                    <td class="text-right text-success"><strong>Rp {{ number_format($totalDibayar, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Potongan -->
        @if($kasbonBulanIni > 0)
        <div class="summary-box">
            <div style="font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Potongan</div>
            <div class="summary-row">
                <span>Potongan Kasbon</span>
                <span class="text-danger">- Rp {{ number_format($kasbonBulanIni, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        <!-- Total Diterima -->
        <div class="summary-box" style="background: #d4edda; border-color: #c3e6cb;">
            <div class="summary-row total">
                <span style="font-size: 16px;">💰 TOTAL DITERIMA</span>
                <span class="text-success" style="font-size: 18px;">Rp {{ number_format($totalBersih, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer Tanda Tangan -->
        <div class="footer">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <div class="signature-line"></div>
                <p><strong>Manager</strong></p>
            </div>
            <div class="signature-box">
                <p>Diterima oleh,</p>
                <div class="signature-line"></div>
                <p><strong>{{ $freelance->nama }}</strong></p>
            </div>
        </div>

        <!-- Catatan -->
        <div style="margin-top: 20px; padding: 10px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; font-size: 11px;">
            <strong>Catatan:</strong>
            <ul style="margin: 5px 0 0 20px;">
                <li>Slip ini merupakan bukti pembayaran yang sah.</li>
                <li>Harap disimpan dengan baik untuk keperluan administrasi.</li>
            </ul>
        </div>
    </div>
</body>
</html>

