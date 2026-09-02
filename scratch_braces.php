<?php
$c = file_get_contents('resources/views/undangan.blade.php');
echo '{{ count: ' . substr_count($c, '{{') . "\n";
echo '}} count: ' . substr_count($c, '}}') . "\n";
echo '{!! count: ' . substr_count($c, '{!!') . "\n";
echo '!!} count: ' . substr_count($c, '!!}') . "\n";
?>
