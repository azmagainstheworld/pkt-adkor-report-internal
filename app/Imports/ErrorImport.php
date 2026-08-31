<?php
namespace App\Imports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
class ErrorImport implements ToCollection {
    public function collection(Collection $rows) {
        $row = collect(['a' => 1]);
        array_key_exists('a', $row); // This will throw TypeError
    }
}
