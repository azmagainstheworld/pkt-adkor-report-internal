<?php
$path = 'resources/views/jasakurir.blade.php';
$content = file_get_contents($path);

// 1. Remove nested <script>
$searchScript = <<<HTML
        function toggleBulkMode() {
            let container = document.getElementById("tableContainerBulk");
            let btn = document.getElementById("btnModeBulk");
            toggleDeleteBtn();
        }

<script>
    // Toggle action dropdown function
HTML;

$replaceScript = <<<HTML
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

    // Toggle action dropdown function
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchScript), str_replace("\r\n", "\n", $replaceScript), $content);

// 2. Change label
$content = str_replace('Mode Hapus Massal', 'Hapus semua', $content);

// 3. Fix bulkDeleteForm
// Action was previously changed to 'jasakurir.data.destroyBulk', but let's just use regex to match it.
$content = preg_replace(
    '/<form\s+id="bulkDeleteForm"[^>]*>/i',
    '<form id="bulkDeleteForm" action="{{ route(\'jasakurir.data.destroyBulk\') }}" method="POST">
            <input type="hidden" id="deleteAllPages" name="delete_all_pages" value="0">',
    $content
);

// 4. Update toggleSelectAll
$searchToggleSelectAll = <<<HTML
        function toggleSelectAll() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtn();
        }
HTML;
$replaceToggleSelectAll = <<<HTML
        function toggleSelectAll() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            let deleteAllInput = document.getElementById("deleteAllPages");
            if (deleteAllInput) { deleteAllInput.value = selectAll.checked ? "1" : "0"; }
            toggleDeleteBtn();
        }
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchToggleSelectAll), str_replace("\r\n", "\n", $replaceToggleSelectAll), $content);

// 5. Add hide-bulk CSS
if (strpos($content, '.hide-bulk th:first-child') === false) {
    $content = str_replace("@section('content')", "@section('content')\n<style>\n.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }\n</style>", $content);
}

file_put_contents($path, $content);
echo "Jasa Kurir frontend fixed!\n";

// ============================================
// BACKEND: Update JasaKurirController@destroyBulk
// ============================================
$ctrlPath = 'app/Http/Controllers/JasaKurirController.php';
$ctrlContent = file_get_contents($ctrlPath);

$searchDestroyBulk = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
        ]);

        \$count = 0;
        foreach(\$request->ids as \$val) {
            \$parts = explode('|', \$val);
            if(count(\$parts) == 2) {
                \$tahun = \$parts[0];
                \$bulan = \$parts[1];
                \App\Models\JasaKurirData::where('tahun', \$tahun)->where('bulan', \$bulan)->delete();
                \$count++;
            }
        }

        return redirect()->back()->with('success', \$count . ' Data grup bulan berhasil dihapus.');
    }
PHP;

$replaceDestroyBulk = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        // Fitur Hapus Semua (Delete All Pages)
        if (\$request->input('delete_all_pages') == '1') {
            \$query = \App\Models\JasaKurirData::query();
            
            if (\$request->filled('tahun') && \$request->tahun !== 'semua') {
                \$query->where('tahun', \$request->tahun);
            }
            if (\$request->filled('bulan') && \$request->bulan !== 'semua') {
                \$query->where('bulan', \$request->bulan);
            }
            
            // Because JasaKurir is grouped by year/month, we delete all underlying data
            \$deletedRows = \$query->delete();
            return redirect()->back()->with('success', 'Seluruh data pengiriman ('.\$deletedRows.' record) berhasil dihapus.');
        }

        // Hapus Massal Biasa
        \$request->validate([
            'ids' => 'required|array',
        ]);

        \$count = 0;
        foreach(\$request->ids as \$val) {
            \$parts = explode('|', \$val);
            if(count(\$parts) == 2) {
                \$tahun = \$parts[0];
                \$bulan = \$parts[1];
                \App\Models\JasaKurirData::where('tahun', \$tahun)->where('bulan', \$bulan)->delete();
                \$count++;
            }
        }

        return redirect()->back()->with('success', \$count . ' Data grup bulan berhasil dihapus.');
    }
PHP;

$searchDestroyBulk = str_replace("\r\n", "\n", $searchDestroyBulk);
$replaceDestroyBulk = str_replace("\r\n", "\n", $replaceDestroyBulk);
$ctrlContent = str_replace("\r\n", "\n", $ctrlContent);
$ctrlContent = str_replace($searchDestroyBulk, $replaceDestroyBulk, $ctrlContent);

file_put_contents($ctrlPath, $ctrlContent);
echo "JasaKurirController backend fixed!\n";
?>
