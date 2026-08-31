<?php
namespace App\Exports;

use App\Models\JasaFotocopy;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JasaFotocopyExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public $isTemplate = false;

    protected $tahun; protected $bulan;

    public function __construct($tahun = 'semua', $bulan = 'semua') {
        $this->tahun = $tahun; $this->bulan = $bulan;
    }

    public function array(): array {
        $query = JasaFotocopy::query();
        if ($this->tahun !== 'semua') $query->where('tahun', $this->tahun);
        if ($this->bulan !== 'semua') $query->where('bulan', $this->bulan);
        $data = $query->get();

        $dataTable1 = [];
        $groupedByYear = $data->groupBy('tahun');
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];

        foreach($groupedByYear as $thn => $yearItems) {
            $groupedByMonth = $yearItems->groupBy('bulan');
            foreach($groupedByMonth as $bln => $items) {
                $dataTable1[] = [
                    'Tahun' => $thn, 'Bulan' => $bln,
                    'Mesin FC' => $items->count(),
                    'Jumlah Pemakaian Jasa Penyediaan Fotocopy' => $items->sum('pemakaian_lembar'),
                    'Nilai Jasa Penyediaan Fotocopy' => $items->sum(function($item) {
                        return ($item->pemakaian_lembar * $item->biaya_fee_per_lembar) + $item->biaya_sewa_mesin;
                    }),
                ];
            }
        }

        usort($dataTable1, function($a, $b) use ($monthsOrder) { 
            if ($a['Tahun'] == $b['Tahun']) { return $monthsOrder[$a['Bulan']] <=> $monthsOrder[$b['Bulan']]; }
            return $b['Tahun'] <=> $a['Tahun']; 
        });

        return $dataTable1;
    }

    public function headings(): array { 
        return ['Tahun', 'Bulan', 'Mesin FC', 'Jumlah Pemakaian Jasa Penyediaan Fotocopy', 'Nilai Jasa Penyediaan Fotocopy']; 
    }

    public function styles(Worksheet $sheet) { 
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '1D4ED8']]]]; 
    }
}