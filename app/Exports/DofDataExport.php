<?php

namespace App\Exports;

use App\Models\DofData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DofDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tahun;
    protected $bulan;

    public function __construct($tahun = 'semua', $bulan = 'semua')
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $query = DofData::with('masterDof');
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
            'Tabel ' . ($row->masterDof->kelompok_tabel ?? '-'),
            $row->tahun,
            $row->bulan,
            $row->masterDof->nama_kegiatan ?? '-',
            $row->jumlah,
            !empty($row->data_tambahan) ? json_encode($row->data_tambahan) : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'EA580C']]]];
    }
}
