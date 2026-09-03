<?php

$file = 'resources/views/non-teknik-non-tekstual.blade.php';
$content = file_get_contents($file);

$target_row = <<<'EOF'
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
EOF;

$replacement_row = <<<'EOF'
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-center w-10">
                            <input type="checkbox" name="ids[]" value="{{ $row['tahun'] }}|{{ $row['bulan'] }}" class="cb-bulk w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500" onclick="toggleCheckboxPa()">
                        </td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
EOF;

if (strpos($content, '<input type="checkbox" name="ids[]"') === false) {
    $content = str_replace($target_row, $replacement_row, $content);
}

// Since I might have missed whitespace, let's use preg_replace if str_replace fails
if (strpos($content, '<input type="checkbox" name="ids[]"') === false) {
    $content = preg_replace('/<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">\s+<td class="px-4 py-3 text-gray-700 font-medium text-center">{{ \$row\[\'tahun\'\] }}<\/td>/', $replacement_row, $content);
}

file_put_contents($file, $content);
echo "Row checkbox added.\n";
