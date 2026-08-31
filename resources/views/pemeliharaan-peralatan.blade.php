<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rincian Perbaikan Peralatan</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; vertical-align: middle; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .header { text-align: center; margin-bottom: 15px; }
        .section-title { font-weight: bold; font-size: 12px; background-color: #d1d5db; padding: 5px; border: 1px solid #333; margin-top: 15px;}
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rincian Pemeliharaan & Perbaikan Peralatan</h2>
        <p>Periode: {{ $filterBulan == 'semua' ? 'Semua Bulan' : $filterBulan }} {{ $filterTahun == 'semua' ? 'Semua Tahun' : $filterTahun }}</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($masterPeralatan as $master) <th>{{ $master->nama_peralatan }}</th> @endforeach
                @if(isset($kolomPeralatan)) @foreach($kolomPeralatan as $k) <th>{{ $k->nama_kolom }}</th> @endforeach @endif
            </tr>
        </thead>
        <tbody>
            @forelse($dataTable as $row)
                <tr>
                    <td>{{ $row['tahun'] }}</td>
                    <td>{{ $row['bulan'] }}</td>
                    @foreach($masterPeralatan as $master) <td>{{ $row['items'][$master->id] ?? 0 }}</td> @endforeach
                    @if(isset($kolomPeralatan))
                        @foreach($kolomPeralatan as $kolom)
                            <td>
                                @if($kolom->tipe_input === 'currency' && isset($row['data_tambahan'][$kolom->nama_kolom])) Rp {{ $row['data_tambahan'][$kolom->nama_kolom] }}
                                @else {{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }} @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @empty
                <tr><td colspan="15">Data kosong untuk periode tersebut.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
