<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class ProgramStrategis extends Model {
    use Auditable;

    protected $auditModuleKey = 'program-strategis';

    protected $table = 'program_strategis';
    protected $guarded = ['id'];

    // TRIK JSON ARRAY UNTUK KOLOM DINAMIS & CAST TANGGAL
    protected $casts = [
        'data_tambahan' => 'array',
    ];
}