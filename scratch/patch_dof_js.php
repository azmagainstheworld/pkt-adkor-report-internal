<?php
$f = 'resources/views/dof.blade.php';
$c = file_get_contents($f);

// Fix the import buttons to use openImportModal
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

// Add the JS if it doesn't exist
$scripts = "
    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>
    <script>
        function openImportModal(kelompok) {
            document.getElementById('importKelompok').value = kelompok;
            document.getElementById('importModalTitle').innerText = 'Import Data DOF - ' + (kelompok == 'tabel1' ? 'Tabel 1' : 'Tabel 2');
            
            // Set template download link based on kelompok
            document.getElementById('btnDownloadTemplate').href = \"{{ route('template.download', '') }}/\" + (kelompok == 'tabel1' ? 'dof-1' : 'dof-2');
            
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

            fetch('{{ route(\"dof.import\") }}?kelompok=' + formData.get('kelompok'), {
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
</main>";

if (strpos($c, 'function openImportModal(') === false) {
    $c = preg_replace('/<\/main>/i', $scripts, $c);
}

file_put_contents($f, $c);
echo "Patched dof.blade.php JS\n";
