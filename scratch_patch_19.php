<?php

$path = 'resources/views/anggaran/index.blade.php';
$content = file_get_contents($path);

$regex = '/(<tr class="hover:bg-gray-100 transition-colors text-sm">\s*)(<td class="px-6 py-3 text-gray-700 text-center align-middle whitespace-nowrap">\{\{ \$entry\[\'tahun\'\] \}\}<\/td>)/';
$replacement = '$1<td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk" value="{{ $anggaran->id }}" onclick="toggleCheckbox()"></td>'."\n".'                                $2';

if (strpos($content, 'value="{{ $anggaran->id }}"') === false) {
    $content = preg_replace($regex, $replacement, $content);
    file_put_contents($path, $content);
    echo "Added checkboxes to TABEL 1 rows.\n";
} else {
    echo "Checkboxes already exist.\n";
}

?>
