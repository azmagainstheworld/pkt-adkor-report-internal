<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PaTekstualData extends Model {
    use Auditable;

    protected $auditModuleKey = 'pa-tekstual';

    protected $table = 'pa_tekstual_data';
    protected $guarded = ['id'];

    // Sama seperti PemeliharaanRutinData — supaya kolom JSON data_tambahan
    // otomatis diterjemahkan Laravel jadi array PHP, bukan string JSON mentah.
    protected $casts = [
        'data_tambahan' => 'array',
    ];

    public function masterTekstual() {
        return $this->belongsTo(PaTekstualMaster::class, 'master_id');
    }
}