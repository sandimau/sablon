<!DOCTYPE html>
<html>
<head>
    <title>Bukti Pembayaran Gaji - {{ $pembayaran->freelance->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .info-section {
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .detail-table th {
            background-color: #f0f0f0;
            text-align: left;
            font-weight: bold;
        }
        .detail-table td.number {
            text-align: right;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .grand-total-row {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 14px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 30%;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Cetak</button>

    <div class="header">
        <h2>BUKTI PEMBAYARAN GAJI FREELANCE</h2>
        <p>Periode: {{ \Carbon\Carbon::create()->month($pembayaran->bulan)->translatedFormat('F') }} {{ $pembayaran->tahun }}</p>
        <p>No. Transaksi: #PBY-{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>Nama Freelance</td>
                <td>: {{ $pembayaran->freelance->nama }}</td>
                <td>Tanggal Pembayaran</td>
                <td>: {{ $pembayaran->tanggal->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Bank</td>
                <td>: {{ $pembayaran->freelance->bank ?? '-' }}</td>
                <td>Periode</td>
                <td>: {{ \Carbon\Carbon::create()->month($pembayaran->bulan)->translatedFormat('F') }} {{ $pembayaran->tahun }}</td>
            </tr>
            <tr>
                <td>No. Rekening</td>
                <td>: {{ $pembayaran->freelance->nomor_rekening ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="detail-table">
        <thead>
            <tr>
                <th width="50%">Keterangan</th>
                <th width="50%" class="number">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Upah Kerja</td>
                <td class="number">{{ number_format($pembayaran->total_upah, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Lembur</td>
                <td class="number">{{ number_format($pembayaran->total_lembur, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Sub Total</td>
                <td class="number">{{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</td>
            </tr>
            @if($pembayaran->potongan_kasbon > 0)
            <tr>
                <td>Potongan Kasbon</td>
                <td class="number">({{ number_format($pembayaran->potongan_kasbon, 0, ',', '.') }})</td>
            </tr>
            @endif
            <tr class="grand-total-row">
                <td>TOTAL DITERIMA</td>
                <td class="number">{{ number_format($pembayaran->total_keluar, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($pembayaran->keterangan)
    <div style="margin: 20px 0;">
        <strong>Keterangan:</strong>
        <p style="margin: 5px 0;">{{ $pembayaran->keterangan }}</p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-line">
                <p>Admin/Finance</p>
            </div>
        </div>
        <div class="signature-box">
            <p>Disetujui Oleh,</p>
            <div class="signature-line">
                <p>Manager</p>
            </div>
        </div>
        <div class="signature-box">
            <p>Diterima Oleh,</p>
            <div class="signature-line">
                <p>{{ $pembayaran->freelance->nama }}</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d F Y H:i:s') }}</p>
        <p>Bukti pembayaran ini sah dan dapat dipertanggungjawabkan</p>
    </div>
</body>
</html>

