<?php
$c = file_get_contents('resources/views/undangan.blade.php');
$c = str_replace('@forelse($detailsData ?? [] as $index => $detail)', '@php $safeDetails = $detailsData ?? []; @endphp'."\n".'                @forelse($safeDetails as $index => $detail)', $c);
file_put_contents('resources/views/undangan.blade.php', $c);
echo "Patched forelse\n";
?>
