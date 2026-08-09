<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Undangan extends Model {
    use Auditable;

    protected $auditModuleKey = 'undangan';

    protected $table = 'undangan';
    protected $guarded = ['id'];
}