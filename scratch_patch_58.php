<?php
$path = 'resources/views/jasakurir.blade.php';
$content = file_get_contents($path);

// Fix the incorrect route name
$content = str_replace("route('jasakurir.destroyBulk')", "route('jasakurir.data.destroyBulk')", $content);

file_put_contents($path, $content);
echo "Route fixed in jasakurir.blade.php!\n";
?>
