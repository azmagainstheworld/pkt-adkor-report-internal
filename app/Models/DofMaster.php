<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class DofMaster extends Model {
    use Auditable;

    protected $auditModuleKey = 'dof';

    protected $table = 'dof_masters';
    protected $guarded = ['id'];

    public function dataDof() {
        return $this->hasMany(DofData::class, 'master_id');
    }
}