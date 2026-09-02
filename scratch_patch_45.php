<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// Revert z-[60] to z-20
$content = str_replace('relative z-[60] !overflow-visible', 'relative z-20', $content);
$content = str_replace('relative z-[60]', 'relative z-20', $content);

// Revert !overflow-visible on form
$content = str_replace('<form id="bulkDeleteFormTerbit" class="!overflow-visible"', '<form id="bulkDeleteFormTerbit"', $content);
$content = str_replace('<form id="bulkDeleteFormProses" class="!overflow-visible"', '<form id="bulkDeleteFormProses"', $content);

// Revert !overflow-visible on x-card to overflow-visible
$content = str_replace('!overflow-visible', 'overflow-visible', $content);

file_put_contents($path, $content);
echo "Reverted all my nuclear CSS hacks!\n";
?>
