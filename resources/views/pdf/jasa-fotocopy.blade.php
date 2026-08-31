<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jasa Fotocopy</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; vertical-align: middle; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .header { text-align: center; margin-bottom: 15px; }
        .section-title { font-weight: bold; font-size: 12px; background-color: #d1d5db; padding: 5px; border: 1px solid #333; margin-top: 15px;}
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rekapitulasi Jasa Fotocopy</h2>
        <p>Periode Pelaporan: {{ $filterBulan == 'semua' ? 'Semua Bulan' : $filterBulan }} {{ $filterTahun == 'semua' ? 'Semua Tahun' : $filterTahun }}</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <div class="section-title">Ringkasan Keseluruhan per Bulan</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Mesin FC</th>
                <th>Jumlah Pemakaian Jasa Penyediaan Fotocopy</th>
                <th>Nilai Jasa Penyediaan Fotocopy</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataTable1 as $row)
                <tr>
                    <td>{{ $row['tahun'] }}</td>
                    <td>{{ $row['bulan'] }}</td>
                    <td>{{ $row['mesin_fc'] }}</td>
                    <td class="right">{{ number_format($row['jumlah_pemakaian'], 0, ',', '.') }}</td>
                    <td class="right">Rp{{ number_format($row['nilai_jasa'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Data kosong untuk periode tersebut.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
