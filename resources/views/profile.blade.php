@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    <div class="flex justify-between items-end mb-8">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Profil Saya</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900">Pengaturan Akun</h2>
        </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- KARTU INFORMASI PRIBADI -->
        <x-card class="!rounded-2xl !p-0 overflow-visible shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 bg-white flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-[#0056A3] text-white flex items-center justify-center font-bold text-2xl flex-shrink-0 shadow-inner">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 font-medium">Pengguna Sistem AdkorReport</p>
                </div>
            </div>
            <div class="p-6 bg-gray-50/50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Email</label>
                        <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status Akun</label>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                        </span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- KARTU UBAH KATA SANDI -->
        <x-card class="!rounded-2xl shadow-sm border border-gray-100 bg-white p-6 md:p-8">
            <div class="mb-6">
                <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#F7941E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Ubah Kata Sandi
                </h3>
                <p class="text-sm text-gray-500">Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.</p>
            </div>

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6 max-w-xl">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0056A3] focus:bg-white transition-colors" placeholder="••••••••">
                    @error('current_password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru</label>
                    <input type="password" name="password" id="new_password" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0056A3] focus:bg-white transition-colors" placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    
                    <!-- Indikator Kekuatan -->
                    <div class="mt-2 flex items-center justify-between">
                        <p id="password-req-msg" class="text-[11px] text-gray-500 font-medium">Gunakan kombinasi huruf besar, kecil, angka, dan simbol.</p>
                        <p id="password-strength" class="text-[11px] font-bold hidden px-2 py-0.5 rounded-md"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="confirm_password" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#0056A3] focus:bg-white transition-colors" placeholder="••••••••">
                    <p id="password-match-msg" class="text-[11px] mt-1 font-bold hidden"></p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-[#0056A3] hover:bg-[#004280] text-white font-medium rounded-xl text-sm shadow-md shadow-blue-900/20 transition-all flex items-center justify-center gap-2">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const password = document.getElementById('new_password');
        const confirm = document.getElementById('confirm_password');
        
        const reqMsg = document.getElementById('password-req-msg');
        const strengthText = document.getElementById('password-strength');
        const matchMsg = document.getElementById('password-match-msg');

        password.addEventListener('input', (e) => {
            const val = e.target.value;
            strengthText.classList.remove('hidden');

            let strength = 0;
            if (val.length >= 8) strength += 1;
            if (val.match(/(?=.*[a-z])(?=.*[A-Z])/)) strength += 1;
            if (val.match(/(?=.*[0-9])(?=.*[!@#$%^&*])/)) strength += 1;

            if (val === '') {
                strengthText.classList.add('hidden');
                reqMsg.style.display = 'block';
            } else if (strength <= 1) {
                strengthText.textContent = 'LEMAH';
                strengthText.className = 'text-[10px] font-bold mt-1 px-2 py-0.5 rounded bg-red-100 text-red-700';
            } else if (strength === 2) {
                strengthText.textContent = 'SEDANG';
                strengthText.className = 'text-[10px] font-bold mt-1 px-2 py-0.5 rounded bg-yellow-100 text-yellow-700';
            } else if (strength === 3) {
                strengthText.textContent = 'KUAT';
                strengthText.className = 'text-[10px] font-bold mt-1 px-2 py-0.5 rounded bg-green-100 text-green-700';
            }

            checkMatch();
        });

        function checkMatch() {
            if (confirm.value === '') {
                matchMsg.classList.add('hidden');
            } else if (password.value !== confirm.value) {
                matchMsg.classList.remove('hidden');
                matchMsg.textContent = "✘ Kata sandi tidak cocok";
                matchMsg.className = 'text-[11px] font-medium mt-1 text-red-500';
            } else {
                matchMsg.classList.remove('hidden');
                matchMsg.textContent = "✔ Kata sandi cocok";
                matchMsg.className = 'text-[11px] font-medium mt-1 text-green-600';
            }
        }

        confirm.addEventListener('input', checkMatch);
    });
</script>
@endsection
