<?php
$content = file_get_contents('app/Http/Controllers/JasaFotocopyController.php');

$bulkMethod = <<<'EOD'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:jasa_fotocopy,id',
        ]);

        \App\Models\JasaFotocopy::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' Data jasa fotocopy berhasil dihapus.');
    }
EOD;

if (strpos($content, 'destroyBulk') === false) {
    $content = str_replace('public function destroy($id)', $bulkMethod . "\n\n    public function destroy(\$id)", $content);
    file_put_contents('app/Http/Controllers/JasaFotocopyController.php', $content);
    echo "Added destroyBulk method to JasaFotocopyController.php\n";
} else {
    echo "Method already exists.\n";
}
