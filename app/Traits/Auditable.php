<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Tempelkan trait ini ke model manapun yang perubahannya ingin dicatat otomatis
 * ke tabel audit_logs (create / update / delete), tanpa perlu menulis kode
 * logging manual di controller.
 *
 * Cara pakai di model:
 *
 *   use App\Traits\Auditable;
 *
 *   class AnggaranAdministrasi extends Model
 *   {
 *       use Auditable;
 *
 *       // Kunci modul yang tampil di filter "Modul" pada Log Audit (opsional,
 *       // kalau tidak diisi otomatis pakai nama tabel)
 *       protected $auditModuleKey = 'anggaran';
 *
 *       // Field mana yang dipakai sebagai "nama" record saat aksi create/delete (opsional)
 *       protected $auditLabelField = 'detail_anggaran';
 *
 *       // Field yang TIDAK perlu dicatat perubahannya (opsional)
 *       protected $auditExcluded = ['updated_at'];
 *
 *       // Field sensitif yang nilainya disamarkan di log (opsional)
 *       protected $auditHidden = ['password'];
 *   }
 */
trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            $model->writeAuditLog('create', null, null, $model->auditLabel());
        });

        static::updated(function ($model) {
            $excluded = array_merge(['created_at', 'updated_at'], $model->auditExcluded ?? []);
            $hidden = $model->auditHidden ?? [];

            foreach ($model->getChanges() as $field => $newValue) {
                if (in_array($field, $excluded, true)) {
                    continue;
                }

                $oldValue = $model->getOriginal($field);

                if (in_array($field, $hidden, true)) {
                    $oldValue = $oldValue !== null ? '[disembunyikan]' : null;
                    $newValue = '[disembunyikan]';
                }

                $model->writeAuditLog('update', $field, $oldValue, $newValue);
            }
        });

        static::deleted(function ($model) {
            $model->writeAuditLog('delete', null, $model->auditLabel(), null);
        });
    }

    public function writeAuditLog(string $action, ?string $field, $oldValue, $newValue): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'module_key' => $this->auditModuleKey ?? $this->getTable(),
            'table_name' => $this->getTable(),
            'record_id' => $this->getKey(),
            'action' => $action,
            'field_name' => $field,
            'old_value' => $oldValue !== null ? (string) $oldValue : null,
            'new_value' => $newValue !== null ? (string) $newValue : null,
        ]);
    }

    // Label manusiawi untuk record ini, ditulis ke kolom new_value/old_value
    // saat aksi create/delete. Override method ini di model kalau butuh format khusus.
    public function auditLabel(): string
    {
        $field = $this->auditLabelField ?? null;

        if ($field && isset($this->{$field})) {
            return (string) $this->{$field};
        }

        return class_basename($this) . ' #' . $this->getKey();
    }
}