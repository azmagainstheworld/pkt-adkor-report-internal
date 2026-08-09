<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class DofData extends Model {
    use Auditable;

    protected $auditModuleKey = 'dof';

    protected $table = 'dof_data';
    protected $guarded = ['id'];

    public function masterDof() {
        return $this->belongsTo(DofMaster::class, 'master_id');
    }
}