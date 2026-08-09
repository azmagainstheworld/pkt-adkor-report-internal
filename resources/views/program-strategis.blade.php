@extends('layouts.app')

@section('content')
<!-- Tambahkan style untuk memodifikasi komponen tabel global khusus di halaman ini -->
<style>
    /* Paksa tabel agar mengikuti lebar layar dan membungkus teks panjang */
    table { table-layout: auto !important; width: 100% !important; }
    /* Jangan nowrap untuk sel isi agar teks turun ke bawah jika kepanjangan */
    td { white-space: normal !important; vertical-align: top; }
    th { white-space: nowrap !important; }
</style>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm flex flex-col shadow-sm">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-bold">Gagal menyimpan data:</span>
            </div>
            <ul class="list-disc list-inside pl-8 text-xs">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Program Strategis</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Program Strategis</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <!-- Filter Kanan Atas -->
        <form action="{{ route('program-strategis.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-orange-500 shadow-sm cursor-pointer">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= TABEL PROGRAM STRATEGIS ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        
        <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm ml-2">Daftar Laporan Progress</h3>
            <x-button variant="primary" onclick="openModalTambah()" class="!py-1.5 !px-3 text-xs">Tambah Program Baru</x-button>
        </div>

        <div class="overflow-x-auto w-full">
            <x-table :headers="['Tahun', 'Program Strategis', 'Deskripsi Kegiatan', 'Target Waktu', 'Realisasi', 'Progress Saat Ini', 'Keterangan', 'Aksi']">
                @forelse($dataProgram as $index => $row)
                    <tr class="hover:bg-gray-50 transition-colors text-[13px] border-b border-gray-100">
                        <td class="p-4 text-gray-700 font-bold text-center border-r border-gray-100">{{ $row->tahun }}</td>
                        <td class="p-4 text-gray-900 font-medium border-r border-gray-100 w-48">{{ $row->program_strategis }}</td>
                        <td class="p-4 text-gray-700 border-r border-gray-100 w-56 whitespace-pre-line">{{ $row->deskripsi_kegiatan }}</td>
                        <td class="p-4 text-gray-700 text-center border-r border-gray-100">{{ $row->target_waktu }}</td>
                        <td class="p-4 text-gray-700 text-center font-medium border-r border-gray-100">{{ $row->realisasi }}</td>
                        
                        <!-- whitespace-pre-line sangat penting agar <enter> yang diketik di textarea bisa muncul ke bawah -->
                        <td class="p-4 text-gray-700 border-r border-gray-100 min-w-[300px] whitespace-pre-line">{!! e($row->progress_saat_ini) !!}</td>
                        
                        <td class="p-4 text-gray-500 italic border-r border-gray-100">{{ $row->keterangan_tambahan ?? '-' }}</td>
                        <td class="p-4 text-center">
                            <div class="flex flex-col gap-2 justify-start items-center">
                                @php $rowDataJson = json_encode($row); @endphp
                                <button type="button" onclick="editData({{ $rowDataJson }})" class="w-full p-1.5 flex justify-center text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('program-strategis.destroy', $row->id) }}')" class="w-full p-1.5 flex justify-center text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data program strategis.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data" message="Data program strategis ini akan dihapus secara permanen. Lanjutkan?" />

    <!-- MODAL TAMBAH DATA -->
    <x-modal id="modalTambah" title="Tambah Program Strategis" description="Masukkan detail kegiatan dan progress terkini.">
        <form action="{{ route('program-strategis.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 novalidate-form" novalidate>
            @csrf
            
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                <input type="number" name="tahun" id="add_tahun" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1 font-medium hidden">Wajib diisi!</span>
            </div>
            
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Waktu Pelaksanaan</label>
                <input type="text" name="target_waktu" placeholder="Misal: Jan - Apr 2026" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Program Strategis <span class="text-red-500">*</span></label>
                <input type="text" name="program_strategis" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1 font-medium hidden">Wajib diisi!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Kegiatan</label>
                <!-- Gunakan Textarea agar bisa multiline -->
                <textarea name="deskripsi_kegiatan" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
            </div>

            <div class="md:col-span-2 grid grid-cols-2 gap-x-6 gap-y-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Realisasi (%)</label>
                    <input type="text" name="realisasi" placeholder="Misal: 93%" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan / Kolom 1</label>
                    <input type="text" name="keterangan_tambahan" placeholder="Opsional..." class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Progress Saat Ini</label>
                    <textarea name="progress_saat_ini" rows="4" placeholder="Ketik rincian progress (bisa dienter ke bawah)..." class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">Gunakan tombol 'Enter' pada keyboard Anda untuk membuat poin atau baris baru.</p>
                </div>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambah')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL EDIT DATA -->
    <x-modal id="modalEdit" title="Edit Program Strategis" description="Perbarui detail kegiatan dan progress terkini.">
        <form id="formEdit" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 novalidate-form" novalidate>
            @csrf
            @method('PUT') <!-- Wajib untuk Update di Laravel -->
            
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                <input type="number" name="tahun" id="edit_tahun" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1 font-medium hidden">Wajib diisi!</span>
            </div>
            
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Waktu Pelaksanaan</label>
                <input type="text" name="target_waktu" id="edit_target" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Program Strategis <span class="text-red-500">*</span></label>
                <input type="text" name="program_strategis" id="edit_program" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1 font-medium hidden">Wajib diisi!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Kegiatan</label>
                <textarea name="deskripsi_kegiatan" id="edit_deskripsi" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
            </div>

            <div class="md:col-span-2 grid grid-cols-2 gap-x-6 gap-y-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Realisasi (%)</label>
                    <input type="text" name="realisasi" id="edit_realisasi" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan / Kolom 1</label>
                    <input type="text" name="keterangan_tambahan" id="edit_keterangan" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Progress Saat Ini</label>
                    <textarea name="progress_saat_ini" id="edit_progress" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                </div>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEdit')">Batal</x-button>
                <x-button variant="primary" type="submit">Update</x-button>
            </div>
        </form>
    </x-modal>
</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function openModalTambah() {
        const now = new Date();
        document.getElementById('add_tahun').value = now.getFullYear();
        openModal('modalTambah');
    }

    function editData(data) {
        // Arahkan action form ke rute update spesifik ID
        const form = document.getElementById('formEdit');
        form.action = `/program-strategis/${data.id}`;

        // Isi form dengan data lama
        document.getElementById('edit_tahun').value = data.tahun;
        document.getElementById('edit_program').value = data.program_strategis;
        document.getElementById('edit_deskripsi').value = data.deskripsi_kegiatan || '';
        document.getElementById('edit_target').value = data.target_waktu || '';
        document.getElementById('edit_realisasi').value = data.realisasi || '';
        document.getElementById('edit_progress').value = data.progress_saat_ini || '';
        document.getElementById('edit_keterangan').value = data.keterangan_tambahan || '';
        
        openModal('modalEdit');
    }

    // Script Validasi Merah
    const forms = document.querySelectorAll('.novalidate-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500', 'bg-red-50'); 
                    field.classList.remove('border-gray-300', 'border-orange-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden'); 
                } else {
                    field.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden'); 
                }
            });
            if (!isValid) e.preventDefault(); 
        });
        
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
    });
</script>
@endsection