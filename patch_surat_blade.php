<?php
$filePath = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($filePath);

// 1. Remove "Tabel 1: " and "Tabel 2: "
$content = str_replace('Tabel 1: ', '', $content);
$content = str_replace('Tabel 2: ', '', $content);

// 2. Remove the `<script>` tag at line 628 (which is after cancelAll())
$content = preg_replace('/function cancelAll\(\) \{[\s\S]*?toggleDeleteBtn\(\);\s*\}/', "function cancelAll() {\n            let selectAll = document.getElementById(\"selectAllBulk\");\n            if (selectAll) selectAll.checked = false;\n            let checkboxes = document.querySelectorAll(\".cb-bulk\");\n            checkboxes.forEach(cb => cb.checked = false);\n            toggleDeleteBtn();\n        }", $content);

$content = str_replace("        }\n\n<script>\n    function openModal", "        }\n\n    function openModal", $content);

// 3. Move the bulkDeleteForm from Table 1 to Table 2
// First, extract the btnGroupBulk and form start from Table 1
$table1Pattern = '/<form id="bulkDeleteForm"[^>]*>[\s\S]*?<div id="btnGroupBulk"[^>]*>[\s\S]*?<\/div>[\s\S]*?<div id="tableContainerBulk" class="hide-bulk overflow-x-auto">/U';

$table1Replacement = '<div class="overflow-x-auto">';

$content = preg_replace($table1Pattern, $table1Replacement, $content, 1);

// Remove the closing form tag of Table 1
$table1EndPattern = '/<\/table>\s*<\/div>\s*<\/form>/U';
$table1EndReplacement = "</table>\n            </div>";
$content = preg_replace($table1EndPattern, $table1EndReplacement, $content, 1);

// Now, wrap Table 2
$table2StartPattern = '/<div class="overflow-x-auto">\s*<table class="w-full/U';
$table2StartReplacement = <<<HTML
        <form id="bulkDeleteForm" action="{{ route('surat.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulk" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

        <div id="tableContainerBulk" class="hide-bulk overflow-x-auto">
            <table class="w-full
HTML;
$content = preg_replace($table2StartPattern, $table2StartReplacement, $content, 1);

// Add the closing form tag for Table 2
// Table 2 ends at:
//             </div>
//         </div>
//     </x-card>
$table2EndPattern = '/<\/table>\s*<\/div>\s*<\/x-card>/U';
$table2EndReplacement = "</table>\n            </div>\n        </form>\n    </x-card>";
$content = preg_replace($table2EndPattern, $table2EndReplacement, $content, 1);

// 4. Change "Mode Hapus Massal" to "Hapus semua"
$modeButtonOld = <<<HTML
                    Mode Hapus Massal
                </button>
HTML;
$modeButtonNew = <<<HTML
                    Hapus semua
                </button>
HTML;
$content = str_replace($modeButtonOld, $modeButtonNew, $content);


file_put_contents($filePath, $content);
echo "Blade updated successfully.\n";
