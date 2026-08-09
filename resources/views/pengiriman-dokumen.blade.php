@extends('layouts.app')

@section('content')
{{-- Load library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <!-- Panggil Modal Notifikasi Sukses -->
    <x-success-modal />

    {{-- REVISI PERMINTAAN: HAPUS BLOK NOTIFIKASI ERROR VALIDASI BACKEND YANG LAMA DI SINI --}}
    
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-500">Administrasi</span>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Pengiriman Dokumen</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pengiriman Dalam Negeri dan Luar Negeri</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
        
        <!-- Filter Kanan Atas Dinamis (SAMA SEPERTI LOOKER) -->
        <form action="{{ route('pengiriman-dokumen.index') }}" method="GET" class="flex items-center gap-3">
            <!-- Dropdown Tahun Dinamis -->
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors hover:border-gray-300 hover:bg-gray-50">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>

            <!-- Dropdown Bulan Dinamis -->
            <select name="month" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors hover:border-gray-300 hover:bg-gray-50">
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $monthName)
                    <option value="{{ $monthName }}" {{ $selectedMonth == $monthName ? 'selected' : '' }}>{{ $monthName }}</option>
                @endforeach
                <option value="semua" {{ $selectedMonth == 'semua' ? 'selected' : '' }}>Semua Bulan ({{ $selectedYear }})</option>
            </select>
        </form>
    </div>

    <!-- ==========================================
          VISUALISASI 1: BAR CHART (Gunakan Component)
          Urutan: Chart di ATAS Tabel (Persis Looker)
    =========================================== -->
    {{-- Komponen Chart yang Anda Buat --}}
    <x-dynamic-chart 
        title="Statistik Volume Pengiriman & Mailroom" 
        subtitle="Rincian Pengiriman Dokumen terkait penerimaan mailroom, registrasi surat masuk via DOF, pengiriman dalam negeri dan pengiriman luar negeri pada bulan laporan." 
        type="bar" 
        id="volumeVolumeChart" 
    />

    <!-- ==========================================
          TABEL 1: VISUALISASI VOLUME DOKUMEN
          Mempresentasikan data volume dokumen per kategori
    =========================================== -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        
        <div class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Rincian Volume Dokumen</h3>
                <p class="text-xs text-gray-400">Rincian volume dokumen berdasarkan kategori per bulan untuk tahun {{ $selectedYear }}</p>
            </div>
            
            {{-- Tombol Buka Modal Tambah Data tetap di sini (Visualisasi Biaya) --}}
            <x-button variant="primary" onclick="openModalTambah()" class="shadow-sm text-xs border-none !py-2 !px-4">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah/Perbaharui Laporan Bulan Ini
            </x-button>
        </div>

        <x-table :headers="['Bulan ▲', 'Penerimaan mailroom', 'Registrasi Surat Masuk via DOF', 'Pengiriman dalam negeri', 'Pengiriman luar negeri']">
            @forelse($costRecords as $record)
                {{-- Gunakan data volume yang sama yang Anda format untuk chart, di Controller PHP ($costRecords) --}}
                <tr class="{{ $loop->iteration % 2 == 1 ? 'bg-gray-50/60' : '' }} hover:bg-gray-100 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-900 font-medium align-middle">{{ $record->bulan }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono align-middle text-left">{{ number_format($record->penerimaan_mailroom, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono align-middle text-left">{{ number_format($record->registrasi_surat_masuk_dof, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono align-middle text-left">{{ number_format($record->pengiriman_dalam_negeri, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono align-middle text-left">{{ number_format($record->pengiriman_luar_negeri, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data laporan volume dokumen tahun {{ $selectedYear }} pada periode filter terpilih.</td></tr>
            @endforelse
        </x-table>
        
        <!-- Pagination Footer (Laravel pagination) -->
        <div class="p-4 border-t border-gray-100 text-xs bg-white">
            {{ $costRecords instanceof \Illuminate\Pagination\LengthAwarePaginator ? $costRecords->links() : "Menampilkan seluruh " . $costRecords->count() . " data laporan bulanan" }}
        </div>
    </x-card>

    <!-- ==========================================
          TABEL 2: VISUALISASI TOTAL ONGKIR
          Urutan: Tabel di BAWAH Chart (Persis Looker)
    =========================================== -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        
        <div class="p-5 border-b border-gray-100 bg-white">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Rincian Total Ongkir Pengiriman Bulanan</h3>
                <p class="text-xs text-gray-400">Rincian total ongkos kirim domestik dan internasional tahun {{ $selectedYear }}</p>
            </div>
        </div>

        {{-- Gunakan x-table komponen Anda, sesuaikan header persis Looker (Image 19) --}}
        <x-table :headers="['Tahun', 'Bulan', 'Total Ongkir Pengiriman Dalam Negeri', 'Total Ongkir Pengiriman Luar Negeri']">
            @forelse($costRecords as $cost)
                <tr class="{{ $loop->iteration % 2 == 1 ? 'bg-gray-50/60' : '' }} hover:bg-gray-100 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-700 font-medium align-middle">{{ $cost->tahun }}</td>
                    <td class="px-6 py-4 text-gray-900 font-medium align-middle">{{ $cost->bulan }}</td>
                    {{-- 
                        --- PERBAIKAN FATAL AKSE DATA ---
                        Ubah properti akses ke nama KOLOM DB yang benar (Migration combined combined combined): 
                        ongkir_dalam_negeri, ongkir_luar_negeri
                    --}}
                    <td class="px-6 py-4 text-gray-700 font-mono font-semibold align-middle text-left">Rp {{ number_format($cost->ongkir_dalam_negeri, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono font-semibold align-middle text-left">Rp {{ number_format($cost->ongkir_luar_negeri, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data laporan biaya ongkir tahun {{ $selectedYear }} pada periode filter terpilih.</td></tr>
            @endforelse
            
            {{-- Row Total Keseluruhan (Persis Looker) --}}
            <tr class="bg-gray-100 border-t border-gray-200 font-bold text-blue-900 text-sm">
                <td colspan="2" class="px-6 py-4 text-right">Total Keseluruhan {{ $selectedMonth == 'semua' ? "$selectedYear" : "$selectedMonth $selectedYear" }} :</td>
                <td class="px-6 py-4 text-pkt-biru font-mono text-left">Rp {{ number_format($totalDomestikOverall, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-pkt-biru font-mono text-left">Rp {{ number_format($totalInternasionalOverall, 0, ',', '.') }}</td>
            </tr>
        </x-table>
        
        <!-- Pagination Footer (Laravel pagination) -->
        <div class="p-4 border-t border-gray-100 text-xs bg-white">
            {{ $costRecords instanceof \Illuminate\Pagination\LengthAwarePaginator ? $costRecords->links() : "Menampilkan seluruh " . $costRecords->count() . " data laporan bulanan" }}
        </div>
    </x-card>

    <!-- ==========================================
          MODAL TAMBAH / UPDATE DATA LAPORAN 
          (Untuk Volume & Biaya sekaligus sesuai Excel)
    =========================================== -->
    <x-modal id="modalTambahLaporan" title="Formulir Laporan Bulanan Pengiriman Dokumen" description="Masukkan jumlah dokumen serta biaya ongkir untuk bulan laporan yang dipilih. Ketentuan: Jika sudah ada, data akan diperbaharui. Jika terdapat penambahan kolom pada template excel, isilah pada kolom yang sesuai.">
        
        <form action="{{ route('pengiriman-dokumen.store') }}" method="POST" id="formLaporan" class="novalidate-form" novalidate>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                
                {{-- Bagian Kunci Primary Tahun/Bulan --}}
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Laporan <span class="text-red-500">*</span></label>
                    {{-- PERBAIKAN UX: Tambahkan class 'border-red-500' jika ada error --}}
                    <select name="tahun" required class="w-full px-4 py-2.5 bg-white border border-gray-300 @error('tahun') border-red-500 @enderror rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 transition-colors">
                        {{-- 
                            --- PERBAIKAN TAHUN DINAMIS ---
                            Loop dropdown Tahun dinamis dari Controller ($availableYears), JANGAN HARDCODE
                        --}}
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ Carbon\Carbon::now()->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    @error('tahun')
                        <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan Laporan <span class="text-red-500">*</span></label>
                    {{-- PERBAIKAN UX: Tambahkan class 'border-red-500' jika ada error --}}
                    <select name="bulan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 @error('bulan') border-red-500 @enderror rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 transition-colors">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $monthName)
                            <option value="{{ $monthName }}" {{ Carbon\Carbon::now()->monthName == $monthName ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endforeach
                    </select>
                    @error('bulan')
                        <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-2 border-t border-gray-100 pt-5 mt-2">
                    <h4 class="font-semibold text-gray-900 mb-4 text-sm bg-gray-50 p-3 rounded-lg border border-gray-100">1. Data Jumlah Dokumen (Visualisasi Chart)</h4>
                </div>

                {{-- Kolom Volume: Dibuat HANYA Menerima ANGKA --}}
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Penerimaan Mailroom <span class="text-red-500">*</span></label>
                    {{-- PERBAIKAN UX: Tambahkan class 'border-red-500' jika ada error --}}
                    <input type="number" name="volume_mailroom" required min="0" placeholder="Contoh: 825" class="w-full px-4 py-2.5 bg-white border border-gray-300 @error('volume_mailroom') border-red-500 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner transition-colors">
                    @error('volume_mailroom')
                        <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span>
                    @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Registrasi Surat Masuk via DOF <span class="text-red-500">*</span></label>
                    {{-- PERBAIKAN UX: Tambahkan class 'border-red-500' jika ada error --}}
                    <input type="number" name="volume_dof" required min="0" placeholder="Contoh: 334" class="w-full px-4 py-2.5 bg-white border border-gray-300 @error('volume_dof') border-red-500 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner transition-colors">
                    @error('volume_dof')
                        <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span>
                    @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pengiriman Dalam Negeri <span class="text-red-500">*</span></label>
                    {{-- PERBAIKAN UX: Tambahkan class 'border-red-500' jika ada error --}}
                    <input type="number" name="volume_domestik" required min="0" placeholder="Contoh: 83" class="w-full px-4 py-2.5 bg-white border border-gray-300 @error('volume_domestik') border-red-500 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner transition-colors">
                    @error('volume_domestik')
                        <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span>
                    @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pengiriman Luar Negeri <span class="text-red-500">*</span></label>
                    {{-- PERBAIKAN UX: Tambahkan class 'border-red-500' jika ada error --}}
                    <input type="number" name="volume_internasional" required min="0" placeholder="Contoh: 0" class="w-full px-4 py-2.5 bg-white border border-gray-300 @error('volume_internasional') border-red-500 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner transition-colors">
                    @error('volume_internasional')
                        <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-2 border-t border-gray-100 pt-5 mt-2">
                    <h4 class="font-semibold text-gray-900 mb-4 text-sm bg-gray-50 p-3 rounded-lg border border-gray-100">2. Data Biaya Ongkir Rupiah (Visualisasi Tabel Total)</h4>
                </div>

                {{-- Kolom Biaya: Dibuat INPUT ANGKA OTOMATIS DITITIKKAN tiap kelipatannya --}}
                <div class="md:col-span-1 relative group">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Ongkir Pengiriman Dalam Negeri <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-sm">Rp</span>
                        {{-- Batasan Teknis: Input Rupiah Otomatis Dititikkan --}}
                        {{-- PERBAIKAN UX: Tambahkan class 'border-red-500' jika ada error --}}
                        <input type="text" id="input_cost_domestik" required placeholder="0" class="input-rupiah w-full pl-11 pr-4 py-2.5 bg-white border border-gray-300 @error('cost_domestik') border-red-500 @enderror rounded-lg text-sm text-gray-900 font-mono focus:outline-none focus:border-pkt-biru focus:ring-1 focus:ring-blue-100 shadow-inner transition-all hover:border-pkt-biru group-hover:border-pkt-biru">
                        {{-- Hidden Input untuk dikirim ke backend (hanya angka) --}}
                        <input type="hidden" name="cost_domestik" required>
                    </div>
                    @error('cost_domestik')
                        <span class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-1 relative group">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Ongkir Pengiriman Luar Negeri <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-sm">Rp</span>
                        {{-- Batasan Teknis: Input Rupiah Otomatis Dititikkan --}}
                        {{-- PERBAIKAN UX: Tambahkan class 'border-red-500' jika ada error --}}
                        <input type="text" id="input_cost_internasional" required placeholder="0" class="input-rupiah w-full pl-11 pr-4 py-2.5 bg-white border border-gray-300 @error('cost_internasional') border-red-500 @enderror rounded-lg text-sm text-gray-900 font-mono focus:outline-none focus:border-pkt-biru focus:ring-1 focus:ring-blue-100 shadow-inner transition-all hover:border-pkt-biru group-hover:border-pkt-biru">
                        {{-- Hidden Input untuk dikirim ke backend (hanya angka) --}}
                        <input type="hidden" name="cost_internasional" required>
                    </div>
                    @error('cost_internasional')
                        <span class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-8 border-t border-gray-100 pt-6">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahLaporan')" class="!px-6 !py-2.5 !rounded-lg text-sm">Batal</x-button>
                <x-button variant="primary" type="submit" class="!px-6 !py-2.5 !rounded-lg text-sm border-none shadow-md !bg-pkt-jingga hover:!bg-orange-600">Simpan Perubahan Laporan</x-button>
            </div>
        </form>
    </x-modal>

</main>

{{-- =======================================================
      JAVASCRIPT SECTION 
      (Logika Visualisasi Chart Dinamis, Batasan Input Rupiah, Validasi Client-Side)
========================================================= --}}
<script>
    // ----------------------------------------------------
    // PERBAIKAN UX (UX): Trik Chart Tetap Muncul Meski Data Kosong
    // ----------------------------------------------------
    // Variabel global untuk menyimpan instansi chart dan datanya
    let volumeVolumeChartInstance = null;
    const volumeChartConfigData = {!! json_encode($chartVolumeConfig) !!}; // Data dinamis dari PHP

    // Fungsi untuk menginisialisasi atau memperbarui chart
    function initVolumeChart(chartType) {
        const volumeChartCanvas = document.getElementById('canvas_volumeVolumeChart'); // ID kanvas component baru
        if (volumeChartCanvas) {
            const ctx = volumeChartCanvas.getContext('2d');

            // Hancurkan instansi chart lama jika ada
            if (volumeVolumeChartInstance) {
                volumeVolumeChartInstance.destroy();
            }

            // --- TRIK DI SINI: Penanganan Data Kosong agar Kerangka Tetap Muncul ---
            let chartDataToUse = JSON.parse(JSON.stringify(volumeChartConfigData)); // Deep copy data asli
            let isEmpty = false;

            // Cek apakah labels kosong atau dataset pertama datanya kosong
            if (!chartDataToUse.labels || chartDataToUse.labels.length === 0 || (chartDataToUse.datasets.length > 0 && chartDataToUse.datasets[0].data.length === 0)) {
                isEmpty = true;
            }

            if (isEmpty) {
                // Tentukan Label default (Jan - Des) agar sumbu X muncul
                chartDataToUse.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                
                // Isi semua dataset dengan angka 0 sesuai jumlah label
                chartDataToUse.datasets.forEach(dataset => {
                    dataset.data = new Array(chartDataToUse.labels.length).fill(0);
                });
            }
            // ----------------------------------------------------------------------

            // Opsi Chart.js yang disesuaikan
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom', // Pindahkan legenda ke bawah agar serasi dengan kartu component
                        labels: { boxWidth: 12, font: { size: 10 } }
                    },
                    tooltip: {
                        enabled: !isEmpty, // Matikan tooltip jika data kosong (angka 0 palsu)
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                return label;
                            }
                        }
                    },
                    // Tambahkan plugin untuk menulis teks "Tidak Ada Data" di tengah canvas
                    beforeDraw: function(chart) {
                        if (isEmpty) {
                            var width = chart.width,
                                height = chart.height,
                                ctx = chart.ctx;
                            ctx.restore();
                            var fontSize = (height / 114).toFixed(2);
                            ctx.font = fontSize + "em sans-serif";
                            ctx.textBaseline = "middle";
                            var text = "Tidak Ada Data Laporan",
                                textX = Math.round((width - ctx.measureText(text).width) / 2),
                                textY = height / 2;
                            ctx.fillStyle = '#9CA3AF'; // gray-400
                            ctx.fillText(text, textX, textY);
                            ctx.save();
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        // Jika kosong, paksa sumbu Y menunjukkan skala kecil (misal 0-10) agar grid muncul
                        max: isEmpty ? 10 : undefined, 
                        grid: { color: '#F3F4F6' },
                        ticks: { font: { size: 10, color: '#6B7280' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '500', color: '#6B7280' } }
                    }
                }
            };

            // Tambahkan konfigurasi khusus berdasarkan tipe chart jika perlu
            // (Misalnya, jika Pie, kita tidak butuh sumbu X/Y)
            if (chartType === 'pie' || chartType === 'doughnut') {
                if (isEmpty) {
                    // Untuk Pie, jika kosong kita hapus label palsu agar tidak jelek di legenda
                    chartDataToUse.labels = []; 
                    // Buat satu dataset abu-abu penuh 100% sebagai placeholder
                    chartDataToUse.datasets = [{
                        data: [1],
                        backgroundColor: ['#E5E7EB'], // gray-200
                        borderWidth: 0
                    }];
                }
                delete chartOptions.scales;
                chartOptions.plugins.legend.position = 'bottom';
            }

            // Buat instansi chart baru dengan data yang sudah disesuaikan
            volumeVolumeChartInstance = new Chart(ctx, {
                type: chartType,
                data: chartDataToUse,
                options: chartOptions
            });
        }
    }

    // Inisialisasi chart default saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        initVolumeChart('{{ $dynamicChartType ?? 'bar' }}'); // Mulai dengan Bar Chart atau default props component
    });

    // Fungsi Global yang dipanggil oleh component x-dynamic-chart saat tipe diubah
    function changeChartType(chartId, newType) {
        if (chartId === 'volumeVolumeChart') {
            initVolumeChart(newType);
        }
        // Tambahkan blok 'else if' untuk ID chart dinamis lainnya di halaman ini jika ada
    }

    // ----------------------------------------------------
    // 2. Logika Batasan Input Rupiah (Otomatis Dititikkan)
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        const rupiahInputs = document.querySelectorAll('.input-rupiah');

        rupiahInputs.forEach(input => {
            // Event saat pengguna mengetik
            input.addEventListener('input', function(e) {
                // Bersihkan karakter selain angka
                let rawValue = this.value.replace(/[^0-9]/g, '');
                
                // REVISI PERMINTAAN: Hapus angka 0 di depan jika diikuti angka lain. "05" -> "5", "00" -> "0", "0" -> "0"
                let cleanedValue = rawValue.replace(/^0+(?!$)/, '');

                // Format dengan ribuan titik (id-ID)
                if (cleanedValue) {
                    let formattedValue = new Intl.NumberFormat('id-ID').format(cleanedValue);
                    this.value = formattedValue;
                    
                    // Masukkan nilai asli (hanya angka) ke hidden input backend
                    this.nextElementSibling.value = cleanedValue;
                } else {
                    this.value = '';
                    this.nextElementSibling.value = '';
                }
            });

            // Pastikan jika ada nilai awal dari backend, hidden input terisi
            if(input.value) {
                // Terapkan logika yang sama untuk data yang sudah ada (reset 0 di depan)
                let rawValue = input.value.replace(/[^0-9]/g, '');
                let cleanedValue = rawValue.replace(/^0+(?!$)/, '');
                input.nextElementSibling.value = cleanedValue;
            }
        });
    });

    // ----------------------------------------------------
    // REVISI PERMINTAAN: 2a. Logika Batasan Angka 0 di Depan untuk Input Standar (Type Number)
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        // Pilih semua input type="number" di dalam form modal
        const standardNumberInputs = document.querySelectorAll('#modalTambahLaporan form input[type="number"]');

        standardNumberInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                if (this.value) {
                    // Regex ini menghapus 0 di depan jika diikuti angka lain. "05" -> "5", "00" -> "0", "0" -> "0"
                    this.value = this.value.replace(/^0+(?!$)/, '');
                }
            });
        });
    });

    // ----------------------------------------------------
    // REVISI PERMINTAAN 1: Logika Validasi Form Client-Side (Merahkan Input, Biarkan Laravel Tampilkan Teks Error)
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        const validateForms = document.querySelectorAll('.novalidate-form');

        validateForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                // Ambil semua required field
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
                    // Cek jika kosong
                    if (!field.value || field.value.trim() === '') {
                        isValid = false;
                        field.classList.add('border-red-500', 'bg-red-50'); // Merahkan input
                    } else {
                        field.classList.remove('border-red-500', 'bg-red-50'); // Hapus merah jika terisi
                    }
                });

                if (!isValid) {
                    e.preventDefault(); // Gagal submit client-side agar user perbaiki
                }
            });

            // Real-time validasi saat input berubah untuk pengalaman user yang lebih baik
            form.querySelectorAll('[required]').forEach(field => {
                field.addEventListener('input', function() {
                    if (this.value && this.value.trim() !== '') {
                         this.classList.remove('border-red-500', 'bg-red-50');
                         // Kita tidak menyembunyikan teks error Laravel di sini, biar submit server yang mengatur.
                    }
                });
            });
        });
    });

    // Fungsi buka/tutup modal standar (reuseable)
    function openModalTambah() {
        document.getElementById('formLaporan').reset(); // Reset form saat buka
        // Hapus style validasi rojo yang mungkin tersisa
        document.getElementById('formLaporan').querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));
        document.getElementById('modalTambahLaporan').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Lock scroll body
    }

    // Fungsi tutup modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = ''; // Mengembalikan scroll body
    }

</script>
@endsection