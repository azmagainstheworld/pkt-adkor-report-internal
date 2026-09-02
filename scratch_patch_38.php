<?php
$path = 'app/Http/Controllers/PerizinanPerkantoranController.php';
$content = file_get_contents($path);

// Patch destroyBulkTerbit
$searchTerbit = <<<EOD
    public function destroyBulkTerbit(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perizinan_terbit,id',
        ]);

        \App\Models\PerizinanTerbit::whereIn('id', \$request->ids)->delete();

        return redirect()->back()->with('success', count(\$request->ids) . ' Data perizinan terbit berhasil dihapus.');
    }
EOD;

$replaceTerbit = <<<EOD
    public function destroyBulkTerbit(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perizinan_terbit,id',
        ]);

        if (\$request->delete_all_pages == '1') {
            \$query = \App\Models\PerizinanTerbit::query();
            if (\$request->filter_tahun && \$request->filter_tahun != 'semua') {
                \$query->whereYear('tanggal_sejak', \$request->filter_tahun);
            }
            if (\$request->filter_bulan && \$request->filter_bulan != 'semua') {
                \$mapBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
                \$monthNum = \$mapBulan[\$request->filter_bulan] ?? null;
                if (\$monthNum) \$query->whereMonth('tanggal_sejak', \$monthNum);
            }
            \$count = \$query->count();
            \$query->delete();
            return redirect()->back()->with('success', \$count . ' Seluruh Data perizinan terbit yang difilter berhasil dihapus.');
        } else {
            \App\Models\PerizinanTerbit::whereIn('id', \$request->ids)->delete();
            return redirect()->back()->with('success', count(\$request->ids) . ' Data perizinan terbit berhasil dihapus.');
        }
    }
EOD;

// Normalize line endings to avoid search failure
$content = str_replace("\r\n", "\n", $content);
$searchTerbit = str_replace("\r\n", "\n", $searchTerbit);

if (strpos($content, "if (\$request->delete_all_pages == '1')") === false) {
    $content = str_replace($searchTerbit, $replaceTerbit, $content);
}

// Patch destroyBulkProses
$searchProses = <<<EOD
    public function destroyBulkProses(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perizinan_proses_list,id',
        ]);

        \App\Models\PerizinanProsesList::whereIn('id', \$request->ids)->delete();

        return redirect()->back()->with('success', count(\$request->ids) . ' Data perizinan proses berhasil dihapus.');
    }
EOD;

$replaceProses = <<<EOD
    public function destroyBulkProses(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perizinan_proses_list,id',
        ]);

        if (\$request->delete_all_pages == '1') {
            \$query = \App\Models\PerizinanProsesList::query();
            if (\$request->filter_tahun && \$request->filter_tahun != 'semua') {
                \$query->where('tahun', \$request->filter_tahun);
            }
            \$count = \$query->count();
            \$query->delete();
            return redirect()->back()->with('success', \$count . ' Seluruh Data perizinan proses yang difilter berhasil dihapus.');
        } else {
            \App\Models\PerizinanProsesList::whereIn('id', \$request->ids)->delete();
            return redirect()->back()->with('success', count(\$request->ids) . ' Data perizinan proses berhasil dihapus.');
        }
    }
EOD;

$searchProses = str_replace("\r\n", "\n", $searchProses);

if (strpos($content, "Seluruh Data perizinan proses") === false) {
    $content = str_replace($searchProses, $replaceProses, $content);
}

file_put_contents($path, $content);
echo "Controller updated\n";
?>
