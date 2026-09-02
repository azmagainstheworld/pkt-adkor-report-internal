<?php

$path = 'resources/views/ketidakhadiran/index.blade.php';
$content = file_get_contents($path);

// Use preg_replace to wrap the entire "Kategori 2: Atur Kolom" section
$regex = '/(<!-- Kategori 2: Atur Kolom -->.*?Atur Kolom\s*<\/button>\s*<\/div>)/s';
$replacement = "@if(auth()->check() && auth()->user()->isAdmin())\n$1\n@endif";

if (strpos($content, '@if(auth()->check() && auth()->user()->isAdmin())') === false || !preg_match('/@if\(auth\(\)->check\(\) && auth\(\)->user\(\)->isAdmin\(\)\)\s*<!-- Kategori 2: Atur Kolom -->/s', $content)) {
    $content = preg_replace($regex, $replacement, $content);
    file_put_contents($path, $content);
    echo "Wrapped Atur Kolom successfully!\n";
} else {
    echo "Already wrapped.\n";
}
?>
