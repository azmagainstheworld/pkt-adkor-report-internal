<?php
$path = 'resources/views/jasakurir.blade.php';
$content = file_get_contents($path);

// 1. Remove inline errors block
$searchInlineErrors = '/@if \(\$errors->any\(\)\).*?<\/ul>\s*<\/div>\s*@endif/is';
$content = preg_replace($searchInlineErrors, '', $content);

// 2. Update errorModalCustom to also show validation errors
$searchErrorModal = '/@if \(session\(\'error_modal\'\)\).*?@endif/is';
$replaceErrorModal = <<<HTML
    <!-- ================= MODAL ERROR KUSTOM & VALIDASI ================= -->
    @if (session('error_modal') || \$errors->any())
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Peringatan Sistem</h3>
            <div class="text-sm text-gray-500 mb-6 text-left">
                @if(session('error_modal'))
                    <p class="text-center">{{ session('error_modal') }}</p>
                @else
                    <p class="font-bold text-gray-700 mb-2">Gagal memproses data. Periksa inputan Anda:</p>
                    <ul class="list-disc ml-5 text-xs text-red-500 space-y-1">
                        @foreach (\$errors->all() as \$error)
                            <li>{{ \$error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <button onclick="document.getElementById('errorModalCustom').remove()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                Kembali & Perbaiki
            </button>
        </div>
    </div>
    @endif
HTML;
$content = preg_replace($searchErrorModal, $replaceErrorModal, $content);

// 3. Add Javascript to strip leading zeros on all number inputs globally
$searchJS = "const canvasKurir = document.getElementById('canvas_kurirChart');";
$replaceJS = <<<JS
        // 1. Strip leading zeros for all number inputs globally
        document.addEventListener('input', function(e) {
            if (e.target.type === 'number') {
                let val = e.target.value;
                if (val.length > 1 && val.startsWith('0') && !val.startsWith('0.')) {
                    e.target.value = val.replace(/^0+/, '');
                    if (e.target.value === '') e.target.value = '0';
                }
            }
        });

        const canvasKurir = document.getElementById('canvas_kurirChart');
JS;
$content = str_replace($searchJS, $replaceJS, $content);

file_put_contents($path, $content);
echo "Jasa Kurir blade updated for errors and leading zeros!\n";
?>
