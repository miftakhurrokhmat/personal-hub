<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kwitansi - {{ $record->receipt_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #111;
            padding: 15px;
        }

        .border-wrap {
            border: 2px solid #333;
            padding: 15px;
            border-radius: 6px;
        }

        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 6px;
            margin-bottom: 15px;
        }

        .header table {
            width: 100%;
        }

        .kw-title {
            font-size: 18px;
            font-weight: bold;
        }

        .kw-number {
            font-size: 12px;
            color: #555;
        }

        .body-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .body-table td {
            padding: 6px 0;
            vertical-align: top;
        }

        .amount-box {
            background: #f3f4f6;
            border: 1px dashed #666;
            font-size: 16px;
            font-weight: bold;
            padding: 8px 12px;
            display: inline-block;
        }

        .footer-table {
            width: 100%;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="border-wrap">
        <div class="header">
            <table>
                <tr>
                    <td class="kw-title">KWITANSI PENGELUARAN</td>
                    <td style="text-align: right;" class="kw-number">No: <strong>{{ $record->receipt_number }}</strong></td>
                </tr>
            </table>
        </div>

        <table class="body-table">
            <tr>
                <td style="width: 25%;">Telah Terima Dari</td>
                <td style="width: 3%;">:</td>
                <td><strong>{{ $record->recipient_name }}</strong></td>
            </tr>
            <tr>
                <td>Diserahkan Kepada</td>
                <td>:</td>
                <td><strong>{{ $record->payer_name }}</strong></td>
            </tr>
            <tr>
                <td>Uang Sejumlah</td>
                <td>:</td>
                <td>
                    <div class="amount-box">Rp {{ number_format($record->amount, 0, ',', '.') }}</div>
                </td>
            </tr>
            <tr>
                <td>Untuk Pembayaran</td>
                <td>:</td>
                <td>{{ $record->for_payment }}</td>
            </tr>
        </table>

        <table class="footer-table">
            <tr>
                <td style="width: 60%; vertical-align: bottom;">
                    <span style="font-size: 10px; color: #666;">Dicetak otomatis melalui Personal-Hub</span>
                </td>
                <td style="text-align: center; width: 40%;">
                    <span>{{ \Carbon\Carbon::parse($record->receipt_date)->translatedFormat('d F Y') }}</span><br>
                    <span>Penerima Dana,</span>
                    <div style="height: 50px;"></div>
                    <strong>( {{ $record->payer_name }} )</strong>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>