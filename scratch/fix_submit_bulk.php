<?php
// The real issue: form.submit() in JS ignores the <input type="hidden" name="_method" value="DELETE">
// because the form's method="POST" is rendered by Laravel Blade, but the browser
// might not recognize _method spoofing with form.submit() in some edge cases.
//
// Actually no - the problem is more subtle. When we look at what's happening:
// The form method is "POST" and has @method('DELETE') which renders:
// <input type="hidden" name="_method" value="DELETE">
// Laravel reads _method to determine the HTTP method.
// So a POST to bulk-destroy with _method=DELETE should work.
//
// BUT the error says GET - which means the form.submit() is NOT being called,
// OR the browser is navigating directly to the URL.
//
// Most likely: the submitBulkDelete1() call is failing (maybe because form ID not found)
// and falling back to some default behavior, OR there's an anchor tag wrapping it.
//
// The SAFEST fix is to change submitBulkDelete to use fetch() to POST the form data.

$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

// Replace submitBulkDelete1 and submitBulkDelete2 with fetch-based version
$oldSubmit1 = 'function submitBulkDelete1() {
            let selectAll = document.getElementById("selectAllBulk1");
            if (selectAll && selectAll.checked) {
                document.getElementById(\'deleteAllFlag1\').value = "1";
            } else {
                document.getElementById(\'deleteAllFlag1\').value = "0";
            }
            document.getElementById(\'bulkDeleteForm1\').submit();
        }';

$newSubmit1 = 'function submitBulkDelete1() {
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

$oldSubmit2 = 'function submitBulkDelete2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll && selectAll.checked) {
                document.getElementById(\'deleteAllFlag2\').value = "1";
            } else {
                document.getElementById(\'deleteAllFlag2\').value = "0";
            }
            document.getElementById(\'bulkDeleteForm2\').submit();
        }';

$newSubmit2 = 'function submitBulkDelete2() {
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

// Also, let's verify the hidden inputs are inside the form by checking positions
$formStart = strpos($content, 'id="bulkDeleteForm1"');
$formEnd = strpos($content, '</form>', strpos($content, 'id="tableContainerBulk1"'));
$deleteAllPos = strpos($content, 'id="deleteAllFlag1"');

echo "Form1 start: $formStart\n";
echo "deleteAllFlag1 pos: $deleteAllPos\n";
echo "Form1 end: $formEnd\n";
echo "deleteAllFlag1 inside form1: " . ($deleteAllPos > $formStart && $deleteAllPos < $formEnd ? "YES" : "NO") . "\n";

$content = str_replace($oldSubmit1, $newSubmit1, $content);
$content = str_replace($oldSubmit2, $newSubmit2, $content);
file_put_contents($file, $content);

echo "\nsubmitBulkDelete functions updated with debug check.\n";
