<!DOCTYPE html>
<html>
<head>
    <title>Data Karyawan Lengkap</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; vertical-align: top; }
        th { background-color: #f3f4f6; font-weight: bold; width: 25%; }
        .header { text-align: center; margin-bottom: 15px; }
        .section-title { font-weight: bold; font-size: 12px; background-color: #d1d5db; padding: 5px; border: 1px solid #333; margin-top: 15px;}
    </style>
</head>
<body>
    <div class="header">
        <h2>Data Detail Karyawan & Keluarga</h2>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    @foreach($karyawan as $index => $item)
        @php 
            $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : ($item->data_tambahan ?? []); 
        @endphp
        
        <div class="section-title">KARYAWAN #{{ $index + 1 }} : {{ strtoupper($item->nama) }} ({{ $item->npk }})</div>
        
        <!-- Tabel Info Utama & Profil -->
        <table>
            <tr>
                <th>Golongan / Grade</th><td>{{ $item->gol_grade }}</td>
                <th>No. PTK</th><td>{{ $item->no_ptk ?? '-' }}</td>
            </tr>
            <tr>
                <th>MPP / PBP</th><td>{{ \Carbon\Carbon::parse($item->mpp_pbp)->format('d/m/Y') }} ({{ $item->ket_pensiun }})</td>
                <th>No. HP / Telepon</th><td>{{ $item->no_hp ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status / Keterangan</th><td>{{ $item->keterangan }}</td>
                <th>Tempat, Tgl Lahir</th><td>{{ $item->tempat_lahir ?? '-' }}, {{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Ukuran Kaos</th><td>{{ $item->ukuran_kaos }}</td>
                <th>Alamat Domisili</th><td>{{ $item->alamat }}</td>
            </tr>
            
            <!-- Injeksi Kolom Dinamis (Tabel Utama + Profil) -->
            @php $allKolomKaryawan = $kolomDinamisTabel->concat($kolomDinamisProfil); @endphp
            @if($allKolomKaryawan->count() > 0)
                <tr><td colspan="4" style="background-color:#e5e7eb; font-weight:bold; text-align:center;">Data Atur Kolom (Karyawan)</td></tr>
                @php $counter = 0; @endphp
                <tr>
                @foreach($allKolomKaryawan as $kol)
                    <th>{{ $kol->nama_kolom }}</th>
                    <td>{{ $tambahan[$kol->nama_kolom] ?? '-' }}</td>
                    @php $counter++; @endphp
                    @if($counter % 2 == 0) </tr><tr> @endif
                @endforeach
                @if($counter % 2 != 0) <th></th><td></td></tr> @endif
            @endif
        </table>

        <!-- Tabel Info Keluarga -->
        <div class="section-title" style="background-color: #e5e7eb;">Daftar Anggota Keluarga</div>
        @if($item->keluarga->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%; text-align:center;">No</th>
                        <th style="width: 25%; text-align:center;">Nama Lengkap</th>
                        <th style="width: 15%; text-align:center;">Hubungan</th>
                        <th style="width: 25%; text-align:center;">Tempat, Tgl Lahir</th>
                        @foreach($kolomDinamisKeluarga as $kolom)
                            <th style="text-align:center;">{{ $kolom->nama_kolom }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->keluarga as $idxKel => $kel)
                        @php $tambahanKel = is_string($kel->data_tambahan) ? json_decode($kel->data_tambahan, true) : ($kel->data_tambahan ?? []); @endphp
                        <tr>
                            <td style="text-align:center;">{{ $idxKel + 1 }}</td>
                            <td>{{ $kel->nama }}</td>
                            <td style="text-align:center;">{{ $kel->hubungan }}</td>
                            <td>{{ $kel->tempat_lahir ?? '-' }}, {{ $kel->tanggal_lahir ? \Carbon\Carbon::parse($kel->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                            @foreach($kolomDinamisKeluarga as $kolom)
                                <td style="text-align:center;">{{ $tambahanKel[$kolom->nama_kolom] ?? '-' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="margin-top: 5px; color:#666; font-size: 10px;"><i>Belum ada data keluarga yang didaftarkan.</i></p>
        @endif

        @if(!($loop->last))
            <hr style="margin: 25px 0; border: 1px dashed #ccc;">
        @endif
    @endforeach
</body>
</html>
