<?php

$filePath = 'resources/views/pdf/pengiriman-dokumen.blade.php';
$content = file_get_contents($filePath);

// Update title based on jenis
$titleOld = "<h2>Laporan Bulanan Pengiriman Dokumen & Ongkir</h2>";
$titleNew = "<h2>Laporan Bulanan {{ \$jenis == 'ongkir' ? 'Ongkir Pengiriman' : 'Volume Pengiriman Dokumen' }}</h2>";
$content = str_replace($titleOld, $titleNew, $content);

// Update table header based on jenis
$headerOld = <<<HTML
            <tr>
                <th width="30">No</th>
                <th>Periode</th>
                <th>Penerimaan Mailroom</th>
                <th>Registrasi Surat Masuk DOF</th>
                <th>Kirim Domestik</th>
                <th>Kirim Internasional</th>
                <th>Ongkir Domestik (Rp)</th>
                <th>Ongkir Internasional (Rp)</th>
HTML;

$headerNew = <<<HTML
            <tr>
                <th width="30">No</th>
                <th>Periode</th>
                @if(\$jenis == 'ongkir')
                    <th>Ongkir Domestik (Rp)</th>
                    <th>Ongkir Internasional (Rp)</th>
                @else
                    <th>Penerimaan Mailroom</th>
                    <th>Registrasi Surat Masuk DOF</th>
                    <th>Kirim Domestik</th>
                    <th>Kirim Internasional</th>
                @endif
HTML;
$content = str_replace($headerOld, $headerNew, $content);

// Update table body based on jenis
$bodyOld = <<<HTML
                    <td>{{ \$item->bulan }}<br>{{ \$item->tahun }}</td>
                    <td>{{ number_format(\$item->penerimaan_mailroom, 0, ',', '.') }}</td>
                    <td>{{ number_format(\$item->registrasi_surat_masuk_dof, 0, ',', '.') }}</td>
                    <td>{{ number_format(\$item->pengiriman_dalam_negeri, 0, ',', '.') }}</td>
                    <td>{{ number_format(\$item->pengiriman_luar_negeri, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(\$item->ongkir_dalam_negeri, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(\$item->ongkir_luar_negeri, 0, ',', '.') }}</td>
HTML;

$bodyNew = <<<HTML
                    <td>{{ \$item->bulan }}<br>{{ \$item->tahun }}</td>
                    @if(\$jenis == 'ongkir')
                        <td class="text-right">{{ number_format(\$item->ongkir_dalam_negeri, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format(\$item->ongkir_luar_negeri, 0, ',', '.') }}</td>
                    @else
                        <td>{{ number_format(\$item->penerimaan_mailroom, 0, ',', '.') }}</td>
                        <td>{{ number_format(\$item->registrasi_surat_masuk_dof, 0, ',', '.') }}</td>
                        <td>{{ number_format(\$item->pengiriman_dalam_negeri, 0, ',', '.') }}</td>
                        <td>{{ number_format(\$item->pengiriman_luar_negeri, 0, ',', '.') }}</td>
                    @endif
HTML;
$content = str_replace($bodyOld, $bodyNew, $content);

// Update totDomestik and totInternasional calculations
$calcOld = <<<HTML
                    \$tambahan = is_string(\$item->data_tambahan) ? json_decode(\$item->data_tambahan, true) : (\$item->data_tambahan ?? []); 
                    \$totDomestik += \$item->ongkir_dalam_negeri;
                    \$totInternasional += \$item->ongkir_luar_negeri;
HTML;
$calcNew = <<<HTML
                    \$tambahan = is_string(\$item->data_tambahan) ? json_decode(\$item->data_tambahan, true) : (\$item->data_tambahan ?? []); 
                    if (\$jenis == 'ongkir') {
                        \$totDomestik += \$item->ongkir_dalam_negeri;
                        \$totInternasional += \$item->ongkir_luar_negeri;
                    }
HTML;
$content = str_replace($calcOld, $calcNew, $content);

// Update summary block
$summaryOld = <<<HTML
    <div class="summary-box">
        <strong>Ringkasan Total Biaya:</strong><br>
        Total Ongkir Dalam Negeri: Rp {{ number_format(\$totalDomestik, 0, ',', '.') }}<br>
        Total Ongkir Luar Negeri: Rp {{ number_format(\$totalInternasional, 0, ',', '.') }}
    </div>
HTML;
$summaryNew = <<<HTML
    @if(\$jenis == 'ongkir')
    <div class="summary-box">
        <strong>Ringkasan Total Biaya:</strong><br>
        Total Ongkir Dalam Negeri: Rp {{ number_format(\$totalDomestik, 0, ',', '.') }}<br>
        Total Ongkir Luar Negeri: Rp {{ number_format(\$totalInternasional, 0, ',', '.') }}
    </div>
    @endif
HTML;
$content = str_replace($summaryOld, $summaryNew, $content);

file_put_contents($filePath, $content);
echo "PDF blade updated.\n";
