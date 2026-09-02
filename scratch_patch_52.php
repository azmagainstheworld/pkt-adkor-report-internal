<?php
$path = 'resources/views/pengiriman-dokumen.blade.php';
$content = file_get_contents($path);

// 1. Remove nested <script> tag
$searchScript = <<<HTML
        function toggleDeleteBtnOngkir() {
            let group = document.getElementById("btnGroupBulkOngkir");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-ongkir:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllOngkir() {
            let selectAll = document.getElementById("selectAllBulkOngkir");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-ongkir");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnOngkir();
        }

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
HTML;

$replaceScript = <<<HTML
        function toggleDeleteBtnOngkir() {
            let group = document.getElementById("btnGroupBulkOngkir");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-ongkir:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAllOngkir() {
            let selectAll = document.getElementById("selectAllBulkOngkir");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-ongkir");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtnOngkir();
        }

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
HTML;

$searchScript = str_replace("\r\n", "\n", $searchScript);
$replaceScript = str_replace("\r\n", "\n", $replaceScript);
$content = str_replace("\r\n", "\n", $content);
$content = str_replace($searchScript, $replaceScript, $content);

// 2. Change "Mode Hapus Massal" to "Hapus semua" (replaces all occurrences)
$content = str_replace('Mode Hapus Massal', 'Hapus semua', $content);

// 3. Wrap Atur Kolom in isAdmin (Volume and Ongkir)
// Volume
$searchAturVol = <<<HTML
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolomVolume'); toggleActionDropdown('dropdownOpsiSuperVolume')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
HTML;
$replaceAturVol = <<<HTML
                        @if(auth()->user()->isAdmin())
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolomVolume'); toggleActionDropdown('dropdownOpsiSuperVolume')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
                        @endif
HTML;
$searchAturVol = str_replace("\r\n", "\n", $searchAturVol);
$replaceAturVol = str_replace("\r\n", "\n", $replaceAturVol);
$content = str_replace($searchAturVol, $replaceAturVol, $content);

// Ongkir
$searchAturOng = <<<HTML
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolomOngkir'); toggleActionDropdown('dropdownOpsiSuperOngkir')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
HTML;
$replaceAturOng = <<<HTML
                        @if(auth()->user()->isAdmin())
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolomOngkir'); toggleActionDropdown('dropdownOpsiSuperOngkir')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
                        @endif
HTML;
$searchAturOng = str_replace("\r\n", "\n", $searchAturOng);
$replaceAturOng = str_replace("\r\n", "\n", $replaceAturOng);
$content = str_replace($searchAturOng, $replaceAturOng, $content);

// 4. Add delete_all_pages hidden input to bulkDeleteFormVolume and bulkDeleteFormOngkir
$searchBulkVol = '<form id="bulkDeleteFormVolume" action="{{ route(\'pengiriman-dokumen.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih?\')">';
$replaceBulkVol = '<form id="bulkDeleteFormVolume" action="{{ route(\'pengiriman-dokumen.destroyBulk\') }}" method="POST">
            <input type="hidden" id="deleteAllPagesVolume" name="delete_all_pages" value="0">';
$content = str_replace($searchBulkVol, $replaceBulkVol, $content);

$searchBulkOng = '<form id="bulkDeleteFormOngkir" action="{{ route(\'pengiriman-dokumen-ongkir.destroyBulk\') }}" method="POST" onsubmit="return confirm(\'Hapus data terpilih?\')">';
$replaceBulkOng = '<form id="bulkDeleteFormOngkir" action="{{ route(\'pengiriman-dokumen-ongkir.destroyBulk\') }}" method="POST">
            <input type="hidden" id="deleteAllPagesOngkir" name="delete_all_pages" value="0">';
$content = str_replace($searchBulkOng, $replaceBulkOng, $content);

// Update Javascript to handle toggleSelectAll setting delete_all_pages
$searchTSAVol = <<<HTML
        function toggleSelectAllVolume() {
            let selectAll = document.getElementById("selectAllBulkVolume");
            let checkboxes = document.querySelectorAll(".cb-bulk-volume");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnVolume();
        }
HTML;
$replaceTSAVol = <<<HTML
        function toggleSelectAllVolume() {
            let selectAll = document.getElementById("selectAllBulkVolume");
            let checkboxes = document.querySelectorAll(".cb-bulk-volume");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            let deleteAllInput = document.getElementById("deleteAllPagesVolume");
            if (deleteAllInput) { deleteAllInput.value = selectAll.checked ? "1" : "0"; }
            toggleDeleteBtnVolume();
        }
HTML;
$searchTSAVol = str_replace("\r\n", "\n", $searchTSAVol);
$replaceTSAVol = str_replace("\r\n", "\n", $replaceTSAVol);
$content = str_replace($searchTSAVol, $replaceTSAVol, $content);

$searchTSAOng = <<<HTML
        function toggleSelectAllOngkir() {
            let selectAll = document.getElementById("selectAllBulkOngkir");
            let checkboxes = document.querySelectorAll(".cb-bulk-ongkir");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtnOngkir();
        }
HTML;
$replaceTSAOng = <<<HTML
        function toggleSelectAllOngkir() {
            let selectAll = document.getElementById("selectAllBulkOngkir");
            let checkboxes = document.querySelectorAll(".cb-bulk-ongkir");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            let deleteAllInput = document.getElementById("deleteAllPagesOngkir");
            if (deleteAllInput) { deleteAllInput.value = selectAll.checked ? "1" : "0"; }
            toggleDeleteBtnOngkir();
        }
HTML;
$searchTSAOng = str_replace("\r\n", "\n", $searchTSAOng);
$replaceTSAOng = str_replace("\r\n", "\n", $replaceTSAOng);
$content = str_replace($searchTSAOng, $replaceTSAOng, $content);

// 5. Add "Wajib diisi!" error spans for required fields
$content = preg_replace('/(<input[^>]+required[^>]*>)/i', '$1 <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span>', $content);
$content = preg_replace('/(<select[^>]+required[^>]*>.*?<\/select>)/is', '$1 <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span>', $content);
$content = preg_replace('/(<textarea[^>]+required[^>]*>.*?<\/textarea>)/is', '$1 <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span>', $content);

file_put_contents($path, $content);
echo "Frontend fixes applied successfully!\n";

// ============================================
// BACKEND: Update PengirimanDokumenController@destroyBulk
// ============================================
$ctrlPath = 'app/Http/Controllers/PengirimanDokumenController.php';
$ctrlContent = file_get_contents($ctrlPath);

$searchDestroyBulk = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengiriman_dokumens,id',
        ]);

        \App\Models\PengirimanDokumen::whereIn('id', \$request->ids)->delete();

        return redirect()->back()->with('success', count(\$request->ids) . ' Data pengiriman dokumen berhasil dihapus.');
    }
