<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengiriman {{ $jenis === 'ongkir' ? 'Biaya Ongkir' : 'Volume Dokumen' }}</title>
    <style>
        @page { size: A4 landscape; margin: 15px; }
        body { font-family: sans-serif; font-size: 10px; margin: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .right { text-align: right; }
        .grandtotal { background-color: #fef08a; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pengiriman {{ $jenis === 'ongkir' ? 'Biaya Ongkir' : 'Volume Dokumen' }}</h2>
        <p>Periode Pelaporan: {{ $month == 'semua' ? 'Semua Bulan' : $month }} {{ $year == 'semua' ? 'Semua Tahun' : $year }}</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @if($jenis === 'ongkir')
                    <th>Total Ongkir Dalam Negeri</th>
                    <th>Total Ongkir Luar Negeri</th>
                @else
                    <th>Penerimaan mailroom</th>
                    <th>Pengiriman dalam negeri</th>
                    <th>Pengiriman luar negeri</th>
                    <th>Registrasi Surat Masuk via DOF</th>
                    @if(isset($kolomDinamis))
                        @foreach($kolomDinamis as $kolom)
                            <th>{{ $kolom->nama_kolom }}</th>
                        @endforeach
                    @endif
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($records as $row)
                <tr>
                    <td>{{ $row->tahun }}</td>
                    <td>{{ $row->bulan }}</td>
                    @if($jenis === 'ongkir')
                        <td class="right">Rp {{ number_format($row->ongkir_dalam_negeri, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($row->ongkir_luar_negeri, 0, ',', '.') }}</td>
                    @else
                        @php $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []); @endphp
                        <td>{{ number_format($row->penerimaan_mailroom, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->pengiriman_dalam_negeri, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->pengiriman_luar_negeri, 0, ',', '.') }}</td>
                        <td>{{ number_format($row->registrasi_surat_masuk_dof, 0, ',', '.') }}</td>
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td>{{ $tambahan[$kolom->nama_kolom] ?? '' }}</td>
                            @endforeach
                        @endif
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $jenis === 'ongkir' ? 4 : (6 + (isset($kolomDinamis) ? count($kolomDinamis) : 0)) }}">Data kosong untuk periode tersebut.</td>
                </tr>
            @endforelse

            @if($jenis === 'ongkir' && count($records) > 0)
                <tr class="grandtotal">
                    <td colspan="2" class="right">Total Keseluruhan :</td>
                    <td class="right">Rp {{ number_format($totalDomestik, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($totalInternasional, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
