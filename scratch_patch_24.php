<?php
$path = 'app/Http/Controllers/AnggaranController.php';
$content = file_get_contents($path);

// Fix index method to accept both 'tahun'/'year' and 'bulan'/'month'
$oldIndex = <<<PHP
        // 'all' = opsi "Semua Tahun" / "Semua Bulan"
        \$selectedYear = \$request->input('year', 'all');
        \$selectedMonth = \$request->input('month', 'all');
PHP;

$newIndex = <<<PHP
        // 'all' = opsi "Semua Tahun" / "Semua Bulan"
        \$selectedYear = \$request->input('year', \$request->input('tahun', 'all'));
        \$selectedMonth = \$request->input('month', \$request->input('bulan', 'all'));
PHP;

$content = str_replace($oldIndex, $newIndex, $content);

// Also fix the redirects just in case
$content = str_replace("['tahun' => \$request->tahun, 'bulan' => \$request->bulan]", "['year' => \$request->tahun, 'month' => \$request->bulan]", $content);
$content = str_replace("['tahun' => \$validated['tahun'], 'bulan' => \$validated['bulan']]", "['year' => \$validated['tahun'], 'month' => \$validated['bulan']]", $content);
$content = str_replace("['tahun' => \$tahun, 'bulan' => \$bulan]", "['year' => \$tahun, 'month' => \$bulan]", $content);

file_put_contents($path, $content);
echo "Fixed URL parameter mismatch year/month vs tahun/bulan in AnggaranController\n";

?>
