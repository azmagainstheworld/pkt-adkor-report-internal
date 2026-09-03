<?php

$files = glob('resources/views/*.blade.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;
    
    // Pattern for button or a tag containing "Atur Kolom Tambahan"
    // Note: In undangan.blade.php there's a div "Konfigurasi" before it, maybe I just wrap the a/button.
    
    $content = preg_replace_callback('/<(a|button)[^>]*>.*?Atur Kolom Tambahan.*?<\/\1>/is', function($matches) {
        $tag = $matches[0];
        
        // Check if already wrapped
        if (strpos($tag, '@if') !== false) {
            return $tag;
        }
        
        return "@if(auth()->check() && auth()->user()->isAdmin())\n" . $tag . "\n@endif";
    }, $content);
    
    // In undangan.blade.php, there's also the "Konfigurasi" header div.
    // Let's specifically handle it if needed.
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Patched $file\n";
    }
}
