<?php
$f = 'app/Http/Controllers/DofController.php';
$c = file_get_contents($f);

$import = "    public function importExcel(Request \$request)
    {
        \$request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'kelompok' => 'required|in:tabel1,tabel2'
        ]);

        try {
            \$kelompokInt = \$request->kelompok == 'tabel1' ? 1 : 2;
            
            \$uuid = \$request->input('import_uuid', uniqid());
            
            DB::table('import_progress')->where('uuid', \$uuid)->delete();
            DB::table('import_progress')->insert([
                'uuid' => \$uuid,
                'total' => 1,
                'current' => 0,
                'percentage' => 0,
                'status' => 'processing',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Excel::import(new \App\Imports\DofImport(\$kelompokInt, \$uuid), \$request->file('file'));

            DB::table('import_progress')->where('uuid', \$uuid)->update([
                'status' => 'completed',
                'percentage' => 100,
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Data DOF berhasil diimport.']);
        } catch (\Exception \$e) {
            DB::table('import_progress')->where('uuid', \$uuid ?? 'error')->update([
                'status' => 'error',
                'updated_at' => now()
            ]);
            return response()->json(['error' => 'Gagal import: ' . \$e->getMessage()], 500);
        }
    }";

if (strpos($c, 'function importExcel') === false) {
    $c = preg_replace('/\}\s*$/', $import . "\n}", $c);
}

file_put_contents($f, $c);
echo "Added importExcel back\n";
