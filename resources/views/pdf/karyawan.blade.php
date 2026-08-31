<!DOCTYPE html>
<html>
<head>
    <title>Data Karyawan</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
        .sub-table { width: 95%; margin: 5px auto; font-size: 9px; }
        .sub-table th { background-color: #e5e7eb; }
        .profil-info { padding: 5px 10px; background-color: #fafafa; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Data Rekapitulasi Karyawan</h2>
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
                @foreach($kolomDinamis as $kolom)
                    <th>{{ $kolom->nama_kolom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($karyawan as $index => $item)
                @php 
                    $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []); 
                    $colspan = 6 + count($kolomDinamis);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-center">{{ $item->npk }}</td>
                    <td class="text-center">{{ $item->gol_grade }}</td>
                    <td class="text-center">{{ $item->mpp_pbp ? \Carbon\Carbon::parse($item->mpp_pbp)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $item->keterangan }}</td>
                    @foreach($kolomDinamis as $kolom)
                        <td>{{ $tambahan[$kolom->nama_kolom] ?? '-' }}</td>
                    @endforeach
                </tr>
                
                @if($type === 'lengkap')
                    <tr>
                        <td colspan="{{ $colspan }}" style="padding: 0;">
                            <div class="profil-info">
                                <strong>Data Profil:</strong> 
                                No. PTK: {{ $item->no_ptk ?? '-' }} | 
                                No. HP: {{ $item->no_hp ?? '-' }} | 
                                Tempat, Tgl Lahir: {{ $item->tempat_lahir ?? '-' }}, {{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') : '-' }}
                                
                                @if(isset($kolomProfil) && count($kolomProfil) > 0)
                                    <br><strong>Informasi Tambahan:</strong> 
                                    @foreach($kolomProfil as $kp)
                                        {{ $kp->nama_kolom }}: {{ $tambahan[$kp->nama_kolom] ?? '-' }} @if(!$loop->last) | @endif
                                    @endforeach
                                @endif
                            </div>
                            
                            @if(isset($item->keluarga) && count($item->keluarga) > 0)
                                <table class="sub-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Keluarga</th>
                                            <th>Hubungan</th>
                                            <th>Tempat Lahir</th>
                                            <th>Tanggal Lahir</th>
                                            @if(isset($kolomKeluarga))
                                                @foreach($kolomKeluarga as $kk)
                                                    <th>{{ $kk->nama_kolom }}</th>
                                                @endforeach
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($item->keluarga as $kel)
                                            @php $tambahanKel = is_string($kel->data_tambahan) ? json_decode($kel->data_tambahan, true) : ($kel->data_tambahan ?? []); @endphp
                                            <tr>
                                                <td>{{ $kel->nama }}</td>
                                                <td>{{ $kel->hubungan }}</td>
                                                <td>{{ $kel->tempat_lahir ?? '-' }}</td>
                                                <td>{{ $kel->tanggal_lahir ? \Carbon\Carbon::parse($kel->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                                                @if(isset($kolomKeluarga))
                                                    @foreach($kolomKeluarga as $kk)
                                                        <td>{{ $tambahanKel[$kk->nama_kolom] ?? '-' }}</td>
                                                    @endforeach
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>