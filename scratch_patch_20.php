<?php

$path = 'resources/views/anggaran/index.blade.php';
$content = file_get_contents($path);

$oldCode = <<<JS
        document.getElementById('periode_input').value = data.tahun + '-' + monthNumStr;
        document.getElementById('kategori').value = data.kategori;
        document.getElementById('detail_anggaran').value = data.detail;
        document.getElementById('rkap').value = new Intl.NumberFormat('id-ID').format(data.rkap);
        document.getElementById('komitmen').value = new Intl.NumberFormat('id-ID').format(data.komitmen);
        document.getElementById('realisasi').value = new Intl.NumberFormat('id-ID').format(data.realisasi);
        document.getElementById('keterangan').value = data.keterangan;
JS;

$newCode = <<<JS
        document.getElementById('periode_input').value = data.tahun + '-' + monthNumStr;
        form.querySelector('select[name="kategori"]').value = data.kategori;
        form.querySelector('input[name="detail_anggaran"]').value = data.detail;
        
        document.getElementById('rkap_input').value = new Intl.NumberFormat('id-ID').format(data.rkap);
        document.getElementById('rkap_hidden').value = data.rkap;
        
        document.getElementById('komitmen_input').value = new Intl.NumberFormat('id-ID').format(data.komitmen);
        document.getElementById('komitmen_hidden').value = data.komitmen;
        
        document.getElementById('realisasi_input').value = new Intl.NumberFormat('id-ID').format(data.realisasi);
        document.getElementById('realisasi_hidden').value = data.realisasi;
        
        form.querySelector('textarea[name="keterangan"]').value = data.keterangan || '';
JS;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($path, $content);
echo "Fixed openModalEditAnggaran JS error\n";

?>
