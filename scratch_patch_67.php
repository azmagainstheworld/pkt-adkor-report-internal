<?php
$path = 'app/Http/Controllers/JasaKurirController.php';
$content = file_get_contents($path);

$searchStoreData = <<<PHP
    public function storeData(Request \$request)
    {
        \$request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'kurir' => 'required|array',
            'kurir.*' => 'nullable|integer|min:0'
        ]);
PHP;
$replaceStoreData = <<<PHP
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
PHP;

$content = str_replace(str_replace("\r\n", "\n", $searchStoreData), str_replace("\r\n", "\n", $replaceStoreData), $content);
file_put_contents($path, $content);
echo "Validation messages updated!\n";
?>
