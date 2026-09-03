<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$compiled = app('blade.compiler')->compileString(file_get_contents('resources/views/surat-masuk-keluar.blade.php'));
file_put_contents('compiled.php', $compiled);
echo "Compiled to compiled.php\n";
