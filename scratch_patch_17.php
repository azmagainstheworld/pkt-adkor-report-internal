<?php

// ============================================
// 1. ANGGARAN: Fix JS Syntax Error & Add Checkboxes to Rows
// ============================================
$anggaranPath = 'resources/views/anggaran/index.blade.php';
$anggaranContent = file_get_contents($anggaranPath);

// Fix JS syntax error (stray `}` and old duplicate functions)
// Remove everything from the stray `}` up to the clean `let isBulkMode = false;` block
$oldJsPattern = '/\}\s*function toggleSelectAll\(\) \{[\s\S]*?\}[\s\S]*?<\/script>\s*<script>\s*let isBulkMode = false;/';
if (preg_match($oldJsPattern, $anggaranContent)) {
    $anggaranContent = preg_replace($oldJsPattern, "<\/script>\n<script>\nlet isBulkMode = false;", $anggaranContent);
}

// Add Checkboxes to rows in TABEL 1
$trContextOld = '<tr class="hover:bg-gray-100 transition-colors text-sm">
                                <td class="px-6 py-3 text-gray-700 text-center align-middle whitespace-nowrap">{{ $entry[\'tahun\'] }}</td>';
$trContextNew = '<tr class="hover:bg-gray-100 transition-colors text-sm">
                                <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk" value="{{ $anggaran->id }}" onclick="toggleCheckbox()"></td>
                                <td class="px-6 py-3 text-gray-700 text-center align-middle whitespace-nowrap">{{ $entry[\'tahun\'] }}</td>';
if (strpos($anggaranContent, 'class="cb-bulk"') === false || substr_count($anggaranContent, 'class="cb-bulk"') == 1) { // 1 means only in JS
    $anggaranContent = str_replace($trContextOld, $trContextNew, $anggaranContent);
}
file_put_contents($anggaranPath, $anggaranContent);


// ============================================
// 2. KETIDAKHADIRAN PDF: Add Tahun & Bulan
// ============================================
$khPdfPath = 'resources/views/pdf/ketidakhadiran.blade.php';
if (file_exists($khPdfPath)) {
    $khPdfContent = file_get_contents($khPdfPath);
    
    // Replace Header
    $thOld = '<th rowspan="2">Nama</th>';
    $thNew = '<th rowspan="2">Tahun</th><th rowspan="2">Bulan</th><th rowspan="2">Nama</th>';
    if (strpos($khPdfContent, '<th rowspan="2">Tahun</th>') === false) {
        $khPdfContent = str_replace($thOld, $thNew, $khPdfContent);
    }
    
    // Replace Row Data
    $tdOld = '<td>{{ $item->nama }}</td>';
    $tdNew = '<td class="text-center">{{ $item->tahun }}</td><td class="text-center">{{ $item->bulan }}</td><td>{{ $item->nama }}</td>';
    if (strpos($khPdfContent, '<td>{{ $item->tahun }}</td>') === false && strpos($khPdfContent, '<td class="text-center">{{ $item->tahun }}</td>') === false) {
        $khPdfContent = str_replace($tdOld, $tdNew, $khPdfContent);
    }
    file_put_contents($khPdfPath, $khPdfContent);
}


// ============================================
// 3. KETIDAKHADIRAN: Prevent Leading Zeroes
// ============================================
$khIndexPath = 'resources/views/ketidakhadiran/index.blade.php';
$khIndexContent = file_get_contents($khIndexPath);

$categories = ['dinas', 'cuti', 'izin', 'training', 'dispensasi', 'detasering'];
foreach ($categories as $cat) {
    $inputOld = '<input type="text" name="' . $cat . '" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">';
    $inputNew = '<input type="number" min="0" oninput="if(this.value.length > 1 && this.value.startsWith(\'0\')) this.value = parseInt(this.value, 10);" name="' . $cat . '" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">';
    $khIndexContent = str_replace($inputOld, $inputNew, $khIndexContent);
}

// Just in case it was already changed to number but without oninput:
foreach ($categories as $cat) {
    $inputOld = '<input type="number" name="' . $cat . '" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">';
    $inputNew = '<input type="number" min="0" oninput="if(this.value.length > 1 && this.value.startsWith(\'0\')) this.value = parseInt(this.value, 10);" name="' . $cat . '" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">';
    $khIndexContent = str_replace($inputOld, $inputNew, $khIndexContent);
}

file_put_contents($khIndexPath, $khIndexContent);

echo "Patched all three issues successfully!\n";

?>
