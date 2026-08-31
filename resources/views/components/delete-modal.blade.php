@props(['id', 'action' => '', 'title' => 'Konfirmasi Hapus', 'message' => 'Apakah Anda yakin ingin menghapus data ini?'])

<div id="{{ $id }}" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>
    
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 overflow-visible">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
                <p class="text-sm text-gray-500">{{ $message }}</p>
            </div>
        </div>

        <form id="formDelete_{{ $id }}" action="{{ $action }}" method="POST" class="flex justify-end gap-3 mt-6" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.classList.add('opacity-50', 'cursor-not-allowed'); btn.innerHTML = 'Menghapus...';">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition-colors shadow-lg shadow-red-600/20">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(modalId, deleteUrl) {
        const modal = document.getElementById(modalId);
        const form = modal.querySelector('form');
        form.action = deleteUrl;
        modal.classList.remove('hidden');
    }
</script>
