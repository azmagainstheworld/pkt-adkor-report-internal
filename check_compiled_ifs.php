<?php
$c = file_get_contents('compiled.php');
$lines = explode("\n", $c);
$if_count = 0;
$endif_count = 0;
foreach ($lines as $i => $l) {
    if (strpos($l, '<?php if') !== false) {
        $if_count++;
        echo "+ if at line " . ($i+1) . ": " . trim($l) . "\n";
    }
    if (strpos($l, '<?php endif') !== false) {
        $endif_count++;
        echo "- endif at line " . ($i+1) . ": " . trim($l) . "\n";
    }
}
echo "if: $if_count, endif: $endif_count\n";
