<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class JasaKurirData extends Model
{
    use Auditable;

    protected $auditModuleKey = 'jasa-kurir';

    protected $table = 'jasa_kurir_data';
    protected $guarded = ['id'];

    // TRIK JSON ARRAY UNTUK KOLOM DINAMIS
    protected $casts = [
        'data_tambahan' => 'array',
    ];

    public function jasaKurirMaster()
    {
        return $this->belongsTo(JasaKurirMaster::class, 'jasa_kurir_id');
    }
}