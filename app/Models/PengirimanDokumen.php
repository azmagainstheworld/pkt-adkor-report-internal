<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PengirimanDokumen extends Model
{
    use Auditable;

    protected $auditModuleKey = 'pengiriman-dokumen';

    // Pastikan nama tabel sesuai dengan phpMyAdmin Gambar 1
    protected $table = 'pengiriman_dokumen';

    // Daftarkan semua kolom yang bisa diisi dari form (mass assignment)
    // Sesuaikan dengan nama kolom tepat seperti di phpMyAdmin Gambar 1 combined migration
    protected $fillable = [
        'tahun',
        'bulan',
        // Kolom Volume (Jumlah Dokumen)
        'penerimaan_mailroom',
        'registrasi_surat_masuk_dof',
        'pengiriman_dalam_negeri',
        'pengiriman_luar_negeri',
        // Kolom Cost (Ongkir Rupiah)
        'ongkir_dalam_negeri',
        'ongkir_luar_negeri',
        'e_materai'
    ];

    // Label yang lebih informatif untuk log audit, misal "Pengiriman Dokumen - Agustus 2026"
    public function auditLabel(): string
    {
        return "Pengiriman Dokumen - {$this->bulan} {$this->tahun}";
    }
}