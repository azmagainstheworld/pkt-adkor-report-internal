<!DOCTYPE html>
<html>
<head>
    <title>Laporan Surat {{ $tahunFilter }} {{ $bulanFilter }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; color: #0056A3; }
        .header p { margin: 2px 0; font-size: 11px; }
        
        .section-title { font-weight: bold; font-size: 13px; margin-top: 15px; margin-bottom: 8px; color: #444; border-left: 4px solid #F7941E; padding-left: 8px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        th, td { border: 1px solid #ccc; padding: 5px; word-wrap: break-word; vertical-align: top;}
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase;}
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Warna Jenis */
        .masuk { color: #0056A3; font-weight: bold; }
        .keluar { color: #F7941E; font-weight: bold; }
        
        /* Badge Status */
        .status-terkirim { color: #047481; background-color: #e6fffa; border: 1px solid #b2f5ea; padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold;}
        .status-batal { color: #c53030; background-color: #fff5f5; border: 1px solid #feb2b2; padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold;}

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN KINERJA PERSURATAN</h2>
        <p>Unit Kerja: Departemen Administrasi & Koordinasi</p>
        <p>Periode: <strong>{{ $tahunFilter == 'semua' ? 'Keseluruhan' : 'Tahun ' . $tahunFilter }} / Bulan: {{ $bulanFilter }}</strong></p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WiB</p>
    </div>

    <!-- TABEL 1 (REKAPITULASI) -->
    <div class="section-title">I. Tabel Akumulasi Laporan Bulanan (Hanya Status Terkirim)</div>
    <table>
        <thead>
            <tr>
                <th width="15%">Tahun</th>
                <th width="25%">Bulan</th>
                <th width="30%">Surat Masuk</th>
                <th width="30%">Surat Keluar</th>
            </tr>
        </thead>
        <tbody>
            @if($rekapData->count() > 0)
                @foreach($rekapData as $rekap)
                <tr>
                    <td class="text-center">{{ $rekap->tahun }}</td>
                    <td class="text-center">{{ $rekap->bulan }}</td>
                    <td class="text-center font-bold text-[#0056A3]">{{ $rekap->total_masuk }}</td>
                    <td class="text-center font-bold text-[#F7941E]">{{ $rekap->total_keluar }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f9fafb;">
                    <td colspan="2" class="text-right font-bold">GRAND TOTAL :</td>
                    <td class="text-center font-bold text-[#0056A3]">{{ $rekapData->sum('total_masuk') }}</td>
                    <td class="text-center font-bold text-[#F7941E]">{{ $rekapData->sum('total_keluar') }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data rekapitulasi.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- TABEL 2 (DETAIL) -->
    <div class="section-title" style="page-break-before: always;">II. Rincian Surat Satuan (Semua Status)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="8%">Tahun</th>
                <th width="12%">Bulan</th>
                <th width="15%">Nomor Surat</th>
                <th width="10%">Tgl Surat</th>
                <th width="12%">Drafter</th>
                <th width="18%">Judul Surat</th>
                <th width="10%">Status</th>
                <th width="10%">Jenis Surat</th>
            </tr>
        </thead>
        <tbody>
            @if($detailData->count() > 0)
                @foreach($detailData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->tahun }}</td>
                    <td class="text-center">{{ $row->bulan }}</td>
                    <td>{{ $row->nomor_surat }}</td>
                    <td class="text-center">{{ $row->tanggal_surat }}</td>
                    <td>{{ $row->drafter }}</td>
                    <td>{{ $row->judul_surat }}</td>
                    <td class="text-center">
                        @if($row->status == 'Terkirim')
                            <span class="status-terkirim">Terkirim</span>
                        @else
                            <span class="status-batal">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($row->jenis_surat == 'Surat Masuk')
                            <span class="masuk">S. Masuk</span>
                        @else
                            <span class="keluar">S. Keluar</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data surat satuan.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh Sistem AdkorReport - PKT &copy; {{ date('Y') }}
    </div>

</body>
</html>
