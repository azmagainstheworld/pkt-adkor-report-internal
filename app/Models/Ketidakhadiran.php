<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ketidakhadiran extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'ketidakhadiran';

    protected $table = 'ketidakhadiran';

    protected $fillable = [
        'karyawan_id',
        'tahun',
        'bulan',
        'keterangan',
        'dinas',
        'cuti',
        'izin',
        'training',
        'dispensasi',
        'detasering',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Label yang lebih informatif untuk log audit, misal "Ahmad Rizki - Agustus 2026"
    // daripada cuma "Ketidakhadiran #12"
    public function auditLabel(): string
    {
        $namaKaryawan = $this->karyawan->nama ?? ('Karyawan #' . $this->karyawan_id);

        return "{$namaKaryawan} - {$this->bulan} {$this->tahun}";
    }

    /**
     * Daftar nama kolom kategori angka (dipakai berulang di controller & view).
     */
    public static function kategoriList(): array
    {
        return ['dinas', 'cuti', 'izin', 'training', 'dispensasi', 'detasering'];
    }

    public static function bulanList(): array
    {
        return [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
    }
}