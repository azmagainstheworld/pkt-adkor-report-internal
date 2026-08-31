<?php
$f = 'app/Http/Controllers/DofController.php';
$c = file_get_contents($f);

$import = "    public function importExcel(Request \$request)
    {
        set_time_limit(0);
        \$request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'kelompok' => 'required|in:tabel1,tabel2'
        ]);

        try {
            \$kelompokInt = \$request->kelompok == 'tabel1' ? 1 : 2;
            \$uuid = \$request->input('import_uuid', uniqid());
            
            Excel::import(new \App\Imports\DofImport(\$kelompokInt, \$uuid), \$request->file('file'));

            return response()->json(['success' => true, 'message' => 'Data DOF berhasil diimport.']);
        } catch (\Exception \$e) {
            return response()->json(['error' => 'Gagal import: ' . \$e->getMessage()], 500);
        }
    }";

$c = preg_replace('/public function importExcel\(Request \$request\).*?^\s*\}/ms', $import, $c);

file_put_contents($f, $c);
echo "Fixed importExcel DB progress tracking bug\n";
