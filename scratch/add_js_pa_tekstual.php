<?php

$file = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$content = file_get_contents($file);

$js = <<<BLADE

    // ================= BULK DELETE TABEL 1 =================
    function toggleBulkMode1() {
        let container = document.getElementById("tableContainerBulk1");
        let btnGroup = document.getElementById("btnGroupBulk1");
        let isBulk = container.classList.contains("bulk-mode");
        
        if (isBulk) {
            cancelAll1();
            container.classList.remove("bulk-mode");
            btnGroup.classList.add("hidden");
            
            document.querySelectorAll(".bulk-cb-header-1").forEach(el => el.classList.add('hidden'));
            document.querySelectorAll(".cb-bulk-1").forEach(el => el.classList.add('hidden'));
        } else {
            container.classList.add("bulk-mode");
            
            document.querySelectorAll(".bulk-cb-header-1").forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll(".cb-bulk-1").forEach(el => el.classList.remove('hidden'));
        }
    }
    
    function toggleSelectAll1() {
        let selectAll = document.getElementById("selectAllBulk1");
        let checkboxes = document.querySelectorAll(".cb-bulk-1");
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        
        let deleteAllInput = document.getElementById("deleteAll1");
        if (deleteAllInput) {
            deleteAllInput.value = selectAll.checked ? '1' : '0';
        }
        toggleDeleteBtn1();
    }
    
    function toggleCheckbox1() {
        let selectAll = document.getElementById("selectAllBulk1");
        let checkboxes = document.querySelectorAll(".cb-bulk-1");
        selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
        
        let deleteAllInput = document.getElementById("deleteAll1");
        if (deleteAllInput) {
            deleteAllInput.value = '0';
        }
        toggleDeleteBtn1();
    }
    
    function toggleDeleteBtn1() {
        let group = document.getElementById("btnGroupBulk1");
        if (group) {
            let checked = document.querySelectorAll(".cb-bulk-1:checked").length > 0;
            if (checked) group.classList.remove("hidden");
            else group.classList.add("hidden");
        }
    }
    
    function cancelAll1() {
        let selectAll = document.getElementById("selectAllBulk1");
        if (selectAll) selectAll.checked = false;
        let checkboxes = document.querySelectorAll(".cb-bulk-1");
        checkboxes.forEach(cb => cb.checked = false);
        toggleDeleteBtn1();
    }

    // ================= BULK DELETE TABEL 2 =================
    function toggleBulkMode2() {
        let container = document.getElementById("tableContainerBulk2");
        let btnGroup = document.getElementById("btnGroupBulk2");
        let isBulk = container.classList.contains("bulk-mode");
        
        if (isBulk) {
            cancelAll2();
            container.classList.remove("bulk-mode");
            btnGroup.classList.add("hidden");
            
            document.querySelectorAll(".bulk-cb-header-2").forEach(el => el.classList.add('hidden'));
            document.querySelectorAll(".cb-bulk-2").forEach(el => el.classList.add('hidden'));
        } else {
            container.classList.add("bulk-mode");
            
            document.querySelectorAll(".bulk-cb-header-2").forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll(".cb-bulk-2").forEach(el => el.classList.remove('hidden'));
        }
    }
    
    function toggleSelectAll2() {
        let selectAll = document.getElementById("selectAllBulk2");
        let checkboxes = document.querySelectorAll(".cb-bulk-2");
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        
        let deleteAllInput = document.getElementById("deleteAll2");
        if (deleteAllInput) {
            deleteAllInput.value = selectAll.checked ? '1' : '0';
        }
        toggleDeleteBtn2();
    }
    
    function toggleCheckbox2() {
        let selectAll = document.getElementById("selectAllBulk2");
        let checkboxes = document.querySelectorAll(".cb-bulk-2");
        selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
        
        let deleteAllInput = document.getElementById("deleteAll2");
        if (deleteAllInput) {
            deleteAllInput.value = '0';
        }
        toggleDeleteBtn2();
    }
    
    function toggleDeleteBtn2() {
        let group = document.getElementById("btnGroupBulk2");
        if (group) {
            let checked = document.querySelectorAll(".cb-bulk-2:checked").length > 0;
            if (checked) group.classList.remove("hidden");
            else group.classList.add("hidden");
        }
    }
    
    function cancelAll2() {
        let selectAll = document.getElementById("selectAllBulk2");
        if (selectAll) selectAll.checked = false;
        let checkboxes = document.querySelectorAll(".cb-bulk-2");
        checkboxes.forEach(cb => cb.checked = false);
        toggleDeleteBtn2();
    }
</script>
BLADE;

$content = str_replace('</script>', $js, $content);
file_put_contents($file, $content);
echo "JS added.\n";
