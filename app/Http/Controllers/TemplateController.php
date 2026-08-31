<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TemplateController extends Controller
{
    public function download($modul)
    {
        $exportClass = null;

        switch ($modul) {
            case 'anggaran':
                $exportClass = new \App\Exports\AnggaranExport(true);
                break;
            case 'dof-1':
                $exportClass = new \App\Exports\DofExport(1);
                break;
            case 'dof-2':
                $exportClass = new \App\Exports\DofExport(2);
                break;
            case 'jasa-fotocopy':
                $exportClass = new \App\Exports\JasaFotocopyExport(true);
                break;
            case 'karyawan':
                $exportClass = new \App\Exports\KaryawanExport(true);
                break;
            case 'keluarga-karyawan':
                $exportClass = new \App\Exports\KeluargaKaryawanExport(true);
                break;
            case 'ketidakhadiran':
                $exportClass = new \App\Exports\KetidakhadiranExport(true);
                break;
            case 'masalah-kendala':
                $exportClass = new \App\Exports\MasalahKendalaExport(true);
                break;
            case 'pa-non-tekstual':
                $exportClass = new \App\Exports\PaNonTekstualExport(true);
                break;
            case 'pa-teknik':
                $exportClass = new \App\Exports\PaTeknikExport(true);
                break;
            case 'pa-tekstual':
                $exportClass = new \App\Exports\PaTekstualExport(true);
                break;
            case 'pelaporan':
                $exportClass = new \App\Exports\PelaporanExport(true);
                break;
            case 'pemeliharaan-peralatan':
                $exportClass = new \App\Exports\PemeliharaanPeralatanExport(true);
                break;
            case 'pemeliharaan-rutin':
                $exportClass = new \App\Exports\PemeliharaanRutinExport(true);
                break;
            case 'pengiriman-dokumen':
                $exportClass = new \App\Exports\PengirimanDokumenExport(true);
                break;
            case 'perizinan-proses':
                $exportClass = new \App\Exports\PerizinanProsesExport(true);
                break;
            case 'perizinan-terbit':
                $exportClass = new \App\Exports\PerizinanTerbitExport(true);
                break;
            case 'program-strategis':
                $exportClass = new \App\Exports\ProgramStrategisExport(true);
                break;
            case 'surat':
                $exportClass = new \App\Exports\SuratExport(true);
                break;
            default:
                abort(404, "Modul template tidak ditemukan.");
        }

        // Set the property added by our script to force it to return an empty collection
        if (property_exists($exportClass, 'isTemplate')) {
            $exportClass->isTemplate = true;
        }

        return Excel::download($exportClass, "Template_{$modul}.xlsx");
    }
}
