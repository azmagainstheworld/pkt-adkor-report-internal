<?php

namespace App\Exports;

use App\Models\Surat;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuratExport implements FromView, ShouldAutoSize, WithStyles
{
    public $isTemplate = false;

    protected $tahunFilter;
    protected $bulanFilter;
    protected $jenis;
    protected $masterMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    public function __construct($tahunFilter, $bulanFilter, $jenis)
    {
        $this->tahunFilter = $tahunFilter;
        $this->bulanFilter = $bulanFilter;
        $this->jenis = $jenis; // 'tabel1', 'tabel2', atau 'keduanya'
    }

    public function view(): View
    {
        $rekapData = collect();
        $detailData = collect();
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'surat')->get();

        // Ambil Data Tabel 1 (Rekap) jika diperlukan
        if (in_array($this->jenis, ['tabel1', 'keduanya'])) {
            $rekapData = Surat::selectRaw("
                tahun, bulan,
                SUM(CASE WHEN jenis_surat = 'Surat Masuk' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis_surat = 'Surat Keluar' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_keluar
            ")
            ->when($this->tahunFilter != 'semua', function($q) {
                return $q->where('tahun', $this->tahunFilter);
            })
            ->when($this->bulanFilter != 'semua', function($q) {
                return $q->where('bulan', $this->bulanFilter);
            })
            ->groupBy('tahun', 'bulan')
            ->get()
            ->sortBy(function($item) {
                return array_search($item->bulan, $this->masterMonths);
            });
        }

        // Ambil Data Tabel 2 (Detail Satuan) jika diperlukan
        if (in_array($this->jenis, ['tabel2', 'keduanya'])) {
            $detailData = Surat::when($this->tahunFilter != 'semua', function($q) {
                    return $q->where('tahun', $this->tahunFilter);
                })
                ->when($this->bulanFilter != 'semua', function($q) {
                    return $q->where('bulan', $this->bulanFilter);
                })
                ->orderBy('tanggal_surat', 'asc')
                ->get();
        }

        return view('excel.laporan-surat', [
            'rekapData' => $rekapData,
            'detailData' => $detailData,
            'jenis' => $this->jenis,
            'tahunFilter' => $this->tahunFilter,
            'bulanFilter' => $this->bulanFilter,
            'kolomDinamis' => $kolomDinamis
        ]);
    }

    // Menambahkan border pada tabel (opsional tapi disarankan agar rapi)
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}