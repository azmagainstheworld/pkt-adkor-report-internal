<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Undangan extends Model {
    use Auditable;

    protected $auditModuleKey = 'undangan';

    protected $table = 'undangan';
    protected $guarded = ['id'];

    // TRIK JSON ARRAY UNTUK KOLOM DINAMIS
    protected $casts = [
        'data_tambahan' => 'array',
    ];
}