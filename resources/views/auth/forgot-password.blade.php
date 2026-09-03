<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi</title>
    <!-- Menggunakan Vite untuk Asset -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 antialiased font-['Inter']">
    <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gray-100 text-center">
        
        <!-- Icon Kunci -->
        <div class="w-16 h-16 bg-pink-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-2">Lupa Kata Sandi?</h2>
        <p class="text-sm text-gray-500 mb-8">Jangan khawatir! Silakan masukkan alamat email yang terhubung dengan akun Anda. Kami akan mengirimkan instruksi untuk mengatur ulang kata sandi.</p>

        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg text-left">
                {{ session('status') }}
            </div>
        @endif

        <!-- Menambahkan atribut 'novalidate' di sini -->
        <form action="{{ route('password.email') }}" method="POST" class="text-left space-y-5" novalidate>
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0056A3] focus:border-transparent text-sm transition-all"
                    placeholder="nama@contoh.com">
                @error('email')
                    <p class="text-[13px] text-red-500 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <x-button type="submit" variant="secondary" class="w-full py-3.5">
                Kirim
            </x-button>
        </form>

        <a href="/login" class="inline-block mt-6 text-sm text-gray-500 hover:text-gray-900 font-medium">
            ← Kembali ke halaman login
        </a>
    </div>
</body>
</html>
