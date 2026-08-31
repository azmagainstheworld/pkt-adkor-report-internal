@props([
    'id' => 'modalImportExcel',
    'route' => '#',
    'title' => 'Import Data Excel',
    'templateRoute' => null // Route untuk mendownload template excel kosong
])

<x-modal :id="$id" :title="$title" description="Unggah file Excel (.xlsx, .xls) untuk menambahkan data secara massal. Pastikan format kolom sesuai dengan sistem.">
    <form id="form_{{ $id }}" action="{{ $route }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        
        <input type="hidden" name="import_uuid" id="import_uuid_{{ $id }}" value="">
        
        <!-- Peringatan / Download Template -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Data tambahan (Kolom Dinamis) juga dapat di-import. Pastikan penulisan judul kolom Excel sama persis dengan nama kolom di aplikasi.</p>
                @if($templateRoute)
                    <a href="{{ $templateRoute }}" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
                @endif
            </div>
        </div>

        <!-- Input File Area -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih File Excel <span class="text-red-500">*</span></label>
            <div class="flex items-center justify-center w-full">
                <label for="dropzone-file-{{ $id }}" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-green-400 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Klik untuk unggah</span> atau seret file ke sini</p>
                        <p class="text-xs text-gray-400">Hanya mendukung .XLSX, .XLS, atau .CSV</p>
                    </div>
                    <input id="dropzone-file-{{ $id }}" name="file" type="file" class="hidden" accept=".xlsx, .xls, .csv" required onchange="document.getElementById('file-name-{{ $id }}').textContent = this.files[0].name" />
                </label>
            </div>
            <!-- Indikator nama file yang terpilih -->
            <p id="file-name-{{ $id }}" class="text-xs font-bold text-green-600 mt-2 text-center"></p>
        </div>

        <div class="flex justify-end gap-3 mt-4 border-t border-gray-100 pt-4">
            <x-button variant="outline" type="button" onclick="closeModal('{{ $id }}')">Batal</x-button>
            <x-button variant="primary" type="submit" class="!bg-green-600 hover:!bg-green-700 border-none">Proses Import</x-button>
        </div>
    </form>
</x-modal>

<script>
    document.getElementById('form_{{ $id }}').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Generate UUID untuk form ini
        const uuid = 'import_' + Math.random().toString(36).substr(2, 9) + Date.now();
        document.getElementById('import_uuid_{{ $id }}').value = uuid;

        const form = this;
        const formData = new FormData(form);

        closeModal('{{ $id }}');

        Swal.fire({
            title: 'Mempersiapkan Data...',
            html: `
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2" id="progress-text-{{ $id }}">Sedang membaca file Excel...</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-600 h-2.5 rounded-full transition-all duration-300" id="progress-bar-{{ $id }}" style="width: 0%"></div>
                    </div>
                    <p class="text-xs font-bold text-gray-700 mt-2" id="progress-percentage-{{ $id }}">0%</p>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Polling Progress
        let pollingInterval = setInterval(async () => {
            try {
                const response = await fetch('/import-progress/' + uuid);
                const data = await response.json();
                
                if (data.total > 0) {
                    Swal.getTitle().textContent = 'Mengimpor Data...';
                    document.getElementById('progress-text-{{ $id }}').textContent = `Berhasil memproses ${data.current} dari ${data.total} baris`;
                    document.getElementById('progress-bar-{{ $id }}').style.width = data.percentage + '%';
                    document.getElementById('progress-percentage-{{ $id }}').textContent = data.percentage + '%';
                }
            } catch (err) {
                // Jangan lakukan apapun kalau error, mungkin session tertahan sesaat
            }
        }, 1000);

        // Submit form via fetch
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            clearInterval(pollingInterval);

            if (response.ok) {
                document.getElementById('progress-bar-{{ $id }}').style.width = '100%';
                document.getElementById('progress-percentage-{{ $id }}').textContent = '100%';
                document.getElementById('progress-text-{{ $id }}').textContent = 'Import selesai!';

                const contentType = response.headers.get("content-type");
                let warningMessage = null;
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    const result = await response.json();
                    if (result.warning) warningMessage = result.warning;
                }
                
                if (warningMessage) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: warningMessage,
                        confirmButtonText: 'Tutup & Muat Ulang',
                        confirmButtonColor: '#eab308'
                    }).then(() => { window.location.reload(); });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Berhasil!',
                        text: 'Seluruh baris data telah masuk ke sistem.',
                        confirmButtonText: 'Tutup & Muat Ulang',
                        confirmButtonColor: '#16a34a'
                    }).then(() => { window.location.reload(); });
                }
            } else {
                let errorMsg = 'Periksa konsol untuk detail atau pastikan format Excel sudah benar.';
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    const result = await response.json();
                    if (result.error) errorMsg = result.error;
                } else {
                    console.error(await response.text());
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: errorMsg,
                    confirmButtonColor: '#ef4444'
                }).then(() => { window.location.reload(); });
            }
        } catch (error) {
            clearInterval(pollingInterval);
            Swal.fire('Error', 'Terjadi masalah pada koneksi server.', 'error').then(() => { window.location.reload(); });
        }
    });
</script>
