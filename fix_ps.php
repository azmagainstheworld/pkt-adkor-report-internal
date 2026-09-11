<?php
$file = 'app/Http/Controllers/ProgramStrategisController.php';
$content = file_get_contents($file);

// The broken part: exportExcel has no body, flows directly into storeKolomDinamis
// We need to find it and fix it - try both CRLF and LF variants

$bad_crlf = "    public function exportExcel(Request \$request)\r\n    public function storeKolomDinamis";
$bad_lf   = "    public function exportExcel(Request \$request)\n    public function storeKolomDinamis";

$fix = "    public function exportExcel(Request \$request)\r\n    {\r\n        \$tahun = \$request->input('tahun', 'semua');\r\n        return Excel::download(new \\App\\Exports\\ProgramStrategisExport(false, \$tahun), 'Data_Program_Strategis_'.\$tahun.'.xlsx');\r\n    }\r\n\r\n    public function storeKolomDinamis";

if (strpos($content, $bad_crlf) !== false) {
    $content = str_replace($bad_crlf, $fix, $content);
    file_put_contents($file, $content);
    echo "Fixed (CRLF)!\n";
} elseif (strpos($content, $bad_lf) !== false) {
    $fix_lf = str_replace("\r\n", "\n", $fix);
    $content = str_replace($bad_lf, $fix_lf, $content);
    file_put_contents($file, $content);
    echo "Fixed (LF)!\n";
} else {
    echo "Pattern not found. Showing relevant section:\n";
    $pos = strpos($content, 'exportExcel');
    echo substr($content, $pos, 200);
    echo "\nHex:\n";
    echo bin2hex(substr($content, $pos, 100));
}
