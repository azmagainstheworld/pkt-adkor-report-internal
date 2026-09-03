<?php

namespace App\Exports;

use App\Models\PaNonTekstualValue;
use App\Models\PaNonTekstualType;
use App\Models\KolomDinamis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaNonTekstualExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate = false;

    protected $tahun;
    protected $bulan;
    protected $masterCols;
    protected $dynamicCols;

    public function __construct($tahun = 'semua', $bulan = 'semua')
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->masterCols = PaNonTekstualType::orderBy('id', 'asc')->get();
        $this->dynamicCols = KolomDinamis::where('modul', 'pa_non_tekstual')->orderBy('id', 'asc')->get();
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $query = PaNonTekstualValue::with('type');
        if ($this->tahun !== 'semua') $query->where('tahun', $this->tahun);
        if ($this->bulan !== 'semua') $query->where('bulan', $this->bulan);
        
        $rawData = $query->orderBy('tahun', 'desc')->get();
        
        // Pivot grouped data
        $groupedData = $rawData->groupBy(function($item) {
            return $item->tahun . '_' . $item->bulan;
        });

        $dataTable = collect();
        foreach($groupedData as $key => $items) {
            $parts = explode('_', $key);
            $tahun = $parts[0];
            $bulan = $parts[1];

            $row = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'items' => [],
                'data_tambahan' => []
            ];

            foreach($items as $item) {
                $row['items'][$item->type_id] = $item->jumlah;
                if (!empty($item->data_tambahan)) {
                    $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : $item->data_tambahan;
                    if(is_array($tambahan)) {
                        $row['data_tambahan'] = array_merge($row['data_tambahan'], $tambahan);
                    }
                }
            }
            $dataTable->push($row);
        }

        return $dataTable;
    }

    public function headings(): array
    {
        $headers = ['Tahun', 'Bulan'];

        foreach ($this->masterCols as $m) {
            $headers[] = $m->name;
        }

        foreach ($this->dynamicCols as $k) {
            $headers[] = $k->nama_kolom;
        }

        return $headers;
    }

    public function map($row): array
    {
        $mapped = [
            $row['tahun'] ?? '',
            $row['bulan'] ?? '',
        ];

        foreach ($this->masterCols as $m) {
            $mapped[] = $row['items'][$m->id] ?? 0;
        }

        foreach ($this->dynamicCols as $k) {
            $mapped[] = $row['data_tambahan'][$k->nama_kolom] ?? '';
        }

        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'EA580C']]]];
    }
}