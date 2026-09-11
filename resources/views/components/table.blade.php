@props(['headers'])

<div class="overflow-x-auto">
    <table class="w-full border-collapse"> 
        <thead>
            <tr class="bg-blue-50/50">
                @foreach($headers as $header)
                    <!-- Logika penyesuaian lebar khusus untuk kolom 'No' dan 'Aksi' -->
                    @php
                        $widthClass = '';
                        $bulkClass = strpos($header, 'type="checkbox"') !== false || strpos($header, "type='checkbox'") !== false ? 'bulk-checkbox-col' : '';
                        if ($header === 'No') {
                            $widthClass = 'w-16'; // Lebar khusus untuk No agar sempit
                        } elseif ($header === 'Aksi') {
                            $widthClass = 'w-32'; // Lebar khusus untuk Aksi
                        }
                    @endphp

                    <th class="px-6 py-4 text-xs font-bold text-blue-700 border-b border-gray-100 text-center {{ $widthClass }} {{ $bulkClass }}">
                        {!! $header !!}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-sm">
            {{ $slot }}
        </tbody>
    </table>
</div>
