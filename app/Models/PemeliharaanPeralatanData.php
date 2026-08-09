<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanPeralatanData extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'pemeliharaan-peralatan';

    protected $table = 'pemeliharaan_peralatan_data';
    protected $guarded = ['id'];

    // Relasi balik ke tabel master
    public function pemeliharaanPeralatanMaster()
    {
        return $this->belongsTo(PemeliharaanPeralatanMaster::class, 'peralatan_id');
    }
}