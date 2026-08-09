<?php
// app/Models/PerizinanProses.php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PerizinanProses extends Model 
{
    use Auditable;

    protected $auditModuleKey = 'perizinan';

    protected $table = 'perizinan_proses';
    
    // Kolom sesuai dengan database
    protected $fillable = [
        'tahun', 
        'bulan', 
        'jumlah_proses'
    ];

    // Label yang lebih informatif untuk log audit, misal "Proses Perizinan - Agustus 2026"
    public function auditLabel(): string
    {
        return "Proses Perizinan - {$this->bulan} {$this->tahun}";
    }
}