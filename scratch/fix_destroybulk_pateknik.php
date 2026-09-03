<?php

$file = 'app/Http/Controllers/PaTeknikController.php';
$content = file_get_contents($file);

$target_destroyBulk = <<<'EOF'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        if ($request->input('delete_all') == '1') {
            $tahun = $request->input('filter_tahun', 'semua');
            $bulan = $request->input('filter_bulan', 'semua');
            $kelompok = $request->input('kelompok_tabel', 1);
            
            $query = \App\Models\PaTeknikData::where('kelompok_tabel', $kelompok);
            if ($tahun !== 'semua') $query->where('tahun', $tahun);
            if ($bulan !== 'semua') $query->where('bulan', $bulan);
            
            $count = $query->delete();
            return back()->with('success', "Seluruh data Tabel $kelompok berhasil dihapus secara massal.");
        }

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
                \App\Models\PaTeknikData::where('kelompok_tabel', $kelompok)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data terpilih berhasil dihapus secara massal.');
    }
EOF;

$replacement_destroyBulk = <<<'EOF'
    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        if ($request->input('delete_all') == '1') {
            $tahun = $request->input('filter_tahun', 'semua');
            $bulan = $request->input('filter_bulan', 'semua');
            $kelompok = $request->input('kelompok_tabel', 1);
            
            $masterIds = \App\Models\PaTeknikMaster::where('kelompok_tabel', $kelompok)->pluck('id');
            
            $query = \App\Models\PaTeknikData::whereIn('master_id', $masterIds);
            if ($tahun !== 'semua') $query->where('tahun', $tahun);
            if ($bulan !== 'semua') $query->where('bulan', $bulan);
            
            $count = $query->delete();
            return back()->with('success', "Seluruh data Tabel $kelompok berhasil dihapus secara massal.");
        }

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
                
                $masterIds = \App\Models\PaTeknikMaster::where('kelompok_tabel', $kelompok)->pluck('id');
                \App\Models\PaTeknikData::whereIn('master_id', $masterIds)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data terpilih berhasil dihapus secara massal.');
    }
EOF;

$content = str_replace($target_destroyBulk, $replacement_destroyBulk, $content);
file_put_contents($file, $content);
echo "destroyBulk logic patched in PaTeknikController.\n";
