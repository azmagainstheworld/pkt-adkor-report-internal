<?php
$f = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/<div style=\"color:red\">DEBUG_PAGINATION.*?<\/div>\n?/s', '', $c);
file_put_contents($f, $c);
echo "Removed debug text.\n";
