<!DOCTYPE html>
<html>
<head>
    <title>Laporan Jasa Kurir</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Statistik Penggunaan Jasa Kurir</h2>
        <p>Periode: <strong>{{ $bulan == 'semua' ? 'Semua Bulan' : $bulan }} {{ $tahun == 'semua' ? 'Semua Tahun' : $tahun }}</strong> | Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($kurirMaster as $kurir)
                    <th>{{ strtoupper($kurir->nama_kurir) }}</th>
                @endforeach
                <th>Total</th>
                @foreach($kolomDinamis as $kolom)
                    <th>{{ $kolom->nama_kolom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($tableData as $index => $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $row['tahun'] }}</td>
                    <td class="text-center">{{ $row['bulan'] }}</td>
                    @foreach($kurirMaster as $kurir)
                        <td class="text-center">{{ $row['kurir_' . $kurir->id] ?? 0 }}</td>
                    @endforeach
                    <td class="text-center font-bold">{{ $row['total_semua'] }}</td>
                    @foreach($kolomDinamis as $kolom)
                        <td>{{ $row['data_tambahan'][$kolom->nama_kolom] ?? '-' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="100%" class="text-center">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
