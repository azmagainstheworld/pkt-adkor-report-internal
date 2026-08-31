<?php
$file = 'resources/views/program-strategis.blade.php';
$content = file_get_contents($file);

// 1. Pindahkan tombol Tambah Master ke luar dropdown
$search1 = '<div class="flex gap-2">
                <!-- DROPDOWN OPSI LANJUTAN -->';
$replace1 = '<div class="flex gap-2">
                <x-button type="button" onclick="openModalTambahMaster()" class="!py-1.5 !px-3 text-xs bg-orange-500 hover:bg-orange-600 text-white border-none font-medium flex items-center gap-1 shadow-sm rounded-xl">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Sasaran & Program Induk
                </x-button>

                <!-- DROPDOWN OPSI LANJUTAN -->';
$content = str_replace($search1, $replace1, $content);

// 2. Hapus dari dalam dropdown
$search2 = '<button type="button" onclick="openModalTambahMaster()" class="w-full text-left px-4 py-2.5 text-xs text-gray-700 hover:bg-orange-50 hover:text-orange-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                + Sasaran & Program Induk
                            </button>';
$content = str_replace($search2, '', $content);

// 3. Perbaiki kolom aksi (hapus flex-col)
$search3 = '<td class="p-4 text-center">
                                <div class="flex flex-col gap-2 justify-start items-center">
                                    @php $rowDataJson = json_encode($row); @endphp
                                    <button type="button" onclick="editData({{ $rowDataJson }})" class="w-full p-1.5 flex justify-center text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="openDeleteModal(\'modalHapus\', \'{{ route(\'program-strategis.destroy\', $row->id) }}\')" class="w-full p-1.5 flex justify-center text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>';
$replace3 = '<td class="p-4 text-center w-28 whitespace-nowrap min-w-[100px]">
                                <div class="flex flex-row gap-2 justify-center items-center">
                                    @php $rowDataJson = json_encode($row); @endphp
                                    <button type="button" onclick="editData({{ $rowDataJson }})" class="p-1.5 flex justify-center text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="openDeleteModal(\'modalHapus\', \'{{ route(\'program-strategis.destroy\', $row->id) }}\')" class="p-1.5 flex justify-center text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>';
$content = str_replace($search3, $replace3, $content);

file_put_contents($file, $content);
echo "UI updated.\n";
