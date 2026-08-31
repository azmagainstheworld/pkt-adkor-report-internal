<?php
$files = [
    'app/Imports/PaNonTekstualImport.php' => [
        'search' => "if (empty(\$tahun) || empty(\$bulan)) continue;",
        'replace' => "if (empty(\$tahun) || empty(\$bulan)) continue;\n\n                // Check if all data columns are empty or zero\n                \$hasData = false;\n                for (\$i = 2; \$i < count(\$headers); \$i++) {\n                    if (isset(\$row[\$i]) && \$row[\$i] !== null && \$row[\$i] !== '' && (int)\$row[\$i] > 0) {\n                        \$hasData = true;\n                        break;\n                    }\n                }\n                if (!\$hasData) continue;\n"
    ],
    'app/Imports/PaTekstualImport.php' => [
        'search' => "if (empty(\$tahun) || empty(\$bulan)) continue;",
        'replace' => "if (empty(\$tahun) || empty(\$bulan)) continue;\n\n                // Check if all data columns are empty or zero\n                \$hasData = false;\n                for (\$i = 2; \$i < count(\$headers); \$i++) {\n                    if (isset(\$row[\$i]) && \$row[\$i] !== null && \$row[\$i] !== '' && (int)\$row[\$i] > 0) {\n                        \$hasData = true;\n                        break;\n                    }\n                }\n                if (!\$hasData) continue;\n"
    ]
];

foreach ($files as $file => $data) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        if (strpos($c, '$hasData = false;') === false) {
            $c = str_replace($data['search'], $data['replace'], $c);
            file_put_contents($file, $c);
            echo "Patched $file\n";
        } else {
            echo "Already patched $file\n";
        }
    }
}
