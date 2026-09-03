<?php

$file = 'app/Http/Controllers/PaTekstualController.php';
$content = file_get_contents($file);

$imports = <<<'EOF'
use App\Models\PaTekstualData;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\PaTekstualExport;
use Barryvdh\DomPDF\Facade\Pdf;
EOF;

// Add exports and PDF to imports if not there
if (strpos($content, 'use App\Exports\PaTekstualExport;') === false) {
    $content = str_replace('use App\Models\PaTekstualData;', "use App\Models\PaTekstualData;\nuse App\Exports\PaTekstualExport;\nuse Barryvdh\DomPDF\Facade\Pdf;", $content);
}

// Replace importExcel, exportExcel, exportPdf and add storeMaster, destroyMaster
$target = <<<'EOF'
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);
        try {
            Excel::import(new PaTekstualImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil di-import dari Excel.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request) { /* TODO */ }
    public function exportPdf(Request $request) { /* TODO */ }
EOF;

$replacement = <<<'EOF'
    public function storeMaster(Request $request)
    {
        $request->validate([
            'kelompok_tabel' => 'required|in:1,2',
            'nama_dokumen' => 'required|string|max:255'
        ]);
        PaTekstualMaster::create([
            'kelompok_tabel' => $request->kelompok_tabel,
            'nama_dokumen' => trim($request->nama_dokumen)
        ]);
        return back()->with('success', 'Kolom kegiatan/dokumen baru berhasil ditambahkan.');
    }

    public function destroyMaster($id)
    {
        $master = PaTekstualMaster::findOrFail($id);
        $master->delete();
        return back()->with('success', 'Kolom kegiatan/dokumen berhasil dihapus beserta seluruh datanya.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls',
            'kelompok_tabel' => 'required|in:1,2'
        ]);
        try {
            $kelompok = $request->kelompok_tabel == 1 ? 'tabel1' : 'tabel2';
            Excel::import(new PaTekstualImport($kelompok), $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil di-import dari Excel.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $kelompok = $request->kelompok_tabel ?? 1;
        $isTemplate = $request->has('template');
        
        $filename = 'Laporan_PA_Tekstual_Tabel_' . $kelompok . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PaTekstualExport($kelompok, $isTemplate), $filename);
    }

    public function exportPdf(Request $request)
    {
        $kelompok = $request->kelompok_tabel ?? 1;
        $masterTabel = PaTekstualMaster::where('kelompok_tabel', $kelompok)->orderBy('id', 'asc')->get();
        
        $query = PaTekstualData::with('masterTekstual')->where('kelompok_tabel', $kelompok);
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

$content = str_replace($target, $replacement, $content);
file_put_contents($file, $content);
echo "PaTekstualController patched.\n";
