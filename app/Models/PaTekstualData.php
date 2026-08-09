<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PaTekstualData extends Model {
    use Auditable;

    protected $auditModuleKey = 'pa-tekstual';

    protected $table = 'pa_tekstual_data';
    protected $guarded = ['id'];

    public function masterTekstual() {
        return $this->belongsTo(PaTekstualMaster::class, 'master_id');
    }
}