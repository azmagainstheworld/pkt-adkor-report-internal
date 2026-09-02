<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// 1. Force overflow-visible on x-card
$content = str_replace('overflow-visible', '!overflow-visible', $content);

// 2. Force overflow-visible on the form
$content = str_replace('<form id="bulkDeleteFormTerbit"', '<form id="bulkDeleteFormTerbit" class="!overflow-visible"', $content);
$content = str_replace('<form id="bulkDeleteFormProses"', '<form id="bulkDeleteFormProses" class="!overflow-visible"', $content);

// 3. Force overflow-visible on the p-5 header
$content = str_replace('class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 relative z-[60]"', 'class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 relative z-[60] !overflow-visible"', $content);

file_put_contents($path, $content);
echo "Nuclear overflow-visible applied!\n";
?>
