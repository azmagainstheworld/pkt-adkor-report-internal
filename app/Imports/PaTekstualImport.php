<?php

namespace App\Imports;

use App\Models\PaTekstualMaster;
use App\Models\PaTekstualData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaTekstualImport implements ToCollection, WithCalculatedFormulas
{
    protected $kelompokTabel;

    public function __construct($kelompokTabel)
    {
        $this->kelompokTabel = ($kelompokTabel === 'tabel1') ? 1 : 2;
    }

    public function collection(Collection $rows)
    {
        $headers = [];
        $isFirstRow = true;

        DB::beginTransaction();
        try {
            // Ambil master dokumen yang sudah diset di database untuk kelompok ini
            $masters = PaTekstualMaster::where('kelompok_tabel', $this->kelompokTabel)->get();
            $masterMap = []; // [nama_dokumen_lowercase => id]
            foreach ($masters as $m) {
                $masterMap[strtolower(trim($m->nama_dokumen))] = $m->id;
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

                    // Cek apakah kolom di Excel ini sudah tersetting di Master Database
                    if (isset($masterMap[$namaKolomLower])) {
                        $masterId = $masterMap[$namaKolomLower];
                        $jumlah = $row[$i] !== null ? (int)$row[$i] : 0;

                        // Gunakan updateOrCreate untuk menimpa data jika sudah ada (Sesuai persetujuan pengguna)
                        PaTekstualData::updateOrCreate(
                            [
                                'master_id' => $masterId,
                                'tahun'     => $tahun,
                                'bulan'     => $bulan,
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
            Log::error("PaTekstualImport failed: " . $e->getMessage());
            throw $e;
        }
    }
}
