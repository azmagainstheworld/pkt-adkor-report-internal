<?php

$files = glob('resources/views/*.blade.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $changed = false;
    
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], 'Atur Kolom Tambahan') !== false) {
            // Find where this tag starts
            $start = $i;
            while ($start >= 0 && strpos($lines[$start], '<a ') === false && strpos($lines[$start], '<button ') === false) {
                $start--;
            }
            
            // Find where this tag ends
            $end = $i;
            while ($end < count($lines) && strpos($lines[$end], '</a>') === false && strpos($lines[$end], '</button>') === false) {
                $end++;
            }
            
            if ($start >= 0 && $end < count($lines)) {
                // Check if already wrapped by looking at the line before start
                if ($start > 0 && strpos($lines[$start-1], '@if') !== false) {
                    continue;
                }
                
                $lines[$start] = "@if(auth()->check() && auth()->user()->isAdmin())\n" . $lines[$start];
                $lines[$end] = $lines[$end] . "\n@endif";
                $changed = true;
                
                // In undangan.blade.php, there's a div containing 'Konfigurasi' just above it.
                // Let's also wrap that if it exists.
                if ($start >= 3) {
                    if (strpos($lines[$start-1], '</div>') !== false && strpos($lines[$start-2], 'Konfigurasi') !== false && strpos($lines[$start-3], '<div ') !== false) {
                        // Move the @if up
                        $lines[$start] = str_replace("@if(auth()->check() && auth()->user()->isAdmin())\n", "", $lines[$start]);
                        $lines[$start-3] = "@if(auth()->check() && auth()->user()->isAdmin())\n" . $lines[$start-3];
                    }
                }
            }
        }
    }
    
    if ($changed) {
        file_put_contents($file, implode("\n", $lines));
        echo "Patched $file\n";
    }
}
