<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model {
    use Auditable;

    protected $auditModuleKey = 'surat';

    protected $table = 'surat';
    protected $guarded = ['id'];
}