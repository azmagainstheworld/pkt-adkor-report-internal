<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;

$filePath = 'C:\Users\LENOVO\Downloads\Template_pemeliharaan-rutin (1).xlsx';
$copyPath = __DIR__.'/scratch/temp_upload.xlsx';
copy($filePath, $copyPath);

$file = new UploadedFile(
    $copyPath,
    'Template_pemeliharaan-rutin (1).xlsx',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    null,
    true
);

$request = \Illuminate\Http\Request::create('/administrasi/pemeliharaan/rutin/import', 'POST', [], [], ['file_excel' => $file]);
$response = $kernel->handle($request);

echo "Response status: " . $response->getStatusCode() . "\n";
echo "Records in DB: " . \App\Models\PemeliharaanRutinData::count() . "\n";
