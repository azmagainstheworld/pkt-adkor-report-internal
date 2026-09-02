<?php
// 1. PATCH JasaFotocopyExport.php
$exportPath = 'app/Exports/JasaFotocopyExport.php';
$exportContent = file_get_contents($exportPath);

$searchConstruct = <<<PHP
    public \$isTemplate = false;

    protected \$tahun; protected \$bulan;

    public function __construct(\$tahun = 'semua', \$bulan = 'semua') {
        \$this->tahun = \$tahun; \$this->bulan = \$bulan;
    }
PHP;

$replaceConstruct = <<<PHP
    public \$isTemplate = false;

    protected \$tahun; protected \$bulan;

    public function __construct(\$isTemplate = false, \$tahun = 'semua', \$bulan = 'semua') {
        \$this->isTemplate = \$isTemplate;
        \$this->tahun = \$tahun; \$this->bulan = \$bulan;
    }
PHP;

$exportContent = str_replace(str_replace("\r\n", "\n", $searchConstruct), str_replace("\r\n", "\n", $replaceConstruct), $exportContent);
file_put_contents($exportPath, $exportContent);
echo "JasaFotocopyExport updated!\n";

// 2. PATCH JasaFotocopyController.php
$ctrlPath = 'app/Http/Controllers/JasaFotocopyController.php';
$ctrlContent = file_get_contents($ctrlPath);

$searchExport = "return Excel::download(new \App\Exports\JasaFotocopyExport(\$tahun, \$bulan), 'Data_Jasa_Fotocopy.xlsx');";
$replaceExport = "return Excel::download(new \App\Exports\JasaFotocopyExport(false, \$tahun, \$bulan), 'Data_Jasa_Fotocopy.xlsx');";
$ctrlContent = str_replace($searchExport, $replaceExport, $ctrlContent);

file_put_contents($ctrlPath, $ctrlContent);
echo "JasaFotocopyController updated!\n";
?>
