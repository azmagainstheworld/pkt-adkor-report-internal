<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PaTeknikMaster;
use App\Models\PaTeknikData;

class PaTeknikImport implements ToCollection, WithCalculatedFormulas
{
    protected $kelompok;

    public function __construct($kelompok)
    {
        $this->kelompok = $kelompok == 'tabel1' ? 1 : 2;
    }

    public function collection(Collection $rows)
    {
        $headers = [];
        $isFirstRow = true;

        DB::beginTransaction();
        try {
            $types = PaTeknikMaster::where('kelompok_tabel', $this->kelompok)->get();
            $typeMap = [];
            foreach ($types as $t) {
                $typeMap[strtolower(trim($t->nama_kegiatan))] = $t->id;
            }

            foreach ($rows as $index => $row) {
                if ($isFirstRow) {
                    $headers = $row->toArray();
                    $isFirstRow = false;
                    continue;
                }

                $tahun = trim($row[0] ?? '');
                $bulan = trim($row[1] ?? '');

                if (empty($tahun) || empty($bulan)) continue;

                // Skip zeros
                $hasData = false;
                for ($i = 2; $i < count($headers); $i++) {
                    if (isset($row[$i]) && $row[$i] !== null && $row[$i] !== '' && (int)$row[$i] > 0) {
                        $hasData = true;
                        break;
                    }
                }
                if (!$hasData) continue;

                for ($i = 2; $i < count($headers); $i++) {
                    $namaKolomExcel = trim($headers[$i] ?? '');
                    if (empty($namaKolomExcel)) continue;

                    $namaKolomLower = strtolower($namaKolomExcel);

                    if (isset($typeMap[$namaKolomLower])) {
                        $masterId = $typeMap[$namaKolomLower];
                        $jumlah = $row[$i] !== null ? (int)$row[$i] : 0;

                        PaTeknikData::updateOrCreate(
                            [
                                'master_id' => $masterId,
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
            Log::error("PaTeknikImport failed: " . $e->getMessage());
            throw $e;
        }
    }
}
