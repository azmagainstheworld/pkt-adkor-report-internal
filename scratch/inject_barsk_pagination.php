<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\BarSkMemoController.php';
$content = file_get_contents($file);

$pattern = '/\/\/\s*---\s*AMBIL DATA DOKUMEN\s*---.*usort\(\$rawData[^}]+\}\);/s';

$replacement = "
        // --- AMBIL DATA DOKUMEN ---
        \$rawDataQuery = DB::table('bar_sk_memo')
            ->whereIn('tahun', \$tahunScope)
            ->whereIn('bulan', \$bulanScope)
            ->get()->toArray();

        usort(\$rawDataQuery, function(\$a, \$b) use (\$monthsOrder) {
            if(\$a->tahun == \$b->tahun) return \$monthsOrder[\$a->bulan] <=> \$monthsOrder[\$b->bulan];
            return \$a->tahun <=> \$b->tahun; 
        });

        // Pisahkan Data Terbit dan Proses
        \$terbitCollection = collect(\$rawDataQuery)->filter(function(\$row) {
            return \$row->skd_keputusan_bersama_terbit || \$row->skd_non_ratifikasi_terbit || 
            \$row->skd_ratifikasi_terbit || \$row->memo_direksi_terbit || \$row->bar_monitoring_terbit || 
            \$row->bar_manajemen_terbit || (\$row->data_tambahan ?? null);
        })->values();

        \$prosesCollection = collect(\$rawDataQuery)->filter(function(\$row) {
            return \$row->proses_skd_keputusan_bersama || \$row->proses_skd_non_ratifikasi || 
            \$row->proses_skd_ratifikasi || \$row->proses_memo_direksi || \$row->proses_bar_monitoring || 
            \$row->proses_bar_manajemen || (\$row->data_tambahan_proses ?? null);
        })->values();

        \$perPage = 10;
        \$pageTerbit = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('page_terbit');
        \$dataTerbit = new \Illuminate\Pagination\LengthAwarePaginator(
            \$terbitCollection->slice((\$pageTerbit - 1) * \$perPage, \$perPage)->all(),
            \$terbitCollection->count(), \$perPage, \$pageTerbit, 
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page_terbit']
        );
        \$dataTerbit->appends(request()->all());

        \$pageProses = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('page_proses');
        \$dataProses = new \Illuminate\Pagination\LengthAwarePaginator(
            \$prosesCollection->slice((\$pageProses - 1) * \$perPage, \$perPage)->all(),
            \$prosesCollection->count(), \$perPage, \$pageProses, 
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page_proses']
        );
        \$dataProses->appends(request()->all());

        \$rawData = \$rawDataQuery; // Keep it for charts if needed
";

$content = preg_replace($pattern, $replacement, $content);
file_put_contents($file, $content);
echo "Pagination injected.\n";
