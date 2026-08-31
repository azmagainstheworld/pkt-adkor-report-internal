<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\Karyawan;
use App\Models\Ketidakhadiran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class KetidakhadiranImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
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
        if ($rows->isEmpty()) return;

        // Validasi header NPK ada
        $firstRow = $rows->first();
        if (!isset($firstRow['npk'])) {
            throw new \Exception("Kolom 'NPK' tidak ditemukan! Pastikan Anda menggunakan template Excel yang benar untuk Ketidakhadiran.");
        }

        foreach ($rows as $row) {
            // Lewati jika NPK kosong pada baris ini
            if (!isset($row['npk']) || trim($row['npk']) == '') {
                continue;
            }

            // Cari Karyawan berdasarkan NPK
            $karyawan = Karyawan::where('npk', (string) $row['npk'])->first();
            if (!$karyawan) {
                continue; 
            }

            $tahun = $row['tahun'] ?? now()->year;
            $bulan = $row['bulan'] ?? Ketidakhadiran::bulanList()[now()->month - 1];

            $keterangan = $row['keterangan'] ?? null;
            
            $dinas = $row['dinas'] ?? '0';
            $cuti = $row['cuti'] ?? '0';
            $izin = $row['izin'] ?? '0';
            $training = $row['training'] ?? '0';
            $dispensasi = $row['dispensasi'] ?? '0';
            $detasering = $row['detasering'] ?? '0';

            // Update atau Create rekap bulanan
            Ketidakhadiran::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'tahun'       => $tahun,
                    'bulan'       => $bulan,
                ],
                [
                    'keterangan' => $keterangan,
                    'dinas'      => $dinas ?: '0',
                    'cuti'       => $cuti ?: '0',
                    'izin'       => $izin ?: '0',
                    'training'   => $training ?: '0',
                    'dispensasi' => $dispensasi ?: '0',
                    'detasering' => $detasering ?: '0',
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
