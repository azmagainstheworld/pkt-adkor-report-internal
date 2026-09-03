<?php
$file = 'app/Http/Controllers/SuratController.php';
$content = file_get_contents($file);

// Fix 1: Table validation name
$content = str_replace("'ids.*' => 'exists:surats,id',", "'ids.*' => 'exists:surat,id',", $content);

// Fix 2: Redirect back to specific route to prevent MethodNotAllowed on referer fallback
$content = str_replace("return redirect()->back()->with('success', count(\$request->ids) . ' Data surat berhasil dihapus.');", "return redirect()->route('surat.index')->with('success', count(\$request->ids) . ' Data surat berhasil dihapus.');", $content);
$content = str_replace("return redirect()->back()->with('success', 'Arsip surat berhasil dihapus. Tabel rekap disesuaikan.');", "return redirect()->route('surat.index')->with('success', 'Arsip surat berhasil dihapus. Tabel rekap disesuaikan.');", $content);

file_put_contents($file, $content);
echo "SuratController fixes applied safely.\n";
