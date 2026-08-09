@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <!-- Panggil Modal Notifikasi Sukses -->
    <x-success-modal />

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Ketidakhadiran</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Ketidakhadiran</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>

        <!-- Filter & Actions Right -->
        <form action="{{ route('ketidakhadiran.index') }}" method="GET" class="flex items-center gap-3">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                @for ($y = now()->year + 1; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                @foreach ($bulanList as $b)
                    <option value="{{ $b }}" {{ $bulanNama === $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>

            <a href="{{ route('ketidakhadiran.index') }}" class="inline-flex">
                <x-button variant="outline" type="button" class="!rounded-xl shadow-sm !text-gray-600">Reset</x-button>
            </a>

            <x-button variant="primary" type="button" class="!rounded-xl shadow-sm !bg-pkt-jingga hover:!bg-orange-600 border-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh PDF
            </x-button>
        </form>
    </div>

    <!-- Banner: muncul kalau user sedang klik salah satu baris karyawan -->
    <div id="filterChartBanner" class="hidden mb-3 flex items-center justify-between bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5">
        <span class="text-sm text-blue-900">Menampilkan chart untuk: <strong id="filterChartNama"></strong></span>
        <button type="button" onclick="resetFilterKetidakhadiran()" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua Karyawan &times;</button>
    </div>

    <!-- Charts Section (Dengan h-96 agar ukurannya Fixed / Statis) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="h-96">
            <x-dynamic-chart id="chartPersentase" title="Persentase Ketidakhadiran Karyawan (Hari)" subtitle="Berdasarkan kategori izin dan cuti - {{ $bulanNama }} {{ $tahun }}" type="pie"></x-dynamic-chart>
        </div>
        <div class="h-96">
            <x-dynamic-chart id="chartJumlah" title="Jumlah Ketidakhadiran Karyawan (Hari)" subtitle="Akumulasi total hari per kategori - {{ $bulanNama }} {{ $tahun }}" type="bar"></x-dynamic-chart>
        </div>
    </div>

    @if ($totalHari === 0)
    <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-between">
        <span class="text-sm font-medium text-blue-900">Belum ada data ketidakhadiran untuk {{ $bulanNama }} {{ $tahun }}.</span>
    </div>
    @endif

    <!-- ================= DATA TABLE SECTION ================= -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100">
        <!-- Tombol Tambah Data Dipindah ke Header Tabel -->
        <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-end items-center">
            <x-button variant="primary" onclick="bukaModalTambah()" class="!py-1.5 !px-3 text-xs flex items-center">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Data Ketidakhadiran
            </x-button>
        </div>

        <x-table :headers="['Tahun', 'Bulan', 'Nama', 'NPK', 'Keterangan', 'Dinas', 'Cuti', 'Izin', 'Training', 'Dispensasi', 'Detasering', 'Aksi']">
            @forelse ($karyawan as $item)
                <tr class="row-ketidakhadiran hover:bg-gray-50 transition-colors cursor-pointer"
                    data-nama="{{ $item->nama }}"
                    data-dinas="{{ $item->dinas ?? 0 }}"
                    data-cuti="{{ $item->cuti ?? 0 }}"
                    data-izin="{{ $item->izin ?? 0 }}"
                    data-training="{{ $item->training ?? 0 }}"
                    data-dispensasi="{{ $item->dispensasi ?? 0 }}"
                    data-detasering="{{ $item->detasering ?? 0 }}">
                    <td class="px-6 py-4 text-gray-700 font-medium">{{ $tahun }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $bulanNama }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama }}</td>
                    <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $item->npk }}</td>
                    <td class="px-6 py-4 text-gray-500 truncate max-w-[160px]" title="{{ $item->keterangan }}">{{ $item->keterangan ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->dinas ? 'opacity-40' : '' }}">{{ $item->dinas ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->cuti ? 'opacity-40' : '' }}">{{ $item->cuti ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->izin ? 'opacity-40' : '' }}">{{ $item->izin ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->training ? 'opacity-40' : '' }}">{{ $item->training ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->dispensasi ? 'opacity-40' : '' }}">{{ $item->dispensasi ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700 {{ !$item->detasering ? 'opacity-40' : '' }}">{{ $item->detasering ?? 0 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            @if ($item->ketidakhadiran_id)
                                <!-- Tambahkan data-tahun dan data-bulan pada tombol Edit -->
                                <button type="button"
                                    class="btn-edit-bulanan p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200"
                                    title="Edit"
                                    data-karyawan-id="{{ $item->karyawan_id }}"
                                    data-npk="{{ $item->npk }}"
                                    data-tahun="{{ $tahun }}"
                                    data-bulan="{{ $bulanNama }}"
                                    data-keterangan="{{ $item->keterangan }}"
                                    data-dinas="{{ $item->dinas }}"
                                    data-cuti="{{ $item->cuti }}"
                                    data-izin="{{ $item->izin }}"
                                    data-training="{{ $item->training }}"
                                    data-dispensasi="{{ $item->dispensasi }}"
                                    data-detasering="{{ $item->detasering }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button"
                                    onclick="openDeleteModal('modalHapusBulanan', '{{ route('ketidakhadiran.destroyBulanan', $item->ketidakhadiran_id) }}')"
                                    class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                <a href="{{ route('ketidakhadiran.harian', ['karyawan_id' => $item->karyawan_id, 'tahun' => $tahun, 'bulan' => $bulanNama]) }}"
                                    class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-md border border-blue-200" title="Riwayat Harian">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </a>
                            @else
                                <span class="text-xs text-gray-300 italic px-1.5 py-1.5">Belum ada data</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="12" class="px-6 py-4 text-center text-gray-500">Belum ada data karyawan.</td></tr>
            @endforelse
        </x-table>

        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            <div>
                @if ($karyawan->total() > 0)
                    Menampilkan {{ $karyawan->firstItem() }}–{{ $karyawan->lastItem() }} dari {{ $karyawan->total() }} karyawan
                @else
                    Tidak ada karyawan ditemukan
                @endif
            </div>
            <div>{{ $karyawan->links() }}</div>
        </div>
    </x-card>

    <x-delete-modal id="modalHapusBulanan" title="Hapus Data Bulanan" message="Apakah Anda yakin ingin menghapus seluruh data ketidakhadiran karyawan ini untuk bulan yang dipilih?" />

    <!-- ================= MODAL TAMBAH / EDIT DATA ================= -->
    <x-modal id="modalTambahData" title="Tambah / Edit Data Ketidakhadiran" description="Pilih input per hari, atau langsung isi total 1 bulan sekaligus">

        <div class="flex gap-2 mb-5 border-b border-gray-100">
            <button type="button" id="tabHarianBtn" onclick="switchTabInput('harian')" class="px-4 py-2 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 transition-colors">
                Input Harian
            </button>
            <button type="button" id="tabBulananBtn" onclick="switchTabInput('bulanan')" class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-400 transition-colors">
                Input / Edit Bulanan
            </button>
        </div>

        <!-- FORM HARIAN dengan novalidate -->
        <form action="{{ route('ketidakhadiran.storeHarian') }}" method="POST" id="formHarian" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Karyawan <span class="text-red-500">*</span></label>
                <select name="karyawan_id" required onchange="autofillNpk(this, 'npkHarian')" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Pilih karyawan...</option>
                    @foreach ($daftarKaryawan as $k)
                        <option value="{{ $k->id }}" data-npk="{{ $k->npk }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NPK</label>
                <input type="text" id="npkHarian" readonly placeholder="Otomatis terisi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required value="{{ now()->toDateString() }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Ketidakhadiran <span class="text-red-500">*</span></label>
                <select name="jenis" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Pilih jenis...</option>
                    <option value="dinas">Dinas</option>
                    <option value="cuti">Cuti</option>
                    <option value="izin">Izin</option>
                    <option value="training">Training</option>
                    <option value="dispensasi">Dispensasi</option>
                    <option value="detasering">Detasering</option>
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                <p class="text-[10px] text-gray-400 mt-1.5">Maksimal 1 jenis per hari per karyawan. Kalau tanggal ini sudah pernah diisi, jenisnya akan diganti otomatis.</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan (opsional)</label>
                <textarea name="keterangan" rows="2" placeholder="cth. Dinas ke Jakarta" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
            </div>
        </form>

        <!-- FORM BULANAN dengan novalidate -->
        <form action="{{ route('ketidakhadiran.storeBulanan') }}" method="POST" id="formBulanan" class="hidden grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Karyawan <span class="text-red-500">*</span></label>
                <select name="karyawan_id" id="karyawanBulanan" required onchange="autofillNpk(this, 'npkBulanan')" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Pilih karyawan...</option>
                    @foreach ($daftarKaryawan as $k)
                        <option value="{{ $k->id }}" data-npk="{{ $k->npk }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NPK</label>
                <input type="text" id="npkBulanan" readonly placeholder="Otomatis terisi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                <input type="number" name="tahun" id="tahunBulanan" required value="{{ $tahun }}" min="2000" max="2100" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan <span class="text-red-500">*</span></label>
                <select name="bulan" id="bulanBulanan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    @foreach ($bulanList as $b)
                        <option value="{{ $b }}" {{ $bulanNama === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan (opsional)</label>
                <textarea name="keterangan" id="keteranganBulanan" rows="2" placeholder="cth. Cuti melahirkan" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dinas</label>
                <input type="number" name="dinas" min="0" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Cuti</label>
                <input type="number" name="cuti" min="0" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Izin</label>
                <input type="number" name="izin" min="0" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Training</label>
                <input type="number" name="training" min="0" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dispensasi</label>
                <input type="number" name="dispensasi" min="0" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Detasering</label>
                <input type="number" name="detasering" min="0" value="0" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <p class="md:col-span-2 text-xs text-gray-400">Menyimpan form ini akan MENIMPA angka yang sudah ada untuk karyawan & bulan yang sama.</p>
        </form>

        <x-slot name="footer">
            <x-button variant="outline" type="button" onclick="document.getElementById('modalTambahData').classList.add('hidden')" class="!px-6 !py-2.5 !rounded-lg">
                Batal
            </x-button>
            <x-button variant="secondary" type="submit" id="btnSubmitTambah" form="formHarian" class="!px-6 !py-2.5 !rounded-lg">
                Simpan Data
            </x-button>
        </x-slot>
    </x-modal>

</main>

<script>
    // Validasi Form Merah
    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500', 'bg-red-50'); 
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden'); 
                }
            });
            if (!isValid) e.preventDefault(); 
        });
        
        form.querySelectorAll('[required]').forEach(field => {
            field.addEventListener(field.tagName === 'SELECT' ? 'change' : 'input', function() {
                const errorSpan = this.nextElementSibling;
                if (this.value && this.value.trim() !== '') {
                    this.classList.remove('border-red-500', 'bg-red-50');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
        });
    });

    // Modal & Tabs Logic
    function bukaModalTambah() {
        document.getElementById('formBulanan').reset();
        document.getElementById('formHarian').reset();
        document.getElementById('npkHarian').value = '';
        document.getElementById('npkBulanan').value = '';
        
        // Buka Kunci ReadOnly
        const tInput = document.getElementById('tahunBulanan');
        tInput.removeAttribute('readonly');
        tInput.classList.remove('bg-gray-100', 'cursor-not-allowed');

        const bInput = document.getElementById('bulanBulanan');
        bInput.removeAttribute('style');
        bInput.classList.remove('bg-gray-100');

        // Hapus border merah jika ada
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        document.getElementById('modalTambahData').classList.remove('hidden');
        switchTabInput('harian');
    }

    function bukaEditBulanan(data) {
        document.getElementById('modalTambahData').classList.remove('hidden');
        switchTabInput('bulanan');

        const formBulanan = document.getElementById('formBulanan');
        
        // Hapus error border saat mode edit
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        formBulanan.querySelector('[name="karyawan_id"]').value = data.karyawanId;
        
        // Kunci Periode Bulan dan Tahun agar Read-Only (Mencegah Redundansi)
        const tInput = document.getElementById('tahunBulanan');
        tInput.value = data.tahun;
        tInput.setAttribute('readonly', true);
        tInput.classList.add('bg-gray-100', 'cursor-not-allowed');

        const bInput = document.getElementById('bulanBulanan');
        bInput.value = data.bulan;
        bInput.setAttribute('style', 'pointer-events: none;');
        bInput.classList.add('bg-gray-100');

        document.getElementById('npkBulanan').value = data.npk ?? '';
        document.getElementById('keteranganBulanan').value = data.keterangan ?? '';

        ['dinas', 'cuti', 'izin', 'training', 'dispensasi', 'detasering'].forEach(function (kategori) {
            formBulanan.querySelector('[name="' + kategori + '"]').value = data[kategori] ?? 0;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-edit-bulanan').forEach(function (btn) {
            btn.addEventListener('click', function () {
                bukaEditBulanan(this.dataset);
            });
        });

        document.querySelectorAll('.row-ketidakhadiran').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.closest('a, button')) return;
                pilihBarisKetidakhadiran(row);
            });
        });
    });

    function switchTabInput(tab) {
        const formHarian = document.getElementById('formHarian');
        const formBulanan = document.getElementById('formBulanan');
        const tabHarianBtn = document.getElementById('tabHarianBtn');
        const tabBulananBtn = document.getElementById('tabBulananBtn');
        const btnSubmit = document.getElementById('btnSubmitTambah');

        const aktif = ['border-blue-600', 'text-blue-600'];
        const nonAktif = ['border-transparent', 'text-gray-400'];

        if (tab === 'harian') {
            formHarian.classList.remove('hidden'); formHarian.classList.add('grid');
            formBulanan.classList.add('hidden'); formBulanan.classList.remove('grid');
            tabHarianBtn.classList.add(...aktif); tabHarianBtn.classList.remove(...nonAktif);
            tabBulananBtn.classList.add(...nonAktif); tabBulananBtn.classList.remove(...aktif);
            btnSubmit.setAttribute('form', 'formHarian');
        } else {
            formBulanan.classList.remove('hidden'); formBulanan.classList.add('grid');
            formHarian.classList.add('hidden'); formHarian.classList.remove('grid');
            tabBulananBtn.classList.add(...aktif); tabBulananBtn.classList.remove(...nonAktif);
            tabHarianBtn.classList.add(...nonAktif); tabHarianBtn.classList.remove(...aktif);
            btnSubmit.setAttribute('form', 'formBulanan');
        }
    }

    function autofillNpk(selectEl, targetInputId) {
        const opt = selectEl.options[selectEl.selectedIndex];
        document.getElementById(targetInputId).value = opt ? (opt.getAttribute('data-npk') || '') : '';
    }

    // Chart.js Setup
    Chart.register(ChartDataLabels);

    const totalHariPHP = {{ $totalHari }};
    const kategoriLabels = {!! json_encode(array_keys($totalPerKategori)) !!};
    const kategoriValues = {!! json_encode(array_values($totalPerKategori)) !!};
    const paletteKategori = ['#3B82F6', '#F97316', '#10B981', '#A855F7', '#EF4444', '#FACC15'];

    const dataPersentase = totalHariPHP > 0 ? {
        labels: kategoriLabels,
        datasets: [{ data: kategoriValues, backgroundColor: paletteKategori, borderWidth: 0 }]
    } : {
        labels: ['Belum ada data'],
        datasets: [{ data: [1], backgroundColor: ['#E5E7EB'], borderWidth: 0 }]
    };

    const dataJumlah = {
        labels: kategoriLabels,
        datasets: [{ data: kategoriValues, backgroundColor: paletteKategori, borderWidth: 0 }]
    };

    let currentDataPersentase = dataPersentase;
    let currentDataJumlah = dataJumlah;
    let currentTotalHari = totalHariPHP;
    let chartTypeState = { chartPersentase: 'pie', chartJumlah: 'bar' };

    // PERBAIKAN DI SINI: Menerima chartId untuk membedakan persentase dan jumlah mentah
    function buildPluginsConfig(type, chartId) {
        return {
            legend: {
                display: type !== 'bar',
                position: 'bottom',
                labels: { usePointStyle: true, padding: 15, font: { size: 12, weight: '500' } }
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                padding: 12, cornerRadius: 8,
                titleFont: { size: 14, weight: '700' }, bodyFont: { size: 12 },
                callbacks: {
                    label: function (context) {
                        if (currentTotalHari === 0) return 'Belum ada data ketidakhadiran';
                        const label = context.label || '';
                        const value = context.parsed.y !== undefined ? context.parsed.y : context.parsed;
                        
                        // Cek spesifik jika ini adalah chart Persentase
                        if (chartId === 'chartPersentase') {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${percentage}% (${value} hari)`;
                        }
                        
                        // Jika chart Jumlah
                        return `${label}: ${value} hari`;
                    }
                }
            },
            datalabels: {
                display: type === 'bar' ? false : function (context) {
                    if (currentTotalHari === 0) return false;
                    return context.dataset.data[context.dataIndex] > 0;
                },
                color: '#fff', font: { weight: 'bold', size: 16 },
                formatter: function (value, context) { 
                    // Tampilkan '%' di label dalam Pie khusus chart persentase
                    if (chartId === 'chartPersentase') {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        if (total === 0) return '';
                        const percentage = Math.round((value / total) * 100);
                        return percentage + '%';
                    }
                    // Tampilkan angka mentah khusus chart jumlah
                    return value; 
                },
                anchor: 'center', align: 'center'
            }
        };
    }

    function renderChartsKetidakhadiran() {
        createChart('chartPersentase', chartTypeState.chartPersentase, currentDataPersentase, chartTypeState.chartPersentase !== 'bar');
        createChart('chartJumlah', chartTypeState.chartJumlah, currentDataJumlah, chartTypeState.chartJumlah !== 'bar');
    }

    function tampilkanBannerFilter(nama) {
        const banner = document.getElementById('filterChartBanner');
        const namaEl = document.getElementById('filterChartNama');
        if (banner && namaEl) {
            namaEl.textContent = nama;
            banner.classList.remove('hidden');
        }
    }

    function sembunyikanBannerFilter() {
        const banner = document.getElementById('filterChartBanner');
        if (banner) banner.classList.add('hidden');
    }

    const kategoriKeyList = ['dinas', 'cuti', 'izin', 'training', 'dispensasi', 'detasering'];
    function pilihBarisKetidakhadiran(row) {
        document.querySelectorAll('.row-ketidakhadiran').forEach(function (r) {
            r.classList.remove('bg-blue-50', 'ring-1', 'ring-inset', 'ring-blue-300');
            r.classList.add('opacity-30');
        });
        row.classList.remove('opacity-30');
        row.classList.add('bg-blue-50', 'ring-1', 'ring-inset', 'ring-blue-300');

        const nilai = kategoriKeyList.map(function (key) { return parseInt(row.dataset[key] || '0', 10); });
        const totalBaris = nilai.reduce(function (a, b) { return a + b; }, 0);
        currentTotalHari = totalBaris;

        currentDataPersentase = totalBaris > 0 ? {
            labels: kategoriLabels,
            datasets: [{ data: nilai, backgroundColor: paletteKategori, borderWidth: 0 }]
        } : {
            labels: ['Belum ada data'],
            datasets: [{ data: [1], backgroundColor: ['#E5E7EB'], borderWidth: 0 }]
        };
        currentDataJumlah = {
            labels: kategoriLabels,
            datasets: [{ data: nilai, backgroundColor: paletteKategori, borderWidth: 0 }]
        };

        renderChartsKetidakhadiran();
        tampilkanBannerFilter(row.dataset.nama);
    }

    function resetFilterKetidakhadiran() {
        document.querySelectorAll('.row-ketidakhadiran').forEach(function (r) {
            r.classList.remove('opacity-30', 'bg-blue-50', 'ring-1', 'ring-inset', 'ring-blue-300');
        });
        currentDataPersentase = dataPersentase;
        currentDataJumlah = dataJumlah;
        currentTotalHari = totalHariPHP;
        renderChartsKetidakhadiran();
        sembunyikanBannerFilter();
    }

    window.charts = {};

    function createChart(chartId, type, data, maintainAspectRatio = true) {
        const ctx = document.getElementById('canvas_' + chartId).getContext('2d');
        if (window.charts[chartId]) window.charts[chartId].destroy();

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: maintainAspectRatio,
            plugins: buildPluginsConfig(type, chartId) // PERBAIKAN: Mengirimkan chartId ke plugin config
        };

        if (type === 'bar') {
            chartOptions.scales = {
                y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 10, weight: '500' } } }
            };
        }

        window.charts[chartId] = new Chart(ctx, { type: type, data: data, options: chartOptions });
    }

    function changeChartType(chartId, newType) {
        chartTypeState[chartId] = newType;
        renderChartsKetidakhadiran();
    }

    window.onload = function () {
        renderChartsKetidakhadiran();
    };
</script>
@endsection