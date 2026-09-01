<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\MasalahKendalaController.php';
$content = file_get_contents($file);

$search = <<<EOT
        // Sorting agar urut dari bulan terbaru
        \$monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        \$dataMasalah = \$query->get()->sortByDesc(function(\$item) use (\$monthsOrder) {
            return sprintf('%04d%02d', \$item->tahun, \$monthsOrder[\$item->bulan] ?? 0);
        });
EOT;

$replace = <<<EOT
        // Sorting agar urut dari bulan terbaru
        \$monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        \$allData = \$query->get()->sortByDesc(function(\$item) use (\$monthsOrder) {
            return sprintf('%04d%02d', \$item->tahun, \$monthsOrder[\$item->bulan] ?? 0);
        });

        \$perPage = 10;
        \$currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        \$currentItems = \$allData->slice((\$currentPage - 1) * \$perPage, \$perPage)->all();
        \$dataMasalah = new \Illuminate\Pagination\LengthAwarePaginator(\$currentItems, count(\$allData), \$perPage, \$currentPage, ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]);
        \$dataMasalah->appends(request()->all());
EOT;

if (strpos($content, $search) !== false) {
    $content = str_replace($search, $replace, $content);
    file_put_contents($file, $content);
    echo "SUCCESS\n";
} else {
    echo "NOT FOUND\n";
}
