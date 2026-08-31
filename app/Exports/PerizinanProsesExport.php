<?php

namespace App\Exports;

use App\Models\PerizinanProsesList;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerizinanProsesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate;
    protected $kolomDinamis;
    protected $tahun;

    public function __construct($isTemplate = false, $tahun = 'semua')
    {
        $this->isTemplate = $isTemplate;
        $this->tahun = $tahun;
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'perizinan_proses_list')->get();
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
              ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $query = PerizinanProsesList::query();
        if ($this->tahun !== 'semua') $query->where('tahun', $this->tahun);

        return $query->orderBy('tahun', 'desc')->orderBy('nama_proses', 'asc')->get();
    }

    public function headings(): array
    {
        $headers = ['Tahun', 'Nama Proses', 'Target', 'Periode'];
        foreach ($this->kolomDinamis as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }
        return $headers;
    }

    protected $lastProsesId = '';

    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        $prosesId = $row->tahun . '_' . $row->nama_proses;
        $displayTahun = $row->tahun;
        $displayNama = $row->nama_proses;

        if ($this->lastProsesId === $prosesId && !$this->isTemplate) {
            $displayTahun = '';
            $displayNama = '';
        }
        $this->lastProsesId = $prosesId;

        $mapped = [
            $displayTahun,
            $displayNama,
            $row->target,
            $row->periode,
        ];

        foreach ($this->kolomDinamis as $kolom) {
            $mapped[] = $tambahan[$kolom->nama_kolom] ?? '';
        }

        return $mapped;
    }

}
