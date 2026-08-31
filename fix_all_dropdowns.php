<?php

// 1. Fix Controller
$controllerPath = 'D:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\PerizinanPerkantoranController.php';
$c = file_get_contents($controllerPath);
$c = str_replace('\Maatwebsite\Excel\Excel::MPDF', '\Maatwebsite\Excel\Excel::DOMPDF', $c);
file_put_contents($controllerPath, $c);

// 2. Fix Blade
$bladePath = 'D:\web_pkt_adkor_internal\adkor-report-internal\resources\views\perizinan-perkantoran.blade.php';
$b = file_get_contents($bladePath);

// Remove "Atur Kolom" from dropdowns
$b = preg_replace('/<div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-\[10px\] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks<\/p><\/div>[\s\S]*?<div class="py-1" role="none">[\s\S]*?<button type="button" onclick="openModal\(\'modalAturKolomTerbit\'\)[^>]*>[\s\S]*?<\/button>[\s\S]*?<\/div>/', '', $b);

$b = preg_replace('/<div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-\[10px\] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks<\/p><\/div>[\s\S]*?<div class="py-1" role="none">[\s\S]*?<button type="button" onclick="openModal\(\'modalAturKolomProses\'\)[^>]*>[\s\S]*?<\/button>[\s\S]*?<\/div>/', '', $b);

// Add Import Modals
$modals = <<<'HTML'
    <!-- ================= MODAL IMPORT ================= -->
    <x-import-modal 
        id="modalImportPerizinanTerbit" 
        route="{{ route('perizinan-perkantoran.import') }}" 
        title="Import Data Perizinan Terbit" 
        templateRoute="{{ route('template.download', 'perizinan-terbit') }}" 
    />

    <x-import-modal 
        id="modalImportPerizinanProses" 
        route="{{ route('perizinan-proses.import') }}" 
        title="Import Data Perizinan Proses" 
        templateRoute="{{ route('template.download', 'perizinan-proses') }}" 
    />

HTML;

if (!str_contains($b, 'id="modalImportPerizinanTerbit"')) {
    $b = str_replace('</main>', $modals . '</main>', $b);
}

file_put_contents($bladePath, $b);
echo "Fixed!";
