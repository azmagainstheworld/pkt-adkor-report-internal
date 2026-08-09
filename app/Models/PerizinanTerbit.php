<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PerizinanTerbit extends Model
{
    use Auditable;

    protected $auditModuleKey = 'perizinan';

    protected $table = 'perizinan_terbit';
    protected $guarded = ['id'];
    protected $casts = [
        'tanggal_sejak' => 'date',
        'tanggal_akhir' => 'date',
    ];
    // Fungsi public function jenisPerizinan() DIHAPUS saja karena sudah tidak relasi
}