<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

$style = <<<EOD
@section('styles')
<style>
    .hide-bulk th:first-child, .hide-bulk td:first-child {
        display: none !important;
    }
</style>
@endsection
EOD;

if (strpos($content, '.hide-bulk') === false) {
    $content = str_replace("@section('content')", "@section('content')\n" . $style, $content);
    file_put_contents($path, $content);
    echo "Added .hide-bulk style\n";
} else {
    echo ".hide-bulk style already exists\n";
}
?>
