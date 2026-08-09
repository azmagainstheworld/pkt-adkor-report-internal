<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaNonTekstualType;

class PaNonTekstualMasterSeeder extends Seeder
{
    public function run(): void
    {
        // Nama kolom disamakan persis dengan Looker (termasuk typo 'Tesktual' bawaan)
        $kolomAwal = [
            'Alih Media (Non Tesktual)',
            'Rekap Sidovit (Non Tekstual)',
            'Upload EDMS (Non Tesktual)'
        ];

        foreach ($kolomAwal as $kolom) {
            PaNonTekstualType::firstOrCreate(
                ['name' => $kolom],
                ['is_active' => true]
            );
        }
    }
}