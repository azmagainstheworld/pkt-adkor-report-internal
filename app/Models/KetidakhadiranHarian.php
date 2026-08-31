<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetidakhadiranHarian extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'ketidakhadiran';

    protected $table = 'ketidakhadiran_harian';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jenis',
        'keterangan',
        'data_tambahan', // Tambahan Kolom JSON
    ];

    protected $casts = [
        'tanggal' => 'date',
        'data_tambahan' => 'array', // Trik Sakti JSON
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function auditLabel(): string
    {
        $namaKaryawan = $this->karyawan->nama ?? ('Karyawan #' . $this->karyawan_id);
        $tanggal = $this->tanggal ? $this->tanggal->format('Y-m-d') : '-';
        return "{$namaKaryawan} - {$tanggal}";
    }
}