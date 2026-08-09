<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PengirimanCost extends Model
{
    use Auditable;

    protected $auditModuleKey = 'pengiriman-dokumen';

    //
}