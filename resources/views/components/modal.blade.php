@props(['id', 'title', 'description' => ''])

<div id="{{ $id }}" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-visible flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $title }}</h3>
                    @if($description)
                        <p class="text-sm text-gray-500">{{ $description }}</p>
                    @endif
                </div>
            </div>
            <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="p-2 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Modal Body (Form dinamis akan masuk ke sini) -->
        <div class="p-6 overflow-y-auto custom-scrollbar">
            {{ $slot }}
        </div>

        <!-- Modal Footer (Tombol-tombol akan masuk ke sini) -->
        @if(isset($footer))
        <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
            {{ $footer }}
        </div>
        @endif
        
    </div>
</div>
