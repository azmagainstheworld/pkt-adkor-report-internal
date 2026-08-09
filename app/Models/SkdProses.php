<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkdProses extends Model {
    use Auditable;

    protected $auditModuleKey = 'skd';

    protected $table = 'skd_proses';
    protected $guarded = ['id'];
}