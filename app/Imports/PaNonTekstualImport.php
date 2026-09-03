<?php

namespace App\Imports;

use App\Models\PaNonTekstualType;
use App\Models\PaNonTekstualValue;
use App\Models\KolomDinamis;
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
            $types = PaNonTekstualType::all();
            $typeMap = [];
            foreach ($types as $t) {
                $typeMap[strtolower(trim($t->name))] = $t->id;
            }

            $koloms = KolomDinamis::where('modul', 'pa_non_tekstual')->get();
            $kolomMap = [];
            foreach ($koloms as $k) {
                $kolomMap[strtolower(trim($k->nama_kolom))] = $k->nama_kolom;
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

                // kumpulkan data_tambahan
                $dataTambahan = [];
                for ($i = 2; $i < count($headers); $i++) {
                    $namaKolomExcel = trim($headers[$i]);
                    if (empty($namaKolomExcel)) continue;
                    $namaKolomLower = strtolower($namaKolomExcel);

                    if (isset($kolomMap[$namaKolomLower])) {
                        $namaAsli = $kolomMap[$namaKolomLower];
                        $dataTambahan[$namaAsli] = $row[$i] ?? '';
                    }
                }

                $hasData = false;
                for ($i = 2; $i < count($headers); $i++) {
                    $namaKolomExcel = trim($headers[$i]);
                    if (empty($namaKolomExcel)) continue;
                    $namaKolomLower = strtolower($namaKolomExcel);
                    $namaKolomLower = str_replace('tesktual', 'tekstual', $namaKolomLower);

                    if (isset($typeMap[$namaKolomLower])) {
                        if (isset($row[$i]) && $row[$i] !== null && $row[$i] !== '') {
                            $hasData = true;
                            break;
                        }
                    }
                }
                
                // If there's no data in master cols but there is data_tambahan, we still process it
                if (!$hasData && empty(array_filter($dataTambahan))) continue;

                $isFirst = true;
                for ($i = 2; $i < count($headers); $i++) {
                    $namaKolomExcel = trim($headers[$i]);
                    if (empty($namaKolomExcel)) continue;
                    $namaKolomLower = strtolower($namaKolomExcel);
                    $namaKolomLower = str_replace('tesktual', 'tekstual', $namaKolomLower);

                    if (isset($typeMap[$namaKolomLower])) {
                        $typeId = $typeMap[$namaKolomLower];
                        $jumlah = $row[$i] !== null ? (int)$row[$i] : 0;

                        $payload = ['jumlah' => $jumlah];
                        
                        // We attach the data_tambahan only on the first master column processed for this row 
                        // so that we don't multiply/overwrite it incorrectly, though it's the same row in UI
                        // so it's attached to the first item. Wait, in UI edit, it pulls data_tambahan from ANY item.
                        if ($isFirst) {
                            $payload['data_tambahan'] = $dataTambahan;
                            $isFirst = false;
                        }

                        PaNonTekstualValue::updateOrCreate(
                            [
                                'type_id' => $typeId,
                                'tahun'   => $tahun,
                                'bulan'   => $bulan,
                            ],
                            $payload
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
