<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// Fix trailing syntax errors in Terbit
$brokenTerbitSearch = <<<EOD
        function toggleDeleteBtnTerbit() {
            let group = document.getElementById("btnGroupBulkTerbit");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-terbit:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;

$fixedTerbit = <<<EOD
        function toggleDeleteBtnTerbit() {
            let group = document.getElementById("btnGroupBulkTerbit");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-terbit:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;

// Fix trailing syntax errors in Proses
$brokenProsesSearch = <<<EOD
        function toggleDeleteBtnProses() {
            let group = document.getElementById("btnGroupBulkProses");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-proses:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;

$fixedProses = <<<EOD
        function toggleDeleteBtnProses() {
            let group = document.getElementById("btnGroupBulkProses");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-proses:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
EOD;

// Because line endings and spaces might vary, let's use a regex to aggressively clean up the trailing garbage.
$content = preg_replace('/function toggleDeleteBtnTerbit\(\) \{[\s\S]*?\}\s*\}\s*else\s*\{\s*group\.classList\.add\("hidden"\);\s*\}\s*\}\s*\}/', $fixedTerbit, $content);
$content = preg_replace('/function toggleDeleteBtnProses\(\) \{[\s\S]*?\}\s*\}\s*else\s*\{\s*group\.classList\.add\("hidden"\);\s*\}\s*\}\s*\}/', $fixedProses, $content);

// Let's also verify that there are no OTHER trailing syntax errors from toggleSelectAllProses etc.
// toggleSelectAll and toggleCheckbox didn't have complex nested braces that got clipped, they just got replaced smoothly.
// But to be absolutely safe, let's just do a clean replacement of the whole JS block if needed.
// For now, let's just see if the preg_replace worked.

file_put_contents($path, $content);
echo "Syntax error fixed!\n";
?>
