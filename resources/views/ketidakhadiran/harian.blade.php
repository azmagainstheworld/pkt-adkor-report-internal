@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <x-success-modal />

    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('ketidakhadiran.index') }}" class="hover:text-blue-600">Ketidakhadiran</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Riwayat Harian</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Riwayat Harian</h2>
            <p class="text-sm text-gray-500">
                Rincian catatan ketidakhadiran per tanggal.
                @if ($karyawanTerpilih)
                    Bisa dihapus di sini kalau ada yang salah input — angka rekap bulanan otomatis ikut terkoreksi.
                @endif
            </p>
        </div>
        <a href="{{ route('ketidakhadiran.index', ['tahun' => $tahun, 'bulan' => $bulanNama]) }}">
            <x-button variant="outline" type="button" class="!rounded-xl shadow-sm !text-gray-600">
                &larr; Kembali ke Rekap Bulanan
            </x-button>
        </a>
    </div>

    <x-card class="!rounded-xl p-5 mb-6 shadow-sm border border-gray-100">
        <form action="{{ route('ketidakhadiran.harian') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Karyawan</label>
                <select name="karyawan_id" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled {{ !$karyawanTerpilih ? 'selected' : '' }}>Pilih karyawan...</option>
                    @foreach ($daftarKaryawan as $k)
                        <option value="{{ $k->id }}" {{ $karyawanTerpilih && $karyawanTerpilih->id == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }} ({{ $k->npk }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun</label>
                <select name="tahun" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    @for ($y = now()->year + 1; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan</label>
                <select name="bulan" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    @foreach ($bulanList as $b)
                        <option value="{{ $b }}" {{ $bulanNama === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4">
                <x-button variant="primary" type="submit" class="!rounded-xl border-none">Tampilkan</x-button>
            </div>
        </form>
    </x-card>

    @if (!$karyawanTerpilih)
        <div class="p-6 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-900 text-center">
            Pilih karyawan, tahun, dan bulan di atas untuk melihat rincian catatan harian.
        </div>
    @else
        <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100">
            <div class="p-5 border-b border-gray-100 bg-white">
                <h3 class="font-bold text-gray-900 text-lg">{{ $karyawanTerpilih->nama }}</h3>
                <p class="text-xs text-gray-400">NPK {{ $karyawanTerpilih->npk }} &bull; {{ $bulanNama }} {{ $tahun }} &bull; {{ $riwayat->count() }} catatan harian</p>
            </div>

            <!-- HEADER TABEL DINAMIS -->
            @php
                $tableHeaders = ['Tanggal', 'Jenis', 'Keterangan'];
                if(isset($kolomDinamis)) {
                    foreach($kolomDinamis as $k) {
                        $tableHeaders[] = $k->nama_kolom;
                    }
                }
                $tableHeaders[] = 'Aksi';
            @endphp

            <x-table :headers="$tableHeaders">
                @forelse ($riwayat as $catatan)
                    @php
                        // Terjemahkan string JSON kembali ke Array
                        $tambahan = is_string($catatan->data_tambahan) ? json_decode($catatan->data_tambahan, true) : ($catatan->data_tambahan ?? []);
                    @endphp

                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $catatan->tanggal->translatedFormat('l, d F Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-600">{{ ucfirst($catatan->jenis) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $catatan->keterangan ?? '-' }}</td>
                        
                        <!-- ISI KOLOM DINAMIS -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-6 py-4 text-gray-600">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                        Rp {{ $tambahan[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahan[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-6 py-4">
                            <button type="button"
                                onclick="openDeleteModal('modalHapusHarian', '{{ route('ketidakhadiran.destroyHarian', $catatan->id) }}')"
                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($tableHeaders) }}" class="px-6 py-4 text-center text-gray-500">Belum ada catatan harian untuk karyawan & bulan ini.</td></tr>
                @endforelse
            </x-table>
        </x-card>
    @endif

    <x-delete-modal id="modalHapusHarian" title="Hapus Catatan Harian" message="Apakah Anda yakin ingin menghapus catatan ini? Angka rekap bulanan terkait akan otomatis dikurangi 1." />

</main>
@endsection
