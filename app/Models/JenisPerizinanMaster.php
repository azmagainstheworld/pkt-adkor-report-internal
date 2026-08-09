<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class JenisPerizinanMaster extends Model
{
    use Auditable;

    protected $auditModuleKey = 'perizinan';
    protected $auditLabelField = 'nama';

    protected $table = 'jenis_perizinan_master';
    protected $guarded = ['id'];

    public function perizinanTerbit()
    {
        return $this->hasMany(PerizinanTerbit::class, 'jenis_perizinan_id');
    }
}