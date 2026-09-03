<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratRekap extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'surat-rekap';
    protected $table = 'surat_rekaps';

    protected $fillable = [
        'tahun',
        'bulan',
        'surat_masuk',
        'surat_keluar'
    ];
}
