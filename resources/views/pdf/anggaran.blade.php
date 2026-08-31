<!DOCTYPE html>
<html>
<head>
    <title>Rekap Anggaran</title>
    <style>
        body { font-family: sans-serif; font-size: 9px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; vertical-align: middle; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #0056A3; font-size: 16px; }
        .header p { margin: 4px 0 0 0; font-size: 10px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI ANGGARAN ADMINISTRASI KORPORAT</h2>
        <p>Periode: <strong>{{ $month == 'all' ? 'Semua Bulan' : $month }} {{ $year == 'all' ? 'Semua Tahun' : $year }}</strong> | Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 5%;">Tahun</th>
                <th style="width: 8%;">Bulan</th>
                <th style="width: 20%;">Detail Anggaran</th>
                <th>RKAP</th>
                <th>Komitmen</th>
                <th>Realisasi</th>
                <th>Real + Komit</th>
                <th>% R+K</th>
                <th>Sisa Anggaran</th>
                <th>% Sisa</th>
                @foreach($kolomDinamis as $kolom)
                    <th>{{ $kolom->nama_kolom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($anggaran as $index => $item)
                @php 
                    $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []); 
                    $realPlusKomit = $item->realisasi + $item->komitmen;
                    $sisa = $item->rkap - $realPlusKomit;
                    $percRealKomit = $item->rkap > 0 ? round(($realPlusKomit / $item->rkap) * 100, 1) : 0;
                    $percSisa = $item->rkap > 0 ? round(($sisa / $item->rkap) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->tahun }}</td>
                    <td class="text-center">{{ $item->bulan }}</td>
                    <td>{{ $item->detail_anggaran }}</td>
                    <td class="text-right">{{ number_format($item->rkap, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->komitmen, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->realisasi, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($realPlusKomit, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $percRealKomit }}%</td>
                    <td class="text-right">{{ number_format($sisa, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $percSisa }}%</td>
                    @foreach($kolomDinamis as $kolom)
                        <td class="{{ $kolom->tipe_input == 'number' || $kolom->tipe_input == 'currency' ? 'text-right' : 'text-center' }}">
                            @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                {{ number_format($tambahan[$kolom->nama_kolom], 0, ',', '.') }}
                            @else
                                {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 11 + count($kolomDinamis) }}" class="text-center" style="padding: 20px;">
                        Tidak ada data anggaran pada filter terpilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
