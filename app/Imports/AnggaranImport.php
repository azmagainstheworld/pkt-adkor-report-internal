<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithEvents;
use App\Imports\Traits\CaseInsensitiveMapper;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Cache;

use App\Models\AnggaranAdministrasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class AnggaranImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents, SkipsEmptyRows
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
    private $lastTahun;
    private $lastBulan;

    public function __construct()
    {
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'anggaran')->get();
        $this->lastTahun = now()->year;
        $this->lastBulan = 'Januari';
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("File Excel kosong! Tidak ada data baris yang ditemukan untuk di-import.");
        }

        // Cek apakah header benar (case-insensitive berkat trait, jadi key-nya lowercase snake_case)
        $firstRow = $rows->first();
        if (!array_key_exists('detail_anggaran', $firstRow->toArray())) {
            throw new \Exception("Format Excel salah! Tidak ditemukan kolom 'Detail Anggaran'. Pastikan Anda menggunakan template yang benar.");
        }

        foreach ($rows as $row) {
            // Forward-fill tahun dan bulan (untuk mengatasi merge cell di Excel)
            if (!empty($row['tahun'])) {
                $this->lastTahun = $row['tahun'];
            }
            if (!empty($row['bulan'])) {
                $this->lastBulan = ucfirst(strtolower($row['bulan']));
            }
            // Lewati kalau Detail Anggaran kosong
            if (!isset($row['detail_anggaran']) || trim($row['detail_anggaran']) === '') {
                continue;
            }

            $kategoriMapping = [
                'Pemeliharaan - Peralatan Kantor' => 'Dikelola',
                'Cetak dan Fotocopy' => 'Dikelola',
                'Pos Materai dan Pengiriman Dok.' => 'Dikelola',
                'Iuran Keanggotaan' => 'Dikelola',
                'Inspeksi dan Perijinan' => 'Dikelola',
                'Sewa - Peralatan Pabrik & Kantor' => 'Dikelola',
                'Jasa - Konsultan' => 'Dikelola',
                
                'Rekreasi dan Olahraga' => 'Rutin',
                'Peralatan Kantor' => 'Rutin',
                'Biaya Makan Minum' => 'Rutin',
                'Perjalanan Dinas Dalam Negeri' => 'Rutin',
                
                'Perlengkapan & Peralatan (Alat-alat Kantor)' => 'Investasi',
                'Perlengkapan & Peralatan (Furniture Kantor)' => 'Investasi',
                'Aset Ttp dlm Proses Konstruksi-Bangunan&Prasarana (HGB)' => 'Investasi',
            ];

            // Abaikan jika detail anggaran merupakan header kategori atau subtotal
            $ignoreHeaders = ['Anggaran Dikelola', 'Anggaran Rutin', 'Anggaran Investasi', 'Total'];
            if (in_array(trim($row['detail_anggaran']), $ignoreHeaders)) {
                continue;
            }

            $tahun = $this->lastTahun;
            $bulan = $this->lastBulan;
            $detail = trim($row['detail_anggaran']);
            $kategori = $kategoriMapping[$detail] ?? 'Dikelola'; // Otomatis petakan kategori

            // Bersihkan format angka (hapus segala karakter non-digit seperti Rp, titik, koma, spasi)
            $rkap = (float) preg_replace('/[^0-9]/', '', $row['rkap'] ?? 0);
            $komitmen = (float) preg_replace('/[^0-9]/', '', $row['komitmen'] ?? 0);
            $realisasi = (float) preg_replace('/[^0-9]/', '', $row['realisasi'] ?? 0);

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

            // UpdateOrCreate: Jika data kembar (berdasarkan unik key), perbarui saja. Jika belum ada, buat baru.
            $model = AnggaranAdministrasi::updateOrCreate(
                [
                    'tahun'           => $tahun,
                    'bulan'           => $bulan,
                    'kategori'        => $kategori,
                    'detail_anggaran' => $detail,
                ],
                [
                    'rkap'            => $rkap,
                    'komitmen'        => $komitmen,
                    'realisasi'       => $realisasi,
                    'keterangan'      => $row['keterangan'] ?? null,
                    'data_tambahan'   => $dataTambahan,
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
