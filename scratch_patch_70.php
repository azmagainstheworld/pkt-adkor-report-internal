<?php
// 1. PATCH jasa-fotocopy.blade.php
$path = 'resources/views/jasa-fotocopy.blade.php';
$content = file_get_contents($path);

// Fix the nested script tag
$searchScript = "<script>\n    const rawChartData";
$replaceScript = "    const rawChartData";
$content = str_replace($searchScript, $replaceScript, $content);
$content = str_replace("<script>\r\n    const rawChartData", $replaceScript, $content);

// Update Hapus Massal button text
$searchModeBulkBtn = <<<HTML
                    <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>
HTML;
$replaceModeBulkBtn = <<<HTML
                    <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchModeBulkBtn), str_replace("\r\n", "\n", $replaceModeBulkBtn), $content);

// Update bulkDeleteForm (remove confirm, add hidden inputs)
$searchBulkForm = <<<HTML
                <form id="bulkDeleteForm" action="{{ route('jasafotocopy.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
            @csrf
            @method('DELETE')
HTML;
$replaceBulkForm = <<<HTML
                <form id="bulkDeleteForm" action="{{ route('jasafotocopy.destroyBulk') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="deleteAllPages" name="delete_all_pages" value="0">
            <input type="hidden" name="tahun" value="{{ request('tahun') }}">
            <input type="hidden" name="bulan" value="{{ request('bulan') }}">
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchBulkForm), str_replace("\r\n", "\n", $replaceBulkForm), $content);

// Add 'hide-bulk' class logic in toggleBulkMode JS
$searchToggleBulk = <<<JS
        function toggleBulkMode() {
            let container = document.getElementById("tableContainerBulk");
            let btn = document.getElementById("btnModeBulk");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
JS;
$replaceToggleBulk = <<<JS
        function toggleBulkMode() {
            let container = document.getElementById("tableContainerBulk");
            let btn = document.getElementById("btnModeBulk");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            let deleteAllInput = document.getElementById("deleteAllPages");
            if (deleteAllInput) { deleteAllInput.value = selectAll.checked ? "1" : "0"; }
            toggleDeleteBtn();
        }
JS;
// Check if tableContainerBulk has hide-bulk initially
$searchContainer = '<div class="overflow-x-auto overflow-y-visible" id="tableContainerBulk">';
$replaceContainer = '<div class="overflow-x-auto overflow-y-visible hide-bulk" id="tableContainerBulk">';
$content = str_replace($searchContainer, $replaceContainer, $content);

// Add CSS for hide-bulk
$searchStyle = '</style>';
$replaceStyle = <<<CSS
        .hide-bulk .cb-bulk, .hide-bulk #selectAllBulk { display: none !important; }
        .hide-bulk th:first-child, .hide-bulk td:first-child { padding: 0 !important; width: 0 !important; overflow: hidden; }
    </style>
CSS;
$content = str_replace($searchStyle, $replaceStyle, $content);

file_put_contents($path, $content);

// 2. PATCH JasaFotocopyController.php
$ctrlPath = 'app/Http/Controllers/JasaFotocopyController.php';
$ctrlContent = file_get_contents($ctrlPath);

$searchDestroyBulk = <<<PHP
        public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:jasa_fotocopy,id',
        ]);

        \App\Models\JasaFotocopy::whereIn('id', \$request->ids)->delete();

        return redirect()->back()->with('success', count(\$request->ids) . ' Data jasa fotocopy berhasil dihapus.');
    }
PHP;

$replaceDestroyBulk = <<<PHP
        public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        if (\$request->delete_all_pages == '1') {
            \$query = \App\Models\JasaFotocopy::query();
            if (\$request->tahun && \$request->tahun != 'semua') \$query->where('tahun', \$request->tahun);
            if (\$request->bulan && \$request->bulan != 'semua') \$query->where('bulan', \$request->bulan);
            
            \$count = \$query->count();
            \$query->delete();
            return redirect()->back()->with('success', \$count . ' Data jasa fotocopy (dari semua halaman) berhasil dihapus.');
        } else {
            \$request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:jasa_fotocopy,id',
            ]);

            \App\Models\JasaFotocopy::whereIn('id', \$request->ids)->delete();
            return redirect()->back()->with('success', count(\$request->ids) . ' Data jasa fotocopy berhasil dihapus.');
        }
    }
PHP;

$ctrlContent = str_replace(str_replace("\r\n", "\n", $searchDestroyBulk), str_replace("\r\n", "\n", $replaceDestroyBulk), $ctrlContent);
file_put_contents($ctrlPath, $ctrlContent);

echo "Jasa Fotocopy UI and Backend updated!\n";
?>
