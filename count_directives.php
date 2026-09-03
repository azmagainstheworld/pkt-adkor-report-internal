<?php
$c = file_get_contents('resources/views/surat-masuk-keluar.blade.php');
preg_match_all('/@(if|foreach|forelse|section|php)\b/', $c, $m);
$opens = $m[1];
preg_match_all('/@(endif|endforeach|endforelse|endsection|endphp)\b/', $c, $m);
$closes = $m[1];
print_r(array_count_values($opens));
print_r(array_count_values($closes));
