<?php
$c = file_get_contents('resources/views/undangan.blade.php');
preg_match_all('/@(if|else|elseif|endif|foreach|endforeach|forelse|empty|endforelse|php|endphp|auth|endauth|guest|endguest)\b/', $c, $m, PREG_OFFSET_CAPTURE);
foreach($m[0] as $match) {
    $line = substr_count(substr($c, 0, $match[1]), "\n") + 1;
    echo str_pad($line, 4, ' ', STR_PAD_LEFT) . ': ' . $match[0] . "\n";
}
?>
