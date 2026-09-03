<?php

namespace App\Imports;

use App\Models\PaTekstualMaster;
use App\Models\PaTekstualData;
use App\Models\PaTekstualKolom;
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
            $masters = PaTekstualMaster::where('kelompok_tabel', $this->kelompokTabel)->get();
            $masterMap = [];
            foreach ($masters as $m) {
                $masterMap[strtolower(trim($m->nama_dokumen))] = $m->id;
            }

            $koloms = PaTekstualKolom::where('kelompok_tabel', $this->kelompokTabel)->get();
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

                    if (isset($masterMap[$namaKolomLower])) {
                        if (isset($row[$i]) && $row[$i] !== null && $row[$i] !== '' && (int)$row[$i] > 0) {
                            $hasData = true;
                            break;
                        }
                    }
                }
                if (!$hasData && empty(array_filter($dataTambahan))) continue;

                $isFirst = true;
                for ($i = 2; $i < count($headers); $i++) {
                    $namaKolomExcel = trim($headers[$i]);
                    if (empty($namaKolomExcel)) continue;
                    $namaKolomLower = strtolower($namaKolomExcel);

                    if (isset($masterMap[$namaKolomLower])) {
                        $masterId = $masterMap[$namaKolomLower];
                        $jumlah = $row[$i] !== null ? (int)$row[$i] : 0;

                        $payload = ['jumlah' => $jumlah];
                        if ($isFirst) {
                            $payload['data_tambahan'] = $dataTambahan;
                            $isFirst = false;
                        }

                        PaTekstualData::updateOrCreate(
                            [
                                'master_id' => $masterId,
                                'tahun'     => $tahun,
                                'bulan'     => $bulan,
                            ],
                            $payload
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
