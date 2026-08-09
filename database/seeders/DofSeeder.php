<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DofMaster;

class DofSeeder extends Seeder
{
    public function run(): void
    {
        // Tabel 1: Laporan DOF (Sesuai Gambar 3 Excel)
        $tabel1 = [
            'Registrasi Surat Masuk via DOF', 
            'Approval File Scan',
            'Revisi DOF', // Dari Gambar 2
            'Pembatalan DOF',
            'Pembuatan Template',
            'Cek Error',
            'Problem DOF',
            'E-meterai'
        ];

        // Tabel 2: Digital Signature
        $tabel2 = [
            'Approval Stempel Digital',
            'Pendaftaran Akun',
            'Perekaman Akun',
            'Digital Signature'
        ];

        foreach ($tabel1 as $nama) {
            DofMaster::firstOrCreate(['kelompok_tabel' => 1, 'nama_kegiatan' => $nama]);
        }
        foreach ($tabel2 as $nama) {
            DofMaster::firstOrCreate(['kelompok_tabel' => 2, 'nama_kegiatan' => $nama]);
        }
    }
}