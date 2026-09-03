<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PengirimanDokumen extends Model
{
    use Auditable;

    protected $auditModuleKey = 'pengiriman-dokumen';
    protected $table = 'pengiriman_dokumen';

    protected $fillable = [
        'tahun',
        'bulan',
        'penerimaan_mailroom',
        'registrasi_surat_masuk_dof',
        'pengiriman_dalam_negeri',
        'pengiriman_luar_negeri',
        'e_materai',
        'data_tambahan'
    ];

    protected $casts = [
        'data_tambahan' => 'array',
    ];
}