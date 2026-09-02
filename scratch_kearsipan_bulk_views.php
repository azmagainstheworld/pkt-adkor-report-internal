<?php

$files = [
    [
        'file' => 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php',
        'route' => 'pa-tekstual.destroyBulk'
    ],
    [
        'file' => 'resources/views/kearsipan-pa-teknik.blade.php',
        'route' => 'pa-teknik.destroyBulk'
    ],
    [
        'file' => 'resources/views/non-teknik-non-tekstual.blade.php',
        'route' => 'non-teknik-non-tekstual.destroyBulk'
    ],
    [
        'file' => 'resources/views/kearsipan-dof.blade.php',
        'route' => 'dof.destroyBulk'
    ]
];

foreach ($files as $item) {
    $filePath = $item['file'];
    $routeName = $item['route'];
    
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    // 1. Add CSS
    if (strpos($content, '.hide-bulk') === false) {
        $css = <<<EOD
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>
EOD;
        $content = preg_replace('/(<x-app-layout[^>]*>)/', "$1\n" . $css, $content);
    }
    
    // --- TABEL 1 ---
    if (strpos($content, 'toggleBulkMode1') === false) {
        $bulkBtn1 = <<<EOD
                <button type="button" id="btnModeBulk1" onclick="toggleBulkMode1()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Mode Hapus Massal
                </button>
EOD;
        $content = str_replace('<x-button variant="primary" onclick="openModalTambah(1)"', $bulkBtn1 . "\n                <x-button variant=\"primary\" onclick=\"openModalTambah(1)\"", $content);

        $actionBar1 = <<<EOD
        <form id="bulkDeleteForm1" action="{{ route('$routeName') }}" method="POST" onsubmit="return confirm('Hapus data terpilih pada Tabel 1?')">
            @csrf
            @method('DELETE')
            <div id="btnGroupBulk1" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll1()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>
            <div id="tableContainerBulk1" class="hide-bulk overflow-x-auto">
EOD;
        // For Tabel 1, find the overflow-x-auto div before @php $headTabel1 = ['Tahun', 'Bulan'];
        $content = preg_replace('/(<div class="overflow-x-auto">\s*@php\s*\$headTabel1 = \[\'Tahun\', \'Bulan\'\];)/s', $actionBar1 . "\n            @php\n                \$headTabel1 = ['<input type=\"checkbox\" id=\"selectAllBulk1\" onclick=\"toggleSelectAll1()\">', 'Tahun', 'Bulan'];", $content, 1);
        
        // Update colspan in empty state for Tabel 1
        $content = preg_replace('/(<tr><td colspan=")\{\{ count\(\$headTabel1\) \}\}(" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong\.)/s', '${1}{{ count($headTabel1) }}$2', $content, 1);
        
        // Checkbox inside row for Tabel 1
        $content = preg_replace('/(@forelse\(\$paginatedTable1 as \$row\)\s*<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">)\s*(<td class="px-4 py-3 text-gray-700 font-medium text-center">\{\{ \$row\[\'tahun\'\] \}\}<\/td>)/s', 
            "$1\n                        <td class=\"px-3 py-2 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk-1\" value=\"1|{{ \$row['tahun'] }}|{{ \$row['bulan'] }}\" onclick=\"toggleCheckbox1()\"></td>\n                        $2", $content, 1);
            
        // Close form for Tabel 1
        // Usually ends before the pagination div
        $content = preg_replace('/(<\/x-table>\s*)<div class="px-6 py-4 border-t border-gray-100 bg-gray-50">\s*\{\{ \$paginatedTable1->links\('."'".'pagination::tailwind'."'".'\) \}\}\s*<\/div>/s', 
            "$1    </div>\n        </form>\n        <div class=\"px-6 py-4 border-t border-gray-100 bg-gray-50\">\n            {{ \$paginatedTable1->links('pagination::tailwind') }}\n        </div>", $content, 1);
    }

    // --- TABEL 2 ---
    if (strpos($content, 'toggleBulkMode2') === false) {
        $bulkBtn2 = <<<EOD
                <button type="button" id="btnModeBulk2" onclick="toggleBulkMode2()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Mode Hapus Massal
                </button>
EOD;
        $content = str_replace('<x-button variant="primary" onclick="openModalTambah(2)"', $bulkBtn2 . "\n                <x-button variant=\"primary\" onclick=\"openModalTambah(2)\"", $content);

        $actionBar2 = <<<EOD
        <form id="bulkDeleteForm2" action="{{ route('$routeName') }}" method="POST" onsubmit="return confirm('Hapus data terpilih pada Tabel 2?')">
            @csrf
            @method('DELETE')
            <div id="btnGroupBulk2" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll2()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>
            <div id="tableContainerBulk2" class="hide-bulk overflow-x-auto">
EOD;
        // For Tabel 2, find the overflow-x-auto div before @php $headTabel2 = ['Tahun', 'Bulan'];
        $content = preg_replace('/(<div class="overflow-x-auto">\s*@php\s*\$headTabel2 = \[\'Tahun\', \'Bulan\'\];)/s', $actionBar2 . "\n            @php\n                \$headTabel2 = ['<input type=\"checkbox\" id=\"selectAllBulk2\" onclick=\"toggleSelectAll2()\">', 'Tahun', 'Bulan'];", $content, 1);
        
        // Update colspan in empty state for Tabel 2
        $content = preg_replace('/(<tr><td colspan=")\{\{ count\(\$headTabel2\) \}\}(" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong\.)/s', '${1}{{ count($headTabel2) }}$2', $content, 1);
        
        // Checkbox inside row for Tabel 2
        $content = preg_replace('/(@forelse\(\$paginatedTable2 as \$row\)\s*<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">)\s*(<td class="px-4 py-3 text-gray-700 font-medium text-center">\{\{ \$row\[\'tahun\'\] \}\}<\/td>)/s', 
            "$1\n                        <td class=\"px-3 py-2 text-center align-middle\"><input type=\"checkbox\" name=\"ids[]\" class=\"cb-bulk-2\" value=\"2|{{ \$row['tahun'] }}|{{ \$row['bulan'] }}\" onclick=\"toggleCheckbox2()\"></td>\n                        $2", $content, 1);
            
        // Close form for Tabel 2
        $content = preg_replace('/(<\/x-table>\s*)<div class="px-6 py-4 border-t border-gray-100 bg-gray-50">\s*\{\{ \$paginatedTable2->links\('."'".'pagination::tailwind'."'".'\) \}\}\s*<\/div>/s', 
            "$1    </div>\n        </form>\n        <div class=\"px-6 py-4 border-t border-gray-100 bg-gray-50\">\n            {{ \$paginatedTable2->links('pagination::tailwind') }}\n        </div>", $content, 1);
    }
    
    // 5. Add JS script
    if (strpos($content, 'toggleBulkMode1') === false) { // to ensure we don't add twice
        $js = <<<EOD
    <script>
        function toggleBulkMode1() {
            let container = document.getElementById("tableContainerBulk1");
            let btn = document.getElementById("btnModeBulk1");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll1();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll1() {
            let selectAll = document.getElementById("selectAllBulk1");
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtn1();
        }
        function toggleCheckbox1() {
            let selectAll = document.getElementById("selectAllBulk1");
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn1();
        }
        function toggleDeleteBtn1() {
            let group = document.getElementById("btnGroupBulk1");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-1:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll1() {
            let selectAll = document.getElementById("selectAllBulk1");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn1();
        }

        function toggleBulkMode2() {
            let container = document.getElementById("tableContainerBulk2");
            let btn = document.getElementById("btnModeBulk2");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll2();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll2() {
            let selectAll = document.getElementById("selectAllBulk2");
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtn2();
        }
        function toggleCheckbox2() {
            let selectAll = document.getElementById("selectAllBulk2");
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn2();
        }
        function toggleDeleteBtn2() {
            let group = document.getElementById("btnGroupBulk2");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-2:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn2();
        }
EOD;

        $content = str_replace('<script>', $js . "\n\n<script>", $content);
    }
    
    file_put_contents($filePath, $content);
    echo "Updated view: $filePath\n";
}
