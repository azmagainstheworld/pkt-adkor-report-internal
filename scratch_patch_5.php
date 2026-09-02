<?php

function fixKaryawanStyle() {
    $path = 'resources/views/karyawan.blade.php';
    $content = file_get_contents($path);

    // Add CSS for hide-bulk
    $style = <<<HTML
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>
HTML;

    if (strpos($content, '.hide-bulk th:first-child') === false) {
        // Find @section('content') or the first div to insert the style
        $content = preg_replace('/@section\(\'content\'\)/', "@section('content')\n" . $style, $content);
        file_put_contents($path, $content);
        echo "karyawan.blade.php style added.\n";
    } else {
        echo "karyawan.blade.php style already exists.\n";
    }
}

fixKaryawanStyle();
