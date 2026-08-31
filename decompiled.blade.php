

@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <?php if (isset($component)) { $__componentOriginal6475feafa5c7d85d71efc5a48adb5766 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6475feafa5c7d85d71efc5a48adb5766 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.success-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('success-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal6475feafa5c7d85d71efc5a48adb5766)): ?>
<?php $attributes = $__attributesOriginal6475feafa5c7d85d71efc5a48adb5766; ?>
<?php unset($__attributesOriginal6475feafa5c7d85d71efc5a48adb5766); ?>
@endif
<?php if (isset($__componentOriginal6475feafa5c7d85d71efc5a48adb5766)): ?>
<?php $component = $__componentOriginal6475feafa5c7d85d71efc5a48adb5766; ?>
<?php unset($__componentOriginal6475feafa5c7d85d71efc5a48adb5766); ?>
@endif

    <!-- ================= MODAL ERROR KUSTOM ================= -->
    @if(session('error_modal'))
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Penambahan Gagal</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error_modal') }}</p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none'; openModal('modalAturKolom');" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Kembali & Perbaiki
            </button>
        </div>
    </div>
    @endif

    <div class="flex justify-between items-end mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Pengiriman Dokumen</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Pengiriman Dalam Negeri dan Luar Negeri</h2>
            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
        
        <form action="{{ route('pengiriman-dokumen.index') }}" method="GET" class="flex items-center gap-3">
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="semua" {{ $selectedYear == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <select name="month" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:border-blue-500 shadow-sm cursor-pointer transition-colors">
                <option value="semua" {{ $selectedMonth == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                <?php $__currentLoopData = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="{{ $monthName }}" {{ $selectedMonth == $monthName ? 'selected' : '' }}>{{ $monthName }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- ================= VISUALISASI 1 ================= -->
    <div class="mb-8"> <!-- DIBERIKAN MARGIN BAWAH AGAR TIDAK NABRAK -->
        <?php if (isset($component)) { $__componentOriginal8213acddf1ec7aafb42f5a9a66fba60a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8213acddf1ec7aafb42f5a9a66fba60a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dynamic-chart','data' => ['title' => 'Statistik Volume Pengiriman & Mailroom','subtitle' => 'Rincian Pengiriman Dokumen pada filter periode laporan.','type' => 'bar','id' => 'volumeVolumeChart']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-chart'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['title' => 'Statistik Volume Pengiriman & Mailroom','subtitle' => 'Rincian Pengiriman Dokumen pada filter periode laporan.','type' => 'bar','id' => 'volumeVolumeChart']); ?>
<?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal8213acddf1ec7aafb42f5a9a66fba60a)): ?>
<?php $attributes = $__attributesOriginal8213acddf1ec7aafb42f5a9a66fba60a; ?>
<?php unset($__attributesOriginal8213acddf1ec7aafb42f5a9a66fba60a); ?>
@endif
<?php if (isset($__componentOriginal8213acddf1ec7aafb42f5a9a66fba60a)): ?>
<?php $component = $__componentOriginal8213acddf1ec7aafb42f5a9a66fba60a; ?>
<?php unset($__componentOriginal8213acddf1ec7aafb42f5a9a66fba60a); ?>
@endif
    </div>

    <!-- ================= ACTION BAR MINIMALIS ================= -->
    <div class="flex justify-end items-center mb-5 flex-wrap gap-4">
        
        <div class="relative inline-block text-left">
            <button type="button" onclick="toggleActionDropdown('dropdownOpsiSuperPengiriman')" class="inline-flex justify-center items-center gap-2 w-full rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Opsi Lanjutan
                <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div id="dropdownOpsiSuperPengiriman" class="hidden absolute right-0 z-[50] mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Kelola Data</p>
                </div>
                <div class="py-1" role="none">
                    <button type="button" onclick="openModal('modalImportPengiriman'); toggleActionDropdown('dropdownOpsiSuperPengiriman')" class="w-full text-left text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Excel
                    </button>
                    <a href="{{ route('pengiriman-dokumen.export.excel', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <a href="{{ route('pengiriman-dokumen.export.pdf', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" target="_blank" class="text-gray-700 px-4 py-2.5 text-sm hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export PDF
                    </a>
                </div>

                <div class="px-4 py-2 bg-gray-50 border-y border-gray-100 mt-1">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tampilan Teks</p>
                </div>
                <div class="py-1" role="none">
                    <button type="button" onclick="openModal('modalAturKolom'); toggleActionDropdown('dropdownOpsiSuperPengiriman')" class="text-gray-700 w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2.5 font-medium transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Atur Kolom Tabel
                    </button>
                </div>
            </div>
        </div>

        <div>
            <button type="button" onclick="openModalTambah()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-pkt-jingga hover:bg-orange-600 rounded-xl shadow-sm transition-colors border-none outline-none focus:ring-2 focus:ring-orange-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah/Perbarui Laporan
            </button>
        </div>

    </div>
    <!-- ========================================================================= -->

    <!-- ================= TABEL 1 (Dengan Kolom Dinamis) ================= -->
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => '!rounded-xl overflow-x-auto !p-0 shadow-sm border border-gray-100 bg-white mb-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['class' => '!rounded-xl overflow-x-auto !p-0 shadow-sm border border-gray-100 bg-white mb-8']); ?>
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Rincian Volume Dokumen</h3>
            <p class="text-xs text-gray-400">Rincian volume dokumen berdasarkan kategori per bulan untuk filter yang dipilih</p>
        </div>

        <div>
            <?php
                $tableHeaders = ['Tahun', 'Bulan', 'Penerimaan mailroom', 'Reg. Surat Masuk DOF', 'Pengiriman Dalam Negeri', 'Pengiriman Luar Negeri'];
                if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $tableHeaders[] = $k->nama_kolom; } }
            ?>
            <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['headers' => $tableHeaders]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableHeaders)]); ?>
                <?php $__empty_1 = true; $__currentLoopData = $costRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $tambahan = is_string($record->data_tambahan) ? json_decode($record->data_tambahan, true) : ($record->data_tambahan ?? []); ?>
                    <tr class="hover:bg-gray-50 transition-colors text-sm">
                        <td class="px-6 py-4 text-gray-700 font-medium align-middle">{{ $record->tahun }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium align-middle">{{ $record->bulan }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono align-middle">{{ number_format($record->penerimaan_mailroom, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono align-middle">{{ number_format($record->registrasi_surat_masuk_dof, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono align-middle">{{ number_format($record->pengiriman_dalam_negeri, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-gray-700 font-mono align-middle">{{ number_format($record->pengiriman_luar_negeri, 0, ',', '.') }}</td>
                        
                        <!-- RENDER KOLOM DINAMIS -->
                        @if(isset($kolomDinamis))
                            @foreach($kolomDinamis as $kolom)
                                <td class="px-6 py-4 text-gray-600 font-medium align-middle">
                                    @if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom]))
                                        Rp {{ $tambahan[$kolom->nama_kolom] }}

                                    @else
                                        {{ $tambahan[$kolom->nama_kolom] ?? '-' }}

                                    @endif
                                </td>
                            @endforeach
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="10" class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada data laporan volume.</td></tr>
                @endif
             <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $attributes = $__attributesOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5); ?>
@endif
<?php if (isset($__componentOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $component = $__componentOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__componentOriginal163c8ba6efb795223894d5ffef5034f5); ?>
@endif
        </div>
     <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
@endif
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
@endif

    <!-- ================= TABEL 2 (BIAYA ONGKIR) ================= -->
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => '!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['class' => '!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8']); ?>
        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Rincian Total Ongkir Pengiriman Bulanan</h3>
        </div>
        <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['headers' => ['Tahun', 'Bulan', 'Total Ongkir Dalam Negeri', 'Total Ongkir Luar Negeri']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Tahun', 'Bulan', 'Total Ongkir Dalam Negeri', 'Total Ongkir Luar Negeri'])]); ?>
            <?php $__empty_1 = true; $__currentLoopData = $costRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition-colors text-sm">
                    <td class="px-6 py-4 text-gray-700 font-medium">{{ $cost->tahun }}</td>
                    <td class="px-6 py-4 text-gray-900 font-medium">{{ $cost->bulan }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono font-semibold">Rp {{ number_format($cost->ongkir_dalam_negeri, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono font-semibold">Rp {{ number_format($cost->ongkir_luar_negeri, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada data biaya ongkir.</td></tr>
            @endif
            <tr class="bg-gray-100 border-t border-gray-200 font-bold text-blue-900 text-sm">
                <td colspan="2" class="px-6 py-4 text-right">Total Keseluruhan {{ $selectedMonth == 'semua' ? "Filter" : "$selectedMonth $selectedYear" }} :</td>
                <td class="px-6 py-4 text-pkt-biru font-mono">Rp {{ number_format($totalDomestikOverall, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-pkt-biru font-mono">Rp {{ number_format($totalInternasionalOverall, 0, ',', '.') }}</td>
            </tr>
         <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $attributes = $__attributesOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5); ?>
@endif
<?php if (isset($__componentOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $component = $__componentOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__componentOriginal163c8ba6efb795223894d5ffef5034f5); ?>
@endif
     <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
@endif
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
@endif

    <?php if (isset($component)) { $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['id' => 'modalHapusKolom','title' => 'Hapus Kolom Tambahan','message' => 'Kolom ini akan dihilangkan dari sistem. Lanjutkan?']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['id' => 'modalHapusKolom','title' => 'Hapus Kolom Tambahan','message' => 'Kolom ini akan dihilangkan dari sistem. Lanjutkan?']); ?>
<?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $attributes = $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
@endif
<?php if (isset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $component = $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
@endif

    <!-- MODAL IMPORT PENGIRIMAN -->
    <?php if (isset($component)) { $__componentOriginal0419d35d7f1d49ad7587d934def323ae = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0419d35d7f1d49ad7587d934def323ae = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.import-modal','data' => ['id' => 'modalImportPengiriman','route' => ''.e(route('pengiriman-dokumen.import')).'','title' => 'Import Data Pengiriman Dokumen','templateRoute' => ''.e(route('template.download', 'pengiriman-dokumen')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('import-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['id' => 'modalImportPengiriman','route' => ''.e(route('pengiriman-dokumen.import')).'','title' => 'Import Data Pengiriman Dokumen','templateRoute' => ''.e(route('template.download', 'pengiriman-dokumen')).'']); ?>
<?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal0419d35d7f1d49ad7587d934def323ae)): ?>
<?php $attributes = $__attributesOriginal0419d35d7f1d49ad7587d934def323ae; ?>
<?php unset($__attributesOriginal0419d35d7f1d49ad7587d934def323ae); ?>
@endif
<?php if (isset($__componentOriginal0419d35d7f1d49ad7587d934def323ae)): ?>
<?php $component = $__componentOriginal0419d35d7f1d49ad7587d934def323ae; ?>
<?php unset($__componentOriginal0419d35d7f1d49ad7587d934def323ae); ?>
@endif

    <!-- ================= MODAL ATUR KOLOM ================= -->
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalAturKolom','title' => 'Pengaturan Kolom Tambahan','description' => 'Kelola kolom ekstra khusus untuk formulir Pengiriman.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['id' => 'modalAturKolom','title' => 'Pengaturan Kolom Tambahan','description' => 'Kelola kolom ekstra khusus untuk formulir Pengiriman.']); ?>
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar Saat Ini:</h4>
            @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                @foreach($kolomDinamis as $kolom)
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $kolom->nama_kolom }}</p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700">{{ $kolom->tipe_input }}</span> 
                                @if($kolom->tipe_input === 'dropdown' && $kolom->pilihan_dropdown) | Opsi: {{ implode(', ', json_decode($kolom->pilihan_dropdown)) }} @endif
                            </p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('{{ route('kolom-dinamis.destroy', $kolom->id) }}')" class="text-red-500 p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                @endforeach
            @else <p class="text-xs text-gray-500 italic">Belum ada kolom tambahan.</p> @endif
        </div>
        <form action="{{ route('kolom-dinamis.store') }}" method="POST" class="border-t pt-4">
            <?php echo csrf_field(); ?> <input type="hidden" name="modul" value="pengiriman_dokumen">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium mb-1">Nama Kolom</label><input type="text" name="nama_kolom" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none"></div>
                <div><label class="block text-xs font-medium mb-1">Tipe Input</label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option><option value="number">Angka Kuantitas</option><option value="currency">Harga / Uang (Rp)</option><option value="date">Tanggal</option><option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4"><?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','type' => 'button','onclick' => 'closeModal(\'modalAturKolom\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['variant' => 'outline','type' => 'button','onclick' => 'closeModal(\'modalAturKolom\')']); ?>Tutup <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
@endif
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
@endif<?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit']); ?>Simpan <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
@endif
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
@endif</div>
        </form>
     <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
@endif
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
@endif

    <!-- ================= MODAL TAMBAH LAPORAN ================= -->
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalTambahLaporan','title' => 'Formulir Laporan Bulanan Pengiriman','description' => 'Data bersifat overwrite (akan mengganti data bulan yang sama jika sudah ada).']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['id' => 'modalTambahLaporan','title' => 'Formulir Laporan Bulanan Pengiriman','description' => 'Data bersifat overwrite (akan mengganti data bulan yang sama jika sudah ada).']); ?>
        <form action="{{ route('pengiriman-dokumen.store') }}" method="POST" id="formLaporan" class="novalidate-form" novalidate>
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                
                <!-- INPUT TYPE MONTH (KALENDER BAWAAN) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Bulan & Tahun) <span class="text-red-500">*</span></label>
                    <input type="month" id="periode_input" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none cursor-pointer">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden font-medium">Periode wajib dipilih!</span>
                    <!-- Hidden field untuk memecah month string menjadi bulan dan tahun -->
                    <input type="hidden" name="tahun" id="tahun_hidden">
                    <input type="hidden" name="bulan" id="bulan_hidden">
                </div>

                <div class="md:col-span-2 border-t pt-5"><h4 class="font-semibold text-sm bg-gray-50 p-3 rounded-lg border">1. Data Jumlah Dokumen (Visualisasi Chart)</h4></div>

                <div class="md:col-span-1"><label class="block text-sm font-medium mb-1.5">Penerimaan Mailroom <span class="text-red-500">*</span></label><input type="number" name="volume_mailroom" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"><span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span></div>
                <div class="md:col-span-1"><label class="block text-sm font-medium mb-1.5">Registrasi Surat Masuk via DOF <span class="text-red-500">*</span></label><input type="number" name="volume_dof" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"><span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span></div>
                <div class="md:col-span-1"><label class="block text-sm font-medium mb-1.5">Pengiriman Dalam Negeri <span class="text-red-500">*</span></label><input type="number" name="volume_domestik" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"><span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span></div>
                <div class="md:col-span-1"><label class="block text-sm font-medium mb-1.5">Pengiriman Luar Negeri <span class="text-red-500">*</span></label><input type="number" name="volume_internasional" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"><span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span></div>

                <div class="md:col-span-2 border-t pt-5"><h4 class="font-semibold text-sm bg-gray-50 p-3 rounded-lg border">2. Data Biaya Ongkir Rupiah (Visualisasi Tabel Total)</h4></div>

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1.5">Total Ongkir Dalam Negeri <span class="text-red-500">*</span></label>
                    <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span><input type="text" id="input_cost_domestik" required class="input-rupiah w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none"><input type="hidden" name="cost_domestik" required></div>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1.5">Total Ongkir Luar Negeri <span class="text-red-500">*</span></label>
                    <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span><input type="text" id="input_cost_internasional" required class="input-rupiah w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none"><input type="hidden" name="cost_internasional" required></div>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi!</span>
                </div>

                <!-- INJEKSI KOLOM DINAMIS (TAMBAH DATA) -->
                @if(isset($kolomDinamis) && $kolomDinamis->count() > 0)
                    <div class="md:col-span-2 border-t pt-5"><h4 class="font-semibold text-sm bg-gray-50 p-3 rounded-lg border">3. Informasi Tambahan</h4></div>
                    @foreach($kolomDinamis as $kolom)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $kolom->nama_kolom }}</label>
                            @if($kolom->tipe_input === 'text')<input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'number')<input type="number" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'currency')
                                <div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">Rp</span><input type="text" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="input-rupiah w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono outline-none" placeholder="0"><input type="hidden" name="data_tambahan[{{ $kolom->nama_kolom }}]"></div>
                            @elseif($kolom->tipe_input === 'date')<input type="date" name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                            @elseif($kolom->tipe_input === 'dropdown')
                                <select name="data_tambahan[{{ $kolom->nama_kolom }}]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                                    <option value="">Pilih...</option>
                                    @if($kolom->pilihan_dropdown) @foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)<option value="{{ trim($pilihan) }}">{{ trim($pilihan) }}</option>@endforeach @endif
                                </select>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','type' => 'button','onclick' => 'closeModal(\'modalTambahLaporan\')','class' => '!px-6 !py-2.5 !rounded-lg text-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['variant' => 'outline','type' => 'button','onclick' => 'closeModal(\'modalTambahLaporan\')','class' => '!px-6 !py-2.5 !rounded-lg text-sm']); ?>Batal <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
@endif
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
@endif
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','type' => 'submit','class' => '!px-6 !py-2.5 !rounded-lg text-sm border-none shadow-md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
@endif
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit','class' => '!px-6 !py-2.5 !rounded-lg text-sm border-none shadow-md']); ?>Simpan Laporan <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
@endif
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
@endif
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
@endif
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
@endif
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
@endif

</main>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    function triggerDeleteKolom(deleteUrl) {
        closeModal('modalAturKolom');
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
    }
    function toggleDropdownConfig() {
        const selector = document.getElementById('tipeInputSelector');
        const configArea = document.getElementById('dropdownConfigArea');
        if(selector.value === 'dropdown') {
            configArea.classList.remove('hidden'); configArea.querySelector('input').setAttribute('required', 'true');
        } else {
            configArea.classList.add('hidden'); configArea.querySelector('input').removeAttribute('required');
        }
    }

    // --- DROPDOWN MENU ACTION BAR LOGIC ---
    function toggleActionDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) { el.classList.remove('hidden'); }
    }

    // Tutup dropdown jika user klik sembarang tempat di luar kotak
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdownOpsiSuper"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    // ================= FUNGSI SINKRONISASI TAHUN BULAN (KALENDER) =================
    const bulanIndoList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const periodeInput = document.getElementById('periode_input');
    function syncPeriode() {
        const val = periodeInput.value;
        if(val) {
            const parts = val.split('-');
            document.getElementById('tahun_hidden').value = parts[0];
            document.getElementById('bulan_hidden').value = bulanIndoList[parseInt(parts[1], 10) - 1];
            
            // Hapus outline merah saat periode dipilih
            periodeInput.classList.remove('border-red-500');
            periodeInput.classList.add('border-gray-300');
            const errorMsg = periodeInput.nextElementSibling;
            if(errorMsg && errorMsg.classList.contains('error-msg')) errorMsg.classList.add('hidden');
        } else {
            document.getElementById('tahun_hidden').value = '';
            document.getElementById('bulan_hidden').value = '';
        }
    }
    periodeInput.addEventListener('change', syncPeriode);
    periodeInput.addEventListener('input', syncPeriode);

    function openModalTambah() {
        document.getElementById('formLaporan').reset();
        
        // Set Default ke Bulan Sekarang
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('periode_input').value = currentMonth;
        syncPeriode();

        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambahLaporan');
    }

    // FORMAT RUPIAH OTOMATIS & PENCEGAH 'e'
    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-rupiah')) {
            let rawValue = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?!$)/, '');
            if(e.target.nextElementSibling && e.target.nextElementSibling.tagName === 'INPUT') {
                e.target.nextElementSibling.value = rawValue;
            }
            if (rawValue) {
                e.target.value = new Intl.NumberFormat('id-ID').format(rawValue);
            } else {
                e.target.value = '';
            }
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.target && e.target.type === 'number') {
            if (['e', 'E', '+', '-', '.'].includes(e.key)) { e.preventDefault(); }
        }
    });

    // VALIDASI FORM CLIENT-SIDE TEXT MERAH
    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            syncPeriode();
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                const errorSpan = field.nextElementSibling;
                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('border-red-500'); field.classList.remove('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500'); field.classList.add('border-gray-300');
                    if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                }
            });
            if (!isValid) e.preventDefault();
        });

        form.querySelectorAll('[required]').forEach(field => {
            ['input', 'change'].forEach(evt => {
                field.addEventListener(evt, function() {
                    const errorSpan = this.nextElementSibling;
                    if (this.value && this.value.trim() !== '') {
                        this.classList.remove('border-red-500'); this.classList.add('border-gray-300');
                        if (errorSpan && errorSpan.classList.contains('error-msg')) errorSpan.classList.add('hidden');
                    }
                });
            });
        });
    });

    // LOGIKA CHART
    let volumeVolumeChartInstance = null;
    const volumeChartConfigData = <?php echo json_encode($chartVolumeConfig); ?>;
    function initVolumeChart(chartType) {
        const volumeChartCanvas = document.getElementById('canvas_volumeVolumeChart');
        if (volumeChartCanvas) {
            const ctx = volumeChartCanvas.getContext('2d');
            if (volumeVolumeChartInstance) volumeVolumeChartInstance.destroy();

            let chartDataToUse = JSON.parse(JSON.stringify(volumeChartConfigData));
            let isEmpty = false;

            if (!chartDataToUse.labels || chartDataToUse.labels.length === 0 || (chartDataToUse.datasets.length > 0 && chartDataToUse.datasets[0].data.length === 0)) {
                isEmpty = true;
            }

            if (isEmpty) {
                chartDataToUse.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                chartDataToUse.datasets.forEach(dataset => { dataset.data = new Array(12).fill(0); });
            }

            const chartOptions = {
                responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                    tooltip: {
                        enabled: !isEmpty, backgroundColor: 'rgba(17, 24, 39, 0.9)', titleFont: { size: 14, weight: 'bold' }, bodyFont: { size: 12 }, padding: 12, cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                return label;
                            }
                        }
                    },
                    beforeDraw: function(chart) {
                        if (isEmpty) {
                            var width = chart.width, height = chart.height, ctx = chart.ctx;
                            ctx.restore();
                            var fontSize = (height / 114).toFixed(2);
                            ctx.font = fontSize + "em sans-serif";
                            ctx.textBaseline = "middle";
                            var text = "Tidak Ada Data Laporan", textX = Math.round((width - ctx.measureText(text).width) / 2), textY = height / 2;
                            ctx.fillStyle = '#9CA3AF';
                            ctx.fillText(text, textX, textY);
                            ctx.save();
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, max: isEmpty ? 10 : undefined, grid: { color: '#F3F4F6' }, ticks: { font: { size: 10, color: '#6B7280' } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: '500', color: '#6B7280' } } }
                }
            };

            volumeVolumeChartInstance = new Chart(ctx, { type: chartType, data: chartDataToUse, options: chartOptions });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initVolumeChart('{{ $dynamicChartType ?? 'bar' }}');
    });

    function changeChartType(chartId, newType) {
        if (chartId === 'volumeVolumeChart') initVolumeChart(newType);
    }
</script>
@endsection

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\web_pkt_adkor_internal\adkor-report-internal\resources\views/pengiriman-dokumen.blade.php ENDPATH**/ ?>