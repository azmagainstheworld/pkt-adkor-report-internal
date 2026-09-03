<?php

$filePath = 'app/Http/Controllers/PengirimanDokumenController.php';
$content = file_get_contents($filePath);

// Update index method
$indexOld = <<<PHP
        \$costRecords = clone \$query;
        \$costRecords = \$costRecords->get();
PHP;
$indexNew = <<<PHP
        \$costQuery = \App\Models\PengirimanOngkir::query();
        if (\$tahunFilter != 'semua') \$costQuery->where('tahun', \$tahunFilter);
        if (\$bulanFilter != 'semua') \$costQuery->where('bulan', \$bulanFilter);
        \$costQuery->orderBy('tahun', 'desc')->orderByRaw("FIELD(bulan, '" . implode("','", \$masterMonths) . "') DESC");
        \$costRecords = \$costQuery->get();
PHP;
$content = str_replace($indexOld, $indexNew, $content);

// Update chart logic in index method
$chartOld = <<<PHP
        foreach (\$tableData as \$row) {
            // Volume
            \$volumeChartConfigData['labels'][] = \$row->bulan . ' ' . \$row->tahun;
            \$volumeChartConfigData['datasets'][0]['data'][] = \$row->penerimaan_mailroom;
            \$volumeChartConfigData['datasets'][1]['data'][] = \$row->registrasi_surat_masuk_dof;
            \$volumeChartConfigData['datasets'][2]['data'][] = \$row->pengiriman_dalam_negeri;
            \$volumeChartConfigData['datasets'][3]['data'][] = \$row->pengiriman_luar_negeri;
            \$volumeChartConfigData['datasets'][4]['data'][] = \$row->e_materai;
            
            // Ongkir
            \$ongkirChartConfigData['labels'][] = \$row->bulan . ' ' . \$row->tahun;
            \$ongkirChartConfigData['datasets'][0]['data'][] = \$row->ongkir_dalam_negeri;
            \$ongkirChartConfigData['datasets'][1]['data'][] = \$row->ongkir_luar_negeri;
        }
PHP;
$chartNew = <<<PHP
        foreach (\$tableData as \$row) {
            // Volume
            \$volumeChartConfigData['labels'][] = \$row->bulan . ' ' . \$row->tahun;
            \$volumeChartConfigData['datasets'][0]['data'][] = \$row->penerimaan_mailroom;
            \$volumeChartConfigData['datasets'][1]['data'][] = \$row->registrasi_surat_masuk_dof;
            \$volumeChartConfigData['datasets'][2]['data'][] = \$row->pengiriman_dalam_negeri;
            \$volumeChartConfigData['datasets'][3]['data'][] = \$row->pengiriman_luar_negeri;
            \$volumeChartConfigData['datasets'][4]['data'][] = \$row->e_materai;
        }
        foreach (array_reverse(\$costRecords->toArray()) as \$rowArr) {
            \$row = (object)\$rowArr;
            // Ongkir
            \$ongkirChartConfigData['labels'][] = \$row->bulan . ' ' . \$row->tahun;
            \$ongkirChartConfigData['datasets'][0]['data'][] = \$row->ongkir_dalam_negeri;
            \$ongkirChartConfigData['datasets'][1]['data'][] = \$row->ongkir_luar_negeri;
        }
PHP;
$content = str_replace($chartOld, $chartNew, $content);

// Update store method
$storeOld = <<<PHP
        \$record = PengirimanDokumen::firstOrNew([
            'tahun' => \$request->tahun,
            'bulan' => \$request->bulan,
        ]);

        if (\$request->jenis_form === 'volume') {
            \$record->penerimaan_mailroom = \$request->mailroom;
            \$record->registrasi_surat_masuk_dof = \$request->surat_masuk;
            \$record->pengiriman_dalam_negeri = \$request->pengiriman_domestik;
            \$record->pengiriman_luar_negeri = \$request->pengiriman_internasional;
            \$record->e_materai = \$request->e_materai;
            // Data tambahan disimpan bersama array dinamis jika ada...
        } else if (\$request->jenis_form === 'ongkir') {
            \$record->ongkir_dalam_negeri = \$request->cost_domestik;
            \$record->ongkir_luar_negeri = \$request->cost_internasional;
        }

        // Ambil data tambahan (selain field bawaan Laravel/bawaan form khusus)
PHP;
$storeNew = <<<PHP
        if (\$request->jenis_form === 'volume') {
            \$record = PengirimanDokumen::firstOrNew([
                'tahun' => \$request->tahun,
                'bulan' => \$request->bulan,
            ]);
            \$record->penerimaan_mailroom = \$request->mailroom;
            \$record->registrasi_surat_masuk_dof = \$request->surat_masuk;
            \$record->pengiriman_dalam_negeri = \$request->pengiriman_domestik;
            \$record->pengiriman_luar_negeri = \$request->pengiriman_internasional;
            \$record->e_materai = \$request->e_materai;
        } else if (\$request->jenis_form === 'ongkir') {
            \$record = \App\Models\PengirimanOngkir::firstOrNew([
                'tahun' => \$request->tahun,
                'bulan' => \$request->bulan,
            ]);
            \$record->ongkir_dalam_negeri = \$request->cost_domestik;
            \$record->ongkir_luar_negeri = \$request->cost_internasional;
        }

        // Ambil data tambahan (selain field bawaan Laravel/bawaan form khusus)
PHP;
$content = str_replace($storeOld, $storeNew, $content);

