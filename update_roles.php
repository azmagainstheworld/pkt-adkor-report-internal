<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$superAdmin = User::where('name', 'superadminadkor')->first();
if ($superAdmin) {
    $superAdmin->role = 'super_admin';
    $superAdmin->save();
    echo "superadminadkor role updated to super_admin.\n";
} else {
    echo "superadminadkor not found.\n";
}

$zahra = User::where('name', 'Zahra')->orWhere('name', 'zahra')->first();
if ($zahra) {
    $zahra->role = 'admin';
    $zahra->save();
    echo "Zahra role updated to admin.\n";
} else {
    echo "Zahra not found.\n";
}

$karyawans = User::whereNotIn('id', [$superAdmin->id ?? 0, $zahra->id ?? 0])->get();
foreach ($karyawans as $k) {
    $k->role = 'karyawan';
    $k->save();
}
echo count($karyawans) . " other users set to karyawan.\n";
