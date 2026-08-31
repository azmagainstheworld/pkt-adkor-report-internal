@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <x-success-modal id="successModal" title="Berhasil!" />

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-8 gap-4">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Struktur Organisasi</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Struktur Organisasi</h2>
            <p class="text-sm text-gray-500">Bagan dan lingkup koordinasi Unit Kerja Administrasi Korporat</p>
        </div>
        
        <!-- Tombol Edit: Hanya muncul jika rolenya admin -->
        @if(auth()->user()->role === 'admin')
        <div>
            <button onclick="document.getElementById('modalEditStruktur').classList.remove('hidden')" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl shadow-sm text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Ubah Konten
            </button>
        </div>
        @endif
    </div>

    <!-- Teks Deskripsi (Dinamis) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8 relative overflow-visible">
        <!-- Aksen Garis Kiri -->
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-600 rounded-l-2xl"></div>
        
        <!-- nl2br untuk menjaga format Enter/Baris Baru -->
        <div class="text-gray-700 text-[15px] leading-relaxed ml-2">
            {!! nl2br(e($struktur->deskripsi)) !!}
        </div>
    </div>

    <!-- Area Gambar Struktur Organisasi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8 flex flex-col items-center justify-center min-h-[400px]">
        @if($struktur->gambar)
            <img src="{{ asset('storage/' . $struktur->gambar) }}" alt="Struktur Organisasi" class="max-w-full h-auto rounded-lg shadow-sm border border-gray-100">
        @else
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-gray-900 font-semibold mb-1">Belum Ada Gambar Struktur</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto">
                    @if(auth()->user()->role === 'admin')
                        Silakan klik tombol "Ubah Konten" di kanan atas untuk mengunggah bagan struktur organisasi.
                    @else
                        Gambar struktur organisasi sedang dalam proses pembaruan oleh Administrator.
                    @endif
                </p>
            </div>
        @endif
    </div>

</main>

<!-- ==========================================
      MODAL UBAH KONTEN (Khusus Admin)
=========================================== -->
@if(auth()->user()->role === 'admin')
<div id="modalEditStruktur" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="document.getElementById('modalEditStruktur').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-visible flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Ubah Konten Struktur Organisasi</h3>
            <button type="button" onclick="document.getElementById('modalEditStruktur').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto">
            <form id="formEditStruktur" action="{{ route('struktur-organisasi.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Input Teks Deskripsi -->
                <div class="mb-6">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1.5">Teks Keterangan / Landasan SK <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" rows="5" required 
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-blue-500 transition-colors" 
                              placeholder="Masukkan teks keterangan atau SK Direksi...">{{ old('deskripsi', $struktur->deskripsi) }}</textarea>
                    <p class="text-[11px] text-gray-500 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Gunakan tombol Enter untuk membuat baris baru.
                    </p>
                    @error('deskripsi') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Input File Gambar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Unggah Bagan Struktur (Gambar)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 transition-colors relative cursor-pointer" onclick="document.getElementById('gambar').click()">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <span class="relative cursor-pointer bg-transparent rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    Pilih File Gambar
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">PNG, JPG, SVG hingga 5MB</p>
                            <p id="fileNameDisplay" class="text-xs font-bold text-blue-600 mt-2 hidden"></p>
                        </div>
                    </div>
                    <!-- Actual hidden input -->
                    <input id="gambar" name="gambar" type="file" accept="image/png, image/jpeg, image/jpg, image/svg+xml" class="hidden" onchange="document.getElementById('fileNameDisplay').textContent = this.files[0].name; document.getElementById('fileNameDisplay').classList.remove('hidden');">
                    @error('gambar') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
            <button type="button" onclick="document.getElementById('modalEditStruktur').classList.add('hidden')" class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</button>
            <button type="submit" form="formEditStruktur" class="px-5 py-2.5 bg-blue-600 rounded-xl text-sm font-medium text-white hover:bg-blue-700 transition-colors shadow-sm">Simpan Perubahan</button>
        </div>

    </div>
</div>
@endif

@endsection
