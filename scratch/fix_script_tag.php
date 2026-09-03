<?php
$file = 'resources/views/non-teknik-non-tekstual.blade.php';
$content = file_get_contents($file);

$target = <<<EOF
<script>
    function openModal(id)
EOF;
$replacement = <<<EOF
    function openModal(id)
EOF;
$content = str_replace($target, $replacement, $content);

// Let's also use regex just in case there are spaces
$content = preg_replace('/<script>\s+function openModal\(id\)/', '    function openModal(id)', $content);

file_put_contents($file, $content);
echo "Fixed script tag.\n";
