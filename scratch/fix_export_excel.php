<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\DofController.php';
$content = file_get_contents($file);

$oldFunc = <<<EOD
    public function exportExcel(Request \$request)
    {
        \$tahun = \$request->query('tahun', 'semua');
        \$bulan = \$request->query('bulan', 'semua');
        // Let's use the old format or new format for Export Excel? 
        // We can just use DofExport(1) and DofExport(2) but Excel only supports one export class per file unless we use MultipleSheets.
        // I will recreate DofDataExport that exports the raw data like PaTeknikExport did.
        return Excel::download(new \App\Exports\DofDataExport(\$tahun, \$bulan), 'Data_DOF_'.\$tahun.'_'.\$bulan.'.xlsx');
    }
EOD;

$newFunc = <<<EOD
    public function exportExcel(Request \$request)
    {
        \$tahun = \$request->query('tahun', 'semua');
        \$bulan = \$request->query('bulan', 'semua');
        \$kelompok_tabel = \$request->query('kelompok_tabel', 1);
        return Excel::download(new \App\Exports\DofDataExport(\$tahun, \$bulan, \$kelompok_tabel), 'Data_DOF_Tabel_'.\$kelompok_tabel.'_'.\$tahun.'_'.\$bulan.'.xlsx');
    }
EOD;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
echo "OK\n";
