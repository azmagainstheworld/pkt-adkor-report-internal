<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class DofData extends Model {
    use Auditable;

    protected $auditModuleKey = 'dof';

    protected $table = 'dof_data';
    protected $guarded = ['id'];

    // TRIK JSON ARRAY UNTUK KOLOM DINAMIS
    protected $casts = [
        'data_tambahan' => 'array',
    ];

    public function masterDof() {
        return $this->belongsTo(DofMaster::class, 'master_id');
    }
}