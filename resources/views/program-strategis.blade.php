@extends('layouts.app')

@section('content')
<!-- Tambahkan style untuk memodifikasi komponen tabel global khusus di halaman ini -->
<style>
    /* Paksa tabel agar mengikuti lebar layar dan membungkus teks panjang */
    table { table-layout: auto !important; width: 100% !important; }
    /* Jangan nowrap untuk sel isi agar teks turun ke bawah jika kepanjangan */
    td { white-space: normal !important; vertical-align: top; }
    th { white-space: nowrap !important; }
    /* Sembunyikan kolom pertama jika ada class hide-bulk */
    .hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
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
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        
        <div class="p-4 border-b border-gray-100 bg-orange-50 flex justify-between items-center gap-3 flex-wrap">
            <h3 class="font-bold text-gray-800 text-sm ml-2"></h3>
            
            <div class="flex gap-2">
                <!-- DROPDOWN OPSI LANJUTAN -->
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleDropdown('dropdownOpsiSuper')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-orange-200 shadow-sm px-4 py-1.5 bg-white text-xs font-medium text-orange-700 hover:bg-orange-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Opsi Lanjutan
                        <svg class="w-3.5 h-3.5 ml-1 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div id="dropdownOpsiSuper" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="py-1" role="menu">
                            
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <button type="button" onclick="openModal('modalAturKolom')" class="w-full text-left px-4 py-2.5 text-xs text-gray-700 hover:bg-orange-50 hover:text-orange-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                                Atur Kolom Tambahan
                            </button>
                            @endif
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <a href="{{ route('program-strategis.export.excel', ['tahun' => $filterTahun]) }}" class="w-full text-left px-4 py-2.5 text-xs text-green-700 hover:bg-green-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Export ke Excel
                            </a>
                            <a href="{{ route('program-strategis.export.pdf', ['tahun' => $filterTahun]) }}" target="_blank" class="w-full text-left px-4 py-2.5 text-xs text-red-700 hover:bg-red-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Cetak PDF
                            </a>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <button type="button" onclick="openModal('modalImport')" class="w-full text-left px-4 py-2.5 text-xs text-blue-700 hover:bg-blue-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import dari Excel
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>

                <x-button type="button" onclick="openModalTambahMaster()" class="!py-1.5 !px-3 text-xs bg-orange-500 hover:bg-orange-600 text-white border-none font-medium flex items-center gap-1 shadow-sm rounded-xl">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Sasaran & Program Strategis
                </x-button>

                <x-button variant="primary" onclick="openModalTambah()" class="!py-1.5 !px-3 text-xs flex items-center gap-1 shadow-sm rounded-xl border-none font-medium text-white">+ Tambah Rincian Data</x-button>
            </div>
        </div>

        <form id="bulkDeleteForm" action="{{ route('program-strategis.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
            @csrf
            @method('DELETE')
            
            <div id="btnGroupBulk" class="hidden justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg><span id="selectedCount">0</span> data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulk" class="hide-bulk overflow-x-auto w-full">
            @php
                $headers = ['<input type="checkbox" id="selectAllBulk" onclick="toggleSelectAll()">', 'Tahun', 'Bulan', 'Sasaran', 'Program Strategis', 'Program Kegiatan', 'Target Waktu', 'Realisasi (%)', 'Progress Saat Ini', 'Kendala'];
                if(isset($kolomDinamis)) {
                    foreach($kolomDinamis as $k) {
                        $headers[] = $k->nama_kolom;
                    }
                }
                $headers[] = 'Keterangan';
                $headers[] = 'Status';
                $headers[] = 'Aksi';
                
                $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            @endphp
            
            <x-table :headers="$headers">
                @forelse($groupedProgram as $group)
                    @php $rowspan = $group->count(); @endphp
                    @foreach($group as $index => $row)
                        @php 
                            $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);
                            
                            $statusColor = 'bg-gray-100 text-gray-700';
                            if(in_array($row->status, ['In Progress', 'Berjalan'])) $statusColor = 'bg-blue-100 text-blue-700';
                            if(in_array($row->status, ['Selesai', 'Tercapai'])) $statusColor = 'bg-green-100 text-green-700';
                            if(in_array($row->status, ['Hold', 'Tertunda'])) $statusColor = 'bg-red-100 text-red-700';
                        @endphp
                        
                        <tr class="hover:bg-gray-50 transition-colors text-[13px] border-b border-gray-100">
                            <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk" value="{{ $row->id }}" onclick="toggleCheckbox()"></td>
                            
                            <!-- INDUK DI-ROWSPAN HANYA PADA INDEX 0 -->
                            @if($index === 0)
                                <td rowspan="{{ $rowspan }}" class="p-4 text-gray-700 font-bold text-center border-r border-gray-100 align-top">{{ $row->tahun }}</td>
                                <td rowspan="{{ $rowspan }}" class="p-4 text-gray-700 font-bold text-center border-r border-gray-100 align-top">{{ ($row->bulan && is_numeric($row->bulan)) ? $bulanIndo[(int)$row->bulan] : '-' }}</td>
                                <td rowspan="{{ $rowspan }}" class="p-4 text-gray-900 font-medium border-r border-gray-100 w-48 align-top">{{ $row->sasaran }}</td>
                                <td rowspan="{{ $rowspan }}" class="p-4 text-gray-900 font-medium border-r border-gray-100 w-48 align-top">{{ $row->program_strategis }}</td>
                            @endif
                            
                            <!-- ANAK / RINCIAN (TIDAK ADA IF, TIDAK ADA ROWSPAN, SEMUANYA MANDIRI) -->
                            <td class="p-4 text-gray-700 border-r border-gray-100 w-56 whitespace-pre-line">{{ $row->deskripsi_kegiatan }}</td>
                            
                            <td class="p-4 text-gray-700 text-center border-r border-gray-100 align-top">
                                @if($row->target_waktu_start)
                                    {{ \Carbon\Carbon::parse($row->target_waktu_start)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            
                            <td class="p-4 text-gray-700 text-center font-medium border-r border-gray-100">{{ (!empty($row->realisasi) && $row->realisasi !== '-' && !str_ends_with(trim($row->realisasi), '%')) ? trim($row->realisasi) . '%' : ($row->realisasi ?? '-') }}</td>
                            <td class="p-4 text-gray-700 border-r border-gray-100 min-w-[300px] whitespace-pre-line">{!! e($row->progress_saat_ini) !!}</td>
                            
                            <td class="p-4 text-gray-700 border-r border-gray-100 align-top">{{ $row->kendala ?? '-' }}</td>
                            
                            @if(isset($kolomDinamis))
                                @foreach($kolomDinamis as $kolom)
                                    <td class="p-4 text-gray-700 text-center border-r border-gray-100">{{ $tambahan[$kolom->nama_kolom] ?? '-' }}</td>
                                @endforeach
                            @endif
                            
                            <td class="p-4 text-gray-500 italic border-r border-gray-100 align-top">{{ $row->keterangan_tambahan ?? '-' }}</td>
                            
                            <td class="p-4 text-center align-top">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $statusColor }} whitespace-nowrap">
                                    {{ $row->status }}
                                </span>
                            </td>
                            
                            <td class="p-4 text-center w-28 whitespace-nowrap min-w-[100px]">
                                <div class="flex flex-row gap-2 justify-center items-center">
                                    @php $rowDataJson = json_encode($row); @endphp
                                    <button type="button" onclick="editData({{ $rowDataJson }})" class="p-1.5 flex justify-center text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('program-strategis.destroy', $row->id) }}')" class="p-1.5 flex justify-center text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data program strategis.</td></tr>
                @endforelse
            </x-table>
            </div>
        </form>
        <!-- Paginator -->
        @if(method_exists($groupedProgram, 'links'))
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                {{ $groupedProgram->links() }}
            </div>
        @endif
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data" message="Data program strategis ini akan dihapus secara permanen. Lanjutkan?" />

    @if(auth()->user()->isAdmin())
    <!-- MODAL ATUR KOLOM -->
    <x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan" description="Tambah atau hapus kolom khusus pada tabel Program Strategis.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="openDeleteModal('modalHapusKolom', '{{ route('program-strategis.kolom.destroy', $kolom->id) }}')" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach
            @else
                <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan. Tabel menggunakan kolom standar.</p>
            @endif
        </div>
        <form action="{{ route('program-strategis.kolom.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="program_strategis">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">Nama Kolom</label>
                    <input type="text" name="nama_kolom" placeholder="Misal: Catatan Auditor" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputProgram" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500" onchange="toggleDropdownConfig('tipeInputProgram', 'dropdownConfigAreaProgram')">
                        <option value="text">Teks Singkat</option>
                        <option value="number">Angka Kuantitas Biasa</option>
                        <option value="currency">Harga / Uang (Titik Otomatis)</option>
                        <option value="date">Tanggal</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigAreaProgram">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Tinggi, Sedang, Rendah" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-orange-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button>
                <x-button variant="primary" type="submit" class="!bg-orange-500 hover:!bg-orange-600 border-none text-white">Simpan</x-button>
            </div>
        </form>
    </x-modal>
    @endif

    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel dan formulir secara permanen. Lanjutkan?" />

    <x-import-modal id="modalImport" route="{{ route('program-strategis.import') }}" title="Import Data Program Strategis" templateRoute="{{ route('template.download', 'program-strategis') }}" />

    <!-- MODAL TAMBAH MASTER -->
    <x-modal id="modalTambahMaster" title="Tambah Sasaran & Program Induk" description="Buat grup program strategis baru.">
        <form action="{{ route('program-strategis.store') }}" method="POST" class="grid grid-cols-1 gap-y-4 novalidate-form" novalidate id="formTambahMaster">
            @csrf
            <input type="hidden" name="is_master" value="1">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan & Tahun (Periode) <span class="text-red-500">*</span></label>
                <input type="month" name="periode" id="add_master_periode" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none cursor-pointer">
                    <span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sasaran <span class="text-red-500">*</span></label>
                <input type="text" name="sasaran" placeholder="Misal: Peningkatan Kinerja..." required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Program Strategis <span class="text-red-500">*</span></label>
                <input type="text" name="program_strategis" placeholder="Misal: Implementasi Sistem A..." required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>
            </div>

            <div class="flex justify-end gap-3 mt-4 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambahMaster')">Batal</x-button>
                <x-button variant="primary" type="submit" class="!bg-orange-500 hover:!bg-orange-600">Simpan Master</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL TAMBAH RINCIAN -->
    <x-modal id="modalTambah" title="Tambah Rincian Kegiatan" description="Pilih program induk yang sudah ada di sistem dan masukkan rincian kegiatan baru. Setiap kegiatan memiliki Target Waktu, Kendala, dan Status masing-masing.">
        <form action="{{ route('program-strategis.store') }}" method="POST" id="formTambah" class="grid grid-cols-1 gap-y-4 novalidate-form" novalidate>
            @csrf
            
            <div class="border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Pasangan Sasaran & Program Induk <span class="text-red-500">*</span></label>
                <select name="sasaran_program_select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none cursor-pointer bg-white">
                    <option value="" disabled selected>-- Pilih Dari Daftar yang Sudah Dibuat --</option>
                    @if(isset($listSasaranProgram))
                        @foreach($listSasaranProgram as $sp)
                            @php 
                                $nmBulan = ($sp->bulan && is_numeric($sp->bulan)) ? $bulanIndo[(int)$sp->bulan] : $sp->bulan; 
                            @endphp
                            <option value="{{ $sp->tahun }}|||{{ $sp->bulan }}|||{{ $sp->sasaran }}|||{{ $sp->program_strategis }}">{{ $sp->tahun }} {{ $nmBulan }} : {{ $sp->sasaran }} — {{ $sp->program_strategis }}</option>
                        @endforeach
                    @endif
                </select>
                    <span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>
                <p class="text-xs text-gray-400 mt-1">Kalau belum ada, bikin dulu lewat tombol "+ Sasaran & Program Induk" di luar ya!</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Program Kegiatan (Baru) <span class="text-red-500">*</span></label>
                <textarea name="deskripsi_kegiatan" id="add_kegiatan" rows="2" required placeholder="Jelaskan detail kegiatan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                    <span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none bg-white">
                        <option value="In Progress">In Progress</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Hold">Hold</option>
                    </select>
                    <span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Waktu</label>
                    <input type="date" name="target_waktu_start" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Realisasi (%)</label>
                    <input type="text" name="realisasi" placeholder="Misal: 93%" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Progress Saat Ini</label>
                    <textarea name="progress_saat_ini" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kendala</label>
                    <textarea name="kendala" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan Tambahan</label>
                    <input type="text" name="keterangan_tambahan" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                
                @if(isset($kolomDinamis) && count($kolomDinamis) > 0)
                    <div class="md:col-span-2 border-t border-gray-200 mt-2 pt-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-3">Kolom Tambahan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($kolomDinamis as $kol)
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $kol->nama_kolom }}</label>
                                    @if($kol->tipe_input == 'date')
                                        <input type="date" name="data_tambahan[{{ $kol->nama_kolom }}]" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm outline-none focus:border-orange-500">
                                    @elseif($kol->tipe_input == 'number')
                                        <input type="number" step="any" name="data_tambahan[{{ $kol->nama_kolom }}]" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm outline-none focus:border-orange-500">
                                    @else
                                        <input type="text" name="data_tambahan[{{ $kol->nama_kolom }}]" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm outline-none focus:border-orange-500">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-4 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalTambah')">Batal</x-button>
                <x-button variant="primary" type="submit">Simpan Kegiatan</x-button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL EDIT DATA -->
    <x-modal id="modalEdit" title="Edit Rincian Kegiatan" description="Perbarui detail kegiatan dan progress terkini.">
        <form id="formEdit" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 novalidate-form" novalidate>
            @csrf
            @method('PUT')
            
            <div class="md:col-span-2 border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pasangan Sasaran & Program Induk</label>
                <!-- Dibuat readonly pakai pointer-events-none agar tidak bisa diklik dan diubah -->
                <select name="sasaran_program_select" id="edit_sp_select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 outline-none cursor-not-allowed" style="pointer-events: none;" tabindex="-1" readonly>
                    <!-- Opsi akan dimasukkan via JS saat Edit diklik -->
                </select>
                    <span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Program Kegiatan</label>
                <textarea name="deskripsi_kegiatan" id="edit_deskripsi" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                <select name="status" id="edit_status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                    <option value="In Progress">In Progress</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Hold">Hold</option>
                </select>
                    <span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Waktu</label>
                <input type="date" name="target_waktu_start" id="edit_target_start" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
            </div>

            <div class="md:col-span-2 grid grid-cols-2 gap-x-6 gap-y-4 bg-gray-50 p-4 rounded-xl border border-gray-100 mt-2">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Realisasi (%)</label>
                    <input type="text" name="realisasi" id="edit_realisasi" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kendala</label>
                    <textarea name="kendala" id="edit_kendala" rows="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan Tambahan</label>
                    <input type="text" name="keterangan_tambahan" id="edit_keterangan" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Progress Saat Ini</label>
                    <textarea name="progress_saat_ini" id="edit_progress" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                </div>
                
                @if(isset($kolomDinamis) && count($kolomDinamis) > 0)
                    <div class="col-span-2 border-t border-gray-200 mt-2 pt-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-3">Kolom Tambahan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($kolomDinamis as $kol)
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $kol->nama_kolom }}</label>
                                    @if($kol->tipe_input == 'date')
                                        <input type="date" id="dt_{{ $kol->id }}" name="data_tambahan[{{ $kol->nama_kolom }}]" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm outline-none focus:border-orange-500">
                                    @elseif($kol->tipe_input == 'number')
                                        <input type="number" step="any" id="dt_{{ $kol->id }}" name="data_tambahan[{{ $kol->nama_kolom }}]" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm outline-none focus:border-orange-500">
                                    @else
                                        <input type="text" id="dt_{{ $kol->id }}" name="data_tambahan[{{ $kol->nama_kolom }}]" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm outline-none focus:border-orange-500">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 mt-2 border-t border-gray-100 pt-4">
                <x-button variant="outline" type="button" onclick="closeModal('modalEdit')">Batal</x-button>
                <x-button variant="primary" type="submit">Update Kegiatan</x-button>
            </div>
        </form>
    </x-modal>
