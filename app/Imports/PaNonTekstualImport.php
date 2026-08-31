<?php

namespace App\Imports;

use App\Models\PaNonTekstualType;
use App\Models\PaNonTekstualValue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaNonTekstualImport implements ToCollection, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        $headers = [];
        $isFirstRow = true;

        DB::beginTransaction();
        try {
            // Ambil master tipe dokumen yang sudah diset di database
            $types = PaNonTekstualType::all();
            $typeMap = []; // [nama_dokumen_lowercase => id]
            foreach ($types as $t) {
                $typeMap[strtolower(trim($t->name))] = $t->id;
            }

            foreach ($rows as $index => $row) {
                // Skip empty rows
                $isEmpty = true;
                foreach ($row as $cell) {
                    if (!empty($cell)) {
                        $isEmpty = false;
                        break;
                    }
                }
                if ($isEmpty) continue;

                if ($isFirstRow) {
                    $headers = $row->toArray();
                    $isFirstRow = false;
                    continue;
                }

                $tahun = trim($row[0]);
                $bulan = trim($row[1]);

                if (empty($tahun) || empty($bulan)) continue;

                // Check if all data columns are empty or zero
                $hasData = false;
                for ($i = 2; $i < count($headers); $i++) {
                    if (isset($row[$i]) && $row[$i] !== null && $row[$i] !== '' && (int)$row[$i] > 0) {
                        $hasData = true;
                        break;
                    }
                }
                if (!$hasData) continue;


                // Loop tiap kolom yang ada di excel mulai dari index 2
                for ($i = 2; $i < count($headers); $i++) {
                    $namaKolomExcel = trim($headers[$i]);
                    if (empty($namaKolomExcel)) continue;

                    $namaKolomLower = strtolower($namaKolomExcel);
                    $namaKolomLower = str_replace('tesktual', 'tekstual', $namaKolomLower);

                    // Cek apakah kolom di Excel ini sudah tersetting di Master Database
                    if (isset($typeMap[$namaKolomLower])) {
                        $typeId = $typeMap[$namaKolomLower];
                        $jumlah = $row[$i] !== null ? (int)$row[$i] : 0;

                        // Gunakan updateOrCreate untuk menimpa data jika sudah ada
                        PaNonTekstualValue::updateOrCreate(
                            [
                                'type_id' => $typeId,
                                'tahun'   => $tahun,
                                'bulan'   => $bulan,
                            ],
                            [
                                'jumlah' => $jumlah
                            ]
                        );
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("PaNonTekstualImport failed: " . $e->getMessage());
            throw $e;
        }
    }
}
