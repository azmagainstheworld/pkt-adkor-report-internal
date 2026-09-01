<!DOCTYPE html>
<html>
<head>
    <title>Data Karyawan</title>
    <style>
        body { font-family: sans-serif; font-size: 8px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        tr { page-break-inside: avoid; }
        th, td { border: 1px solid #777; padding: 4px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; vertical-align: middle; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #0056A3; font-size: 14px; }
        .header p { margin: 4px 0 0 0; font-size: 9px; color: #555; }
        
        /* Trik menyamarkan border agar terlihat kosong seperti rowspan */
        .group-child { border-top: 1px solid transparent !important; color: transparent !important; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DATA REKAPITULASI KARYAWAN</h2>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NPK</th>
                <th>Gol/Grade</th>
                <th>MPP/PBP</th>
                <th>Keterangan</th>
                <th>Ket. Pensiun</th>
                <th>Alamat Lengkap</th>
                <th>Ukuran Kaos</th>
                
                @foreach($kolomDinamis as $kolom)
                    <th>{{ $kolom->nama_kolom }}</th>
                @endforeach
                
                @if($type === 'lengkap')
                    <th>No. PTK</th>
                    <th>No. HP</th>
                    <th>TTL Karyawan</th>
                    @if(isset($kolomProfil) && $kolomProfil->count() > 0)
                        @foreach($kolomProfil as $kp)
                            <th>{{ $kp->nama_kolom }} (Profil)</th>
                        @endforeach
                    @endif
                    
                    <th style="background-color: #e5e7eb;">Nama Keluarga</th>
                    <th style="background-color: #e5e7eb;">Hub.</th>
                    <th style="background-color: #e5e7eb;">TTL Keluarga</th>
                    @if(isset($kolomKeluarga) && $kolomKeluarga->count() > 0)
                        @foreach($kolomKeluarga as $kk)
                            <th style="background-color: #e5e7eb;">{{ $kk->nama_kolom }} (Kel)</th>
                        @endforeach
                    @endif
                @endif
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($karyawan as $item)
                @php 
                    $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []); 
                    $keluargaCount = (isset($item->keluarga) && $item->keluarga->count() > 0) ? $item->keluarga->count() : 1;
                    $nomorUrut = $no++;
                @endphp
                
                @for($i = 0; $i < $keluargaCount; $i++)
                    <tr>
                        @if($i === 0)
                            <td class="text-center">{{ $nomorUrut }}</td>
                            <td>{{ $item->nama }}</td>
                            <td class="text-center">{{ $item->npk }}</td>
                            <td class="text-center">{{ $item->gol_grade }}</td>
                            <td class="text-center">{{ $item->mpp_pbp ? \Carbon\Carbon::parse($item->mpp_pbp)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">{{ $item->keterangan }}</td>
                            <td class="text-center">{{ $item->ket_pensiun ?? '-' }}</td>
                            <td>{{ $item->alamat ?? '-' }}</td>
                            <td class="text-center">{{ $item->ukuran_kaos ?? '-' }}</td>

                            @foreach($kolomDinamis as $kolom)
                                <td>{{ $tambahan[$kolom->nama_kolom] ?? '-' }}</td>
                            @endforeach

                            @if($type === 'lengkap')
                                <td class="text-center">{{ $item->no_ptk ?? '-' }}</td>
                                <td class="text-center">{{ $item->no_hp ?? '-' }}</td>
                                <td>{{ $item->tempat_lahir ?? '-' }},<br>{{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                                @if(isset($kolomProfil) && $kolomProfil->count() > 0)
                                    @foreach($kolomProfil as $kp)
                                        <td>{{ $tambahan[$kp->nama_kolom] ?? '-' }}</td>
                                    @endforeach
                                @endif
                            @endif

                        @else
                            <!-- Sel Karyawan Dikosongkan (Trik Visual CSS) -->
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            <td class="group-child">-</td>
                            @foreach($kolomDinamis as $kolom)
                                <td class="group-child">-</td>
                            @endforeach

                            @if($type === 'lengkap')
                                <td class="group-child">-</td>
                                <td class="group-child">-</td>
                                <td class="group-child">-</td>
                                @if(isset($kolomProfil) && $kolomProfil->count() > 0)
                                    @foreach($kolomProfil as $kp)
                                        <td class="group-child">-</td>
                                    @endforeach
                                @endif
                            @endif
                        @endif

                        <!-- Render Data Keluarga di Kolom Paling Kanan -->
                        @if($type === 'lengkap')
                            @if(isset($item->keluarga) && $item->keluarga->count() > 0 && isset($item->keluarga[$i]))
                                @php 
                                    $kel = $item->keluarga[$i]; 
                                    $tambahanKel = is_string($kel->data_tambahan) ? json_decode($kel->data_tambahan, true) : ($kel->data_tambahan ?? []);
                                @endphp
                                <td>{{ $kel->nama }}</td>
                                <td class="text-center">{{ $kel->hubungan }}</td>
                                <td>{{ $kel->tempat_lahir ?? '-' }},<br>{{ $kel->tanggal_lahir ? \Carbon\Carbon::parse($kel->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                                @if(isset($kolomKeluarga) && $kolomKeluarga->count() > 0)
                                    @foreach($kolomKeluarga as $kk)
                                        <td>{{ $tambahanKel[$kk->nama_kolom] ?? '-' }}</td>
                                    @endforeach
                                @endif
                            @else
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                @if(isset($kolomKeluarga) && $kolomKeluarga->count() > 0)
                                    @foreach($kolomKeluarga as $kk)
                                        <td class="text-center">-</td>
                                    @endforeach
                                @endif
                            @endif
                        @endif
                    </tr>
                @endfor
            @endforeach
        </tbody>
    </table>
</body>
</html>