<?php
$path = 'resources/views/pengiriman-dokumen.blade.php';
$content = file_get_contents($path);

// 1. Fix bulkDeleteFormVolume
// It might look like: <form id="bulkDeleteFormVolume" action="{{ route('pengiriman-dokumen.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data volume dokumen terpilih?')">
$content = preg_replace(
    '/<form\s+id="bulkDeleteFormVolume"[^>]*>/i',
    '<form id="bulkDeleteFormVolume" action="{{ route(\'pengiriman-dokumen.destroyBulk\') }}" method="POST">
            <input type="hidden" id="deleteAllPagesVolume" name="delete_all_pages" value="0">',
    $content
);

// 2. Fix bulkDeleteFormOngkir
// It might look like: <form id="bulkDeleteFormOngkir" action="{{ route('pengiriman-dokumen.destroyBulk') }}" method="POST" onsubmit="return confirm('Hapus data biaya ongkir terpilih?')">
$content = preg_replace(
    '/<form\s+id="bulkDeleteFormOngkir"[^>]*>/i',
    '<form id="bulkDeleteFormOngkir" action="{{ route(\'pengiriman-dokumen.destroyBulk\') }}" method="POST">
            <input type="hidden" id="deleteAllPagesOngkir" name="delete_all_pages" value="0">',
    $content
);

file_put_contents($path, $content);
echo "Frontend forms fixed successfully!\n";
?>
