<?php

$filePath = 'app/Http/Controllers/PengirimanDokumenController.php';
$content = file_get_contents($filePath);

// Rename costRecords to volumeRecords and fetch ongkirRecords
$indexOld = <<<PHP
        \$costQueryBuilder = clone \$baseQuery;
        \$costQueryBuilder->orderBy('tahun', 'desc');
        \$costQueryBuilder->orderByRaw("FIELD(bulan, '" . implode("','", \$this->masterMonths) . "')");
        \$costRecords = \$costQueryBuilder->paginate(5)->withQueryString(); 

        \$ongkirBaseQuery = \App\Models\PengirimanOngkir::query();
        if (\$selectedYear != 'semua') \$ongkirBaseQuery->where('tahun', \$selectedYear);
        if (\$selectedMonth != 'semua') \$ongkirBaseQuery->where('bulan', \$selectedMonth);
        if (\$request->filled('search')) {
            \$search = \$request->search;
            \$ongkirBaseQuery->where(function(\$q) use (\$search) {
                \$q->where('tahun', 'like', "%{\$search}%")->orWhere('bulan', 'like', "%{\$search}%")
                  ->orWhere('ongkir_dalam_negeri', 'like', "%{\$search}%")->orWhere('ongkir_luar_negeri', 'like', "%{\$search}%");
            });
        }
        
        \$totalDomestikOverall = (clone \$ongkirBaseQuery)->sum('ongkir_dalam_negeri');
        \$totalInternasionalOverall = (clone \$ongkirBaseQuery)->sum('ongkir_luar_negeri');
        \$kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pengiriman_dokumen')->get();

        return view('pengiriman-dokumen', compact(
            'selectedYear', 'selectedMonth', 'availableYears', 'chartVolumeConfig',
            'costRecords', 'totalDomestikOverall', 'totalInternasionalOverall', 'kolomDinamis'
        ));
PHP;

$indexNew = <<<PHP
        \$volumeQueryBuilder = clone \$baseQuery;
        \$volumeQueryBuilder->orderBy('tahun', 'desc');
        \$volumeQueryBuilder->orderByRaw("FIELD(bulan, '" . implode("','", \$this->masterMonths) . "')");
        \$volumeRecords = \$volumeQueryBuilder->paginate(5, ['*'], 'volume_page')->withQueryString(); 

        \$ongkirBaseQuery = \App\Models\PengirimanOngkir::query();
        if (\$selectedYear != 'semua') \$ongkirBaseQuery->where('tahun', \$selectedYear);
        if (\$selectedMonth != 'semua') \$ongkirBaseQuery->where('bulan', \$selectedMonth);
        if (\$request->filled('search')) {
            \$search = \$request->search;
            \$ongkirBaseQuery->where(function(\$q) use (\$search) {
                \$q->where('tahun', 'like', "%{\$search}%")->orWhere('bulan', 'like', "%{\$search}%")
                  ->orWhere('ongkir_dalam_negeri', 'like', "%{\$search}%")->orWhere('ongkir_luar_negeri', 'like', "%{\$search}%");
            });
        }
        
        \$ongkirQueryBuilder = clone \$ongkirBaseQuery;
        \$ongkirQueryBuilder->orderBy('tahun', 'desc');
        \$ongkirQueryBuilder->orderByRaw("FIELD(bulan, '" . implode("','", \$this->masterMonths) . "')");
        \$ongkirRecords = \$ongkirQueryBuilder->paginate(5, ['*'], 'ongkir_page')->withQueryString();

        \$totalDomestikOverall = (clone \$ongkirBaseQuery)->sum('ongkir_dalam_negeri');
        \$totalInternasionalOverall = (clone \$ongkirBaseQuery)->sum('ongkir_luar_negeri');
        \$kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pengiriman_dokumen')->get();

        return view('pengiriman-dokumen', compact(
            'selectedYear', 'selectedMonth', 'availableYears', 'chartVolumeConfig',
            'volumeRecords', 'ongkirRecords', 'totalDomestikOverall', 'totalInternasionalOverall', 'kolomDinamis'
        ));
PHP;
$content = str_replace($indexOld, $indexNew, $content);

file_put_contents($filePath, $content);
echo "PengirimanDokumenController updated for volumeRecords and ongkirRecords.\n";
