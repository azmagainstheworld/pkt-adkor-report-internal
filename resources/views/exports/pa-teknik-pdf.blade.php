<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan PA Teknik</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #1a1a1a; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #EA580C; padding-bottom: 8px; }
        .header h1 { font-size: 14px; font-weight: bold; color: #EA580C; }
        .header p { font-size: 9px; color: #555; margin-top: 2px; }
        .section-title { font-size: 11px; font-weight: bold; color: #fff; background: #EA580C;
            padding: 5px 10px; border-radius: 4px 4px 0 0; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #FEF3C7; color: #92400E; font-weight: bold; padding: 5px 6px;
            border: 1px solid #FCD34D; text-align: center; font-size: 8px; }
        td { padding: 4px 6px; border: 1px solid #E5E7EB; text-align: center; vertical-align: middle; }
        tr:nth-child(even) td { background: #FFF7ED; }
        .no-data { text-align: center; color: #9CA3AF; padding: 12px; font-style: italic; border: 1px solid #E5E7EB; }
        .footer { margin-top: 20px; font-size: 8px; color: #9CA3AF; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PUSAT ARSIP (PA) TEKNIK</h1>
        <p>
            PT Pupuk Kalimantan Timur &nbsp;|&nbsp;
            Periode: {{ $filterTahun === 'semua' ? 'Semua Tahun' : $filterTahun }}
            @if($filterBulan !== 'semua') - {{ $filterBulan }} @endif
            &nbsp;|&nbsp; Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </p>
    </div>

    {{-- TABEL 1 --}}
    <div class="section-title">Tabel 1 - Data Kearsipan PA Teknik</div>
    @if($rowsTabel1 && count($rowsTabel1) > 0)
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th style="width:40px">Tahun</th>
                <th style="width:50px">Bulan</th>
                @foreach($masterTabel1 as $m)
                    <th>{{ $m->nama_kegiatan }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rowsTabel1 as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['tahun'] }}</td>
                <td>{{ $row['bulan'] }}</td>
                @foreach($masterTabel1 as $m)
                    <td>{{ $row['items'][$m->id] ?? 0 }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p class="no-data">Tidak ada data untuk Tabel 1.</p>
    @endif

    {{-- TABEL 2 --}}
    <div class="section-title">Tabel 2 - Data Kearsipan PA Teknik</div>
    @if($rowsTabel2 && count($rowsTabel2) > 0)
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th style="width:40px">Tahun</th>
                <th style="width:50px">Bulan</th>
                @foreach($masterTabel2 as $m)
                    <th>{{ $m->nama_kegiatan }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rowsTabel2 as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['tahun'] }}</td>
                <td>{{ $row['bulan'] }}</td>
                @foreach($masterTabel2 as $m)
                    <td>{{ $row['items'][$m->id] ?? 0 }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p class="no-data">Tidak ada data untuk Tabel 2.</p>
    @endif

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem &mdash; AdkorReport PKT
    </div>
</body>
</html>
