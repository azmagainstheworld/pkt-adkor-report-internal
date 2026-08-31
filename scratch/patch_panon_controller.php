<?php
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
    "'availableTypes', 'dataPa', 'grandTotals', 'kolomDinamis'",
    "'availableTypes', 'paginatedDataPa', 'dataPa', 'grandTotals', 'kolomDinamis'",
    $c2
);

file_put_contents($f2, $c2);
echo "Patched PaNonTekstualController\n";
