<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = Illuminate\Http\Request::create('/kearsipan/pa-non-teknik/tekstual');
$res = app()->make(Illuminate\Contracts\Http\Kernel::class)->handle($req);
$html = $res->getContent();

$count = substr_count($html, '<tr class="hover:bg-gray-50');
echo "Number of TRs: " . $count . "\n";
