@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <x-success-modal />

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
            <h4 class="font-bold text-gray-800 text-base mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Lengkap
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                <div><span class="text-xs text-gray-400 block">Golongan / Grade</span><span class="font-bold text-gray-800 text-base">{{ $karyawan->gol_grade }}</span></div>
                <div><span class="text-xs text-gray-400 block">No. HP / Telepon</span><span class="font-medium text-gray-800">{{ $karyawan->no_hp ?? '-' }}</span></div>
                <div><span class="text-xs text-gray-400 block">Tempat, Tanggal Lahir</span><span class="font-medium text-gray-800">{{ $karyawan->tempat_lahir ?? '-' }}, {{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</span></div>
                <div><span class="text-xs text-gray-400 block">Tanggal MPP / PBP</span><span class="font-medium text-orange-500">{{ \Carbon\Carbon::parse($karyawan->mpp_pbp)->translatedFormat('d F Y') }}</span></div>
                <div><span class="text-xs text-gray-400 block">Ket. Pensiun</span><span class="font-bold text-red-600">{{ $karyawan->ket_pensiun ?? '-'     }}</span></div>
                <div><span class="text-xs text-gray-400 block">Ukuran Kaos</span><span class="font-bold text-gray-800">{{ $karyawan->ukuran_kaos }}</span></div>                
                <div class="md:col-span-2"><span class="text-xs text-gray-400 block">Alamat Lengkap</span><span class="font-medium text-gray-800 leading-relaxed">{{ $karyawan->alamat }}</span></div>
            </div>
        </x-card>
    </div>

    <!-- ================= DATA KELUARGA ================= -->
    <x-card class="!rounded-2xl overflow-hidden !p-0">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Data Keluarga (Pasangan & Anak)</h3>
                <p class="text-xs text-gray-400">Daftar anggota keluarga yang terikat dengan karyawan ini</p>
            </div>
            <button type="button" onclick="document.getElementById('modalTambahKeluarga').classList.remove('hidden')" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-semibold transition-colors">
                + Tambah Anggota Keluarga
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3 font-semibold">No</th>
                        <th class="px-6 py-3 font-semibold">Nama Anggota Keluarga</th>
                        <th class="px-6 py-3 font-semibold">Hubungan</th>
                        <th class="px-6 py-3 font-semibold">Tempat, Tanggal Lahir</th>
                        <th class="px-6 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($karyawan->keluarga as $index => $keluarga)
                        @php
                            $isAnak = strtolower($keluarga->hubungan) == 'anak';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $keluarga->nama }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold {{ $isAnak ? 'bg-orange-100 text-orange-800 border border-orange-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $keluarga->hubungan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $keluarga->tempat_lahir ?? '-' }}, {{ $keluarga->tanggal_lahir ? \Carbon\Carbon::parse($keluarga->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button type="button" onclick="openEditKeluargaModal({{ json_encode($keluarga) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Anggota Keluarga">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="openDeleteModal('modalHapusKeluarga', '{{ route('keluarga.destroy', $keluarga->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Anggota Keluarga">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">
                                Belum ada data pasangan atau anak yang ditambahkan untuk karyawan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapusKeluarga" title="Hapus Anggota Keluarga" message="Apakah Anda yakin ingin menghapus data anggota keluarga ini?" />

    <!-- ================= MODAL TAMBAH KELUARGA ================= -->
    <x-modal id="modalTambahKeluarga" title="Tambah Anggota Keluarga" description="Masukkan data pasangan atau anak dari karyawan ini">
        <!-- Tambahkan class novalidate-form -->
        <form action="{{ route('keluarga.store', $karyawan->id) }}" method="POST" id="formTambahKeluarga" class="space-y-4 novalidate-form" novalidate>
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required placeholder="cth. Hafizhah Azzahra" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan Keluarga <span class="text-red-500">*</span></label>
                <select name="hubungan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Pilih hubungan...</option>
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" placeholder="cth. Palembang" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
        </form>

        <x-slot name="footer">
            <button type="button" onclick="document.getElementById('modalTambahKeluarga').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">
                Batal
            </button>
            <button type="submit" form="formTambahKeluarga" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Simpan Keluarga
            </button>
        </x-slot>
    </x-modal>

    <!-- ================= MODAL EDIT KELUARGA ================= -->
    <x-modal id="modalEditKeluarga" title="Edit Anggota Keluarga" description="Perbarui data pasangan atau anak ini">
        <!-- Tambahkan class novalidate-form -->
        <form id="formEditKeluarga" method="POST" class="space-y-4 novalidate-form" novalidate>
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="edit_nama" name="nama" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan Keluarga <span class="text-red-500">*</span></label>
                <select id="edit_hubungan" name="hubungan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" id="edit_tempat_lahir" name="tempat_lahir" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
        </form>

        <x-slot name="footer">
            <button type="button" onclick="document.getElementById('modalEditKeluarga').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">
                Batal
            </button>
            <button type="submit" form="formEditKeluarga" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700">
                Perbarui Keluarga
            </button>
        </x-slot>
    </x-modal>

    <!-- JavaScript Pendukung -->
    <script>
        function openEditKeluargaModal(keluarga) {
            const form = document.getElementById('formEditKeluarga');
            form.action = `/keluarga/${keluarga.id}`;
            
            document.getElementById('edit_nama').value = keluarga.nama || '';
            document.getElementById('edit_hubungan').value = keluarga.hubungan || 'Anak';
            document.getElementById('edit_tempat_lahir').value = keluarga.tempat_lahir || '';
            document.getElementById('edit_tanggal_lahir').value = keluarga.tanggal_lahir || '';
            
            // Hapus styling error jika ada sisa
            form.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500', 'bg-red-50');
                el.classList.add('border-gray-300');
            });
            form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));

            document.getElementById('modalEditKeluarga').classList.remove('hidden');
        }

        // VALIDASI CLIENT-SIDE (Cegah submit kosong & munculkan teks merah)
        document.querySelectorAll('.novalidate-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                form.querySelectorAll('[required]').forEach(field => {
                    const errorSpan = field.nextElementSibling;
                    if (!field.value || field.value.trim() === '') {
                        isValid = false;
                        field.classList.add('border-red-500', 'bg-red-50');
                        field.classList.remove('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                    } else {
                        field.classList.remove('border-red-500', 'bg-red-50');
                        if (!field.classList.contains('border-gray-300')) field.classList.add('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                    }
                });
                if (!isValid) e.preventDefault();
            });

            form.querySelectorAll('[required]').forEach(field => {
                field.addEventListener(field.tagName === 'SELECT' ? 'change' : 'input', function() {
                    const errorSpan = this.nextElementSibling;
                    if (this.value && this.value.trim() !== '') {
                        this.classList.remove('border-red-500', 'bg-red-50');
                        this.classList.add('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                    }
                });
            });
        });
    </script>

</main>
@endsection