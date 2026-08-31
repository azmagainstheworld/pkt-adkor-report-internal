<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

$request = Illuminate\Http\Request::create('/perizinan-perkantoran', 'GET');
$response = app()->handle($request);

echo 'HTTP CODE: ' . $response->getStatusCode() . "\n";
if ($response->getStatusCode() !== 200) {
    if (preg_match('/<title>(.*?)<\/title>/is', $response->getContent(), $matches)) {
        echo 'Error Title: ' . $matches[1] . "\n";
    }
}
