<?php
$file = 'resources/views/pemeliharaan.blade.php';
$content = file_get_contents($file);

$style = <<<EOD
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>

EOD;

if (strpos($content, '.hide-bulk th:first-child') === false) {
    $content = str_replace(
        "@section('content')",
        "@section('content')\n" . $style,
        $content
    );
    file_put_contents($file, $content);
    echo "Style added.\n";
} else {
    echo "Style already present.\n";
}
