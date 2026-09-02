@extends('layouts.app')

@section('content')
<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">Manajemen Pengguna</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Daftarkan Karyawan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Daftarkan Akun Karyawan</h2>
            <p class="text-sm text-gray-500">Buat akun baru untuk karyawan agar dapat mengakses sistem AdkorReport</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- ================= FORM SECTION ================= -->
    <x-card class="!rounded-xl p-8 shadow-sm border border-gray-100 bg-white max-w-2xl">
        
        <!-- Tambahkan class novalidate-form dan atribut novalidate -->
        <form action="{{ route('admin.users.store') }}" method="POST" class="novalidate-form" novalidate>
            @csrf
            
            <div class="space-y-6">
                <!-- Field Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap Karyawan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Lucas Bennett" 
                           class="w-full px-4 py-2.5 bg-white border @error('name') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                    @error('name') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Field Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Perusahaan <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="karyawan@perusahaan.com" 
                           class="w-full px-4 py-2.5 bg-white border @error('email') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500 font-mono">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                    @error('email') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-gray-100">
                    <!-- Field Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required placeholder="••••••••" 
                               class="w-full px-4 py-2.5 bg-white border @error('password') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @else
                            <p class="text-[11px] text-gray-400 mt-1.5">Minimal 8 karakter. Berikan password ini kepada karyawan.</p>
                        @enderror
                    </div>

                    <!-- Field Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••" 
                               class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                    </div>
                </div>

                <!-- Field Role -->
                <div class="pt-2 border-t border-gray-100">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Role / Hak Akses <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required
                            class="w-full px-4 py-2.5 bg-white border @error('role') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500 cursor-pointer">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                        <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>
                            👤 Karyawan — Akses data & input biasa
                        </option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                            🛡️ Admin — Akses penuh + kelola pengguna & kolom
                        </option>
                        {{-- Super Admin hanya bisa dibuat via seeder/artisan, tidak tersedia di form --}}
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1.5">Super Admin tidak dapat didaftarkan melalui form. Hubungi pengembang sistem.</p>
                    @error('role') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 mt-10 pt-6 border-t border-gray-100">
                <x-button variant="primary" type="submit" class="!bg-[#F7941E] hover:!bg-orange-600 border-none !rounded-xl !py-2.5 shadow-sm text-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Daftarkan Akun
                </x-button>
            </div>
        </form>
    </x-card>
</main>

<script>
    // VALIDASI CLIENT-SIDE (Cegah submit kosong & munculkan teks merah)
    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500', 'bg-red-50');
                    field.classList.remove('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500', 'bg-red-50');
                    if (!field.classList.contains('border-gray-300')) field.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
            if (!isValid) e.preventDefault();
        });

        form.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    this.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
    });
</script>
@endsection
