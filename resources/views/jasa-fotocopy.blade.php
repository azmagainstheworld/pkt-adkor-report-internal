@extends('layouts.app')

@section('content')
<style>th { white-space: nowrap !important; } td { font-size: 11px !important; text-align: center; }        .hide-bulk .cb-bulk, .hide-bulk #selectAllBulk { display: none !important; }
        .hide-bulk th:first-child, .hide-bulk td:first-child { padding: 0 !important; width: 0 !important; overflow: hidden; }
    </style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 min-w-0 min-h-0 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    <x-success-modal />

    @if (session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Peringatan Sistem</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">Tutup Peringatan</button>
        </div>
    </div>
    @endif

    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Jasa Fotocopy</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Penyediaan Fotocopy</h2>
            <p class="text-sm text-gray-500 font-medium">{{ $tanggalToday }}</p>
        </div>
        
        <form action="{{ route('administrasi.jasa-fotocopy') }}" method="GET" id="filterForm" class="flex items-center gap-3">
            <select name="tahun" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-blue-500">
                <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunTersedia as $thn)
                    <option value="{{ $thn }}" {{ $filterTahun == $thn ? 'selected' : '' }}>Tahun {{ $thn }}</option>
                @endforeach
            </select>
            <select name="bulan" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 shadow-sm cursor-pointer outline-none focus:border-blue-500">
                <option value="semua" {{ $filterBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                    <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>Bulan {{ $b }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= CHART SECTION ================= -->
    <div class="mb-8 h-96">
        <x-dynamic-chart title="Statistik Pemakaian Jasa Fotocopy" subtitle="Total pemakaian mesin per bulan" type="bar" id="fcStats" />
    </div>

    <!-- ================= TABEL 1: REKAP BACA SAJA ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-blue-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Tabel 1: Rekapitulasi Utama (Otomatis)</h3>
        </div>
        <div class="overflow-x-auto">
            <x-table :headers="['Tahun', 'Bulan', 'Mesin FC', 'Jumlah Pemakaian Jasa Penyediaan Fotocopy', 'Nilai Jasa Penyediaan Fotocopy']">
                @forelse($dataTable1 as $row)
                    <tr class="hover:bg-gray-50 transition-colors whitespace-nowrap">
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row['tahun'] }}</td>
                        <td class="px-4 py-3 text-gray-900 font-bold text-center">{{ $row['bulan'] }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center">{{ $row['mesin_fc'] }}</td>
                        <td class="px-4 py-3 text-blue-600 font-bold text-center">{{ number_format($row['jumlah_pemakaian'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-800 font-mono text-center font-bold">Rp{{ number_format($row['nilai_jasa'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
            </x-table>
            </div>
        </form>
    </x-card>

    <!-- ================= TABEL 2: DETAIL SPREADSHEET ================= -->
    <x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8">
        <div class="p-4 border-b border-gray-100 bg-blue-50/50 rounded-t-xl flex justify-between items-center gap-3">
            <!-- INI BAGIAN YANG TADI ERROR, SUDAH DIPERBAIKI SAYANG 👇 -->
            <h3 class="font-bold text-gray-800 text-sm uppercase">REKAPITULASI PEMAKAIAN MESIN FOTOCOPY BIAYA FEE & SEWA BULAN {{ strtoupper($filterBulan == 'semua' ? 'SEMUA BULAN' : $filterBulan) }} {{ strtoupper($filterTahun == 'semua' ? 'SEMUA TAHUN' : $filterTahun) }}</h3>
            <div class="flex gap-2">
                
                <div class="relative inline-block text-left overflow-visible">
                    <button type="button" onclick="toggleDropdown('dropdownOpsi')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-blue-200 shadow-sm px-4 py-1.5 bg-white text-xs font-medium text-blue-700 hover:bg-blue-100 focus:outline-none transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg> Opsi Lanjutan <svg class="w-3.5 h-3.5 ml-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="dropdownOpsi" class="hidden absolute right-0 z-[50] mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor & Impor</p></div>
                        <div class="py-1">
                            <button type="button" onclick="openModal('modalImportExcel'); toggleDropdown('dropdownOpsi')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Import dari Excel
                            </button>
                            <a href="{{ route('jasafotocopy.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium border-t border-gray-50">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Export Excel (Rekap)
                            </a>
                            <a href="{{ route('jasafotocopy.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan]) }}" target="_blank" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-red-50 flex items-center gap-2 font-medium border-t border-gray-50">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Export Laporan PDF
                            </a>
                        </div>
                        <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konfigurasi</p></div>
                        <div class="py-1">
                            @if(auth()->check() && auth()->user()->isAdmin())
<button type="button" onclick="openModal('modalAturKolom'); toggleDropdown('dropdownOpsi')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium border-t border-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Atur Kolom Tambahan
                            </button>
@endif
                        </div>
                    </div>
                </div>
                
                                <button type="button" id="btnModeBulk" onclick="toggleBulkMode()" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 border border-red-200 rounded-xl hover:bg-red-100 transition-colors shadow-sm outline-none focus:ring-2 focus:ring-red-300 mr-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus semua
                </button>
                <x-button variant="primary" onclick="openModalTambahBaris()" class="!py-1.5 !px-3 text-xs bg-blue-600 hover:bg-blue-700 border-none">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Data
                </x-button>
            </div>
        </div>

                <form id="bulkDeleteForm" action="{{ route('jasafotocopy.destroyBulk') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="deleteAllPages" name="delete_all_pages" value="0">
            <input type="hidden" name="tahun" value="{{ request('tahun') }}">
            <input type="hidden" name="bulan" value="{{ request('bulan') }}">
            
            <div id="btnGroupBulk" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

            <div id="tableContainerBulk" class="hide-bulk overflow-x-auto w-full max-w-full">
            @php
                $lblBln = ($filterBulan === 'semua') ? '(Bln Terkait)' : 'bln ' . $filterBulan;
                $lblSdBln = ($filterBulan === 'semua') ? '(s.d. Bln Terkait)' : 's.d. bln ' . $filterBulan;
                $lblBlnOnly = ($filterBulan === 'semua') ? 'Bln Terkait' : $filterBulan;
                
                $headers = ['<input type="checkbox" id="selectAllBulk" onclick="toggleSelectAll()">', 'NO', 'Tahun', 'Bulan', 'UNIT KERJA', 'Cost Centre', 'Jlh pemakaian ' . $lblBln, 'Jlh pemakaian ' . $lblSdBln, 'Ket.', 'Type mesin', 'Biaya fee bulan ' . $lblBlnOnly, 'Biaya fee s.d. bulan ' . $lblBlnOnly, 'Biaya fee/Lbr', 'Biaya sewa/bulan', 'Biaya Jasa Sewa bln Januari & Fee ' . $lblBlnOnly, 'Total biaya Sewa & Fee s.d. bln ' . $lblBlnOnly, 'Aksi'];
                
                if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { array_splice($headers, count($headers)-1, 0, $k->nama_kolom); } }
                $lastGroupKey = null;
            @endphp
            
            <x-table :headers="$headers">
                @forelse($dataTable2 as $index => $row)
                    @php
                        $tambahan = is_string($row['data_tambahan'] ?? '') ? json_decode($row['data_tambahan'], true) : ($row['data_tambahan'] ?? []);
                        $groupKey = $row['tahun'] . '|' . $row['bulan'];
                        $mulaiGrupBaru = $tampilkanSubtotalGrup && $lastGroupKey !== null && $lastGroupKey !== $groupKey;
                    @endphp

                    @if($mulaiGrupBaru)
                        @php $st = $subtotalGroups[$lastGroupKey]['totals']; @endphp
                        <tr class="font-semibold text-[11px] whitespace-nowrap bg-blue-50/70 border-t border-b border-blue-200">
                            <td colspan="6" class="px-3 py-2 text-right pr-4">Subtotal {{ $subtotalGroups[$lastGroupKey]['bulan'] }} {{ $subtotalGroups[$lastGroupKey]['tahun'] }} :</td>
                            <td class="px-3 py-2 bg-yellow-200 border-x border-gray-300 text-center">{{ number_format($st['pemakaian_bln'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-center">{{ number_format($st['pemakaian_sd'], 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                            <td class="px-3 py-2 font-mono text-center">{{ number_format($st['fee_bln'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 font-mono text-center">{{ number_format($st['fee_sd'], 0, ',', '.') }}</td>
                            <td></td>
                            <td class="px-3 py-2 font-mono text-center">{{ number_format($st['sewa_bln'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 font-mono text-center">{{ number_format($st['total_bln'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2 font-mono font-bold border-l border-gray-300 text-center">{{ number_format($st['total_sd'], 0, ',', '.') }}</td>
                            <td colspan="{{ isset($kolomDinamis) ? $kolomDinamis->count() + 1 : 1 }}"></td>
                        </tr>
                    @endif
                    @php $lastGroupKey = $groupKey; @endphp

                    <tr class="hover:bg-gray-50 whitespace-nowrap">
                        <td class="px-3 py-2 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk" value="{{ $row['id'] }}" onclick="toggleCheckbox()"></td>
                        <td class="px-3 py-2 font-medium">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 text-center text-gray-500">{{ $row['tahun'] }}</td>
                        <td class="px-3 py-2 text-center font-bold text-gray-700">{{ $row['bulan'] }}</td>
                        <td class="px-3 py-2 text-left font-medium">{{ $row['unit_kerja'] }}</td>
                        <td class="px-3 py-2">{{ $row['cost_centre'] }}</td>
                        <td class="px-3 py-2 bg-yellow-300 font-bold text-gray-900 border-x border-gray-300">{{ number_format($row['pemakaian_bln_ini'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2">{{ number_format($row['pemakaian_sd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2">{{ $row['keterangan'] }}</td>
                        <td class="px-3 py-2 font-mono">{{ $row['tipe_mesin'] }}</td>
                        <td class="px-3 py-2 font-mono">{{ number_format($row['fee_bln_ini'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 font-mono">{{ number_format($row['fee_sd'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 font-mono">{{ number_format($row['fee_per_lbr'], 2, ',', '.') }}</td>
                        <td class="px-3 py-2 font-mono">{{ number_format($row['sewa_bln_ini'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 font-mono font-medium">{{ number_format($row['total_bln_ini'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 font-mono font-bold text-gray-800 bg-gray-50 border-l border-gray-300">{{ number_format($row['total_sd'], 0, ',', '.') }}</td>
                        
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-3 py-2 border-l border-gray-100">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom])) Rp {{ $tambahan[$kolom->nama_kolom] }}
                                    @else {{ $tambahan[$kolom->nama_kolom] ?? '-' }} @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-3 py-2 text-center border-l border-gray-200">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editBarisSatuan('{{ $row['id'] }}', '{{ $row['unit_kerja'] }}', '{{ $row['cost_centre'] }}', '{{ $row['tipe_mesin'] }}', '{{ $row['keterangan'] }}', '{{ $row['pemakaian_bln_ini'] }}', '{{ $row['fee_per_lbr'] }}', '{{ $row['sewa_bln_ini'] }}', {{ json_encode($tambahan ?: new stdClass()) }})" class="p-1 text-amber-500 hover:bg-amber-50 rounded border border-amber-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                                <button type="button" onclick="openDeleteModal('modalHapus', '{{ route('jasafotocopy.destroy', $row['id']) }}')" class="p-1 text-red-500 hover:bg-red-50 rounded border border-red-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="20" class="px-6 py-10 text-center text-gray-500 font-medium">Data di periode ini kosong. Silakan tambah data.</td></tr>
                @endforelse

                @if(count($dataTable2) > 0 && $tampilkanSubtotalGrup && $lastGroupKey !== null)
                    @php $st = $subtotalGroups[$lastGroupKey]['totals'] ?? null; @endphp
                    @if($st)
                    <tr class="font-semibold text-[11px] whitespace-nowrap bg-blue-50/70 border-t border-b border-blue-200">
                        <td colspan="6" class="px-3 py-2 text-right pr-4">Subtotal {{ $subtotalGroups[$lastGroupKey]['bulan'] }} {{ $subtotalGroups[$lastGroupKey]['tahun'] }} :</td>
                        <td class="px-3 py-2 bg-yellow-200 border-x border-gray-300 text-center">{{ number_format($st['pemakaian_bln'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-center">{{ number_format($st['pemakaian_sd'], 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                        <td class="px-3 py-2 font-mono text-center">{{ number_format($st['fee_bln'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 font-mono text-center">{{ number_format($st['fee_sd'], 0, ',', '.') }}</td>
                        <td></td>
                        <td class="px-3 py-2 font-mono text-center">{{ number_format($st['sewa_bln'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 font-mono text-center">{{ number_format($st['total_bln'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 font-mono font-bold border-l border-gray-300 text-center">{{ number_format($st['total_sd'], 0, ',', '.') }}</td>
                        <td colspan="{{ isset($kolomDinamis) ? $kolomDinamis->count() + 1 : 1 }}"></td>
                    </tr>
                    @endif
                @endif

                @if(count($dataTable2) > 0)
                    <tr class="font-bold text-xs whitespace-nowrap border-t border-b border-gray-800 bg-white">
                        <td colspan="5" class="px-3 py-3 uppercase text-center border-r border-gray-800">GRAND TOTAL {{ $tampilkanSubtotalGrup ? '(SEMUA GRUP)' : '' }}</td>
                        <td class="px-3 py-3 font-bold border-r border-gray-800 bg-yellow-200">{{ number_format($grandTotals['pemakaian_bln'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 font-bold border-r border-gray-800">{{ number_format($grandTotals['pemakaian_sd'], 0, ',', '.') }}</td>
                        <td colspan="2" class="border-r border-gray-800"></td>
                        <td class="px-3 py-3 font-mono border-r border-gray-800">{{ number_format($grandTotals['fee_bln'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 font-mono border-r border-gray-800">{{ number_format($grandTotals['fee_sd'], 0, ',', '.') }}</td>
                        <td class="border-r border-gray-800"></td>
                        <td class="px-3 py-3 font-mono border-r border-gray-800">{{ number_format($grandTotals['sewa_bln'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 font-mono text-red-600 border-r border-gray-800">{{ number_format($grandTotals['total_bln'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 font-mono font-bold text-red-600">{{ number_format($grandTotals['total_sd'], 0, ',', '.') }}</td>
                        @if(isset($kolomDinamis)) <td colspan="{{ count($kolomDinamis) + 1 }}"></td> @else <td></td> @endif
                    </tr>
                @endif
            </x-table>
        </div>
    </x-card>

    <x-delete-modal id="modalHapus" title="Hapus Data Baris Ini" message="Data akan dihapus permanen. Lanjutkan?" />
    <x-delete-modal id="modalHapusKolom" title="Hapus Kolom Tambahan" message="Kolom ini akan dihilangkan dari tabel dan formulir. Lanjutkan?" />

    <!-- ================= MODAL ATUR KOLOM DINAMIS ================= -->
    @if(auth()->user()->isAdmin())
<x-modal id="modalAturKolom" title="Pengaturan Kolom Tambahan" description="Kelola kolom ekstra khusus untuk tabel Jasa Fotocopy.">
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 max-h-48 overflow-y-auto">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar:</h4>
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div><p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p><p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span></p></div>
                        <button type="button" onclick="triggerDeleteKolom('{{ route('jasafotocopy.kolom.destroy', $kolom->id) }}')" class="text-red-500 p-1 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p> @endif
        </div>
        <form action="{{ route('jasafotocopy.kolom.store') }}" method="POST" class="border-t pt-4">
            @csrf <input type="hidden" name="modul" value="jasa_fotocopy">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option><option value="number">Angka Kuantitas Biasa</option><option value="currency">Harga / Uang (Rp)</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Selesai, Pending" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><x-button variant="outline" type="button" onclick="closeModal('modalAturKolom')">Tutup</x-button><x-button variant="primary" type="submit" class="bg-blue-600 hover:bg-blue-700 border-none">Simpan</x-button></div>
        </form>
    </x-modal>
@endif

    <!-- ================= MODAL IMPORT EXCEL ================= -->
    <x-import-modal id="modalImportExcel" route="{{ route('jasafotocopy.import') }}" title="Import Data Jasa Fotocopy" templateRoute="{{ route('template.download', 'jasa-fotocopy') }}" />

    <!-- MODAL TAMBAH TRANSAKSI -->
    <x-modal id="modalTambahData" title="Tambah Data Transaksi" description="Isi data mesin dan pemakaian untuk bulan tertentu.">
        <form action="{{ route('jasafotocopy.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-3 mb-4 pb-4 border-b border-gray-100">
                <div class="col-span-2">
                    <label class="block text-[10px] font-semibold text-gray-600 mb-1">Bulan & Tahun <span class="text-red-500">*</span></label>
                    <input type="month" required id="picker_tambah" class="w-full px-3 py-1.5 border rounded-md text-xs outline-none focus:border-blue-500" onchange="syncPeriode(this.value, 'add_tahun', 'add_bulan')">
                    <input type="hidden" name="tahun" id="add_tahun"><input type="hidden" name="bulan" id="add_bulan">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 pb-4 border-b border-gray-100">
                <div class="col-span-2"><label class="block text-[10px] font-semibold text-gray-600 mb-1">Unit Kerja *</label><input type="text" name="unit_kerja" required class="w-full px-2 py-1.5 border rounded text-xs" placeholder="Misal: Direksi"></div>
                <div class="col-span-1"><label class="block text-[10px] font-semibold text-gray-600 mb-1">Cost Centre</label><input type="text" name="cost_centre" class="w-full px-2 py-1.5 border rounded text-xs" placeholder="D0010000"></div>
                <div class="col-span-1"><label class="block text-[10px] font-semibold text-gray-600 mb-1">Tipe Mesin</label><input type="text" name="tipe_mesin" class="w-full px-2 py-1.5 border rounded text-xs" placeholder="Bizhub-350"></div>
                <div class="col-span-2"><label class="block text-[10px] font-semibold text-gray-600 mb-1">Keterangan</label><input type="text" name="keterangan" value="KOPKAR" class="w-full px-2 py-1.5 border rounded text-xs"></div>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-4 pb-4 border-b border-gray-100">
                <div><label class="block text-[10px] font-semibold text-gray-600 mb-1">Jml Lembar *</label><input type="number" name="pemakaian" required class="w-full px-2 py-1.5 border rounded text-xs"></div>
                <div><label class="block text-[10px] font-semibold text-gray-600 mb-1">Fee/Lbr</label><input type="number" step="0.01" name="fee" value="47.22" class="w-full px-2 py-1.5 border rounded text-xs bg-white"></div>
                <div><label class="block text-[10px] font-semibold text-gray-600 mb-1">Sewa/Bulan</label><input type="number" name="sewa" value="909000" class="w-full px-2 py-1.5 border rounded text-xs bg-white"></div>
            </div>

            @if($kolomDinamis->count() > 0)
            <div class="mb-4">
                <p class="text-xs font-bold text-gray-700 mb-2">Data Tambahan</p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($kolomDinamis as $kolom)
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-600 mb-1">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-2 py-1.5 border rounded text-xs">
                                    <option value="">Pilih...</option>
                                    @foreach(json_decode($kolom->pilihan_dropdown ?? '[]', true) ?? [] as $opsi)<option value="{{ $opsi }}">{{ $opsi }}</option>@endforeach
                                </select>
                            @elseif($kolom->tipe_input === 'date') <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-2 py-1.5 border rounded text-xs">
                            @elseif(in_array($kolom->tipe_input, ['number', 'currency'])) <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-2 py-1.5 border rounded text-xs">
                            @else <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-2 py-1.5 border rounded text-xs"> @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4"><x-button variant="outline" type="button" onclick="closeModal('modalTambahData')">Batal</x-button><x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Simpan Data</x-button></div>
        </form>
    </x-modal>

    <!-- MODAL EDIT SATUAN -->
    <x-modal id="modalEditSatuan" title="Edit Data Transaksi" description="Perbarui detail data baris ini.">
        <form action="" method="POST" id="formEditSatuan">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3 mb-4 pb-4 border-b border-gray-100">
                <div class="col-span-2"><label class="block text-[10px] font-semibold text-gray-600 mb-1">Unit Kerja *</label><input type="text" name="unit_kerja" id="e_unit" required class="w-full px-2 py-1.5 border rounded text-xs"></div>
                <div class="col-span-1"><label class="block text-[10px] font-semibold text-gray-600 mb-1">Cost Centre</label><input type="text" name="cost_centre" id="e_cost" class="w-full px-2 py-1.5 border rounded text-xs"></div>
                <div class="col-span-1"><label class="block text-[10px] font-semibold text-gray-600 mb-1">Tipe Mesin</label><input type="text" name="tipe_mesin" id="e_tipe" class="w-full px-2 py-1.5 border rounded text-xs"></div>
                <div class="col-span-2"><label class="block text-[10px] font-semibold text-gray-600 mb-1">Keterangan</label><input type="text" name="keterangan" id="e_ket" class="w-full px-2 py-1.5 border rounded text-xs"></div>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-4 pb-4 border-b border-gray-100">
                <div><label class="block text-[10px] font-semibold text-gray-600 mb-1">Jml Lembar *</label><input type="number" name="pemakaian" id="e_pemakaian" required class="w-full px-2 py-1.5 border rounded text-xs"></div>
                <div><label class="block text-[10px] font-semibold text-gray-600 mb-1">Fee/Lbr</label><input type="number" step="0.01" name="fee" id="e_fee" class="w-full px-2 py-1.5 border rounded text-xs"></div>
                <div><label class="block text-[10px] font-semibold text-gray-600 mb-1">Sewa/Bulan</label><input type="number" name="sewa" id="e_sewa" class="w-full px-2 py-1.5 border rounded text-xs"></div>
            </div>

            @if($kolomDinamis->count() > 0)
            <div class="mb-4">
                <p class="text-xs font-bold text-gray-700 mb-2">Data Tambahan</p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($kolomDinamis as $kolom)
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-600 mb-1">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" data-kolom="{{ $kolom->nama_kolom }}" class="w-full px-2 py-1.5 border rounded text-xs">
                                    <option value="">-- Pilih {{ $kolom->nama_kolom }} --</option>
                                    @foreach(json_decode($kolom->pilihan_dropdown ?? '[]', true) ?? [] as $opsi)<option value="{{ $opsi }}">{{ $opsi }}</option>@endforeach
                                </select>
                            @elseif($kolom->tipe_input === 'date') <input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-kolom="{{ $kolom->nama_kolom }}" class="w-full px-2 py-1.5 border rounded text-xs">
                            @elseif(in_array($kolom->tipe_input, ['number', 'currency'])) <input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-kolom="{{ $kolom->nama_kolom }}" class="w-full px-2 py-1.5 border rounded text-xs">
                            @else <input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" data-kolom="{{ $kolom->nama_kolom }}" class="w-full px-2 py-1.5 border rounded text-xs"> @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="flex justify-end gap-2 mt-4 border-t border-gray-100 pt-4"><x-button variant="outline" type="button" onclick="closeModal('modalEditSatuan')">Batal</x-button><x-button variant="primary" type="submit" class="bg-blue-600 border-none text-xs">Update Data</x-button></div>
        </form>
    </x-modal>
</main>

    <script>
        function toggleBulkMode() {
            let container = document.getElementById("tableContainerBulk");
            let btn = document.getElementById("btnModeBulk");
            if (container.classList.contains("hide-bulk")) {
                container.classList.remove("hide-bulk");
                if(btn) { btn.classList.replace("bg-red-50", "bg-red-600"); btn.classList.replace("text-red-600", "text-white"); }
            } else {
                container.classList.add("hide-bulk");
                cancelAll();
                if(btn) { btn.classList.replace("bg-red-600", "bg-red-50"); btn.classList.replace("text-white", "text-red-600"); }
            }
        }
        function toggleSelectAll() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleDeleteBtn();
        }
        function toggleCheckbox() {
            let selectAll = document.getElementById("selectAllBulk");
            let checkboxes = document.querySelectorAll(".cb-bulk");
            selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            toggleDeleteBtn();
        }
        function toggleDeleteBtn() {
            let group = document.getElementById("btnGroupBulk");
            if (group) {
                let checked = document.querySelectorAll(".cb-bulk:checked").length > 0;
                if (checked) { group.classList.remove("hidden"); } 
                else { group.classList.add("hidden"); }
            }
        }
        function cancelAll() {
            let selectAll = document.getElementById("selectAllBulk");
            if (selectAll) selectAll.checked = false;
            let checkboxes = document.querySelectorAll(".cb-bulk");
            checkboxes.forEach(cb => cb.checked = false);
            toggleDeleteBtn();
        }

    const rawChartData = {!! json_encode($chartJsonData ?? []) !!}; let chartInstance = null;
    function initChart(chartId, chartType) {
        if(!rawChartData || !rawChartData.labels) return; const ctx = document.getElementById('canvas_' + chartId);
        if (!ctx) return; if (chartInstance) { chartInstance.destroy(); }
        chartInstance = new Chart(ctx, { type: chartType, data: { labels: rawChartData.labels, datasets: rawChartData.datasets }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', } }, scales: { y: { beginAtZero: true, display: true }, x: { display: true } } } });
    }
    document.addEventListener("DOMContentLoaded", function() { initChart('fcStats', 'bar'); });

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    function toggleDropdown(id) { document.getElementById(id).classList.toggle('hidden'); }
    document.addEventListener('click', function(event) { if (!event.target.closest('.relative.inline-block')) { document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden')); } });

    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function syncPeriode(val, yearId, monthId) {
        if(val) { const parts = val.split('-'); document.getElementById(yearId).value = parts[0]; document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1]; }
    }

    function openModalTambahBaris() {
        const now = new Date(); const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah').value = currentMonth; syncPeriode(currentMonth, 'add_tahun', 'add_bulan');
        openModal('modalTambahData');
    }

    function editBarisSatuan(id, unit, cost, tipe, ket, pemakaian, fee, sewa, tambahan) {
        document.getElementById('formEditSatuan').action = `/administrasi/jasa-fotocopy/${id}`;
        document.getElementById('e_unit').value = unit; document.getElementById('e_cost').value = cost;
        document.getElementById('e_tipe').value = tipe; document.getElementById('e_ket').value = ket;
        document.getElementById('e_pemakaian').value = pemakaian; document.getElementById('e_fee').value = fee; document.getElementById('e_sewa').value = sewa;
        
        tambahan = tambahan || {};
        document.querySelectorAll('#modalEditSatuan [data-kolom]').forEach(function(el) {
            const key = el.getAttribute('data-kolom');
            el.value = (tambahan[key] !== undefined && tambahan[key] !== null) ? tambahan[key] : '';
        });
        openModal('modalEditSatuan');
    }
    function triggerDeleteKolom(url) { closeModal('modalAturKolom'); setTimeout(() => openDeleteModal('modalHapusKolom', url), 200); }
    function toggleDropdownConfig() {
        const selector = document.getElementById('tipeInputSelector');
        const configArea = document.getElementById('dropdownConfigArea');
        if(selector.value === 'dropdown') { configArea.classList.remove('hidden'); configArea.querySelector('input').setAttribute('required', 'true'); } 
        else { configArea.classList.add('hidden'); configArea.querySelector('input').removeAttribute('required'); }
    }
</script>
@endsection
