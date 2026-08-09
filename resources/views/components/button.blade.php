@props([
    'variant' => 'primary', // Pilihan: primary, secondary, outline, outline-orange, light
    'href' => null,
])

@php
    // Base styling yang selalu ada di setiap tombol
    $baseClasses = 'inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm';
    
    // Variasi warna berdasarkan props 'variant'
    $variantClasses = match($variant) {
        'primary' => 'bg-pkt-jingga hover:bg-orange-600 text-white', // Oranye
        'secondary' => 'bg-pkt-biru hover:bg-blue-800 text-white', // Biru
        'outline' => 'border border-gray-300 text-gray-700 bg-white hover:bg-gray-50', // Outline Abu
        'outline-orange' => 'border border-orange-500 text-orange-500 bg-white hover:bg-orange-50', // Outline Oranye
        'light' => 'bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-100 shadow-none', // Biru Muda
        default => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
    };

    $classes = $baseClasses . ' ' . $variantClasses;
@endphp

@if($href)
    <!-- Akan dirender sebagai Link <a> jika ada href -->
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <!-- Akan dirender sebagai Button biasa jika tidak ada href -->
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif