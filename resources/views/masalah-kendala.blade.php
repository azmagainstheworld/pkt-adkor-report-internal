@extends('layouts.app')

@section('content')
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
                <span class="text-blue-600 font-medium">Masalah & Kendala</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Masalah / Kendala Operasional</h2>
            <!-- Tanggal Inggris -->
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('masalah-kendala.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-orange-500">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-orange-500">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= TABEL DATA ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Masalah dan Kendala</h3>
                <p class="text-xs text-gray-400">Pencatatan kendala operasional beserta solusi yang telah dilakukan</p>
            </div>
            <!-- Tombol Tambah di pojok kanan atas -->
            <x-button variant="primary" onclick="openModalTambah()" class="!py-2 text-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Data
            </x-button>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['No', 'Tahun', 'Bulan', 'Masalah/Kendala', 'Solusi', 'Aksi']">
                @forelse($dataMasalah as $index => $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center align-top">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center align-top">{{ $row->tahun }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center align-top">{{ $row->bulan }}</td>
                        <!-- Sesuai request, teks ditaruh di center -->
                        <td class="px-4 py-3 text-gray-600 max-w-sm text-center align-top whitespace-pre-line">{{ $row->masalah }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-md text-center align-top whitespace-pre-line">{{ $row->solusi }}</td>
                        <td class="px-4 py-3 text-center align-top">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editData({{ $row }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('masalah-kendala.destroy', $row->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data masalah / kendala.</td></tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data" message="Data yang dihapus tidak dapat dikembalikan. Lanjutkan?" />

    <!-- ================= MODAL TAMBAH ================= -->
    <x-modal id="modalTambah" title="Tambah Catatan Kendala" description="Masukkan masalah yang dihadapi beserta solusi yang telah dilakukan.">
        <form action="{{ route('masalah-kendala.store') }}" method="POST" id="formTambah" class="grid grid-cols-1 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <!-- Kalendar Khusus Bulan/Tahun -->
                <input type="month" id="picker_tambah" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'add_tahun', 'add_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <!-- Hidden Input untuk dikirim ke Backend -->
                <input type="hidden" name="tahun" id="add_tahun">
                <input type="hidden" name="bulan" id="add_bulan">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Masalah / Kendala <span class="text-red-500">*</span></label>
                <textarea name="masalah" id="add_masalah" rows="3" required placeholder="Jelaskan masalah yang terjadi..." class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Masalah wajib diisi!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Solusi / Tindak Lanjut <span class="text-red-500">*</span></label>
                <textarea name="solusi" id="add_solusi" rows="3" required placeholder="Jelaskan solusi atau tindak lanjutnya..." class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Solusi wajib diisi!</span>
            </div>

            <div class="flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambah')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL EDIT ================= -->
    <x-modal id="modalEdit" title="Edit Catatan Kendala" description="Perbarui rincian masalah maupun solusinya.">
        <form action="" method="POST" id="formEdit" class="grid grid-cols-1 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_edit" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'edit_tahun', 'edit_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="edit_tahun">
                <input type="hidden" name="bulan" id="edit_bulan">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Masalah / Kendala <span class="text-red-500">*</span></label>
                <textarea name="masalah" id="edit_masalah" rows="3" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Masalah wajib diisi!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Solusi / Tindak Lanjut <span class="text-red-500">*</span></label>
                <textarea name="solusi" id="edit_solusi" rows="3" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Solusi wajib diisi!</span>
            </div>

            <div class="flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEdit')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // ================= Parsing Kalendar Bulan =================
    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    
    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
            
            // Hapus warning merah kalau sudah diisi
            if(event && event.target) {
                const picker = event.target;
                picker.classList.remove('border-red-500', 'bg-red-50');
                const err = picker.nextElementSibling;
                if(err && err.classList.contains('error-msg')) err.classList.add('hidden');
            }
        } else {
            document.getElementById(yearId).value = '';
            document.getElementById(monthId).value = '';
        }
    }

    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) {
            return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`;
        }
        return '';
    }

    // ================= Aksi Buka Modal =================
    function openModalTambah() {
        const form = document.getElementById('formTambah');
        form.reset();
        
        // Set Default ke Bulan Sekarang
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah').value = currentMonth;
        syncPeriode(currentMonth, 'add_tahun', 'add_bulan');
        
        // Reset Error Text
        form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        openModal('modalTambah');
    }

    function editData(data) {
        const form = document.getElementById('formEdit');
        form.action = `/masalah-kendala/${data.id}`;
        
        // Set Picker ke Bulan & Tahun data tersebut
        const pickerVal = getMonthPickerValue(data.tahun, data.bulan);
        document.getElementById('picker_edit').value = pickerVal;
        syncPeriode(pickerVal, 'edit_tahun', 'edit_bulan');

        document.getElementById('edit_masalah').value = data.masalah;
        document.getElementById('edit_solusi').value = data.solusi;
        
        // Reset Error Text
        form.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        openModal('modalEdit');
    }

    // ================= Validasi Modal (Wajib Diisi Teks Merah) =================
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