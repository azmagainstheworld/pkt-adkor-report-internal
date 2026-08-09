<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaNonTekstualType extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'pa-non-tekstual';
    protected $auditLabelField = 'name';

    protected $table = 'pa_non_tekstual_types';
    protected $fillable = ['name', 'is_active'];

    // Relasi ke data nilai
    public function values()
    {
        return $this->hasMany(PaNonTekstualValue::class, 'type_id');
    }
}