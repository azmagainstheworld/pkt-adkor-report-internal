<?php
$path = 'app/Http/Controllers/AnggaranController.php';
$content = file_get_contents($path);

$oldCode = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            \App\Models\AnggaranAdministrasi::whereIn('id', \$ids)->delete();
            return redirect()->back()->with('success', 'Berhasil menghapus data secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }
PHP;

$newCode = <<<PHP
    public function destroyBulk(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        \Illuminate\Support\Facades\Log::info("Bulk delete IDs received: " . json_encode(\$ids));
        
        if (\$ids && is_array(\$ids)) {
            \$deleted = \App\Models\AnggaranAdministrasi::whereIn('id', \$ids)->delete();
            \Illuminate\Support\Facades\Log::info("Deleted count: " . \$deleted);
            return redirect()->back()->with('success', 'Berhasil menghapus data secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }
PHP;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($path, $content);
echo "Added logging to destroyBulk in AnggaranController\n";

$path2 = 'resources/views/anggaran/index.blade.php';
$content2 = file_get_contents($path2);
$content2 = str_replace('onsubmit="return confirm(\'Hapus data terpilih?\')"','', $content2);
file_put_contents($path2, $content2);
echo "Removed browser confirm from anggaran/index.blade.php\n";
?>
