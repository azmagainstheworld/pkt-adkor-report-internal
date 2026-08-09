<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkdTerbit extends Model {
    use Auditable;

    protected $auditModuleKey = 'skd';

    protected $table = 'skd_terbit';
    protected $guarded = ['id'];
}