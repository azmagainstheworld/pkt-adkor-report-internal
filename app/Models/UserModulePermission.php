<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class UserModulePermission extends Model
{
    use Auditable;

    protected $auditModuleKey = 'user-permission';

    protected $table = 'user_module_permissions';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}