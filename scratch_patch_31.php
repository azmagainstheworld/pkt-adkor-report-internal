<?php
$path = 'scratch_bulk_perizinan.php';
$content = file_get_contents($path);

// Fix the missing </script> tag
$content = str_replace('// 4. Add JS script', "// 4. Add JS script", $content);
$content = preg_replace("/\\\$content = preg_replace\('\/\(\<script\>\\\\s\*function openModal\)\/s', \\\$js . \"\\\\n\\\$1\", \\\$content, 1\);/", '$content = preg_replace(\'/(<script>\s*function openModal)/s\', $js . "\n</script>\n$1", $content, 1);', $content);

// Change Mode Hapus Massal to Hapus semua
$content = str_replace('Mode Hapus Massal', 'Hapus semua', $content);

// Remove onsubmit
$content = str_replace('onsubmit="return confirm(\'Hapus data perizinan terbit terpilih?\')"','', $content);
$content = str_replace('onsubmit="return confirm(\'Hapus data perizinan proses terpilih?\')"','', $content);

file_put_contents($path, $content);
echo "Fixed scratch_bulk_perizinan.php\n";
?>
