<?php

namespace App\Exports;

use App\Models\Undangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UndanganExport implements FromCollection, WithHeadings, WithMapping
{
    protected $isTemplate;

    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }

    public function collection()
    {
        if ($this->isTemplate) {
            return collect([]);
        }

        return Undangan::orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->tahun,
            $row->bulan,
            $row->undangan_intern,
            $row->undangan_ekstern,
        ];
    }

    public function headings(): array
    {
        return [
            'Tahun',
            'Bulan',
            'Undangan Intern',
            'Undangan Ekstern',
        ];
    }
}
