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
                <span class="text-gray-400">/</span><span class="text-gray-500">Kearsipan</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">PA Non Teknik</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pusat Arsip Non Teknik Non Tekstual</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('non-teknik-non-tekstual.index') }}" method="GET" class="flex items-center gap-3">
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
        <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-end items-center">
            <x-button variant="primary" onclick="openModalTambah()" class="!py-1.5 !px-3 text-xs">Tambah Data</x-button>
        </div>
        <div class="overflow-x-auto">
            @php
                $headers = ['Tahun', 'Bulan'];
                foreach($availableTypes as $type) { $headers[] = $type->name; }
                $headers[] = 'Aksi';
            @endphp
            <x-table :headers="$headers">
                @forelse($dataPa as $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        @foreach($availableTypes as $type)
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $row['items'][$type->id] ?? 0 }}</td>
                        @endforeach
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                @php $itemsJson = json_encode($row['items']); @endphp
                                <button type="button" onclick="editBulan('{{ $row['tahun'] }}', '{{ $row['bulan'] }}', '{{ $itemsJson }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('non-teknik-non-tekstual.destroyBulan', ['tahun' => $row['tahun'], 'bulan' => $row['bulan']]) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
                
                {{-- GRAND TOTAL ROW --}}
                @if(count($dataPa) > 0)
                <tr class="bg-gray-100 font-bold text-xs whitespace-nowrap border-t-2 border-gray-300">
                    <td colspan="2" class="px-4 py-4 text-gray-900 text-center uppercase tracking-wide">Total Keseluruhan</td>
                    @foreach($availableTypes as $type)
                        <td class="px-4 py-4 text-orange-700 text-center">{{ number_format($grandTotals[$type->id] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    <td class="px-4 py-4"></td>
                </tr>
                @endif
            </x-table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data" message="Data di bulan ini akan dihapus secara permanen. Lanjutkan?" />

    <!-- MODAL TAMBAH DINAMIS -->
    <x-modal id="modalTambah" title="Tambah Data Dokumen" description="Pilih jenis dokumen dan masukkan jumlahnya.">
        <form action="{{ route('non-teknik-non-tekstual.storeDokumen') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode (Bulan & Tahun) <span class="text-red-500">*</span></label>
                <input type="month" id="picker_tambah" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="syncPeriode(this.value, 'add_tahun', 'add_bulan')">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Periode wajib dipilih!</span>
                <input type="hidden" name="tahun" id="add_tahun">
                <input type="hidden" name="bulan" id="add_bulan">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kegiatan/Dokumen <span class="text-red-500">*</span></label>
                <select name="type_id" id="add_type_id" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none" onchange="toggleVarBaru(this.value)">
                    <option value="" disabled selected>Pilih Jenis...</option>
                    @foreach($availableTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                    <option value="tambah_baru" class="font-bold text-orange-600">++ Tambah Variabel Baru ++</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Bidang ini wajib dipilih!</span>
            </div>
            <div class="md:col-span-2 hidden" id="wrap_var_baru">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Variabel Baru <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kegiatan_baru" id="input_var_baru" placeholder="Ketik nama kolom/kegiatan..." class="w-full px-4 py-2 bg-orange-50 border border-orange-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" required placeholder="0" onfocus="if(this.value === '0') this.value = '';" oninput="this.value = this.value.replace(/^0+(?=\d)/, '');" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Jumlah wajib diisi!</span>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambah')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL EDIT BULAN -->
    <x-modal id="modalEditBulan" title="Edit Data Bulan" description="Perbarui seluruh data jumlah pada bulan terkait.">
        <form action="{{ route('non-teknik-non-tekstual.updateBulan') }}" method="POST" class="grid grid-cols-2 gap-x-4 gap-y-4 novalidate-form" novalidate>
            @csrf
            
            <!-- Input Periode dibuat Readonly dan visualnya diabu-abukan -->
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Periode Laporan</label>
                <input type="month" id="picker_edit" readonly class="w-full px-3 py-2 border border-gray-200 bg-gray-100 text-gray-500 rounded-md text-sm outline-none cursor-not-allowed font-medium">
                <p class="text-[10px] text-gray-400 mt-1">Periode tidak dapat diubah. Untuk bulan lain, silakan klik edit pada baris yang sesuai.</p>
                
                <!-- Hidden input untuk dikirim ke Controller -->
                <input type="hidden" name="tahun" id="edit_tahun">
                <input type="hidden" name="bulan" id="edit_bulan">
            </div>
            
            <div class="col-span-2 border-b border-gray-100 my-1"></div>
            
            <!-- Tempat Input Dinamis Dirender -->
            <div id="edit_dynamic_inputs" class="col-span-2 grid grid-cols-2 gap-4"></div>
            
            <div class="col-span-2 flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEditBulan')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>
    
</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
            if(event && event.target) {
                const picker = event.target;
                picker.classList.remove('border-red-500', 'bg-red-50');
                const err = picker.nextElementSibling;
                if(err && err.classList.contains('error-msg')) err.classList.add('hidden');
            }
        }
    }
    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) {
            return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`;
        }
        return '';
    }

    const availableTypes = {!! json_encode($availableTypes) !!};

    function toggleVarBaru(value) {
        const wrap = document.getElementById('wrap_var_baru');
        const input = document.getElementById('input_var_baru');
        if (value === 'tambah_baru') { 
            wrap.classList.remove('hidden'); input.setAttribute('required', 'required'); 
        } else { 
            wrap.classList.add('hidden'); input.removeAttribute('required'); 
            input.classList.remove('border-red-500', 'bg-red-50');
            if (input.nextElementSibling) input.nextElementSibling.classList.add('hidden');
        }
    }

    function openModalTambah() {
        const select = document.getElementById('add_type_id');
        select.value = ""; 
        toggleVarBaru(""); 
        
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah').value = currentMonth;
        syncPeriode(currentMonth, 'add_tahun', 'add_bulan');
        
        openModal('modalTambah');
    }

    function editBulan(tahun, bulan, itemsJson) {
        // document.getElementById('old_tahun').value = tahun;
        // document.getElementById('old_bulan').value = bulan;
        
        const pickerVal = getMonthPickerValue(tahun, bulan);
        document.getElementById('picker_edit').value = pickerVal;
        syncPeriode(pickerVal, 'edit_tahun', 'edit_bulan');
        
        const items = JSON.parse(itemsJson);
        const container = document.getElementById('edit_dynamic_inputs');
        container.innerHTML = '';
        
        // Render input dinamis dengan atribut required dan error label
        availableTypes.forEach(t => {
            const val = items[t.id] !== undefined ? items[t.id] : '';
            container.innerHTML += `
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1 truncate" title="${t.name}">${t.name} <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${t.id}]" value="${val}" required onfocus="if(this.value === '0') this.value = '';" oninput="this.value = this.value.replace(/^0+(?=\d)/, '');" class="dynamic-req w-full px-3 py-1.5 border border-gray-300 rounded-md text-xs outline-none focus:border-orange-500">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                </div>
            `;
        });

        // Binding event listener agar garis merah hilang saat user mengetik di input dinamis
        document.querySelectorAll('.dynamic-req').forEach(field => {
            field.addEventListener('input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
        
        openModal('modalEditBulan');
    }

    // ================= Validasi Teks Merah Global =================
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
        
        // Berlaku untuk field statis (yang tidak di-generate oleh JS)
        const requiredFields = form.querySelectorAll('[required]:not(.dynamic-req)');
        requiredFields.forEach(field => {
            const eventType = field.tagName === 'SELECT' ? 'change' : 'input';
            field.addEventListener(eventType, function() {
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