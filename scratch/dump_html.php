<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = Illuminate\Http\Request::create('/kearsipan/pa-non-teknik/tekstual');
$res = app()->make(Illuminate\Contracts\Http\Kernel::class)->handle($req);
file_put_contents('scratch/output.html', $res->getContent());
echo "HTML dumped.\n";
