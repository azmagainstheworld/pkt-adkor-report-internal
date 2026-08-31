<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class JasaFotocopy extends Model {
    use Auditable;
    protected $auditModuleKey = 'jasa-fotocopy';
    protected $table = 'jasa_fotocopy';
    protected $guarded = ['id'];
    protected $casts = ['data_tambahan' => 'array'];
}