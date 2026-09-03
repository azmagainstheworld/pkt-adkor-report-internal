<?php

$filePath = 'app/Http/Controllers/PengirimanDokumenController.php';
$content = file_get_contents($filePath);

$destroyBulkOld = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        // Fitur Hapus Semua (Delete All Pages)
        if (\$request->input('delete_all_pages') == '1') {
            \$query = \App\Models\PengirimanDokumen::query();
            
            if (\$request->filled('tahun') && \$request->tahun !== 'semua') {
                \$query->where('tahun', \$request->tahun);
            }
            if (\$request->filled('bulan') && \$request->bulan !== 'semua') {
                \$query->where('bulan', \$request->bulan);
            }
            
            \$count = \$query->count();
            \$query->delete();
            
            return redirect()->back()->with('success', \$count . ' Data pengiriman dokumen (seluruh halaman) berhasil dihapus.');
        }

        // Hapus Massal Biasa
        \$request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengiriman_dokumen,id',
        ]);

        \App\Models\PengirimanDokumen::whereIn('id', \$request->ids)->delete();
        return redirect()->back()->with('success', count(\$request->ids) . ' Data pengiriman dokumen berhasil dihapus.');
    }
PHP;

$destroyBulkNew = <<<PHP
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

$content = str_replace($destroyBulkOld, $destroyBulkNew, $content);

file_put_contents($filePath, $content);
echo "Controller patched successfully.\n";
