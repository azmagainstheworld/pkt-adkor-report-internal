<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class UserFileImport implements ToArray, WithHeadingRow
{
    public function array(array $array)
    {
        echo "Headers parsed by Laravel Excel:\n";
        if (count($array) > 0) {
            foreach (array_keys($array[0]) as $key) {
                echo "- " . $key . "\n";
            }
            echo "\nFirst row data:\n";
            print_r($array[0]);
        } else {
            echo "Array is empty!\n";
        }
    }
}

$filePath = 'C:\Users\LENOVO\Downloads\Template_pemeliharaan-rutin (1).xlsx';
echo "Importing user file: $filePath\n";
Excel::import(new UserFileImport, $filePath);
