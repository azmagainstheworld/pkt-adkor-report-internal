<?php
$path = 'resources/views/jasakurir.blade.php';
$content = file_get_contents($path);

// The exact string causing the problem
$problematicString = "<script>\n    // Toggle action dropdown function";
$fixedString = "    // Toggle action dropdown function";

$content = str_replace($problematicString, $fixedString, $content);
// Also try with different newline formats just in case
$content = str_replace("<script>\r\n    // Toggle action dropdown function", $fixedString, $content);

file_put_contents($path, $content);
echo "Nested script tag removed successfully!\n";
?>
