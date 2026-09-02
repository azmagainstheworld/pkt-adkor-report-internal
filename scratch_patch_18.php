<?php

$path = 'resources/views/anggaran/index.blade.php';
$content = file_get_contents($path);

// 1. Remove the literal <\/script>
$content = str_replace("<\/script>\n<script>\nlet isBulkMode = false;", "let isBulkMode = false;", $content);

// 2. Remove empty script tags
$content = preg_replace('/<script>\s*<\/script>/', '', $content);

file_put_contents($path, $content);
echo "Fixed JS Syntax Error in Anggaran\n";
?>
