<!DOCTYPE html>
<html>
<head>
    <title>Rekap Ketidakhadiran</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rekapitulasi Ketidakhadiran Karyawan</h2>
        <p>Periode: <strong>{{ $bulan }} {{ $tahun }}</strong> | Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama</th>
                <th rowspan="2">NPK</th>
                <th rowspan="2">Keterangan</th>
                <th colspan="6">Jumlah Hari</th>
            </tr>
            <tr>
                <th>Dinas</th><th>Cuti</th><th>Izin</th><th>Training</th><th>Dispen</th><th>Detasering</th>
            </tr>
        </thead>
        <tbody>
            @foreach($karyawan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-center">{{ $item->npk }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td class="text-center">{{ $item->dinas ?? 0 }}</td>
                    <td class="text-center">{{ $item->cuti ?? 0 }}</td>
                    <td class="text-center">{{ $item->izin ?? 0 }}</td>
                    <td class="text-center">{{ $item->training ?? 0 }}</td>
                    <td class="text-center">{{ $item->dispensasi ?? 0 }}</td>
                    <td class="text-center">{{ $item->detasering ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
