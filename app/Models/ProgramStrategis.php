<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class ProgramStrategis extends Model {
    use Auditable;

    protected $auditModuleKey = 'program-strategis';

    protected $table = 'program_strategis';
    protected $guarded = ['id'];
}