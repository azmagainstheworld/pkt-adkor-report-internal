<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Karyawan extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $auditModuleKey = 'karyawan';
    protected $auditLabelField = 'nama';

    protected $table = 'karyawan';

    protected $fillable = [
        'nama',
        'npk',
        'tempat_lahir',
        'tanggal_lahir',
        'no_ptk',
        'no_hp',
        'foto',
        'gol_grade',
        'mpp_pbp',
        'keterangan',
        'ket_pensiun',
        'alamat',
        'ukuran_kaos',
        'status',
        'data_tambahan', // <-- TAMBAHKAN INI AGAR BISA DISIMPAN
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'mpp_pbp' => 'date',
        'data_tambahan' => 'array', // <-- TAMBAHKAN INI AGAR OTOMATIS JADI ARRAY
    ];

    protected $appends = [];

    public function keluarga()
    {
        return $this->hasMany(KeluargaKaryawan::class, 'karyawan_id');
    }

    public function ketidakhadiran()
    {
        return $this->hasMany(Ketidakhadiran::class, 'karyawan_id');
    }
}