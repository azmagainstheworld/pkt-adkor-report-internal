<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarSkMemoData extends Model {
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'bar-sk-memo';

    protected $table = 'bar_sk_memo_data';
    protected $guarded = ['id'];

    public function barSkMemoMaster() {
        return $this->belongsTo(BarSkMemoMaster::class, 'dokumen_id');
    }
}