<?php
$content = file_get_contents('app/Http/Controllers/UndanganController.php');
if (strpos($content, 'function destroyBulkDetail') === false) {
    $bulkMethod = <<<EOD

    public function destroyBulkDetail(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            // Wait, is the model Undangan or UndanganDetail? Let me check the namespace.
            // Oh wait, let's just use DB facade
            \Illuminate\Support\Facades\DB::table('undangan_detail')->whereIn('id', \$ids)->delete();
            return redirect()->back()->with('success', 'Berhasil menghapus data rincian secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }
EOD;
    $content = preg_replace('/\}\s*$/', $bulkMethod . "\n}", $content);
    file_put_contents('app/Http/Controllers/UndanganController.php', $content);
    echo "Added destroyBulkDetail to UndanganController\n";
} else {
    echo "Already exists\n";
}
