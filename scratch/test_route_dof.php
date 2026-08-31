<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/kearsipan/dof', 'GET');
$response = app()->handle($request);
echo "Status: " . $response->status() . "\n";
if ($response->status() != 200 && $response->status() != 302) {
    echo $response->getContent();
}
