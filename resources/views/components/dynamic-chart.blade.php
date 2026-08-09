@props(['title', 'subtitle' => '', 'type' => 'pie', 'id'])

<!-- Tambahkan h-full agar kotak sama tinggi dalam grid -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col h-full">
    <!-- Header Kartu & Customizer Tipe Grafik -->
    <div class="flex items-start justify-between mb-4 flex-shrink-0">
        <div>
            <h3 class="font-bold text-gray-900 mb-1">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-xs text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>
        
        <!-- Pilihan Tipe Grafik Dinamis - Menambahkan ID unik -->
        <select id="select_{{ $id }}" onchange="changeChartType('{{ $id }}', this.value)" class="text-xs px-2.5 py-1.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm font-medium">
            <option value="pie" {{ $type == 'pie' ? 'selected' : '' }}>Pie Chart</option>
            <option value="bar" {{ $type == 'bar' ? 'selected' : '' }}>Bar Chart</option>
            <option value="doughnut" {{ $type == 'doughnut' ? 'selected' : '' }}>Doughnut</option>
        </select>
    </div>
    
    <!-- Isi Grafik Sebenarnya (Canvas untuk Chart.js) -->
    <!-- flex-grow agar canvas mengambil sisa ruang kotak -->
    <div class="relative h-[300px] w-full"> 
        <canvas id="canvas_{{ $id }}"></canvas>
    </div>
</div>