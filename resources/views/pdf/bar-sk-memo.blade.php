<!DOCTYPE html>
<html>
<head>
    <title>Rekap BAR SK Memo</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; }
        .header p { margin: 0; font-size: 11px; color: #555; }
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; text-align: left; background-color: #eee; padding: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Rekapitulasi BAR SK Memo</h2>
        <p>Periode: <strong>{{ $filterBulan == 'semua' ? 'Semua Bulan' : $filterBulan }} {{ $filterTahun == 'semua' ? 'Semua Tahun' : $filterTahun }}</strong> | Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    @if($tipe === 'terbit' || $tipe === 'semua')
        <div class="section-title">DATA DOKUMEN TERBIT</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Periode</th>
                    <th>SKD Keputusan Bersama</th>
                    <th>SKD Non Ratifikasi</th>
                    <th>SKD Ratifikasi</th>
                    <th>Memo Direksi</th>
                    <th>BAR Monitoring</th>
                    <th>BAR Manajemen</th>
                    @foreach($kolomTerbit as $kolom)
                        <th>{{ $kolom->nama_kolom }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($data as $row)
                    @if($row->skd_keputusan_bersama_terbit || $row->skd_non_ratifikasi_terbit || $row->skd_ratifikasi_terbit || $row->memo_direksi_terbit || $row->bar_monitoring_terbit || $row->bar_manajemen_terbit || ($row->data_tambahan ?? null))
                    @php $tambahan = is_string($row->data_tambahan ?? null) ? json_decode($row->data_tambahan ?? '', true) : ($row->data_tambahan ?? []); @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $row->bulan }}<br>{{ $row->tahun }}</td>
                        <td>{{ $row->skd_keputusan_bersama_terbit }}</td>
                        <td>{{ $row->skd_non_ratifikasi_terbit }}</td>
                        <td>{{ $row->skd_ratifikasi_terbit }}</td>
                        <td>{{ $row->memo_direksi_terbit }}</td>
                        <td>{{ $row->bar_monitoring_terbit }}</td>
                        <td>{{ $row->bar_manajemen_terbit }}</td>
                        @foreach($kolomTerbit as $kolom)
                            <td>
                                @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                    Rp {{ number_format($tambahan[$kolom->nama_kolom], 0, ',', '.') }}
                                @else
                                    {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="{{ 8 + count($kolomTerbit) }}">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if($tipe === 'proses' || $tipe === 'semua')
        <div class="section-title">DATA DOKUMEN PROSES</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Periode</th>
                    <th>Proses SKD Kep. Bersama</th>
                    <th>Proses SKD Non Ratifikasi</th>
                    <th>Proses SKD Ratifikasi</th>
                    <th>Proses Memo Direksi</th>
                    <th>Proses BAR Monitoring</th>
                    <th>Proses BAR Manajemen</th>
                    @foreach($kolomProses as $kolom)
                        <th>{{ $kolom->nama_kolom }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($data as $row)
                    @if($row->proses_skd_keputusan_bersama || $row->proses_skd_non_ratifikasi || $row->proses_skd_ratifikasi || $row->proses_memo_direksi || $row->proses_bar_monitoring || $row->proses_bar_manajemen || ($row->data_tambahan_proses ?? null))
                    @php $tambahan = is_string($row->data_tambahan_proses ?? null) ? json_decode($row->data_tambahan_proses ?? '', true) : ($row->data_tambahan_proses ?? []); @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $row->bulan }}<br>{{ $row->tahun }}</td>
                        <td>{{ $row->proses_skd_keputusan_bersama }}</td>
                        <td>{{ $row->proses_skd_non_ratifikasi }}</td>
                        <td>{{ $row->proses_skd_ratifikasi }}</td>
                        <td>{{ $row->proses_memo_direksi }}</td>
                        <td>{{ $row->proses_bar_monitoring }}</td>
                        <td>{{ $row->proses_bar_manajemen }}</td>
                        @foreach($kolomProses as $kolom)
                            <td>
                                @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                    Rp {{ number_format($tambahan[$kolom->nama_kolom], 0, ',', '.') }}
                                @else
                                    {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="{{ 8 + count($kolomProses) }}">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>
