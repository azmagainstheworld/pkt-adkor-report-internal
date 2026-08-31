<?php

namespace App\Models;

use App\Traits\Auditable; // Trait auditable Anda
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use Auditable;

    protected $auditModuleKey = 'surat';
    protected $table = 'surat';
    
    // guarded id agar kolom lain otomatis bisa diisi massal
    protected $guarded = ['id'];

    // Casting array untuk kolom tambahan dinamis dan date (Req 5 & 1)
    protected $casts = [
        'data_tambahan' => 'array',
        'tanggal_surat' => 'date', // Penting untuk formatting di View
    ];
}