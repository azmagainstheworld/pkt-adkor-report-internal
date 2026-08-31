<?php
$file = 'resources/views/pelaporan.blade.php';
$content = file_get_contents($file);

// 1. Fix date to English
$content = str_replace(
    '<p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>',
    '<p class="text-sm text-gray-500 font-medium" id="tanggal-en"></p>
            <script>document.getElementById(\'tanggal-en\').textContent = new Date().toLocaleDateString(\'en-US\', {weekday:\'long\',day:\'2-digit\',month:\'long\',year:\'numeric\'});</script>',
    $content
);

// 2. Add search bar to header of rincian table
$content = str_replace(
    '            <div class="flex justify-end items-center gap-3">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown(\'dropdownOpsiSuperPelaporan\')"',
    '            <div class="flex justify-end items-center gap-3">
                <!-- SEARCH BAR -->
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" id="pelaporanSearchInput" placeholder="Cari laporan..." autocomplete="off"
                        class="pl-8 pr-4 py-2 text-xs border border-gray-300 rounded-xl bg-white text-gray-700 outline-none focus:border-orange-400 transition-colors w-44"
                        oninput="pelaporanSearch(this.value)">
                </div>
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleActionDropdown(\'dropdownOpsiSuperPelaporan\')"',
    $content
);

// 3. Add class pelaporan-row to table rows
$content = str_replace(
    '<tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ $index % 2 == 1 ? \'bg-gray-50/60\' : \'\' }}">',
    '<tr class="pelaporan-row hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ $index % 2 == 1 ? \'bg-gray-50/60\' : \'\' }}">',
    $content
);

// 4. Also handle Ringkasan pagination (pelaporan) - add page-info div to header and pagination controls
// First add id to ringkasan card header
$content = str_replace(
    '        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Ringkasan Akumulasi Laporan</h3>
                <p class="text-xs text-gray-400">Total data laporan per periode</p>
            </div>
        </div>',
    '        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Ringkasan Akumulasi Laporan</h3>
                <p class="text-xs text-gray-400">Total data laporan per periode</p>
            </div>
            <div class="text-xs text-gray-400" id="ringkasan-page-info"></div>
        </div>',
    $content
);

// Add pelaporan-ringkasan-row class to its rows
$content = str_replace(
    '@forelse($dataRingkasan as $index => $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ $index % 2 == 1 ? \'bg-gray-50/60\' : \'\' }}">',
    '@forelse($dataRingkasan as $index => $row)
                    <tr class="ringkasan-row hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ $index % 2 == 1 ? \'bg-gray-50/60\' : \'\' }}" style="display:none;">',
    $content
);

// Add pagination after the ringkasan x-table closing and before x-card closing
$content = str_replace(
    '                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PELAPORAN',
    '                @endforelse
            </x-table>
        </div>

        <!-- Pagination Controls Ringkasan -->
        <div id="ringkasan-pagination" class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 rounded-b-xl">
            <button id="ringkasan-btn-prev" onclick="ringkasanChangePage(-1)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 bg-white hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors" disabled>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Sebelumnya
            </button>
            <span id="ringkasan-page-label" class="text-xs text-gray-500"></span>
            <button id="ringkasan-btn-next" onclick="ringkasanChangePage(1)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 bg-white hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors" disabled>
                Selanjutnya
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PELAPORAN',
    $content
);

// 5. Add all JS before </script> at the end
$searchAndPagination = <<<'JSBLOCK'

    // ============================================================
    // PAGINATION RINGKASAN PELAPORAN
    // ============================================================
    (function() {
        const ROWS_PER_PAGE = 6;
        let currentPage = 1;
        const rows = Array.from(document.querySelectorAll('.ringkasan-row'));
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / ROWS_PER_PAGE);

        function renderPage(page) {
            const start = (page - 1) * ROWS_PER_PAGE;
            const end = start + ROWS_PER_PAGE;
            rows.forEach((row, i) => { row.style.display = (i >= start && i < end) ? '' : 'none'; });
            const label = document.getElementById('ringkasan-page-label');
            const info = document.getElementById('ringkasan-page-info');
            if (label) label.textContent = totalRows > 0 ? `Halaman ${page} dari ${totalPages}` : '';
            if (info) info.textContent = totalRows > 0 ? `${Math.min(end, totalRows)} dari ${totalRows} data` : '';
            document.getElementById('ringkasan-btn-prev').disabled = (page <= 1);
            document.getElementById('ringkasan-btn-next').disabled = (page >= totalPages);
            const pagination = document.getElementById('ringkasan-pagination');
            if (pagination) pagination.style.display = (totalPages <= 1) ? 'none' : '';
        }
        window.ringkasanChangePage = function(delta) {
            currentPage = Math.max(1, Math.min(totalPages, currentPage + delta));
            renderPage(currentPage);
        };
        if (totalRows > 0) renderPage(1);
    })();

    // ============================================================
    // SEARCH RINCIAN PELAPORAN (tanpa reload, highlight kuning)
    // ============================================================
    function pelaporanSearch(keyword) {
        const term = keyword.trim().toLowerCase();
        const rows = document.querySelectorAll('.pelaporan-row');

        rows.forEach(function(row) {
            // Hapus semua mark highlight sebelumnya
            row.querySelectorAll('mark.pelaporan-highlight').forEach(function(mark) {
                const parent = mark.parentNode;
                parent.replaceChild(document.createTextNode(mark.textContent), mark);
                parent.normalize();
            });

            if (term === '') {
                row.style.display = '';
                return;
            }

            const rowText = row.innerText.toLowerCase();
            if (!rowText.includes(term)) {
                row.style.display = 'none';
                return;
            }

            row.style.display = '';

            // Highlight hanya di text node menggunakan TreeWalker (aman, tidak merusak HTML)
            row.querySelectorAll('td').forEach(function(td) {
                const walker = document.createTreeWalker(td, NodeFilter.SHOW_TEXT, null, false);
                const textNodes = [];
                let node;
                while ((node = walker.nextNode())) { textNodes.push(node); }

                textNodes.forEach(function(textNode) {
                    const content = textNode.nodeValue;
                    if (!content.toLowerCase().includes(term)) return;

                    const escaped = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const regex = new RegExp(escaped, 'gi');
                    const fragment = document.createDocumentFragment();
                    let lastIndex = 0;
                    let match;

                    regex.lastIndex = 0;
                    while ((match = regex.exec(content)) !== null) {
                        if (match.index > lastIndex) {
                            fragment.appendChild(document.createTextNode(content.slice(lastIndex, match.index)));
                        }
                        const mark = document.createElement('mark');
                        mark.className = 'pelaporan-highlight bg-yellow-200 rounded px-0.5';
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
        });
    }
JSBLOCK;

$content = str_replace('</script>
@endsection', $searchAndPagination . '
</script>
@endsection', $content);

file_put_contents($file, $content);
echo "Done! File size: " . strlen($content) . " bytes\n";
echo "Line count: " . (substr_count($content, "\n") + 1) . "\n";

// Verify key patterns exist
$checks = [
    'pelaporanSearch function' => 'function pelaporanSearch(',
    'pelaporan-row class' => 'pelaporan-row',
    'TreeWalker usage' => 'createTreeWalker',
    'English date JS' => 'tanggal-en',
    'search input' => 'pelaporanSearchInput',
    'ringkasan pagination' => 'ringkasan-pagination',
];
foreach ($checks as $label => $needle) {
    echo ($label) . ': ' . (str_contains($content, $needle) ? 'OK' : 'MISSING') . "\n";
}
