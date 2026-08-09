<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanPeralatanMaster extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'pemeliharaan-peralatan';

    protected $table = 'pemeliharaan_peralatan_master';
    protected $guarded = ['id'];

    // Relasi ke tabel data peralatan
    public function pemeliharaanPeralatanData()
    {
        return $this->hasMany(PemeliharaanPeralatanData::class, 'peralatan_id');
    }
}