</main>

<script>
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    
    function toggleDropdown(id) {
        const el = document.getElementById(id);
        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    }
    
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdownOpsiSuper');
        const button = dropdown ? dropdown.previousElementSibling : null;
        if (dropdown && !dropdown.classList.contains('hidden') && !dropdown.contains(event.target) && !button.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    function openModalTambahMaster() {
        document.getElementById('formTambahMaster').reset();
        const now = new Date();
        const monthStr = String(now.getMonth() + 1).padStart(2, '0');
        document.getElementById('add_master_periode').value = `${now.getFullYear()}-${monthStr}`;
        
        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500', 'bg-red-50'); el.classList.add('border-gray-300'); });
        openModal('modalTambahMaster');
    }

    function openModalTambah() {
        document.getElementById('formTambah').reset();
        
        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500', 'bg-red-50'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));

        openModal('modalTambah');
    }

    function editData(data) {
        const form = document.getElementById('formEdit');
        form.action = `/program-strategis/${data.id}`;

        const spSelect = document.getElementById('edit_sp_select');
        const spValue = `${data.tahun}|||${data.bulan}|||${data.sasaran}|||${data.program_strategis}`;
        
        let optionExists = false;
        for (let i = 0; i < spSelect.options.length; i++) {
            if (spSelect.options[i].value === spValue) {
                optionExists = true; break;
            }
        }
        
        if (!optionExists) {
            const newOption = document.createElement("option");
            
            const listBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const nBulan = data.bulan && !isNaN(data.bulan) ? listBulan[parseInt(data.bulan)] : data.bulan;
            
            newOption.text = `${data.tahun} ${nBulan} : ${data.sasaran} — ${data.program_strategis}`;
            newOption.value = spValue;
            spSelect.add(newOption);
        }
        spSelect.value = spValue;

        document.getElementById('edit_deskripsi').value = data.deskripsi_kegiatan || '';
        document.getElementById('edit_target_start').value = data.target_waktu_start || '';
        
        document.getElementById('edit_realisasi').value = data.realisasi || '';
        document.getElementById('edit_progress').value = data.progress_saat_ini || '';
        document.getElementById('edit_kendala').value = (data.kendala && data.kendala !== '-') ? data.kendala : '';
        document.getElementById('edit_keterangan').value = (data.keterangan_tambahan && data.keterangan_tambahan !== '-') ? data.keterangan_tambahan : '';
        document.getElementById('edit_status').value = data.status || '-';
        
        let tambahan = {};
        try {
            tambahan = (typeof data.data_tambahan === 'string') ? JSON.parse(data.data_tambahan) : (data.data_tambahan || {});
        } catch(e) {}
        
        @if(isset($kolomDinamis))
            @foreach($kolomDinamis as $kol)
                const inputEl_{{ $kol->id }} = document.getElementById('dt_{{ $kol->id }}');
                if(inputEl_{{ $kol->id }}) {
                    inputEl_{{ $kol->id }}.value = tambahan['{{ $kol->nama_kolom }}'] || '';
                }
            @endforeach
        @endif
        
        openModal('modalEdit');
    }

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

    let isBulkMode = false;

    function toggleBulkMode() {
        isBulkMode = !isBulkMode;
        const container = document.getElementById('tableContainerBulk');
        const btnGroup = document.getElementById('btnGroupBulk');
        if (container) {
            if (isBulkMode) {
                container.classList.remove('hide-bulk');
                if(btnGroup) {
                    btnGroup.classList.remove('hidden');
                    btnGroup.classList.add('flex');
                }
            } else {
                container.classList.add('hide-bulk');
                if(btnGroup) {
                    btnGroup.classList.add('hidden');
                    btnGroup.classList.remove('flex');
                }
                cancelAllBtnOnly();
            }
        }
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAllBulk');
        const checkboxes = document.querySelectorAll('.cb-bulk');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelectedCount();
    }

    function toggleCheckbox() {
        const selectAll = document.getElementById('selectAllBulk');
        const checkboxes = document.querySelectorAll('.cb-bulk');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        if(selectAll) selectAll.checked = allChecked;
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.cb-bulk:checked').length;
        const countEl = document.getElementById('selectedCount');
        if (countEl) countEl.textContent = count;
    }

    function cancelAllBtnOnly() {
        const selectAll = document.getElementById('selectAllBulk');
        if (selectAll) selectAll.checked = false;
        const checkboxes = document.querySelectorAll('.cb-bulk');
        checkboxes.forEach(cb => cb.checked = false);
        updateSelectedCount();
    }

    function cancelAll() {
        cancelAllBtnOnly();
        isBulkMode = false;
        const container = document.getElementById('tableContainerBulk');
        if (container) container.classList.add('hide-bulk');
        const btnGroup = document.getElementById('btnGroupBulk');
        if (btnGroup) {
            btnGroup.classList.add('hidden');
            btnGroup.classList.remove('flex');
        }
    }
</script>
@endsection