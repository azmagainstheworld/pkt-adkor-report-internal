<?php
namespace App\Imports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class DebugImport implements ToCollection, WithHeadingRow {
    public function collection(Collection $rows) {
        if ($rows->count() > 0) {
            print_r($rows->first()->toArray());
        }
    }
}
