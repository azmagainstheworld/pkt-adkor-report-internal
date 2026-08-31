<?php

namespace App\Exports;

use App\Models\KeluargaKaryawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class KeluargaKaryawanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public $isTemplate = false;

    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $keluarga = KeluargaKaryawan::with('karyawan')->get();
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'keluarga_karyawan')->get();

        $data = [];
        foreach ($keluarga as $k) {
            $row = [
                $k->karyawan ? $k->karyawan->npk : '-',
                $k->nama,
                $k->hubungan,
                $k->tempat_lahir,
                $k->tanggal_lahir,
            ];

            $tambahan = is_string($k->data_tambahan) ? json_decode($k->data_tambahan, true) : ($k->data_tambahan ?? []);
            foreach ($kolomDinamis as $kolom) {
                $row[] = $tambahan[$kolom->nama_kolom] ?? '-';
            }

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $headers = [
            'NPK Karyawan',
            'Nama Lengkap',
            'Hubungan',
            'Tempat Lahir',
            'Tanggal Lahir',
        ];

        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'keluarga_karyawan')->get();
        foreach ($kolomDinamis as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FF0056A3']]],
        ];
    }
}
