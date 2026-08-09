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
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Label yang lebih informatif untuk log audit, misal "Ahmad Rizki - 2026-08-07"
    public function auditLabel(): string
    {
        $namaKaryawan = $this->karyawan->nama ?? ('Karyawan #' . $this->karyawan_id);
        $tanggal = $this->tanggal ? $this->tanggal->format('Y-m-d') : '-';

        return "{$namaKaryawan} - {$tanggal}";
    }
}