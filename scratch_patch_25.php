<?php
$path = 'app/Http/Controllers/PerizinanPerkantoranController.php';
$content = file_get_contents($path);

// Fix double quotes to single quotes for SQL string literals
$content = str_replace('kegiatan = "Produk"', "kegiatan = 'Produk'", $content);
$content = str_replace('kegiatan = "Aset"', "kegiatan = 'Aset'", $content);
$content = str_replace('kegiatan = "Proyek"', "kegiatan = 'Proyek'", $content);
$content = str_replace('kegiatan = "Peralatan Pabrik"', "kegiatan = 'Peralatan Pabrik'", $content);
$content = str_replace('kegiatan = "Adm & Lainnya"', "kegiatan = 'Adm & Lainnya'", $content);

file_put_contents($path, $content);
echo "Fixed double quotes in PerizinanPerkantoranController\n";

$bladePath = 'resources/views/perizinan-perkantoran.blade.php';
$blade = file_get_contents($bladePath);

// Change Mode Hapus Massal to Hapus semua
$blade = str_replace('>Mode Hapus Massal<', '>Hapus semua<', $blade);
// Remove browser confirms
$blade = str_replace('onsubmit="return confirm(\'Hapus data terpilih?\')"','', $blade);

// Now, we need to extract the broken script blocks that were injected before TABEL 2
// and place them safely at the end of the file.
// The broken block starts around `<script>\n        // TABEL 2 (TERBIT)`
// and ends right before `<!-- ================= TABEL 2`

$startPattern = '<script>
        // TABEL 2 (TERBIT)';
$endPattern = '<!-- ================= TABEL 2: RINCIAN PERIZINAN';

$posStart = strpos($blade, $startPattern);
$posEnd = strpos($blade, $endPattern);

if ($posStart !== false && $posEnd !== false && $posStart < $posEnd) {
    // Extract everything in between
    $extracted = substr($blade, $posStart, $posEnd - $posStart);
    // Remove it from the original place
    $blade = substr_replace($blade, '', $posStart, $posEnd - $posStart);
    
    // Clean up the extracted block (remove nested <script> tags)
    $extracted = str_replace('<script>', '', $extracted);
    $extracted = str_replace('</script>', '', $extracted);
    
    // Now append it to the end of the file, right before @endsection
    $endsectionPos = strrpos($blade, '@endsection');
    if ($endsectionPos !== false) {
        $cleanScript = "\n<script>\n" . trim($extracted) . "\n</script>\n";
        $blade = substr_replace($blade, $cleanScript, $endsectionPos, 0);
    }
    
    file_put_contents($bladePath, $blade);
    echo "Fixed JS syntax and placement in perizinan-perkantoran.blade.php\n";
} else {
    echo "Could not find the script block to move.\n";
}

?>
