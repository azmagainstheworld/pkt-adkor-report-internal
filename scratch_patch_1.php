<?php

function patchKaryawan() {
    $content = file_get_contents('resources/views/karyawan.blade.php');

    // 1. Rename 'Mode Hapus Massal' to 'Hapus semua'
    $content = str_replace('Mode Hapus Massal', 'Hapus semua', $content);

    // 2. Add form and btnGroup for bulk delete
    // Find the x-card for table
    if (strpos($content, '<form id="bulkDeleteForm"') === false) {
        $xcardPos = strpos($content, '<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100">');
        if ($xcardPos !== false) {
            $bulkFormHtml = <<<HTML
    <form id="bulkDeleteForm" action="{{ route('karyawan.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data karyawan terpilih beserta keluarganya?')">
        @csrf
        @method('DELETE')
        
        <div id="btnGroup" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 rounded-t-xl border-b border-red-100">
            <span class="text-xs text-red-600 font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span id="selectedCount">0</span> data terpilih untuk dihapus
            </span>
            <div class="flex gap-2">
                <button type="button" onclick="cancelAll()" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm flex items-center gap-1.5">
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 hide-bulk" id="tableContainerBulk">
HTML;
            $content = str_replace('<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100">', $bulkFormHtml, $content);
            
            // Close form after x-card
            $content = str_replace("</x-card>\n\n    <x-delete-modal", "</x-card>\n    </form>\n\n    <x-delete-modal", $content);
        }
    }

    // 3. Add checkboxes to table
    // Header
    $content = str_replace("\$headers = ['No', 'Nama',", "\$headers = ['<input type=\"checkbox\" id=\"selectAllBulk\" onclick=\"toggleSelectAll()\">', 'No', 'Nama',", $content);
    
    // Row
    $rowCheckbox = <<<HTML
                    <td class="px-3 py-2 text-center align-middle">
                        <input type="checkbox" name="ids[]" class="cb-bulk" value="{{ \$item->id }}" onclick="toggleCheckbox()">
                    </td>
                    <td class="px-6 py-4 text-center font-medium text-gray-500">
HTML;
    $content = preg_replace('/<td class="px-6 py-4 text-center font-medium text-gray-500">\s*\{\{ \$karyawan->firstItem\(\) \+ \$index \}\}\s*<\/td>/', $rowCheckbox . "\n                        {{ \$karyawan->firstItem() + \$index }}\n                    </td>", $content);

    // 4. Remove Atur Kolom Keluarga from dropdown
    $content = preg_replace('/<button type="button" onclick="openModal\(\'modalAturKolomKeluarga\'\).*?Atur Kolom Keluarga\s*<\/button>/s', '', $content);

    // 5. Enhance openModal to be safe
    $content = str_replace("function openModal(id) { document.getElementById(id).classList.remove('hidden'); }", "function openModal(id) { const el = document.getElementById(id); if(el) el.classList.remove('hidden'); else console.error('Modal not found:', id); }", $content);
    
    // 6. Ensure bulk delete logic exists in script
    if (strpos($content, 'function toggleBulkMode') === false) {
        $bulkScript = <<<JAVASCRIPT
    let isBulkMode = false;
    function toggleBulkMode() {
        isBulkMode = !isBulkMode;
        const container = document.getElementById('tableContainerBulk');
        const btnGroup = document.getElementById('btnGroup');
        if (container) {
            if (isBulkMode) {
                container.classList.remove('hide-bulk');
                if(btnGroup) btnGroup.classList.remove('hidden');
            } else {
                container.classList.add('hide-bulk');
                if(btnGroup) btnGroup.classList.add('hidden');
                cancelAll();
            }
        }
    }
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAllBulk');
        const checkboxes = document.querySelectorAll('.cb-bulk');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelectedCount();
    }
    function toggleCheckbox() {
        const selectAll = document.getElementById('selectAllBulk');
        const checkboxes = document.querySelectorAll('.cb-bulk');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        selectAll.checked = allChecked;
        updateSelectedCount();
    }
    function updateSelectedCount() {
        const count = document.querySelectorAll('.cb-bulk:checked').length;
        const countEl = document.getElementById('selectedCount');
        if (countEl) countEl.textContent = count;
    }
    function cancelAll() {
        const selectAll = document.getElementById('selectAllBulk');
        if(selectAll) selectAll.checked = false;
        document.querySelectorAll('.cb-bulk').forEach(cb => cb.checked = false);
        updateSelectedCount();
        isBulkMode = false;
        const container = document.getElementById('tableContainerBulk');
        if (container) container.classList.add('hide-bulk');
        const btnGroup = document.getElementById('btnGroup');
        if (btnGroup) btnGroup.classList.add('hidden');
    }
JAVASCRIPT;
        $content = str_replace('// --- DROPDOWN TOGGLE LOGIC ---', $bulkScript . "\n\n    // --- DROPDOWN TOGGLE LOGIC ---", $content);
    }

    file_put_contents('resources/views/karyawan.blade.php', $content);
    echo "karyawan.blade.php patched.\n";
}

function patchKeluarga() {
    // Check if keluarga.blade.php exists
    $path = 'resources/views/keluarga.blade.php';
    if (!file_exists($path)) {
        // Maybe it's inside a folder? Wait, the user said "tabelkeluarga agar berfungsi kembali"
        // Let's find it.
        echo "keluarga.blade.php not found in resources/views/\n";
        return;
    }
    $content = file_get_contents($path);
    // Enhance openModal
    $content = str_replace("function openModal(id) { document.getElementById(id).classList.remove('hidden'); }", "function openModal(id) { const el = document.getElementById(id); if(el) el.classList.remove('hidden'); else console.error('Modal not found:', id); }", $content);
    file_put_contents($path, $content);
    echo "keluarga.blade.php patched.\n";
}

function patchNavbar() {
    // Let's find navbar in layouts
    $path = 'resources/views/layouts/app.blade.php';
    if (file_exists($path)) {
        $content = file_get_contents($path);
        // Fix duplicate close logo in search bar
        // Will examine this manually later if needed, or try to fix here
        // usually it's `<button ... clear-search> <svg x-icon> </button>`
        echo "Please check search bar in $path\n";
    }
}

patchKaryawan();
patchKeluarga();
patchNavbar();
