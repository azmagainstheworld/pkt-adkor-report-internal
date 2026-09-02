<?php
$c = file_get_contents('resources/views/undangan.blade.php');

$search = <<<HTML
                @php \$safeDetails = \$detailsData ?? []; @endphp
                @forelse(\$safeDetails as \$index => \$detail)
                    <tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-100 last:border-0">
                        <td class="px-6 py-4 text-center"><input type="checkbox" name="ids[]" class="cb-detail" value="{{ \$detail->id }}" onclick="toggleCheckbox('detail')"></td>
                        <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">{{ \$index + 1 }}</td>
                        <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">{{ \$detail->tahun }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium whitespace-nowrap">{{ \$detail->bulan }}</td>
                        <td class="px-6 py-4 text-gray-700">
                            @if(\$detail->jenis_undangan == 'Internal')
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-semibold border border-blue-100">Internal</span>
                            @else
                                <span class="px-2.5 py-1 bg-orange-50 text-orange-700 rounded-md text-xs font-semibold border border-orange-100">Eksternal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ \$detail->agenda ?? '-' }}</td>
                        
                        <td class="px-6 py-4 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="editDataDetail({{ json_encode(\$detail) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('undangan.detail.destroy', \$detail->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus rincian ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">Tidak ada rincian undangan.</td></tr>
                @endforelse
HTML;

$replace = <<<HTML
                @if(empty(\$detailsData) || count(\$detailsData) === 0)
                    <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">Tidak ada rincian undangan.</td></tr>
                @else
                    @foreach(\$detailsData as \$index => \$detail)
                        <tr class="hover:bg-gray-50 transition-colors text-sm border-b border-gray-100 last:border-0">
                            <td class="px-6 py-4 text-center"><input type="checkbox" name="ids[]" class="cb-detail" value="{{ \$detail->id }}" onclick="toggleCheckbox('detail')"></td>
                            <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">{{ \$index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">{{ \$detail->tahun }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium whitespace-nowrap">{{ \$detail->bulan }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                @if(\$detail->jenis_undangan == 'Internal')
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-semibold border border-blue-100">Internal</span>
                                @else
                                    <span class="px-2.5 py-1 bg-orange-50 text-orange-700 rounded-md text-xs font-semibold border border-orange-100">Eksternal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ \$detail->agenda ?? '-' }}</td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex gap-2 justify-center">
                                    <button type="button" onclick="editDataDetail({{ json_encode(\$detail) }})" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md border border-amber-200" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('undangan.detail.destroy', \$detail->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus rincian ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md border border-red-200" title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
HTML;

$c = str_replace($search, $replace, $c);
file_put_contents('resources/views/undangan.blade.php', $c);
echo "Replaced @forelse with @if @foreach\n";
?>
