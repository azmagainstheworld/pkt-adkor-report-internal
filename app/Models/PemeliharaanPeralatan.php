<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanPeralatan extends Model {
    use Auditable;

    protected $auditModuleKey = 'pemeliharaan-peralatan';

    protected $table = 'pemeliharaan_peralatan';
    protected $guarded = ['id'];
}