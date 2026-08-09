<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class JasaKurirMaster extends Model
{
    use Auditable;

    protected $auditModuleKey = 'jasa-kurir';
    protected $auditLabelField = 'nama';

    protected $table = 'jasa_kurir_master';
    protected $guarded = ['id'];

    public function jasaKurirData()
    {
        return $this->hasMany(JasaKurirData::class, 'jasa_kurir_id');
    }
}