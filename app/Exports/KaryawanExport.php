<?php

namespace App\Exports;

use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $isTemplate;
    protected $type;
    protected $kolomTabel;
    protected $kolomProfil;
    protected $kolomKeluarga;

    public function __construct($isTemplate = false, $type = 'ringkasan')
    {
        $this->isTemplate = $isTemplate;
        $this->type = $type;
        $this->kolomTabel = DB::table('dynamic_columns')->where('modul', 'karyawan_tabel')->get();
        
        if ($type === 'lengkap') {
            $this->kolomProfil = DB::table('dynamic_columns')->where('modul', 'karyawan_profil')->get();
            $this->kolomKeluarga = DB::table('dynamic_columns')->where('modul', 'keluarga_karyawan')->get();
        }
    }

    public function collection()
    {
        if ($this->isTemplate) return collect([]);

        $karyawanQuery = Karyawan::orderBy('nama', 'asc');

        if ($this->type === 'lengkap') {
            $karyawans = $karyawanQuery->with('keluarga')->get();
            $flattened = collect();
            
            foreach($karyawans as $k) {
                if($k->keluarga->isEmpty()) {
                    $flattened->push(['karyawan' => $k, 'keluarga' => null]);
                } else {
                    foreach($k->keluarga as $kel) {
                        $flattened->push(['karyawan' => $k, 'keluarga' => $kel]);
                    }
                }
            }
            return $flattened;
        }

        return $karyawanQuery->get();
    }

    public function headings(): array
    {
        $headers = ['Nama', 'Gol/Grade', 'NPK', 'TTL', 'MPP/PBP', 'Ket. Pensiun', 'Keterangan', 'Alamat', 'Ukuran Kaos'];
        
        foreach ($this->kolomTabel as $kolom) {
            $headers[] = $kolom->nama_kolom;
        }

        if ($this->type === 'lengkap') {
            array_push($headers, 'No. PTK', 'No. HP', 'Tempat Lahir', 'Tanggal Lahir');
            
            foreach ($this->kolomProfil as $kolom) {
                $headers[] = $kolom->nama_kolom . ' (Profil)';
            }
            
            array_push($headers, 'Nama Keluarga', 'Hubungan', 'TTL Keluarga');
            
            foreach ($this->kolomKeluarga as $kolom) {
                $headers[] = $kolom->nama_kolom . ' (Keluarga)';
            }
        }

        return $headers;
    }

    public function map($row): array
    {
        if ($this->type === 'lengkap') {
            $karyawan = $row['karyawan'];
            $keluarga = $row['keluarga'];
        } else {
            $karyawan = $row;
            $keluarga = null;
        }

        $tambahanTabel = is_string($karyawan->data_tambahan) ? json_decode($karyawan->data_tambahan, true) : ($karyawan->data_tambahan ?? []);

        $mapped = [
            $karyawan->nama,
            $karyawan->gol_grade,
            $karyawan->npk,
            trim(($karyawan->tempat_lahir ?? '') . ', ' . ($karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('Y-m-d') : ''), ', '),
            $karyawan->mpp_pbp ? \Carbon\Carbon::parse($karyawan->mpp_pbp)->format('Y-m-d') : '',
            $karyawan->ket_pensiun,
            $karyawan->keterangan,
            $karyawan->alamat,
            $karyawan->ukuran_kaos,
        ];

        foreach ($this->kolomTabel as $kolom) {
            $mapped[] = $tambahanTabel[$kolom->nama_kolom] ?? '';
        }

        if ($this->type === 'lengkap') {
            $mapped[] = $karyawan->no_ptk;
            $mapped[] = $karyawan->no_hp;
            $mapped[] = $karyawan->tempat_lahir;
            $mapped[] = $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('Y-m-d') : '';
            
            foreach ($this->kolomProfil as $kolom) {
                $mapped[] = $tambahanTabel[$kolom->nama_kolom] ?? ''; 
            }

            if ($keluarga) {
                $mapped[] = $keluarga->nama;
                $mapped[] = $keluarga->hubungan;
                $tglLhrKel = $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->format('d/m/Y') : '-';
                $mapped[] = ($keluarga->tempat_lahir ?? '-') . ', ' . $tglLhrKel;
                
                $tambahanKeluarga = is_string($keluarga->data_tambahan) ? json_decode($keluarga->data_tambahan, true) : ($keluarga->data_tambahan ?? []);
                foreach ($this->kolomKeluarga as $kolom) {
                    $mapped[] = $tambahanKeluarga[$kolom->nama_kolom] ?? '';
                }
            } else {
                array_push($mapped, '-', '-', '-');
                foreach ($this->kolomKeluarga as $kolom) {
                    $mapped[] = '-';
                }
            }
        }

        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        return [ 1 => ['font' => ['bold' => true]] ];
    }
}