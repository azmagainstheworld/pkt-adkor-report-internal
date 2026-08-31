@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <x-success-modal />

    <!-- ================= MODAL ERROR KUSTOM ================= -->
    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Penambahan Gagal</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors">Mengerti</button>
        </div>
    </div>
    @endif

    <!-- Header Navigasi & Tombol Kembali -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="{{ route('karyawan.index') }}" class="hover:text-blue-600">Karyawan</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Detail Karyawan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900">Profil & Detail Karyawan</h2>
        </div>
        <a href="{{ route('karyawan.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- Grid Informasi Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <x-card class="p-6 !rounded-2xl text-center bg-yellow-50/40 border border-yellow-200">
            <div class="w-24 h-24 bg-yellow-500 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4 shadow-sm">
                {{ strtoupper(substr($karyawan->nama, 0, 1)) }}
            </div>
            <span class="inline-block px-2.5 py-0.5 mb-2 bg-yellow-200 text-yellow-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Karyawan Utama</span>
            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $karyawan->nama }}</h3>
            <p class="text-sm text-gray-500 font-mono mb-4">{{ $karyawan->npk }}</p>
            <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $karyawan->keterangan == 'Organik' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }}">
                {{ $karyawan->keterangan }}
            </div>
        </x-card>

        <x-card class="p-6 !rounded-2xl lg:col-span-2">
            <h4 class="font-bold text-gray-800 text-base mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Informasi Lengkap
                </span>
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                <div><span class="text-xs text-gray-400 block">Golongan / Grade</span><span class="font-bold text-gray-800 text-base">{{ $karyawan->gol_grade }}</span></div>
                <div><span class="text-xs text-gray-400 block">No. HP / Telepon</span><span class="font-medium text-gray-800">{{ $karyawan->no_hp ?? '-' }}</span></div>
                <div><span class="text-xs text-gray-400 block">Tempat, Tanggal Lahir</span><span class="font-medium text-gray-800">{{ ($karyawan->tempat_lahir && $karyawan->tempat_lahir !== '-' ? $karyawan->tempat_lahir . ', ' : '') }}{{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</span></div>
                <div><span class="text-xs text-gray-400 block">Tanggal MPP / PBP</span><span class="font-medium text-orange-500">{{ \Carbon\Carbon::parse($karyawan->mpp_pbp)->translatedFormat('d F Y') }}</span></div>
                <div><span class="text-xs text-gray-400 block">Ket. Pensiun</span><span class="font-bold text-red-600">{{ $karyawan->ket_pensiun ?? '-' }}</span></div>
                <div><span class="text-xs text-gray-400 block">Ukuran Kaos</span><span class="font-bold text-gray-800">{{ $karyawan->ukuran_kaos }}</span></div>                
                
                <!-- RENDER KOLOM DINAMIS PROFIL KHUSUS HALAMAN DETAIL -->
                @php $tambahanProfil = is_string($karyawan->data_tambahan) ? json_decode($karyawan->data_tambahan, true) : ($karyawan->data_tambahan ?? []); @endphp
                @if(isset($kolomDinamisProfil))
                    @foreach($kolomDinamisProfil as $kolom)
                        <div>
                            <span class="text-xs text-gray-400 block">{{ $kolom->nama_kolom }}</span>
                            <span class="font-medium text-gray-800">
                                @if($kolom->tipe_input === 'currency' && isset($tambahanProfil[$kolom->nama_kolom]))
                                    Rp {{ $tambahanProfil[$kolom->nama_kolom] }}
                                @else
                                    {{ $tambahanProfil[$kolom->nama_kolom] ?? '-' }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                @endif

                <div class="md:col-span-2"><span class="text-xs text-gray-400 block">Alamat Lengkap</span><span class="font-medium text-gray-800 leading-relaxed">{{ $karyawan->alamat }}</span></div>
            </div>
        </x-card>
    </div>

    <!-- ================= DATA KELUARGA ================= -->
    <x-card class="!rounded-2xl overflow-visible !p-0">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white flex-wrap gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Data Keluarga (Pasangan & Anak)</h3>
                <p class="text-xs text-gray-400">Daftar anggota keluarga yang terikat dengan karyawan ini</p>
            </div>
            <div class="flex gap-2">
                <x-button variant="outline" onclick="openModal('modalAturKolomKeluarga')" class="!py-2 text-xs border-gray-300 text-gray-700 hover:bg-gray-50 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    Atur Kolom Keluarga
                </x-button>
                <button type="button" onclick="document.getElementById('modalTambahKeluarga').classList.remove('hidden')" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-semibold transition-colors">
                    + Tambah Anggota Keluarga
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            @php
                $tableHeadersKeluarga = ['No', 'Nama Anggota Keluarga', 'Hubungan', 'Tempat, Tanggal Lahir'];
                if(isset($kolomDinamisKeluarga)) { foreach($kolomDinamisKeluarga as $k) { $tableHeadersKeluarga[] = $k->nama_kolom; } }
                $tableHeadersKeluarga[] = 'Aksi';
            @endphp

            <x-table :headers="$tableHeadersKeluarga">
                @forelse($karyawan->keluarga as $index => $keluarga)
                    @php
                        $isAnak = strtolower($keluarga->hubungan) == 'anak';
                        $tambahanKeluarga = is_string($keluarga->data_tambahan) ? json_decode($keluarga->data_tambahan, true) : ($keluarga->data_tambahan ?? []);
                    @endphp
                    <tr class="hover:bg-gray-50 text-sm">
                        <td class="px-6 py-4 text-gray-500 text-center">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $keluarga->nama }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold {{ $isAnak ? 'bg-orange-100 text-orange-800' : 'bg-blue-50 text-blue-700' }}">
                                {{ $keluarga->hubungan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ ($keluarga->tempat_lahir && $keluarga->tempat_lahir !== '-' ? $keluarga->tempat_lahir . ', ' : '') }}{{ $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                        </td>

                        <!-- RENDER KOLOM DINAMIS KELUARGA DI TABEL -->
                        @if(isset($kolomDinamisKeluarga))
                            @foreach($kolomDinamisKeluarga as $kolom)
                                <td class="px-6 py-4 text-center text-gray-600">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahanKeluarga[$kolom->nama_kolom]))
                                        Rp {{ $tambahanKeluarga[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahanKeluarga[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-6 py-4">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="openEditKeluargaModal({{ json_encode($keluarga) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusKeluarga', '{{ route('keluarga.destroy', $keluarga->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-gray-400 italic">
                            Belum ada data keluarga yang ditambahkan.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapusKeluarga" title="Hapus Anggota Keluarga" message="Apakah Anda yakin ingin menghapus data anggota keluarga ini?" />
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel keluarga. Lanjutkan?" />

    <!-- MODAL ATUR KOLOM KELUARGA -->
    <x-modal id="modalAturKolomKeluarga" title="Atur Kolom (Keluarga Karyawan)" description="Kelola kolom ekstra khusus untuk tabel keluarga.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
            @if(isset($kolomDinamisKeluarga) && $kolomDinamisKeluarga->count() > 0)
                @foreach($kolomDinamisKeluarga as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('modalAturKolomKeluarga', '{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="keluarga_karyawan">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputKeluarga" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig('tipeInputKeluarga', 'dropdownConfigAreaKeluarga')">
                        <option value="text">Teks Singkat</option>
                        <option value="number">Angka Kuantitas Biasa</option>
                        <option value="currency">Harga / Uang (Titik Otomatis)</option>
                        <option value="date">Tanggal</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigAreaKeluarga">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Laki-laki, Perempuan" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolomKeluarga')">Tutup</x-button><x-button variant="primary" type="submit">Simpan</x-button></div>
        </form>
    </x-modal>

    <!-- ================= MODAL TAMBAH KELUARGA ================= -->
    <x-modal id="modalTambahKeluarga" title="Tambah Anggota Keluarga" description="Masukkan data pasangan atau anak dari karyawan ini">
        <form action="{{ route('keluarga.store', $karyawan->id) }}" method="POST" id="formTambahKeluarga" class="space-y-4 novalidate-form" novalidate>
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required placeholder="cth. Hafizhah Azzahra" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan Keluarga <span class="text-red-500">*</span></label>
                <select name="hubungan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                    <option value="" disabled selected>Pilih hubungan...</option>
                    <option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option>
                </select>
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" placeholder="cth. Palembang" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
            </div>

            <!-- INJEKSI KOLOM DINAMIS KELUARGA (TAMBAH) -->
            @if(isset($kolomDinamisKeluarga))
                @foreach($kolomDinamisKeluarga as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-currency w-full pl-9 pr-4 py-2 border rounded-lg text-sm outline-none" placeholder="0"></div>
                        @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2 border rounded-lg text-sm outline-none">
                                <option value="">Pilih...</option>
                                @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                            </select>
                        @endif
                    </div>
                @endforeach
            @endif
        </form>

        <x-slot name="footer">
            <button type="button" onclick="document.getElementById('modalTambahKeluarga').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">Batal</button>
            <button type="submit" form="formTambahKeluarga" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan Keluarga</button>
        </x-slot>
    </x-modal>

    <!-- ================= MODAL EDIT KELUARGA ================= -->
    <x-modal id="modalEditKeluarga" title="Edit Anggota Keluarga" description="Perbarui data pasangan atau anak ini">
        <form id="formEditKeluarga" method="POST" class="space-y-4 novalidate-form" novalidate>
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="edit_nama" name="nama" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan Keluarga <span class="text-red-500">*</span></label>
                <select id="edit_hubungan" name="hubungan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                    <option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option>
                </select>
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" id="edit_tempat_lahir" name="tempat_lahir" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
            </div>

            <!-- INJEKSI KOLOM DINAMIS KELUARGA (EDIT) -->
            @if(isset($kolomDinamisKeluarga))
                @foreach($kolomDinamisKeluarga as $kolom)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $kolom->nama_kolom }}</label>
                        @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-keluarga w-full px-4 py-2 border rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-keluarga w-full px-4 py-2 border rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'currency')
                            <div class="relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</div><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-currency input-dinamis-keluarga w-full pl-9 pr-4 py-2 border rounded-lg text-sm outline-none"></div>
                        @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-keluarga w-full px-4 py-2 border rounded-lg text-sm outline-none">
                        @elseif($kolom->tipe_input === 'dropdown')
                            <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-key="{{ $kolom->nama_kolom }}" class="input-dinamis-keluarga w-full px-4 py-2 border rounded-lg text-sm outline-none">
                                <option value="">Pilih...</option>
                                @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                            </select>
                        @endif
                    </div>
                @endforeach
            @endif
        </form>

        <x-slot name="footer">
            <button type="button" onclick="document.getElementById('modalEditKeluarga').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">Batal</button>
            <button type="submit" form="formEditKeluarga" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700">Perbarui Keluarga</button>
        </x-slot>
    </x-modal>

    <!-- JavaScript Pendukung -->
    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        function triggerDeleteKolom(modalAsal, deleteUrl) {
            closeModal(modalAsal);
            setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
        }

        function toggleDropdownConfig(selectorId, configAreaId) {
            const selector = document.getElementById(selectorId);
            const configArea = document.getElementById(configAreaId);
            if (selector.value === 'dropdown') {
                configArea.classList.remove('hidden');
                configArea.querySelector('input').setAttribute('required', 'true');
            } else {
                configArea.classList.add('hidden');
                configArea.querySelector('input').removeAttribute('required');
                configArea.querySelector('input').value = '';
            }
        }

        function openEditKeluargaModal(keluarga) {
            const form = document.getElementById('formEditKeluarga');
            form.action = `/keluarga/${keluarga.id}`;
            
            document.getElementById('edit_nama').value = keluarga.nama || '';
            document.getElementById('edit_hubungan').value = keluarga.hubungan || 'Anak';
            document.getElementById('edit_tempat_lahir').value = keluarga.tempat_lahir || '';
            document.getElementById('edit_tanggal_lahir').value = keluarga.tanggal_lahir ? keluarga.tanggal_lahir.substring(0, 10) : '';
            
            // Auto-fill JSON keluarga saat edit
            const tambahan = typeof keluarga.data_tambahan === 'string' ? JSON.parse(keluarga.data_tambahan) : (keluarga.data_tambahan || {});
            document.querySelectorAll('.input-dinamis-keluarga').forEach(el => {
                const key = el.getAttribute('data-key');
                el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
            });

            form.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });
            form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));

            document.getElementById('modalEditKeluarga').classList.remove('hidden');
        }

        // AUTO-DOT FORMATTER UNTUK HARGA (MATA UANG)
        document.addEventListener('input', function(e) {
            if(e.target && e.target.classList.contains('input-currency')) {
                let value = e.target.value.replace(/[^,\d]/g, '');
                let split = value.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if(ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
                e.target.value = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            }
        });

        // PENCEGAH HURUF 'e' PADA INPUT NUMBER
        document.addEventListener('keydown', function(e) {
            if (e.target && e.target.type === 'number') {
                if (['e', 'E', '+', '-', '.'].includes(e.key)) { e.preventDefault(); }
            }
        });

        // VALIDASI CLIENT-SIDE CLEAN (Hanya border merah & teks error di bawah)
        document.querySelectorAll('.novalidate-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                form.querySelectorAll('[required]').forEach(field => {
                    const errorSpan = field.nextElementSibling;
                    if (!field.value || field.value.trim() === '') {
                        isValid = false;
                        field.classList.add('border-red-500');
                        field.classList.remove('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                    } else {
                        field.classList.remove('border-red-500');
                        field.classList.add('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                    }
                });
                if (!isValid) e.preventDefault();
            });

            form.querySelectorAll('[required]').forEach(field => {
                ['input', 'change'].forEach(evt => {
                    field.addEventListener(evt, function() {
                        const errorSpan = this.nextElementSibling;
                        if (this.value && this.value.trim() !== '') {
                            this.classList.remove('border-red-500');
                            this.classList.add('border-gray-300');
                            if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                        }
                    });
                });
            });
        });
    </script>

</main>
@endsection
