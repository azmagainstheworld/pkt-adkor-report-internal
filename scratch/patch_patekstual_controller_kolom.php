<?php

$file = 'app/Http/Controllers/PaTekstualController.php';
$content = file_get_contents($file);

// 1. Add PaTekstualKolom use statement
if (strpos($content, 'use App\Models\PaTekstualKolom;') === false) {
    $content = str_replace('use App\Models\PaTekstualMaster;', "use App\Models\PaTekstualMaster;\nuse App\Models\PaTekstualKolom;", $content);
}

// 2. Fetch kolomTabel1 and kolomTabel2 in index()
$target_index1 = <<<'EOF'
        $masterTabel1 = PaTekstualMaster::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        $masterTabel2 = PaTekstualMaster::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();
EOF;
$replacement_index1 = <<<'EOF'
        $masterTabel1 = PaTekstualMaster::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        $masterTabel2 = PaTekstualMaster::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();
        
        $kolomTabel1 = PaTekstualKolom::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        $kolomTabel2 = PaTekstualKolom::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();
EOF;
$content = str_replace($target_index1, $replacement_index1, $content);

// 3. Extract data_tambahan into groupedData
$target_index2 = <<<'EOF'
            $rowTabel1 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];
            $rowTabel2 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];

            foreach($items as $item) {
EOF;
$replacement_index2 = <<<'EOF'
            $rowTabel1 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];
            $rowTabel2 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];

            foreach($items as $item) {
                if (!empty($item->data_tambahan)) {
                    if ($item->masterTekstual->kelompok_tabel == 1) {
                        $rowTabel1['data_tambahan'] = array_merge($rowTabel1['data_tambahan'], $item->data_tambahan);
                    } else {
                        $rowTabel2['data_tambahan'] = array_merge($rowTabel2['data_tambahan'], $item->data_tambahan);
                    }
                }
EOF;
$content = str_replace($target_index2, $replacement_index2, $content);

// 4. Pass them to view
$target_index3 = <<<'EOF'
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'masterTabel1', 'masterTabel2', 'paginatedTable1', 'paginatedTable2', 'dataTable1', 'dataTable2',
            'totalsTabel1', 'totalsTabel2'
EOF;
$replacement_index3 = <<<'EOF'
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'masterTabel1', 'masterTabel2', 'kolomTabel1', 'kolomTabel2', 'paginatedTable1', 'paginatedTable2', 'dataTable1', 'dataTable2',
            'totalsTabel1', 'totalsTabel2'
EOF;
$content = str_replace($target_index3, $replacement_index3, $content);

// 5. Update updateBulan to save data_tambahan
$target_updateBulan = <<<'EOF'
    public function updateBulan(Request $request)
    {
        // Validasi Ketat agar user tidak mengosongkan form
        $request->validate([
            'tahun' => 'required',
            'bulan' => 'required',
            'items' => 'required|array',
            'items.*' => 'required|integer|min:0'
        ], [
            'items.*.required' => 'Semua kolom jumlah wajib diisi!'
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;

        if ($request->has('items')) {
            foreach ($request->items as $m_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    // Update langsung pada tahun & bulan tersebut
                    PaTekstualData::updateOrCreate(
                        ['tahun' => $tahun, 'bulan' => $bulan, 'master_id' => $m_id],
                        ['jumlah' => $jumlah]
                    );
                }
            }
        }
        
        return back()->with('success', "Data periode $bulan $tahun berhasil diperbarui.");
    }
EOF;
$replacement_updateBulan = <<<'EOF'
    public function updateBulan(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'bulan' => 'required',
            'items' => 'required|array',
            'items.*' => 'required|integer|min:0'
        ], [
            'items.*.required' => 'Semua kolom jumlah wajib diisi!'
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $dataTambahan = $request->input('data_tambahan', []);

        if ($request->has('items')) {
            $isFirst = true;
            foreach ($request->items as $m_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    $payload = ['jumlah' => $jumlah];
                    if ($isFirst) {
                        $payload['data_tambahan'] = $dataTambahan;
                        $isFirst = false;
                    }
                    
                    PaTekstualData::updateOrCreate(
                        ['tahun' => $tahun, 'bulan' => $bulan, 'master_id' => $m_id],
                        $payload
                    );
                }
            }
        }
        
        return back()->with('success', "Data periode $bulan $tahun berhasil diperbarui.");
    }
EOF;
$content = str_replace($target_updateBulan, $replacement_updateBulan, $content);

// 6. Add storeKolom and destroyKolom methods
$target_kolom = <<<'EOF'
    public function destroyMaster($id)
    {
        $master = PaTekstualMaster::findOrFail($id);
        $master->delete();
        return back()->with('success', 'Kolom kegiatan/dokumen berhasil dihapus beserta seluruh datanya.');
    }
EOF;
$replacement_kolom = <<<'EOF'
    public function destroyMaster($id)
    {
        $master = PaTekstualMaster::findOrFail($id);
        $master->delete();
        return back()->with('success', 'Kolom kegiatan/dokumen berhasil dihapus beserta seluruh datanya.');
    }

    public function storeKolom(Request $request)
    {
        $request->validate([
            'kelompok_tabel' => 'required|in:1,2',
            'nama_kolom' => 'required|string|max:255',
            'tipe_input' => 'required|in:text,number,currency'
        ]);

        PaTekstualKolom::create([
            'kelompok_tabel' => $request->kelompok_tabel,
            'nama_kolom' => trim($request->nama_kolom),
            'tipe_input' => $request->tipe_input
        ]);

        return back()->with('success', 'Kolom tambahan berhasil dibuat.');
    }

    public function destroyKolom($id)
    {
        $kolom = PaTekstualKolom::findOrFail($id);
        $kolom->delete();
        return back()->with('success', 'Kolom tambahan berhasil dihapus.');
    }
EOF;
$content = str_replace($target_kolom, $replacement_kolom, $content);


// 7. Update storeDokumen to remove support for 'tambah_baru'
$target_storeDokumen = <<<'EOF'
    public function storeDokumen(Request $request)
    {
        $request->validate([
            'kelompok_tabel' => 'required|in:1,2', 
            'tahun' => 'required|integer', 
            'bulan' => 'required|string', 
            'jumlah' => 'required|integer|min:0'
        ]);
        
        $master_id = $request->master_id;

        if ($master_id == 'tambah_baru' && $request->filled('nama_kegiatan_baru')) {
            $master = PaTekstualMaster::create([
                'kelompok_tabel' => $request->kelompok_tabel, 
                'nama_dokumen' => $request->nama_kegiatan_baru
            ]);
            $master_id = $master->id;
        }

        if (!$master_id || $master_id == 'tambah_baru') return back()->withErrors(['Variabel dokumen tidak valid.']);

        // Logika Akumulasi (Tambah jumlah jika data sudah ada)
EOF;
$replacement_storeDokumen = <<<'EOF'
    public function storeDokumen(Request $request)
    {
        $request->validate([
            'kelompok_tabel' => 'required|in:1,2', 
            'tahun' => 'required|integer', 
            'bulan' => 'required|string', 
            'jumlah' => 'required|integer|min:0',
            'master_id' => 'required|exists:pa_tekstual_master,id'
        ]);
        
        $master_id = $request->master_id;

        // Logika Akumulasi (Tambah jumlah jika data sudah ada)
EOF;
$content = str_replace($target_storeDokumen, $replacement_storeDokumen, $content);

file_put_contents($file, $content);
echo "PaTekstualController patched for dynamic columns.\n";
