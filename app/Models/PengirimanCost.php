<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PengirimanCost extends Model
{
    use Auditable;

    protected $auditModuleKey = 'pengiriman-dokumen';
    protected $table = 'pengiriman_costs';

    // WAJIB ADA AGAR DATA EXCEL TIDAK DITOLAK LARAVEL
    protected $fillable = [
        'tahun',
        'bulan',
        'cost_domestik',
        'cost_internasional',
    ];
}