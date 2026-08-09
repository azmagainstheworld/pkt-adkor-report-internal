<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class MemoProses extends Model {
    use Auditable;

    protected $auditModuleKey = 'memo';

    protected $table = 'memo_proses';
    protected $guarded = ['id'];
}