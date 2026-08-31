<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DofMaster;

// The old IDs were:
// Registrasi Surat Masuk via DOF -> 1 (Deleted)
// Approval File Scan -> 2
// Revisi DOF -> 3
// Pembatalan DOF -> 4
// Pembuatan Template -> 5
// Cek Error -> 6
// Problem DOF -> 7
// E-meterai -> 8
// Approval Stempel Digital -> 9
// Pendaftaran Akun -> 10
// Perekaman Akun -> 11
// Digital Signature -> 12

// The new IDs are:
// 1 - Approval Stempel Digital
// 2 - Approval File Scan
// 3 - Digital Signature
// 4 - Pendaftaran Akun
// 5 - Perekaman Akun
// 6 - Revisi DOF
// 7 - Pembatalan DOF
// 8 - Pembuatan Template
// 9 - Cek Error
// 10 - Problem DOF
// 11 - E-meterai

$oldToNew = [
    2 => 2, // Approval File Scan
    3 => 6, // Revisi DOF
    4 => 7, // Pembatalan DOF
    5 => 8, // Pembuatan Template
    6 => 9, // Cek Error
    7 => 10, // Problem DOF
    8 => 11, // E-meterai
    9 => 1, // Approval Stempel Digital
    10 => 4, // Pendaftaran Akun
    11 => 5, // Perekaman Akun
    12 => 3, // Digital Signature
];

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Update dof_data master_id mapping
// Because I shifted IDs to 9999+id during the swap, let's just match them by name directly!
// Wait, I didn't update dof_data when I did the swap, so dof_data STILL has the original IDs 1-12!
foreach ($oldToNew as $oldId => $newId) {
    // We update to a temporary high ID to avoid collisions during the update loop
    DB::table('dof_data')->where('master_id', $oldId)->update(['master_id' => $newId + 1000]);
}

// Now remove the +1000
DB::table('dof_data')->where('master_id', '>', 1000)->update(['master_id' => DB::raw('master_id - 1000')]);

DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Fixed data mapping!\n";
