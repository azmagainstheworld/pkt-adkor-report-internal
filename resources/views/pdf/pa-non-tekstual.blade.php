<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan PA Non Tekstual</title>
    <style>
        @page { margin: 20px 25px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #333333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ea580c; padding-bottom: 8px; }
        .header h2 { margin: 0; color: #111827; font-size: 14pt; text-transform: uppercase; }
        .header p { margin: 4px 0 0 0; font-size: 9pt; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: auto; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; font-size: 8pt; }
        th { background-color: #fff7ed; color: #9a3412; font-weight: bold; text-align: center; }
        td.center { text-align: center; }
        .total-row { background-color: #f3f4f6; font-weight: bold; }
        .footer { margin-top: 20px; text-align: right; font-size: 8pt; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pusat Arsip Non Teknik Non Tekstual</h2>
        <p>Filter Periode: Tahun {{ $filterTahun == 'semua' ? 'Semua' : $filterTahun }} | Bulan {{ $filterBulan == 'semua' ? 'Semua' : $filterBulan }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Tahun</th>
                <th style="width: 60px;">Bulan</th>
                @foreach($availableTypes as $type)
                    <th>{{ $type->name }}</th>
                @endforeach
                @if(isset($kolomDinamis))
                    @foreach($kolomDinamis as $kolom)
                        <th>{{ $kolom->nama_kolom }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($dataPa as $row)
                <tr>
                    <td class="center">{{ $row['tahun'] }}</td>
                    <td class="center">{{ $row['bulan'] }}</td>
                    @foreach($availableTypes as $type)
                        <td class="center">{{ $row['items'][$type->id] ?? 0 }}</td>
                    @endforeach
                    @if(isset($kolomDinamis))
                        @foreach($kolomDinamis as $kolom)
                            <td class="center">
                                @if($kolom->tipe_input === 'currency' && isset($row['data_tambahan'][$kolom->nama_kolom]))
                                    Rp {{ $row['data_tambahan'][$kolom->nama_kolom] }}
                                @else
                                    {{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + count($availableTypes) + (isset($kolomDinamis) ? count($kolomDinamis) : 0) }}" class="center">Data tidak ditemukan.</td>
                </tr>
            @endforelse

            @if(count($dataPa) > 0)
                <tr class="total-row">
                    <td colspan="2" class="center">TOTAL</td>
                    @foreach($availableTypes as $type)
                        <td class="center">{{ number_format($grandTotals[$type->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    @if(isset($kolomDinamis))
                        <td colspan="{{ count($kolomDinamis) }}"></td>
                    @endif
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">Dicetak pada: {{ date('d-m-Y H:i:s') }}</div>
</body>
</html>
