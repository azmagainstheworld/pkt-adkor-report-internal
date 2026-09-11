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
        $table2Result = \App\Http\Controllers\JasaFotocopyController::getTable2Data($this->tahun, $this->bulan);
        $dataTable2 = $table2Result['dataTable2'];

        $exportData = [];
        $no = 1;
        foreach($dataTable2 as $row) {
            $exportData[] = [
                'NO' => $no++,
                'Tahun' => $row['tahun'],
                'Bulan' => $row['bulan'],
                'UNIT KERJA' => $row['unit_kerja'],
                'Cost Centre' => $row['cost_centre'],
                'Ket.' => $row['keterangan'],
                'Type Mesin' => $row['tipe_mesin'],
                'Jlh pemakaian Bln' => $row['pemakaian_bln_ini'],
                'Jlh pemakaian s.d. Bln' => $row['pemakaian_sd'],
                'Biaya fee bulan' => $row['fee_bln_ini'],
                'Biaya fee s.d. bulan' => $row['fee_sd'],
                'Biaya fee/Lbr' => $row['fee_per_lbr'],
                'Biaya sewa/bulan' => $row['sewa_bln_ini'],
                'Biaya Jasa Sewa bln & Fee' => $row['total_bln_ini'],
                'Total biaya Sewa & Fee s.d. bln' => $row['total_sd'],
            ];
        }

        return $exportData;
    }

    public function headings(): array { 
        return [
            'NO', 'Tahun', 'Bulan', 'UNIT KERJA', 'Cost Centre', 'Ket.', 'Type Mesin', 
            'Jlh pemakaian Bln', 'Jlh pemakaian s.d. Bln', 'Biaya fee bulan', 
            'Biaya fee s.d. bulan', 'Biaya fee/Lbr', 'Biaya sewa/bulan', 
            'Biaya Jasa Sewa bln & Fee', 'Total biaya Sewa & Fee s.d. bln'
        ]; 
    }

    public function styles(Worksheet $sheet) { 
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '1D4ED8']]]]; 
    }
}