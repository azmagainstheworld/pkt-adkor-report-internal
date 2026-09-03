<?php

$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

$new_js = <<<JS
        function submitBulkDelete1() {
            let selectAll = document.getElementById("selectAllBulk1");
            if (selectAll && selectAll.checked) {
                document.getElementById('deleteAllFlag1').value = "1";
            } else {
                document.getElementById('deleteAllFlag1').value = "0";
            }
            document.getElementById('bulkDeleteForm1').submit();
        }
        function submitBulkDelete2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll && selectAll.checked) {
                document.getElementById('deleteAllFlag2').value = "1";
            } else {
                document.getElementById('deleteAllFlag2').value = "0";
            }
            document.getElementById('bulkDeleteForm2').submit();
        }
JS;

if (strpos($content, 'function submitBulkDelete1') === false) {
    $content = str_replace('function cancelAll2() {', $new_js . "\n        function cancelAll2() {", $content);
}

file_put_contents($file, $content);
echo "Injected submitBulkDelete scripts.\n";
