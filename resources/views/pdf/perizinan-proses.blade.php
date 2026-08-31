<!DOCTYPE html>
<html>
<head>
    <title>Rekap Perizinan Proses</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .align-top { vertical-align: top; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daftar Perizinan Proses Administrasi Korporat</h2>
        <p>Tahun: <strong>{{ $tahun == 'semua' ? 'Semua Tahun' : $tahun }}</strong> | Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>No.</th>
                <th>Perizinan Proses</th>
                <th>Target</th>
                <th>Periode (Bulan)</th>
                @foreach($kolomDinamis as $kolom)
                    <th>{{ $kolom->nama_kolom }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($groupedProses as $group)
                @php $rowspan = $group->count(); @endphp
                @foreach($group as $index => $proses)
                    @php $tambahan = is_string($proses->data_tambahan) ? json_decode($proses->data_tambahan, true) : ($proses->data_tambahan ?? []); @endphp
                    <tr>
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}" class="text-center align-top">{{ $proses->tahun }}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center align-top">{{ $no++ }}</td>
                            <td rowspan="{{ $rowspan }}" class="align-top font-bold">{{ $proses->nama_proses }}</td>
                        @endif
                        
                        <td class="align-top">{!! nl2br(e($proses->target)) !!}</td>
                        
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}" class="text-center align-top">{{ $proses->periode }}</td>
                        @endif

                        @foreach($kolomDinamis as $kolom)
                            <td class="text-center align-top">{{ $tambahan[$kolom->nama_kolom] ?? '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
