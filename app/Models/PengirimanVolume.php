<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PengirimanVolume extends Model
{
    use Auditable;

    protected $auditModuleKey = 'pengiriman-dokumen';

    //
}