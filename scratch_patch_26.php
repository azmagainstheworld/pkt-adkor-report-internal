<?php
$path = 'app/Http/Controllers/PerizinanPerkantoranController.php';
$content = file_get_contents($path);

// Fix PHP syntax error by escaping the single quotes inside the single-quoted selectRaw
$content = str_replace("= 'Produk'", "= \'Produk\'", $content);
$content = str_replace("= 'Aset'", "= \'Aset\'", $content);
$content = str_replace("= 'Proyek'", "= \'Proyek\'", $content);
$content = str_replace("= 'Peralatan Pabrik'", "= \'Peralatan Pabrik\'", $content);
$content = str_replace("= 'Adm & Lainnya'", "= \'Adm & Lainnya\'", $content);

file_put_contents($path, $content);
echo "Fixed PHP syntax in PerizinanPerkantoranController\n";
?>
