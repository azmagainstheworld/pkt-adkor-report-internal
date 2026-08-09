<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanRutin extends Model {
    use Auditable;

    protected $auditModuleKey = 'pemeliharaan-rutin';

    protected $table = 'pemeliharaan_rutin';
    protected $guarded = ['id'];
}