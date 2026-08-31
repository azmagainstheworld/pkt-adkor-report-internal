<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\Pelaporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class PelaporanImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
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
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pelaporan')->get();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nomorLaporan = $row['nomor_laporan'] ?? $row['nomor'] ?? '';
            // Lewati jika Nomor Laporan kosong
            if (trim($nomorLaporan) === '') {
                continue;
            }

            // Tangkap data tambahan berdasarkan kolom dinamis
            $dataTambahan = [];
            foreach ($this->kolomDinamis as $kolom) {
                $keyExcel = strtolower(str_replace(' ', '_', $kolom->nama_kolom));
                if (isset($row[$keyExcel])) {
                    $dataTambahan[$kolom->nama_kolom] = $row[$keyExcel];
                }
            }

            // [AUTO-PATCH] Map Dropdown Dinamis case-insensitive
            $dataTambahan = $this->mapDynamicDropdowns($this->kolomDinamis, $dataTambahan);

            // Format tanggal dari Excel ke MySQL format
            $bulanIndo = [
                'januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember',
                'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'
            ];
            $bulanEng = [
                'january','february','march','april','may','june','july','august','september','october','november','december',
                'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'
            ];
            
            $tglInput = $row['tanggal'] ?? '';
            if (empty(trim($tglInput)) || $tglInput === '-') {
                $tgl = now()->format('Y-m-d');
            } else {
                if (is_numeric($tglInput)) {
                    $tgl = Date::excelToDateTimeObject($tglInput)->format('Y-m-d');
                } else {
                    $tglInputStr = str_ireplace($bulanIndo, $bulanEng, $tglInput);
                    try {
                        $tgl = Carbon::parse($tglInputStr)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Jika format masih salah, gunakan format hari ini atau biarkan kosong
                        $tgl = now()->format('Y-m-d');
                    }
                }
            }

            // Gunakan Nomor sebagai acuan unik
            Pelaporan::updateOrCreate(
                [
                    'nomor' => $nomorLaporan,
                ],
                [
                    'tujuan'        => $row['tujuan_laporan'] ?? 'Internal',
                    'laporan'       => $row['laporan'] ?? '-',
                    'tanggal'       => $tgl,
                    'jenis'         => $row['jenis'] ?? 'Bulan',
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
