<?php
$f1 = 'app/Http/Controllers/PaTeknikController.php';
$c1 = file_get_contents($f1);

// Fix pagination logic
$search = "usort(\$dataTable1, \$sorter);\n        usort(\$dataTable2, \$sorter);";
$replace = "usort(\$dataTable1, \$sorter);\n        usort(\$dataTable2, \$sorter);\n\n        \$perPage = 10;\n        \$currentPage1 = LengthAwarePaginator::resolveCurrentPage('page1');\n        \$currentItems1 = array_slice(\$dataTable1, (\$currentPage1 - 1) * \$perPage, \$perPage);\n        \$paginatedTable1 = new LengthAwarePaginator(\$currentItems1, count(\$dataTable1), \$perPage, \$currentPage1, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page1']);\n        \$paginatedTable1->appends(\$request->all());\n\n        \$currentPage2 = LengthAwarePaginator::resolveCurrentPage('page2');\n        \$currentItems2 = array_slice(\$dataTable2, (\$currentPage2 - 1) * \$perPage, \$perPage);\n        \$paginatedTable2 = new LengthAwarePaginator(\$currentItems2, count(\$dataTable2), \$perPage, \$currentPage2, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page2']);\n        \$paginatedTable2->appends(\$request->all());";
$c1 = str_replace($search, $replace, $c1);

// Fix compact
$c1 = str_replace(
    "'masterTabel1', 'masterTabel2', 'dataTable1', 'dataTable2',",
    "'masterTabel1', 'masterTabel2', 'paginatedTable1', 'paginatedTable2',",
    $c1
);

file_put_contents($f1, $c1);
echo "Patched correctly.\n";
