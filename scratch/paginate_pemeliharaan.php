<?php
$file = 'app/Http/Controllers/PemeliharaanController.php';
$content = file_get_contents($file);

// Add use Illuminate\Pagination\LengthAwarePaginator; if not exists
if (strpos($content, 'use Illuminate\Pagination\LengthAwarePaginator;') === false) {
    $content = str_replace(
        "use Illuminate\Support\Facades\DB;",
        "use Illuminate\Support\Facades\DB;\nuse Illuminate\Pagination\LengthAwarePaginator;",
        $content
    );
}

// Replace the end of dataRutinTable sorting and compact
$pattern = '/usort\(\$dataRutinTable.*?\};\s*\n\s*\/\/ =========================================================================/s';

$replacement = <<<'EOD'
usort($dataRutinTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        // Paginate dataRutinTable
        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $itemsForCurrentPage = array_slice($dataRutinTable, $offset, $perPage);
        $dataRutinTablePaginated = new LengthAwarePaginator(
            $itemsForCurrentPage,
            count($dataRutinTable),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // =========================================================================
EOD;

$content = preg_replace($pattern, $replacement, $content);

// replace 'dataRutinTable' in compact
$content = str_replace("'dataRutinTable',", "'dataRutinTablePaginated',", $content);

file_put_contents($file, $content);
echo "Controller paginated.\n";

$blade = 'resources/views/pemeliharaan.blade.php';
$bladeContent = file_get_contents($blade);

$bladeContent = str_replace('@forelse($dataRutinTable as $row)', '@forelse($dataRutinTablePaginated as $row)', $bladeContent);

// Add pagination links below x-table
$bladeContent = str_replace('</x-table>', "</x-table>\n            <div class=\"mt-4 px-4 pb-4\">\n                {{ \$dataRutinTablePaginated->links() }}\n            </div>", $bladeContent);

file_put_contents($blade, $bladeContent);
echo "Blade paginated.\n";
