@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<!-- Main Scrollable Content -->
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">

    <x-success-modal />

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Karyawan</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Karyawan</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <!-- SECTION ATAS: CHART UTAMA -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <x-dynamic-chart id="chartJenis" title="Karyawan Dep. Adkor" subtitle="Berdasarkan jenis karyawan" type="pie">
            <div class="flex items-center justify-around">
                <div class="relative w-32 h-32 rounded-full bg-gray-100 border-[14px] border-blue-200 border-t-pkt-jingga transform -rotate-45">
                    <div class="absolute inset-0 bg-white rounded-full m-1 transform rotate-45 flex flex-col items-center justify-center">
                        <span class="text-xl font-bold text-gray-900">75%</span>
                        <span class="text-[9px] text-gray-400 font-semibold">ORGANIK</span>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-pkt-jingga"></span><span class="text-gray-600">Organik (12)</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-blue-200"></span><span class="text-gray-600">Non Organik (4)</span></div>
                </div>
            </div>
        </x-dynamic-chart>

        <x-dynamic-chart id="chartPensiun" title="Status Ket. Pensiun Karyawan" subtitle="Distribusi berdasarkan masa kerja tersisa" type="pie">
            <div class="flex items-center justify-around">
                <div class="relative w-32 h-32 rounded-full bg-gray-100 border-[14px] border-blue-500 border-t-red-500 border-r-orange-400 transform -rotate-90">
                    <div class="absolute inset-0 bg-white rounded-full m-1 transform rotate-90 flex flex-col items-center justify-center">
                        <span class="text-lg font-bold text-gray-900">> 10 Thn</span>
                        <span class="text-[9px] text-gray-400 font-semibold">DOMINAN</span>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-blue-500"></span><span class="text-gray-600">> 10 Tahun (8)</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-red-500"></span><span class="text-gray-600">< 5 Tahun (4)</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-orange-400"></span><span class="text-gray-600">< 10 Tahun (4)</span></div>
                </div>
            </div>
        </x-dynamic-chart>
    </div>

    <!-- Action Bar -->
    <div class="flex justify-end items-center mb-6">
        <x-button variant="primary" onclick="bukaModalTambah()" class="!rounded-xl shadow-sm !bg-pkt-jingga hover:!bg-orange-600 border-none">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Karyawan
        </x-button>
    </div>

    <!-- DATA TABLE SECTION -->
    <x-card class="!rounded-xl overflow-hidden !p-0 shadow-sm border border-gray-100">
        <x-table :headers="['No', 'Nama', 'NPK', 'Gol/Grade', 'MPP/PBP', 'Ket. Pensiun', 'Keterangan', 'Ukuran Kaos', 'Aksi']">
            @forelse($karyawan as $index => $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-center font-medium text-gray-500">
                        {{ $karyawan->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama }}</td>
                    <td class="px-6 py-4 text-gray-500 font-mono text-xs text-center">{{ $item->npk }}</td>
                    <td class="px-6 py-4 text-center"><span class="font-bold text-blue-600">{{ $item->gol_grade }}</span></td>
                    <td class="px-6 py-4 text-center text-gray-700">
                        {{ \Carbon\Carbon::parse($item->mpp_pbp)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $bgPensiun = 'bg-gray-100 text-gray-600';
                            if ($item->ket_pensiun == '> 10 Tahun') {
                                $bgPensiun = 'bg-green-100 text-green-700 border border-green-200';
                            } elseif ($item->ket_pensiun == '< 5 Tahun') {
                                $bgPensiun = 'bg-red-500 text-white font-bold border border-red-600';
                            } elseif ($item->ket_pensiun == '< 10 Tahun') {
                                $bgPensiun = 'bg-orange-100 text-orange-700 border border-orange-200';
                            }
                        @endphp
                        <span class="px-2.5 py-1 rounded-md text-xs font-medium {{ $bgPensiun }}">
                            {{ $item->ket_pensiun ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-md text-xs font-medium {{ $item->keterangan == 'Organik' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }}">
                            {{ $item->keterangan }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-700 text-center">{{ $item->ukuran_kaos }}</td>
                    
                    <td class="px-6 py-4">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('karyawan.show', $item->id) }}" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-md border border-blue-200" title="Lihat Detail & Keluarga">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <button type="button" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit"
                                onclick="bukaModalEdit({
                                    id: '{{ $item->id }}',
                                    nama: '{{ addslashes($item->nama) }}',
                                    npk: '{{ $item->npk }}',
                                    foto: '{{ $item->foto }}',
                                    tempat_lahir: '{{ addslashes($item->tempat_lahir) }}',
                                    tanggal_lahir: '{{ $item->tanggal_lahir ? $item->tanggal_lahir->format('Y-m-d') : '' }}',
                                    no_ptk: '{{ $item->no_ptk }}',
                                    gol_grade: '{{ $item->gol_grade }}',
                                    keterangan: '{{ $item->keterangan }}',
                                    no_hp: '{{ $item->no_hp }}',
                                    alamat: '{{ addslashes($item->alamat) }}',
                                    ukuran_kaos: '{{ $item->ukuran_kaos }}',
                                    mpp_pbp: '{{ $item->mpp_pbp ? $item->mpp_pbp->format('Y-m-d') : '' }}',
                                    ket_pensiun: '{{ $item->ket_pensiun }}'
                                })">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button type="button" onclick="openDeleteModal('modalHapusKaryawan', '/karyawan/{{ $item->id }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-6 py-4 text-center text-gray-500 py-6">Tidak ada data karyawan ditemukan.</td></tr>
            @endforelse
        </x-table>
        
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-white">
            {{ $karyawan->links() }}
        </div>
    </x-card>

    <x-delete-modal id="modalHapusKaryawan" title="Hapus Data Karyawan" message="Apakah Anda yakin ingin menghapus data karyawan ini beserta seluruh keluarganya?" />

    <!-- ================= MODAL TAMBAH / EDIT KARYAWAN ================= -->
    <x-modal id="modalKaryawan" title="Formulir Data Karyawan" description="Lengkapi data personal dan atribut karyawan">
        <!-- Tambahkan class novalidate-form -->
        <form action="" method="POST" id="formKaryawan" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 novalidate-form" novalidate>
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="id" id="karyawanId" value="{{ old('id') }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="formNama" value="{{ old('nama') }}" required placeholder="Bambang Setiawan" class="w-full px-4 py-2.5 bg-white border @error('nama') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                @error('nama') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NPK <span class="text-red-500">*</span></label>
                <input type="text" name="npk" id="formNpk" value="{{ old('npk') }}" required placeholder="40xxxxx" class="w-full px-4 py-2.5 bg-white border @error('npk') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                @error('npk') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Foto</label>
                <input type="url" name="foto" id="formFoto" value="{{ old('foto') }}" placeholder="https://..." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" id="formTempatLahir" value="{{ old('tempat_lahir') }}" placeholder="Bontang" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="formTanggalLahir" value="{{ old('tanggal_lahir') }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. PTK</label>
                <input type="text" name="no_ptk" id="formNoPtk" value="{{ old('no_ptk') }}" placeholder="00xxxx" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. HP / Telepon</label>
                <input type="text" name="no_hp" id="formNoHp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal MPP/PBP <span class="text-red-500">*</span></label>
                <input type="date" name="mpp_pbp" id="formMppPbp" value="{{ old('mpp_pbp') }}" required class="w-full px-4 py-2.5 bg-white border @error('mpp_pbp') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                @error('mpp_pbp') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ket. Pensiun <span class="text-red-500">*</span></label>
                <select name="ket_pensiun" id="formKetPensiun" required class="w-full px-4 py-2.5 bg-white border @error('ket_pensiun') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                    <option value="" disabled {{ old('ket_pensiun') ? '' : 'selected' }}>Pilih...</option>
                    <option value="> 10 Tahun" {{ old('ket_pensiun') == '> 10 Tahun' ? 'selected' : '' }}>> 10 Tahun</option>
                    <option value="< 10 Tahun" {{ old('ket_pensiun') == '< 10 Tahun' ? 'selected' : '' }}>< 10 Tahun</option>
                    <option value="< 5 Tahun" {{ old('ket_pensiun') == '< 5 Tahun' ? 'selected' : '' }}>< 5 Tahun</option>
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                @error('ket_pensiun') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Golongan / Grade <span class="text-red-500">*</span></label>
                <select name="gol_grade" id="formGolGrade" required class="w-full px-4 py-2.5 bg-white border @error('gol_grade') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="" disabled {{ old('gol_grade') ? '' : 'selected' }}>Pilih gol...</option>
                    @foreach(['I-A','I-B','I-C','I-D','II-A','II-B','II-C','II-D','III-A','III-B','III-C','III-D','IV-A','IV-B','IV-C','IV-D'] as $gol)
                        <option value="{{ $gol }}" {{ old('gol_grade') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                @error('gol_grade') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan <span class="text-red-500">*</span></label>
                <select name="keterangan" id="formKeterangan" required class="w-full px-4 py-2.5 bg-white border @error('keterangan') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                    <option value="" disabled {{ old('keterangan') ? '' : 'selected' }}>Pilih ket...</option>
                    <option value="Organik" {{ old('keterangan') == 'Organik' ? 'selected' : '' }}>Organik</option>
                    <option value="Non Organik" {{ old('keterangan') == 'Non Organik' ? 'selected' : '' }}>Non Organik</option>
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                @error('keterangan') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="alamat" id="formAlamat" required rows="3" placeholder="Jl. Gladiol No. 1..." class="w-full px-4 py-2.5 bg-white border @error('alamat') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none">{{ old('alamat') }}</textarea>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi!</span>
                @error('alamat') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ukuran Kaos <span class="text-red-500">*</span></label>
                <select name="ukuran_kaos" id="formUkuranKaos" required class="w-full px-4 py-2.5 bg-white border @error('ukuran_kaos') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm">
                    <option value="" disabled {{ old('ukuran_kaos') ? '' : 'selected' }}>Pilih...</option>
                    @foreach(['S','M','L','XL','XXL','XXXL'] as $uk)
                        <option value="{{ $uk }}" {{ old('ukuran_kaos') == $uk ? 'selected' : '' }}>{{ $uk }}</option>
                    @endforeach
                </select>
                <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib dipilih!</span>
                @error('ukuran_kaos') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>
        </form>

        <x-slot name="footer">
            <x-button variant="outline" onclick="closeModalKaryawan()" class="!px-6 !py-2.5 !rounded-lg">
                Batal
            </x-button>
            <x-button variant="secondary" type="submit" form="formKaryawan" id="btnSubmitKaryawan" class="!px-6 !py-2.5 !rounded-lg !bg-blue-700 hover:!bg-blue-800">
                Simpan Data
            </x-button>
        </x-slot>
    </x-modal>

</main>

<script>
    Chart.register(ChartDataLabels);

    // Warna abu-abu netral untuk kondisi "belum ada data"
    const EMPTY_CHART_COLOR = '#E5E7EB';

    const warnaPensiunMap = {
        '> 10 Tahun': '#3B82F6',
        '< 5 Tahun': '#EF4444',
        '< 10 Tahun': '#FB923C',
        'Sudah Pensiun': '#6B7280',
    };

    const labelsPensiunDariDB = {!! json_encode(array_keys($chartPensiunData)) !!};
    const backgroundColorsPensiun = labelsPensiunDariDB.map(label => warnaPensiunMap[label] || '#94A3B8');

    // Konfigurasi mentah untuk tiap chart (dipakai renderChart() & saat ganti tipe via dropdown)
    const allChartData = {
        chartJenis: {
            labels: {!! json_encode(array_keys($chartJenisData)) !!},
            data: {!! json_encode(array_values($chartJenisData)) !!},
            colors: ['#F7941E', '#0056A3'],
            hasData: {{ (array_sum($chartJenisData)) > 0 ? 'true' : 'false' }},
        },
        chartPensiun: {
            labels: labelsPensiunDariDB,
            data: {!! json_encode(array_values($chartPensiunData)) !!},
            colors: backgroundColorsPensiun,
            hasData: {{ (array_sum($chartPensiunData)) > 0 ? 'true' : 'false' }},
        },
    };

    // Menyimpan instance Chart.js yang sedang aktif per chart, supaya bisa diganti tipenya (pie/bar/doughnut)
    const chartInstances = {};

    function buildChartData(config) {
        if (!config.hasData) {
            return {
                labels: ['Tidak ada data'],
                datasets: [{ label: 'Status', data: [1], backgroundColor: [EMPTY_CHART_COLOR], borderWidth: 0 }]
            };
        }

        return {
            labels: config.labels,
            datasets: [{ label: 'Jumlah', data: config.data, backgroundColor: config.colors, borderWidth: 0, hoverOffset: 10 }]
        };
    }

    function renderChart(chartId, type) {
        const canvasEl = document.getElementById('canvas_' + chartId);
        const config = allChartData[chartId];
        if (!canvasEl || !config) return;

        const isEmpty = !config.hasData;

        if (chartInstances[chartId]) {
            chartInstances[chartId].destroy();
        }

        chartInstances[chartId] = new Chart(canvasEl.getContext('2d'), {
            type: type,
            data: buildChartData(config),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: (type === 'bar' && !isEmpty) ? { y: { beginAtZero: true, ticks: { precision: 0 } } } : {},
                plugins: {
                    // Legend per-dataset pada Bar Chart menyebabkan label "undefined" (dataset tanpa nama);
                    // untuk Bar, jumlah tiap kategori sudah terlihat dari sumbu-X + angka di atas bar.
                    legend: {
                        display: type !== 'bar',
                        position: 'bottom',
                        labels: { usePointStyle: true, font: { size: 10 }, padding: 15 }
                    },
                    tooltip: {
                        enabled: !isEmpty,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 10,
                        callbacks: {
                            label: (context) => {
                                const val = (context.parsed && typeof context.parsed === 'object')
                                    ? (context.parsed.y ?? context.parsed.x)
                                    : context.parsed;
                                return ` ${context.label}: ${val} orang`;
                            }
                        }
                    },
                    datalabels: isEmpty ? { display: false } : {
                        color: (type === 'bar') ? '#111827' : '#ffffff',
                        anchor: (type === 'bar') ? 'end' : 'center',
                        align: (type === 'bar') ? 'top' : 'center',
                        font: { weight: 'bold', size: 12 },
                        formatter: (value) => value
                    }
                }
            }
        });
    }

    // Dipanggil otomatis oleh komponen x-dynamic-chart saat dropdown tipe chart diganti
    function changeChartType(chartId, newType) {
        renderChart(chartId, newType);
    }

    window.onload = function () {
        renderChart('chartJenis', 'pie');
        renderChart('chartPensiun', 'pie');
    };

    const modalEl = document.getElementById('modalKaryawan');
    const formKaryawan = document.getElementById('formKaryawan');
    const modalTitle = modalEl.querySelector('h3');
    const methodField = document.getElementById('methodField');

    function bukaModalTambah() {
        modalTitle.textContent = "Tambah Karyawan Baru";
        formKaryawan.action = "{{ route('karyawan.store') }}"; 
        methodField.value = "POST";
        document.getElementById('karyawanId').value = "";
        formKaryawan.reset();

        // Hapus styling error jika ada sisa
        formKaryawan.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'bg-red-50');
            el.classList.add('border-gray-300');
        });
        formKaryawan.querySelectorAll('p.text-red-500, span.error-msg').forEach(el => el.classList.add('hidden'));

        modalEl.classList.remove('hidden');
    }

    function bukaModalEdit(data) {
        modalTitle.textContent = "Edit Data Karyawan";
        formKaryawan.action = "/karyawan/" + data.id;
        methodField.value = "PUT";
        document.getElementById('karyawanId').value = data.id;

        document.getElementById('formNama').value = data.nama;
        document.getElementById('formNpk').value = data.npk;
        document.getElementById('formFoto').value = data.foto || '';
        document.getElementById('formTempatLahir').value = data.tempat_lahir || '';
        document.getElementById('formTanggalLahir').value = data.tanggal_lahir || '';
        document.getElementById('formNoPtk').value = data.no_ptk || '';
        document.getElementById('formMppPbp').value = data.mpp_pbp || '';
        document.getElementById('formKetPensiun').value = data.ket_pensiun || '';
        document.getElementById('formGolGrade').value = data.gol_grade;
        document.getElementById('formKeterangan').value = data.keterangan;
        document.getElementById('formNoHp').value = data.no_hp || '';
        document.getElementById('formAlamat').value = data.alamat || '';
        document.getElementById('formUkuranKaos').value = data.ukuran_kaos;

        // Hapus styling error jika ada sisa
        formKaryawan.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'bg-red-50');
            el.classList.add('border-gray-300');
        });
        formKaryawan.querySelectorAll('p.text-red-500, span.error-msg').forEach(el => el.classList.add('hidden'));

        modalEl.classList.remove('hidden');
    }

    function closeModalKaryawan() {
        modalEl.classList.add('hidden');
    }

    // LOGIKA AUTO-OPEN MODAL JIKA SERVER VALIDATION GAGAL (Contoh: NPK Duplikat)
    @if($errors->any())
        modalEl.classList.remove('hidden');
        let methodTerdahulu = "{{ old('_method') }}";
        let idTerdahulu = "{{ old('id') }}";
        
        if(methodTerdahulu === 'PUT' && idTerdahulu) {
            modalTitle.textContent = "Edit Data Karyawan";
            formKaryawan.action = "/karyawan/" + idTerdahulu;
            methodField.value = "PUT";
            document.getElementById('karyawanId').value = idTerdahulu;
        } else {
            modalTitle.textContent = "Tambah Karyawan Baru";
            formKaryawan.action = "{{ route('karyawan.store') }}"; 
            methodField.value = "POST";
        }
    @endif

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
@endsection