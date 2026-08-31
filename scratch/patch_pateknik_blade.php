<?php
$f = 'resources/views/kearsipan-pa-teknik.blade.php';
$c = file_get_contents($f);

// 1. Pagination loops
$c = str_replace('@forelse($dataTable1 as $row)', '@forelse($paginatedTable1 as $row)', $c);
$c = str_replace('@forelse($dataTable2 as $row)', '@forelse($paginatedTable2 as $row)', $c);

// 2. Add Links Table 1
$c = str_replace(
    "</x-table>\n        </div>\n    </x-card>",
    "</x-table>\n            <div class=\"px-6 py-4 border-t border-gray-100 bg-gray-50\">\n                {{ \$paginatedTable1->links('pagination::tailwind') }}\n            </div>\n        </div>\n    </x-card>",
    $c
);

// 3. Add Links Table 2
$c = str_replace(
    "</x-table>\n        </div>\n    </x-card>\n\n    <!-- MODAL TAMBAH JENIS DOKUMEN -->",
    "</x-table>\n            <div class=\"px-6 py-4 border-t border-gray-100 bg-gray-50\">\n                {{ \$paginatedTable2->links('pagination::tailwind') }}\n            </div>\n        </div>\n    </x-card>\n\n    <!-- MODAL TAMBAH JENIS DOKUMEN -->",
    $c
);

// 4. Update import buttons to pass parameter 'tabel1' or 'tabel2'
$c = str_replace(
    "openModal('modalImportExcel'); toggleDropdown('dropdownOpsi1')",
    "openImportModal('tabel1'); toggleDropdown('dropdownOpsi1')",
    $c
);
$c = str_replace(
    "openModal('modalImportExcel'); toggleDropdown('dropdownOpsi2')",
    "openImportModal('tabel2'); toggleDropdown('dropdownOpsi2')",
    $c
);

// 5. Replace x-import-modal with custom AJAX import modal
$oldModal = "    <!-- ================= MODAL IMPORT EXCEL ================= -->
    <x-import-modal 
        id=\"modalImportExcel\" 
        route=\"{{ route('pa-teknik.import') }}\" 
        title=\"Import Data PA Teknik\" 
        templateRoute=\"{{ route('template.download', 'pa-teknik') }}\" 
    />";

$newModal = "    <!-- ================= MODAL IMPORT EXCEL (AJAX) ================= -->
    <div id=\"modalImportExcel\" class=\"fixed inset-0 z-50 hidden\">
        <div class=\"absolute inset-0 bg-black/50 backdrop-blur-sm\" onclick=\"closeModal('modalImportExcel')\"></div>
        <div class=\"flex items-center justify-center min-h-screen px-4\">
            <div class=\"bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative transform transition-all\">
                <div class=\"flex justify-between items-center mb-5\">
                    <h3 class=\"text-lg font-bold text-gray-900\" id=\"importModalTitle\">Import dari Excel</h3>
                    <button onclick=\"closeModal('modalImportExcel')\" class=\"text-gray-400 hover:text-gray-600\">
                        <svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M6 18L18 6M6 6l12 12\"></path></svg>
                    </button>
                </div>
                
                <form id=\"formImportExcel\" onsubmit=\"submitImport(event)\">
                    <input type=\"hidden\" id=\"importKelompok\" name=\"kelompok\" value=\"\">
                    
                    <div class=\"mb-6\">
                        <div class=\"flex items-center justify-center w-full\">
                            <label class=\"flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all\">
                                <div class=\"flex flex-col items-center justify-center pt-5 pb-6\">
                                    <svg class=\"w-8 h-8 text-gray-400 mb-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12\"></path></svg>
                                    <p class=\"text-sm text-gray-500\"><span class=\"font-semibold\">Klik untuk upload</span> atau drag & drop</p>
                                    <p class=\"text-xs text-gray-400 mt-1\">.XLSX, .XLS atau .CSV</p>
                                </div>
                                <input type=\"file\" class=\"hidden\" id=\"fileExcel\" name=\"file\" accept=\".xlsx, .xls, .csv\" required onchange=\"updateFileName(this)\" />
                            </label>
                        </div>
                        <p id=\"fileNameDisplay\" class=\"text-xs text-center text-blue-600 mt-2 font-medium hidden\"></p>
                    </div>

                    <!-- Progress Bar (Hidden by default) -->
                    <div id=\"importProgressContainer\" class=\"hidden mb-6\">
                        <div class=\"flex justify-between text-xs mb-1\">
                            <span class=\"font-medium text-blue-700\">Mengimpor Data...</span>
                            <span class=\"font-medium text-blue-700\" id=\"importProgressText\">0%</span>
                        </div>
                        <div class=\"w-full bg-gray-200 rounded-full h-2\">
                            <div class=\"bg-blue-600 h-2 rounded-full transition-all duration-300\" id=\"importProgressBar\" style=\"width: 0%\"></div>
                        </div>
                    </div>

                    <div class=\"flex justify-end gap-3\">
                        <button type=\"button\" onclick=\"closeModal('modalImportExcel')\" class=\"px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors\">Batal</button>
                        <button type=\"submit\" id=\"btnSubmitImport\" class=\"px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors flex items-center gap-2\">
                            <svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12\"></path></svg>
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>";

$c = str_replace($oldModal, $newModal, $c);

// 6. Add the scripts
$scripts = "    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>
    <script>
        function openImportModal(kelompok) {
            document.getElementById('importKelompok').value = kelompok;
            document.getElementById('importModalTitle').innerText = 'Import Data PA Teknik - ' + (kelompok == 'tabel1' ? 'Tabel 1' : 'Tabel 2');
            document.getElementById('modalImportExcel').classList.remove('hidden');
        }

        function updateFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                display.textContent = 'File terpilih: ' + input.files[0].name;
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        }

        function submitImport(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('btnSubmitImport');
            const progressContainer = document.getElementById('importProgressContainer');
            const progressBar = document.getElementById('importProgressBar');
            const progressText = document.getElementById('importProgressText');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class=\"animate-spin h-4 w-4 text-white\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\"><circle class=\"opacity-25\" cx=\"12\" cy=\"12\" r=\"10\" stroke=\"currentColor\" stroke-width=\"4\"></circle><path class=\"opacity-75\" fill=\"currentColor\" d=\"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\"></path></svg> Memproses...';
            
            progressContainer.classList.remove('hidden');
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                progressBar.style.width = progress + '%';
                progressText.innerText = Math.round(progress) + '%';
            }, 500);

            fetch('{{ route(\"pa-teknik.import\") }}?kelompok=' + formData.get('kelompok'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(interval);
                progressBar.style.width = '100%';
                progressText.innerText = '100%';

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.success,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.error || 'Terjadi kesalahan saat mengimpor data.'
                    });
                    resetImportForm();
                }
            })
            .catch(error => {
                clearInterval(interval);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan jaringan atau server.'
                });
                resetImportForm();
            });
        }

        function resetImportForm() {
            const submitBtn = document.getElementById('btnSubmitImport');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12\"></path></svg> Import Data';
            document.getElementById('importProgressContainer').classList.add('hidden');
        }
    </script>
</main>
@endsection";

$c = str_replace("</main>\n@endsection", $scripts, $c);

file_put_contents($f, $c);
echo "Patched kearsipan-pa-teknik.blade.php\n";
