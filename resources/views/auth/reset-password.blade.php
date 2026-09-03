<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Kata Sandi Baru</title>
    <!-- Menggunakan Vite untuk Asset -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 antialiased font-['Inter']">
    <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gray-100">
        
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Atur Kata Sandi Baru</h2>
        <p class="text-sm text-gray-500 mb-8">Pastikan kata sandi baru Anda kuat dan aman.</p>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <!-- Email (Hidden/Readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-100 text-gray-500 text-sm">
            </div>

            <!-- Password Baru -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#0056A3] text-sm transition-all"
                    placeholder="Masukkan kata sandi baru">
                
                <p id="password-req-msg" class="text-[12px] text-red-500 mt-2 font-medium">Kata sandi harus berisi kombinasi huruf, angka, dan simbol</p>
                <p id="password-strength" class="text-[12px] font-bold mt-1 hidden"></p>
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#0056A3] text-sm transition-all"
                    placeholder="Ketik ulang kata sandi baru">
                
                <p id="password-match-msg" class="text-[12px] text-red-500 mt-2 font-medium hidden">Kata sandi harus sama</p>
            </div>

            <!-- Menggunakan Komponen Button Laravel (Murni Statis Biru) -->
            <x-button type="submit" variant="secondary" class="w-full py-3.5 mt-4">
                Atur Ulang Kata Sandi
            </x-button>
        </form>
    </div>

    <!-- Script Validasi Klien -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            
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
                    strengthText.textContent = 'Kata Sandi: Lemah';
                    strengthText.className = 'text-[12px] font-bold mt-1 text-red-500';
                    reqMsg.style.display = 'block';
                } else if (strength === 2) {
                    strengthText.textContent = 'Kata Sandi: Sedang';
                    strengthText.className = 'text-[12px] font-bold mt-1 text-yellow-500';
                    reqMsg.style.display = 'block'; 
                } else if (strength === 3) {
                    strengthText.textContent = 'Kata Sandi: Kuat';
                    strengthText.className = 'text-[12px] font-bold mt-1 text-green-600';
                    reqMsg.style.display = 'none'; 
                }

                checkMatch();
            });

            function checkMatch() {
                if (confirm.value === '') {
                    matchMsg.classList.add('hidden');
                } else if (password.value !== confirm.value) {
                    matchMsg.classList.remove('hidden');
                    matchMsg.textContent = "Kata sandi harus sama";
                    matchMsg.classList.replace('text-green-600', 'text-red-500');
                } else {
                    matchMsg.classList.remove('hidden');
                    matchMsg.textContent = "Kata sandi cocok";
                    matchMsg.classList.replace('text-red-500', 'text-green-600');
                }
            }

            confirm.addEventListener('input', checkMatch);
        });
    </script>
</body>
</html>
