<?php
$c = file_get_contents('resources/views/surat-masuk-keluar.blade.php');
echo '{{ : ' . substr_count($c, '{{') . PHP_EOL;
echo '}} : ' . substr_count($c, '}}') . PHP_EOL;
echo '{!! : ' . substr_count($c, '{!!') . PHP_EOL;
echo '!!} : ' . substr_count($c, '!!}') . PHP_EOL;
