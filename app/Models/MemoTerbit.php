<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class MemoTerbit extends Model {
    use Auditable;

    protected $auditModuleKey = 'memo';

    protected $table = 'memo_terbit';
    protected $guarded = ['id'];
}