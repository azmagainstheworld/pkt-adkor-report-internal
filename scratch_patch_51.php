<?php
$path = 'resources/views/pelaporan.blade.php';
$content = file_get_contents($path);

// 1. Re-add missing </form> for Tabel 2
$searchMissingForm = <<<HTML
            </x-table>
        </div>
        
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            <div>
                @if (\$dataRincian->total() > 0)
HTML;

$replaceMissingForm = <<<HTML
            </x-table>
        </div>
        </form>
        
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            <div>
                @if (\$dataRincian->total() > 0)
HTML;

$searchMissingForm = str_replace("\r\n", "\n", $searchMissingForm);
$replaceMissingForm = str_replace("\r\n", "\n", $replaceMissingForm);
$content = str_replace("\r\n", "\n", $content);
$content = str_replace($searchMissingForm, $replaceMissingForm, $content);

// 2. Add delete_all_pages hidden input to bulkDeleteForm
$searchBulkForm = '<form id="bulkDeleteForm" action="{{ route(\'pelaporan.destroyBulk\') }}" method="POST" >';
$replaceBulkForm = '<form id="bulkDeleteForm" action="{{ route(\'pelaporan.destroyBulk\') }}" method="POST" >
            <input type="hidden" id="deleteAllPages" name="delete_all_pages" value="0">';
$content = str_replace($searchBulkForm, $replaceBulkForm, $content);

// 3. Update Javascript to handle toggleSelectAll setting delete_all_pages
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
            if (deleteAllInput) {
                deleteAllInput.value = selectAll.checked ? "1" : "0";
            }
            
            toggleDeleteBtn();
        }
HTML;
$searchToggleSelectAll = str_replace("\r\n", "\n", $searchToggleSelectAll);
$replaceToggleSelectAll = str_replace("\r\n", "\n", $replaceToggleSelectAll);
$content = str_replace($searchToggleSelectAll, $replaceToggleSelectAll, $content);

// 4. Add "Wajib diisi!" error spans for required fields in modals
// For inputs:
$content = preg_replace('/(<input[^>]+required[^>]*>)/i', '$1 <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span>', $content);
// For selects:
$content = preg_replace('/(<select[^>]+required[^>]*>.*?<\/select>)/is', '$1 <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span>', $content);
// For textareas:
$content = preg_replace('/(<textarea[^>]+required[^>]*>.*?<\/textarea>)/is', '$1 <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span>', $content);

file_put_contents($path, $content);
echo "Frontend fixes applied successfully!\n";

// ============================================
// BACKEND: Update PelaporanController@destroyBulk
// ============================================
$ctrlPath = 'app/Http/Controllers/PelaporanController.php';
$ctrlContent = file_get_contents($ctrlPath);

$searchDestroyBulk = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pelaporan,id',
        ]);

        \App\Models\Pelaporan::whereIn('id', \$request->ids)->delete();

        return redirect()->back()->with('success', count(\$request->ids) . ' Data pelaporan berhasil dihapus.');
    }
PHP;

$replaceDestroyBulk = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        // Fitur Hapus Semua (Delete All Pages)
        if (\$request->input('delete_all_pages') == '1') {
            // Re-apply current filters to delete ALL matching data
            \$query = \App\Models\Pelaporan::query();
            
            if (\$request->filled('tahun') && \$request->tahun !== 'semua') {
                \$query->whereYear('tanggal', \$request->tahun);
            }
            if (\$request->filled('bulan') && \$request->bulan !== 'semua') {
                \$bulanMap = [
                    'Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,
                    'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12
                ];
                if(isset(\$bulanMap[\$request->bulan])) {
                    \$query->whereMonth('tanggal', \$bulanMap[\$request->bulan]);
                }
            }
            
            \$count = \$query->count();
            \$query->delete();
            
            return redirect()->back()->with('success', \$count . ' Data pelaporan (seluruh halaman) berhasil dihapus.');
        }

        // Hapus Massal Biasa (Hanya halaman saat ini)
        if (\$request->has('ids') && is_array(\$request->ids)) {
            \App\Models\Pelaporan::whereIn('id', \$request->ids)->delete();
            return redirect()->back()->with('success', count(\$request->ids) . ' Data pelaporan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
    }
PHP;

$searchDestroyBulk = str_replace("\r\n", "\n", $searchDestroyBulk);
$replaceDestroyBulk = str_replace("\r\n", "\n", $replaceDestroyBulk);
$ctrlContent = str_replace("\r\n", "\n", $ctrlContent);
$ctrlContent = str_replace($searchDestroyBulk, $replaceDestroyBulk, $ctrlContent);

file_put_contents($ctrlPath, $ctrlContent);
echo "Backend fixes applied successfully!\n";
?>
