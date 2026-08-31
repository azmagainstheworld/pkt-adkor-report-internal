<?php

namespace App\Imports;

use App\Models\KeluargaKaryawan;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;
use App\Imports\Traits\CaseInsensitiveMapper;

class KeluargaKaryawanImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
{
    use RegistersEventListeners;
    use CaseInsensitiveMapper;

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
    protected $lastNpk = null;
    public $skippedNpk = [];

    public function __construct()
    {
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'keluarga_karyawan')->get();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $npk = trim($row['npk_karyawan'] ?? '');
            
            if (!empty($npk)) {
                $this->lastNpk = $npk;
            } else {
                $npk = $this->lastNpk;
            }

            if (empty($npk) || empty($row['nama_lengkap'])) {
                continue;
            }

            $karyawan = Karyawan::where('npk', $npk)->first();
            if (!$karyawan) {
                if (!in_array($npk, $this->skippedNpk)) {
                    $this->skippedNpk[] = $npk;
                }
                continue; // Skip if NPK doesn't exist
            }

            $dataTambahan = [];
            foreach ($this->kolomDinamis as $kolom) {
                $keyExcel = strtolower(str_replace(' ', '_', $kolom->nama_kolom));
                if (array_key_exists($keyExcel, $row)) {
                    $dataTambahan[$kolom->nama_kolom] = $row[$keyExcel];
                }
            }

            // Map Dropdown Dinamis case-insensitive
            $dataTambahan = $this->mapDynamicDropdowns($this->kolomDinamis, $dataTambahan);

            // Parsing Tanggal Lahir
            $tanggal_lahir = null;
            if (isset($row['tanggal_lahir'])) {
                if (is_numeric($row['tanggal_lahir'])) {
                    $tanggal_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
                } else {
                    $tgl = $row['tanggal_lahir'];
                    $bulanIndo = ['Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March', 'April' => 'April', 'Mei' => 'May', 'Juni' => 'June', 'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September', 'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December', 'Okt' => 'Oct', 'Nov' => 'Nov', 'Des' => 'Dec', 'Agt' => 'Aug'];
                    $tgl = str_ireplace(array_keys($bulanIndo), array_values($bulanIndo), $tgl);
                    $parsed = strtotime($tgl);
                    $tanggal_lahir = $parsed ? date('Y-m-d', $parsed) : null;
                }
            }

            KeluargaKaryawan::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'nama'        => $row['nama_lengkap']
                ],
                [
                    'hubungan'      => $this->mapStaticDropdown($row['hubungan'] ?? '-', ['Suami', 'Istri', 'Anak']),
                    'tempat_lahir'  => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $tanggal_lahir,
                    'data_tambahan' => $dataTambahan
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
