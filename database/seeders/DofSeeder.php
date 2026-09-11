<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DofMaster;

class DofSeeder extends Seeder
{
    public function run(): void
    {
        // Tabel 1: Digital Signature
        $tabel1 = [
            'Approval Stempel Digital',
            'Approval File Scan',
            'Digital Signature',
            'Pendaftaran Akun',
            'Perekaman Akun'
        ];

        // Tabel 2: Laporan DOF
        $tabel2 = [
            'Revisi DOF',
            'Pembatalan DOF',
            'Pembuatan Template',
            'Cek Error',
            'Problem DOF',
            'E-meterai'
        ];

        foreach ($tabel1 as $nama) {
            DofMaster::firstOrCreate(['kelompok_tabel' => 1, 'nama_kegiatan' => $nama]);
        }
        foreach ($tabel2 as $nama) {
            DofMaster::firstOrCreate(['kelompok_tabel' => 2, 'nama_kegiatan' => $nama]);
        }
    }
}