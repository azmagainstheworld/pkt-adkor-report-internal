<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaNonTekstualValue extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'pa-non-tekstual';

    protected $table = 'pa_non_tekstual_values';
    protected $fillable = ['tahun', 'bulan', 'type_id', 'jumlah', 'data_tambahan']; // Pastikan data_tambahan ada di fillable

    // TRIK JSON ARRAY UNTUK KOLOM DINAMIS
    protected $casts = [
        'data_tambahan' => 'array',
    ];

    public function type()
    {
        return $this->belongsTo(PaNonTekstualType::class, 'type_id');
    }

    public function auditLabel(): string
    {
        $namaJenis = $this->type->name ?? ('Jenis #' . $this->type_id);
        return "{$namaJenis} - {$this->bulan} {$this->tahun}";
    }
}