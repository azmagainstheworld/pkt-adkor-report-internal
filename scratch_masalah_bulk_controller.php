<?php
$content = file_get_contents('app/Http/Controllers/MasalahKendalaController.php');

$bulkMethod = <<<'EOD'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:masalah_kendala,id',
        ]);

        \App\Models\MasalahKendala::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' Data masalah & kendala berhasil dihapus.');
    }
EOD;

if (strpos($content, 'destroyBulk') === false) {
    $content = str_replace('public function destroy($id)', $bulkMethod . "\n\n    public function destroy(\$id)", $content);
    file_put_contents('app/Http/Controllers/MasalahKendalaController.php', $content);
    echo "Added destroyBulk method to MasalahKendalaController.php\n";
} else {
    echo "Method already exists.\n";
}
