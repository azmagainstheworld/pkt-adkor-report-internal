<?php

namespace App\Imports;

use App\Models\JasaKurirMaster;
use App\Models\JasaKurirData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JasaKurirImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $headers = [];
        $isFirstRow = true;

        DB::beginTransaction();
        try {
            // Kita hapus semua data lama agar import baru menimpa sepenuhnya
            JasaKurirData::truncate();

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
                    
                    // Pastikan Master Kurir terdaftar berdasarkan Header Excel
                    for ($i = 2; $i < count($headers); $i++) {
                        $namaKurir = trim($headers[$i]);
                        if (!empty($namaKurir)) {
                            JasaKurirMaster::firstOrCreate(
                                ['nama_kurir' => strtolower($namaKurir)],
                                ['nama_kurir' => strtolower($namaKurir)]
                            );
                        }
                    }
                    continue;
                }

                $tahun = $row[0];
                $bulan = $row[1];

                // Validate Tahun and Bulan
                if (empty($tahun) || empty($bulan)) continue;

                // Loop tiap kolom Kurir
                for ($i = 2; $i < count($headers); $i++) {
                    $namaKurir = trim($headers[$i]);
                    if (empty($namaKurir)) continue;

                    $jumlah = $row[$i] !== null ? (int)$row[$i] : 0;

                    $kurirMaster = JasaKurirMaster::where('nama_kurir', strtolower($namaKurir))->first();
                    if ($kurirMaster) {
                        JasaKurirData::create([
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'jasa_kurir_id' => $kurirMaster->id,
                            'jumlah' => $jumlah,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("JasaKurirImport failed: " . $e->getMessage());
            throw $e;
        }
    }
}
