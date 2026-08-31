<?php
$files = [
    'app/Http/Controllers/PaTekstualController.php' => [
        'importClass' => 'PaTekstualImport',
        'importMethod' => "
    public function importExcel(Request \$request)
    {
        \$request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);
        try {
            Excel::import(new PaTekstualImport, \$request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil di-import dari Excel.');
        } catch (\Exception \$e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . \$e->getMessage());
        }
    }

    public function exportExcel(Request \$request) { /* TODO */ }
    public function exportPdf(Request \$request) { /* TODO */ }
"
    ],
    'app/Http/Controllers/PaNonTekstualController.php' => [
        'importClass' => 'PaNonTekstualImport',
        'importMethod' => "
    public function importExcel(Request \$request)
    {
        \$request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);
        try {
            Excel::import(new PaNonTekstualImport, \$request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil di-import dari Excel.');
        } catch (\Exception \$e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . \$e->getMessage());
        }
    }

    public function exportExcel(Request \$request) { /* TODO */ }
    public function exportPdf(Request \$request) { /* TODO */ }
"
    ]
];

foreach ($files as $file => $data) {
    $c = file_get_contents($file);
    
    // Add Excel facade import
    if (strpos($c, "use Maatwebsite\Excel\Facades\Excel;") === false) {
        $c = preg_replace("/use Illuminate\\\\Http\\\\Request;/", "use Illuminate\Http\Request;\nuse Maatwebsite\Excel\Facades\Excel;\nuse App\Imports\\" . $data['importClass'] . ";", $c, 1);
    }
    
    // Append methods if missing
    if (strpos($c, "function importExcel") === false) {
        $c = preg_replace("/\}\s*$/", $data['importMethod'] . "\n}\n", $c);
    }
    
    file_put_contents($file, $c);
    echo "Patched $file\n";
}
