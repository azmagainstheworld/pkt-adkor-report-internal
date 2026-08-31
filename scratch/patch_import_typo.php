<?php
$f = 'app/Imports/PaNonTekstualImport.php';
$c = file_get_contents($f);
if (strpos($c, 'str_replace') === false) {
    $c = str_replace(
        "\$namaKolomLower = strtolower(\$namaKolomExcel);",
        "\$namaKolomLower = strtolower(\$namaKolomExcel);\n                    \$namaKolomLower = str_replace('tesktual', 'tekstual', \$namaKolomLower);",
        $c
    );
    file_put_contents($f, $c);
    echo "Patched typo handler in PaNonTekstualImport\n";
} else {
    echo "Already patched\n";
}
