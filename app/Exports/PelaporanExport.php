<?php

namespace App\Exports;

use App\Models\Pelaporan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelaporanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pelaporan')->get();
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        if ($this->isTemplate) {
            return collect([]);
        }

        $query = Pelaporan::query();
        
        if ($this->tahun !== 'semua') $query->whereYear('tanggal', $this->tahun);
        if ($this->bulan !== 'semua') {
            $mapBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
            if (isset($mapBulan[$this->bulan])) $query->whereMonth('tanggal', $mapBulan[$this->bulan]);
        }

        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function headings(): array
    {
        $headers = ['Tujuan Laporan', 'Nomor Laporan', 'Laporan', 'Tanggal', 'Jenis'];
        
        foreach ($this->kolomDinamis as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }

        return $headers;
    }

    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        $mapped = [
            $row->tujuan,
            $row->nomor,
            $row->laporan,
            $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') : '',
            $row->jenis,
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