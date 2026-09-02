<?php
$path = 'resources/views/pengiriman-dokumen.blade.php';
$content = file_get_contents($path);

// 1. Add hide-bulk CSS
if (strpos($content, '.hide-bulk th:first-child') === false) {
    $content = str_replace("@section('content')", "@section('content')\n<style>\n.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }\n</style>", $content);
}

// 2. Fix Domestik Input
$searchDomestik = <<<HTML
                    <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span><input type="text" id="input_cost_domestik" required class="input-rupiah w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none"> <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span><input type="hidden" name="cost_domestik" required> <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span></div>
HTML;
$replaceDomestik = <<<HTML
                    <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span><input type="number" id="cost_domestik" name="cost_domestik" required class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none"> <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span></div>
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchDomestik), str_replace("\r\n", "\n", $replaceDomestik), $content);

// 3. Fix Internasional Input
$searchInternasional = <<<HTML
                    <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span><input type="text" id="input_cost_internasional" required class="input-rupiah w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none"> <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span><input type="hidden" name="cost_internasional" required> <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span></div>
HTML;
$replaceInternasional = <<<HTML
                    <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span><input type="number" id="cost_internasional" name="cost_internasional" required class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none"> <span class="text-[10px] text-red-500 font-medium mt-1 hidden error-msg">Wajib diisi!</span></div>
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchInternasional), str_replace("\r\n", "\n", $replaceInternasional), $content);

// 4. Update openEditModalOngkir to not use formatting
$searchOpenEditModal = <<<HTML
        document.getElementById('input_cost_domestik').value = new Intl.NumberFormat('id-ID').format(record.ongkir_dalam_negeri);
        document.querySelector('input[name="cost_domestik"]').value = record.ongkir_dalam_negeri;

        document.getElementById('input_cost_internasional').value = new Intl.NumberFormat('id-ID').format(record.ongkir_luar_negeri);
        document.querySelector('input[name="cost_internasional"]').value = record.ongkir_luar_negeri;
HTML;
$replaceOpenEditModal = <<<HTML
        document.getElementById('cost_domestik').value = record.ongkir_dalam_negeri;
        document.getElementById('cost_internasional').value = record.ongkir_luar_negeri;
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchOpenEditModal), str_replace("\r\n", "\n", $replaceOpenEditModal), $content);

// 5. Remove input-rupiah event listener
$searchInputRupiah = <<<HTML
    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-rupiah')) {
            let rawValue = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?!$)/, '');
            if(e.target.nextElementSibling && e.target.nextElementSibling.tagName === 'INPUT') {
                e.target.nextElementSibling.value = rawValue;
            }
            if (rawValue) { e.target.value = new Intl.NumberFormat('id-ID').format(rawValue); } 
            else { e.target.value = ''; }
        }
    });
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchInputRupiah), "", $content);

file_put_contents($path, $content);
echo "UI/UX fixes applied successfully to pengiriman-dokumen.blade.php!\n";
?>
