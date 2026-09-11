<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\JasaFotocopy;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class JasaFotocopyImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, WithBatchInserts, SkipsEmptyRows
{
    use CaseInsensitiveMapper;
    use RegistersEventListeners;

    public static function beforeImport(BeforeImport $event)
    {
        $uuid = request('import_uuid');
        if ($uuid) {
            $totalRowsArray = $event->getReader()->getTotalRows();
            $totalRows = 0;
            foreach($totalRowsArray as $sheet => $rows) {
                $totalRows += ($rows > 0 ? $rows - 1 : 0); // subtract header
            }
            Cache::put('import_total_' . $uuid, $totalRows, 300);
            Cache::put('import_current_' . $uuid, 0, 300);
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (isset($row['tahun']) && isset($row['bulan']) && isset($row['unit_kerja'])) {
                JasaFotocopy::updateOrCreate(
                    [
                        'tahun' => $row['tahun'],
                        'bulan' => trim($row['bulan']),
                        'unit_kerja' => trim($row['unit_kerja'])
                    ],
                    [
                        'cost_centre' => $row['cost_centre'] ?? null,
                        'keterangan' => $row['keterangan'] ?? 'KOPKAR',
                        'tipe_mesin' => $row['tipe_mesin'] ?? null,
                        'pemakaian_lembar' => (int) ($row['pemakaian_lbr'] ?? 0),
                        'biaya_fee_per_lembar' => (float) ($row['fee_lbr'] ?? 47.22),
                        'biaya_sewa_mesin' => (float) ($row['sewa_bln'] ?? 909000)
                    ]
                );
            }
        }
        
        $uuid = request('import_uuid');
        if ($uuid) {
            Cache::increment('import_current_' . $uuid, count($rows));
        }
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
