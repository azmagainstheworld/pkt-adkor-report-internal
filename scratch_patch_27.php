<?php
$bladePath = 'resources/views/perizinan-perkantoran.blade.php';
$blade = file_get_contents($bladePath);

// Remove browser confirm for bulkDeleteFormProses
$blade = str_replace('onsubmit="return confirm(\'Hapus data perizinan proses terpilih?\')"','', $blade);

file_put_contents($bladePath, $blade);
echo "Removed browser confirm for proses\n";
?>
