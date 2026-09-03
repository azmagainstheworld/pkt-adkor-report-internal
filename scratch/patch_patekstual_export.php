<?php

$file = 'app/Exports/PaTekstualExport.php';
$content = file_get_contents($file);

$target = <<<'EOF'
        $query = PaTekstualData::with('masterTekstual')
                    ->where('kelompok_tabel', $this->kelompokTabel)
                    ->orderBy('tahun', 'desc');
EOF;
$replacement = <<<'EOF'
        $masterIds = $this->masterCols->pluck('id')->toArray();
        $query = PaTekstualData::with('masterTekstual')
                    ->whereIn('master_id', $masterIds)
                    ->orderBy('tahun', 'desc');
EOF;

$content = str_replace($target, $replacement, $content);
file_put_contents($file, $content);
echo "Export query patched.\n";
