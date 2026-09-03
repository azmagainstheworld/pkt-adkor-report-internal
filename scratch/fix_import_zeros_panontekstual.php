<?php

$file = 'app/Imports/PaNonTekstualImport.php';
$content = file_get_contents($file);

$target_hasData = <<<'EOF'
                    if (isset($typeMap[$namaKolomLower])) {
                        if (isset($row[$i]) && $row[$i] !== null && $row[$i] !== '' && (int)$row[$i] > 0) {
                            $hasData = true;
                            break;
                        }
                    }
EOF;

$replacement_hasData = <<<'EOF'
                    if (isset($typeMap[$namaKolomLower])) {
                        if (isset($row[$i]) && $row[$i] !== null && $row[$i] !== '') {
                            $hasData = true;
                            break;
                        }
                    }
EOF;

$content = str_replace($target_hasData, $replacement_hasData, $content);

file_put_contents($file, $content);
echo "Fixed 0 skipping in PaNonTekstualImport.\n";
