<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jasa Fotocopy Detail</title>
    <style>
        @page { size: A4 landscape; margin: 10px; }
        body { font-family: sans-serif; font-size: 8px; margin: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .header { text-align: center; margin-bottom: 15px; }
        .section-title { font-weight: bold; font-size: 10px; background-color: #d1d5db; padding: 5px; border: 1px solid #333; margin-top: 15px;}
        .right { text-align: right; }
        .subtotal { background-color: #e0f2fe; font-weight: bold; }
        .grandtotal { background-color: #fef08a; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Detail Jasa Fotocopy</h2>
        <p>Periode Pelaporan: {{ $filterBulan == 'semua' ? 'Semua Bulan' : $filterBulan }} {{ $filterTahun == 'semua' ? 'Semua Tahun' : $filterTahun }}</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    @php
        $lblBln = ($filterBulan === 'semua') ? '(Bln Terkait)' : 'bln ' . $filterBulan;
        $lblSdBln = ($filterBulan === 'semua') ? '(s.d. Bln Terkait)' : 's.d. bln ' . $filterBulan;
        $lblBlnOnly = ($filterBulan === 'semua') ? 'Bln Terkait' : $filterBulan;
        $lastGroupKey = null;
        $tampilkanSubtotalGrup = count($subtotalGroups) > 1;
    @endphp

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>UNIT KERJA</th>
                <th>Cost Centre</th>
                <th>Jlh pemakaian<br>{{ $lblBln }}</th>
                <th>Jlh pemakaian<br>{{ $lblSdBln }}</th>
                <th>Ket.</th>
                <th>Type mesin</th>
                <th>Biaya fee bulan<br>{{ $lblBlnOnly }}</th>
                <th>Biaya fee s.d. bulan<br>{{ $lblBlnOnly }}</th>
                <th>Biaya fee/Lbr</th>
                <th>Biaya sewa/bulan</th>
                <th>Biaya Jasa Sewa bln & Fee<br>{{ $lblBlnOnly }}</th>
                <th>Total biaya Sewa & Fee s.d. bln<br>{{ $lblBlnOnly }}</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($dataTable2 as $row)
                @php
                    $groupKey = $row['tahun'] . '|' . $row['bulan'];
                    $mulaiGrupBaru = $tampilkanSubtotalGrup && $lastGroupKey !== null && $lastGroupKey !== $groupKey;
                @endphp

                @if($mulaiGrupBaru)
                    @php $st = $subtotalGroups[$lastGroupKey]['totals']; @endphp
                    <tr class="subtotal">
                        <td colspan="5" class="right">Subtotal {{ $subtotalGroups[$lastGroupKey]['bulan'] }} {{ $subtotalGroups[$lastGroupKey]['tahun'] }} :</td>
                        <td>{{ number_format($st['pemakaian_bln'], 0, ',', '.') }}</td>
                        <td>{{ number_format($st['pemakaian_sd'], 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                        <td>{{ number_format($st['fee_bln'], 0, ',', '.') }}</td>
                        <td>{{ number_format($st['fee_sd'], 0, ',', '.') }}</td>
                        <td></td>
                        <td>{{ number_format($st['sewa_bln'], 0, ',', '.') }}</td>
                        <td>{{ number_format($st['total_bln'], 0, ',', '.') }}</td>
                        <td>{{ number_format($st['total_sd'], 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $row['tahun'] }}</td>
                    <td>{{ $row['bulan'] }}</td>
                    <td>{{ $row['unit_kerja'] }}</td>
                    <td>{{ $row['cost_centre'] }}</td>
                    <td class="right">{{ number_format($row['pemakaian_bln_ini'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['pemakaian_sd'], 0, ',', '.') }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                    <td>{{ $row['tipe_mesin'] }}</td>
                    <td class="right">{{ number_format($row['fee_bln_ini'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['fee_sd'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['fee_per_lbr'], 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['sewa_bln_ini'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['total_bln_ini'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['total_sd'], 0, ',', '.') }}</td>
                </tr>

                @php $lastGroupKey = $groupKey; @endphp
            @empty
                <tr><td colspan="15">Data kosong untuk periode tersebut.</td></tr>
            @endforelse

            @if(count($dataTable2) > 0 && $tampilkanSubtotalGrup && $lastGroupKey !== null)
                @php $st = $subtotalGroups[$lastGroupKey]['totals'] ?? null; @endphp
                @if($st)
                <tr class="subtotal">
                    <td colspan="5" class="right">Subtotal {{ $subtotalGroups[$lastGroupKey]['bulan'] }} {{ $subtotalGroups[$lastGroupKey]['tahun'] }} :</td>
                    <td>{{ number_format($st['pemakaian_bln'], 0, ',', '.') }}</td>
                    <td>{{ number_format($st['pemakaian_sd'], 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                    <td>{{ number_format($st['fee_bln'], 0, ',', '.') }}</td>
                    <td>{{ number_format($st['fee_sd'], 0, ',', '.') }}</td>
                    <td></td>
                    <td>{{ number_format($st['sewa_bln'], 0, ',', '.') }}</td>
                    <td>{{ number_format($st['total_bln'], 0, ',', '.') }}</td>
                    <td>{{ number_format($st['total_sd'], 0, ',', '.') }}</td>
                </tr>
                @endif
            @endif

            @if(count($dataTable2) > 0)
                <tr class="grandtotal">
                    <td colspan="5" class="right">GRAND TOTAL {{ $tampilkanSubtotalGrup ? '(SEMUA GRUP)' : '' }}</td>
                    <td>{{ number_format($grandTotals['pemakaian_bln'], 0, ',', '.') }}</td>
                    <td>{{ number_format($grandTotals['pemakaian_sd'], 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                    <td>{{ number_format($grandTotals['fee_bln'], 0, ',', '.') }}</td>
                    <td>{{ number_format($grandTotals['fee_sd'], 0, ',', '.') }}</td>
                    <td></td>
                    <td>{{ number_format($grandTotals['sewa_bln'], 0, ',', '.') }}</td>
                    <td>{{ number_format($grandTotals['total_bln'], 0, ',', '.') }}</td>
                    <td>{{ number_format($grandTotals['total_sd'], 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
