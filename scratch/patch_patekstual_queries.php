<?php

$file = 'app/Http/Controllers/PaTekstualController.php';
$content = file_get_contents($file);

// Fix exportPdf
$target_exportPdf = <<<'EOF'
        $query = PaTekstualData::with('masterTekstual')->where('kelompok_tabel', $kelompok);
EOF;
$replacement_exportPdf = <<<'EOF'
        $masterIds = PaTekstualMaster::where('kelompok_tabel', $kelompok)->pluck('id');
        $query = PaTekstualData::with('masterTekstual')->whereIn('master_id', $masterIds);
EOF;
$content = str_replace($target_exportPdf, $replacement_exportPdf, $content);

// Fix destroyBulk (delete_all logic)
$target_destroyBulk1 = <<<'EOF'
            $query = \App\Models\PaTekstualData::where('kelompok_tabel', $kelompok);
EOF;
$replacement_destroyBulk1 = <<<'EOF'
            $masterIds = \App\Models\PaTekstualMaster::where('kelompok_tabel', $kelompok)->pluck('id');
            $query = \App\Models\PaTekstualData::whereIn('master_id', $masterIds);
EOF;
$content = str_replace($target_destroyBulk1, $replacement_destroyBulk1, $content);

// Fix destroyBulk (specific ids logic)
$target_destroyBulk2 = <<<'EOF'
                \App\Models\PaTekstualData::where('kelompok_tabel', $kelompok)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->delete();
EOF;
$replacement_destroyBulk2 = <<<'EOF'
                $masterIds = \App\Models\PaTekstualMaster::where('kelompok_tabel', $kelompok)->pluck('id');
                \App\Models\PaTekstualData::whereIn('master_id', $masterIds)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->delete();
EOF;
$content = str_replace($target_destroyBulk2, $replacement_destroyBulk2, $content);

file_put_contents($file, $content);
echo "Controller queries patched.\n";
