<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$compiler = app('blade.compiler');
try {
    $compiled = $compiler->compileString(file_get_contents('resources/views/undangan.blade.php'));
    file_put_contents('scratch_compiled.php', $compiled);
    echo "Compiled successfully.\n";
} catch (\Exception $e) {
    echo "Error compiling: " . $e->getMessage() . "\n";
}
?>
