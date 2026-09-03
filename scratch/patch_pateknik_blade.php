<?php

$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

// Replace "Hapus Massal" with "Hapus Semua" and remove confirm
$target_btn1 = '<button type="button" id="btnModeBulk1" onclick="toggleBulkMode1()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Massal</button>';
$target_btn2 = '<button type="button" id="btnModeBulk2" onclick="toggleBulkMode2()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Massal</button>';

$replacement_btn1 = '<button type="button" id="btnModeBulk1" onclick="toggleBulkMode1()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Semua</button>';
$replacement_btn2 = '<button type="button" id="btnModeBulk2" onclick="toggleBulkMode2()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus Semua</button>';

$content = str_replace($target_btn1, $replacement_btn1, $content);
$content = str_replace($target_btn2, $replacement_btn2, $content);

// Form 1
$target_form1 = '<form id="bulkDeleteForm1" action="{{ route(\'pa-teknik.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih pada Tabel 1?\')">';
$replacement_form1 = <<<'EOF'
            <form id="bulkDeleteForm1" action="{{ route('pa-teknik.destroyBulk') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="filter_tahun" value="{{ $filterTahun }}">
                <input type="hidden" name="filter_bulan" value="{{ $filterBulan }}">
                <input type="hidden" name="delete_all" id="deleteAllFlag1" value="0">
                <input type="hidden" name="kelompok_tabel" value="1">
                
                <div id="btnGroupBulk1" class="hidden mb-3 bg-red-50 border border-red-200 p-3 rounded-xl flex items-center justify-between">
                    <span class="text-xs font-semibold text-red-700 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Data terpilih akan dihapus permanen.</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="cancelAll1()" class="px-3 py-1.5 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="button" onclick="submitBulkDelete1()" class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus Terpilih</button>
                    </div>
                </div>
EOF;

// Since the old form might have inner parts, let's just replace the old form's beginning
$content = preg_replace('/<form id="bulkDeleteForm1" .*?>\s*@csrf\s*@method\(\'DELETE\'\)\s*<div id="btnGroupBulk1" .*?<\/div>\s*<\/div>/s', $replacement_form1, $content);
// If it was already simple:
if (strpos($content, 'id="deleteAllFlag1"') === false) {
    $content = preg_replace('/<form id="bulkDeleteForm1".*?@method\(\'DELETE\'\)/s', $replacement_form1, $content);
}

// Form 2
$target_form2 = '<form id="bulkDeleteForm2" action="{{ route(\'pa-teknik.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih pada Tabel 2?\')">';
$replacement_form2 = <<<'EOF'
            <form id="bulkDeleteForm2" action="{{ route('pa-teknik.destroyBulk') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="filter_tahun" value="{{ $filterTahun }}">
                <input type="hidden" name="filter_bulan" value="{{ $filterBulan }}">
                <input type="hidden" name="delete_all" id="deleteAllFlag2" value="0">
                <input type="hidden" name="kelompok_tabel" value="2">
                
                <div id="btnGroupBulk2" class="hidden mb-3 bg-red-50 border border-red-200 p-3 rounded-xl flex items-center justify-between">
                    <span class="text-xs font-semibold text-red-700 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Data terpilih akan dihapus permanen.</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="cancelAll2()" class="px-3 py-1.5 text-xs font-medium bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="button" onclick="submitBulkDelete2()" class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-sm flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus Terpilih</button>
                    </div>
                </div>
EOF;

if (strpos($content, 'id="deleteAllFlag2"') === false) {
    $content = preg_replace('/<form id="bulkDeleteForm2".*?@method\(\'DELETE\'\)/s', $replacement_form2, $content);
}

// Remove old button groups inside the forms if any
$content = preg_replace('/<div id="btnGroupBulk1" class="hidden mb-4.*?>.*?<\/div>.*?<\/div>/s', '', $content);
$content = preg_replace('/<div id="btnGroupBulk2" class="hidden mb-4.*?>.*?<\/div>.*?<\/div>/s', '', $content);

// Ensure CSS is there
$css = <<<CSS
<style>
    .hide-bulk .cb-bulk, .hide-bulk .bulk-cb-header { display: none !important; }
    .hide-bulk th:first-child, .hide-bulk td:first-child { width: 0; padding: 0; overflow: hidden; opacity: 0; }
</style>
CSS;
if (strpos($content, '.hide-bulk .cb-bulk') === false) {
    $content = str_replace("@section('content')", "@section('content')\n" . $css, $content);
}

// Add hide-bulk class to containers
$content = str_replace('<div class="overflow-x-auto" id="tableContainerBulk1">', '<div class="overflow-x-auto hide-bulk" id="tableContainerBulk1">', $content);
$content = str_replace('<div class="overflow-x-auto" id="tableContainerBulk2">', '<div class="overflow-x-auto hide-bulk" id="tableContainerBulk2">', $content);

// Remove 'hidden' class from cb-bulk and bulk-cb-header just in case it was there from earlier code
$content = str_replace('class="bulk-cb-header-1 hidden"', 'class="bulk-cb-header-1"', $content);
$content = str_replace('class="bulk-cb-header-2 hidden"', 'class="bulk-cb-header-2"', $content);
$content = str_replace('class="cb-bulk-1 hidden', 'class="cb-bulk-1', $content);
$content = str_replace('class="cb-bulk-2 hidden', 'class="cb-bulk-2', $content);

// Ensure JavaScript handles delete_all flags
$new_js = <<<JS
        function submitBulkDelete1() {
            let selectAll = document.getElementById("selectAllBulk1");
            if (selectAll && selectAll.checked) {
                document.getElementById('deleteAllFlag1').value = "1";
            } else {
                document.getElementById('deleteAllFlag1').value = "0";
            }
            document.getElementById('bulkDeleteForm1').submit();
        }
        function submitBulkDelete2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll && selectAll.checked) {
                document.getElementById('deleteAllFlag2').value = "1";
            } else {
                document.getElementById('deleteAllFlag2').value = "0";
            }
            document.getElementById('bulkDeleteForm2').submit();
        }
JS;

if (strpos($content, 'function submitBulkDelete1') === false) {
    $content = str_replace('function cancelAll2() {', $new_js . "\n        function cancelAll2() {", $content);
}

file_put_contents($file, $content);
echo "PA Teknik patched.\n";
