<?php
$bladePath = 'D:\web_pkt_adkor_internal\adkor-report-internal\resources\views\perizinan-perkantoran.blade.php';
$b = file_get_contents($bladePath);

$js = <<<'HTML'
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            @if(old('nama_perizinan') || old('nomor'))
                openModal('modalPerizinanTerbit');
            @elseif(old('nama_proses') || old('target'))
                openModal('modalPerizinanProses');
            @endif
        @endif
    });
HTML;

if (!str_contains($b, "openModal('modalPerizinanTerbit')") || !str_contains($b, "DOMContentLoaded")) {
    $b = str_replace('</script>', "\n" . $js . "\n</script>", $b);
    file_put_contents($bladePath, $b);
    echo "Injected JS!\n";
} else {
    echo "JS already injected!\n";
}
