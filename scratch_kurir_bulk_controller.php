<?php
$content = file_get_contents('app/Http/Controllers/JasaKurirController.php');

$bulkMethod = <<<'EOD'
    public function destroyBulk(\Illuminate\Http\Request $request)
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
                \App\Models\JasaKurirData::where('tahun', $tahun)->where('bulan', $bulan)->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data grup bulan berhasil dihapus.');
    }
EOD;

if (strpos($content, 'destroyBulk') === false) {
    $content = str_replace('public function destroyData($tahun, $bulan)', $bulkMethod . "\n\n    public function destroyData(\$tahun, \$bulan)", $content);
    file_put_contents('app/Http/Controllers/JasaKurirController.php', $content);
    echo "Added destroyBulk method to JasaKurirController.php\n";
} else {
    echo "Method already exists.\n";
}
