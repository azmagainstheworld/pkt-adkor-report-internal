<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\PemeliharaanPeralatanData;
use App\Models\PemeliharaanPeralatanMaster;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


class PemeliharaanPeralatanImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, WithBatchInserts, SkipsEmptyRows
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
        $masters = PemeliharaanPeralatanMaster::all();
        
        foreach ($rows as $row) {
            if (!isset($row['tahun']) || !isset($row['bulan'])) continue;

            $tahun = $row['tahun'];
            $bulan = ucfirst(trim($row['bulan']));

            foreach($masters as $master) {
                $key = strtolower(str_replace([' ', '/', '-'], '_', $master->nama_peralatan));
                
                if (isset($row[$key])) {
                    PemeliharaanPeralatanData::updateOrCreate(
                        ['tahun' => $tahun, 'bulan' => $bulan, 'peralatan_id' => $master->id],
                        ['jumlah' => (int) $row[$key]]
                    );

    }
            }
        }
    }

    public function batchSize(): int
    {
        return 500;
            $uuid = request('import_uuid');
        if ($uuid) {
            Cache::increment('import_current_' . $uuid, count($rows));
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
