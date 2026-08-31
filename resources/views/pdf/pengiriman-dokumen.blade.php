<!DOCTYPE html>
<html>
<head>
    <title>Rekap Pengiriman Dokumen</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16px; color: #0056A3; }
        .header p { margin: 3px 0; font-size: 10px; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        th, td { border: 1px solid #999; padding: 6px; text-align: center; vertical-align: middle; }
        th { background-color: #f2f2f2; font-weight: bold; }
        
        .bg-gray { background-color: #e5e7eb; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI VOLUME & BIAYA PENGIRIMAN DOKUMEN</h2>
        <p>Periode: <strong>{{ $month == 'semua' ? 'Semua Bulan' : $month }} {{ $year == 'semua' ? 'Semua Tahun' : $year }}</strong></p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 10%;">Periode</th>
                <th colspan="4">Volume Dokumen (Item)</th>
                <th colspan="2">Biaya Ongkir (Rp)</th>
                @if(isset($kolomDinamis) && count($kolomDinamis) > 0)
                    <th colspan="{{ count($kolomDinamis) }}">Atribut Tambahan</th>
                @endif
            </tr>
            <tr>
                <!-- Volume -->
                <th>Mailroom</th>
                <th>Reg. Surat Masuk</th>
                <th>Dalam Negeri</th>
                <th>Luar Negeri</th>
                <!-- Biaya -->
                <th>Dalam Negeri</th>
                <th>Luar Negeri</th>
                <!-- Dinamis -->
                @if(isset($kolomDinamis) && count($kolomDinamis) > 0)
                    @foreach($kolomDinamis as $kolom)
                        <th>{{ $kolom->nama_kolom }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1; 
                $totDomestik = 0;
                $totInternasional = 0;
            @endphp
            @forelse($costRecords as $item)
                @php 
                    $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []); 
                    $totDomestik += $item->ongkir_dalam_negeri;
                    $totInternasional += $item->ongkir_luar_negeri;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->bulan }}<br>{{ $item->tahun }}</td>
                    <td>{{ number_format($item->penerimaan_mailroom, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->registrasi_surat_masuk_dof, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->pengiriman_dalam_negeri, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->pengiriman_luar_negeri, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->ongkir_dalam_negeri, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->ongkir_luar_negeri, 0, ',', '.') }}</td>
                    
                    @if(isset($kolomDinamis) && count($kolomDinamis) > 0)
                        @foreach($kolomDinamis as $kolom)
                            <td class="{{ $kolom->tipe_input == 'number' || $kolom->tipe_input == 'currency' ? 'text-right' : '' }}">
                                @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                    {{ number_format($tambahan[$kolom->nama_kolom], 0, ',', '.') }}
                                @else
                                    {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 8 + (isset($kolomDinamis) ? count($kolomDinamis) : 0) }}" style="text-align: center; padding: 20px;">
                        Tidak ada data ditemukan pada filter terpilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        
        <!-- FOTOER HANYA MUNCUL JIKA ADA DATA -->
        @if($costRecords->count() > 0)
        <tfoot>
            <tr class="bg-gray">
                <td colspan="6" class="text-right">GRAND TOTAL KESELURUHAN (RP):</td>
                <td class="text-right">{{ number_format($totDomestik, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totInternasional, 0, ',', '.') }}</td>
                
                <!-- Spacer jika ada kolom dinamis -->
                @if(isset($kolomDinamis) && count($kolomDinamis) > 0)
                    <td colspan="{{ count($kolomDinamis) }}"></td>
                @endif
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
