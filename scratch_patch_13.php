<?php

// ============================================
// 1. PATCH KETIDAKHADIRAN
// ============================================
$khPath = 'resources/views/ketidakhadiran/index.blade.php';
$khContent = file_get_contents($khPath);

$khJsNew = <<<JS
let isBulkMode = false;

function toggleBulkMode() {
    isBulkMode = !isBulkMode;
    const container = document.getElementById('tableContainer');
    const btnGroup = document.getElementById('btnGroup');
    if (container) {
        if (isBulkMode) {
            container.classList.remove('hide-bulk');
            if(btnGroup) {
                btnGroup.classList.remove('hidden');
                btnGroup.classList.add('flex');
            }
        } else {
            container.classList.add('hide-bulk');
            if(btnGroup) {
                btnGroup.classList.add('hidden');
                btnGroup.classList.remove('flex');
            }
            cancelAllBtnOnly();
        }
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.cb-bulk');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function toggleCheckbox() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.cb-bulk');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    if(selectAll) selectAll.checked = allChecked;
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.cb-bulk:checked').length;
    const countEl = document.getElementById('selectedCount');
    if (countEl) countEl.textContent = count;
}

function cancelAllBtnOnly() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) selectAll.checked = false;
    const checkboxes = document.querySelectorAll('.cb-bulk');
    checkboxes.forEach(cb => cb.checked = false);
    updateSelectedCount();
}

function cancelAll() {
    cancelAllBtnOnly();
    isBulkMode = false;
    const container = document.getElementById('tableContainer');
    if (container) container.classList.add('hide-bulk');
    const btnGroup = document.getElementById('btnGroup');
    if (btnGroup) {
        btnGroup.classList.add('hidden');
        btnGroup.classList.remove('flex');
    }
}
JS;

// Replace existing JS with the new one
$khContent = preg_replace('/let isBulkMode = false;[\s\S]*?(?=<script>|<\/script>|function openModal)/', '', $khContent); // clean up if exists
$khContent = preg_replace('/function toggleBulkMode\(\) \{[\s\S]*?function cancelAll\(\) \{[\s\S]*?\}[\s\S]*?\}/', '', $khContent);
$khContent = str_replace('</script>', $khJsNew . "\n</script>", $khContent);
file_put_contents($khPath, $khContent);


// ============================================
// 2. PATCH PROGRAM STRATEGIS
// ============================================
$psPath = 'resources/views/program-strategis.blade.php';
$psContent = file_get_contents($psPath);

// Replace btnGroupBulk span to include selectedCount
$psSpanOld = '<span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>';
$psSpanNew = '<span class="text-xs text-red-600 font-semibold flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg><span id="selectedCount">0</span> data terpilih untuk dihapus</span>';
$psContent = str_replace($psSpanOld, $psSpanNew, $psContent);

// Also check btnGroupBulk div to add flex properly
$psDivOld = 'id="btnGroupBulk" class="hidden flex justify-between';
$psDivNew = 'id="btnGroupBulk" class="hidden justify-between'; // removing flex so it doesn't conflict, wait, "hidden flex" is Tailwind conflict.
$psContent = str_replace($psDivOld, $psDivNew, $psContent);
$psContent = str_replace('id="btnGroupBulk" class="hidden flex items-center', 'id="btnGroupBulk" class="hidden items-center', $psContent);


$psJsNew = <<<JS
let isBulkMode = false;

function toggleBulkMode() {
    isBulkMode = !isBulkMode;
    const container = document.getElementById('tableContainerBulk');
    const btnGroup = document.getElementById('btnGroupBulk');
    if (container) {
        if (isBulkMode) {
            container.classList.remove('hide-bulk');
            if(btnGroup) {
                btnGroup.classList.remove('hidden');
                btnGroup.classList.add('flex');
            }
        } else {
            container.classList.add('hide-bulk');
            if(btnGroup) {
                btnGroup.classList.add('hidden');
                btnGroup.classList.remove('flex');
            }
            cancelAllBtnOnly();
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
    if(selectAll) selectAll.checked = allChecked;
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.cb-bulk:checked').length;
    const countEl = document.getElementById('selectedCount');
    if (countEl) countEl.textContent = count;
}

function cancelAllBtnOnly() {
    const selectAll = document.getElementById('selectAllBulk');
    if (selectAll) selectAll.checked = false;
    const checkboxes = document.querySelectorAll('.cb-bulk');
    checkboxes.forEach(cb => cb.checked = false);
    updateSelectedCount();
}

function cancelAll() {
    cancelAllBtnOnly();
    isBulkMode = false;
    const container = document.getElementById('tableContainerBulk');
    if (container) container.classList.add('hide-bulk');
    const btnGroup = document.getElementById('btnGroupBulk');
    if (btnGroup) {
        btnGroup.classList.add('hidden');
        btnGroup.classList.remove('flex');
    }
}
JS;

$psContent = preg_replace('/function toggleBulkMode\(\) \{[\s\S]*?function cancelAll\(\) \{[\s\S]*?\}[\s\S]*?\}/', '', $psContent);
$psContent = str_replace('</script>', $psJsNew . "\n</script>", $psContent);
file_put_contents($psPath, $psContent);

echo "Patched both blade files!\n";

?>
