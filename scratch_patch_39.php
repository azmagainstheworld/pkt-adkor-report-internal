<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// 1. Clean up button groups to have ONLY "Batal" and "Hapus Terpilih"
// This is the clean structure we want:
$cleanBtnGroup = <<<EOD
<div class="flex gap-2">
                    <button type="button" onclick="cancelAll%s()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
EOD;

// We use regex to replace whatever is inside <div class="flex gap-2">...</div> in both groups
$content = preg_replace('/<div class="flex gap-2">\s*<button type="button" onclick="cancelAllTerbit\(\)".*?<\/div>/s', sprintf($cleanBtnGroup, 'Terbit'), $content);
$content = preg_replace('/<div class="flex gap-2">\s*<button type="button" onclick="cancelAllProses\(\)".*?<\/div>/s', sprintf($cleanBtnGroup, 'Proses'), $content);

// 2. Add hidden inputs for delete_all_pages
// We will replace @method('DELETE') with @method('DELETE') + hidden input
$methodDeleteTerbitSearch = "@method('DELETE')\n            <input type=\"hidden\" name=\"filter_tahun\"";
$methodDeleteTerbitReplace = "@method('DELETE')\n            <input type=\"hidden\" name=\"delete_all_pages\" id=\"deleteAllFlagTerbit\" value=\"0\">\n            <input type=\"hidden\" name=\"filter_tahun\"";
$content = str_replace($methodDeleteTerbitSearch, $methodDeleteTerbitReplace, $content);

$methodDeleteProsesSearch = "@method('DELETE')\n              <input type=\"hidden\" name=\"filter_tahun\"";
$methodDeleteProsesReplace = "@method('DELETE')\n            <input type=\"hidden\" name=\"delete_all_pages\" id=\"deleteAllFlagProses\" value=\"0\">\n              <input type=\"hidden\" name=\"filter_tahun\"";
// It seems earlier it was added without extra spaces, let's use a simpler replace
$methodDeleteSearch = "@method('DELETE')";
$methodDeleteTerbitCount = 0;
// We know there are two @method('DELETE') inside the forms. Let's just find the forms and insert the input right after them.
$formTerbitPattern = '/(<form id="bulkDeleteFormTerbit".*?>.*?@method\(\'DELETE\'\))/s';
$content = preg_replace($formTerbitPattern, '$1' . "\n            <input type=\"hidden\" name=\"delete_all_pages\" id=\"deleteAllFlagTerbit\" value=\"0\">", $content);

$formProsesPattern = '/(<form id="bulkDeleteFormProses".*?>.*?@method\(\'DELETE\'\))/s';
$content = preg_replace($formProsesPattern, '$1' . "\n            <input type=\"hidden\" name=\"delete_all_pages\" id=\"deleteAllFlagProses\" value=\"0\">", $content);

// Remove duplicates if we accidentally added it twice
$content = preg_replace('/(<input type="hidden" name="delete_all_pages" id="deleteAllFlagTerbit" value="0">\s*){2,}/s', '<input type="hidden" name="delete_all_pages" id="deleteAllFlagTerbit" value="0">', $content);
$content = preg_replace('/(<input type="hidden" name="delete_all_pages" id="deleteAllFlagProses" value="0">\s*){2,}/s', '<input type="hidden" name="delete_all_pages" id="deleteAllFlagProses" value="0">', $content);

