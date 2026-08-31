<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\MasalahKendala;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class MasalahKendalaImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
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


    protected $kolomDinamis;

    public function __construct()
    {
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'masalah_kendala')->get();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Lewati jika Masalah kosong
            if (!isset($row['masalah_kendala']) || trim($row['masalah_kendala']) === '') {
                continue;

    }

            // Tangkap data tambahan berdasarkan kolom dinamis
            $dataTambahan = [];
            foreach ($this->kolomDinamis as $kolom) {
                $keyExcel = strtolower(str_replace(' ', '_', $kolom->nama_kolom));
                if (array_key_exists($keyExcel, $row)) {
                    $dataTambahan[$kolom->nama_kolom] = $row[$keyExcel];
                }
            }

            // [AUTO-PATCH] Map Dropdown Dinamis case-insensitive
            $dataTambahan = $this->mapDynamicDropdowns($this->kolomDinamis, $dataTambahan);

            MasalahKendala::updateOrCreate(
                [
                    'tahun'   => $row['tahun'] ?? now()->year,
                    'bulan'   => $row['bulan'] ?? 'Januari',
                    'masalah' => $row['masalah_kendala'],
                ],
                [
                    'solusi'        => $row['solusi'] ?? '-',
                    'data_tambahan' => $dataTambahan,
                ]
            );
        }
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
