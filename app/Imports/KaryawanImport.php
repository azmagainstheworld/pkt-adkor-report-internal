<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class KaryawanImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
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
        // Ambil data kolom dinamis tabel karyawan
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'karyawan_tabel')->get();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Jika kolom NPK kosong, lewati baris ini
            if (!isset($row['npk']) || $row['npk'] == '') {
                continue;

    }

            // Tangkap data tambahan berdasarkan kolom dinamis
            $dataTambahan = [];
            foreach ($this->kolomDinamis as $kolom) {
                // Excel biasanya mengubah header menjadi lowercase dan underscore (cth: "Tunjangan Khusus" -> "tunjangan_khusus")
                $keyExcel = strtolower(str_replace(' ', '_', $kolom->nama_kolom));
                
                if (array_key_exists($keyExcel, $row)) {
                    $dataTambahan[$kolom->nama_kolom] = $row[$keyExcel];
                }
            }

            // [AUTO-PATCH] Map Dropdown Dinamis case-insensitive
            $dataTambahan = $this->mapDynamicDropdowns($this->kolomDinamis, $dataTambahan);

            // Konversi format tanggal excel ke format Y-m-d MySQL
            $mpp_pbp = null;
            if (isset($row['mpppbp'])) {
                $mpp_pbp = is_numeric($row['mpppbp']) 
                    ? Date::excelToDateTimeObject($row['mpppbp'])->format('Y-m-d') 
                    : date('Y-m-d', strtotime($row['mpppbp']));
            } else {
                $mpp_pbp = now()->format('Y-m-d'); // Fallback
            }

            // Parsing TTL
            $tempat_lahir = null;
            $tanggal_lahir = null;
            if (!empty($row['ttl'])) {
                $ttl_parts = explode(',', $row['ttl']);
                if (count($ttl_parts) >= 2) {
                    $tempat_lahir = trim($ttl_parts[0]);
                    $tgl_raw = trim($ttl_parts[1]);
                    $tanggal_lahir = is_numeric($tgl_raw) 
                        ? Date::excelToDateTimeObject($tgl_raw)->format('Y-m-d') 
                        : date('Y-m-d', strtotime($tgl_raw));
                } else {
                    // Coba cek apakah isinya cuma angka/tanggal
                    if (is_numeric(trim($row['ttl']))) {
                        $tanggal_lahir = Date::excelToDateTimeObject(trim($row['ttl']))->format('Y-m-d');
                    } else if (strtotime(trim($row['ttl'])) !== false && !is_numeric(trim($row['ttl']))) {
                        $tanggal_lahir = date('Y-m-d', strtotime(trim($row['ttl'])));
                    } else {
                        $tempat_lahir = trim($row['ttl']);
                    }
                }
            }

            $rawGol = trim($row['golgrade'] ?? 'I-A');
            // Format IIA menjadi II-A
            if (preg_match('/^(I{1,3}|IV)([A-D])$/i', $rawGol, $matches)) {
                $rawGol = strtoupper($matches[1]) . '-' . strtoupper($matches[2]);
            } else if (preg_match('/^(I{1,3}|IV)-([A-D])$/i', $rawGol, $matches)) {
                $rawGol = strtoupper($matches[1]) . '-' . strtoupper($matches[2]);
            }
            $validGols = ['I-A','I-B','I-C','I-D','II-A','II-B','II-C','II-D','III-A','III-B','III-C','III-D','IV-A','IV-B','IV-C','IV-D'];
            $gol_grade = in_array($rawGol, $validGols) ? $rawGol : 'I-A';

            // UpdateOrCreate: Jika NPK sudah ada, update. Jika belum, tambah baru.
            Karyawan::updateOrCreate(
                ['npk' => (string) $row['npk']],
                [
                    'nama'          => $row['nama'] ?? '-',
                    'gol_grade'     => $gol_grade,
                    'tempat_lahir'  => $tempat_lahir,
                    'tanggal_lahir' => $tanggal_lahir,
                    'mpp_pbp'       => $mpp_pbp,
                    'ket_pensiun'   => $this->mapStaticDropdown($row['ket_pensiun'] ?? '< 5 Tahun', ['> 10 Tahun', '< 10 Tahun', '< 5 Tahun']),
                    'keterangan'    => $this->mapStaticDropdown($row['keterangan'] ?? 'Organik', ['Organik', 'Non Organik']),
                    'ukuran_kaos'   => $this->mapStaticDropdown($row['ukuran_kaos'] ?? 'L', ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL']),
                    'alamat'        => $row['alamat'] ?? '-',
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
