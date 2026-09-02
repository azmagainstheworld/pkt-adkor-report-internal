<?php
$content = file_get_contents('resources/views/program-strategis.blade.php');

// 1. Move btnModeBulk
$content = preg_replace('/<button type="button" id="btnModeBulk"[^>]+>.*?<\/button>\s*/s', '', $content);
// Find dropdownOpsiSuper closing div and inject btnModeBulk
$btnModeBulk = <<<EOD
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>
EOD;

$content = preg_replace('/(id="dropdownOpsiSuper".*?<\/div>\s*<\/div>\s*<\/div>)/s', "$1\n$btnModeBulk", $content);

// 2. Change Table Display for Target Waktu
$tableTargetTime = <<<EOD
                                    @if(\$row->target_waktu_start)
                                        {{ \Carbon\Carbon::parse(\$row->target_waktu_start)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
EOD;
$content = preg_replace('/@if\(\$row->target_waktu_start && \$row->target_waktu_end\).*?@endif/s', $tableTargetTime, $content);

// 3. Modals Target Waktu (Tambah)
$tambahTargetTime = <<<EOD
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Waktu</label>
                    <input type="date" name="target_waktu_start" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
EOD;
$content = preg_replace('/<div class="col-span-1">\s*<label[^>]+>Target Waktu Mulai<\/label>.*?<\/div>\s*<div class="col-span-1">\s*<label[^>]+>Target Waktu Selesai<\/label>.*?<\/div>/s', $tambahTargetTime, $content, 1);

// 4. Modals Target Waktu (Edit)
$editTargetTime = <<<EOD
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Waktu</label>
                <input type="date" name="target_waktu_start" id="edit_target_start" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
            </div>
EOD;
$content = preg_replace('/<div class="col-span-1">\s*<label[^>]+>Target Waktu Mulai<\/label>.*?<\/div>\s*<div class="col-span-1">\s*<label[^>]+>Target Waktu Selesai<\/label>.*?<\/div>/s', $editTargetTime, $content, 1);

// 5. Remove edit_target_end from JS
$content = preg_replace('/document\.getElementById\(\'edit_target_end\'\)\.value = data\.target_waktu_end \|\| \'\';/', '', $content);

file_put_contents('resources/views/program-strategis.blade.php', $content);

// Patch Export PDF
$pdfContent = file_get_contents('resources/views/pdf/program-strategis.blade.php');
// Header
$pdfContent = str_replace('<th style="width: 8%;">Waktu Mulai</th>', '<th style="width: 8%;">Target Waktu</th>', $pdfContent);
$pdfContent = preg_replace('/<th[^>]*>Waktu Selesai<\/th>/', '', $pdfContent);
// Data
$pdfTarget = <<<EOD
                        <td>
                            @if(\$row->target_waktu_start)
                                {{ \Carbon\Carbon::parse(\$row->target_waktu_start)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
EOD;
$pdfContent = preg_replace('/<td>\s*@if\(\$row->target_waktu_start\)\s*{{ \Carbon.*?<\/td>\s*<td>\s*@if\(\$row->target_waktu_end\).*?<\/td>/s', $pdfTarget, $pdfContent);
file_put_contents('resources/views/pdf/program-strategis.blade.php', $pdfContent);

// Patch Export Excel
$excelContent = file_get_contents('app/Exports/ProgramStrategisExport.php');
$excelContent = str_replace("'Target Waktu Mulai', 'Target Waktu Selesai',", "'Target Waktu',", $excelContent);
$excelContent = str_replace("\$isFirst ? \$row->target_waktu_start : '',\n            \$isFirst ? \$row->target_waktu_end : '',", "\$isFirst ? \$row->target_waktu_start : '',", $excelContent);
file_put_contents('app/Exports/ProgramStrategisExport.php', $excelContent);

echo "Done\n";
