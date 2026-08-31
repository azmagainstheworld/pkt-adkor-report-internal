<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Pelaporan extends Model {
    use Auditable;

    protected $auditModuleKey = 'pelaporan';

    protected $table = 'pelaporan';
    protected $guarded = ['id'];
    
    // Trik sakti agar JSON otomatis jadi Array
    protected $casts = [
        'data_tambahan' => 'array',
    ];
}