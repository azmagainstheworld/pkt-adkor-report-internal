<?php

// 1. Fix default month in KetidakhadiranController
$controllerPath = 'app/Http/Controllers/KetidakhadiranController.php';
$controllerContent = file_get_contents($controllerPath);
$oldBulanDefault = '$bulanNama = $request->input(\'bulan\', $bulanList[now()->month - 1]);';
$newBulanDefault = '$bulanNama = $request->input(\'bulan\', \'semua\');';
if (strpos($controllerContent, $oldBulanDefault) !== false) {
    $controllerContent = str_replace($oldBulanDefault, $newBulanDefault, $controllerContent);
    file_put_contents($controllerPath, $controllerContent);
    echo "Fixed default month in KetidakhadiranController\n";
} else {
    echo "Default month code not found or already fixed.\n";
}

// 2. Fix 'Atur Kolom' text and highlight search results in ketidakhadiran/index.blade.php
$bladePath = 'resources/views/ketidakhadiran/index.blade.php';
$bladeContent = file_get_contents($bladePath);

// Rename button
$oldButtonText = 'Atur Kolom Harian';
$newButtonText = 'Atur Kolom';
if (strpos($bladeContent, $oldButtonText) !== false) {
    $bladeContent = str_replace($oldButtonText, $newButtonText, $bladeContent);
    echo "Renamed Atur Kolom button\n";
}

// Add highlight logic for nama
$oldNama = '<td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama }}</td>';
$newNama = '<td class="px-6 py-4 font-medium text-gray-900">{!! request(\'search\') ? preg_replace(\'/(\' . preg_quote(request(\'search\'), \'/\') . \')/i\', \'<mark class="bg-yellow-200 px-1 rounded text-yellow-900">\$1</mark>\', $item->nama) : $item->nama !!}</td>';
if (strpos($bladeContent, $oldNama) !== false) {
    $bladeContent = str_replace($oldNama, $newNama, $bladeContent);
    echo "Added highlight logic for nama\n";
}

// Add highlight logic for npk
$oldNpk = '<td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $item->npk }}</td>';
$newNpk = '<td class="px-6 py-4 text-gray-500 font-mono text-xs">{!! request(\'search\') ? preg_replace(\'/(\' . preg_quote(request(\'search\'), \'/\') . \')/i\', \'<mark class="bg-yellow-200 px-1 rounded text-yellow-900">\$1</mark>\', $item->npk) : $item->npk !!}</td>';
if (strpos($bladeContent, $oldNpk) !== false) {
    $bladeContent = str_replace($oldNpk, $newNpk, $bladeContent);
    echo "Added highlight logic for npk\n";
}

file_put_contents($bladePath, $bladeContent);

?>
