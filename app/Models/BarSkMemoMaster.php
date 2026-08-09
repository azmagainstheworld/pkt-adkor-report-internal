<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarSkMemoMaster extends Model {
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'bar-sk-memo';

    protected $table = 'bar_sk_memo_master';
    protected $guarded = ['id'];

    public function barSkMemoData() {
        return $this->hasMany(BarSkMemoData::class, 'dokumen_id');
    }
}