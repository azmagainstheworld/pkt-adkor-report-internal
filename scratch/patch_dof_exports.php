<?php
$f = 'app/Http/Controllers/DofController.php';
$c = file_get_contents($f);

// 1. Add export methods if missing
$exports = "
    public function exportExcel(Request \$request)
    {
        \$tahun = \$request->query('tahun', 'semua');
        \$bulan = \$request->query('bulan', 'semua');
        // Let's use the old format or new format for Export Excel? 
        // We can just use DofExport(1) and DofExport(2) but Excel only supports one export class per file unless we use MultipleSheets.
        // I will recreate DofDataExport that exports the raw data like PaTeknikExport did.
        return Excel::download(new \App\Exports\DofDataExport(\$tahun, \$bulan), 'Data_DOF_'.\$tahun.'_'.\$bulan.'.xlsx');
    }

    public function exportPdf(Request \$request)
    {
        \$tahun = \$request->query('tahun', 'semua');
        \$bulan = \$request->query('bulan', 'semua');
        
        \$query = DofData::with('masterDof');
        if (\$tahun !== 'semua') \$query->where('tahun', \$tahun);
        if (\$bulan !== 'semua') \$query->where('bulan', \$bulan);
        \$data = \$query->orderBy('tahun', 'desc')->get();

        \$pdf = Pdf::loadView('exports.dof-pdf', compact('data', 'tahun', 'bulan'))
                  ->setPaper('a4', 'landscape');
        
        return \$pdf->download('Data_DOF_'.\$tahun.'_'.\$bulan.'.pdf');
    }
";

if (strpos($c, 'function exportExcel') === false) {
    $c = preg_replace('/\}\s*$/', $exports . "\n}", $c);
}

// 2. We need use Pdf
if (strpos($c, 'use Barryvdh\DomPDF\Facade\Pdf;') === false) {
    $c = str_replace("use App\Imports\DofImport;", "use App\Imports\DofImport;\nuse Barryvdh\DomPDF\Facade\Pdf;", $c);
}

file_put_contents($f, $c);
echo "Patched DofController exports\n";
