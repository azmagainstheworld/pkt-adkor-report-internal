<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaNonTekstualData; // Import Model asli yang baru dibuat ulang
use Carbon\Carbon;

class PaNonTekstualSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = Carbon::now()->year;
        $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        // Cek jika data sudah ada, jangan double seed
        if(PaNonTekstualData::where('tahun', $tahun)->exists()) { return; }

        foreach ($bulan as $b) {
            // Isi data default 0
            PaNonTekstualData::create([
                'tahun' => $tahun,
                'bulan' => $b,
                'alih_media' => 0,
                'rekap_sidovit' => 0,
                'upload_edms' => 0,
            ]);
        }
    }
}