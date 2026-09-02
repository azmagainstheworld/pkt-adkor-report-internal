<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$super = User::firstOrCreate(
    ['name' => 'superadminadkor'],
    ['email' => 'superadminadkor@admin.com', 'password' => Hash::make('password123'), 'role' => 'super_admin']
);
if (!$super->wasRecentlyCreated) {
    $super->role = 'super_admin';
    $super->save();
}

$zahra = User::firstOrCreate(
    ['name' => 'Zahra'],
    ['email' => 'zahra@admin.com', 'password' => Hash::make('password123'), 'role' => 'admin']
);
if (!$zahra->wasRecentlyCreated) {
    $zahra->role = 'admin';
    $zahra->save();
}

echo "Created superadminadkor and Zahra.\n";
