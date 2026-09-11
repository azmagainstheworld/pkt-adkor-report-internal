<?php
/**
 * Script to safely append getReportData() to multiple controllers.
 * Uses simple string search for the LAST "}" in the class, then inserts before it.
 */

$controllers = [
    'app/Http/Controllers/PaTekstualController.php' => [
        'marker' => "    public function destroyBulk(\\Illuminate\\Http\\Request \$request)",
        'method' => <<<'PHP'

    /**
     * Mengambil data PA Tekstual untuk laporan PDF bulanan.
     */
    public static function getReportData($tahun, $bulan)
    {
        $paTekstual = \App\Models\PaTekstualData::with('masterTekstual')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'nama_dokumen' => $item->masterTekstual->nama_dokumen ?? '-',
                    'jumlah' => $item->jumlah,
                ];
            });

        \Log::info('[PDF Section] PA Tekstual', ['bulan' => $bulan, 'tahun' => $tahun, 'count' => $paTekstual->count()]);

        return ['paTekstual' => $paTekstual];
    }
PHP
    ],

    'app/Http/Controllers/PaNonTekstualController.php' => [
        'marker' => "    public function destroyBulk(\\Illuminate\\Http\\Request \$request)",
        'method' => <<<'PHP'

    /**
     * Mengambil data PA Non Tekstual untuk laporan PDF bulanan.
     */
    public static function getReportData($tahun, $bulan)
    {
        $paNonTekstual = \App\Models\PaNonTekstualValue::with('type')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'jenis'  => $item->type->name ?? '-',
                    'jumlah' => $item->jumlah,
                ];
            });

        \Log::info('[PDF Section] PA Non Tekstual', ['bulan' => $bulan, 'tahun' => $tahun, 'count' => $paNonTekstual->count()]);

        return ['paNonTekstual' => $paNonTekstual];
    }
PHP
    ],

    'app/Http/Controllers/PaTeknikController.php' => [
        'marker' => "    public function destroyBulk(\\Illuminate\\Http\\Request \$request)",
        'method' => <<<'PHP'

    /**
     * Mengambil data PA Teknik untuk laporan PDF bulanan.
     */
    public static function getReportData($tahun, $bulan)
    {
        $paTeknik = \App\Models\PaTeknikData::with('masterTeknik')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'nama_kegiatan' => $item->masterTeknik->nama_kegiatan ?? '-',
                    'jumlah' => $item->jumlah,
                ];
            });

        \Log::info('[PDF Section] PA Teknik', ['bulan' => $bulan, 'tahun' => $tahun, 'count' => $paTeknik->count()]);

        return ['paTeknik' => $paTeknik];
    }
PHP
    ],

    'app/Http/Controllers/DofController.php' => [
        'marker' => "        public function destroyBulk(\\Illuminate\\Http\\Request \$request)",
        'method' => <<<'PHP'

    /**
     * Mengambil data DOF untuk laporan PDF bulanan.
     */
    public static function getReportData($tahun, $bulan)
    {
        $dof = \App\Models\DofData::with('masterDof')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'nama_kegiatan' => $item->masterDof->nama_kegiatan ?? '-',
                    'jumlah' => $item->jumlah,
                ];
            });

        \Log::info('[PDF Section] DOF', ['bulan' => $bulan, 'tahun' => $tahun, 'count' => $dof->count()]);

        return ['dof' => $dof];
    }
PHP
    ],

    'app/Http/Controllers/MasalahKendalaController.php' => [
        'marker' => "    public function destroy(\$id)",
        'method' => <<<'PHP'

    /**
     * Mengambil data Masalah Kendala untuk laporan PDF bulanan.
     */
    public static function getReportData($tahun, $bulan)
    {
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];

        $kendala = \App\Models\MasalahKendala::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get()
            ->sortBy(function($item) use ($monthsOrder) {
                return sprintf('%04d%02d', $item->tahun, $monthsOrder[$item->bulan] ?? 0);
            })
            ->values();

        \Log::info('[PDF Section] Masalah Kendala', ['bulan' => $bulan, 'tahun' => $tahun, 'count' => $kendala->count()]);

        return ['kendala' => $kendala];
    }
PHP
    ],
];

foreach ($controllers as $file => $config) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }
    $content = file_get_contents($file);

    // Check if getReportData already exists
    if (strpos($content, 'public static function getReportData') !== false) {
        echo "SKIP (already has getReportData): $file\n";
        continue;
    }

    $marker = $config['marker'];
    $method = $config['method'];

    // Find marker in content
    $pos = strpos($content, $marker);
    if ($pos === false) {
        // Try finding last }
        echo "Marker not found in $file, will append before last }\n";
        $lastClose = strrpos($content, "\n}");
        if ($lastClose !== false) {
            $content = substr($content, 0, $lastClose) . "\n" . $method . "\n}" . substr($content, $lastClose + 2);
            file_put_contents($file, $content);
            echo "Appended to: $file\n";
        }
        continue;
    }

    // Insert method BEFORE the marker
    $content = substr($content, 0, $pos) . $method . "\n" . substr($content, $pos);
    file_put_contents($file, $content);
    echo "Done: $file\n";
}

echo "\nAll done!\n";
