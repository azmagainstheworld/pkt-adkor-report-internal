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
                    \App\Models\JasaKurirData::updateOrCreate(
                        ['jasa_kurir_id' => \$kurirId, 'tahun' => \$request->tahun, 'bulan' => \$request->bulan],
                        ['jumlah' => \$jumlah, 'data_tambahan' => \$dataTambahan]
                    );
                } else {
                    // Jika input dikosongkan saat edit, hapus data lama jika ada
                    \App\Models\JasaKurirData::where([
                        'jasa_kurir_id' => \$kurirId, 'tahun' => \$request->tahun, 'bulan' => \$request->bulan
                    ])->delete();
                }
            }
        }
        
        // Update kolom dinamis saja jika kurir kosong tapi ada kolom dinamis
        if (!empty(\$dataTambahan)) {
            // Karena tidak ada kurir spesifik, update kolom dinamis ke semua data di bulan itu
            \App\Models\JasaKurirData::where(['tahun' => \$request->tahun, 'bulan' => \$request->bulan])->update(['data_tambahan' => \$dataTambahan]);
        }
PHP;
// Let's do a more robust regex or just use str_replace on the validate part
$content = preg_replace('/\'kurir\'\s*=>\s*\'required\|array\'/', "'kurir' => 'nullable|array'", $content);
$content = preg_replace('/foreach\s*\(\$request->kurir\s*as\s*\$kurirId\s*=>\s*\$jumlah\)\s*\{/', "if(\$request->has('kurir') && is_array(\$request->kurir)) { foreach (\$request->kurir as \$kurirId => \$jumlah) {", $content);
$content = str_replace('}

        return redirect()->back()->with(\'success\',', '} } return redirect()->back()->with(\'success\',', $content);

file_put_contents($path, $content);
echo "Validation fixed!\n";
?>
