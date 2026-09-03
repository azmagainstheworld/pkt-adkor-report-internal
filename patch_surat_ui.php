<?php
$file = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($file);

// 1. Add CSS for hide-bulk
if (strpos($content, '.hide-bulk th:first-child') === false) {
    $content = str_replace("@section('content')", "@section('content')\n<style>\n.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }\n</style>", $content);
}

// 2. Remove "Unduh Template Excel" from modalTambah
$modalTambahPattern = '/(<x-modal id="modalTambah"[\s\S]*?)<!-- Download Template injected -->[\s\S]*?<\/div>\s*(<div>\s*<label class="block text-sm font-medium)/';
$content = preg_replace($modalTambahPattern, '$1$2', $content, 1);

// 3. Move "Opsi Lanjutan" to Table 1 Header
$opsiLanjutanPattern = '/<!-- Opsi Lanjutan -->\s*<div class="relative inline-block text-left">[\s\S]*?<!-- Tambah Data Utama -->/U';
preg_match($opsiLanjutanPattern, $content, $matches);
if (!empty($matches[0])) {
    $opsiLanjutanHtml = $matches[0];
    // Remove it from its original place
    $content = str_replace($opsiLanjutanHtml, '<!-- Tambah Data Utama -->', $content);
    
    // Clean up Opsi Lanjutan HTML to fit nicely
    $opsiLanjutanHtml = str_replace('<!-- Tambah Data Utama -->', '', $opsiLanjutanHtml);
    
    // Inject it into Table 1 header
    $table1HeaderPattern = '/(<div class="p-5 border-b border-gray-100 bg-white">)\s*(<h3 class="font-bold text-gray-900 text-lg">Akumulasi Laporan Surat Masuk dan Kekuar<\/h3>)\s*(<p class="text-xs text-gray-400">.*?<\/p>)\s*(<\/div>)/is';
    
    $table1HeaderReplacement = '$1
        <div class="flex justify-between items-start">
            <div>
                $2
                $3
            </div>
            ' . trim($opsiLanjutanHtml) . '
        </div>
    $4';
    
    $content = preg_replace($table1HeaderPattern, $table1HeaderReplacement, $content, 1);
}

// 4. Move bulk delete form out of Table 1 and wrap Table 2 properly
// Currently Table 1 is wrapped in <form id="bulkDeleteForm"...>
$table1StartPattern = '/<form id="bulkDeleteForm"[^>]*>[\s\S]*?<div id="btnGroupBulk"[^>]*>[\s\S]*?<\/div>\s*<div id="tableContainerBulk" class="hide-bulk overflow-x-auto">\s*<table class="w-full/U';
$table1StartReplacement = '<div class="overflow-x-auto"><table class="w-full';
if (preg_match('/<form id="bulkDeleteForm"/', $content)) {
    // Extract the form start and btnGroupBulk
    preg_match('/(<form id="bulkDeleteForm"[^>]*>)\s*@csrf\s*@method\(\'DELETE\'\)\s*(<div id="btnGroupBulk"[^>]*>[\s\S]*?<\/div>)\s*<div id="tableContainerBulk" class="hide-bulk overflow-x-auto">/U', $content, $formMatches);
    
    if (!empty($formMatches)) {
        $formTag = $formMatches[1] . "\n            @csrf\n            @method('DELETE')\n            \n            " . $formMatches[2] . "\n\n";
        
        // Remove it from Table 1
        $content = preg_replace('/<form id="bulkDeleteForm"[^>]*>[\s\S]*?<div id="btnGroupBulk"[^>]*>[\s\S]*?<\/div>\s*<div id="tableContainerBulk" class="hide-bulk overflow-x-auto">/U', '<div class="overflow-x-auto">', $content, 1);
        
        // Remove closing form from Table 1
        $content = preg_replace('/<\/table>\s*<\/div>\s*<\/form>\s*<\/x-card>/U', '</table></div></x-card>', $content, 1);
        
        // Now wrap Table 2 with the extracted form
        $table2Pattern = '/(<x-card class="!rounded-xl[^>]*>)\s*(<div class="p-5 border-b[^>]*>[\s\S]*?<\/div>)\s*(@php[\s\S]*?@endphp)\s*<div class="overflow-x-auto">/U';
        $table2Replacement = '$1' . "\n        " . $formTag . '        $2' . "\n" . '        $3' . "\n" . '        <div id="tableContainerBulk" class="hide-bulk overflow-x-auto">';
        
        $content = preg_replace($table2Pattern, $table2Replacement, $content, 1);
        
        // Add closing form tag for Table 2
        $content = preg_replace('/<\/table>\s*<\/div>\s*<\/x-card>/U', "</table>\n        </div>\n        </form>\n    </x-card>", $content, 1);
    }
}

file_put_contents($file, $content);
echo "All requirements applied.\n";
