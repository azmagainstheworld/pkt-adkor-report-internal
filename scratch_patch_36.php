<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// Hapus block @section('styles') yang salah sebelumnya
$content = preg_replace('/@section\(\'styles\'\).*?@endsection/s', '', $content);

// Masukkan <style> langsung di dalam @section('content')
$style = <<<EOD
<style>
    .hide-bulk th:first-child, .hide-bulk td:first-child {
        display: none !important;
    }
</style>
EOD;

if (strpos($content, '.hide-bulk th:first-child') === false) {
    $content = str_replace("@section('content')", "@section('content')\n" . $style, $content);
    file_put_contents($path, $content);
    echo "Added .hide-bulk style directly\n";
} else {
    echo ".hide-bulk style already exists\n";
}
?>
