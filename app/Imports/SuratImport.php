<?php
// file: app/Imports/SuratImport.php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\Surat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SuratImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
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
            if (!isset($row['nomor_surat']) || trim($row['nomor_surat']) === '') {
                continue;

    }

            $tglSurat = null;
            if (isset($row['tanggal_surat'])) {
                $tglSurat = is_numeric($row['tanggal_surat']) 
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_surat'])->format('Y-m-d')
                    : date('Y-m-d', strtotime($row['tanggal_surat']));
            }

            Surat::updateOrCreate(
                [
                    'tahun' => $row['tahun'] ?? now()->year,
                    'bulan' => $row['bulan'] ?? 'Januari',
                    'nomor_surat' => $row['nomor_surat'],
                    'jenis_surat' => $row['jenis_surat'] ?? '-',
                ],
                [
                    'tanggal_surat' => $tglSurat,
                    'drafter'       => $row['drafter'] ?? '-',
                    'judul_surat'   => $row['judul_surat'] ?? '-',
                    'status'        => $row['status'] ?? '-',
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
