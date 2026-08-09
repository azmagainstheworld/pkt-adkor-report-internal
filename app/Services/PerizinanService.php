<?php

namespace App\Services;

use App\Models\PerizinanProses;
use App\Models\PerizinanTerbit;
use Carbon\Carbon;

class PerizinanService
{
    /**
     * Mendapatkan statistik Perizinan (Proses vs Terbit)
     */
    public function getStatistikPerizinan($tahun, $bulan)
    {
        // 1. Ambil jumlah perizinan yang sedang diproses
        $proses = PerizinanProses::where('tahun', $tahun)->where('bulan', $bulan)->first();
        $jumlahProses = $proses ? $proses->jumlah_proses : 0;

        // 2. Hitung jumlah perizinan yang terbit pada tahun & bulan tersebut
        // Menggunakan array mapping untuk konversi string bulan ke angka (1-12)
        $bulanAngka = $this->konversiBulanKeAngka($bulan);
        
        $jumlahTerbit = PerizinanTerbit::whereYear('tanggal_sejak', $tahun)
                                        ->whereMonth('tanggal_sejak', $bulanAngka)
                                        ->count();

        return [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'jumlah_proses' => $jumlahProses,
            'jumlah_terbit' => $jumlahTerbit,
            'total_aktivitas' => $jumlahProses + $jumlahTerbit
        ];
    }

    /**
     * Helper internal untuk konversi Enum Bulan ke Angka
     */
    private function konversiBulanKeAngka($stringBulan)
    {
        $bulanMap = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        return $bulanMap[$stringBulan] ?? 1; // Default Januari jika tidak valid
    }
}