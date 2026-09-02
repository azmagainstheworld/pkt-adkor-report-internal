<?php
    $path = 'resources/views/layouts/app.blade.php';
    if (file_exists($path)) {
        $content = file_get_contents($path);
        // Change type="search" to type="text" to prevent native double x
        $content = str_replace('type="search" name="search"', 'type="text" name="search"', $content);
        
        // Also make sure the JS highlighting doesn't break form inputs or attributes
        // The tree walker nodeFilter rejects SCRIPT, STYLE, NOSCRIPT, MARK
        // But maybe it's replacing text inside attribute values? No, NodeFilter.SHOW_TEXT only matches text nodes.
        
        file_put_contents($path, $content);
        echo "layouts/app.blade.php patched.\n";
    }
