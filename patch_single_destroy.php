<?php

$filePath = 'app/Http/Controllers/PengirimanDokumenController.php';
$content = file_get_contents($filePath);

$destroyOld = <<<PHP
    public function destroy(\$id)
    {
        try {
            PengirimanDokumen::findOrFail(\$id)->delete();
            return redirect()->back()->with('success', 'Data pengiriman dokumen berhasil dihapus.');
        } catch (\Exception \$e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
PHP;

$destroyNew = <<<PHP
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

$content = str_replace($destroyOld, $destroyNew, $content);

file_put_contents($filePath, $content);
echo "Single destroy patched successfully.\n";
