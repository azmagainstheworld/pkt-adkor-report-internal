<?php

function fixKaryawan() {
    $path = 'resources/views/karyawan.blade.php';
    $content = file_get_contents($path);

    // Find the end of dropdownOpsiSuper and inject the Hapus Semua button
    $target = "</div>\n            </div>";
    
    $btnHtml = <<<HTML
</div>
            </div>
            
            <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus semua
            </button>
HTML;

    // Only inject if not already there
    if (strpos($content, 'id="btnModeBulk"') === false) {
        $content = preg_replace('/<\/div>\s*<\/div>\s*<!-- Tambah Data Utama \(Paling Kanan \/ Ujung\) -->/s', $btnHtml . "\n\n            <!-- Tambah Data Utama (Paling Kanan / Ujung) -->", $content);
        file_put_contents($path, $content);
        echo "karyawan.blade.php button added.\n";
    }
}

function fixSearchForm() {
    $path = 'resources/views/layouts/app.blade.php';
    $content = file_get_contents($path);

    // Replace except('search') with except(['search', 'page'])
    if (strpos($content, "except('search')") !== false) {
        $content = str_replace("except('search')", "except(['search', 'page'])", $content);
        file_put_contents($path, $content);
        echo "layouts/app.blade.php search form fixed.\n";
    } else {
        // Just in case it was written differently
        if (strpos($content, "except(['search', 'page'])") === false) {
             echo "Could not find except('search') in app.blade.php\n";
        } else {
             echo "app.blade.php search form already fixed.\n";
        }
    }
}

fixKaryawan();
fixSearchForm();
