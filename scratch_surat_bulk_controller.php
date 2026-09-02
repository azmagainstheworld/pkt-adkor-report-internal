<?php
$content = file_get_contents('app/Http/Controllers/SuratController.php');

$bulkMethod = <<<'EOD'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);

        \App\Models\Surat::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' Data surat berhasil dihapus.');
    }
EOD;

if (strpos($content, 'destroyBulk') === false) {
    $content = str_replace('public function destroy($id)', $bulkMethod . "\n\n    public function destroy(\$id)", $content);
    file_put_contents('app/Http/Controllers/SuratController.php', $content);
    echo "Added destroyBulk method to SuratController.php\n";
} else {
    echo "Method already exists.\n";
}
