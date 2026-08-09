<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class BarSkMemoRapat extends Model {
    use Auditable;

    protected $auditModuleKey = 'bar-sk-memo';

    protected $table = 'bar_sk_memo_rapat';
    protected $guarded = ['id'];
}