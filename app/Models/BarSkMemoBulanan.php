<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class BarSkMemoBulanan extends Model {
    use Auditable;

    protected $auditModuleKey = 'bar-sk-memo';

    protected $table = 'bar_sk_memo_bulanan';
    protected $guarded = ['id'];
}