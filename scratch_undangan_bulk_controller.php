<?php
$content = file_get_contents('app/Http/Controllers/UndanganController.php');

$bulkMethod = <<<'EOD'
    public function destroyBulkDetail(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:undangan_details,id',
        ]);

        $details = \App\Models\UndanganDetail::whereIn('id', $request->ids)->get();
        $undangansToRecalculate = [];
        
        foreach($details as $d) {
            $undangansToRecalculate[$d->undangan_id] = $d->undangan;
        }

        \App\Models\UndanganDetail::whereIn('id', $request->ids)->delete();

        foreach($undangansToRecalculate as $undangan) {
            $this->recalculateUndangan($undangan);
        }

        return redirect()->back()->with('success', count($request->ids) . ' Rincian undangan berhasil dihapus.');
    }
EOD;

if (strpos($content, 'destroyBulkDetail') === false) {
    $content = str_replace('public function destroyDetail($id)', $bulkMethod . "\n\n    public function destroyDetail(\$id)", $content);
    file_put_contents('app/Http/Controllers/UndanganController.php', $content);
    echo "Added destroyBulkDetail method to UndanganController.php\n";
} else {
    echo "Method already exists.\n";
}
