<?php
$c = file_get_contents('resources/views/perizinan-perkantoran.blade.php');
echo "if: " . substr_count($c, '@if') . " endif: " . substr_count($c, '@endif') . "\n";
echo "forelse: " . substr_count($c, '@forelse') . " empty: " . substr_count($c, '@empty') . " endforelse: " . substr_count($c, '@endforelse') . "\n";
echo "foreach: " . substr_count($c, '@foreach') . " endforeach: " . substr_count($c, '@endforeach') . "\n";
