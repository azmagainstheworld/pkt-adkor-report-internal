<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaTeknikMaster;

class PaTeknikSeeder extends Seeder
{
    public function run(): void
    {
        // Tabel 1
        $tabel1 = [
            'Alih Media (berkas/lembar)', 'Jasa Cetak Gambar (berkas)', 
            'Peminjaman Dok. (berkas/lembar)', 'Peminjaman Dok. (bantex)', 
            'Peminjaman Dok. (CD)', 'Peminjaman Dok. (lembar)', 
            'Permintaan Copy/Soft (berkas/lembar)', 'Konversi TIF ke PDF (File)'
        ];

        // Tabel 2
        $tabel2 = [
            'Penyerahan Dok. (berkas/lembar)', 'Penyerahan Dok. (bantex)', 
            'Penyerahan Dok. (CD)', 'Upload Dokumen TF ke Smartshare', 
            'Alih Media Dokumen Proyek (Sodaash, Revamping, Turn Arround, Crash Program, Procurement)', 
            'Rekapitulasi Dokumen Proyek (Sodaash, Revamping, Turn Arround, Crash Program, Procurement)'
        ];

        foreach ($tabel1 as $nama) {
            PaTeknikMaster::firstOrCreate(['kelompok_tabel' => 1, 'nama_kegiatan' => $nama]);
        }
        foreach ($tabel2 as $nama) {
            PaTeknikMaster::firstOrCreate(['kelompok_tabel' => 2, 'nama_kegiatan' => $nama]);
        }
    }
}