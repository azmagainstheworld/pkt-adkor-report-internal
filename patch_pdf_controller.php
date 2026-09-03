<?php

$filePath = 'app/Http/Controllers/PengirimanDokumenController.php';
$content = file_get_contents($filePath);

$exportPdfOld = <<<PHP
        \$totalDomestik = \$jenis === 'ongkir' ? \$records->sum('ongkir_dalam_negeri') : 0;
        \$totalInternasional = \$jenis === 'ongkir' ? \$records->sum('ongkir_luar_negeri') : 0;
        \$kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pengiriman_dokumen')->get();

        \$pdf = Pdf::loadView('exports.pengiriman-pdf', compact('records', 'year', 'month', 'totalDomestik', 'totalInternasional', 'kolomDinamis'))->setPaper('a4', 'landscape');
        return \$pdf->stream("Laporan_Pengiriman_{\$month}_{\$year}.pdf");
PHP;

$exportPdfNew = <<<PHP
        \$totalDomestik = \$jenis === 'ongkir' ? \$records->sum('ongkir_dalam_negeri') : 0;
        \$totalInternasional = \$jenis === 'ongkir' ? \$records->sum('ongkir_luar_negeri') : 0;
        \$kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pengiriman_dokumen')->get();

        \$pdf = Pdf::loadView('exports.pengiriman-pdf', compact('records', 'year', 'month', 'totalDomestik', 'totalInternasional', 'kolomDinamis', 'jenis'))->setPaper('a4', 'landscape');
        return \$pdf->stream("Laporan_Pengiriman_{\$jenis}_{\$month}_{\$year}.pdf");
PHP;
$content = str_replace($exportPdfOld, $exportPdfNew, $content);

file_put_contents($filePath, $content);
echo "exportPdf controller updated.\n";
