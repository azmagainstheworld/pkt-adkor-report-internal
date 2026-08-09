<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanRutinMaster extends Model {
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'pemeliharaan-rutin';

    protected $table = 'pemeliharaan_rutin_master';
    protected $guarded = ['id'];

    public function pemeliharaanRutinData() {
        return $this->hasMany(PemeliharaanRutinData::class, 'rutin_id');
    }
}