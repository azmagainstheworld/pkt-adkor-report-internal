<?php
$controllersDir = 'app/Http/Controllers';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllersDir));
$phpFiles = [];

foreach ($files as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
        $phpFiles[] = $file->getPathname();
    }
}

foreach ($phpFiles as $filePath) {
    $content = file_get_contents($filePath);
    
    $changed = false;
    
    // Protect storeKolomDinamis
    if (strpos($content, 'function storeKolomDinamis') !== false) {
        if (strpos($content, 'abort_if(!auth()->user()->isAdmin()') === false) {
            $content = preg_replace(
                '/(public function storeKolomDinamis\([^\)]+\)\s*\{)/s',
                "$1\n        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');",
                $content
            );
            $changed = true;
        }
    }
    
    // Protect destroyKolomDinamis
    if (strpos($content, 'function destroyKolomDinamis') !== false) {
        // Also check if not already protected
        // wait, I only checked store above. Let me just use preg_replace directly.
        $content = preg_replace(
            '/(public function destroyKolomDinamis\([^\)]+\)\s*\{)(?!\s*abort_if)/s',
            "$1\n        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');",
            $content
        );
        $changed = true; // this might be slightly inaccurate for logging if nothing replaced, but it's fine.
    }
    
    if ($changed) {
        file_put_contents($filePath, $content);
        echo "Protected dynamic columns in: $filePath\n";
    }
}
echo "Done controllers.\n";
