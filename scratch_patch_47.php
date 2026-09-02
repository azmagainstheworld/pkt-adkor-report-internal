<?php
$path = 'resources/views/pelaporan.blade.php';
$content = file_get_contents($path);

// 1. Remove nested <script> tag
$searchScript = <<<HTML
        function cancelAll() {
            let selectAll = document.getElementById("selectAllBulk");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn();
        }

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
HTML;

$replaceScript = <<<HTML
        function cancelAll() {
            let selectAll = document.getElementById("selectAllBulk");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn();
        }

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
HTML;
$searchScript = str_replace("\r\n", "\n", $searchScript);
$replaceScript = str_replace("\r\n", "\n", $replaceScript);
$content = str_replace("\r\n", "\n", $content);
$content = str_replace($searchScript, $replaceScript, $content);

// 2. Change "Mode Hapus Massal" to "Hapus semua"
$searchBtn = <<<HTML
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Mode Hapus Massal
                </button>
HTML;

$replaceBtn = <<<HTML
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>
HTML;
$searchBtn = str_replace("\r\n", "\n", $searchBtn);
$replaceBtn = str_replace("\r\n", "\n", $replaceBtn);
$content = str_replace($searchBtn, $replaceBtn, $content);

// 3. Wrap Atur Kolom in isAdmin
$searchAtur = <<<HTML
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolom'); toggleActionDropdown('dropdownOpsiSuperPelaporan')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
HTML;

$replaceAtur = <<<HTML
                        @if(auth()->user()->isAdmin())
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                        </div>
                        <div class="py-1" role="none">
                            <button type="button" onclick="openModal('modalAturKolom'); toggleActionDropdown('dropdownOpsiSuperPelaporan')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Atur Kolom Tabel
                            </button>
                        </div>
                        @endif
HTML;
$searchAtur = str_replace("\r\n", "\n", $searchAtur);
$replaceAtur = str_replace("\r\n", "\n", $replaceAtur);
$content = str_replace($searchAtur, $replaceAtur, $content);

// 4. Fix search syntax error
$searchQuery = 'pelaporanSearch("{{ request(\'search\') }}");';
$replaceQuery = 'pelaporanSearch(@json(request(\'search\')));';
$content = str_replace($searchQuery, $replaceQuery, $content);

file_put_contents($path, $content);
echo "All fixes applied successfully!\n";
?>
