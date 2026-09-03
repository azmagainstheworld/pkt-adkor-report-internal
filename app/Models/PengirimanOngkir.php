<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PengirimanOngkir extends Model
{
    use Auditable;

    protected $auditModuleKey = 'pengiriman-ongkir';
    protected $table = 'pengiriman_ongkir';

    protected $fillable = [
        'tahun',
        'bulan',
        'ongkir_dalam_negeri',
        'ongkir_luar_negeri',
        'data_tambahan'
    ];

    protected $casts = [
        'data_tambahan' => 'array',
    ];
}
