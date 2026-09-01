<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\bar-sk-memo.blade.php';
$content = file_get_contents($file);

$js = <<<JS

    // ==========================================
    // JS HAPUS MASSAL (BULK DELETE)
    // ==========================================
    function toggleBulkMode(tipe) {
        let container = document.getElementById("tableContainer" + (tipe === "terbit" ? "Terbit" : "Proses"));
        if(container.classList.contains("hide-bulk-" + tipe)) {
            container.classList.remove("hide-bulk-" + tipe);
        } else {
            container.classList.add("hide-bulk-" + tipe);
            cancelAll(tipe);
        }
    }
    
    function toggleSelectAll(tipe) {
        let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
        let checkboxes = document.querySelectorAll(".cb-" + tipe);
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        toggleDeleteBtn(tipe);
    }
    
    function toggleCheckbox(tipe) {
        let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
        let checkboxes = document.querySelectorAll(".cb-" + tipe);
        selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
        toggleDeleteBtn(tipe);
    }
    
    function toggleDeleteBtn(tipe) {
        let group = document.getElementById("btnGroup" + (tipe === "terbit" ? "Terbit" : "Proses"));
        if(group) {
            let checked = document.querySelectorAll(".cb-" + tipe + ":checked").length > 0;
            if(checked) group.classList.remove("hidden"); else group.classList.add("hidden");
        }
    }
    
    function cancelAll(tipe) {
        let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
        if(selectAll) selectAll.checked = false;
        let checkboxes = document.querySelectorAll(".cb-" + tipe);
        checkboxes.forEach(cb => cb.checked = false);
        toggleDeleteBtn(tipe);
    }
JS;

// Append just before </script>
$content = str_replace('</script>', $js . "\n</script>", $content);

file_put_contents($file, $content);
echo "JavaScript successfully injected!\n";
