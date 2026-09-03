<?php

namespace App\Exports;

use App\Models\PengirimanOngkir;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengirimanOngkirSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $isTemplate;
    protected $year;
    protected $month;

    public function __construct($isTemplate = false, $year = 'semua', $month = 'semua')
    {
        $this->isTemplate = $isTemplate;
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $masterMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $query = PengirimanOngkir::query();
        if ($this->year !== 'semua') $query->where('tahun', $this->year);
        if ($this->month !== 'semua') $query->where('bulan', $this->month);

        $query->orderBy('tahun', 'desc');
        $query->orderByRaw("FIELD(bulan, '" . implode("','", $masterMonths) . "')");

        return $query->get();
    }

    public function headings(): array
    {
        // Urutan kolom mengikuti tabel "Rincian Total Ongkir Pengiriman Bulanan" di halaman web
        return [
            'Tahun',
            'Bulan',
            'Total Ongkir Pengiriman Dalam Negeri',
            'Total Ongkir Pengiriman Luar Negeri',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tahun,
            $row->bulan,
            $row->ongkir_dalam_negeri,
            $row->ongkir_luar_negeri,
        ];
    }

    public function title(): string
    {
        return 'Rincian Ongkir Pengiriman';
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}