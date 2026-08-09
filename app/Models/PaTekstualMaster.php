<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PaTekstualMaster extends Model {
    use Auditable;

    protected $auditModuleKey = 'pa-tekstual';

    protected $table = 'pa_tekstual_master';
    protected $guarded = ['id'];

    public function dataTekstual() {
        return $this->hasMany(PaTekstualData::class, 'master_id');
    }
}