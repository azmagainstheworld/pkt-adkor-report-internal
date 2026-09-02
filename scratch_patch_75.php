<?php
$c = file_get_contents('resources/views/undangan.blade.php');

$searchTable = "<x-table :headers=\"['<input type=\\'checkbox\\' id=\\'selectAllDetail\\' onclick=\\'toggleSelectAll(\"detail\")\\'>', 'No.', 'Tahun', 'Bulan', 'Jenis Undangan', 'Agenda', 'Aksi']\">";
$replaceTable = <<<HTML
            @php
                \$headersDetail = [
                    '<input type="checkbox" id="selectAllDetail" onclick="toggleSelectAll(\\'detail\\')">',
                    'No.', 'Tahun', 'Bulan', 'Jenis Undangan', 'Agenda', 'Aksi'
                ];
            @endphp
            <x-table :headers="\$headersDetail">
HTML;

$c = str_replace($searchTable, $replaceTable, $c);
file_put_contents('resources/views/undangan.blade.php', $c);
echo "Fixed x-table syntax.\n";
?>
