<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekapMaster;

class RekapSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriAwal = [
            'Pusat Arsip',
            'Teknikal File',
            'Approval File Scan',
            'Approval Stampel Digital',
            'Digital Signature',
            'DOF',
            'E-materai'
        ];

        foreach ($kategoriAwal as $nama) {
            RekapMaster::firstOrCreate(['nama_kegiatan' => $nama]);
        }
    }
}