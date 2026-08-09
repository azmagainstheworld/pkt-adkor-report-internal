<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JenisPerizinanMaster;
use App\Models\JasaKurirMaster;
use App\Models\PemeliharaanPeralatanMaster;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat Akun Admin Default
        User::create([
            'name' => 'Zahra',
            'email' => 'admin@adkor.com',
            'password' => Hash::make('password123'), // Password wajib di-hash
            'status' => 'aktif',
        ]);



        

    }
}