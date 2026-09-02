<?php
$content = file_get_contents('app/Http/Controllers/PengirimanDokumenController.php');

$bulkMethod = <<<'EOD'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengiriman_dokumens,id',
        ]);

        \App\Models\PengirimanDokumen::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' Data pengiriman dokumen berhasil dihapus.');
    }
EOD;

if (strpos($content, 'destroyBulk') === false) {
    $content = str_replace('public function destroy($id)', $bulkMethod . "\n\n    public function destroy(\$id)", $content);
    file_put_contents('app/Http/Controllers/PengirimanDokumenController.php', $content);
    echo "Added destroyBulk method to PengirimanDokumenController.php\n";
} else {
    echo "Method already exists.\n";
}
