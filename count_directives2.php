<?php
$c = file_get_contents('resources/views/surat-masuk-keluar.blade.php');
$directives = ['auth', 'guest', 'isset', 'push', 'error', 'can'];
foreach ($directives as $d) {
    preg_match_all("/@{$d}\b/", $c, $m1);
    preg_match_all("/@end{$d}\b/", $c, $m2);
    if (count($m1[0]) !== count($m2[0])) {
        echo "Mismatch for $d: " . count($m1[0]) . " vs " . count($m2[0]) . "\n";
    }
}
echo "Check done.\n";
