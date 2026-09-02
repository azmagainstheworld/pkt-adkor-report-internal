<?php
$path = 'resources/views/components/delete-modal.blade.php';
$content = file_get_contents($path);

$searchScript = <<<JS
<script>
    function openDeleteModal(modalId, deleteUrl) {
        const modal = document.getElementById(modalId);
        const form = modal.querySelector('form');
        form.action = deleteUrl;
        modal.classList.remove('hidden');
    }
</script>
JS;

$replaceScript = <<<JS
<script>
    if (typeof window.openDeleteModal !== 'function') {
        window.openDeleteModal = function(modalId, deleteUrl) {
            const modal = document.getElementById(modalId);
            if (!modal) { console.error("Modal not found: " + modalId); return; }
            const form = document.getElementById('formDelete_' + modalId) || modal.querySelector('form');
            if (!form) { console.error("Form not found inside modal: " + modalId); return; }
            form.action = deleteUrl;
            modal.classList.remove('hidden');
        };
    }
</script>
JS;

$content = str_replace(str_replace("\r\n", "\n", $searchScript), str_replace("\r\n", "\n", $replaceScript), $content);
file_put_contents($path, $content);
echo "delete-modal.blade.php updated!\n";
?>
