<?php
namespace App\Exports;

use App\Models\DofMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class DofExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public $kelompok;

    public function __construct($kelompok)
    {
        $this->kelompok = $kelompok;
    }

    public function collection()
    {
        // Return an empty collection for template
        return collect([]);
    }

    public function headings(): array
    {
        $headings = ['Tahun', 'Bulan'];
        
        $masters = DofMaster::where('kelompok_tabel', $this->kelompok)->orderBy('id', 'asc')->get();
        foreach ($masters as $master) {
            $headings[] = $master->nama_kegiatan;
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'EA580C']]
            ]
        ];
    }
}