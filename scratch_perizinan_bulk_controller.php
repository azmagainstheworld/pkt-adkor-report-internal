<?php
$content = file_get_contents('app/Http/Controllers/PerizinanPerkantoranController.php');

$bulkMethodTerbit = <<<'EOD'
    public function destroyBulkTerbit(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perizinan_terbit,id',
        ]);

        \App\Models\PerizinanTerbit::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' Data perizinan terbit berhasil dihapus.');
    }
EOD;

$bulkMethodProses = <<<'EOD'
    public function destroyBulkProses(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perizinan_proses_lists,id',
        ]);

        \App\Models\PerizinanProsesList::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' Data perizinan proses berhasil dihapus.');
    }
EOD;

if (strpos($content, 'destroyBulkTerbit') === false) {
    $content = str_replace('public function destroy($id)', $bulkMethodTerbit . "\n\n    public function destroy(\$id)", $content);
}
if (strpos($content, 'destroyBulkProses') === false) {
    $content = str_replace('public function destroyProses($id)', $bulkMethodProses . "\n\n    public function destroyProses(\$id)", $content);
}

file_put_contents('app/Http/Controllers/PerizinanPerkantoranController.php', $content);
echo "Added destroyBulk methods to PerizinanPerkantoranController.php\n";
