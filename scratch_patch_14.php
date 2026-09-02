<?php

$path = 'resources/views/ketidakhadiran/index.blade.php';
$content = file_get_contents($path);

// 1. Remove the injected JS from the <script src="..."> blocks
// It looks like: <script src="https://cdn.jsdelivr.net/npm/chart.js">let isBulkMode = false; ... </script>
// I will just replace <script src="https://cdn.jsdelivr.net/npm/chart.js">...</script> with the clean one.
$content = preg_replace('/<script src="https:\/\/cdn\.jsdelivr\.net\/npm\/chart\.js">.*?<\/script>/s', '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>', $content);
$content = preg_replace('/<script src="https:\/\/cdn\.jsdelivr\.net\/npm\/chartjs-plugin-datalabels@2\.2\.0">.*?<\/script>/s', '<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>', $content);

// 2. Remove all occurrences of the JS block from the file
$jsBulkFunctionsRegex = '/let isBulkMode = false;[\s\S]*?function cancelAll\(\) \{[\s\S]*?\}\s*\}/';
$content = preg_replace($jsBulkFunctionsRegex, '', $content);

// 3. Remove the old duplicate toggleCheckbox/toggleDeleteButton/cancelAll logic if it exists
$oldLogicRegex = '/function toggleCheckbox\(\) \{[\s\S]*?function cancelAll\(\) \{[\s\S]*?\}\s*\}/';
$content = preg_replace($oldLogicRegex, '', $content);

// 4. Also there's a loose <script> block at the end that might just be empty now.
// Let's clean up empty script tags
$content = preg_replace('/<script>\s*<\/script>/', '', $content);

// 5. Now inject the clean JS block ONCE right before @endsection
$khJsNew = <<<JS
<script>
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
</script>
@endsection
JS;

$content = str_replace('@endsection', $khJsNew, $content);

file_put_contents($path, $content);
echo "Cleaned up and fixed JS in ketidakhadiran/index.blade.php\n";

?>
