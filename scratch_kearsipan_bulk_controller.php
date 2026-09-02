<?php
function updateController($filePath, $modelClass) {
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return;
    }
    
    $content = file_get_contents($filePath);
    
    if (strpos($content, 'destroyBulk') !== false) {
        echo "destroyBulk already exists in $filePath\n";
        return;
    }

    $bulkMethod = <<<EOD
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
        ]);

        \$count = 0;
        foreach(\$request->ids as \$val) {
            \$parts = explode('|', \$val);
            if(count(\$parts) == 3) {
                \$kelompok = \$parts[0];
                \$tahun = \$parts[1];
                \$bulan = \$parts[2];
                \\$modelClass::where('kelompok_tabel', \$kelompok)
                    ->where('tahun', \$tahun)
                    ->where('bulan', \$bulan)
                    ->delete();
                \$count++;
            }
        }

        return redirect()->back()->with('success', \$count . ' Data berhasil dihapus secara massal.');
    }
EOD;

    $content = str_replace('public function destroyBulan(Request $request)', $bulkMethod . "\n\n    public function destroyBulan(Request \$request)", $content);
    file_put_contents($filePath, $content);
    echo "Updated $filePath\n";
}

updateController('app/Http/Controllers/PaTekstualController.php', 'App\Models\PaTekstualData');
updateController('app/Http/Controllers/PaTeknikController.php', 'App\Models\PaTeknikData');
updateController('app/Http/Controllers/PaNonTekstualController.php', 'App\Models\PaNonTekstualData');
updateController('app/Http/Controllers/DofController.php', 'App\Models\DofData');
