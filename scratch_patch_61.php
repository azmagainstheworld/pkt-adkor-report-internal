<?php
// 1. Update frontend buttons
$path = 'resources/views/jasakurir.blade.php';
$content = file_get_contents($path);

// "Atur Kolom Tambahan" button block
$searchAturKolom = <<<HTML
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <a href="javascript:void(0)" onclick="openModal('modalAturKolom'); document.getElementById('dropdownKurir').classList.add('hidden')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                Atur Kolom Tambahan
                            </a>
                        </div>
HTML;
$replaceAturKolom = <<<HTML
                        @if(auth()->user()->isAdmin())
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <a href="javascript:void(0)" onclick="openModal('modalAturKolom'); document.getElementById('dropdownKurir').classList.add('hidden')" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                Atur Kolom Tambahan
                            </a>
                        </div>
                        @endif
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchAturKolom), str_replace("\r\n", "\n", $replaceAturKolom), $content);

// "Atur Ekspedisi" button
$searchAturEkspedisi = <<<HTML
                <x-button variant="outline" onclick="openModal('modalMasterKurir')" class="!rounded-xl !py-2 shadow-sm text-xs font-medium text-gray-600 border-gray-300 hover:bg-gray-50 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Atur Ekspedisi
                </x-button>
HTML;
$replaceAturEkspedisi = <<<HTML
                @if(auth()->user()->isAdmin())
                <x-button variant="outline" onclick="openModal('modalMasterKurir')" class="!rounded-xl !py-2 shadow-sm text-xs font-medium text-gray-600 border-gray-300 hover:bg-gray-50 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Atur Ekspedisi
                </x-button>
                @endif
HTML;
$content = str_replace(str_replace("\r\n", "\n", $searchAturEkspedisi), str_replace("\r\n", "\n", $replaceAturEkspedisi), $content);

file_put_contents($path, $content);

// 2. Update backend controllers
$ctrlPath = 'app/Http/Controllers/JasaKurirController.php';
$ctrlContent = file_get_contents($ctrlPath);

// Protect storeMaster
$searchStoreMaster = <<<PHP
    public function storeMaster(Request \$request)
    {
        \$request->validate(['nama_kurir' => 'required|string|max:50|unique:jasa_kurir_master,nama_kurir']);
PHP;
$replaceStoreMaster = <<<PHP
    public function storeMaster(Request \$request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        \$request->validate(['nama_kurir' => 'required|string|max:50|unique:jasa_kurir_master,nama_kurir']);
PHP;
$ctrlContent = str_replace(str_replace("\r\n", "\n", $searchStoreMaster), str_replace("\r\n", "\n", $replaceStoreMaster), $ctrlContent);

// Protect destroyMaster
$searchDestroyMaster = <<<PHP
    public function destroyMaster(\$id)
    {
        try {
PHP;
$replaceDestroyMaster = <<<PHP
    public function destroyMaster(\$id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        try {
PHP;
$ctrlContent = str_replace(str_replace("\r\n", "\n", $searchDestroyMaster), str_replace("\r\n", "\n", $replaceDestroyMaster), $ctrlContent);

file_put_contents($ctrlPath, $ctrlContent);

echo "Atur Kolom & Ekspedisi successfully hidden from Karyawan!\n";
?>
