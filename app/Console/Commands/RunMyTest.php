<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:run-my-test')]
#[Description('Command description')]
class RunMyTest extends Command
{
    protected $signature = 'app:run-my-test';

    public function handle()
    {
        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PerizinanTerbitImport, 'referensi/Template_perizinan-terbit (1).xlsx');
            $this->info('Import succeeded. Total: ' . \App\Models\PerizinanTerbit::count());
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
