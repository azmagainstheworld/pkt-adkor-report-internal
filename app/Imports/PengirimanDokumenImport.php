<?php

namespace App\Imports;

use App\Models\PengirimanDokumen;
use App\Models\PengirimanOngkir;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PengirimanDokumenImport implements ToCollection, WithHeadingRow
{
    protected $jenis;
    protected $kolomDinamis;

    public function __construct($jenis)
    {
        $this->jenis = $jenis;
        $this->kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pengiriman_dokumen')->get();
    }

    public function collection(Collection $rows)
    {
        $barisMasuk = 0;

        foreach ($rows as $row) {
            $tahun = $row['tahun'] ?? $row['Tahun'] ?? null;
            $bulan = $row['bulan'] ?? $row['Bulan'] ?? null;

            if (!$tahun || !$bulan) continue;

            $bulanFormatted = ucfirst(strtolower(trim($bulan)));

            if ($this->jenis === 'volume') {
                $record = PengirimanDokumen::firstOrNew([
                    'tahun' => $tahun,
                    'bulan' => $bulanFormatted
                ]);
                $record->penerimaan_mailroom = $row['penerimaan_mailroom'] ?? $record->penerimaan_mailroom ?? 0;
                $record->registrasi_surat_masuk_dof = $row['registrasi_surat_masuk_via_dof'] ?? $row['registrasi_surat_masuk_dof'] ?? $record->registrasi_surat_masuk_dof ?? 0;
                $record->pengiriman_dalam_negeri = $row['pengiriman_dalam_negeri'] ?? $record->pengiriman_dalam_negeri ?? 0;
                $record->pengiriman_luar_negeri = $row['pengiriman_luar_negeri'] ?? $record->pengiriman_luar_negeri ?? 0;
                $record->e_materai = $record->e_materai ?? 0;

                $dataTambahan = $record->data_tambahan ?? [];
                foreach ($this->kolomDinamis as $kolom) {
                    $keyExcel = strtolower(str_replace(' ', '_', $kolom->nama_kolom));
                    if (isset($row[$keyExcel])) {
                        $dataTambahan[$kolom->nama_kolom] = $row[$keyExcel];
                    }
                }
                $record->data_tambahan = empty($dataTambahan) ? null : $dataTambahan;
                $record->save();
            } else if ($this->jenis === 'ongkir') {
                $record = PengirimanOngkir::firstOrNew([
                    'tahun' => $tahun,
                    'bulan' => $bulanFormatted
                ]);
                $valDalam = $row['total_ongkir_dalam_negeri'] ?? $row['total_ongkir_pengiriman_dalam_negeri'] ?? $record->ongkir_dalam_negeri ?? 0;
                $valLuar = $row['total_ongkir_luar_negeri'] ?? $row['total_ongkir_pengiriman_luar_negeri'] ?? $record->ongkir_luar_negeri ?? 0;
                
                $record->ongkir_dalam_negeri = is_string($valDalam) ? (int) preg_replace('/[^0-9]/', '', $valDalam) : (int) $valDalam;
                $record->ongkir_luar_negeri = is_string($valLuar) ? (int) preg_replace('/[^0-9]/', '', $valLuar) : (int) $valLuar;
                
                // Currently ongkir does not have dynamic columns implemented via excel in original script, leaving as is
                $record->save();
            }

            $barisMasuk++;
        }

        if ($barisMasuk === 0) {
            throw new \Exception("Gagal: Excel kosong atau format Tahun/Bulan tidak ditemukan.");
        }
    }
}