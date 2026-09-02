<?php
    $path = 'resources/views/show.blade.php';
    if (file_exists($path)) {
        $content = file_get_contents($path);
        // Enhance openModal
        $content = str_replace("function openModal(id) { document.getElementById(id).classList.remove('hidden'); }", "function openModal(id) { const el = document.getElementById(id); if(el) el.classList.remove('hidden'); else console.error('Modal not found:', id); }", $content);
        file_put_contents($path, $content);
        echo "show.blade.php patched.\n";
    }
