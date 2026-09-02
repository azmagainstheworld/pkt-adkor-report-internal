<?php

namespace App\Exports;

use App\Models\JasaKurirData;
use App\Models\JasaKurirMaster;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JasaKurirExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate;
    protected $tahun;
    protected $bulan;
    protected $kurirMaster;
    protected $kolomDinamis;
    protected $masterMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    public function __construct($isTemplate = false, $tahun = 'semua', $bulan = 'semua')
    {
        $this->isTemplate = $isTemplate;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->kurirMaster = JasaKurirMaster::where('aktif', true)->orderBy('id')->get();
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'jasa_kurir')->get();
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $query = JasaKurirData::with('jasaKurirMaster');
        if ($this->tahun !== 'semua') $query->where('tahun', $this->tahun);
        if ($this->bulan !== 'semua') $query->where('bulan', $this->bulan);
        
        $rawData = $query->get();

        $tableData = [];
        foreach ($rawData as $data) {
            $key = $data->tahun . '-' . $data->bulan;
            
            if (!isset($tableData[$key])) {
                $tableData[$key] = [
                    'tahun' => $data->tahun,
                    'bulan' => $data->bulan,
                    'total_semua' => 0,
                    'data_tambahan' => []
                ];
                foreach ($this->kurirMaster as $kurir) {
                    $tableData[$key]['kurir_' . $kurir->id] = 0;
                }
            }

            $tableData[$key]['kurir_' . $data->jasa_kurir_id] = $data->jumlah;
            $tableData[$key]['total_semua'] += $data->jumlah;
            
            if (!empty($data->data_tambahan)) {
                $tableData[$key]['data_tambahan'] = array_merge($tableData[$key]['data_tambahan'], $data->data_tambahan);
            }
        }

        // Sort collection using masterMonths
        usort($tableData, function($a, $b) {
            if ($a['tahun'] == $b['tahun']) {
                $idxA = array_search($a['bulan'], $this->masterMonths);
                $idxB = array_search($b['bulan'], $this->masterMonths);
                return $idxA <=> $idxB;
            }
            return $b['tahun'] <=> $a['tahun'];
        });

        return collect($tableData);
    }

    public function headings(): array
    {
        $headings = ['Tahun', 'Bulan'];
        foreach ($this->kurirMaster as $kurir) {
            $headings[] = strtoupper($kurir->nama_kurir);
        }
        $headings[] = 'Total';

        foreach ($this->kolomDinamis as $kolom) {
            $headings[] = $kolom->nama_kolom;
        }

        return $headings;
    }

    public function map($row): array
    {
        $mapped = [
            $row['tahun'],
            $row['bulan'],
        ];

        foreach ($this->kurirMaster as $kurir) {
            $mapped[] = $row['kurir_' . $kurir->id];
        }

        $mapped[] = $row['total_semua'];

        foreach ($this->kolomDinamis as $kolom) {
            $mapped[] = $row['data_tambahan'][$kolom->nama_kolom] ?? '-';
        }

        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]],
        ];
    }
}
