<?php
require 'vendor/autoload.php';
use Barryvdh\DomPDF\Facade\Pdf;

// 1. Generate DomPDF
$html = '<html><body><h1>Test Landscape</h1></body></html>';
$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdfOutput = $dompdf->output();
file_put_contents('temp_dompdf.pdf', $dompdfOutput);

// 2. mPDF merge
$mpdf = new \Mpdf\Mpdf(['format' => 'A4-P']); // Portrait cover
try {
    // Add cover
    $mpdf->AddPage();
    $mpdf->Image('public/images/cover-laporan.png', 0, 0, 210, 297, 'png', '', true, false);
    $mpdf->SetFillColor(43, 73, 143);
    $mpdf->Rect(20, 180, 80, 30, 'F');
    $mpdf->SetXY(20, 180);
    $mpdf->SetFont('Arial', 'B', 32);
    $mpdf->SetTextColor(255, 255, 255);
    $mpdf->Cell(80, 15, "SEPTEMBER", 0, 1, 'C');
    $mpdf->SetXY(20, 195);
    $mpdf->SetTextColor(43, 73, 143);
    $mpdf->Cell(80, 15, "2026", 0, 0, 'C');

    // Import DomPDF
    $pageCount = $mpdf->setSourceFile('temp_dompdf.pdf');
    for ($i = 1; $i <= $pageCount; $i++) {
        $mpdf->AddPage('L'); // Landscape for report
        $tplId = $mpdf->importPage($i);
        $mpdf->UseTemplate($tplId);
    }
    
    $mpdf->Output('test_merged.pdf', 'F');
    echo "Merge Success!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
