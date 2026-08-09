<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RekapData extends Model {
    use Auditable;

    protected $auditModuleKey = 'rekap';

    protected $table = 'rekap_data';
    protected $guarded = ['id'];

    public function masterRekap() {
        return $this->belongsTo(RekapMaster::class, 'master_id');
    }
}