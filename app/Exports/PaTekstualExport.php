<?php

namespace App\Exports;

use App\Models\PaTekstualData;
use App\Models\PaTekstualMaster;
use App\Models\PaTekstualKolom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaTekstualExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate;
    protected $kelompokTabel;
    protected $masterCols;
    protected $dynamicCols;

    public function __construct($kelompokTabel = 1, $isTemplate = false)
    {
        $this->kelompokTabel = $kelompokTabel;
        $this->isTemplate = $isTemplate;
        $this->masterCols = PaTekstualMaster::where('kelompok_tabel', $this->kelompokTabel)->orderBy('id', 'asc')->get();
        $this->dynamicCols = PaTekstualKolom::where('kelompok_tabel', $this->kelompokTabel)->orderBy('id', 'asc')->get();
    }

    public function collection()
    {
        if ($this->isTemplate) {
            return collect([]);
        }

        $masterIds = $this->masterCols->pluck('id')->toArray();
        $query = PaTekstualData::with('masterTekstual')
                    ->whereIn('master_id', $masterIds)
                    ->orderBy('tahun', 'desc');

        if (request('tahun') && request('tahun') != 'semua') {
            $query->where('tahun', request('tahun'));
        }
        if (request('bulan') && request('bulan') != 'semua') {
            $query->where('bulan', request('bulan'));
        }

        $rawData = $query->get();
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
                $row['items'][$item->master_id] = $item->jumlah;
                if (!empty($item->data_tambahan)) {
                    $row['data_tambahan'] = array_merge($row['data_tambahan'], $item->data_tambahan);
                }
            }
            $dataTable->push($row);
        }

        return $dataTable;
    }

    public function headings(): array
    {
        $headers = [
            'Tahun',
            'Bulan',
        ];

        foreach ($this->masterCols as $m) {
            $headers[] = $m->nama_dokumen;
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
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'EA580C']]],
        ];
    }
}