<?php
namespace App\Exports;

use App\Models\PemeliharaanPeralatanData;
use App\Models\PemeliharaanPeralatanMaster;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PemeliharaanPeralatanExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public $isTemplate = false;

    protected $tahun; protected $bulan; protected $masters; protected $kolomDinamis;

    public function __construct($tahun = 'semua', $bulan = 'semua') {
        $this->tahun = $tahun; $this->bulan = $bulan;
        $this->masters = PemeliharaanPeralatanMaster::orderBy('id', 'asc')->get();
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pemeliharaan_peralatan')->get();
    }

    public function array(): array {
        $query = PemeliharaanPeralatanData::with('pemeliharaanPeralatanMaster');
        if ($this->tahun !== 'semua') $query->where('tahun', $this->tahun);
        if ($this->bulan !== 'semua') $query->where('bulan', $this->bulan);
        
        $grouped = $query->get()->groupBy(function($i) { return $i->tahun . '_' . $i->bulan; });
        $result = [];
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];

        foreach($grouped as $key => $items) {
            $parts = explode('_', $key);
            $row = ['Tahun' => $parts[0], 'Bulan' => $parts[1]];
            
            $mappedItems = []; $dataTambahan = [];
            foreach($items as $item) {
                $mappedItems[$item->peralatan_id] = $item->jumlah;
                if (!empty($item->data_tambahan)) $dataTambahan = array_merge($dataTambahan, $item->data_tambahan);
            }
            foreach($this->masters as $master) { $row[$master->nama_peralatan] = $mappedItems[$master->id] ?? 0; }
            foreach($this->kolomDinamis as $kolom) { $row[$kolom->nama_kolom] = $dataTambahan[$kolom->nama_kolom] ?? '-'; }
            $result[] = $row;
        }

        usort($result, function($a, $b) use ($monthsOrder) {
            if($a['Tahun'] == $b['Tahun']) return $monthsOrder[$b['Bulan']] <=> $monthsOrder[$a['Bulan']];
            return $b['Tahun'] <=> $a['Tahun'];
        });

        return $result;
    }

    public function headings(): array {
        $headers = ['Tahun', 'Bulan'];
        foreach($this->masters as $master) $headers[] = $master->nama_peralatan;
        foreach($this->kolomDinamis as $kolom) $headers[] = $kolom->nama_kolom;
        return $headers;
    }

    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '0056A3']]]];
    }
}