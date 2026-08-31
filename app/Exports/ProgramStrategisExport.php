<?php

namespace App\Exports;

use App\Models\ProgramStrategis;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProgramStrategisExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate;
    protected $tahun;
    protected $kolomDinamis;
    protected $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    public function __construct($isTemplate = false, $tahun = 'semua')
    {
        $this->isTemplate = $isTemplate;
        $this->tahun = $tahun;
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'program_strategis')->get();
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]); 

        $query = ProgramStrategis::query();
        if ($this->tahun !== 'semua') {
            $query->where('tahun', $this->tahun);
        }

        return $query->orderBy('tahun', 'desc')->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        $headers = [
            'Tahun', 'Bulan', 'Sasaran', 'Program Strategis', 'Program Kegiatan', 
            'Target Waktu Mulai', 'Target Waktu Selesai', 'Realisasi (%)', 
            'Progress Saat Ini', 'Kendala', 'Keterangan Tambahan', 'Status'
        ];
        
        foreach ($this->kolomDinamis as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }
        return $headers;
    }

    protected $lastParentKey = null;

    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        // Pakai Array Bahasa Indonesia Anti-Error
        $bulanStr = ($row->bulan && is_numeric($row->bulan)) ? $this->bulanIndo[(int)$row->bulan] : '-';

        $currentKey = $row->tahun . '_' . $row->bulan . '_' . $row->sasaran . '_' . $row->program_strategis;
        $isFirst = ($this->lastParentKey !== $currentKey);
        $this->lastParentKey = $currentKey;

        $mapped = [
            $isFirst ? $row->tahun : '',
            $isFirst ? $bulanStr : '',
            $isFirst ? $row->sasaran : '',
            $isFirst ? $row->program_strategis : '',
            $row->deskripsi_kegiatan, // Tetap ngambil dari database
            $isFirst ? $row->target_waktu_start : '',
            $isFirst ? $row->target_waktu_end : '',
            $row->realisasi,
            $row->progress_saat_ini,
            $isFirst ? $row->kendala : '',
            $isFirst ? $row->keterangan_tambahan : '',
            $isFirst ? $row->status : '',
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