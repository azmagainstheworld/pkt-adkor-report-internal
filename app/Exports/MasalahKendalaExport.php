<?php

namespace App\Exports;

use App\Models\MasalahKendala;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasalahKendalaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate;
    protected $kolomDinamis;
    protected $tahun;
    protected $bulan;

    public function __construct($isTemplate = false, $tahun = 'semua', $bulan = 'semua')
    {
        $this->isTemplate = $isTemplate;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'masalah_kendala')->get();
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $query = MasalahKendala::query();
        if ($this->tahun !== 'semua') $query->where('tahun', $this->tahun);
        if ($this->bulan !== 'semua') $query->where('bulan', $this->bulan);

        return $query->orderBy('tahun', 'desc')->get();
    }

    public function headings(): array
    {
        $headers = ['Tahun', 'Bulan', 'Masalah/Kendala', 'Solusi'];
        foreach ($this->kolomDinamis as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }
        return $headers;
    }

    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        $mapped = [
            $row->tahun,
            $row->bulan,
            $row->masalah_kendala,
            $row->solusi,
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