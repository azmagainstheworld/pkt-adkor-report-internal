<?php

$filePath = 'resources/views/pengiriman-dokumen.blade.php';
$content = file_get_contents($filePath);

// Patch the error modal to handle session('error') as well
$errorOld = <<<HTML
    <!-- ================= MODAL ERROR KUSTOM ================= -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Penambahan Gagal</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none'; openModal('modalAturKolom');" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Kembali & Perbaiki
            </button>
        </div>
    </div>
    @endif
HTML;

$errorNew = <<<HTML
    <!-- ================= MODAL ERROR KUSTOM ================= -->
    @if (session('error_modal') || session('error'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Aksi Gagal</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') ?? session('error') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Tutup
            </button>
        </div>
    </div>
    @endif
HTML;

$content = str_replace($errorOld, $errorNew, $content);

file_put_contents($filePath, $content);
echo "Blade error modal updated.\n";
