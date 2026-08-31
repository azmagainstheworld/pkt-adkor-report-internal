<?php

namespace App\Imports;

use App\Models\PerizinanProsesList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PerizinanProsesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public function collection(Collection $rows)
    {
        // 1. Pengaman pertama: Cek apakah file benar-benar ada isinya
        if ($rows->isEmpty()) {
            throw new \Exception('File Excel kosong. Pastikan Anda mengisi data di baris kedua dan seterusnya.');
        }

        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'perizinan_proses_list')->get();
        $jumlahDataDitemukan = 0;
        
        //   Simpan nama proses terakhir untuk menangani cell Excel yang dimerge
        $lastNamaProses = 'Data Tanpa Nama';

        foreach ($rows as $index => $row) {
            $rowData = $row->toArray();

            // ==========================================
            // TEKNIK ANTI-GAGAL: Tangkap Nama Proses 
            // ==========================================
            $namaProses = $rowData['nama_proses'] ?? $rowData['nama'] ?? $rowData['proses'] ?? null;
            
            // Jika kosong (mungkin karena merge cell di Excel), kita gunakan nama proses dari baris sebelumnya
            if (empty(trim($namaProses))) {
                $namaProses = $lastNamaProses;
            } else {
                $lastNamaProses = $namaProses; // Update untuk baris berikutnya
            }

            // Tangkap Tahun (Otomatis ke tahun ini jika dikosongkan)
            $tahun = $rowData['tahun'] ?? now()->year;
            if (empty(trim($tahun))) {
                $tahun = now()->year;
            }

            // Tangkap data lainnya
            $target = $rowData['target'] ?? null;
            $periode = $rowData['periode'] ?? $rowData['periode_bulan'] ?? null;

            // Perbaikan untuk tanggal excel (angka serial)
            if (is_numeric($periode)) {
                if ($periode > 10000) {
                    try {
                        $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($periode);
                        $periode = $dateObj->format('M-y'); // misal Jan-26
                    } catch (\Exception $e) {
                        // Abaikan jika bukan tanggal valid
                    }
                } else {
                    $periode = (string) $periode; // Just year or string
                }
            }

            // Jika target dan periode kosong, skip (baris benar-benar kosong)
            if (empty(trim($target)) && empty(trim($periode))) {
                continue;
            }

            // ==========================================
            // MAPPING KOLOM DINAMIS (JSON)
            // ==========================================
            $dataTambahan = [];
            foreach ($kolomDinamis as $kolom) {
                $keyExcel = strtolower(str_replace(' ', '_', $kolom->nama_kolom));
                if (array_key_exists($keyExcel, $rowData)) {
                    $dataTambahan[$kolom->nama_kolom] = $rowData[$keyExcel];
                }
            }

            // ==========================================
            // EKSEKUSI SIMPAN KE DATABASE
            // ==========================================
            // Kita gunakan updateOrCreate dengan kombinasi tahun, nama_proses, dan target 
            // agar mencegah duplikasi data jika user meng-import file yang sama dua kali,
            // namun tetap mengizinkan satu nama_proses memiliki banyak target.
            PerizinanProsesList::updateOrCreate([
                'tahun'         => $tahun,
                'nama_proses'   => trim($namaProses),
                'target'        => $target,
            ], [
                'periode'       => $periode,
                'data_tambahan' => $dataTambahan,
            ]);

            $jumlahDataDitemukan++;
        }

        // Pengaman kedua: Jika looping terlewati tanpa eksekusi, lempar error
        if ($jumlahDataDitemukan === 0) {
            throw new \Exception('Sistem berhasil membaca file Excel, tetapi tidak menemukan baris data target yang valid untuk dimasukkan ke database.');
        }
    }
}
