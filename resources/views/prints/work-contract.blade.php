<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kontrak Kerja - {{ $record->contract_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #111; line-height: 1.5; padding: 20px; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h3 { margin: 0; text-transform: uppercase; font-size: 15px; text-decoration: underline; }
        .table-data { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .table-data td { padding: 4px 0; vertical-align: top; }
        .box { background: #fafafa; border: 1px solid #ddd; padding: 8px 12px; margin: 8px 0; }
    </style>
</head>
<body>
    <div class="title">
        <h3>SURAT PERJANJIAN KERJA SAMA / KONTRAK</h3>
        <p style="margin: 2px 0 0 0;">Nomor: {{ $record->contract_number }}</p>
    </div>

    <p>Pada hari ini, disepakati perjanjian kerja sama antara:</p>

    <table class="table-data">
        <tr>
            <td style="width: 28%; font-weight: bold;">Pemberi Tugas / Mitra</td>
            <td style="width: 3%;">:</td>
            <td>{{ $record->client ? $record->client->name : 'Pihak Mitra' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pelaksana Tugas</td>
            <td>:</td>
            <td>Miftakhur Rokhmat</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Judul Pekerjaan</td>
            <td>:</td>
            <td>{{ $record->job_title }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Periode Kontrak</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($record->start_date)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($record->end_date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nilai Kesepakatan</td>
            <td>:</td>
            <td>{{ $record->contract_value ? 'Rp ' . number_format($record->contract_value, 0, ',', '.') : 'Tentatif / Mengikuti Progres' }}</td>
        </tr>
    </table>

    <p><strong>Ruang Lingkup Pekerjaan:</strong></p>
    <div class="box">{!! nl2br(e($record->scope_of_work)) !!}</div>

    @if($record->notes)
    <p><strong>Catatan Tambahan & Pembayaran:</strong></p>
    <div class="box">{!! nl2br(e($record->notes)) !!}</div>
    @endif

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 50%; text-align: center;">
                Pihak Mitra,<br><br><br><br>
                <strong>( {{ $record->client ? $record->client->name : 'Mitra' }} )</strong>
            </td>
            <td style="width: 50%; text-align: center;">
                Pelaksana,<br><br><br><br>
                <strong>Miftakhur Rokhmat</strong>
            </td>
        </tr>
    </table>
</body>
</html>