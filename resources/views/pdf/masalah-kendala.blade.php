<!DOCTYPE html>
<html>
<head>
    <title>Rekap Masalah dan Kendala</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; vertical-align: top; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Masalah dan Kendala Operasional</h2>
        <p>Periode: <strong>{{ $bulan == 'semua' ? 'Semua Bulan' : $bulan }} {{ $tahun == 'semua' ? 'Semua Tahun' : $tahun }}</strong> | Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">Periode</th>
                <th style="width: 40%;">Masalah / Kendala</th>
                <th style="width: 45%;">Solusi / Tindak Lanjut</th>
                @foreach($kolomDinamis as $kolom)
                    <th>{{ $kolom->nama_kolom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($dataMasalah as $item)
                @php $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []); @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $item->bulan }}<br>{{ $item->tahun }}</td>
                    <td>{!! nl2br(e($item->masalah_kendala)) !!}</td>
                    <td>{!! nl2br(e($item->solusi)) !!}</td>
                    @foreach($kolomDinamis as $kolom)
                        <td>{{ $tambahan[$kolom->nama_kolom] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
