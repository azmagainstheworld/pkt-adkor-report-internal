@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Pemeliharaan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pemeliharaan Peralatan & Furnitur</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('pelaporan.index') }}" method="GET" class="flex items-center gap-3">
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

    <!-- ================= CHART SECTION ================= -->
    <x-card class="!rounded-xl overflow-hidden shadow-sm border border-gray-100 mb-8 p-6 bg-white">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Statistik Volume Laporan {{ $filterTahun == 'semua' ? 'Tahunan' : 'Bulanan' }}</h3>
                <p class="text-xs text-gray-400">Distribusi jumlah pelaporan Eksternal vs Internal berdasarkan filter</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div class="flex items-center gap-1.5">
                    <span class="w-3.5 h-3.5 rounded-md bg-[#F7941E]"></span>
                    <span class="text-gray-700 font-medium">Laporan Eksternal</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3.5 h-3.5 rounded-md bg-[#0056A3]"></span>
                    <span class="text-gray-700 font-medium">Laporan Internal</span>
                </div>
            </div>
        </div>

        <div class="h-64 w-full relative">
            <canvas id="pelaporanChart"></canvas>
        </div>
    </x-card>

    <!-- ================= TABEL 1: RINGKASAN AKUMULASI (RATA TENGAH) ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Ringkasan Akumulasi Laporan</h3>
                <p class="text-xs text-gray-400">Total data laporan per periode</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['Tahun', 'Bulan', 'Tujuan Eksternal', 'Tujuan Internal', 'Total Laporan']">
                @forelse($dataRingkasan as $index => $row)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row['bulan'] }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $row['eksternal'] }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $row['internal'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-bold text-center">{{ $row['total_laporan'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">Tidak ada data ringkasan.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </x-card>

    <!-- ================= TABEL 2: RINCIAN PELAPORAN (RATA TENGAH & AKSI) ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Daftar Rincian Pelaporan</h3>
                <p class="text-xs text-gray-400">Detail spesifik masing-masing laporan</p>
            </div>
            
            <x-button variant="primary" onclick="openModalTambah()" class="!py-2 text-xs !bg-[#F7941E] hover:!bg-orange-600 border-none">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Laporan
            </x-button>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['Tujuan Laporan', 'Nomor Laporan', 'Laporan', 'Tanggal', 'Jenis', 'Aksi']">
                @forelse($dataRincian as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap {{ $index % 2 == 1 ? 'bg-gray-50/60' : '' }}">
                        <td class="px-4 py-3 text-center">
                            @if($item->tujuan == 'Eksternal')
                                <span class="bg-orange-100 text-orange-700 px-2.5 py-1 rounded-md font-medium text-[10px]">Eksternal</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-md font-medium text-[10px]">Internal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $item->nomor }}</td>
                        <td class="px-4 py-3 text-gray-600 truncate max-w-xs text-center" title="{{ $item->laporan }}">{{ $item->laporan }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $item->jenis }}</td>
                        
                        <!-- TOMBOL AKSI: EDIT DAN HAPUS -->
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editLaporan({{ $item }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" onclick="openDeleteModal('modalHapusLaporan', '{{ route('pelaporan.destroy', $item->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">Tidak ada rincian pelaporan.</td>
                    </tr>
                @endforelse
            </x-table>
        </div>
        
        <!-- Pagination Footer -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            <div>
                @if ($dataRincian->total() > 0)
                    Menampilkan {{ $dataRincian->firstItem() }}–{{ $dataRincian->lastItem() }} dari {{ $dataRincian->total() }} laporan
                @else
                    Tidak ada laporan ditemukan
                @endif
            </div>
            <div>{{ $dataRincian->links() }}</div>
        </div>
    </x-card>

    <x-delete-modal id="modalHapusLaporan" title="Hapus Data Laporan" message="Data laporan ini akan dihapus secara permanen dari sistem. Lanjutkan?" />

    <!-- ================= MODAL TAMBAH LAPORAN ================= -->
    <x-modal id="modalTambahLaporan" title="Formulir Tambah Laporan" description="Masukkan detail pelaporan yang baru.">
        <form action="{{ route('pelaporan.store') }}" method="POST" id="formTambahLaporan" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tujuan Laporan <span class="text-red-500">*</span></label>
                <select name="tujuan" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <option value="" disabled selected>Pilih Tujuan...</option>
                    <option value="Eksternal">Eksternal</option>
                    <option value="Internal">Internal</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Laporan <span class="text-red-500">*</span></label>
                <select name="jenis" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <option value="" disabled selected>Pilih Jenis...</option>
                    <option value="Bulan">Bulan</option>
                    <option value="Triwulan">Triwulan</option>
                    <option value="Semester">Semester</option>
                    <option value="Tahun">Tahun</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Laporan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Tanggal wajib diisi!</span>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor <span class="text-red-500">*</span></label>
                <input type="text" name="nomor" required placeholder="Cth: LAP/01/2026" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan / Laporan <span class="text-red-500">*</span></label>
                <textarea name="laporan" rows="2" required placeholder="Tuliskan isi laporan..." class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Laporan wajib diisi!</span>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahLaporan')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan Data</x-button>
            </div>
        </form>
    </x-modal>

    <!-- ================= MODAL EDIT LAPORAN ================= -->
    <x-modal id="modalEditLaporan" title="Edit Data Laporan" description="Perbarui rincian laporan yang sudah ada.">
        <form action="" method="POST" id="formEditLaporan" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tujuan Laporan <span class="text-red-500">*</span></label>
                <select name="tujuan" id="edit_tujuan" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <option value="" disabled>Pilih Tujuan...</option>
                    <option value="Eksternal">Eksternal</option>
                    <option value="Internal">Internal</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Laporan <span class="text-red-500">*</span></label>
                <select name="jenis" id="edit_jenis" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <option value="" disabled>Pilih Jenis...</option>
                    <option value="Bulan">Bulan</option>
                    <option value="Triwulan">Triwulan</option>
                    <option value="Semester">Semester</option>
                    <option value="Tahun">Tahun</option>
                </select>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib dipilih!</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Laporan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="edit_tanggal" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Tanggal wajib diisi!</span>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Laporan <span class="text-red-500">*</span></label>
                <input type="text" name="nomor" id="edit_nomor" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Wajib diisi!</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan / Laporan <span class="text-red-500">*</span></label>
                <textarea name="laporan" id="edit_laporan" rows="2" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                <span class="error-msg text-red-500 text-xs mt-1.5 font-medium hidden">Laporan wajib diisi!</span>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEditLaporan')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Data</x-button>
            </div>
        </form>
    </x-modal>
</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // ==========================================================
    // ACTION TRIGGERS (TAMBAH & EDIT)
    // ==========================================================
    function openModalTambah() {
        document.getElementById('formTambahLaporan').reset();
        openModal('modalTambahLaporan');
    }

    function editLaporan(data) {
        const form = document.getElementById('formEditLaporan');
        form.action = `/pelaporan/${data.id}`;
        
        document.getElementById('edit_tujuan').value = data.tujuan;
        document.getElementById('edit_jenis').value = data.jenis;
        document.getElementById('edit_tanggal').value = data.tanggal;
        document.getElementById('edit_nomor').value = data.nomor;
        document.getElementById('edit_laporan').value = data.laporan;

        openModal('modalEditLaporan');
    }

    // ==========================================================
    // CLIENT-SIDE VALIDATION LOGIC
    // ==========================================================
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

    // ==========================================================
    // CHART.JS LOGIC
    // ==========================================================
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('pelaporanChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const rawChartData = {!! json_encode($chartData) !!};
            
            const labels = rawChartData.map(d => d.label);
            const dataEksternal = rawChartData.map(d => d.eksternal);
            const dataInternal = rawChartData.map(d => d.internal);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Eksternal',
                            data: dataEksternal,
                            backgroundColor: '#F7941E',
                            borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8
                        },
                        {
                            label: 'Internal',
                            data: dataInternal,
                            backgroundColor: '#0056A3',
                            borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#F3F4F6', drawBorder: false } },
                        x: { grid: { display: false, drawBorder: false } }
                    }
                }
            });
        }
    });
</script>
@endsection