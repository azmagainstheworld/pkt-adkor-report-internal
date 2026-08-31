<?php
// PATCH PATEKSTUAL
$f1 = 'app/Http/Controllers/PaTekstualController.php';
$c1 = file_get_contents($f1);

// Add import
$c1 = str_replace(
    "use Barryvdh\DomPDF\Facade\Pdf;",
    "use Barryvdh\DomPDF\Facade\Pdf;\nuse App\Imports\PaTekstualImport;\nuse Illuminate\Pagination\LengthAwarePaginator;\nuse Maatwebsite\Excel\Facades\Excel;",
    $c1
);

// Add pagination logic
$c1 = str_replace(
    "usort(\$dataTable1, \$sorter); usort(\$dataTable2, \$sorter);",
    "usort(\$dataTable1, \$sorter); usort(\$dataTable2, \$sorter);\n\n        \$perPage = 10;\n        \$currentPage1 = LengthAwarePaginator::resolveCurrentPage('page1');\n        \$currentItems1 = array_slice(\$dataTable1, (\$currentPage1 - 1) * \$perPage, \$perPage);\n        \$paginatedTable1 = new LengthAwarePaginator(\$currentItems1, count(\$dataTable1), \$perPage, \$currentPage1, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page1']);\n        \$paginatedTable1->appends(\$request->all());\n\n        \$currentPage2 = LengthAwarePaginator::resolveCurrentPage('page2');\n        \$currentItems2 = array_slice(\$dataTable2, (\$currentPage2 - 1) * \$perPage, \$perPage);\n        \$paginatedTable2 = new LengthAwarePaginator(\$currentItems2, count(\$dataTable2), \$perPage, \$currentPage2, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page2']);\n        \$paginatedTable2->appends(\$request->all());\n",
    $c1
);

// Update compact to use paginated tables
$c1 = str_replace(
    "'dataTable1', 'dataTable2', 'totalsTabel1'",
    "'paginatedTable1', 'paginatedTable2', 'totalsTabel1'",
    $c1
);

// Since I reset via Git, the old importExcel is present which expects file_excel and returns redirect.
// Let's replace the whole old importExcel
$oldImport = "    public function importExcel(Request \$request)
    {
        set_time_limit(0);
        \$request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv|max:51200']);

        try {
            Excel::import(new \App\Imports\PaTekstualImport, \$request->file('file_excel'));
            return redirect()->back()->with('success', 'Data PA Tekstual berhasil di-import!');
        } catch (\Exception \$e) {
            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import. Pastikan file sesuai format template. Detail: ' . \$e->getMessage());
        }
    }";

$newImport = "    public function importExcel(Request \$request)
    {
        set_time_limit(0);
        \$request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        \$kelompok = \$request->query('kelompok');
        if (!in_array(\$kelompok, ['tabel1', 'tabel2'])) return response()->json(['error' => 'Kelompok tabel tidak valid.'], 400);
        try {
            Excel::import(new PaTekstualImport(\$kelompok), \$request->file('file'));
            return response()->json(['success' => 'Data berhasil di-import.']);
        } catch (\Exception \$e) {
            return response()->json(['error' => 'Gagal meng-import: ' . \$e->getMessage()], 500);
        }
    }";

$c1 = str_replace($oldImport, $newImport, $c1);
file_put_contents($f1, $c1);
echo "Patched PaTekstualController\n";

// PATCH PANONTEKSTUAL (Add pagination to PaNonTekstualController)
$f2 = 'app/Http/Controllers/PaNonTekstualController.php';
$c2 = file_get_contents($f2);

// Add LengthAwarePaginator import if missing
if (strpos($c2, "use Illuminate\Pagination\LengthAwarePaginator;") === false) {
    $c2 = str_replace(
        "use Barryvdh\DomPDF\Facade\Pdf;",
        "use Barryvdh\DomPDF\Facade\Pdf;\nuse Illuminate\Pagination\LengthAwarePaginator;",
        $c2
    );
}

// Add pagination logic
$c2 = str_replace(
    "usort(\$dataPa, \$sorter);",
    "usort(\$dataPa, \$sorter);\n\n        \$perPage = 10;\n        \$currentPage = LengthAwarePaginator::resolveCurrentPage();\n        \$currentItems = array_slice(\$dataPa, (\$currentPage - 1) * \$perPage, \$perPage);\n        \$paginatedDataPa = new LengthAwarePaginator(\$currentItems, count(\$dataPa), \$perPage, \$currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);\n        \$paginatedDataPa->appends(\$request->all());\n",
    $c2
);

// Update compact to use paginated table
$c2 = str_replace(
    "compact(\n            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',\n            'availableTypes', 'dataPa', 'grandTotals', 'kolomDinamis'\n        )",
    "compact(\n            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',\n            'availableTypes', 'paginatedDataPa', 'grandTotals', 'kolomDinamis'\n        )",
    $c2
);

file_put_contents($f2, $c2);
echo "Patched PaNonTekstualController\n";

