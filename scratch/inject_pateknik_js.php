<?php

$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

$js = <<<JS
        // ================= BULK DELETE TABEL 1 =================
        function toggleBulkMode1() {
            let container = document.getElementById("tableContainerBulk1");
            let btn = document.getElementById("btnModeBulk1");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll1();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll1() {
            let selectAll = document.getElementById("selectAllBulk1");
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtn1();
        }
        function toggleCheckbox1() {
            let selectAll = document.getElementById("selectAllBulk1");
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn1();
        }
        function toggleDeleteBtn1() {
            let group = document.getElementById("btnGroupBulk1");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-1:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll1() {
            let selectAll = document.getElementById("selectAllBulk1");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-1");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn1();
            let container = document.getElementById("tableContainerBulk1");
            if (container && !container.classList.contains("hide-bulk")) {
                toggleBulkMode1();
            }
        }

        // ================= BULK DELETE TABEL 2 =================
        function toggleBulkMode2() {
            let container = document.getElementById("tableContainerBulk2");
            let btn = document.getElementById("btnModeBulk2");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll2();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll2() {
            let selectAll = document.getElementById("selectAllBulk2");
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtn2();
        }
        function toggleCheckbox2() {
            let selectAll = document.getElementById("selectAllBulk2");
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn2();
        }
        function toggleDeleteBtn2() {
            let group = document.getElementById("btnGroupBulk2");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk-2:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll2() {
            let selectAll = document.getElementById("selectAllBulk2");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk-2");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn2();
            let container = document.getElementById("tableContainerBulk2");
            if (container && !container.classList.contains("hide-bulk")) {
                toggleBulkMode2();
            }
        }
JS;

if (strpos($content, 'function toggleBulkMode1') === false) {
    // Insert just before the last </script>
    $content = preg_replace('/(<\/script>\s*@endsection)/s', $js . "\n$1", $content);
}

file_put_contents($file, $content);
echo "Injected JS into kearsipan-pa-teknik.blade.php\n";
