<?php
$file = 'resources/views/pelaporan.blade.php';
$content = file_get_contents($file);

// 1. DATE FORMAT
$dateOld = "<script>document.getElementById('tanggal-en').textContent = new Date().toLocaleDateString('en-US', {weekday:'long',day:'2-digit',month:'long',year:'numeric'});</script>";
$dateNew = "<script>
                const d = new Date();
                const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                const m = ['august','january','february','march','april','may','june','july','august','september','october','november','december'];
                document.getElementById('tanggal-en').textContent = days[d.getDay()] + ', ' + d.getDate() + ' ' + m[d.getMonth()] + ' ' + d.getFullYear();
            </script>";
$content = str_replace($dateOld, $dateNew, $content);

// 2. SEARCH BAR
$searchBarTarget = '            <div class="flex gap-3">
                <x-button variant="outline" onclick="openModal(\'modalAturKolom\')"';
$searchBarNew = '            <div class="flex justify-end items-center gap-3">
                <!-- SEARCH BAR -->
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" id="pelaporanSearchInput" placeholder="Cari laporan..." autocomplete="off"
                        class="pl-8 pr-4 py-2 text-xs border border-gray-300 rounded-xl bg-white text-gray-700 outline-none focus:border-orange-400 transition-colors w-44"
                        oninput="pelaporanSearch(this.value)">
                </div>
                <x-button variant="outline" onclick="openModal(\'modalAturKolom\')"';
$content = str_replace($searchBarTarget, $searchBarNew, $content);

// 3. SEARCH JS FUNCTION
$searchJS = '    function pelaporanSearch(term) {
        term = term.toLowerCase().trim();
        const rows = document.querySelectorAll(\'.pelaporan-row\');
        
        rows.forEach(function(row) {
            // Hapus semua mark highlight sebelumnya
            row.querySelectorAll(\'mark.pelaporan-highlight\').forEach(function(mark) {
                const parent = mark.parentNode;
                parent.replaceChild(document.createTextNode(mark.textContent), mark);
                parent.normalize();
            });

            if (term === \'\') {
                row.style.display = \'\';
                return;
            }

            let found = false;
            // Gunakan textContent dari baris untuk cek cepat apakah ada match
            if (row.textContent.toLowerCase().includes(term)) {
                found = true;
                
                // Highlight hanya di text node menggunakan TreeWalker (aman, tidak merusak HTML)
                row.querySelectorAll(\'td\').forEach(function(td) {
                    const walker = document.createTreeWalker(td, NodeFilter.SHOW_TEXT, null, false);
                    const textNodes = [];
                    let node;
                    while ((node = walker.nextNode())) { textNodes.push(node); }

                    textNodes.forEach(function(textNode) {
                        const content = textNode.nodeValue;
                        if (!content.toLowerCase().includes(term)) return;

                        const escaped = term.replace(/[.*+?^${}()|[\]\\\]/g, \'\\\\$&\');
                        const regex = new RegExp(escaped, \'gi\');
                        const fragment = document.createDocumentFragment();
                        let lastIndex = 0;
                        let match;

                        regex.lastIndex = 0;
                        while ((match = regex.exec(content)) !== null) {
                            if (match.index > lastIndex) {
                                fragment.appendChild(document.createTextNode(content.slice(lastIndex, match.index)));
                            }
                            const mark = document.createElement(\'mark\');
                            mark.className = \'pelaporan-highlight bg-yellow-200 rounded px-0.5\';
                            mark.textContent = match[0];
                            fragment.appendChild(mark);
                            lastIndex = match.index + match[0].length;
                        }
                        if (lastIndex < content.length) {
                            fragment.appendChild(document.createTextNode(content.slice(lastIndex)));
                        }
                        textNode.parentNode.replaceChild(fragment, textNode);
                    });
                });
            }

            row.style.display = found ? \'\' : \'none\';
        });
    }

    document.addEventListener(\'DOMContentLoaded\', function() {';
$content = str_replace('    document.addEventListener(\'DOMContentLoaded\', function() {', $searchJS, $content);

// 4. RINGKASAN PAGINATION UI
$paginationTarget = '                    </tr>
                @empty';
$paginationNew = '                    </tr>
                @empty';
$content = str_replace('<tr class="pelaporan-row hover:bg-gray-50', '<tr class="ringkasan-row hover:bg-gray-50', $content); // rename ringkasan rows to distinguish from rincian rows

$ringkasanCardEndTarget = '    <!-- ================= TABEL 2: RINCIAN PELAPORAN ================= -->';
$ringkasanPaginationUI = '        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            <div id="ringkasanPageInfo">
                Menampilkan 0–0 dari 0 data
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="ringkasanChangePage(-1)" class="px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Sebelum</button>
                <button type="button" onclick="ringkasanChangePage(1)" class="px-3 py-1.5 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Sesudah</button>
            </div>
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PELAPORAN ================= -->';

// Wait, the original card ending is:
/*
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PELAPORAN ================= -->
*/
$ringkasanCardEndSearch = '        </div>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PELAPORAN ================= -->';
$content = str_replace($ringkasanCardEndSearch, $ringkasanPaginationUI, $content);

// 5. RINGKASAN PAGINATION JS
$paginationJS = '    let ringkasanCurrentPage = 1;
    const ringkasanPerPage = 5;

    function renderRingkasanPagination() {
        const rows = Array.from(document.querySelectorAll(\'.ringkasan-row\'));
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / ringkasanPerPage);
        
        if (totalRows === 0) {
            const info = document.getElementById(\'ringkasanPageInfo\');
            if(info) info.textContent = "Tidak ada data ringkasan.";
            return;
        }

        if (ringkasanCurrentPage < 1) ringkasanCurrentPage = 1;
        if (ringkasanCurrentPage > totalPages) ringkasanCurrentPage = totalPages;

        const startIndex = (ringkasanCurrentPage - 1) * ringkasanPerPage;
        const endIndex = startIndex + ringkasanPerPage;

        rows.forEach((row, index) => {
            if (index >= startIndex && index < endIndex) {
                row.style.display = \'\';
            } else {
                row.style.display = \'none\';
            }
        });

        const info = document.getElementById(\'ringkasanPageInfo\');
        if(info) {
            info.textContent = `Menampilkan ${startIndex + 1}–${Math.min(endIndex, totalRows)} dari ${totalRows} data`;
        }
    }

    function ringkasanChangePage(direction) {
        const rows = document.querySelectorAll(\'.ringkasan-row\');
        const totalPages = Math.ceil(rows.length / ringkasanPerPage);
        
        ringkasanCurrentPage += direction;
        if (ringkasanCurrentPage < 1) ringkasanCurrentPage = 1;
        if (ringkasanCurrentPage > totalPages) ringkasanCurrentPage = totalPages;
        
        renderRingkasanPagination();
    }

    document.addEventListener(\'DOMContentLoaded\', function() {
        renderRingkasanPagination();';
$content = str_replace('    document.addEventListener(\'DOMContentLoaded\', function() {', $paginationJS, $content);

file_put_contents($file, $content);
echo "Applied fixes to $file\n";
