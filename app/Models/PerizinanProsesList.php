<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerizinanProsesList extends Model
{
    use HasFactory;
    use Auditable;

    protected $auditModuleKey = 'perizinan';

    protected $table = 'perizinan_proses_list';
    protected $guarded = [];
}