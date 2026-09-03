@if($jenis == 'tabel1')
<table>
    <thead>
        <tr>
            <th>Tahun</th>
            <th>Bulan</th>
            <th>Surat Masuk</th>
            <th>Surat Keluar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapData as $row)
        <tr>
            <td>{{ $row->tahun }}</td>
            <td>{{ $row->bulan }}</td>
            <td>{{ $row->total_masuk }}</td>
            <td>{{ $row->total_keluar }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@elseif($jenis == 'tabel2')
<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Tahun</th>
            <th>Bulan</th>
            <th>Nomor Surat</th>
            <th>Tanggal surat</th>
            <th>Drafter</th>
            <th>Judul Surat</th>
            <th>Status Upload</th>
            <th>Status</th>
            <th>Detail</th>
            <th>Jenis Surat</th>
            @if(isset($kolomDinamis))
                @foreach($kolomDinamis as $kolom)
                    <th>{{ $kolom->nama_kolom }}</th>
                @endforeach
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($detailData as $index => $row)
        @php
            $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $row->tahun }}</td>
            <td>{{ $row->bulan }}</td>
            <td>{{ $row->nomor_surat }}</td>
            <td>{{ $row->tanggal_surat }}</td>
            <td>{{ $row->drafter }}</td>
            <td>{{ $row->judul_surat }}</td>
            <td>{{ $row->file_path ? 'Terupload' : 'Tidak' }}</td>
            <td>{{ $row->status }}</td>
            <td></td>
            <td>{{ $row->jenis_surat }}</td>
            @if(isset($kolomDinamis))
                @foreach($kolomDinamis as $kolom)
                    <td>
                        @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                            Rp {{ number_format((float)$tambahan[$kolom->nama_kolom], 0, ',', '.') }}
                        @else
                            {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                        @endif
                    </td>
                @endforeach
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
@endif
