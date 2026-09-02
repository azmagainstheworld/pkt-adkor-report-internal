<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UndanganDetail extends Model
{
    protected $fillable = [
        'undangan_id',
        'jenis_undangan',
        'agenda',
    ];

    public function undangan()
    {
        return $this->belongsTo(Undangan::class, 'undangan_id');
    }
}
