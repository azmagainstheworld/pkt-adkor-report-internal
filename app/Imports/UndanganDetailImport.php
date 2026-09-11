<?php

namespace App\Imports;

use App\Models\Undangan;
use App\Models\UndanganDetail;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class UndanganDetailImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['tahun']) || !isset($row['bulan']) || !isset($row['jenis_undangan']) || !isset($row['agenda'])) {
                continue;
            }

            $bulanFormatted = ucfirst(strtolower(trim($row['bulan'])));
            $jenisUndangan = ucfirst(strtolower(trim($row['jenis_undangan'])));
            // Normalize internal naming if necessary
            if ($jenisUndangan == 'Internal' || $jenisUndangan == 'Intern') {
                $jenisUndangan = 'Intern';
            } elseif ($jenisUndangan == 'Eksternal' || $jenisUndangan == 'Ekstern') {
                $jenisUndangan = 'Eksternal';
            }

            // Ensure parent Undangan exists
            $undangan = Undangan::firstOrCreate([
                'tahun' => trim($row['tahun']),
                'bulan' => $bulanFormatted
            ], [
                'undangan_intern' => 0,
                'undangan_ekstern' => 0
            ]);

            // Create Detail record
            UndanganDetail::create([
                'undangan_id' => $undangan->id,
                'jenis_undangan' => $jenisUndangan,
                'agenda' => trim($row['agenda'])
            ]);
        }
    }
}
