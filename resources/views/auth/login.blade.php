<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - AdkorReport PKT</title>
    <!-- Menggunakan CDN Tailwind untuk render instan -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex bg-gray-50 antialiased">

    <!-- ================= SISI KIRI (60%): BRANDING KORPORAT ================= -->
    <div class="hidden lg:flex lg:w-[60%] relative overflow-visible flex-col items-center justify-center p-12">
        
        <!-- Background Gradient Utama (Biru Kaltim) -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#003D73] via-[#0056A3] to-[#0056A3] z-0"></div>
        
        <!-- Aksen Glow Oranye Halus di Pojok Kanan Bawah -->
        <div class="absolute -bottom-32 -right-32 w-[500px] h-[500px] bg-[#F7941E] opacity-20 rounded-full blur-[100px] z-0 pointer-events-none"></div>

        <!-- Elemen Dekoratif: Lingkaran Outline Geometris Besar -->
        <svg class="absolute inset-0 w-full h-full pointer-events-none z-0" xmlns="http://www.w3.org/2000/svg">
            <circle cx="20%" cy="20%" r="350" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="2" />
            <circle cx="85%" cy="85%" r="450" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="2" />
            <circle cx="50%" cy="110%" r="300" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="3" />
        </svg>

        <!-- Konten Kiri (Logo & Judul) -->
        <div class="relative z-10 flex flex-col items-center text-center">
            
            <!-- Frame Logo (Placeholder) -->
            <div class="w-28 h-28 border border-white/30 rounded-xl flex items-center justify-center mb-10 backdrop-blur-sm bg-white/5 shadow-lg">
                <span class="text-white/70 text-xs font-semibold tracking-[0.2em]">LOGO</span>
            </div>

            <!-- Judul Besar -->
            <h1 class="text-3xl xl:text-4xl font-bold text-white leading-snug max-w-2xl mb-4">
                Sistem Pelaporan Internal<br>Departemen Administrasi Korporat
            </h1>
            
            <!-- Sub-judul -->
            <p class="text-[17px] text-white/70 max-w-xl font-medium tracking-wide">
                PT Pupuk Kalimantan Timur — Departemen Administrasi Korporat
            </p>
        </div>
    </div>

    <!-- ================= SISI KANAN (40%): FORM LOGIN ================= -->
    <div class="w-full lg:w-[40%] bg-white flex flex-col justify-center px-8 sm:px-16 lg:px-12 xl:px-20 relative z-10 shadow-[-10px_0_30px_rgba(0,0,0,0.05)]">
        <div class="w-full max-w-sm mx-auto">
            
            <!-- Mobile Logo Placeholder (Hanya terlihat di layar kecil) -->
            <div class="lg:hidden w-16 h-16 border border-gray-200 rounded-lg flex items-center justify-center mb-8 bg-gray-50 shadow-sm">
                <span class="text-gray-400 text-[10px] font-semibold tracking-widest">LOGO</span>
            </div>

            <!-- Header Form -->
            <div class="mb-10">
                <p class="text-sm text-gray-500 font-medium mb-1.5">Selamat Datang Kembali</p>
                <h2 class="text-3xl font-bold text-[#003D73] tracking-tight">Masuk ke Akun Anda</h2>
            </div>

            <!-- Form Autentikasi -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Field: Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-md border {{ $errors->has('email') ? 'border-red-500 bg-red-50/30' : 'border-gray-300 focus:border-[#0056A3]' }} focus:outline-none focus:ring-1 focus:ring-[#0056A3] text-gray-900 shadow-sm transition-colors text-sm"
                        placeholder="Masukkan email Anda">
                </div>

                <!-- Field: Kata Sandi -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <!-- ATRIBUT REQUIRED DIKEMBALIKAN -->
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 rounded-md border {{ $errors->has('email') ? 'border-red-500 text-red-900 bg-red-50/30' : 'border-gray-300 focus:border-[#0056A3]' }} focus:outline-none focus:ring-1 focus:ring-[#0056A3] text-gray-900 shadow-sm transition-colors text-sm pr-12"
                            placeholder="Masukkan kata sandi Anda">
                        
                        <!-- Toggle Show/Hide Icon -->
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-[#F7941E] transition-colors focus:outline-none">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    @error('email')
                    <!-- Pesan Error Validasi Dinamis -->
                    <p class="text-[13px] text-red-500 mt-2 font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-[#0056A3] focus:ring-[#0056A3] border-gray-300 rounded cursor-pointer">
                        <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                            Remember me
                        </label>
                    </div>
                    
                    <a href="{{ route('password.request') }}" class="text-sm font-bold text-gray-900 hover:text-[#0056A3] transition-colors">
                        Lupa Kata Sandi?
                    </a>
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                    class="w-full bg-[#0056A3] hover:bg-[#F7941E] text-white font-semibold py-3 px-4 rounded-md shadow-sm shadow-[#0056A3]/20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F7941E] text-sm mt-8">
                    Masuk
                </button>
            </form>

            <!-- Footer Teks Bantuan -->
            <p class="text-sm text-gray-500 text-center mt-8">
                Lupa kata sandi? <a href="#" class="text-[#0056A3] hover:text-[#F7941E] font-medium transition-colors">Hubungi Admin</a>
            </p>
            
        </div>
    </div>

    <!-- Script untuk Toggle Password SAJA -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('toggle-password');

            // Fitur Lihat/Sembunyikan Kata Sandi
            toggleBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
            });
        });
    </script>
</body>
</html>
