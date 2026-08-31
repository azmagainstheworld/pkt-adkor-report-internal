@props([
    'exportExcelRoute' => null,
    'exportPdfRoute' => null,
    'importModalId' => 'modalImportExcel'
])

<div class="flex flex-wrap items-center gap-2">
    <!-- Tombol Import Excel (Membuka Modal) -->
    <x-button variant="outline" onclick="openModal('{{ $importModalId }}')" class="!py-1.5 !px-3 text-xs bg-white border-green-600 text-green-700 hover:bg-green-50 flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Import Excel
    </x-button>

    <!-- Tombol Export Excel -->
    @if($exportExcelRoute)
    <a href="{{ $exportExcelRoute }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors shadow-sm border border-transparent">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Export Excel
    </a>
    @endif

    <!-- Tombol Export PDF -->
    @if($exportPdfRoute)
    <a href="{{ $exportPdfRoute }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm border border-transparent">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
        Export PDF
    </a>
    @endif
</div>
