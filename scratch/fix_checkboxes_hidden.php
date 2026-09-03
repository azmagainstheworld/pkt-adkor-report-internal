<?php

$file = 'resources/views/non-teknik-non-tekstual.blade.php';
$content = file_get_contents($file);

$content = str_replace('class="bulk-cb-header hidden"', 'class="bulk-cb-header"', $content);
$content = str_replace('class="cb-bulk hidden w-4 h-4', 'class="cb-bulk w-4 h-4', $content);

file_put_contents($file, $content);
echo "Fixed hidden classes on checkboxes.\n";
