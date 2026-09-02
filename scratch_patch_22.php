<?php

$path = 'app/Traits/Auditable.php';
$content = file_get_contents($path);

$oldCode = <<<PHP
    public function writeAuditLog(string \$action, ?string \$field, \$oldValue, \$newValue): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'module_key' => \$this->auditModuleKey ?? \$this->getTable(),
            'table_name' => \$this->getTable(),
            'record_id' => \$this->getKey(),
            'action' => \$action,
            'field_name' => \$field,
            'old_value' => \$oldValue !== null ? (string) \$oldValue : null,
            'new_value' => \$newValue !== null ? (string) \$newValue : null,
        ]);
    }
PHP;

$newCode = <<<PHP
    public function writeAuditLog(string \$action, ?string \$field, \$oldValue, \$newValue): void
    {
        \$oldValueStr = \$oldValue !== null ? (is_array(\$oldValue) || is_object(\$oldValue) ? json_encode(\$oldValue) : (string) \$oldValue) : null;
        \$newValueStr = \$newValue !== null ? (is_array(\$newValue) || is_object(\$newValue) ? json_encode(\$newValue) : (string) \$newValue) : null;

        AuditLog::create([
            'user_id' => Auth::id(),
            'module_key' => \$this->auditModuleKey ?? \$this->getTable(),
            'table_name' => \$this->getTable(),
            'record_id' => \$this->getKey(),
            'action' => \$action,
            'field_name' => \$field,
            'old_value' => \$oldValueStr,
            'new_value' => \$newValueStr,
        ]);
    }
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($path, $content);
echo "Fixed Array to string conversion in Auditable trait\n";

?>
