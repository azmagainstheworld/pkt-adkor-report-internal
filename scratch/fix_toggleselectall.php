<?php
$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

// Fix toggleSelectAll1: when header checkbox is checked → set deleteAllFlag1=1 and immediately submit
$old1 = 'function toggleSelectAll1() {
              let selectAll = document.getElementById("selectAllBulk1");
              let checkboxes = document.querySelectorAll(".cb-bulk-1");
              checkboxes.forEach(cb => cb.checked = selectAll.checked);
              toggleDeleteBtn1();
          }';
$new1 = 'function toggleSelectAll1() {
              let selectAll = document.getElementById("selectAllBulk1");
              let checkboxes = document.querySelectorAll(".cb-bulk-1");
              checkboxes.forEach(cb => cb.checked = selectAll.checked);
              if (selectAll.checked) {
                  // Mark delete_all = 1 agar semua data di tabel terhapus (termasuk halaman lain)
                  document.getElementById("deleteAllFlag1").value = "1";
                  // Langsung hapus semua tanpa perlu tekan tombol lagi
                  let form = document.getElementById("bulkDeleteForm1");
                  let formData = new FormData(form);
                  fetch(form.action, {
                      method: "POST",
                      body: formData,
                      headers: { "X-Requested-With": "XMLHttpRequest" }
                  }).then(function(res) {
                      if (res.redirected) { window.location.href = res.url; }
                      else { window.location.reload(); }
                  }).catch(function() { form.submit(); });
              } else {
                  document.getElementById("deleteAllFlag1").value = "0";
                  toggleDeleteBtn1();
              }
          }';

// Fix toggleSelectAll2 too
$old2 = 'function toggleSelectAll2() {
              let selectAll = document.getElementById("selectAllBulk2");
              let checkboxes = document.querySelectorAll(".cb-bulk-2");
              checkboxes.forEach(cb => cb.checked = selectAll.checked);
              toggleDeleteBtn2();
          }';
$new2 = 'function toggleSelectAll2() {
              let selectAll = document.getElementById("selectAllBulk2");
              let checkboxes = document.querySelectorAll(".cb-bulk-2");
              checkboxes.forEach(cb => cb.checked = selectAll.checked);
              if (selectAll.checked) {
                  document.getElementById("deleteAllFlag2").value = "1";
                  let form = document.getElementById("bulkDeleteForm2");
                  let formData = new FormData(form);
                  fetch(form.action, {
                      method: "POST",
                      body: formData,
                      headers: { "X-Requested-With": "XMLHttpRequest" }
                  }).then(function(res) {
                      if (res.redirected) { window.location.href = res.url; }
                      else { window.location.reload(); }
                  }).catch(function() { form.submit(); });
              } else {
                  document.getElementById("deleteAllFlag2").value = "0";
                  toggleDeleteBtn2();
              }
          }';

$newContent = str_replace($old1, $new1, $content);
$newContent = str_replace($old2, $new2, $newContent);

if ($newContent === $content) {
    echo "WARNING: No replacement made. Pattern not found exactly.\n";
    // Try trimming-based search
    $count1 = substr_count($content, 'function toggleSelectAll1()');
    $count2 = substr_count($content, 'function toggleSelectAll2()');
    echo "Found toggleSelectAll1: $count1 times\n";
    echo "Found toggleSelectAll2: $count2 times\n";
} else {
    file_put_contents($file, $newContent);
    echo "toggleSelectAll1 and toggleSelectAll2 updated.\n";
}
