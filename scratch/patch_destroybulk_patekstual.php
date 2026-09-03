<?php

$file = 'app/Http/Controllers/PaTekstualController.php';
$content = file_get_contents($file);

$target = <<<'EOF'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);
EOF;

$replacement = <<<'EOF'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        if ($request->input('delete_all') == '1') {
            $kelompok = $request->input('kelompok_tabel');
            $tahun = $request->input('filter_tahun', 'semua');
            $bulan = $request->input('filter_bulan', 'semua');
            
            $query = \App\Models\PaTekstualData::where('kelompok_tabel', $kelompok);
            if ($tahun !== 'semua') $query->where('tahun', $tahun);
            if ($bulan !== 'semua') $query->where('bulan', $bulan);
            
            $count = $query->delete();
            return back()->with('success', "Seluruh data pemeliharaan berhasil dihapus.");
        }

        $request->validate([
            'ids' => 'required|array',
        ]);
EOF;

$content = str_replace($target, $replacement, $content);
file_put_contents($file, $content);
echo "Controller patched.\n";
