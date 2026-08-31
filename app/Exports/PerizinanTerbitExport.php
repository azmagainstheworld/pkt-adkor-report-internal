<?php

namespace App\Exports;

use App\Models\PerizinanTerbit;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerizinanTerbitExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'perizinan_terbit')->get();
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

        if ($this->isTemplate) {
            return collect([]);
        }

        $query = PerizinanTerbit::query();
        
        if ($this->tahun !== 'semua') $query->whereYear('tanggal_sejak', $this->tahun);
        if ($this->bulan !== 'semua') {
            $mapBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
            if (isset($mapBulan[$this->bulan])) $query->whereMonth('tanggal_sejak', $mapBulan[$this->bulan]);
        }

        return $query->orderBy('tanggal_sejak', 'desc')->get();
    }

    public function headings(): array
    {
        $headers = ['Tahun', 'Perizinan Terbit', 'Nomor', 'Terbit', 'Berakhir', 'Instansi Penerbit', 'Bulan', 'Kegiatan'];
        foreach ($this->kolomDinamis as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }
        return $headers;
    }

    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        // Ambil Bulan dari tanggal_sejak
        $bulanPanjang = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanPendek = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agustus', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $bulanStr = $row->tanggal_sejak ? $bulanPanjang[$row->tanggal_sejak->month] : '-';
        $tahunStr = $row->tanggal_sejak ? $row->tanggal_sejak->format('Y') : '-';
        
        $terbitStr = $row->tanggal_sejak ? $row->tanggal_sejak->format('d-') . $bulanPendek[$row->tanggal_sejak->month] . $row->tanggal_sejak->format('-y') : '';
        $akhirStr = $row->tanggal_akhir ? $row->tanggal_akhir->format('d-') . $bulanPendek[$row->tanggal_akhir->month] . $row->tanggal_akhir->format('-y') : '';

        $mapped = [
            $tahunStr,
            $row->nama_perizinan,
            $row->nomor,
            $terbitStr,
            $akhirStr,
            $row->instansi_penerbit,
            $bulanStr,
            $row->kegiatan,
        ];

        foreach ($this->kolomDinamis as $kolom) {
            $mapped[] = $tambahan[$kolom->nama_kolom] ?? '';
        }

        return $mapped;
    }

}
