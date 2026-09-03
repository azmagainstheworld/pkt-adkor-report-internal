<?php

namespace App\Imports;

use App\Models\SuratRekap;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class SuratRekapImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("Template Excel yang Anda unggah kosong. Harap isi baris data Tahun, Bulan, dsb di bawah judul kolom sebelum mengunggahnya.");
        }

        $firstRow = $rows->first();
        if (!isset($firstRow['surat_masuk']) && !isset($firstRow['total_surat_masuk']) && !isset($firstRow['surat_keluar']) && !isset($firstRow['total_surat_keluar'])) {
            throw new \Exception("Format kolom tidak sesuai. Harap gunakan template yang benar dengan kolom: Tahun, Bulan, Surat Masuk, Surat Keluar.");
        }

        \Illuminate\Support\Facades\Log::info('SuratRekapImport started with ' . $rows->count() . ' rows.');
        foreach ($rows as $index => $row) {
            \Illuminate\Support\Facades\Log::info("Row {$index}: " . json_encode($row));
            if (!isset($row['tahun']) || !isset($row['bulan'])) {
                \Illuminate\Support\Facades\Log::warning("Skipped row {$index} because missing tahun or bulan.");
                continue;
            }

            $bulanFormatted = ucfirst(strtolower(trim($row['bulan'])));

            $rekap = SuratRekap::firstOrCreate([
                'tahun' => trim($row['tahun']),
                'bulan' => $bulanFormatted
            ], [
                'surat_masuk' => 0,
                'surat_keluar' => 0
            ]);

            $valMasuk = isset($row['surat_masuk']) ? $row['surat_masuk'] : (isset($row['total_surat_masuk']) ? $row['total_surat_masuk'] : null);
            $valKeluar = isset($row['surat_keluar']) ? $row['surat_keluar'] : (isset($row['total_surat_keluar']) ? $row['total_surat_keluar'] : null);

            $masuk = $valMasuk !== null ? (int)$valMasuk : $rekap->surat_masuk;
            $keluar = $valKeluar !== null ? (int)$valKeluar : $rekap->surat_keluar;

            $rekap->update([
                'surat_masuk' => $masuk,
                'surat_keluar' => $keluar
            ]);
        }
    }
}
