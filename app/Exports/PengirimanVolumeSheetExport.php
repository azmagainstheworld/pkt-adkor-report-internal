<?php

namespace App\Exports;

use App\Models\PengirimanDokumen;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengirimanVolumeSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $isTemplate;
    protected $kolomDinamis;
    protected $year;
    protected $month;

    public function __construct($isTemplate = false, $year = 'semua', $month = 'semua')
    {
        $this->isTemplate = $isTemplate;
        $this->year = $year;
        $this->month = $month;
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pengiriman_dokumen')->get();
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $masterMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $query = PengirimanDokumen::query();
        if ($this->year !== 'semua') $query->where('tahun', $this->year);
        if ($this->month !== 'semua') $query->where('bulan', $this->month);

        $query->orderBy('tahun', 'desc');
        $query->orderByRaw("FIELD(bulan, '" . implode("','", $masterMonths) . "')");

        return $query->get();
    }

    public function headings(): array
    {
        // Urutan kolom mengikuti tabel "Rincian Volume Dokumen" di halaman web
        $headers = [
            'Tahun',
            'Bulan',
            'Penerimaan mailroom',
            'Pengiriman dalam negeri',
            'Pengiriman luar negeri',
            'Registrasi Surat Masuk via DOF',
        ];

        foreach ($this->kolomDinamis as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }

        return $headers;
    }

    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        $mapped = [
            $row->tahun,
            $row->bulan,
            $row->penerimaan_mailroom,
            $row->pengiriman_dalam_negeri,
            $row->pengiriman_luar_negeri,
            $row->registrasi_surat_masuk_dof,
        ];

        foreach ($this->kolomDinamis as $kolom) {
            $mapped[] = $tambahan[$kolom->nama_kolom] ?? '';
        }

        return $mapped;
    }

    public function title(): string
    {
        return 'Rincian Volume Dokumen';
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}