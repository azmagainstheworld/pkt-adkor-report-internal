<?php
// Remove duplicate methods from ProgramStrategisController.php
$file = 'app/Http/Controllers/ProgramStrategisController.php';
$content = file_get_contents($file);

// The duplicate block is between lines 258-309 (the injected duplicate storeKolomDinamis...exportExcel)
// We need to remove from "    public function storeKolomDinamis" (the first one at line 258)
// up to and including "    }\r\n\r\n    public function exportPdf" (keeping exportPdf)

// Strategy: remove everything from first storeKolomDinamis to start of exportPdf (second instance)
// The block we want to remove:
$toRemove = "    public function storeKolomDinamis(Request \$request)\n    {\n        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');\n        \$request->validate([\n            'modul'      => 'required|string',\n            'nama_kolom' => 'required|string|max:100',\n            'tipe_input' => 'required|in:text,number,date,dropdown,currency',\n        ]);\n\n        \$isDuplicate = DB::table('dynamic_columns')->where('modul', \$request->modul)\n            ->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim(\$request->nama_kolom))])->exists();\n\n        if (\$isDuplicate) return back()->with('error_modal', 'Kolom sudah ada!');\n\n        \$pilihanDropdown = null;\n        if (\$request->tipe_input === 'dropdown' && \$request->pilihan_dropdown) {\n            \$pilihanDropdown = json_encode(array_map('trim', explode(',', \$request->pilihan_dropdown)));\n        }\n\n        DB::table('dynamic_columns')->insert([\n            'modul' => \$request->modul, 'nama_kolom' => trim(\$request->nama_kolom),\n            'tipe_input' => \$request->tipe_input, 'pilihan_dropdown' => \$pilihanDropdown,\n            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),\n        ]);\n\n        return back()->with('success', 'Kolom dinamis baru berhasil ditambahkan.');\n    }\n\n    public function destroyKolomDinamis(\$id)\n    {\n        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');\n        DB::table('dynamic_columns')->where('id', \$id)->delete();\n        return back()->with('success', 'Kolom dinamis berhasil dihapus.');\n    }\n\n    public function import(Request \$request)\n    {\n        set_time_limit(0);\n        \$request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:51200']);\n        try {\n            Excel::import(new \\App\\Imports\\ProgramStrategisImport, \$request->file('file'));\n            return redirect()->back()->with('success', 'Data berhasil di-import!');\n        } catch (\\Exception \$e) {\n            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import: ' . \$e->getMessage());\n        }\n    }\n\n    public function exportExcel(Request \$request)\n    {\n        \$tahun = \$request->input('tahun', 'semua');\n        return Excel::download(new \\App\\Exports\\ProgramStrategisExport(false, \$tahun), 'Data_Program_Strategis_'.\$tahun.'.xlsx');\n    }\n\n    public function exportPdf";

$replacement = "    public function exportPdf";

if (strpos($content, $toRemove) !== false) {
    $content = str_replace($toRemove, $replacement, $content);
    file_put_contents($file, $content);
    echo "Done! Removed duplicate block.\n";
} else {
    echo "Pattern not found with LF. Trying CRLF mix...\n";
    // Try a simpler approach: just find the position of second storeKolomDinamis
    $positions = [];
    $offset = 0;
    while (($pos = strpos($content, 'public function storeKolomDinamis', $offset)) !== false) {
        $positions[] = $pos;
        $offset = $pos + 1;
    }
    echo "Found storeKolomDinamis at positions: " . implode(', ', $positions) . "\n";
    
    $positions2 = [];
    $offset = 0;
    while (($pos = strpos($content, 'public function exportPdf', $offset)) !== false) {
        $positions2[] = $pos;
        $offset = $pos + 1;
    }
    echo "Found exportPdf at positions: " . implode(', ', $positions2) . "\n";
    
    // Remove from first storeKolomDinamis to second exportPdf (exclusive)
    if (count($positions) >= 2 && count($positions2) >= 1) {
        $start = $positions[0] - 4; // include the leading spaces
        $end   = $positions2[0];
        echo "Will remove from $start to $end\n";
        $content = substr($content, 0, $start) . substr($content, $end);
        file_put_contents($file, $content);
        echo "Done!\n";
    } elseif (count($positions) == 1 && count($positions2) >= 2) {
        // Only one storeKolomDinamis but two exportPdf - remove duplicate exportPdf area
        echo "Different duplicate structure detected.\n";
    }
}
