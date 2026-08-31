<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\PerizinanTerbit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class PerizinanTerbitImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
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
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'perizinan_terbit')->get();
    }

    public function collection(Collection $rows)
    {
        // Pengecekan apakah template yang digunakan benar
        $firstRow = $rows->first();
        if ($firstRow !== null && !array_key_exists('nomor', $firstRow->toArray())) {
            throw new \Exception('Kolom "Nomor" tidak ditemukan! Anda sepertinya menggunakan Template Excel yang salah. Silakan klik tombol "Download Template" untuk mendapatkan format terbaru.');
        }

        foreach ($rows as $row) {
            $nomorRaw = trim($row['nomor'] ?? '');
            $nomor = $nomorRaw === '' ? '-' : $nomorRaw;

            $dataTambahan = [];
            foreach ($this->kolomDinamis as $kolom) {
                $keyExcel = strtolower(str_replace(' ', '_', $kolom->nama_kolom));
                if (array_key_exists($keyExcel, $row)) {
                    $dataTambahan[$kolom->nama_kolom] = $row[$keyExcel];
                }
            }

            // [AUTO-PATCH] Map Dropdown Dinamis case-insensitive
            $dataTambahan = $this->mapDynamicDropdowns($this->kolomDinamis, $dataTambahan);

            // Kolom dari template baru:
            // Tahun, Perizinan Terbit, Nomor, Terbit, Berakhir, Instansi Penerbit, Bulan, Kegiatan
            
            // Format tanggal dari Excel ke MySQL format
            $terbitInput = trim($row['terbit'] ?? '');
            
            // Helper untuk replace nama bulan Indonesia ke Inggris
            $bulanIndo = [
                'januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember',
                'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'
            ];
            $bulanEng = [
                'january','february','march','april','may','june','july','august','september','october','november','december',
                'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'
            ];
            
            if ($terbitInput === '' || $terbitInput === '-') {
                $tglSejak = now()->format('Y-m-d');
            } else {
                if (is_numeric($terbitInput)) {
                    $tglSejak = Date::excelToDateTimeObject($terbitInput)->format('Y-m-d');
                } else {
                    $terbitInput = str_ireplace($bulanIndo, $bulanEng, $terbitInput);
                    $tglSejak = Carbon::parse($terbitInput)->format('Y-m-d');
                }
            }
            
            $berakhirInput = trim($row['berakhir'] ?? '');
            if ($berakhirInput === '' || $berakhirInput === '-') {
                $tglAkhir = now()->format('Y-m-d');
            } else {
                if (is_numeric($berakhirInput)) {
                    $tglAkhir = Date::excelToDateTimeObject($berakhirInput)->format('Y-m-d');
                } else {
                    $berakhirInput = str_ireplace($bulanIndo, $bulanEng, $berakhirInput);
                    $tglAkhir = Carbon::parse($berakhirInput)->format('Y-m-d');
                }
            }

            // Handle relasi ke jenis_perizinan_master
            $namaPerizinanInput = trim($row['perizinan_terbit'] ?? '-');
            
            // Map Kegiatan case-insensitive
            $kegiatanInput = trim($row['kegiatan'] ?? 'Adm & Lainnya');
            $kegiatanMap = [
                'aset' => 'Aset',
                'adm & lainnya' => 'Adm & Lainnya',
                'peralatan pabrik' => 'Peralatan Pabrik',
                'produk' => 'Produk',
                'proyek' => 'Proyek'
            ];
            $kegiatanLower = strtolower($kegiatanInput);
            if (array_key_exists($kegiatanLower, $kegiatanMap)) {
                $kegiatanInput = $kegiatanMap[$kegiatanLower];
            }

            // Case-insensitive cari nama_jenis
            $jenis = DB::table('jenis_perizinan_master')
                ->whereRaw('LOWER(nama_jenis) = ?', [strtolower($namaPerizinanInput)])
                ->first();

            if (!$jenis) {
                $jenisId = DB::table('jenis_perizinan_master')->insertGetId([
                    'nama_jenis' => $namaPerizinanInput,
                    'kategori_grup' => $kegiatanInput,
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $jenisId = $jenis->id;
            }

            PerizinanTerbit::updateOrCreate(
                [
                    'nomor'              => $nomor,
                    'jenis_perizinan_id' => $jenisId,
                ],
                [
                    'kegiatan'           => $kegiatanInput,
                    'tanggal_sejak'      => $tglSejak,
                    'tanggal_akhir'      => $tglAkhir,
                    'instansi_penerbit'  => $row['instansi_penerbit'] ?? '-',
                    'data_tambahan'      => $dataTambahan,
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
