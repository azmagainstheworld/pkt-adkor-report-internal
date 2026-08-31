<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PengirimanVolume extends Model
{
    use Auditable;

    protected $auditModuleKey = 'pengiriman-dokumen';
    protected $table = 'pengiriman_volumes';

    // WAJIB ADA AGAR DATA EXCEL TIDAK DITOLAK LARAVEL
    protected $fillable = [
        'tahun',
        'bulan',
        'volume_mailroom',
        'volume_domestik',
        'volume_internasional',
        'volume_dof',
    ];
}