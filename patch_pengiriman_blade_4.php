<?php

$filePath = 'resources/views/pengiriman-dokumen.blade.php';
$content = file_get_contents($filePath);

// Update table 1 loop
$table1Old = <<<HTML
            <x-table :headers="\$tableHeaders">
                @forelse(\$costRecords as \$record)
HTML;
$table1New = <<<HTML
            <x-table :headers="\$tableHeaders">
                @forelse(\$volumeRecords as \$record)
HTML;
$content = str_replace($table1Old, $table1New, $content);

// Update table 1 pagination
$paginate1Old = <<<HTML
            <div class="mt-6">
                {{ \$costRecords->links('vendor.pagination.tailwind') }}
            </div>
HTML;
$paginate1New = <<<HTML
            <div class="mt-6">
                {{ \$volumeRecords->links('vendor.pagination.tailwind') }}
            </div>
HTML;
$content = str_replace($paginate1Old, $paginate1New, $content);

// Update table 2 loop
$table2Old = <<<HTML
            <x-table :headers="\$tableHeadersOngkir">
                @forelse(\$costRecords as \$cost)
HTML;
$table2New = <<<HTML
            <x-table :headers="\$tableHeadersOngkir">
                @forelse(\$ongkirRecords as \$cost)
HTML;
$content = str_replace($table2Old, $table2New, $content);

// Update table 2 pagination
// Wait, is table 2 pagination using $costRecords->links() ? Let's just do a generic replace for it too.
// I will just use preg_replace for table 2 pagination to be safe, or just do a general search.
$paginate2Old = <<<HTML
            <div class="mt-6">
                {{ \$costRecords->links('vendor.pagination.tailwind') }}
            </div>
HTML;
$paginate2New = <<<HTML
            <div class="mt-6">
                {{ \$ongkirRecords->links('vendor.pagination.tailwind') }}
            </div>
HTML;
$content = str_replace($paginate2Old, $paginate2New, $content);

// Let's also verify that there is no other `$costRecords` being used
$content = str_replace('$costRecords', '$ongkirRecords', $content); // In case there are leftover $costRecords references in pagination or similar for Ongkir

file_put_contents($filePath, $content);
echo "Blade template updated.\n";
