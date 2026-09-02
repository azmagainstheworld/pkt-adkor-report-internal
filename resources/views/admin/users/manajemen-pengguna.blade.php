@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <x-success-modal id="successModal" title="Berhasil!" />

    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-500">Admin</span>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Manajemen Pengguna</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Manajemen Pengguna</h2>
            <p class="text-sm text-gray-500">Kelola status akun dan keamanan pengguna sistem</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-[#F7941E] hover:bg-orange-600 border-none rounded-xl shadow-sm text-sm font-medium text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Daftarkan Karyawan
            </a>
        </div>
    </div>

    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Akun Pengguna</h3>
                <p class="text-xs text-gray-400">Menampilkan seluruh pengguna yang terdaftar di aplikasi AdkorReport</p>
            </div>
            
            <form action="{{ route('admin.users.index') }}" method="GET" class="relative m-0 p-0Form">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="px-4 py-1.5 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:outline-none focus:bg-white focus:border-blue-500 transition-colors">
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </form>
        </div>

        <x-table :headers="['Nama', 'Email', 'Role', 'Status Akun', 'Aktivitas', 'Aksi']">
            @forelse($users as $user)
                @php
                    $colors = ['bg-blue-600', 'bg-orange-600', 'bg-purple-600', 'bg-teal-600', 'bg-red-600', 'bg-green-600'];
                    $avatarColor = $colors[$user->id % count($colors)];
                    $initials = collect(explode(' ', $user->name))->map(fn($segment) => $segment[0])->take(2)->join('');
                @endphp
                <tr class="hover:bg-gray-50 transition-colors text-sm {{ $loop->iteration % 2 == 0 ? 'bg-gray-50/60' : '' }}">
                    <td class="px-6 py-4 font-medium text-gray-900 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $avatarColor }} text-white flex items-center justify-center text-xs font-bold">{{ strtoupper($initials) }}</div>
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="text-xs text-gray-400 font-normal">(Anda)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 font-mono text-xs">{{ $user->email }}</td>
                    
                    <td class="px-6 py-4">
                        @if(strtolower($user->role) === 'super_admin')
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-300">⭐ Super Admin</span>
                        @elseif(strtolower($user->role) === 'admin')
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">Admin</span>
                        @else
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">Karyawan</span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        @if($user->is_active)
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700 border border-green-200">Aktif</span>
                        @else
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">Nonaktif</span>
                        @endif
                    </td>
                    {{-- Berikan ID unik pada container aktivitas ini --}}
                    <td class="px-6 py-4 relative" id="status-aktivitas-{{ $user->id }}">
                        {{-- Tampilan Default (PHP Render pertama kali) --}}
                        @if($user->isOnline())
                            <div class="flex items-center gap-2 text-green-600 font-medium">
                                <span class="relative flex h-2.5 w-2.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                </span>
                                Online
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-gray-500">
                                <span class="relative flex h-2.5 w-2.5">
                                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gray-400"></span>
                                </span>
                                Offline
                            </div>
                        @endif
                    </td>
                    
                    
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            @if($user->isSuperAdmin())
                                {{-- Super Admin tidak bisa diubah status atau dihapus oleh siapapun --}}
                                <span class="text-xs text-yellow-600 font-semibold italic flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                    Terlindungi
                                </span>
                            @elseif($user->id !== auth()->id())
                                <label class="relative inline-flex items-center cursor-pointer mt-1" 
                                       title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                       onclick="event.preventDefault(); openToggleModal('{{ route('admin.users.toggle', $user->id) }}', '{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}', '{{ $user->name }}')"> 
                                    <input type="checkbox" class="sr-only peer" {{ $user->is_active ? 'checked' : '' }} readonly>
                                    <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                                </label>

                                <button type="button" 
                                        onclick="openDeleteModal('deleteModal', '{{ route('admin.users.destroy', $user->id) }}')" 
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @else
                                <span class="text-xs text-gray-400 italic">No Action</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 rounded-b-xl bg-white">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Tidak ada data pengguna ditemukan.
                    </td>
                </tr>
            @endforelse
        </x-table>
        
        <div class="p-4 border-t border-gray-100 bg-white rounded-b-xl">
            {{ $users->links() }}
        </div>
    </x-card>
</main>

<!-- MODAL KONFIRMASI UBAH STATUS (AKTIF/NONAKTIF) -->
<div id="toggleModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeToggleModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 overflow-visible">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900" id="toggleModalTitle">Konfirmasi Ubah Status</h3>
                <p class="text-sm text-gray-500" id="toggleModalMessage">Apakah Anda yakin?</p>
            </div>
        </div>

        <form id="formToggleStatus" action="" method="POST" class="flex justify-end gap-3 mt-6">
            @csrf
            @method('PATCH')
            <button type="button" onclick="closeToggleModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">Batal</button>
            <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-xl text-sm font-medium hover:bg-orange-700 transition-colors shadow-lg shadow-orange-600/20">Ya, Lanjutkan</button>
        </form>
    </div>
</div>

<x-delete-modal id="deleteModal" title="Hapus Permanen Akun" message="Apakah Anda yakin ingin menghapus akun ini secara permanen? Data yang telah dihapus tidak dapat dikembalikan." />

<script>
    function openToggleModal(url, actionText, userName) {
        document.getElementById('formToggleStatus').action = url;
        document.getElementById('toggleModalTitle').innerText = 'Konfirmasi ' + actionText;
        document.getElementById('toggleModalMessage').innerText = 'Apakah Anda yakin ingin ' + actionText.toLowerCase() + ' akun milik ' + userName + '?';
        document.getElementById('toggleModal').classList.remove('hidden');
    }

    function closeToggleModal() {
        document.getElementById('toggleModal').classList.add('hidden');
    }

    // ==========================================
    // WEBSOCKET: REAL-TIME ONLINE PRESENCE
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
        // Template HTML untuk status Online & Offline
        const templateOnline = `
            <div class="flex items-center gap-2 text-green-600 font-medium">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                </span>
                Online
            </div>
        `;

        const templateOffline = `
            <div class="flex items-center gap-2 text-gray-500">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gray-400"></span>
                </span>
                Offline
            </div>
        `;

        // Menggunakan Echo untuk bergabung dengan Presence Channel
        Echo.join('online-users')
            // 'here' dipanggil sekali saat kita baru memuat halaman (mendapatkan daftar siapa saja yang online saat ini)
            .here((users) => {
                users.forEach((user) => {
                    const statusTd = document.getElementById('status-aktivitas-' + user.id);
                    if(statusTd) statusTd.innerHTML = templateOnline;
                });
            })
            // 'joining' dipanggil otomatis secara real-time saat ada user lain yang baru login / membuka web
            .joining((user) => {
                const statusTd = document.getElementById('status-aktivitas-' + user.id);
                if(statusTd) statusTd.innerHTML = templateOnline;
            })
            // 'leaving' dipanggil otomatis secara real-time saat user lain menutup tab web atau logout
            .leaving((user) => {
                const statusTd = document.getElementById('status-aktivitas-' + user.id);
                if(statusTd) statusTd.innerHTML = templateOffline;
            });
    });
</script>
@endsection
