<?php
$lines = file('scratch_compiled.php');
for($i = 260; $i <= 300; $i++) {
    echo str_pad($i+1, 4, ' ', STR_PAD_LEFT) . ': ' . $lines[$i];
}
?>
