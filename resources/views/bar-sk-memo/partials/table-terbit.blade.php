<!-- ================= TABEL 2: DOKUMEN TERBIT (NO 2) ================= -->
<x-card class="!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white relative">
    <div class="p-5 border-b border-gray-100 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl z-[30] relative">
        <h3 class="font-bold text-gray-900 text-base uppercase">TABEL BAR SK MEMO TERBIT</h3>
        
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" id="btnModeBulkTerbit" onclick="toggleBulkMode('terbit')" class="inline-flex justify-center items-center gap-1.5 rounded-xl border border-red-300 shadow-sm px-3 py-1.5 bg-white text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Mode Hapus Massal
            </button>
            
            <div class="relative inline-block text-left overflow-visible z-[50]">
                <button type="button" onclick="toggleDropdown('dropdownOpsiTerbit')" class="inline-flex justify-center items-center gap-1.5 rounded-xl border border-gray-300 shadow-sm px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Opsi Lanjutan
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div id="dropdownOpsiTerbit" class="hidden absolute right-0 z-[50] mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data Excel</p>
                    </div>
                    <div class="py-1">
                        <button type="button" onclick="openModal('modalImportTerbit'); toggleDropdown('dropdownOpsiTerbit')" class="w-full text-left text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import Data Massal
                        </button>
                        <a href="{{ route('bar-sk-memo.export.excel', ['tahun' => $filterTahun, 'bulan' => $filterBulan, 'tipe' => 'terbit']) }}" class="text-gray-700 px-4 py-2 text-xs hover:bg-green-50 flex items-center gap-2 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel Terbit
                        </a>
                    </div>
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data PDF</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('bar-sk-memo.export.pdf', ['tahun' => $filterTahun, 'bulan' => $filterBulan, 'tipe' => 'terbit']) }}" target="_blank" class="text-gray-700 px-4 py-2 text-xs hover:bg-red-50 flex items-center gap-2 font-medium transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export PDF Terbit
                        </a>
                    </div>
                    <div class="px-4 py-2 bg-gray-50 border-y border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                    </div>
                    <div class="py-1">
                        <button type="button" onclick="openModal('modalAturKolomTerbit'); toggleDropdown('dropdownOpsiTerbit')" class="text-gray-700 w-full text-left px-4 py-2 text-xs hover:bg-gray-50 flex items-center gap-2 font-medium transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Atur Kolom Tabel Terbit
                        </button>
                    </div>
                </div>
            </div>

            <x-button variant="primary" onclick="openModalTambah('Terbit')" class="!py-1.5 !px-3 text-xs flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Data Terbit</x-button>
        </div>
    </div>

    <form id="bulkDeleteTerbitForm" action="{{ route('bar-sk-memo.destroyTerbit') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
        @csrf @method('DELETE')
        
        <div id="btnGroupTerbit" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
            <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
            <div class="flex gap-2">
                <button type="button" onclick="cancelAll('terbit')" class="px-3 py-1 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition-colors">Batal</button>
                <button type="submit" class="px-3 py-1 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors">Hapus Terpilih</button>
            </div>
        </div>

        <div id="tableContainerTerbit" class="hide-bulk-terbit overflow-x-auto">
            @php
                $headTerbit = ["<input type=\"checkbox\" id=\"selectAllTerbit\" class=\"w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500\" onclick=\"toggleSelectAll('terbit')\">", 'Tahun', 'Bulan', 'SKD Keputusan Bersama', 'SKD Non Ratifikasi', 'SKD Ratifikasi', 'Memo Direksi', 'BAR Monitoring', 'BAR Manajemen'];
                if(isset($kolomDinamisTerbit)) { foreach($kolomDinamisTerbit as $k) { $headTerbit[] = $k->nama_kolom; } }
                $headTerbit[] = 'Aksi';
            @endphp

            <x-table :headers="$headTerbit">
                @forelse($dataTerbit as $row)
                    @php $tambahanTerbit = is_string($row->data_tambahan ?? null) ? json_decode($row->data_tambahan ?? '', true) : ($row->data_tambahan ?? []); @endphp
                    <tr class="hover:bg-gray-50 transition-colors text-xs whitespace-nowrap">
                        <td class="px-4 py-3 text-center"><input type="checkbox" name="ids[]" class="cb-terbit w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" value="{{ $row->id }}" onclick="toggleCheckbox('terbit')"></td>
                        <td class="px-4 py-3 text-gray-700 font-medium text-center">{{ $row->tahun }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium text-center">{{ $row->bulan }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->skd_keputusan_bersama_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->skd_non_ratifikasi_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->skd_ratifikasi_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->memo_direksi_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->bar_monitoring_terbit }}</td>
                        <td class="px-4 py-3 text-gray-600 text-center font-bold">{{ $row->bar_manajemen_terbit }}</td>
                        
                        @if(isset($kolomDinamisTerbit))
                            @foreach($kolomDinamisTerbit as $kolom)
                                <td class="px-4 py-3 text-center text-gray-600">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahanTerbit[$kolom->nama_kolom]))
                                        Rp {{ $tambahanTerbit[$kolom->nama_kolom] }}
                                    @else
                                        {{ $tambahanTerbit[$kolom->nama_kolom] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        @endif

                        <td class="px-4 py-3 text-center border-l border-gray-100">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editTerbit({{ json_encode($row) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('bar-sk-memo.destroyTerbit') }}" method="POST" class="inline">
                                    @csrf @method('DELETE') <input type="hidden" name="id" value="{{ $row->id }}">
                                    <button type="submit" onclick="return confirm('Hapus baris terbit di bulan ini?')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headTerbit) + 1 }}" class="px-6 py-10 text-center text-gray-500 text-sm">Data kosong.</td></tr>
                @endforelse
                
                @if(count($dataTerbit) > 0)
                    <tr class="bg-gray-100 text-gray-800 font-bold text-xs whitespace-nowrap border-t border-gray-200">
                        <td></td><td colspan="2" class="px-4 py-3 text-right uppercase border-r border-gray-300">Total Keseluruhan</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['skd_kb'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['skd_nr'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['skd_r'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['memo'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['bar_mon'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $grandTotalTerbit['bar_man'] }}</td>
                        @if(isset($kolomDinamisTerbit)) @foreach($kolomDinamisTerbit as $k) <td></td> @endforeach @endif
                        <td></td>
                    </tr>
                @endif
            </x-table>
        </div>
    </form>

    <div class="p-4 border-t border-gray-100">
        {{ $dataTerbit->links() }}
    </div>
</x-card>
