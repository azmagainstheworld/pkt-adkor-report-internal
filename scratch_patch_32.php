<?php
$path = 'scratch_bulk_perizinan.php';
$content = file_get_contents($path);

// Replace the line that appends JS
$pattern = '/\$content = preg_replace\(\'\/\\(<script>\\\\s\*function openModal\\)\/s\', \$js \. "\\\\n\$1", \$content, 1\);/';
$replacement = '$content = preg_replace(\'/(<script>\s*function openModal)/s\', $js . "\n</script>\n$1", $content, 1);';

// I will just str_replace it because preg_replace of a string literal is annoying in PHP.
$lines = file($path);
foreach ($lines as &$line) {
    if (strpos($line, 'function openModal') !== false && strpos($line, 'preg_replace') !== false) {
        $line = '    $content = preg_replace(\'/(<script>\s*function openModal)/s\', $js . "\n</script>\n$1", $content, 1);' . "\n";
    }
}
$content = implode("", $lines);

file_put_contents($path, $content);
echo "Fixed javascript insertion in scratch_bulk_perizinan.php\n";
?>
