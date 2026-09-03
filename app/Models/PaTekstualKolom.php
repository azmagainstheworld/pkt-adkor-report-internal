<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PaTekstualKolom extends Model
{
    use Auditable;
    protected $auditModuleKey = 'pa-tekstual';
    
    protected $table = 'pa_tekstual_kolom';
    protected $guarded = ['id'];
}
