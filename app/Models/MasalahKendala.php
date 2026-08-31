<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasalahKendala extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'masalah-kendala';

    protected $table = 'masalah_kendala';
    protected $guarded = ['id'];

    // Trik sakti agar JSON otomatis jadi Array di PHP
    protected $casts = [
        'data_tambahan' => 'array',
    ];
}