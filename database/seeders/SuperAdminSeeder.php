<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Membuat atau memperbarui akun Super Admin tunggal sistem.
     *
     * Jalankan dengan: php artisan db:seed --class=SuperAdminSeeder
     *
     * PENTING: Ganti password default setelah pertama login!
     */
    public function run(): void
    {
        $email    = env('SUPER_ADMIN_EMAIL', 'superadmin@adkor.id');
        $password = env('SUPER_ADMIN_PASSWORD', 'SuperAdmin@123');
        $name     = env('SUPER_ADMIN_NAME', 'Super Administrator');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'      => $name,
                'role'      => 'super_admin',
                'password'  => Hash::make($password),
                'is_active' => true,
            ]
        );

        // Pastikan role selalu super_admin meski email sudah ada
        if ($user->role !== 'super_admin') {
            $user->update(['role' => 'super_admin']);
        }

        $this->command->info("Super Admin tunggal berhasil dibuat/diperbarui:");
        $this->command->info("  Email    : {$email}");
        $this->command->info("  Password : {$password}");
        $this->command->warn("  ⚠️  SEGERA GANTI PASSWORD setelah login pertama kali!");
    }
}
