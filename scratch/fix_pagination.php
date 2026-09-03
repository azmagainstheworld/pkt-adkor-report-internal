<?php
$file = 'app/Http/Controllers/PemeliharaanController.php';
$content = file_get_contents($file);

$target = <<<'EOD'
        usort($dataRutinTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        // =========================================================================
EOD;

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
        $dataRutinTablePaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            count($dataRutinTable),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // =========================================================================
EOD;

$content = str_replace($target, $replacement, $content);
file_put_contents($file, $content);
echo "Fixed!\n";
