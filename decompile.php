<?php
$content = file_get_contents('storage/framework/views/5cd05037d81c8fb6327ca1afc58d4cc5.php');

// We must avoid the literal sequence of question mark and greater-than sign in our script,
// because PHP might interpret it as a closing tag depending on context.
$php_close = '?' . '>';
$php_open = '<' . '?php';

$replacements = [
    $php_open . ' $__env->startSection(\'content\'); ' . $php_close => '@extends(\'layouts.app\')' . "\n" . '@section(\'content\')',
    $php_open . ' $__env->stopSection(); ' . $php_close => '@endsection',
    $php_open . ' echo $__env->make(\'layouts.app\', \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ' . $php_close => '',
    
    $php_open . ' if(isset($kolomDinamis)): ' . $php_close => '@if(isset($kolomDinamis))',
    $php_open . ' $__currentLoopData = $kolomDinamis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kolom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ' . $php_close => '@foreach($kolomDinamis as $kolom)',
    $php_open . ' endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ' . $php_close => '@endforeach',
    $php_open . ' endif; ' . $php_close => '@endif',
    
    $php_open . ' $__empty_1 = true; $__currentLoopData = $dataRingkasan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ' . $php_close => '@forelse($dataRingkasan as $index => $row)',
    $php_open . ' endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ' . $php_close => '@empty',
    
    $php_open . ' $__empty_1 = true; $__currentLoopData = $dataRincian; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ' . $php_close => '@forelse($dataRincian as $index => $item)',
    
    $php_open . ' if($item->tujuan == \'Eksternal\'): ' . $php_close => '@if($item->tujuan == \'Eksternal\')',
    $php_open . ' else: ' . $php_close => '@else',
    
    $php_open . ' if($kolom->tipe_input === \'currency\' && isset($tambahan[$kolom->nama_kolom])): ' . $php_close => '@if($kolom->tipe_input === \'currency\' && isset($tambahan[$kolom->nama_kolom]))',
    $php_open . ' elseif($kolom->tipe_input === \'date\'): ' . $php_close => '@elseif($kolom->tipe_input === \'date\')',
    $php_open . ' elseif($kolom->tipe_input === \'dropdown\'): ' . $php_close => '@elseif($kolom->tipe_input === \'dropdown\')',
    
    $php_open . ' if($kolom->pilihan_dropdown): ' . $php_close => '@if($kolom->pilihan_dropdown)',
    $php_open . ' $__currentLoopData = json_decode($kolom->pilihan_dropdown); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pilihan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ' . $php_close => '@foreach(json_decode($kolom->pilihan_dropdown) as $pilihan)',
    $php_open . ' if($errors->has(\'nama_kolom\') || $errors->has(\'tipe_input\')): ' . $php_close => '@if($errors->has(\'nama_kolom\') || $errors->has(\'tipe_input\'))',
];

$content = preg_replace('/<\?php echo e\((.*?)\); \?>/', '{{ $1 }}', $content);

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

$content = preg_replace('/<\?php if\((.*?)\): \?>/', '@if($1)', $content);
$content = preg_replace('/<\?php elseif\((.*?)\): \?>/', '@elseif($1)', $content);
$content = str_replace('<?php else: ?>', '@else', $content);
$content = str_replace('<?php endif; ?>', '@endif', $content);

file_put_contents('decompiled.blade.php', $content);
echo "Decompiled safely to decompiled.blade.php\n";
