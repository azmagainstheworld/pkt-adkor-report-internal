<?php
$file = 'app/Http/Controllers/KaryawanController.php';
$content = file_get_contents($file);

$content = str_replace(
    "\App\Models\DynamicColumn::where",
    "\Illuminate\Support\Facades\DB::table('dynamic_columns')->where",
    $content
);

file_put_contents($file, $content);
echo "Fixed DB table reference in KaryawanController.\n";
