<?php
$exportPath = 'app/Exports/JasaFotocopyExport.php';
$exportContent = file_get_contents($exportPath);

$searchArray = <<<PHP
    public function array(): array {
        \$query = JasaFotocopy::query();
PHP;
$replaceArray = <<<PHP
    public function array(): array {
        if (\$this->isTemplate) return [];
        \$query = JasaFotocopy::query();
PHP;
$exportContent = str_replace(str_replace("\r\n", "\n", $searchArray), str_replace("\r\n", "\n", $replaceArray), $exportContent);

$searchHeadings = <<<PHP
    public function headings(): array { 
        return ['Tahun', 'Bulan', 'Mesin FC', 'Jumlah Pemakaian Jasa Penyediaan Fotocopy', 'Nilai Jasa Penyediaan Fotocopy']; 
    }
PHP;
$replaceHeadings = <<<PHP
    public function headings(): array { 
        if (\$this->isTemplate) {
            return [
                'NO', 'Tahun', 'Bulan', 'UNIT KERJA', 'Cost Centre', 
                'Jlh pemakaian (Bln Terkait)', 'Jlh pemakaian (s.d. Bln Terkait)', 
                'Ket.', 'Type mesin', 'Biaya fee bulan Bln Terkait', 
                'Biaya fee s.d. bulan Bln Terkait', 'Biaya fee/Lbr', 
                'Biaya sewa/bulan', 'Biaya Jasa Sewa bln Januari & Fee Bln Terkait', 
                'Total biaya Sewa & Fee s.d. bln Bln Terkait'
            ];
        }
        return ['Tahun', 'Bulan', 'Mesin FC', 'Jumlah Pemakaian Jasa Penyediaan Fotocopy', 'Nilai Jasa Penyediaan Fotocopy']; 
    }
PHP;
$exportContent = str_replace(str_replace("\r\n", "\n", $searchHeadings), str_replace("\r\n", "\n", $replaceHeadings), $exportContent);
file_put_contents($exportPath, $exportContent);
echo "JasaFotocopyExport updated!\n";

$importPath = 'app/Imports/JasaFotocopyImport.php';
$importContent = file_get_contents($importPath);

$searchImport = <<<PHP
                JasaFotocopyData::updateOrCreate(
                    ['master_id' => \$master->id, 'tahun' => \$row['tahun'], 'bulan' => trim(\$row['bulan'])],
                    [
                        'pemakaian_lembar' => (int) (\$row['pemakaian_lbr'] ?? 0),
                        'biaya_fee_per_lembar' => (float) (\$row['fee_lbr'] ?? 47.22),
                        'biaya_sewa_mesin' => (float) (\$row['sewa_bln'] ?? 909000)
                    ]
                );
PHP;
$replaceImport = <<<PHP
                JasaFotocopyData::updateOrCreate(
                    ['master_id' => \$master->id, 'tahun' => \$row['tahun'], 'bulan' => trim(\$row['bulan'])],
                    [
                        'pemakaian_lembar' => (int) (\$row['jlh_pemakaian_bln_terkait'] ?? \$row['pemakaian_lbr'] ?? 0),
                        'biaya_fee_per_lembar' => (float) (\$row['biaya_feelbr'] ?? \$row['fee_lbr'] ?? 47.22),
                        'biaya_sewa_mesin' => (float) (\$row['biaya_sewabulan'] ?? \$row['sewa_bln'] ?? 909000)
                    ]
                );
PHP;
$importContent = str_replace(str_replace("\r\n", "\n", $searchImport), str_replace("\r\n", "\n", $replaceImport), $importContent);
file_put_contents($importPath, $importContent);
echo "JasaFotocopyImport updated!\n";
?>
