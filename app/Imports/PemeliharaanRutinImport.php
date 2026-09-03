<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\PemeliharaanRutinData;
use App\Models\PemeliharaanRutinMaster;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PemeliharaanRutinImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, WithBatchInserts, SkipsEmptyRows
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
        Log::info("PemeliharaanRutinImport started parsing collection with " . count($rows) . " rows.");
        $masters = PemeliharaanRutinMaster::all();
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pemeliharaan_rutin')->get();
        $uuid = request('import_uuid');
        
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                Log::info("PemeliharaanRutinImport first row keys: " . json_encode(array_keys($row->toArray())));
            }
            if (!isset($row['tahun']) || !isset($row['bulan'])) {
                Log::info("Row skipped due to missing tahun or bulan", $row->toArray());
                continue;
            }

            $tahun = $row['tahun'];
            $bulan = ucfirst(trim($row['bulan']));
            
            if ($uuid) {
                Cache::increment('import_current_' . $uuid, 1);
            }

            // Extract dynamic columns
            $dataTambahan = [];
            foreach($kolomDinamis as $kolom) {
                // Laravel Excel default header row formatting is snake_case (slug with underscore)
                $kolKey = Str::slug($kolom->nama_kolom, '_');
                if (isset($row[$kolKey])) {
                    $dataTambahan[$kolom->nama_kolom] = $row[$kolKey];
                }
            }
            $dataTambahan = $this->mapDynamicDropdowns($kolomDinamis, $dataTambahan);

            foreach($masters as $master) {
                $key = Str::slug($master->nama_pemeliharaan, '_');
                
                if (isset($row[$key])) {
                    PemeliharaanRutinData::updateOrCreate(
                        ['tahun' => $tahun, 'bulan' => $bulan, 'rutin_id' => $master->id],
                        ['jumlah' => (int) $row[$key], 'data_tambahan' => empty($dataTambahan) ? null : $dataTambahan]
                    );
                } else {
                    if ($index === 0) Log::info("Missing key for master: " . $key);
                }
            }
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
