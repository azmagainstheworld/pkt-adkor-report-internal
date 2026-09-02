<?php
$path = 'resources/views/jasakurir.blade.php';
$content = file_get_contents($path);

// 1. Wrap Atur Kolom block
$content = preg_replace(
    '/(<div class="[^"]*Konfigurasi[^<]*<\/span>\s*<\/div>\s*<a[^>]*onclick="openModal\(\'modalAturKolom\'\)[^>]*>.*?<\/a>)/is',
    "@if(auth()->user()->isAdmin())\n$1\n@endif",
    $content
);

// 2. Wrap Atur Ekspedisi button
$content = preg_replace(
    '/(<x-button[^>]*onclick="openModal\(\'modalMasterKurir\'\)[^>]*>.*?Atur Ekspedisi\s*<\/x-button>)/is',
    "@if(auth()->user()->isAdmin())\n$1\n@endif",
    $content
);

// 3. Replace old Chart card with dynamic-chart component
$searchChartHtml = '/<x-card[^>]*>.*?Statistik Penggunaan Jasa Kurir.*?<canvas id="kurirChart"><\/canvas><\/div>\s*<\/x-card>/is';
$replaceChartHtml = <<<HTML
    <div class="mb-8">
        <x-dynamic-chart 
            id="kurirChart" 
            title="Statistik Penggunaan Jasa Kurir" 
            subtitle="Total volume pengiriman dokumen/barang per ekspedisi (Tahun: {{ \$tahunFilter }})" 
            type="bar" 
        />
    </div>
HTML;
$content = preg_replace($searchChartHtml, $replaceChartHtml, $content);

// 4. Update JS for dynamic-chart compatibility
// Change ID query
$content = str_replace("document.getElementById('kurirChart')", "document.getElementById('canvas_kurirChart')", $content);

// Register changeChartType globally and store instance
$searchChartNew = 'new Chart(ctx, {';
$replaceChartNew = <<<JS
            window.chartInstances = window.chartInstances || {};
            window.changeChartType = window.changeChartType || function(id, newType) {
                if (window.chartInstances && window.chartInstances[id]) {
                    window.chartInstances[id].config.type = newType;
                    window.chartInstances[id].update();
                }
            };
            window.chartInstances['kurirChart'] = new Chart(ctx, {
JS;
$content = str_replace($searchChartNew, $replaceChartNew, $content);

file_put_contents($path, $content);
echo "Jasa Kurir successfully updated!\n";
?>
