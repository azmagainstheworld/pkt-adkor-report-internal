

<?php $__env->startSection('content'); ?>
<style>
.hide-bulk th:first-child, .hide-bulk td:first-child { display: none !important; }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <!-- KOMPONEN MODAL SUKSES ANDALAN ZAHRA ✨ -->
    <?php if (isset($component)) { $__componentOriginal6475feafa5c7d85d71efc5a48adb5766 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6475feafa5c7d85d71efc5a48adb5766 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.success-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('success-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6475feafa5c7d85d71efc5a48adb5766)): ?>
<?php $attributes = $__attributesOriginal6475feafa5c7d85d71efc5a48adb5766; ?>
<?php unset($__attributesOriginal6475feafa5c7d85d71efc5a48adb5766); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6475feafa5c7d85d71efc5a48adb5766)): ?>
<?php $component = $__componentOriginal6475feafa5c7d85d71efc5a48adb5766; ?>
<?php unset($__componentOriginal6475feafa5c7d85d71efc5a48adb5766); ?>
<?php endif; ?>

    <!-- MODAL ERROR KUSTOM -->
    <?php if(session('error_modal')): ?>
    <div id="errorModalCustom" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center relative transform transition-all">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Gagal Memproses</h3>
            <p class="text-sm text-gray-600 mb-6"><?php echo e(session('error_modal')); ?></p>
            <button type="button" onclick="document.getElementById('errorModalCustom').style.display='none';" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-red-600/20">
                Tutup
            </button>
        </div>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-xl text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex flex-col shadow-sm">
            <span class="font-bold mb-2">Gagal memproses data. Periksa inputan Anda:</span>
            <ul class="list-disc list-inside pl-4 text-xs">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Header Section & Filter (Req 1) -->
    <div class="flex justify-between items-end mb-6 gap-4 flex-wrap">
        <div>
            <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
                <a href="#" class="hover:text-blue-600">Dashboard</a>
                <span class="text-gray-400">/</span><span class="text-gray-500">Administrasi</span><span class="text-gray-400">/</span>
                <span class="text-blue-600 font-medium">Surat Masuk & Keluar</span>
            </nav>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">Surat Masuk & Surat Keluar</h2>
            <p class="text-sm text-gray-500 font-medium"><?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d F Y')); ?></p>
        </div>
        
        <!-- Filter Dinamis -->
        <form action="<?php echo e(route('surat.index')); ?>" method="GET" class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-gray-100">
            <select name="tahun" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 cursor-pointer outline-none focus:border-blue-500">
                <option value="semua" <?php echo e($tahunFilter == 'semua' ? 'selected' : ''); ?>>Semua Tahun</option>
                <?php $__currentLoopData = $tahunTersedia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($thn); ?>" <?php echo e($tahunFilter == $thn ? 'selected' : ''); ?>>Tahun <?php echo e($thn); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="bulan" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 cursor-pointer outline-none focus:border-blue-500">
                <option value="semua" <?php echo e($bulanFilter == 'semua' ? 'selected' : ''); ?>>Semua Bulan</option>
                <?php $__currentLoopData = $bulanTersedia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($b); ?>" <?php echo e($bulanFilter == $b ? 'selected' : ''); ?>><?php echo e($b); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
    </div>

    <!-- Req 2 & 3: CHART SECTION MENGGUNAKAN KOMPONEN DYNAMIC CHART -->
    <div class="mb-8">
        <?php if (isset($component)) { $__componentOriginal8213acddf1ec7aafb42f5a9a66fba60a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8213acddf1ec7aafb42f5a9a66fba60a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dynamic-chart','data' => ['id' => 'chartSurat','title' => 'Tren Distribusi Surat '.e($tahunFilter == 'semua' ? 'Keseluruhan' : 'Tahun '.$tahunFilter).'','subtitle' => 'Menampilkan 12 bulan penuh (Status Terkirim) untuk perbandingan tren','type' => 'bar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-chart'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'chartSurat','title' => 'Tren Distribusi Surat '.e($tahunFilter == 'semua' ? 'Keseluruhan' : 'Tahun '.$tahunFilter).'','subtitle' => 'Menampilkan 12 bulan penuh (Status Terkirim) untuk perbandingan tren','type' => 'bar']); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8213acddf1ec7aafb42f5a9a66fba60a)): ?>
<?php $attributes = $__attributesOriginal8213acddf1ec7aafb42f5a9a66fba60a; ?>
<?php unset($__attributesOriginal8213acddf1ec7aafb42f5a9a66fba60a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8213acddf1ec7aafb42f5a9a66fba60a)): ?>
<?php $component = $__componentOriginal8213acddf1ec7aafb42f5a9a66fba60a; ?>
<?php unset($__componentOriginal8213acddf1ec7aafb42f5a9a66fba60a); ?>
<?php endif; ?>
    </div>

    <!-- TABEL 1 (REKAPITULASI - READ ONLY) -->
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => '!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white mb-8']); ?>
        <div class="p-5 border-b border-gray-100 bg-white">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">Akumulasi Laporan Surat Masuk dan Kekuar</h3>
                <p class="text-xs text-gray-400">Total surat berstatus <span class="text-green-600 font-medium">"Terkirim"</span> terfilter Tahun: <?php echo e($tahunFilter); ?>, Bulan: <?php echo e($bulanFilter); ?></p>
            </div>
            <!-- Opsi Lanjutan -->
            <div class="relative inline-block text-left">
                <button type="button" onclick="toggleDropdown('dropdownOpsiSurat1')" class="inline-flex justify-center items-center gap-2 rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Opsi Lanjutan
                    <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Isi Dropdown (Req 7 & 8: Opsi Ekspor 3 Jenis) -->
                <div id="dropdownOpsiSurat1" class="hidden absolute right-0 z-[50] mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-visible transition-all">
                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ekspor Tabel Rekapitulasi (Excel/PDF)</p>
                    </div>
                    <div class="py-1" role="none">
                        <a href="<?php echo e(route('surat.export.excel', ['jenis' => 'tabel1', 'tahun' => $tahunFilter, 'bulan' => $bulanFilter])); ?>" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-green-50 flex items-center gap-2.5 font-medium transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Ekspor ke Excel
                        </a>
                        <a href="<?php echo e(route('surat.export.pdf', ['jenis' => 'tabel1', 'tahun' => $tahunFilter, 'bulan' => $bulanFilter])); ?>" target="_blank" class="w-full text-left text-gray-700 px-4 py-2.5 text-xs hover:bg-red-50 flex items-center gap-2.5 font-medium transition-colors border-t border-gray-50">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Ekspor ke PDF
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>

    <!-- TABEL 2 (DETAIL DATA SATUAN) -->
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => '!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!rounded-xl overflow-visible !p-0 shadow-sm border border-gray-100 bg-white']); ?>
        <form id="bulkDeleteForm" action="<?php echo e(route('surat.destroyBulk')); ?>" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            
            <div id="btnGroupBulk" class="hidden flex justify-between items-center px-4 py-2 bg-red-50 border-b border-red-100">
                <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
                <div class="flex gap-2">
                    <button type="button" onclick="cancelAll()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700">Hapus Terpilih</button>
                </div>
            </div>

        <div class="p-5 border-b border-gray-100 bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Arsip Detail Surat Satuan</h3>
            <p class="text-xs text-gray-400">Pencatatan satuan setiap berkas surat masuk dan keluar</p>
        </div>
        <?php
            // Definisi Header (Req 5: Kolom File Dihapus)
            $headers = ['<input type="checkbox" id="selectAllBulk" onclick="toggleSelectAll()">', 'No', 'Tahun', 'Bulan', 'Nomor Surat', 'Tanggal Surat', 'Judul Surat', 'Status', 'Jenis Surat'];
            if(isset($kolomDinamis)) { foreach($kolomDinamis as $k) { $headers[] = $k->nama_kolom; } }
            $headers[] = 'Aksi';
        ?>
        <div id="tableContainerBulk" class="hide-bulk overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-6 py-3.5 font-semibold whitespace-nowrap"><?php echo $header; ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $tableDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $tambahan = $row->data_tambahan ?? []; ?>
                        <tr class="hover:bg-gray-50 transition-colors text-xs">
                            <td class="px-6 py-4 text-center align-middle"><input type="checkbox" name="ids[]" class="cb-bulk" value="<?php echo e($row->id); ?>" onclick="toggleCheckbox()"></td>
                            <td class="px-6 py-4 text-gray-500 font-medium"><?php echo e($index + 1); ?></td>
                            <td class="px-6 py-4"><?php echo e($row->tahun); ?></td>
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo e($row->bulan); ?></td>
                            <td class="px-6 py-4 font-mono text-gray-800"><?php echo e($row->nomor_surat); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e($row->tanggal_surat ? $row->tanggal_surat->format('d/m/Y') : '-'); ?></td>
                            <td class="px-6 py-4 max-w-xs truncate"><?php echo e($row->judul_surat); ?></td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?php echo e($row->status == 'Terkirim' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'); ?>">
                                    <?php echo e($row->status); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                <?php if($row->jenis_surat == 'Surat Masuk'): ?>
                                    <span class="text-[#0056A3]">Masuk</span>
                                <?php else: ?>
                                    <span class="text-[#F7941E]">Keluar</span>
                                <?php endif; ?>
                            </td>
                            
                            <?php if(isset($kolomDinamis)): ?>
                                <?php $__currentLoopData = $kolomDinamis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kolom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        <?php if($kolom->tipe_input === 'currency' && isset($tambahan[$kolom->nama_kolom])): ?>
                                            Rp <?php echo e(number_format($tambahan[$kolom->nama_kolom], 0, ',', '.')); ?>

                                        <?php else: ?>
                                            <?php echo e($tambahan[$kolom->nama_kolom] ?? '-'); ?>

                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex gap-1.5 justify-center">
                                    <!-- Req 6: Tombol Detail Menggunakan <a> ke Halaman Baru -->
                                    <a href="<?php echo e(route('surat.show', $row->id)); ?>" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg border border-blue-200" title="Lihat Halaman Detail Lengkap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <!-- Edit -->
                                    <button type="button" onclick="openEditDataSatuanModal(<?php echo e(json_encode($row)); ?>)" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg border border-amber-200" title="Edit Data">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <!-- Hapus -->
                                    <button type="button" onclick="openDeleteModal('modalHapusSurat', '<?php echo e(route('surat.destroy', $row->id)); ?>')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg border border-red-200" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="<?php echo e(count($headers)); ?>" class="px-6 py-10 text-center text-gray-500 py-6">Tidak ada data arsip surat satuan untuk periode ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['id' => 'modalHapusSurat','title' => 'Hapus Arsip Surat','message' => 'Apakah Anda yakin ingin menghapus arsip surat satuan ini? Data rekap bulanan akan disesuaikan otomatis.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalHapusSurat','title' => 'Hapus Arsip Surat','message' => 'Apakah Anda yakin ingin menghapus arsip surat satuan ini? Data rekap bulanan akan disesuaikan otomatis.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $attributes = $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $component = $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['id' => 'modalHapusKolom','title' => 'Hapus Kolom Tambahan','message' => 'Kolom ini akan dihilangkan dari tabel dan formulir. Lanjutkan?']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalHapusKolom','title' => 'Hapus Kolom Tambahan','message' => 'Kolom ini akan dihilangkan dari tabel dan formulir. Lanjutkan?']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $attributes = $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $component = $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>

    <?php if(auth()->user()->isAdmin()): ?>
<!-- MODAL ATUR KOLOM -->
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalAturKolom','title' => 'Pengaturan Kolom Surat','description' => 'Kelola kolom ekstra khusus untuk pencatatan detail surat satuan.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalAturKolom','title' => 'Pengaturan Kolom Surat','description' => 'Kelola kolom ekstra khusus untuk pencatatan detail surat satuan.']); ?>
        <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 max-h-48 overflow-y-auto">
            <h4 class="text-sm font-bold text-gray-800 mb-3">Kolom Terdaftar Saat Ini:</h4>
            <?php if(isset($kolomDinamis) && $kolomDinamis->count() > 0): ?>
                <?php $__currentLoopData = $kolomDinamis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kolom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 mb-2 shadow-sm">
                        <div>
                            <p class="text-sm font-semibold text-gray-800"><?php echo e($kolom->nama_kolom); ?></p>
                            <p class="text-[11px] text-gray-500">Tipe: <span class="uppercase font-bold text-gray-700"><?php echo e($kolom->tipe_input); ?></span></p>
                        </div>
                        <button type="button" onclick="triggerDeleteKolom('<?php echo e(route('kolom-dinamis.destroy', $kolom->id)); ?>')" class="text-red-500 p-1 hover:bg-red-50 rounded transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?> <p class="text-xs text-gray-500 italic text-center py-4">Belum ada kolom tambahan yang dibuat.</p> <?php endif; ?>
        </div>
        <form action="<?php echo e(route('kolom-dinamis.store')); ?>" method="POST" class="border-t pt-5 space-y-4 novalidate-form" novalidate>
            <?php echo csrf_field(); ?>
        <!-- Download Template injected -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="<?php echo e(route('template.download', 'surat')); ?>" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>
 <input type="hidden" name="modul" value="surat">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Buat Kolom Baru:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">Nama Kolom <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kolom" required placeholder="Cth: Perihal Ringkas" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[10px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Tipe Input <span class="text-red-500">*</span></label>
                    <select name="tipe_input" id="tipeInputSelector" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500 cursor-pointer" onchange="toggleDropdownConfig()">
                        <option value="text">Teks Singkat</option><option value="number">Angka Biasa</option><option value="date">Tanggal</option><option value="dropdown">Pilihan (Dropdown)</option>
                    </select>
                </div>
                <div class="md:col-span-2 hidden" id="dropdownConfigArea">
                    <label class="block text-xs font-medium mb-1">Pilihan Dropdown (Pisahkan dengan koma)</label>
                    <input type="text" name="pilihan_dropdown" placeholder="Cth: Internal, Eksternal" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500">
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
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','type' => 'button','onclick' => 'closeModal(\'modalAturKolom\')']); ?>Tutup <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','type' => 'submit','class' => '!bg-blue-600 hover:!bg-blue-700 border-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit','class' => '!bg-blue-600 hover:!bg-blue-700 border-none']); ?>Simpan Kolom <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?></div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php endif; ?>

    <!-- MODAL TAMBAH DATA SATUAN -->
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalTambah','title' => 'Catat Surat Satuan Baru','description' => 'Lengkapi detail surat. Tahun dan Bulan laporan otomatis diset berdasarkan Periode Laporan.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalTambah','title' => 'Catat Surat Satuan Baru','description' => 'Lengkapi detail surat. Tahun dan Bulan laporan otomatis diset berdasarkan Periode Laporan.']); ?>
        <form action="<?php echo e(route('surat.store')); ?>" method="POST" id="formTambah" class="space-y-4 novalidate-form" novalidate>
            <?php echo csrf_field(); ?>
        <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Masuk Rekap Bulan/Tahun) <span class="text-red-500">*</span></label>
                <input type="month" name="periode_laporan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
            </div>
            <hr class="border-gray-100">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_surat" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_surat" required placeholder="Cth: 001/UM/XI/2023" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 font-mono">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi & unik</span>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul / Perihal Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_surat" required placeholder="Cth: Undangan Rapat Koordinasi Wilayah" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Drafter / Konseptor</label>
                    <input type="text" name="drafter" placeholder="Nama drafter" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                </div>
                <!-- Req 5: Upload Berkas Dihapus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer outline-none">
                        <option value="Terkirim" selected>Terkirim (Masuk Hitungan Rekap)</option>
                        <option value="Dibatalkan">Dibatalkan (Abaikan dari Rekap)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Surat <span class="text-red-500">*</span></label>
                    <select name="jenis_surat" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer outline-none">
                        <option value="" disabled selected>Pilih...</option>
                        <option value="Surat Masuk">Surat Masuk</option>
                        <option value="Surat Keluar">Surat Keluar</option>
                    </select>
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib dipilih</span>
                </div>
            </div>

            <!-- INJEKSI KOLOM DINAMIS (TAMBAH) -->
            <?php if(isset($kolomDinamis) && $kolomDinamis->count() > 0): ?>
                <div class="space-y-4 pt-2 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-800">Informasi Tambahan</h4>
                    <?php $__currentLoopData = $kolomDinamis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kolom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e($kolom->nama_kolom); ?></label>
                            <?php if($kolom->tipe_input === 'text'): ?>
                                <input type="text" name="data_tambahan[<?php echo e($kolom->nama_kolom); ?>]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            <?php elseif($kolom->tipe_input === 'number'): ?>
                                <input type="number" name="data_tambahan[<?php echo e($kolom->nama_kolom); ?>]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            <?php elseif($kolom->tipe_input === 'date'): ?>
                                <input type="date" name="data_tambahan[<?php echo e($kolom->nama_kolom); ?>]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 cursor-pointer">
                            <?php elseif($kolom->tipe_input === 'dropdown'): ?>
                                <select name="data_tambahan[<?php echo e($kolom->nama_kolom); ?>]" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 cursor-pointer outline-none">
                                    <option value="">Pilih...</option>
                                    <?php if($kolom->pilihan_dropdown): ?> 
                                        <?php $__currentLoopData = json_decode($kolom->pilihan_dropdown); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pilihan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(trim($pilihan)); ?>"><?php echo e(trim($pilihan)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                                    <?php endif; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </form>

         <?php $__env->slot('footer', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','onclick' => 'closeModal(\'modalTambah\')','class' => '!px-6 !py-2.5 !rounded-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','onclick' => 'closeModal(\'modalTambah\')','class' => '!px-6 !py-2.5 !rounded-lg']); ?>Batal <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','type' => 'submit','form' => 'formTambah','class' => '!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600 border-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit','form' => 'formTambah','class' => '!px-6 !py-2.5 !rounded-lg !bg-pkt-jingga hover:!bg-orange-600 border-none']); ?>Simpan Arsip <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

    <!-- MODAL EDIT DATA SATUAN -->
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalEditDataSatuan','title' => 'Edit Arsip Surat','description' => 'Perbarui detail surat. Perubahan periode laporan akan menyesuaikan rekap otomatis.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalEditDataSatuan','title' => 'Edit Arsip Surat','description' => 'Perbarui detail surat. Perubahan periode laporan akan menyesuaikan rekap otomatis.']); ?>
        <form action="" method="POST" id="formEditDataSatuan" class="space-y-4 novalidate-form" novalidate>
            <?php echo csrf_field(); ?>
        <!-- Download Template injected -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="<?php echo e(route('template.download', 'surat')); ?>" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>

            <?php echo method_field('PUT'); ?>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Laporan (Masuk Rekap Bulan/Tahun) <span class="text-red-500">*</span></label>
                <input type="month" name="periode_laporan" id="edit_periode_laporan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer">
                <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
            </div>
            <hr class="border-gray-100">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_surat" id="edit_tanggal" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_surat" id="edit_nomor" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 font-mono">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi & unik</span>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul / Perihal Surat <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_surat" id="edit_judul" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                    <span class="error-msg text-red-500 text-[11px] mt-1 hidden">Wajib diisi</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Drafter / Konseptor</label>
                    <input type="text" name="drafter" id="edit_drafter" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500">
                </div>
                <!-- Req 5: Upload Berkas Dihapus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 cursor-pointer outline-none">
                        <option value="Terkirim">Terkirim</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Surat <span class="text-red-500">*</span></label>
                    <select name="jenis_surat" id="edit_jenis" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:border-blue-500 outline-none">
                        <option value="Surat Masuk">Surat Masuk</option>
                        <option value="Surat Keluar">Surat Keluar</option>
                    </select>
                </div>
            </div>

            <!-- INJEKSI KOLOM DINAMIS (EDIT) -->
            <?php if(isset($kolomDinamis) && $kolomDinamis->count() > 0): ?>
                <div class="space-y-4 pt-2 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-800">Informasi Tambahan</h4>
                    <?php $__currentLoopData = $kolomDinamis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kolom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e($kolom->nama_kolom); ?></label>
                            <?php if($kolom->tipe_input === 'text'): ?>
                                <input type="text" name="data_tambahan[<?php echo e($kolom->nama_kolom); ?>]" data-key="<?php echo e($kolom->nama_kolom); ?>" class="input-dinamis-edit w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            <?php elseif($kolom->tipe_input === 'number'): ?>
                                <input type="number" name="data_tambahan[<?php echo e($kolom->nama_kolom); ?>]" data-key="<?php echo e($kolom->nama_kolom); ?>" class="input-dinamis-edit w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            <?php elseif($kolom->tipe_input === 'date'): ?>
                                <input type="date" name="data_tambahan[<?php echo e($kolom->nama_kolom); ?>]" data-key="<?php echo e($kolom->nama_kolom); ?>" class="input-dinamis-edit w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 cursor-pointer">
                            <?php elseif($kolom->tipe_input === 'dropdown'): ?>
                                <select name="data_tambahan[<?php echo e($kolom->nama_kolom); ?>]" data-key="<?php echo e($kolom->nama_kolom); ?>" class="input-dinamis-edit w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 outline-none">
                                    <option value="">Pilih...</option>
                                    <?php if($kolom->pilihan_dropdown): ?> 
                                        <?php $__currentLoopData = json_decode($kolom->pilihan_dropdown); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pilihan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(trim($pilihan)); ?>"><?php echo e(trim($pilihan)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                                    <?php endif; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </form>

         <?php $__env->slot('footer', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','onclick' => 'closeModal(\'modalEditDataSatuan\')','class' => '!px-6 !py-2.5 !rounded-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','onclick' => 'closeModal(\'modalEditDataSatuan\')','class' => '!px-6 !py-2.5 !rounded-lg']); ?>Batal <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','type' => 'submit','form' => 'formEditDataSatuan','class' => '!px-6 !py-2.5 !rounded-lg !bg-amber-500 hover:!bg-amber-600 border-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit','form' => 'formEditDataSatuan','class' => '!px-6 !py-2.5 !rounded-lg !bg-amber-500 hover:!bg-amber-600 border-none']); ?>Simpan Perubahan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

    <!-- Modal Impor Excel -->
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalImportExcel','title' => 'Impor Data Surat via Excel','description' => 'Unduh template Excel yang disediakan, isi data satuan, lalu unggah kembali di sini. Sistem akan melewati data duplikat secara otomatis.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalImportExcel','title' => 'Impor Data Surat via Excel','description' => 'Unduh template Excel yang disediakan, isi data satuan, lalu unggah kembali di sini. Sistem akan melewati data duplikat secara otomatis.']); ?>
        <form action="<?php echo e(route('surat.import.excel')); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
        <!-- Download Template injected -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs p-3 rounded-lg flex items-start gap-2 mb-4">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-medium mb-1">Tips Import Data:</p>
                <p>Unduh template, isi, lalu unggah kembali.</p>
                <a href="<?php echo e(route('template.download', 'surat')); ?>" class="inline-block mt-2 font-bold text-blue-700 hover:text-blue-900 underline">Unduh Template Excel</a>
            </div>
        </div>

            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs text-gray-600 flex flex-col gap-3 shadow-inner mb-4">
                <div class="flex items-center gap-2 text-green-700 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Instruksi Impor
                </div>
                <ul class="list-disc list-inside pl-1 space-y-1">
                    <li>Gunakan format tanggal di Excel: <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-gray-200">YYYY-MM-DD</span>.</li>
                    <li>Kolom 'Status': <span class="font-bold">Terkirim</span> / <span class="font-bold">Dibatalkan</span>.</li>
                    <li>Kolom 'Jenis Surat': <span class="font-bold">Surat Masuk</span> / <span class="font-bold">Surat Keluar</span>.</li>
                </ul>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih File Excel (.xlsx / .xls) <span class="text-red-500">*</span></label>
                <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
            </div>
            
            <div class="flex justify-end gap-3 mt-5 pt-4 border-t border-gray-100">
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','type' => 'button','onclick' => 'closeModal(\'modalImportExcel\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','type' => 'button','onclick' => 'closeModal(\'modalImportExcel\')']); ?>Batal <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','type' => 'submit','class' => '!bg-green-600 hover:!bg-green-700 border-none !px-6 flex items-center gap-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','type' => 'submit','class' => '!bg-green-600 hover:!bg-green-700 border-none !px-6 flex items-center gap-2']); ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Unggah & Proses
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

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

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // ================= DROPDOWN TOGGLE LOGIC =================
    function toggleDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) el.classList.remove('hidden');
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

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

    function openModalTambah() {
        document.getElementById('formTambah').reset();
        const now = new Date();
        const today = now.toISOString().substring(0, 10);
        document.querySelector('#formTambah input[name="tanggal_surat"]').value = today;
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('formTambah').querySelector('input[name="periode_laporan"]').value = currentMonth;

        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalTambah');
    }

    function openEditDataSatuanModal(row) {
        document.getElementById('formEditDataSatuan').reset();
        document.getElementById('formEditDataSatuan').action = "/administrasi/surat-masuk-keluar/" + row.id;

        document.getElementById('edit_tanggal').value = row.tanggal_surat.substring(0, 10);
        document.getElementById('edit_nomor').value = row.nomor_surat;
        document.getElementById('edit_judul').value = row.judul_surat;
        document.getElementById('edit_drafter').value = row.drafter || '';
        document.getElementById('edit_status').value = row.status;
        document.getElementById('edit_jenis').value = row.jenis_surat;

        const masterMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const monthIndex = masterMonths.indexOf(row.bulan) + 1;
        const formattedMonth = String(monthIndex).padStart(2, '0');
        document.getElementById('edit_periode_laporan').value = `${row.tahun}-${formattedMonth}`;

        const tambahan = row.data_tambahan || {};
        document.querySelectorAll('.input-dinamis-edit').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = tambahan[key] || '';
        });

        document.querySelectorAll('.border-red-500').forEach(el => { el.classList.remove('border-red-500'); el.classList.add('border-gray-300'); });
        document.querySelectorAll('.error-msg').forEach(el => el.classList.add('hidden'));
        openModal('modalEditDataSatuan');
    }

    document.addEventListener('input', function(e) {
        if(e.target && e.target.classList.contains('input-currency')) {
            let rawValue = e.target.value.replace(/[^0-9]/g, '');
            if(e.target.nextElementSibling && e.target.nextElementSibling.tagName === 'INPUT') {
                e.target.nextElementSibling.value = rawValue;
            }
            if (rawValue) e.target.value = new Intl.NumberFormat('id-ID').format(rawValue);
            else e.target.value = '';
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.target && e.target.type === 'number') {
            if (['e', 'E', '+', '-', '.'].includes(e.key)) { e.preventDefault(); }
        }
    });

    document.querySelectorAll('.novalidate-form').forEach(form => {
        form.addEventListener('submit', function(e) {
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
    });

    // ================= INTEGRASI DYNAMIC CHART KOMPONEN =================
    const allChartData = {
        chartSurat: {
            labels: <?php echo json_encode(array_column($chartData, 'label')); ?>,
            dataMasuk: <?php echo json_encode(array_column($chartData, 'masuk')); ?>,
            dataKeluar: <?php echo json_encode(array_column($chartData, 'keluar')); ?>,
            hasData: true
        }
    };
    
    let chartInstances = {};

    function renderChart(chartId, type) {
        const canvasEl = document.getElementById('canvas_' + chartId);
        const config = allChartData[chartId];
        if (!canvasEl || !config) return;

        if (chartInstances[chartId]) chartInstances[chartId].destroy();

        // Pengaturan format tooltip dan sumbu yang rapi
        const options = {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: type !== 'bar', position: 'bottom' }, 
                tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8 }
            }
        };

        if (type === 'bar') {
            options.scales = {
                y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            };
        }

        chartInstances[chartId] = new Chart(canvasEl.getContext('2d'), {
            type: type,
            data: {
                labels: config.labels,
                datasets: [
                    { label: 'Surat Masuk', data: config.dataMasuk, backgroundColor: '#0056A3', borderRadius: (type === 'bar' ? 4 : 0) },
                    { label: 'Surat Keluar', data: config.dataKeluar, backgroundColor: '#F7941E', borderRadius: (type === 'bar' ? 4 : 0) }
                ]
            },
            options: options
        });
    }

    // Fungsi ini dipanggil dari event onchange select HTML komponen x-dynamic-chart
    function changeChartType(chartId, newType) { renderChart(chartId, newType); }
    
    // Inisialisasi awal saat dimuat
    window.onload = function () { renderChart('chartSurat', 'bar'); };
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>