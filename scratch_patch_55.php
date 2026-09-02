<?php
$ctrlPath = 'app/Http/Controllers/PelaporanController.php';
$ctrlContent = file_get_contents($ctrlPath);

$searchPdf = <<<PHP
        \$dataRincian = \$query->orderBy('tanggal', 'desc')->get();
        
        \$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pelaporan', compact('dataRincian'))->setPaper('a4', 'landscape');
PHP;

$replacePdf = <<<PHP
        \$dataRincian = \$query->orderBy('tanggal', 'desc')->get();
        
        // Load kolom dinamis agar tidak error di tampilan PDF
        \$kolomDinamis = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', 'pelaporan')->get();
        
        \$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pelaporan', compact('dataRincian', 'tahun', 'bulan', 'kolomDinamis'))->setPaper('a4', 'landscape');
PHP;

$searchPdf = str_replace("\r\n", "\n", $searchPdf);
$replacePdf = str_replace("\r\n", "\n", $replacePdf);
$ctrlContent = str_replace("\r\n", "\n", $ctrlContent);

$ctrlContent = str_replace($searchPdf, $replacePdf, $ctrlContent);

file_put_contents($ctrlPath, $ctrlContent);
echo "PelaporanController exportPdf variables fixed!\n";
?>
