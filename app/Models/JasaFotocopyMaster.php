<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class JasaFotocopyMaster extends Model {
    use Auditable;
    protected $auditModuleKey = 'jasa-fotocopy';
    protected $table = 'jasa_fotocopy_masters';
    protected $guarded = ['id'];

    public function dataFotocopy() {
        return $this->hasMany(JasaFotocopyData::class, 'master_id');
    }
}