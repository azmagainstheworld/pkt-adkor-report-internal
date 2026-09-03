<?php
$content = file_get_contents('resources/views/surat-masuk-keluar.blade.php');

$oldModal = <<<'EOD'
    <x-modal id="modalImportExcel" title="Impor Data Surat via Excel" description="Unduh template Excel yang disediakan, isi data satuan, lalu unggah kembali di sini. Sistem akan melewati data duplikat secara otomatis.">
        <form action="{{ route('surat.import.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
        <!-- Download Template injected -->
        

            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs text-gray-600 flex flex-col gap-3 shadow-inner mb-4">
                <div class="flex items-center gap-2 text-green-700 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Instruksi Impor
                </div>
                <ul class="list-disc list-inside pl-1 space-y-1">
                    <li>Gunakan format tanggal di Excel: <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-gray-200">YYYY-MM-DD</span>.</li>
                    <li>Kolom 'Status': <span class="font-bold">Terkirim</span> / <span class="font-bold">Dibatalkan</span>.</li>
                    <li>Kolom 'Jenis Surat': <span class="font-bold">Surat Masuk</span> / <span class="font-bold">Surat Keluar</span>.</li>
                </ul>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih File Excel (.xlsx / .xls) <span class="text-red-500">*</span></label>
                <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            
            <div class="flex justify-end gap-3 mt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalImportExcel')">Batal</x-button>
                <x-button variant="primary" type="submit" class="!bg-green-600 hover:!bg-green-700 border-none !px-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Unggah & Proses
                </x-button>
            </div>
        </form>
    </x-modal>
EOD;

$newModals = <<<'EOD'
    <x-modal id="modalImportDetail" title="Impor Data Surat via Excel" description="Unduh template Excel yang disediakan, isi data satuan, lalu unggah kembali di sini. Sistem akan melewati data duplikat secara otomatis.">
        <form action="{{ route('surat.import.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs text-gray-600 flex flex-col gap-3 shadow-inner mb-4">
                <div class="flex items-center gap-2 text-green-700 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Instruksi Impor Rincian
                </div>
                <ul class="list-disc list-inside pl-1 space-y-1">
                    <li>Gunakan format tanggal di Excel: <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-gray-200">YYYY-MM-DD</span>.</li>
                    <li>Kolom 'Status': <span class="font-bold">Terkirim</span> / <span class="font-bold">Dibatalkan</span>.</li>
                    <li>Kolom 'Jenis Surat': <span class="font-bold">Surat Masuk</span> / <span class="font-bold">Surat Keluar</span>.</li>
                </ul>
                <div class="mt-2">
                    <a href="{{ route('template.download', 'surat') }}" class="inline-block font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel Detail</a>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih File Excel (.xlsx / .xls) <span class="text-red-500">*</span></label>
                <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            
            <div class="flex justify-end gap-3 mt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalImportDetail')">Batal</x-button>
                <x-button variant="primary" type="submit" class="!bg-green-600 hover:!bg-green-700 border-none !px-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Unggah & Proses
                </x-button>
            </div>
        </form>
    </x-modal>

    <x-modal id="modalImportRekap" title="Impor Data Rekapitulasi via Excel" description="Impor data histori akumulasi bulanan langsung tanpa rincian surat satuan.">
        <form action="{{ route('import.rekap.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs text-gray-600 flex flex-col gap-3 shadow-inner mb-4">
                <div class="flex items-center gap-2 text-green-700 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Instruksi Impor Rekapitulasi
                </div>
                <ul class="list-disc list-inside pl-1 space-y-1">
                    <li>Kolom terdiri dari: Tahun, Bulan, Surat Masuk, Surat Keluar.</li>
                    <li>Jika ada data lama di bulan/tahun yang sama, akan tertimpa otomatis.</li>
                </ul>
                <div class="mt-2">
                    <a href="{{ route('surat.export.excel', ['jenis' => 'tabel1', 'tahun' => date('Y'), 'bulan' => 'semua']) }}" class="inline-block font-bold text-blue-700 hover:text-blue-900 underline">Unduh Contoh Template Rekap</a>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih File Excel (.xlsx / .xls) <span class="text-red-500">*</span></label>
                <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            
            <div class="flex justify-end gap-3 mt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalImportRekap')">Batal</x-button>
                <x-button variant="primary" type="submit" class="!bg-green-600 hover:!bg-green-700 border-none !px-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Unggah Rekap
                </x-button>
            </div>
        </form>
    </x-modal>
EOD;

$content = str_replace($oldModal, $newModals, $content);
file_put_contents('resources/views/surat-masuk-keluar.blade.php', $content);
echo "Modals replaced.\n";
