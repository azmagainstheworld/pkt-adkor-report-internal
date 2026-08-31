<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class AnggaranAdministrasi extends Model
{
    use Auditable;

    protected $auditModuleKey = 'anggaran';
    protected $auditLabelField = 'detail_anggaran';

    protected $table = 'anggaran_administrasi';

    protected $fillable = [
        'tahun', 'bulan', 'kategori', 'detail_anggaran', 'rkap', 'komitmen', 'realisasi', 'keterangan', 'data_tambahan'
    ];

    protected $casts = [
        'data_tambahan' => 'array', // Trik Sakti JSON
    ];
}