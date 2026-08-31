<?php
$f = 'resources/views/dof.blade.php';
$c = file_get_contents($f);

$oldModalRegex = '/<!-- ================= MODAL IMPORT EXCEL \(AJAX\) ================= -->.*?<\/form>\s*<\/div>\s*<\/div>\s*<\/div>/s';

$newModals = '<!-- ================= MODAL IMPORT EXCEL ================= -->
    <x-import-modal 
        id="modalImportExcel1" 
        route="{{ route(\'dof.import\') }}?kelompok=tabel1" 
        title="Import Data DOF (Tabel 1)" 
        templateRoute="{{ route(\'template.download\', \'dof-1\') }}" 
    />
    <x-import-modal 
        id="modalImportExcel2" 
        route="{{ route(\'dof.import\') }}?kelompok=tabel2" 
        title="Import Data DOF (Tabel 2)" 
        templateRoute="{{ route(\'template.download\', \'dof-2\') }}" 
    />';

$c = preg_replace($oldModalRegex, $newModals, $c);

// Also we need to replace the dropdown buttons
$c = str_replace("openImportModal('tabel1')", "openModal('modalImportExcel1')", $c);
$c = str_replace("openImportModal('tabel2')", "openModal('modalImportExcel2')", $c);

// And remove the custom JS
$jsRegex = '/function openImportModal\(kelompok\).*?function resetImportForm\(\).*?\}/s';
$c = preg_replace($jsRegex, '', $c);

file_put_contents($f, $c);
echo "Reverted Modal\n";
