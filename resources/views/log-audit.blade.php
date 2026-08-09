@extends('layouts.app')

@section('content')
<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-500">Admin</span>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Log Audit</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Log Audit</h2>
            <p class="text-sm text-gray-500">Rekaman aktivitas dan riwayat perubahan data sistem (Read-only)</p>
        </div>

        <!-- Filter Kanan Atas (Rentang Tanggal, Pengguna, Modul) -->
        <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center gap-3">
            <!-- Dropdown Rentang Tanggal -->
            <select name="range" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl bg-white text-xs text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                <option value="hari-ini" {{ $selectedRange === 'hari-ini' ? 'selected' : '' }}>Hari Ini</option>
                <option value="7-hari" {{ $selectedRange === '7-hari' ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30-hari" {{ $selectedRange === '30-hari' ? 'selected' : '' }}>30 Hari Terakhir</option>
            </select>

            <!-- Dropdown Nama Pengguna -->
            <select name="user" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl bg-white text-xs text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                <option value="" {{ !$selectedUser ? 'selected' : '' }}>Semua Pengguna</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" {{ (string) $selectedUser === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>

            <!-- Dropdown Modul -->
            <select name="module" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl bg-white text-xs text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                <option value="" {{ !$selectedModule ? 'selected' : '' }}>Semua Modul</option>
                @foreach ($modules as $m)
                    <option value="{{ $m }}" {{ $selectedModule === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= DATA TABLE SECTION (READ-ONLY) ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white">

        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Riwayat Aktivitas Sistem</h3>
                <p class="text-xs text-gray-400">Data tercatat secara otomatis dan bersifat permanen</p>
            </div>

            <!-- Info Badge Read-only -->
            <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-semibold flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Read-Only Mode
            </span>
        </div>

        <!-- Memanggil Komponen Tabel -->
        <x-table :headers="['Waktu', 'Pengguna', 'Modul', 'Aksi']">
            @forelse ($logs as $log)
                <tr class="{{ $loop->even ? 'bg-gray-50/60' : '' }} hover:bg-gray-100 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-500 font-mono text-xs whitespace-nowrap">{{ $log->created_at->translatedFormat('d-M-Y H:i') }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $log->actorNameDisplay() }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $log->module_key ? ucfirst($log->module_key) : '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-md text-xs font-bold border {{ $log->actionBadgeClass() }}">{{ $log->actionLabel() }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada aktivitas tercatat untuk filter yang dipilih.</td>
                </tr>
            @endforelse
        </x-table>

        <!-- Pagination Footer -->
        @if ($logs->total() > 0)
            <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-500 bg-white">
                <div>Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} total log aktivitas</div>
                <div class="flex items-center gap-1">
                    <a href="{{ $logs->previousPageUrl() ?? '#' }}"
                       class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 {{ $logs->onFirstPage() ? 'text-gray-300 bg-gray-50 pointer-events-none' : 'text-gray-700 hover:bg-gray-50' }}">&lt;</a>

                    @php
                        $start = max(1, $logs->currentPage() - 2);
                        $end = min($logs->lastPage(), $logs->currentPage() + 2);
                    @endphp

                    @for ($page = $start; $page <= $end; $page++)
                        <a href="{{ $logs->url($page) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-xl font-medium {{ $page === $logs->currentPage() ? 'bg-pkt-biru text-white' : 'border border-gray-200 hover:bg-gray-50 text-gray-700' }}">{{ $page }}</a>
                    @endfor

                    <a href="{{ $logs->nextPageUrl() ?? '#' }}"
                       class="w-8 h-8 flex items-center justify-center rounded-xl border border-gray-200 {{ $logs->hasMorePages() ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-300 bg-gray-50 pointer-events-none' }}">&gt;</a>
                </div>
            </div>
        @endif

    </x-card>

</main>
@endsection