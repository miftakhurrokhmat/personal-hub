<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Tugas - {{ $record->letter_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #111; line-height: 1.5; padding: 20px; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 25px; }
        .kop h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .kop p { margin: 2px 0 0 0; font-size: 11px; color: #444; }
        .title-box { text-align: center; margin-bottom: 25px; }
        .title-box h3 { margin: 0; text-decoration: underline; font-size: 15px; }
        .title-box p { margin: 2px 0 0 0; font-size: 12px; }
        .content { margin-bottom: 20px; text-align: justify; }
        .table-info { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .table-info td { vertical-align: top; padding: 4px 0; }
        .ttd-box { width: 100%; margin-top: 40px; }
        .ttd { float: right; width: 220px; text-align: center; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>PERSONAL HUB MANAGEMENT</h2>
        <p>Konsultan IT & Sistem Operasional Terpadu</p>
    </div>

    <div class="title-box">
        <h3>SURAT TUGAS</h3>
        <p>Nomor: {{ $record->letter_number }}</p>
    </div>

    <div class="content">
        <p>Dengan ini memberikan penugasan kepada personel berikut:</p>
        
        <table class="table-info">
            <tr>
                <td style="width: 25%; font-weight: bold;">Personel Ditugaskan</td>
                <td style="width: 3%;">:</td>
                <td>{!! nl2br(e($record->assigned_personnel)) !!}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Perihal Penugasan</td>
                <td>:</td>
                <td>{{ $record->assignment_title }}</td>
            </tr>
            @if($record->project)
            <tr>
                <td style="font-weight: bold;">Proyek Terkait</td>
                <td>:</td>
                <td>{{ $record->project->title }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight: bold;">Jadwal Pelaksanaan</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($record->start_date)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($record->end_date)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Lokasi Tugas</td>
                <td>:</td>
                <td>{{ $record->location ?? 'Sesuai Arahan / Daring' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tugas & Kewajiban</td>
                <td>:</td>
                <td>{!! nl2br(e($record->assignment_duty)) !!}</td>
            </tr>
        </table>

        <p>Demikian surat tugas ini diterbitkan untuk dilaksanakan dengan penuh rasa tanggung jawab.</p>
    </div>

    <table class="ttd-box">
        <tr>
            <td style="width: 60%;"></td>
            <td class="ttd">
                <p>{{ \Carbon\Carbon::parse($record->start_date)->translatedFormat('d F Y') }}<br>Pemberi Tugas,</p>
                <div style="height: 60px;"></div>
                <strong>Miftakhur Rokhmat</strong>
            </td>
        </tr>
    </table>
</body>
</html>