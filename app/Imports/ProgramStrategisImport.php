<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\ProgramStrategis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class ProgramStrategisImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
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
    protected $bulanMap = [
        'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
        'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
        'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12'
    ];

    public function __construct()
    {
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'program_strategis')->get();
    }

    // State untuk menyimpan baris sebelumnya agar bisa group
    protected $lastTahun = null;
    protected $lastBulan = null;
    protected $lastSasaran = null;
    protected $lastProgramStrategis = null;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Inherit Tahun jika kosong/-
            $tahun = trim($row['tahun'] ?? '');
            if ($tahun === '' || $tahun === '-') {
                $row['tahun'] = $this->lastTahun;

    } else {
                $this->lastTahun = $tahun;
            }

            // Inherit Bulan jika kosong/-
            $bulan = trim($row['bulan'] ?? '');
            if ($bulan === '' || $bulan === '-') {
                $row['bulan'] = $this->lastBulan;
            } else {
                $this->lastBulan = $bulan;
            }

            // Inherit Sasaran jika kosong/-
            $sasaran = trim($row['sasaran'] ?? '');
            if ($sasaran === '' || $sasaran === '-') {
                $row['sasaran'] = $this->lastSasaran;
            } else {
                $this->lastSasaran = $sasaran;
            }

            // Inherit Program Strategis jika kosong/-
            $programStrategis = trim($row['program_strategis'] ?? '');
            if ($programStrategis === '' || $programStrategis === '-') {
                $row['program_strategis'] = $this->lastProgramStrategis;
            } else {
                $this->lastProgramStrategis = $programStrategis;
            }

            // Tangkap nama kolom baru "Program Kegiatan" dari Excel
            $kegiatan = trim($row['program_kegiatan'] ?? $row['deskripsi_kegiatan'] ?? '');
            
            if (empty($row['program_strategis']) || empty($kegiatan)) {
                continue;
            }

            $dataTambahan = [];
            foreach ($this->kolomDinamis as $kolom) {
                $keyExcel = strtolower(str_replace(' ', '_', $kolom->nama_kolom));
                if (array_key_exists($keyExcel, $row)) {
                    $dataTambahan[$kolom->nama_kolom] = $row[$keyExcel];
                }
            }

            // [AUTO-PATCH] Map Dropdown Dinamis case-insensitive
            $dataTambahan = $this->mapDynamicDropdowns($this->kolomDinamis, $dataTambahan);

            $bulanInput = strtolower(trim($row['bulan'] ?? date('m')));
            $bulanAngka = $this->bulanMap[$bulanInput] ?? str_pad($bulanInput, 2, '0', STR_PAD_LEFT);

            ProgramStrategis::updateOrCreate(
                [
                    'tahun'              => $row['tahun'] ?? date('Y'),
                    'bulan'              => $bulanAngka,
                    'sasaran'            => $row['sasaran'] ?? null,
                    'program_strategis'  => $row['program_strategis'],
                    'deskripsi_kegiatan' => $kegiatan,
                ],
                [
                    'target_waktu_start'  => $row['target_waktu_mulai'] ?? null,
                    'target_waktu_end'    => $row['target_waktu_selesai'] ?? null,
                    'realisasi'           => $row['realisasi'] ?? null,
                    'progress_saat_ini'   => $row['progress_saat_ini'] ?? null,
                    'kendala'             => $row['kendala'] ?? null,
                    'keterangan_tambahan' => $row['keterangan_tambahan'] ?? null,
                    'status'              => $row['status'] ?? 'In Progress',
                    'data_tambahan'       => $dataTambahan,
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
