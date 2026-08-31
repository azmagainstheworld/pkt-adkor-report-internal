<?php
$file = 'app/Http/Controllers/ProgramStrategisController.php';
$content = file_get_contents($file);

// Find the line where $kolomDinamis is defined
$search = "\$kolomDinamis = DB::table('dynamic_columns')->where('modul', 'program_strategis')->get();";

// We want to add pagination for $groupedProgram
$replace = "
        \$page = \$request->get('page', 1);
        \$perPage = 10; // Tampilkan 10 grup per halaman
        \$paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
            \$groupedProgram->forPage(\$page, \$perPage),
            \$groupedProgram->count(),
            \$perPage,
            \$page,
            ['path' => \$request->url(), 'query' => \$request->query()]
        );
        \$groupedProgram = \$paginatedGroups;

        " . $search;

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Pagination added to ProgramStrategisController.\n";
