<?php
$bladePath = 'resources/views/perizinan-perkantoran.blade.php';
$lines = file($bladePath);
$newLines = [];
$inStrayBlock = false;
$strayBlockCount = 0;

foreach ($lines as $line) {
    if (!$inStrayBlock && strpos($line, '// TABEL 2 (TERBIT)') !== false) {
        // Check if the previous non-empty line was a <script> tag
        $prevLine = '';
        for ($i = count($newLines) - 1; $i >= 0; $i--) {
            if (trim($newLines[$i]) !== '') {
                $prevLine = trim($newLines[$i]);
                break;
            }
        }
        
        if (strpos($prevLine, '<script>') === false) {
            // This is a stray block!
            $inStrayBlock = true;
            $strayBlockCount++;
            continue; // Skip this line
        }
    }
    
    if ($inStrayBlock) {
        if (strpos($line, '})();') !== false) {
            $inStrayBlock = false; // End of stray block
        }
        continue; // Skip all lines inside stray block
    }
    
    $newLines[] = $line;
}

if ($strayBlockCount > 0) {
    file_put_contents($bladePath, implode("", $newLines));
    echo "Successfully removed $strayBlockCount stray JS block(s)!\n";
} else {
    echo "No stray blocks found.\n";
}

?>
