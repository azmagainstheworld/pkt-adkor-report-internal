<?php
function fixController($path) {
    if (!file_exists($path)) return;
    $content = file_get_contents($path);
    // Replace json response with redirect back
    $content = str_replace(
        "return response()->json(['success' => true]);", 
        "return redirect()->back()->with('success', 'Berhasil menghapus data secara massal.');", 
        $content
    );
    $content = str_replace(
        "return response()->json(['success' => false], 400);", 
        "return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');", 
        $content
    );
    file_put_contents($path, $content);
    echo "Fixed $path\n";
}

fixController('app/Http/Controllers/KaryawanController.php');
fixController('app/Http/Controllers/AnggaranController.php');
fixController('app/Http/Controllers/KetidakhadiranController.php');

// Also patch Undangan
$content = file_get_contents('app/Http/Controllers/UndanganController.php');
if (strpos($content, 'function destroyBulkDetail') === false) {
    $bulkMethod = <<<EOD

    public function destroyBulkDetail(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            \Illuminate\Support\Facades\DB::table('undangan_detail')->whereIn('id', \$ids)->delete();
            return redirect()->back()->with('success', 'Berhasil menghapus data rincian secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }
EOD;
    $content = preg_replace('/\}\s*$/', $bulkMethod . "\n}", $content);
    file_put_contents('app/Http/Controllers/UndanganController.php', $content);
    echo "Added destroyBulkDetail to UndanganController\n";
}
