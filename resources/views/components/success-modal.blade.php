@props(['id' => 'successModal', 'title' => 'Berhasil!'])

@if (session('success'))
<div id="{{ $id }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 text-center relative transform transition-all scale-100 animate-bounce-short">
        
        <!-- Icon Sukses -->
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $title }}</h3>
        <p class="text-sm text-gray-600 mb-6">{{ session('success') }}</p>

        <button type="button" onclick="document.getElementById('{{ $id }}').style.display='none'" class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-green-600/20">
            OK, Mengerti
        </button>
    </div>
</div>
@endif