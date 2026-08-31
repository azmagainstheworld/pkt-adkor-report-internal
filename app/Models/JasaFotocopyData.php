<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class JasaFotocopyData extends Model {
    use Auditable;
    protected $auditModuleKey = 'jasa-fotocopy';
    protected $table = 'jasa_fotocopy_data';
    protected $guarded = ['id'];
    protected $casts = ['data_tambahan' => 'array'];

    public function masterFotocopy() {
        return $this->belongsTo(JasaFotocopyMaster::class, 'master_id');
    }
}