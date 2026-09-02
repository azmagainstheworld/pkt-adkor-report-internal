<?php
$c = file_get_contents('resources/views/undangan.blade.php');

$replaceTable = <<<HTML
            @php
                \$headersDetail = [
                    '<input type="checkbox" id="selectAllDetail" onclick="toggleSelectAll(\\'detail\\')">',
                    'No.', 'Tahun', 'Bulan', 'Jenis Undangan', 'Agenda', 'Aksi'
                ];
            @endphp
            <x-table :headers="\$headersDetail">
HTML;

$c = preg_replace('/<x-table\s+:headers="\[\'<input[^>]+>.*?\]">/s', $replaceTable, $c);
file_put_contents('resources/views/undangan.blade.php', $c);
echo "Fixed x-table syntax via regex.\n";
?>
