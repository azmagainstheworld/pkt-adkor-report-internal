<?php

$file = 'app/Http/Controllers/PaNonTekstualController.php';
$content = file_get_contents($file);

$target = <<<'EOF'
        public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $count = 0;
        foreach($request->ids as $val) {
            $parts = explode('|', $val);
            if(count($parts) == 3) {
                $kelompok = $parts[0];
                $tahun = $parts[1];
                $bulan = $parts[2];
                \App\Models\PaNonTekstualData::where('kelompok_tabel', $kelompok)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data berhasil dihapus secara massal.');
    }
EOF;

$replacement = <<<'EOF'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        if ($request->input('delete_all') == '1') {
            $tahun = $request->input('filter_tahun', 'semua');
            $bulan = $request->input('filter_bulan', 'semua');
            
            $query = \App\Models\PaNonTekstualValue::query();
            if ($tahun !== 'semua') $query->where('tahun', $tahun);
            if ($bulan !== 'semua') $query->where('bulan', $bulan);
            
            $count = $query->delete();
            return back()->with('success', "Seluruh data berhasil dihapus secara massal.");
        }

        $request->validate([
            'ids' => 'required|array',
        ]);

        $count = 0;
        foreach($request->ids as $val) {
            $parts = explode('|', $val);
            if(count($parts) == 2) {
                $tahun = $parts[0];
                $bulan = $parts[1];
                \App\Models\PaNonTekstualValue::where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data terpilih berhasil dihapus secara massal.');
    }
EOF;

$content = str_replace($target, $replacement, $content);
file_put_contents($file, $content);
echo "PaNonTekstualController patched for destroyBulk.\n";
