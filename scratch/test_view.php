<?php
$f = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$c = file_get_contents($f);
if (strpos($c, 'DEBUG_PAGINATION') === false) {
    $c = str_replace('<x-table :headers="$headTabel1">', "<div style=\"color:red\">DEBUG_PAGINATION: {{ count(\$paginatedTable1) }} items</div>\n<x-table :headers=\"\$headTabel1\">", $c);
    file_put_contents($f, $c);
}
