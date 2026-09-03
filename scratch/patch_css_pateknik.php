<?php

$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

$target_css = '.hide-bulk .cb-bulk, .hide-bulk .bulk-cb-header { display: none !important; }';
$replacement_css = '.hide-bulk .cb-bulk, .hide-bulk .bulk-cb-header, .hide-bulk .cb-bulk-1, .hide-bulk .cb-bulk-2, .hide-bulk .bulk-cb-header-1, .hide-bulk .bulk-cb-header-2 { display: none !important; }';
$content = str_replace($target_css, $replacement_css, $content);

$target_head1 = '[\'<input type="checkbox" id="selectAllBulk1" onclick="toggleSelectAll1()">\', \'Tahun\', \'Bulan\']';
$replacement_head1 = '[\'<input type="checkbox" id="selectAllBulk1" class="bulk-cb-header-1" onclick="toggleSelectAll1()">\', \'Tahun\', \'Bulan\']';
$content = str_replace($target_head1, $replacement_head1, $content);

$target_head2 = '[\'<input type="checkbox" id="selectAllBulk2" onclick="toggleSelectAll2()">\', \'Tahun\', \'Bulan\']';
$replacement_head2 = '[\'<input type="checkbox" id="selectAllBulk2" class="bulk-cb-header-2" onclick="toggleSelectAll2()">\', \'Tahun\', \'Bulan\']';
$content = str_replace($target_head2, $replacement_head2, $content);

file_put_contents($file, $content);
echo "CSS and headers patched.\n";
