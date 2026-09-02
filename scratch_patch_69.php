<?php
$path = 'app/Http/Controllers/JasaKurirController.php';
$content = file_get_contents($path);

$searchStoreData = <<<PHP
    public function storeData(Request \$request)
    {
        \$request->validate([
            'tahun' => 'required',
            'bulan' => 'required',
            'kurir' => 'required|array',
            'kurir.*' => 'nullable|integer|min:0'
        ], [
            'tahun.required' => 'Tahun wajib diisi!',
            'bulan.required' => 'Bulan wajib diisi!',
            'kurir.required' => 'Data kurir wajib diisi minimal 1!',
            'kurir.*.integer' => 'Data kurir harus berupa angka!',
            'kurir.*.min' => 'Data kurir tidak boleh kurang dari 0!'
        ]);

        \$dataTambahan = \$request->input('data_tambahan', []);

        foreach (\$request->kurir as \$kurirId => \$jumlah) {
            // Jika input ada nilainya, Update atau Create
            if (\$jumlah !== null && \$jumlah !== '') {
                JasaKurirData::updateOrCreate(
                    ['jasa_kurir_id' => \$kurirId, 'tahun' => \$request->tahun, 'bulan' => \$request->bulan],
                    ['jumlah' => \$jumlah, 'data_tambahan' => \$dataTambahan]
                );
            } else {
                // Jika input dikosongkan saat edit, hapus data lama jika ada
                JasaKurirData::where([
                    'jasa_kurir_id' => \$kurirId, 'tahun' => \$request->tahun, 'bulan' => \$request->bulan
                ])->delete();
            }
        }

        return redirect()->back()->with('success', 'Data Jasa Kurir berhasil disimpan.');
    }
PHP;

$replaceStoreData = <<<PHP
    public function storeData(Request \$request)
    {
        \$request->validate([
            'tahun' => 'required',
            'bulan' => 'required',
            'kurir' => 'nullable|array',
            'kurir.*' => 'nullable|integer|min:0'
        ], [
            'tahun.required' => 'Tahun wajib diisi!',
            'bulan.required' => 'Bulan wajib diisi!',
            'kurir.*.integer' => 'Data kurir harus berupa angka!',
            'kurir.*.min' => 'Data kurir tidak boleh kurang dari 0!'
        ]);

        \$dataTambahan = \$request->input('data_tambahan', []);

        if (\$request->has('kurir') && is_array(\$request->kurir)) {
            foreach (\$request->kurir as \$kurirId => \$jumlah) {
                // Jika input ada nilainya, Update atau Create
                if (\$jumlah !== null && \$jumlah !== '') {
                    JasaKurirData::updateOrCreate(
                        ['jasa_kurir_id' => \$kurirId, 'tahun' => \$request->tahun, 'bulan' => \$request->bulan],
                        ['jumlah' => \$jumlah, 'data_tambahan' => \$dataTambahan]
                    );
                } else {
                    // Jika input dikosongkan saat edit, hapus data lama jika ada
                    JasaKurirData::where([
                        'jasa_kurir_id' => \$kurirId, 'tahun' => \$request->tahun, 'bulan' => \$request->bulan
                    ])->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'Data Jasa Kurir berhasil disimpan.');
    }
PHP;

$content = str_replace(str_replace("\r\n", "\n", $searchStoreData), str_replace("\r\n", "\n", $replaceStoreData), $content);
file_put_contents($path, $content);
echo "Validation fixed properly!\n";
?>
