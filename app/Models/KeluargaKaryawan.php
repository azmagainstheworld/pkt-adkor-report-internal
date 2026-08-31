<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeluargaKaryawan extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'karyawan';
    protected $auditLabelField = 'nama';

    protected $table = 'keluarga_karyawan';

    protected $fillable = [
        'karyawan_id',
        'nama',
        'hubungan',
        'tempat_lahir',
        'tanggal_lahir',
        'data_tambahan', // <-- TAMBAHKAN INI
    ];

    protected $casts = [
        'data_tambahan' => 'array', // <-- TAMBAHKAN INI
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}