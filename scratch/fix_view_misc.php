<?php

$file = 'resources/views/pemeliharaan.blade.php';
$content = file_get_contents($file);

// 1. Wrap the Konfigurasi section with @if(auth()->check() && auth()->user()->isAdmin())
// We look for:
// <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi</p></div>
// <div class="py-1">
// ...
// </div>
$konfigurasiPattern = '/(<div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-\[10px\] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi<\/p><\/div>\s*<div class="py-1">.*?Atur Dokumen Kegiatan<\/button>\s*@if\(auth\(\)->check\(\) && auth\(\)->user\(\)->isAdmin\(\)\)\s*<button.*?Atur Kolom Tambahan<\/button>\s*@endif\s*<\/div>)/s';

// Wait, the HTML for Konfigurasi might be slightly different. Let's make it simpler.
$konfigurasiPattern = '/<div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-\[10px\] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi<\/p><\/div>\s*<div class="py-1">.*?Atur Kolom Tambahan<\/button>\s*@endif\s*<\/div>/s';
$replacement = "@if(auth()->check() && auth()->user()->isAdmin())\n$0\n@endif";
$content = preg_replace($konfigurasiPattern, $replacement, $content);

// 2. Move Legend
// Find the legend overlay block
$legendPattern = '/<!-- Legend Overlay Khusus untuk Pemeliharaan -->\s*<div class="absolute top-6 left-1\/2 transform -translate-x-1\/4 flex flex-wrap items-center gap-3 text-\[10px\] bg-white\/90 p-2 rounded-lg border border-gray-100 shadow-sm pointer-events-none">\s*@foreach\(\$masterRutin as \$index => \$master\)\s*<div class="flex items-center gap-1\.5">\s*<span class="w-3 h-3 rounded-md" style="background-color: \{\{ \$chartColors\[\$index % count\(\$chartColors\)\] \}\}"><\/span>\s*<span class="text-gray-700 font-medium">\{\{ \$master->nama_pemeliharaan \}\}<\/span>\s*<\/div>\s*@endforeach\s*<\/div>/s';

$legendReplacement = <<<EOD
<!-- Legend Overlay Khusus untuk Pemeliharaan -->
        <div class="mt-4 flex flex-wrap justify-center items-center gap-3 text-[11px] bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
            @foreach(\$masterRutin as \$index => \$master)
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-md" style="background-color: {{ \$chartColors[\$index % count(\$chartColors)] }}"></span>
                    <span class="text-gray-700 font-medium">{{ \$master->nama_pemeliharaan }}</span>
                </div>
            @endforeach
        </div>
EOD;

$content = preg_replace($legendPattern, '', $content);
$content = str_replace('</div>

    <!-- ================= TABEL 1: PEMELIHARAAN RUTIN ================= -->', 
$legendReplacement . "\n    </div>\n\n    <!-- ================= TABEL 1: PEMELIHARAAN RUTIN ================= -->", $content);

// 3. Add JS for removing leading zero
$jsToAdd = <<<EOD
document.addEventListener('input', function(e) {
        if (e.target.type === 'number') {
            e.target.value = e.target.value.replace(/^0+(?=\d)/, '');
        }
    });
EOD;

$content = str_replace(
    "document.addEventListener('DOMContentLoaded', function() {", 
    $jsToAdd . "\n\n    document.addEventListener('DOMContentLoaded', function() {", 
    $content
);

file_put_contents($file, $content);
echo "View patched successfully.\n";
