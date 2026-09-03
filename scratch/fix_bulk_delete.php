<?php

// 1. Update PemeliharaanController.php
$controllerFile = 'app/Http/Controllers/PemeliharaanController.php';
$content = file_get_contents($controllerFile);

$targetFunction = "public function destroyRutinBulk(\Illuminate\Http\Request \$request)\n    {\n        \$request->validate([\n            'ids' => 'required|array',\n        ]);";

$newFunction = <<<'EOD'
public function destroyRutinBulk(\Illuminate\Http\Request $request)
    {
        if ($request->input('delete_all') == '1') {
            $tahun = $request->input('filter_tahun', 'semua');
            $bulan = $request->input('filter_bulan', 'semua');
            $query = \App\Models\PemeliharaanRutinData::query();
            if ($tahun !== 'semua') $query->where('tahun', $tahun);
            if ($bulan !== 'semua') $query->where('bulan', $bulan);
            
            $count = $query->delete();
            return back()->with('success', "Seluruh data pemeliharaan rutin berhasil dihapus.");
        }

        $request->validate([
            'ids' => 'required|array',
        ]);
EOD;

$content = str_replace($targetFunction, $newFunction, $content);
file_put_contents($controllerFile, $content);
echo "Controller updated.\n";


// 2. Update pemeliharaan.blade.php
$bladeFile = 'resources/views/pemeliharaan.blade.php';
$blade = file_get_contents($bladeFile);

// Remove confirm and add hidden inputs
$formTarget = '<form id="bulkDeleteFormRutin" action="{{ route(\'pemeliharaan-rutin.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data pemeliharaan rutin terpilih?\')">';
$formNew = '<form id="bulkDeleteFormRutin" action="{{ route(\'pemeliharaan-rutin.destroyBulk\') }}" method="POST">
              <input type="hidden" name="filter_tahun" value="{{ request(\'tahun\', \'semua\') }}">
              <input type="hidden" name="filter_bulan" value="{{ request(\'bulan\', \'semua\') }}">
              <input type="hidden" name="delete_all" id="deleteAllRutin" value="0">';

$blade = str_replace($formTarget, $formNew, $blade);

// Update toggleSelectAllRutin JS
$jsTarget = <<<'EOD'
function toggleSelectAllRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnRutin();
        }
EOD;

$jsNew = <<<'EOD'
function toggleSelectAllRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            
            let deleteAllInput = document.getElementById("deleteAllRutin");
            if (deleteAllInput) {
                deleteAllInput.value = selectAll.checked ? '1' : '0';
            }
            toggleDeleteBtnRutin();
        }
EOD;

$blade = str_replace($jsTarget, $jsNew, $blade);

// Update toggleCheckboxRutin JS
$jsTarget2 = <<<'EOD'
function toggleCheckboxRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtnRutin();
        }
EOD;

$jsNew2 = <<<'EOD'
function toggleCheckboxRutin() {
            let selectAll = document.getElementById("selectAllBulkRutin");
            let checkboxes = document.querySelectorAll(".cb-bulk-rutin");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            
            let deleteAllInput = document.getElementById("deleteAllRutin");
            if (deleteAllInput) {
                deleteAllInput.value = '0';
            }
            toggleDeleteBtnRutin();
        }
EOD;

$blade = str_replace($jsTarget2, $jsNew2, $blade);

file_put_contents($bladeFile, $blade);
echo "Blade updated.\n";
