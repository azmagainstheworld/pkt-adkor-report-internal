<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdkorReport - PKT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Import Font Poppins (Bawaan) dan Merriweather (Khusus Judul SIPAKAR) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Menambahkan class untuk font Merriweather */
        .font-merriweather { font-family: 'Merriweather', serif; }
    </style>
</head>
<body class="bg-[#F8F9FA] font-sans antialiased text-gray-800">

    <div class="flex h-screen overflow-visible">
        
        <!-- ================= SIDEBAR ================= -->
        <aside class="w-[280px] bg-pkt-biru text-white flex flex-col justify-between flex-shrink-0 h-full overflow-y-auto hidden-scrollbar">
            <div>
                <!-- Logo Area -->
                <div class="flex items-center gap-3 p-6">
                    <!-- Wrapper Logo (Kotak putih dihapus, background dibuat transparan) -->
                    <div class="w-12 h-12 flex items-center justify-center flex-shrink-0 drop-shadow-md">
                        <!-- Mengarah ke logo transparan yang benar -->
                        <img src="{{ asset('images/logo-sipakar.png') }}" alt="Logo SIPAKAR" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <!-- Tulisan SIPAKAR dengan Merriweather dan 2 Warna -->
                        <h1 class="text-2xl font-bold tracking-wide font-merriweather drop-shadow-sm">
                            <span class="text-[#F7941E]">SI</span><span class="text-white">PAKAR</span>
                        </h1>
                        <!-- Tulisan "Adkor - PKT" Dihapus di sini -->
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="px-4 pb-6 space-y-1">
                    <p class="text-[11px] font-semibold text-blue-300 mb-4 px-3 tracking-wider">NAVIGASI UTAMA</p>
                    
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('summary.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('summary.index') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <!-- Ikon Pie Chart untuk Summary -->
                        <svg class="w-5 h-5 {{ request()->routeIs('summary.index') ? 'opacity-100' : 'opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        Summary
                    </a>

                    <a href="{{ route('struktur-organisasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('struktur-organisasi') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Struktur Organisasi
                    </a>

                    <a href="{{ route('program-strategis.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('program-strategis') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Program Strategis
                    </a>
                    
                    <a href="{{ route('karyawan.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('karyawan', 'karyawan/*') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Karyawan
                    </a>

                    <a href="{{ route('ketidakhadiran.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('ketidakhadiran', 'ketidakhadiran/*') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Ketidakhadiran
                    </a>

                    <a href="/anggaran" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('anggaran') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Anggaran
                    </a>

                    <a href="{{ route('perizinan-perkantoran.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('perizinan-perkantoran.*') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 {{ request()->routeIs('perizinan-perkantoran.*') ? 'opacity-100' : 'opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Perizinan Perkantoran
                    </a>

                    <a href="{{ route('pelaporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('pelaporan.*') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Pelaporan
                    </a>

                    <!-- ================= GRUP MENU ADMINISTRASI ================= -->
                    <div>
                        <div class="flex items-center justify-between px-3 py-2.5 {{ request()->is('administrasi*') ? 'bg-white/15 text-white' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium text-sm cursor-pointer mt-1">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Administrasi
                            </div>
                            <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        <div class="pl-11 pr-2 mt-1 space-y-1 relative before:absolute before:inset-y-0 before:left-[21px] before:w-px before:bg-blue-800">
                            <a href="/administrasi/pengiriman-dokumen" class="block py-2 text-sm {{ request()->is('administrasi/pengiriman-dokumen') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                Pengiriman Dokumen
                            </a>
                            <a href="/administrasi/jasa-kurir" class="block py-2 text-sm {{ request()->is('administrasi/jasa-kurir') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                Jasa Kurir
                            </a>
                            <a href="/administrasi/jasa-fotocopy" class="block py-2 text-sm {{ request()->is('administrasi/jasa-fotocopy') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                Jasa Fotocopy
                            </a>
                            <a href="/administrasi/undangan" class="block py-2 text-sm {{ request()->is('administrasi/undangan') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                Undangan
                            </a>
                            <a href="{{ route('surat.index') }}" class="block py-2 text-sm {{ request()->routeIs('surat.*') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                Surat Masuk & Keluar
                            </a>
                            <a href="/administrasi/bar-sk-memo" class="block py-2 text-sm {{ request()->is('administrasi/bar-sk-memo') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                @if(request()->is('administrasi/bar-sk-memo'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-pkt-jingga"></span>
                                @endif
                                BAR SK Memo
                            </a>
                            <a href="/administrasi/pemeliharaan" class="block py-2 text-sm {{ request()->is('administrasi/pemeliharaan') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                @if(request()->is('administrasi/pemeliharaan'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-pkt-jingga"></span>
                                @endif
                                Pemeliharaan
                            </a>
                        </div>
                    </div>

                    <!-- ================= GRUP MENU KEARSIPAN ================= -->
                    <div>
                        <div class="flex items-center justify-between px-3 py-2.5 {{ request()->is('kearsipan*') ? 'bg-white/15 text-white' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium text-sm cursor-pointer mt-1">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                Kearsipan
                            </div>
                            <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        <div class="pl-11 pr-2 mt-1 space-y-1 relative before:absolute before:inset-y-0 before:left-[21px] before:w-px before:bg-blue-800">
                            <!-- PA Non Teknik (Tekstual) -->
                            <a href="{{ route('pa-tekstual.index') }}" class="block py-2 text-sm {{ request()->routeIs('pa-tekstual.*') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                @if(request()->routeIs('pa-tekstual.*')) <span class="w-1.5 h-1.5 rounded-full bg-pkt-jingga"></span> @endif
                                PA Non Teknik (Tekstual)
                            </a>
                            <!-- PA Non Teknik (Non Tekstual) -->
                            <a href="{{ route('non-teknik-non-tekstual.index') }}" class="block py-2 text-sm {{ request()->routeIs('non-teknik-non-tekstual.index') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors"> 
                                @if(request()->routeIs('non-teknik-non-tekstual.index')) 
                                    <span class="w-1.5 h-1.5 rounded-full bg-pkt-jingga"></span> 
                                @endif
                                PA Non Teknik (Non Tekstual)
                            </a>
                            <!-- PA Teknik -->
                            <a href="/kearsipan/pa-teknik" class="block py-2 text-sm {{ request()->is('kearsipan/pa-teknik') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                @if(request()->is('kearsipan/pa-teknik'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-pkt-jingga"></span>
                                @endif
                                PA Teknik
                            </a>
                            <!-- DOF -->
                            <a href="/kearsipan/dof" class="block py-2 text-sm {{ request()->is('kearsipan/dof') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                @if(request()->is('kearsipan/dof'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-pkt-jingga"></span>
                                @endif
                                DOF
                            </a>
                            <!-- Rekap (Aktif) -->
                            <a href="/kearsipan/rekap" class="block py-2 text-sm {{ request()->is('kearsipan/rekap') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
                                @if(request()->is('kearsipan/rekap'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-pkt-jingga"></span>
                                @endif
                                Rekap
                            </a>
                        </div>
                    </div>

                    <!-- ================= MENU ADMIN (DIPISAH GARIS TIPIS) ================= -->
                    <div class="pt-2 mt-2 border-t border-blue-900/60 space-y-1">

                        <a href="{{ route('masalah-kendala.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('masalah-kendala.*') || request()->is('masalah-kendala') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 {{ request()->routeIs('masalah-kendala.*') || request()->is('masalah-kendala') ? 'opacity-100' : 'opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Masalah/Kendala Operasional
                        </a>

                        <!-- INI TOMBOL SAKTI CETAK LAPORAN -->
                        <a href="{{ route('cetak.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('cetak.laporan.*') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 {{ request()->routeIs('cetak.laporan.*') ? 'opacity-100' : 'opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Cetak Laporan Bulanan
                        </a>

                        
                        @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <!-- Manajemen Pengguna -->
                        <a href="/admin/manajemen-pengguna" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/manajemen-pengguna') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Manajemen Pengguna
                        </a>
                        @endif

                        <a href="/admin/log-audit" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/log-audit') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Log Audit
                        </a>

                    </div>
                </nav>
            </div>

            <!-- ================= USER PROFILE ================= -->
            <a href="{{ route('profile.index') }}" class="block p-4 mx-4 mb-6 {{ request()->routeIs('profile.*') ? 'bg-white/15 border-white/20' : 'bg-white/5 border-white/10 hover:bg-white/10' }} rounded-xl border flex items-center gap-3 transition-colors cursor-pointer group">
                <div class="w-10 h-10 rounded-full bg-pkt-jingga text-white flex items-center justify-center font-bold text-sm flex-shrink-0 group-hover:scale-105 transition-transform">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-visible flex-1">
                    <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-blue-200 truncate">{{ auth()->user()->email ?? 'user@email.com' }}</p>
                </div>
                <svg class="w-4 h-4 text-blue-300 opacity-0 group-hover:opacity-100 transition-opacity -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </aside>

        <!-- ================= MAIN CONTENT AREA ================= -->
        <div class="flex-1 flex flex-col min-w-0 overflow-visible">
            
            <!-- Navbar -->
            <header class="h-[72px] bg-white border-b border-gray-200 flex items-center px-8 flex-shrink-0 z-10">
                <div class="w-1/3 flex justify-start"></div>
                <div class="w-1/3 flex justify-center">
                    <form method="GET" action="{{ url()->current() }}" class="relative w-full max-w-lg m-0">
                        @foreach(request()->except(['search', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-10 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-colors" placeholder="Cari data di halaman ini... (Tekan Enter)">
                            
                            @if(request('search'))
                                <a href="{{ request()->fullUrlWithoutQuery('search') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition-colors" title="Hapus Pencarian">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="w-1/3 flex items-center justify-end gap-5">
                    <form action="{{ route('logout') }}" method="POST" class="inline m-0 p-0 flex items-center">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 transition-colors" title="Keluar dari Sistem">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- RUANG KOSONG UNTUK KONTEN HALAMAN -->
            @yield('content')
            
        </div>
        
        <!-- Floating Action Button (FAB) Help -->
        <button class="fixed bottom-8 right-8 w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-gray-800 transition-colors z-50 text-xl font-bold">?</button>
    </div>

    <!-- SCRIPT GLOBAL UNTUK MENGATUR MODAL LAINNYA -->
    <script>
        if (typeof window.openModal !== 'function') {
            window.openModal = function(id) { 
                const el = document.getElementById(id);
                if(el) el.classList.remove('hidden'); 
            };
        }
        if (typeof window.closeModal !== 'function') {
            window.closeModal = function(id) { 
                const el = document.getElementById(id);
                if(el) el.classList.add('hidden'); 
            };
        }
    </script>

    @if(request('search'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchTerm = @json(request('search'));
            if (!searchTerm || searchTerm.trim() === '') return;
            
            const walker = document.createTreeWalker(
                document.body,
                NodeFilter.SHOW_TEXT,
                {
                    acceptNode: function(node) {
                        const parent = node.parentNode;
                        if (parent.tagName === 'SCRIPT' || parent.tagName === 'STYLE' || parent.tagName === 'NOSCRIPT' || parent.tagName === 'MARK') {
                            return NodeFilter.FILTER_REJECT;
                        }
                        if (node.nodeValue.toLowerCase().includes(searchTerm.toLowerCase())) {
                            return NodeFilter.FILTER_ACCEPT;
                        }
                        return NodeFilter.FILTER_SKIP;
                    }
                }
            );

            const nodesToReplace = [];
            let node;
            while (node = walker.nextNode()) {
                nodesToReplace.push(node);
            }

            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            
            nodesToReplace.forEach(node => {
                const parent = node.parentNode;
                const text = node.nodeValue;
                const html = text.replace(regex, '<mark class="bg-yellow-300 rounded px-1 text-black font-bold">\$1</mark>');
                
                const wrapper = document.createElement('span');
                wrapper.innerHTML = html;
                
                while (wrapper.firstChild) {
                    parent.insertBefore(wrapper.firstChild, node);
                }
                parent.removeChild(node);
            });
            
            const firstMark = document.querySelector('mark');
            if(firstMark) {
                firstMark.scrollIntoView({behavior: "smooth", block: "center"});
            }
        });
    </script>
    @endif
</body>

</html>