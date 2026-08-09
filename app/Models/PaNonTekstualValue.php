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
    protected $fillable = ['tahun', 'bulan', 'type_id', 'jumlah'];

    // Relasi balik ke jenis
    public function type()
    {
        return $this->belongsTo(PaNonTekstualType::class, 'type_id');
    }

    // Label yang lebih informatif untuk log audit, misal "Rapat Koordinasi - Agustus 2026"
    public function auditLabel(): string
    {
        $namaJenis = $this->type->name ?? ('Jenis #' . $this->type_id);

        return "{$namaJenis} - {$this->bulan} {$this->tahun}";
    }
}