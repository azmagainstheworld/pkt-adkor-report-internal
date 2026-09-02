<?php
// 1. Remove confirm dialog from frontend
$viewPath = 'resources/views/pelaporan.blade.php';
$viewContent = file_get_contents($viewPath);
$viewContent = str_replace('onsubmit="return confirm(\'Hapus data terpilih?\')"', '', $viewContent);
file_put_contents($viewPath, $viewContent);
echo "Removed browser confirmation from frontend!\n";

// 2. Fix exists validation rule in controller
$ctrlPath = 'app/Http/Controllers/PelaporanController.php';
$ctrlContent = file_get_contents($ctrlPath);
$ctrlContent = str_replace('\'exists:pelaporans,id\'', '\'exists:pelaporan,id\'', $ctrlContent);
file_put_contents($ctrlPath, $ctrlContent);
echo "Fixed table name in validation rule!\n";
?>
