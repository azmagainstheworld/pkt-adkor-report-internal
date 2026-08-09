<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RekapMaster extends Model {
    use Auditable;

    protected $auditModuleKey = 'rekap';

    protected $table = 'rekap_masters';
    protected $guarded = ['id'];

    public function dataRekap() {
        return $this->hasMany(RekapData::class, 'master_id');
    }
}