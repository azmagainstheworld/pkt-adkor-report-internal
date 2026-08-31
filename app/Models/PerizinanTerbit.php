<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PerizinanTerbit extends Model
{
    use Auditable;

    protected $auditModuleKey = 'perizinan';

    protected $table = 'perizinan_terbit';
    protected $guarded = ['id'];
    
    // Tambahkan array data_tambahan ke dalam casts
    protected $casts = [
        'tanggal_sejak' => 'date',
        'tanggal_akhir' => 'date',
        'data_tambahan' => 'array', // Trik sakti JSON
    ];
}