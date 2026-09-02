<!DOCTYPE html>
<html>
<head>
    <title>Rekap Program Strategis</title>
    <style>
        body { font-family: sans-serif; font-size: 9px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
        tr { page-break-inside: avoid; }
        
        /* Tambahkan vertical-align: top dan word-wrap agar teks panjang rapi dan tidak bocor */
        th, td { border: 1px solid #999; padding: 6px; text-align: center; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
        th { background-color: #f3f4f6; font-weight: bold; vertical-align: middle; }
        
        .text-left { text-align: left; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #0056A3; font-size: 16px; }
        .header p { margin: 4px 0 0 0; font-size: 10px; color: #555; }
        
        /* Trik CSS untuk menyamarkan border agar terlihat seperti rowspan */
        .group-child { border-top: 1px solid transparent !important; color: transparent !important; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PROGRAM STRATEGIS</h2>
        <p>Periode: <strong>{{ $tahun == 'semua' ? 'Semua Tahun' : $tahun }}</strong> | Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 4%;">Tahun</th>
                <th style="width: 6%;">Bulan</th>
                <th style="width: 14%;">Sasaran</th>
                <th style="width: 14%;">Program Strategis</th>
                <th style="width: 15%;">Program Kegiatan</th>
                <th style="width: 8%;">Target Waktu</th>
                <th style="width: 5%;">Realisasi</th>
                <th style="width: 13%;">Progress</th>
                <th style="width: 7%;">Kendala</th>
                <th style="width: 6%;">Keterangan</th>
                <th style="width: 5%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1; 
                $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            @endphp
            @forelse($groupedProgram as $group)
                @php $noDisplay = $no++; @endphp
                @foreach($group as $index => $row)
                    <tr>
                        @if($index === 0)
                            <!-- Baris Utama Grup -->
                            <td>{{ $noDisplay }}</td>
                            <td>{{ $row->tahun }}</td>
                            <td>{{ ($row->bulan && is_numeric($row->bulan)) ? $bulanIndo[(int)$row->bulan] : '-' }}</td>
                            <td class="text-left">{{ $row->sasaran ?? '-' }}</td>
                            <td class="text-left">{{ $row->program_strategis }}</td>
                        @else
                            <!-- Trik pengganti rowspan: Sel tetap dirender agar DomPDF tidak error saat ganti halaman -->
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                        @endif
                        
                        <td class="text-left">{{ $row->deskripsi_kegiatan ?? '-' }}</td>
                        <td>
                            @if($row->target_waktu_start && $row->target_waktu_end)
                                {{ \Carbon\Carbon::parse($row->target_waktu_start)->format('d/m/Y') }}<br>s.d<br>{{ \Carbon\Carbon::parse($row->target_waktu_end)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ (!empty($row->realisasi) && $row->realisasi !== '-' && !str_ends_with(trim($row->realisasi), '%')) ? trim($row->realisasi) . '%' : ($row->realisasi ?? '-') }}</td>
                        <td class="text-left">{{ $row->progress_saat_ini ?? '-' }}</td>
                        <td class="text-left">{{ $row->kendala ?? '-' }}</td>
                        <td class="text-left">{{ $row->keterangan_tambahan ?? '-' }}</td>
                        <td>{{ $row->status }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="12" style="padding: 20px;">Belum ada data program strategis.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>