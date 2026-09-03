<?php
$content = file_get_contents('app/Http/Controllers/SuratController.php');

// Add model usage
if (strpos($content, 'use App\Models\SuratRekap;') === false) {
    $content = str_replace('use App\Models\Surat;', "use App\Models\Surat;\nuse App\Models\SuratRekap;", $content);
}
if (strpos($content, 'use App\Imports\SuratRekapImport;') === false) {
    $content = str_replace('use App\Imports\SuratImport;', "use App\Imports\SuratImport;\nuse App\Imports\SuratRekapImport;", $content);
}

// Helper method for merging
$mergeMethod = <<<'EOD'
    private function getMergedRekapData($tahunFilter, $bulanFilter)
    {
        $rekapManualQuery = \App\Models\SuratRekap::query();
        if ($tahunFilter != 'semua') $rekapManualQuery->where('tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $rekapManualQuery->where('bulan', $bulanFilter);
        $rekapManualData = $rekapManualQuery->get();

        $rekapDetailQuery = \App\Models\Surat::selectRaw("
                tahun, bulan,
                SUM(CASE WHEN jenis_surat = 'Surat Masuk' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis_surat = 'Surat Keluar' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_keluar
            ");
        if ($tahunFilter != 'semua') $rekapDetailQuery->where('tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $rekapDetailQuery->where('bulan', $bulanFilter);
        $rekapDetailData = $rekapDetailQuery->groupBy('tahun', 'bulan')->get();

        // Merge logic
        $merged = collect();
        $keys = $rekapManualData->map(function($i) { return $i->tahun . '_' . $i->bulan; })
            ->concat($rekapDetailData->map(function($i) { return $i->tahun . '_' . $i->bulan; }))
            ->unique();

        foreach ($keys as $key) {
            list($thn, $bln) = explode('_', $key);
            $manual = $rekapManualData->first(function($i) use ($thn, $bln) { return $i->tahun == $thn && $i->bulan == $bln; });
            $detail = $rekapDetailData->first(function($i) use ($thn, $bln) { return $i->tahun == $thn && $i->bulan == $bln; });

            $merged->push((object)[
                'tahun' => $thn,
                'bulan' => $bln,
                'total_masuk' => ($manual ? $manual->surat_masuk : 0) + ($detail ? $detail->total_masuk : 0),
                'total_keluar' => ($manual ? $manual->surat_keluar : 0) + ($detail ? $detail->total_keluar : 0),
            ]);
        }

        return $merged->sortBy(function($item) {
            return array_search($item->bulan, $this->masterMonths);
        })->values();
    }
EOD;

if (strpos($content, 'private function getMergedRekapData') === false) {
    // Insert before index method
    $content = str_replace('public function index(Request $request)', $mergeMethod . "\n\n    public function index(Request $request)", $content);
}

// Replace the old rekap query in index
$oldIndexQuery = <<<'EOD'
        // --- A. QUERY TABEL 1 (REKAP DATA) ---
        $rekapQuery = Surat::selectRaw("
                tahun, bulan,
                SUM(CASE WHEN jenis_surat = 'Surat Masuk' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis_surat = 'Surat Keluar' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_keluar
            ");
            
        if ($tahunFilter != 'semua') $rekapQuery->where('tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $rekapQuery->where('bulan', $bulanFilter);
        
        $rekapData = $rekapQuery->groupBy('tahun', 'bulan')->get()->sortBy(function($item) {
            return array_search($item->bulan, $this->masterMonths);
        });
EOD;

$newIndexQuery = <<<'EOD'
        // --- A. QUERY TABEL 1 (REKAP DATA) ---
        $rekapData = $this->getMergedRekapData($tahunFilter, $bulanFilter);
EOD;

$content = str_replace($oldIndexQuery, $newIndexQuery, $content);

// Replace the old rekap query in exportPdf
$oldPdfQuery = <<<'EOD'
        $rekapData = Surat::selectRaw("
            tahun, bulan,
            SUM(CASE WHEN jenis_surat = 'Surat Masuk' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_masuk,
            SUM(CASE WHEN jenis_surat = 'Surat Keluar' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_keluar
        ")
        ->where('tahun', $tahunFilter)
        ->when($bulanFilter != 'semua', function($q) use ($bulanFilter) {
            return $q->where('bulan', $bulanFilter);
        })
        ->groupBy('tahun', 'bulan')
        ->get()
        ->sortBy(function($item) {
            return array_search($item->bulan, $this->masterMonths);
        });
EOD;
$newPdfQuery = "        \$rekapData = \$this->getMergedRekapData(\$tahunFilter, \$bulanFilter);";
$content = str_replace($oldPdfQuery, $newPdfQuery, $content);

// Add importRekapExcel
$importRekapMethod = <<<'EOD'
    public function importRekapExcel(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:51200',
        ]);

        try {
            Excel::import(new SuratRekapImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data rekapitulasi histori berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
EOD;

if (strpos($content, 'public function importRekapExcel') === false) {
    $content = str_replace('public function importExcel', $importRekapMethod . "\n\n    public function importExcel", $content);
}

file_put_contents('app/Http/Controllers/SuratController.php', $content);
echo "SuratController updated.\n";
