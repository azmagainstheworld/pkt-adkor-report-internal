<?php
$bladePath = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($bladePath);

$pattern = '/<script>\s*\/\/ TABEL 2 \(TERBIT\).*?if \(totalRows > 0\) renderPage\(1\);\s*\}\)\(\);\s*<\/script>/s';

preg_match($pattern, $content, $matches);
$goodBlock = isset($matches[0]) ? trim($matches[0]) : '';

if ($goodBlock) {
    // Remove all copies
    $content = preg_replace($pattern, '', $content);
    
    // Append exactly one copy at the end
    $endsectionPos = strrpos($content, '@endsection');
    if ($endsectionPos !== false) {
        $content = substr_replace($content, "\n" . $goodBlock . "\n", $endsectionPos, 0);
    }
    
    file_put_contents($bladePath, $content);
    echo "Removed duplicate script blocks and left only 1 at the end.\n";
} else {
    echo "Pattern not found.\n";
}
?>
