<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarSkMemoRapat extends Model
{
    use HasFactory;
    use Auditable; // Trait ini digunakan untuk pencatatan audit log jika ada

    // Tetapkan kunci modul untuk audit log agar sesuai dengan Controller
    protected $auditModuleKey = 'bar-sk-memo';

    // Secara eksplisit tetapkan nama tabel ke 'bar_sk_memo_rapat'
    // agar Eloquent tidak mencari 'bar_sk_memo_rapats'
    protected $table = 'bar_sk_memo_rapat';

    // Properti yang tidak boleh diisi secara massal
    protected $guarded = ['id'];
}