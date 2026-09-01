<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan DOF</title>
    <style>
        @page { margin: 20px 25px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #333333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ea580c; padding-bottom: 8px; }
        .header h2 { margin: 0; color: #111827; font-size: 14pt; text-transform: uppercase; }
        .header p { margin: 4px 0 0 0; font-size: 9pt; color: #6b7280; }
        .section-title { font-size: 10pt; font-weight: bold; color: #ea580c; margin-top: 15px; margin-bottom: 6px; text-transform: uppercase; }
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
        <h2>Laporan Digital Office (DOF)</h2>
        <p>Filter Periode: Tahun {{ $filterTahun == 'semua' ? 'Semua Tahun' : $filterTahun }} | Bulan {{ $filterBulan == 'semua' ? 'Semua Bulan' : $filterBulan }}</p>
    </div>

    @if(!isset($kelompok_tabel) || $kelompok_tabel == 1)
    <!-- TABEL 1 -->
    <div class="section-title">1. Laporan Admin DOF (Tabel 1)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Tahun</th>
                <th style="width: 60px;">Bulan</th>
                @foreach($masterTabel1 as $m1) <th>{{ $m1->nama_kegiatan }}</th> @endforeach
                @if(isset($kolomTabel1)) @foreach($kolomTabel1 as $k1) <th>{{ $k1->nama_kolom }}</th> @endforeach @endif
            </tr>
        </thead>
        <tbody>
            @forelse($dataTable1 as $row)
                <tr>
                    <td class="center">{{ $row['tahun'] }}</td>
                    <td class="center">{{ $row['bulan'] }}</td>
                    @foreach($masterTabel1 as $m1) <td class="center">{{ $row['items'][$m1->id] ?? 0 }}</td> @endforeach
                    @if(isset($kolomTabel1))
                        @foreach($kolomTabel1 as $k1)
                            <td class="center">
                                @if($k1->tipe_input === 'currency' && isset($row['data_tambahan'][$k1->nama_kolom])) Rp {{ $row['data_tambahan'][$k1->nama_kolom] }}
                                @else {{ $row['data_tambahan'][$k1->nama_kolom] ?? '-' }} @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ 2 + count($masterTabel1) + (isset($kolomTabel1) ? count($kolomTabel1) : 0) }}" class="center">Data tidak ditemukan.</td></tr>
            @endforelse

            @if(count($dataTable1) > 0)
                <tr class="total-row">
                    <td colspan="2" class="center">TOTAL</td>
                    @foreach($masterTabel1 as $m1) <td class="center">{{ number_format($totalsTabel1[$m1->id] ?? 0, 0, ',', '.') }}</td> @endforeach
                    @if(isset($kolomTabel1)) <td colspan="{{ count($kolomTabel1) }}"></td> @endif
                </tr>
            @endif
        </tbody>
    </table>

    @endif

    @if(!isset($kelompok_tabel) || $kelompok_tabel == 2)
    <!-- TABEL 2 -->
    <div class="section-title">2. Laporan Helpdesk DOF (Tabel 2)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Tahun</th>
                <th style="width: 60px;">Bulan</th>
                @foreach($masterTabel2 as $m2) <th>{{ $m2->nama_kegiatan }}</th> @endforeach
                @if(isset($kolomTabel2)) @foreach($kolomTabel2 as $k2) <th>{{ $k2->nama_kolom }}</th> @endforeach @endif
            </tr>
        </thead>
        <tbody>
            @forelse($dataTable2 as $row)
                <tr>
                    <td class="center">{{ $row['tahun'] }}</td>
                    <td class="center">{{ $row['bulan'] }}</td>
                    @foreach($masterTabel2 as $m2) <td class="center">{{ $row['items'][$m2->id] ?? 0 }}</td> @endforeach
                    @if(isset($kolomTabel2))
                        @foreach($kolomTabel2 as $k2)
                            <td class="center">
                                @if($k2->tipe_input === 'currency' && isset($row['data_tambahan'][$k2->nama_kolom])) Rp {{ $row['data_tambahan'][$k2->nama_kolom] }}
                                @else {{ $row['data_tambahan'][$k2->nama_kolom] ?? '-' }} @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ 2 + count($masterTabel2) + (isset($kolomTabel2) ? count($kolomTabel2) : 0) }}" class="center">Data tidak ditemukan.</td></tr>
            @endforelse

            @if(count($dataTable2) > 0)
                <tr class="total-row">
                    <td colspan="2" class="center">TOTAL</td>
                    @foreach($masterTabel2 as $m2) <td class="center">{{ number_format($totalsTabel2[$m2->id] ?? 0, 0, ',', '.') }}</td> @endforeach
                    @if(isset($kolomTabel2)) <td colspan="{{ count($kolomTabel2) }}"></td> @endif
                </tr>
            @endif
        </tbody>
    </table>

    @endif

    <div class="footer">Dicetak pada: {{ date('d-m-Y H:i:s') }}</div>
</body>
</html>

