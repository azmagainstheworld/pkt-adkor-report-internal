<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan PA Tekstual Tabel {{ $kelompok }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ea580c; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #ea580c; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background-color: #ea580c; color: white; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .total-row { background-color: #f3f4f6; font-weight: bold; }
        .section-title { font-size: 12px; font-weight: bold; margin-bottom: 8px; color: #1f2937; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pengelolaan Dokumen Tekstual - Tabel {{ $kelompok }}</h2>
        <p>Waktu Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
    </div>

    <div class="section-title">Data Laporan Dokumen Tekstual</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Tahun</th>
                <th style="width: 60px;">Bulan</th>
                @foreach($masterTabel as $m)
                    <th>{{ $m->nama_dokumen }}</th>
                @endforeach
                @if(isset($kolomTabel))
                    @foreach($kolomTabel as $k)
                        <th>{{ $k->nama_kolom }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($dataTable as $row)
                <tr>
                    <td class="center">{{ $row['tahun'] }}</td>
                    <td class="center">{{ $row['bulan'] }}</td>
                    @foreach($masterTabel as $m)
                        <td class="center">{{ $row['items'][$m->id] ?? 0 }}</td>
                    @endforeach
                    @if(isset($kolomTabel))
                        @foreach($kolomTabel as $k)
                            <td class="center">
                                @if($k->tipe_input === 'currency' && isset($row['data_tambahan'][$k->nama_kolom]))
                                    Rp {{ number_format((float)$row['data_tambahan'][$k->nama_kolom], 0, ',', '.') }}
                                @else
                                    {{ $row['data_tambahan'][$k->nama_kolom] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($masterTabel) + (isset($kolomTabel) ? count($kolomTabel) : 0) + 2 }}" class="center">Data kosong pada periode yang dipilih.</td>
                </tr>
            @endforelse

            @if(count($dataTable) > 0)
                <tr class="total-row">
                    <td colspan="2" class="center">TOTAL KESELURUHAN</td>
                    @foreach($masterTabel as $m)
                        <td class="center">{{ number_format($totals[$m->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    @if(isset($kolomTabel))
                        @foreach($kolomTabel as $k)
                            <td></td>
                        @endforeach
                    @endif
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
