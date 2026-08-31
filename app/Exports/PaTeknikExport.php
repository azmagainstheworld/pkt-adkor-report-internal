<?php

namespace App\Exports;

use App\Models\PaTeknikData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaTeknikExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate = false;

    protected $tahun;
    protected $bulan;

    public function __construct($tahun = 'semua', $bulan = 'semua')
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $query = PaTeknikData::with('masterTeknik');
        if ($this->tahun !== 'semua') $query->where('tahun', $this->tahun);
        if ($this->bulan !== 'semua') $query->where('bulan', $this->bulan);
        return $query->orderBy('tahun', 'desc')->get();
    }

    public function headings(): array
    {
        return ['Kelompok Tabel', 'Tahun', 'Bulan', 'Nama Dokumen / Kegiatan', 'Jumlah', 'Data Tambahan (JSON)'];
    }

    public function map($row): array
    {
        return [
            'Tabel ' . ($row->masterTeknik->kelompok_tabel ?? '-'),
            $row->tahun,
            $row->bulan,
            $row->masterTeknik->nama_kegiatan ?? '-',
            $row->jumlah,
            !empty($row->data_tambahan) ? json_encode($row->data_tambahan) : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'EA580C']]]];
    }
}