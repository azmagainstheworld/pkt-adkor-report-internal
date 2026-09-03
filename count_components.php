<?php
$c = file_get_contents('resources/views/surat-masuk-keluar.blade.php');
preg_match_all('/<x-([a-zA-Z0-9_-]+)/', $c, $open);
preg_match_all('/<\/x-([a-zA-Z0-9_-]+)>/', $c, $close);
print_r(array_count_values($open[1]));
print_r(array_count_values($close[1]));
