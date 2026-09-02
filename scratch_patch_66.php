<?php
$path = 'app/Imports/JasaKurirImport.php';
$content = file_get_contents($path);

// Replace truncate with delete to avoid implicit transaction commit
$content = str_replace('JasaKurirData::truncate();', 'JasaKurirData::query()->delete();', $content);

file_put_contents($path, $content);
echo "JasaKurirImport truncate fixed!\n";
?>
