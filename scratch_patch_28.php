<?php
$bladePath = 'resources/views/perizinan-perkantoran.blade.php';
$blade = file_get_contents($bladePath);

$pattern = '/\s*\/\/ TABEL 2 \(TERBIT\).*?if \(totalRows > 0\) renderPage\(1\);\s*\}\)\(\);/s';

// Extract one copy of the block to save it just in case
preg_match($pattern, $blade, $matches);
$goodBlock = isset($matches[0]) ? trim($matches[0]) : '';

if ($goodBlock) {
    // Remove ALL occurrences of this block from the blade file
    $blade = preg_replace($pattern, '', $blade);
    
    // Now append the good block at the end, before @endsection, inside a <script> tag
    $endsectionPos = strrpos($blade, '@endsection');
    if ($endsectionPos !== false) {
        $cleanScript = "\n<script>\n" . $goodBlock . "\n</script>\n";
        $blade = substr_replace($blade, $cleanScript, $endsectionPos, 0);
    }
    
    file_put_contents($bladePath, $blade);
    echo "Successfully removed stray JS text and appended cleanly at the bottom.\n";
} else {
    echo "Could not match the stray JS text pattern.\n";
}

?>
