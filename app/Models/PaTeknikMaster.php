<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PaTeknikMaster extends Model {
    use Auditable;

    protected $auditModuleKey = 'pa-teknik';

    protected $table = 'pa_teknik_masters';
    protected $guarded = ['id'];

    public function dataTeknik() {
        return $this->hasMany(PaTeknikData::class, 'master_id');
    }
}