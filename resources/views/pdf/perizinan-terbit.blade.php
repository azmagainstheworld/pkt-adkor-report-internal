<!DOCTYPE html>
<html>
<head>
    <title>Rekap Perizinan Terbit</title>
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
        <h2>Daftar Perizinan Terbit Administrasi Korporat</h2>
        <p>Periode: <strong>{{ $bulan == 'semua' ? 'Semua Bulan' : $bulan }} {{ $tahun == 'semua' ? 'Semua Tahun' : $tahun }}</strong> | Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Perizinan</th>
                <th>Nomor</th>
                <th>Terbit</th>
                <th>Berakhir</th>
                <th>Instansi Penerbit</th>
                <th>Kegiatan</th>
                @foreach($kolomDinamis as $kolom)
                    <th>{{ $kolom->nama_kolom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($dataRincian as $index => $item)
                @php $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []); @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_perizinan }}</td>
                    <td>{{ $item->nomor }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_sejak)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_akhir)->format('d/m/Y') }}</td>
                    <td>{{ $item->instansi_penerbit }}</td>
                    <td class="text-center">{{ $item->kegiatan }}</td>
                    @foreach($kolomDinamis as $kolom)
                        <td>{{ $tambahan[$kolom->nama_kolom] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