// Update destroyBulk method
$destroyBulkOld = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$tipe = \$request->input('tipe_tabel'); // 'volume' atau 'ongkir'

        // 1. Ambil ID yang terpengaruh
        \$query = \App\Models\PengirimanDokumen::query();
        if (\$request->input('delete_all_pages') == '1') {
            if (\$request->filled('tahun') && \$request->tahun !== 'semua') {
                \$query->where('tahun', \$request->tahun);
            }
            if (\$request->filled('bulan') && \$request->bulan !== 'semua') {
                \$query->where('bulan', \$request->bulan);
            }
            \$affectedRecords = \$query->get();
        } else {
            \$request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:pengiriman_dokumen,id',
            ]);
            \$affectedRecords = \App\Models\PengirimanDokumen::whereIn('id', \$request->ids)->get();
        }

        \$count = count(\$affectedRecords);
        if (\$count == 0) {
            return redirect()->back()->with('success', 'Tidak ada data yang dihapus.');
        }

        // 2. Lakukan pengosongan berdasarkan tipe
        foreach (\$affectedRecords as \$record) {
            if (\$tipe === 'ongkir') {
                \$record->ongkir_dalam_negeri = 0;
                \$record->ongkir_luar_negeri = 0;
            } else {
                \$record->penerimaan_mailroom = 0;
                \$record->registrasi_surat_masuk_dof = 0;
                \$record->pengiriman_dalam_negeri = 0;
                \$record->pengiriman_luar_negeri = 0;
                \$record->e_materai = 0;
            }
            
            // Cek apakah KEDUANYA (volume & ongkir) sudah kosong (semua 0)
            if (\$record->penerimaan_mailroom == 0 &&
                \$record->registrasi_surat_masuk_dof == 0 &&
                \$record->pengiriman_dalam_negeri == 0 &&
                \$record->pengiriman_luar_negeri == 0 &&
                \$record->e_materai == 0 &&
                \$record->ongkir_dalam_negeri == 0 &&
                \$record->ongkir_luar_negeri == 0) {
                \$record->delete(); // Hapus seutuhnya jika kosong semua
            } else {
                \$record->save(); // Simpan perubahannya
            }
        }

        return redirect()->back()->with('success', \$count . ' Data berhasil dihapus.');
    }
PHP;

$destroyBulkNew = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$tipe = \$request->input('tipe_tabel'); // 'volume' atau 'ongkir'

        \$modelClass = \$tipe === 'ongkir' ? \App\Models\PengirimanOngkir::class : \App\Models\PengirimanDokumen::class;
        \$query = \$modelClass::query();

        if (\$request->input('delete_all_pages') == '1') {
            if (\$request->filled('tahun') && \$request->tahun !== 'semua') {
                \$query->where('tahun', \$request->tahun);
            }
            if (\$request->filled('bulan') && \$request->bulan !== 'semua') {
                \$query->where('bulan', \$request->bulan);
            }
            \$count = \$query->count();
            \$query->delete();
        } else {
            \$tableName = \$tipe === 'ongkir' ? 'pengiriman_ongkir' : 'pengiriman_dokumen';
            \$request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:' . \$tableName . ',id',
            ]);
            \$count = count(\$request->ids);
            \$modelClass::whereIn('id', \$request->ids)->delete();
        }

        return redirect()->back()->with('success', \$count . ' Data berhasil dihapus.');
    }
PHP;
$content = str_replace($destroyBulkOld, $destroyBulkNew, $content);

// Update single destroy method
$destroyOld = <<<PHP
    public function destroy(\$id, \Illuminate\Http\Request \$request)
    {
        try {
            \$record = PengirimanDokumen::findOrFail(\$id);
            \$tipe = \$request->query('tipe');

            if (\$tipe === 'ongkir') {
                \$record->ongkir_dalam_negeri = 0;
                \$record->ongkir_luar_negeri = 0;
            } elseif (\$tipe === 'volume') {
                \$record->penerimaan_mailroom = 0;
                \$record->registrasi_surat_masuk_dof = 0;
                \$record->pengiriman_dalam_negeri = 0;
                \$record->pengiriman_luar_negeri = 0;
                \$record->e_materai = 0;
            } else {
                \$record->delete();
                return redirect()->back()->with('success', 'Data berhasil dihapus.');
            }

            if (\$record->penerimaan_mailroom == 0 &&
                \$record->registrasi_surat_masuk_dof == 0 &&
                \$record->pengiriman_dalam_negeri == 0 &&
                \$record->pengiriman_luar_negeri == 0 &&
                \$record->e_materai == 0 &&
                \$record->ongkir_dalam_negeri == 0 &&
                \$record->ongkir_luar_negeri == 0) {
                \$record->delete();
            } else {
                \$record->save();
            }

            return redirect()->back()->with('success', 'Data berhasil dihapus dari tabel.');
        } catch (\Exception \$e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
PHP;

$destroyNew = <<<PHP
    public function destroy(\$id, \Illuminate\Http\Request \$request)
    {
        try {
            \$tipe = \$request->query('tipe');
            if (\$tipe === 'ongkir') {
                \App\Models\PengirimanOngkir::findOrFail(\$id)->delete();
            } else {
                PengirimanDokumen::findOrFail(\$id)->delete();
            }
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Exception \$e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
PHP;
$content = str_replace($destroyOld, $destroyNew, $content);

file_put_contents($filePath, $content);
echo "PengirimanDokumenController updated successfully.\n";
