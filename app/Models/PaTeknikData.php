<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PaTeknikData extends Model {
    use Auditable;

    protected $auditModuleKey = 'pa-teknik';

    protected $table = 'pa_teknik_data';
    protected $guarded = ['id'];

    public function masterTeknik() {
        return $this->belongsTo(PaTeknikMaster::class, 'master_id');
    }
}