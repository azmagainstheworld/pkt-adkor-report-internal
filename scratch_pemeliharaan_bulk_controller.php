<?php
$content = file_get_contents('app/Http/Controllers/PemeliharaanController.php');

$bulkMethodRutin = <<<'EOD'
    public function destroyRutinBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $count = 0;
        foreach($request->ids as $val) {
            $parts = explode('|', $val);
            if(count($parts) == 2) {
                $tahun = $parts[0];
                $bulan = $parts[1];
                \App\Models\PemeliharaanRutinData::where('tahun', $tahun)->where('bulan', $bulan)->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data pemeliharaan rutin berhasil dihapus.');
    }
EOD;

$bulkMethodPeralatan = <<<'EOD'
    public function destroyPeralatanBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $count = 0;
        foreach($request->ids as $val) {
            $parts = explode('|', $val);
            if(count($parts) == 2) {
                $tahun = $parts[0];
                $bulan = $parts[1];
                \App\Models\PemeliharaanPeralatanData::where('tahun', $tahun)->where('bulan', $bulan)->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data perbaikan peralatan berhasil dihapus.');
    }
EOD;

if (strpos($content, 'destroyRutinBulk') === false) {
    $content = str_replace('public function destroyRutinBulan(Request $request)', $bulkMethodRutin . "\n\n    public function destroyRutinBulan(Request \$request)", $content);
}
if (strpos($content, 'destroyPeralatanBulk') === false) {
    $content = str_replace('public function destroyPeralatanBulan(Request $request)', $bulkMethodPeralatan . "\n\n    public function destroyPeralatanBulan(Request \$request)", $content);
}

file_put_contents('app/Http/Controllers/PemeliharaanController.php', $content);
echo "Added destroyBulk methods to PemeliharaanController.php\n";
