<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdkorReport - PKT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#F8F9FA] font-sans antialiased text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        <!-- ================= SIDEBAR ================= -->
        <aside class="w-[280px] bg-pkt-biru text-white flex flex-col justify-between flex-shrink-0 h-full overflow-y-auto hidden-scrollbar">
            <div>
                <!-- Logo Area -->
                <div class="flex items-center gap-3 p-6">
                    <div class="w-10 h-10 bg-pkt-jingga rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-wide">AdkorReport</h1>
                        <p class="text-[11px] text-blue-200 tracking-wider">ADKOR - PKT</p>
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Summary
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

                    <a href="{{ route('salinan-anggaran') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('salinan-anggaran') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Salinan Anggaran
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

                    <!-- MENU MASALAH / KENDALA BERDIRI SENDIRI -->
                    <a href="{{ route('masalah-kendala.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('masalah-kendala.*') || request()->is('masalah-kendala') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 {{ request()->routeIs('masalah-kendala.*') || request()->is('masalah-kendala') ? 'opacity-100' : 'opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Masalah/Kendala Operasional
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
                            <a href="/administrasi/surat-masuk-keluar" class="block py-2 text-sm {{ request()->is('administrasi/surat-masuk-keluar') ? 'text-pkt-jingga font-bold flex items-center gap-2' : 'text-blue-200 hover:text-white' }} transition-colors">
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

                    <!-- ================= MENU ADMIN (DIPISAH GARIS TIPIS DENGAN JARAK SAMA) ================= -->
                    <div class="pt-2 mt-2 border-t border-blue-900/60 space-y-1">
                        
                        <!-- Manajemen Pengguna -->
                        <a href="/admin/manajemen-pengguna" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/manajemen-pengguna') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Manajemen Pengguna
                        </a>

                        <a href="/admin/log-audit" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/log-audit') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Log Audit
                        </a>

                        <a href="{{ route('struktur-organisasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('struktur-organisasi') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Struktur Organisasi
                        </a>
                    </div>
                </nav>
            </div>

            <!-- ================= USER PROFILE ================= -->
            <div class="p-4 mx-4 mb-6 bg-white/5 rounded-xl border border-white/10 flex items-center gap-3 cursor-default hover:bg-white/10 transition-colors">
                <div class="w-10 h-10 rounded-full bg-pkt-jingga text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-blue-200 truncate">{{ auth()->user()->email ?? 'user@email.com' }}</p>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT AREA ================= -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Navbar -->
            <header class="h-[72px] bg-white border-b border-gray-200 flex items-center px-8 flex-shrink-0 z-10">
                
                <!-- Kiri: Spacer untuk menyeimbangkan posisi tengah -->
                <div class="w-1/3 flex justify-start"></div>

                <!-- Tengah: Search Bar -->
                <div class="w-1/3 flex justify-center">
                    <div class="relative w-full max-w-lg">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-colors" placeholder="Cari menu, laporan, atau data...">
                    </div>
                </div>

                <!-- Kanan: Actions & Logout -->
                <div class="w-1/3 flex items-center justify-end gap-5">
                    
                    <!-- Tombol Logout dengan Icon -->
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
</body>
</html>