// 3. Revert toggleDeleteBtn to basic visibility toggling
$jsTerbitBtnSearch = '/function toggleDeleteBtnTerbit\(\) \{.*?\}\s*\}/s';
$jsTerbitBtnReplace = <<<EOD
function toggleDeleteBtnTerbit() {
            let group = document.getElementById("btnGroupBulkTerbit");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-terbit:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;
$content = preg_replace($jsTerbitBtnSearch, $jsTerbitBtnReplace, $content);

$jsProsesBtnSearch = '/function toggleDeleteBtnProses\(\) \{.*?\}\s*\}/s';
$jsProsesBtnReplace = <<<EOD
function toggleDeleteBtnProses() {
            let group = document.getElementById("btnGroupBulkProses");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-proses:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;
$content = preg_replace($jsProsesBtnSearch, $jsProsesBtnReplace, $content);

// 4. Update toggleSelectAll and toggleCheckbox to manage the hidden input value
$jsTerbitSelectAllSearch = '/function toggleSelectAllTerbit\(\) \{.*?\}/s';
$jsTerbitSelectAllReplace = <<<EOD
function toggleSelectAllTerbit() {
            let selectAll = document.getElementById("selectAllBulkTerbit");
            let checkboxes = document.querySelectorAll(".cb-bulk-terbit");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            let flag = document.getElementById("deleteAllFlagTerbit");
            if (flag) flag.value = selectAll.checked ? "1" : "0";
            toggleDeleteBtnTerbit();
        }
EOD;
$content = preg_replace($jsTerbitSelectAllSearch, $jsTerbitSelectAllReplace, $content);

$jsTerbitCheckboxSearch = '/function toggleCheckboxTerbit\(\) \{.*?\}/s';
$jsTerbitCheckboxReplace = <<<EOD
function toggleCheckboxTerbit() {
            let selectAll = document.getElementById("selectAllBulkTerbit");
            let checkboxes = document.querySelectorAll(".cb-bulk-terbit");
            let allChecked = Array.from(checkboxes).every(cb => cb.checked);
            selectAll.checked = allChecked;
            let flag = document.getElementById("deleteAllFlagTerbit");
            if (flag) flag.value = allChecked ? "1" : "0";
            toggleDeleteBtnTerbit();
        }
EOD;
$content = preg_replace($jsTerbitCheckboxSearch, $jsTerbitCheckboxReplace, $content);

$jsProsesSelectAllSearch = '/function toggleSelectAllProses\(\) \{.*?\}/s';
$jsProsesSelectAllReplace = <<<EOD
function toggleSelectAllProses() {
            let selectAll = document.getElementById("selectAllBulkProses");
            let checkboxes = document.querySelectorAll(".cb-bulk-proses");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            let flag = document.getElementById("deleteAllFlagProses");
            if (flag) flag.value = selectAll.checked ? "1" : "0";
            toggleDeleteBtnProses();
        }
EOD;
// Wait! toggleSelectAllProses currently does: selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
// This is WRONG! toggleSelectAll should SET checkboxes to match selectAll!
$jsProsesSelectAllReplaceCorrect = <<<EOD
function toggleSelectAllProses() {
            let selectAll = document.getElementById("selectAllBulkProses");
            let checkboxes = document.querySelectorAll(".cb-bulk-proses");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            let flag = document.getElementById("deleteAllFlagProses");
            if (flag) flag.value = selectAll.checked ? "1" : "0";
            toggleDeleteBtnProses();
        }
EOD;
$content = preg_replace($jsProsesSelectAllSearch, $jsProsesSelectAllReplaceCorrect, $content);

$jsProsesCheckboxSearch = '/function toggleCheckboxProses\(\) \{.*?\}/s';
$jsProsesCheckboxReplace = <<<EOD
function toggleCheckboxProses() {
            let selectAll = document.getElementById("selectAllBulkProses");
            let checkboxes = document.querySelectorAll(".cb-bulk-proses");
            let allChecked = Array.from(checkboxes).every(cb => cb.checked);
            selectAll.checked = allChecked;
            let flag = document.getElementById("deleteAllFlagProses");
            if (flag) flag.value = allChecked ? "1" : "0";
            toggleDeleteBtnProses();
        }
EOD;
$content = preg_replace($jsProsesCheckboxSearch, $jsProsesCheckboxReplace, $content);

file_put_contents($path, $content);
echo "Blade completely patched!\n";
?>
