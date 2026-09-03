<?php
$content = file_get_contents('resources/views/kearsipan-pa-teknik.blade.php');

$formPos = strpos($content, 'id="bulkDeleteForm1"');
$tablePos = strpos($content, 'id="tableContainerBulk1"');
$formEnd = strpos($content, '</form>', $tablePos);

echo 'Form1 pos: ' . $formPos . PHP_EOL;
echo 'Table container pos: ' . $tablePos . PHP_EOL;
echo 'Form end pos: ' . $formEnd . PHP_EOL;
echo 'Form wraps table: ' . ($formPos < $tablePos && $formEnd > $tablePos ? 'YES' : 'NO') . PHP_EOL;
echo PHP_EOL . '=== Context around form end ===' . PHP_EOL;
echo substr($content, $formEnd - 200, 400) . PHP_EOL;
