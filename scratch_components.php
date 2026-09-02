<?php
$c = file_get_contents('resources/views/undangan.blade.php');
preg_match_all('/<x-[^\s>]+[^>]*>/', $c, $m);
foreach($m[0] as $match) {
    echo $match . "\n";
}
?>
