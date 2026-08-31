<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\Ketidakhadiran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KetidakhadiranExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate;
    protected $tahun;
    protected $bulan;

    public function __construct($isTemplate = false, $tahun = null, $bulan = null)
    {
        $this->isTemplate = $isTemplate;
        $this->tahun = $tahun ?? now()->year;
        $this->bulan = $bulan ?? Ketidakhadiran::bulanList()[now()->month - 1];
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        if ($this->isTemplate) {
            return collect([]); // Template kosong
        }

        // Ambil SEMUA karyawan, di-left join dengan data ketidakhadiran bulan terpilih
        return Karyawan::leftJoin('ketidakhadiran', function ($join) {
                $join->on('ketidakhadiran.karyawan_id', '=', 'karyawan.id')
                    ->where('ketidakhadiran.tahun', $this->tahun)
                    ->where('ketidakhadiran.bulan', $this->bulan);
            })
            ->select([
                'karyawan.nama', 'karyawan.npk', 'ketidakhadiran.tahun', 'ketidakhadiran.bulan',
                'ketidakhadiran.keterangan', 'ketidakhadiran.dinas', 'ketidakhadiran.cuti',
                'ketidakhadiran.izin', 'ketidakhadiran.training', 'ketidakhadiran.dispensasi', 'ketidakhadiran.detasering'
            ])
            ->orderBy('karyawan.nama', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return ['Tahun', 'Bulan', 'Nama', 'NPK', 'Keterangan', 'Dinas', 'Cuti', 'Izin', 'Training', 'Dispensasi', 'Detasering'];
    }

    public function map($row): array
    {
        return [
            $row->tahun ?? $this->tahun,
            $row->bulan ?? $this->bulan,
            $row->nama,
            $row->npk,
            $row->keterangan,
            $row->dinas ?? 0,
            $row->cuti ?? 0,
            $row->izin ?? 0,
            $row->training ?? 0,
            $row->dispensasi ?? 0,
            $row->detasering ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [ 1 => ['font' => ['bold' => true]] ];
    }
}