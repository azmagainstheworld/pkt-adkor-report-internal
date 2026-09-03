<?php

$file = 'app/Http/Controllers/PaTekstualController.php';
$content = file_get_contents($file);

$target_exportPdf = <<<'EOF'
    public function exportPdf(Request $request)
    {
        $kelompok = $request->kelompok_tabel ?? 1;
        $masterTabel = PaTekstualMaster::where('kelompok_tabel', $kelompok)->orderBy('id', 'asc')->get();
        
        $masterIds = PaTekstualMaster::where('kelompok_tabel', $kelompok)->pluck('id');
        $query = PaTekstualData::with('masterTekstual')->whereIn('master_id', $masterIds);
        if ($request->tahun && $request->tahun != 'semua') $query->where('tahun', $request->tahun);
        if ($request->bulan && $request->bulan != 'semua') $query->where('bulan', $request->bulan);
        
        $rawData = $query->get();
        $groupedData = $rawData->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        
        $dataTable = [];
        $totals = array_fill_keys($masterTabel->pluck('id')->toArray(), 0);
        
        foreach($groupedData as $key => $items) {
            $parts = explode('_', $key);
            $tahun = $parts[0]; $bulan = $parts[1];
            $row = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];
            
            foreach($items as $item) {
                $row['items'][$item->master_id] = $item->jumlah;
                if(isset($totals[$item->master_id])) {
                    $totals[$item->master_id] += $item->jumlah;
                }
            }
            if(count($row['items']) > 0) $dataTable[] = $row;
        }
        
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        usort($dataTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        $pdf = Pdf::loadView('pdf.pa-tekstual', compact('dataTable', 'masterTabel', 'totals', 'kelompok'))
                  ->setPaper('a4', 'landscape');
        
        return $pdf->stream('Laporan_PA_Tekstual_Tabel_' . $kelompok . '.pdf');
    }
EOF;

$replacement_exportPdf = <<<'EOF'
    public function exportPdf(Request $request)
    {
        $kelompok = $request->kelompok_tabel ?? 1;
        $masterTabel = PaTekstualMaster::where('kelompok_tabel', $kelompok)->orderBy('id', 'asc')->get();
        $kolomTabel = PaTekstualKolom::where('kelompok_tabel', $kelompok)->orderBy('id', 'asc')->get();
        
        $masterIds = $masterTabel->pluck('id');
        $query = PaTekstualData::with('masterTekstual')->whereIn('master_id', $masterIds);
        if ($request->tahun && $request->tahun != 'semua') $query->where('tahun', $request->tahun);
        if ($request->bulan && $request->bulan != 'semua') $query->where('bulan', $request->bulan);
        
        $rawData = $query->get();
        $groupedData = $rawData->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        
        $dataTable = [];
        $totals = array_fill_keys($masterTabel->pluck('id')->toArray(), 0);
        
        foreach($groupedData as $key => $items) {
            $parts = explode('_', $key);
            $tahun = $parts[0]; $bulan = $parts[1];
            $row = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];
            
            foreach($items as $item) {
                $row['items'][$item->master_id] = $item->jumlah;
                if(isset($totals[$item->master_id])) {
                    $totals[$item->master_id] += $item->jumlah;
                }
                if (!empty($item->data_tambahan)) {
                    $row['data_tambahan'] = array_merge($row['data_tambahan'], $item->data_tambahan);
                }
            }
            if(count($row['items']) > 0) $dataTable[] = $row;
        }
        
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        usort($dataTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        $pdf = Pdf::loadView('pdf.pa-tekstual', compact('dataTable', 'masterTabel', 'kolomTabel', 'totals', 'kelompok'))
                  ->setPaper('a4', 'landscape');
        
        return $pdf->stream('Laporan_PA_Tekstual_Tabel_' . $kelompok . '.pdf');
    }
EOF;
$content = str_replace($target_exportPdf, $replacement_exportPdf, $content);
file_put_contents($file, $content);
echo "exportPdf updated for dynamic columns.\n";
