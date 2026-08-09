<?php

namespace App\Services;

use App\Models\PaNonTeknik;
use App\Models\PaTeknik;
use App\Models\Dof;
use App\Models\PengirimanDokumen;

class KearsipanService
{
    /**
     * Mengambil dan mengakumulasi Rekap Kearsipan berdasarkan Tahun dan Bulan
     */
    public function getRekapKearsipan($tahun, $bulan)
    {
        // Ambil data dari masing-masing model berdasarkan tahun & bulan
        $paNonTeknik = PaNonTeknik::where('tahun', $tahun)->where('bulan', $bulan)->first();
        $paTeknik = PaTeknik::where('tahun', $tahun)->where('bulan', $bulan)->first();
        $dof = Dof::where('tahun', $tahun)->where('bulan', $bulan)->first();
        $pengiriman = PengirimanDokumen::where('tahun', $tahun)->where('bulan', $bulan)->first();

        // Total akumulasi PA Non Teknik
        $totalPaNonTeknik = $paNonTeknik ? (
            $paNonTeknik->permintaan_dok_asli_berkas + $paNonTeknik->penyerahan_dok_asli_berkas +
            $paNonTeknik->penyerahan_dok_asli_bantex + $paNonTeknik->peminjaman_dok_asli_berkas +
            $paNonTeknik->permintaan_copy_softcopy_berkas + $paNonTeknik->alih_media_lembar +
            $paNonTeknik->rekaman_rapat + $paNonTeknik->upload_dokumen_paradm +
            $paNonTeknik->penyerahan_dokumen_inaktif + $paNonTeknik->pengecekan_dokumen_inaktif_box +
            $paNonTeknik->pemusnahan_dokumen_berkas + $paNonTeknik->stock_name_dokumen_vital +
            $paNonTeknik->permintaan_box_arsip_pcs
        ) : 0;

        // Total akumulasi PA Teknik
        $totalPaTeknik = $paTeknik ? (
            $paTeknik->alih_media_berkas_lembar + $paTeknik->jasa_cetak_gambar_berkas +
            $paTeknik->peminjaman_dok_berkas_lembar + $paTeknik->peminjaman_dok_bantex +
            $paTeknik->peminjaman_dok_cd + $paTeknik->peminjaman_dok_lembar +
            $paTeknik->permintaan_copy_soft_berkas_lembar + $paTeknik->konversi_tif_ke_pdf_file
        ) : 0;

        // Total akumulasi DOF
        $totalDof = $dof ? (
            $dof->registrasi_surat_masuk_dof + $dof->approval_file_scan +
            $dof->approval_stempel_digital + $dof->pendaftaran_akun +
            $dof->perekaman_akun
        ) : 0;

        // Asumsi nilai E-materai dari tabel pengiriman_dokumen
        $totalEMaterai = $pengiriman ? $pengiriman->e_materai : 0;

        // Kembalikan struktur array yang rapi untuk dikonsumsi Controller/API
        return [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'rincian' => [
                'pa_non_teknik_total' => $totalPaNonTeknik,
                'pa_teknik_total' => $totalPaTeknik,
                'dof_total' => $totalDof,
                'e_materai_total' => $totalEMaterai,
            ],
            'grand_total' => $totalPaNonTeknik + $totalPaTeknik + $totalDof + $totalEMaterai
        ];
    }
}