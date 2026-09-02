<?php
$viewsDir = 'resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$phpFiles = [];

foreach ($files as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $phpFiles[] = $file->getPathname();
    }
}

foreach ($phpFiles as $filePath) {
    $content = file_get_contents($filePath);
    
    // There are modalAturKolom, modalAturKolom1, modalAturKolom2 etc.
    // The button typically looks like:
    // <button type="button" onclick="openModal('modalAturKolom...
    // Atur Kolom Tambahan
    // </button>
    // We can use a regex to wrap it.
    
    $pattern = '/(<button type="button" onclick="openModal\(\'modalAturKolom[^\']*\'.*?Atur Kolom Tambahan\s*<\/button>)/s';
    
    if (preg_match($pattern, $content)) {
        // Prevent double wrapping
        if (strpos($content, '@if(auth()->check() && auth()->user()->isAdmin())') === false || 
            !preg_match('/@if\(auth\(\)->check\(\) && auth\(\)->user\(\)->isAdmin\(\)\)\s*<button type="button" onclick="openModal\(\'modalAturKolom/', $content)) {
            
            $content = preg_replace($pattern, "@if(auth()->check() && auth()->user()->isAdmin())\n\$1\n@endif", $content);
            file_put_contents($filePath, $content);
            echo "Updated view: $filePath\n";
        }
    }
}
echo "Done views.\n";
