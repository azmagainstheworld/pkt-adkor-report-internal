<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanRutinData extends Model {
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'pemeliharaan-rutin';

    protected $table = 'pemeliharaan_rutin_data';
    protected $guarded = ['id'];

    public function pemeliharaanRutinMaster() {
        return $this->belongsTo(PemeliharaanRutinMaster::class, 'rutin_id');
    }
}