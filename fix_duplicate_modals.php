<?php

$filePath = 'resources/views/undangan.blade.php';
$lines = file($filePath);

// Find the second occurrence of '<x-modal id="modalAturKolom"'
$firstModalAturKolom = -1;
$secondModalAturKolom = -1;

foreach ($lines as $index => $line) {
    if (strpos($line, '<x-modal id="modalAturKolom"') !== false) {
        if ($firstModalAturKolom === -1) {
            $firstModalAturKolom = $index;
        } else {
            $secondModalAturKolom = $index;
            break;
        }
    }
}

if ($secondModalAturKolom !== -1) {
    // Find the end of this duplicated block, which is right before '<!-- ================= MODAL TAMBAH RINCIAN ================= -->'
    $endOfDuplicatedBlock = -1;
    for ($i = $secondModalAturKolom; $i < count($lines); $i++) {
        if (strpos($lines[$i], 'MODAL TAMBAH RINCIAN') !== false) {
            $endOfDuplicatedBlock = $i;
            break;
        }
    }
    
    if ($endOfDuplicatedBlock !== -1) {
        // Remove the duplicated lines
        array_splice($lines, $secondModalAturKolom, $endOfDuplicatedBlock - $secondModalAturKolom);
        file_put_contents($filePath, implode("", $lines));
        echo "Removed duplicated block from line " . ($secondModalAturKolom + 1) . " to " . ($endOfDuplicatedBlock) . "\n";
    } else {
        echo "Could not find end of duplicated block.\n";
    }
} else {
    echo "Could not find second modalAturKolom.\n";
}
