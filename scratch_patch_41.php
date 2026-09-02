<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// Clean up duplicate delete_all_pages in bulkDeleteFormProses
$formProsesSearch = <<<EOD
<input type="hidden" name="delete_all_pages" id="deleteAllFlagProses" value="0">
            <input type="hidden" name="delete_all_pages" id="deleteAllFlagTerbit" value="0">
EOD;

$formProsesReplace = <<<EOD
<input type="hidden" name="delete_all_pages" id="deleteAllFlagProses" value="0">
EOD;

// Because line endings might vary, we can use preg_replace
$content = preg_replace('/<input type="hidden" name="delete_all_pages" id="deleteAllFlagProses" value="0">\s*<input type="hidden" name="delete_all_pages" id="deleteAllFlagTerbit" value="0">/', $formProsesReplace, $content);

// Just to be extremely robust, let's remove ALL delete_all_pages from both forms and re-inject them cleanly.
$content = preg_replace('/<input type="hidden" name="delete_all_pages".*?>\s*/', '', $content);

$formTerbitPattern = '/(<form id="bulkDeleteFormTerbit".*?>.*?@method\(\'DELETE\'\))/s';
$content = preg_replace($formTerbitPattern, '$1' . "\n            <input type=\"hidden\" name=\"delete_all_pages\" id=\"deleteAllFlagTerbit\" value=\"0\">", $content);

$formProsesPattern = '/(<form id="bulkDeleteFormProses".*?>.*?@method\(\'DELETE\'\))/s';
$content = preg_replace($formProsesPattern, '$1' . "\n            <input type=\"hidden\" name=\"delete_all_pages\" id=\"deleteAllFlagProses\" value=\"0\">", $content);

file_put_contents($path, $content);
echo "Cleaned up hidden inputs!\n";
?>
