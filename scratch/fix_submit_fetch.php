<?php
// Found it! The x-table component has its OWN <div class="overflow-x-auto"> wrapper.
// And the blade page ALSO has <div id="tableContainerBulk1" class="hide-bulk overflow-x-auto">
// 
// The REAL issue: the FORM wraps the container div which is OUTSIDE the table.
// But the checkboxes are INSIDE the x-table's own div wrapper.
// 
// HTML spec: a <form> wrapping a <div> which contains a <table> with <input>s is VALID.
// The inputs belong to the parent form.
//
// So the HTML structure should work. The issue must be that document.getElementById('bulkDeleteForm1') 
// is returning null or the wrong element. 
//
// Wait - let me re-read the error: "The GET method is not supported for route..."
// This means the BROWSER is making a GET request to that URL.
// This ONLY happens if:
// 1. An <a> tag or some navigation link is going to that URL (GET)
// 2. The form.submit() is finding a DIFFERENT form (one with method=GET) and submitting that
// 3. The form itself has method="GET" 
//
// Actually, the fallback route we added should now prevent the 405 error.
// But the real fix is to ensure the DELETE method is sent properly.
//
// Let's change the approach: instead of using form.submit(), use fetch() with the DELETE method
// so we guarantee the correct HTTP method is used.

$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

// Replace submitBulkDelete1 with a fetch-based approach
$oldSubmit1 = 'function submitBulkDelete1() {
            let selectAll = document.getElementById("selectAllBulk1");
            let form = document.getElementById("bulkDeleteForm1");
            if (!form) { alert("Form tidak ditemukan!"); return; }
            if (selectAll && selectAll.checked) {
                document.getElementById("deleteAllFlag1").value = "1";
            } else {
                document.getElementById("deleteAllFlag1").value = "0";
            }
            form.submit();
        }';

$newSubmit1 = 'function submitBulkDelete1() {
            let selectAll = document.getElementById("selectAllBulk1");
            if (selectAll && selectAll.checked) {
                document.getElementById("deleteAllFlag1").value = "1";
            } else {
                document.getElementById("deleteAllFlag1").value = "0";
            }
            let form = document.getElementById("bulkDeleteForm1");
            let formData = new FormData(form);
            // Gather all checked checkboxes in table1 (they\'re outside the form due to table restrictions)
            document.querySelectorAll(".cb-bulk-1:checked").forEach(function(cb) {
                formData.append("ids[]", cb.value);
            });
            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" }
            }).then(function(res) {
                if (res.redirected) { window.location.href = res.url; }
                else { window.location.reload(); }
            }).catch(function() { form.submit(); });
        }';

$oldSubmit2 = 'function submitBulkDelete2() {
            let selectAll = document.getElementById("selectAllBulk2");
            let form = document.getElementById("bulkDeleteForm2");
            if (!form) { alert("Form tidak ditemukan!"); return; }
            if (selectAll && selectAll.checked) {
                document.getElementById("deleteAllFlag2").value = "1";
            } else {
                document.getElementById("deleteAllFlag2").value = "0";
            }
            form.submit();
        }';

$newSubmit2 = 'function submitBulkDelete2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll && selectAll.checked) {
                document.getElementById("deleteAllFlag2").value = "1";
            } else {
                document.getElementById("deleteAllFlag2").value = "0";
            }
            let form = document.getElementById("bulkDeleteForm2");
            let formData = new FormData(form);
            document.querySelectorAll(".cb-bulk-2:checked").forEach(function(cb) {
                formData.append("ids[]", cb.value);
            });
            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" }
            }).then(function(res) {
                if (res.redirected) { window.location.href = res.url; }
                else { window.location.reload(); }
            }).catch(function() { form.submit(); });
        }';

$content = str_replace($oldSubmit1, $newSubmit1, $content);
$content = str_replace($oldSubmit2, $newSubmit2, $content);
file_put_contents($file, $content);
echo "Updated submitBulkDelete to use fetch() with explicit form data collection.\n";
