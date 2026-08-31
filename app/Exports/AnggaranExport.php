<?php

namespace App\Exports;

use App\Models\AnggaranAdministrasi;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnggaranExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate;
    protected $kolomDinamis;
    protected $year;
    protected $month;

    public function __construct($isTemplate = false, $year = 'all', $month = 'all')
    {
        $this->isTemplate = $isTemplate;
        $this->year = $year;
        $this->month = $month;
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'anggaran')->get();
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        if ($this->isTemplate) {
            return collect([]); 
        }

        $query = AnggaranAdministrasi::query();
        
        if ($this->year !== 'all') {
            $query->where('tahun', $this->year);
        }
        if ($this->month !== 'all') {
            $query->where('bulan', $this->month);
        }

        return $query->orderBy('tahun', 'desc')->orderBy('bulan', 'asc')->orderBy('kategori', 'asc')->get();
    }

    public function headings(): array
    {
        // Sesuaikan dengan tampilan tabel utama Zahra!
        $headers = [
            'Tahun', 'Bulan', 'Detail Anggaran', 'RKAP', 'Komitmen', 'Realisasi', 
            'Realisasi + Komitmen', '% Realisasi+Komitmen', 'Sisa Anggaran', '% Sisa Anggaran'
        ];
        
        foreach ($this->kolomDinamis as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }

        return $headers;
    }

    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        // Hitung-hitungan ala akuntan cantik
        $realPlusKomit = $row->komitmen + $row->realisasi;
        $sisa = $row->rkap - $realPlusKomit;
        $percRealKomit = $row->rkap > 0 ? round(($realPlusKomit / $row->rkap) * 100, 1) . '%' : '0%';
        $percSisa = $row->rkap > 0 ? round(($sisa / $row->rkap) * 100, 1) . '%' : '0%';

        $mapped = [
            $row->tahun,
            $row->bulan,
            $row->detail_anggaran,
            $row->rkap,
            $row->komitmen,
            $row->realisasi,
            $realPlusKomit,
            $percRealKomit,
            $sisa,
            $percSisa,
        ];

        foreach ($this->kolomDinamis as $kolom) {
            $mapped[] = $tambahan[$kolom->nama_kolom] ?? '';
        }

        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        return [ 1 => ['font' => ['bold' => true]] ];
    }
}