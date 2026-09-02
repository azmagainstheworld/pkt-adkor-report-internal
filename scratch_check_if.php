<?php
$lines = file('resources/views/undangan.blade.php');
$stack = [];
foreach ($lines as $i => $l) {
    if (preg_match('/@if\b/', $l)) {
        $stack[] = ['type' => 'if', 'line' => $i+1];
    }
    if (preg_match('/@endif/', $l)) {
        if (empty($stack)) {
            echo 'Unmatched @endif at line ' . ($i+1) . "\n";
        } else {
            array_pop($stack);
        }
    }
}
if (!empty($stack)) {
    echo "Unmatched @if blocks:\n";
    print_r($stack);
}
echo "Done checking.\n";
?>
