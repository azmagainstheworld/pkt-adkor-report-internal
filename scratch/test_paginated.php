<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new App\Http\Controllers\PaTekstualController();
$req = Illuminate\Http\Request::create('/kearsipan/pa-non-teknik/tekstual');
$res = $c->index($req);
$paginated = $res->getData()['paginatedTable1'];
print_r($paginated->items()[0]);
