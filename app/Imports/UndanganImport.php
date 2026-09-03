<?php

namespace App\Imports;

use App\Models\Undangan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class UndanganImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['tahun']) || !isset($row['bulan'])) {
                continue;
            }

            $bulanFormatted = ucfirst(strtolower(trim($row['bulan'])));

            // Cari Undangan Master, update jika sudah ada
            $undangan = Undangan::firstOrCreate([
                'tahun' => trim($row['tahun']),
                'bulan' => $bulanFormatted
            ], [
                'undangan_intern' => 0,
                'undangan_ekstern' => 0
            ]);

            $intern = isset($row['undangan_intern']) ? (int)$row['undangan_intern'] : $undangan->undangan_intern;
            $ekstern = isset($row['undangan_ekstern']) ? (int)$row['undangan_ekstern'] : $undangan->undangan_ekstern;

            $undangan->update([
                'undangan_intern' => $intern,
                'undangan_ekstern' => $ekstern
            ]);
        }
    }
}
