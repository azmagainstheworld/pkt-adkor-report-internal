<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\components\table.blade.php';
$content = file_get_contents($file);
$content = str_replace('{{ $header }}', '{!! $header !!}', $content);
file_put_contents($file, $content);
echo "Component table updated";