PHP;

$replaceDestroyBulk = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        // Fitur Hapus Semua (Delete All Pages)
        if (\$request->input('delete_all_pages') == '1') {
            \$query = \App\Models\PengirimanDokumen::query();
            
            if (\$request->filled('tahun') && \$request->tahun !== 'semua') {
                \$query->where('tahun', \$request->tahun);
            }
            if (\$request->filled('bulan') && \$request->bulan !== 'semua') {
                \$query->where('bulan', \$request->bulan);
            }
            
            \$count = \$query->count();
            \$query->delete();
            
            return redirect()->back()->with('success', \$count . ' Data pengiriman dokumen (seluruh halaman) berhasil dihapus.');
        }

        // Hapus Massal Biasa
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengiriman_dokumen,id',
        ]);

        \App\Models\PengirimanDokumen::whereIn('id', \$request->ids)->delete();

        return redirect()->back()->with('success', count(\$request->ids) . ' Data pengiriman dokumen berhasil dihapus.');
    }
PHP;

$searchDestroyBulk = str_replace("\r\n", "\n", $searchDestroyBulk);
$replaceDestroyBulk = str_replace("\r\n", "\n", $replaceDestroyBulk);
$ctrlContent = str_replace("\r\n", "\n", $ctrlContent);
$ctrlContent = str_replace($searchDestroyBulk, $replaceDestroyBulk, $ctrlContent);

file_put_contents($ctrlPath, $ctrlContent);
echo "Backend Volume fixes applied successfully!\n";

// ============================================
// BACKEND: Update PengirimanDokumenOngkirController@destroyBulk
// ============================================
$ctrlPathOngkir = 'app/Http/Controllers/PengirimanDokumenOngkirController.php';
$ctrlContentOngkir = file_get_contents($ctrlPathOngkir);

$searchDestroyBulkOngkir = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengiriman_dokumen_ongkirs,id',
        ]);

        \App\Models\PengirimanDokumenOngkir::whereIn('id', \$request->ids)->delete();

        return redirect()->back()->with('success', count(\$request->ids) . ' Data tagihan ongkir berhasil dihapus.');
    }
PHP;

$replaceDestroyBulkOngkir = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        // Fitur Hapus Semua (Delete All Pages)
        if (\$request->input('delete_all_pages') == '1') {
            \$query = \App\Models\PengirimanDokumenOngkir::query();
            
            if (\$request->filled('tahun') && \$request->tahun !== 'semua') {
                \$query->where('tahun', \$request->tahun);
            }
            if (\$request->filled('bulan') && \$request->bulan !== 'semua') {
                \$query->where('bulan', \$request->bulan);
            }
            
            \$count = \$query->count();
            \$query->delete();
            
            return redirect()->back()->with('success', \$count . ' Data tagihan ongkir (seluruh halaman) berhasil dihapus.');
        }

        // Hapus Massal Biasa
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengiriman_dokumen_ongkir,id',
        ]);

        \App\Models\PengirimanDokumenOngkir::whereIn('id', \$request->ids)->delete();

        return redirect()->back()->with('success', count(\$request->ids) . ' Data tagihan ongkir berhasil dihapus.');
    }
PHP;

$searchDestroyBulkOngkir = str_replace("\r\n", "\n", $searchDestroyBulkOngkir);
$replaceDestroyBulkOngkir = str_replace("\r\n", "\n", $replaceDestroyBulkOngkir);
$ctrlContentOngkir = str_replace("\r\n", "\n", $ctrlContentOngkir);
$ctrlContentOngkir = str_replace($searchDestroyBulkOngkir, $replaceDestroyBulkOngkir, $ctrlContentOngkir);

file_put_contents($ctrlPathOngkir, $ctrlContentOngkir);
echo "Backend Ongkir fixes applied successfully!\n";
?>
