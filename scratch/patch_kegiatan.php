<?php
$file = 'app/Http/Controllers/PemeliharaanController.php';
$content = file_get_contents($file);

// Replace nama_kegiatan with nama_pemeliharaan in storeMasterRutin
$content = str_replace(
    "\$request->validate(['nama_kegiatan' => 'required|string']);",
    "\$request->validate(['nama_pemeliharaan' => 'required|string']);",
    $content
);
$content = str_replace(
    "PemeliharaanRutinMaster::create(['nama_kegiatan' => \$request->nama_kegiatan]);",
    "PemeliharaanRutinMaster::create(['nama_pemeliharaan' => \$request->nama_pemeliharaan]);",
    $content
);

file_put_contents($file, $content);
echo "Controller patched successfully.\n";

$bladeFile = 'resources/views/pemeliharaan.blade.php';
$bladeContent = file_get_contents($bladeFile);

// Replace nama_kegiatan with nama_pemeliharaan
$bladeContent = str_replace('->nama_kegiatan', '->nama_pemeliharaan', $bladeContent);
$bladeContent = str_replace('name="nama_kegiatan"', 'name="nama_pemeliharaan"', $bladeContent);

file_put_contents($bladeFile, $bladeContent);
echo "Blade patched successfully.\n";
