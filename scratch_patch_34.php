<?php
$path = 'app/Http/Controllers/PerizinanPerkantoranController.php';
$content = file_get_contents($path);

// Fix the validation rule for the table name
$content = str_replace("exists:perizinan_proses_lists,id", "exists:perizinan_proses_list,id", $content);

file_put_contents($path, $content);
echo "Fixed validation rule for destroyBulkProses\n";
?>
