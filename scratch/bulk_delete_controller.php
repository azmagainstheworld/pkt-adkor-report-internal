<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\BarSkMemoController.php';
$content = file_get_contents($file);

// Fix destroyTerbit
$searchTerbit = "DB::table('bar_sk_memo')->where('id', \$request->id)->update([";
$replaceTerbit = "\$ids = \$request->ids ?? [\$request->id];\n        DB::table('bar_sk_memo')->whereIn('id', \$ids)->update([";
$content = str_replace($searchTerbit, $replaceTerbit, $content);

$searchTerbit2 = "return back()->with('success', 'Data Terbit berhasil direset.');";
$replaceTerbit2 = "return back()->with('success', 'Data Terbit berhasil dihapus/direset.');";
$content = str_replace($searchTerbit2, $replaceTerbit2, $content);

// Fix destroyProses
$searchProses = "DB::table('bar_sk_memo')->where('id', \$request->id)->update([";
$replaceProses = "\$ids = \$request->ids ?? [\$request->id];\n        DB::table('bar_sk_memo')->whereIn('id', \$ids)->update([";
$content = str_replace($searchProses, $replaceProses, $content);

$searchProses2 = "return back()->with('success', 'Data Proses berhasil direset.');";
$replaceProses2 = "return back()->with('success', 'Data Proses berhasil dihapus/direset.');";
$content = str_replace($searchProses2, $replaceProses2, $content);

file_put_contents($file, $content);
echo "Controller updated for bulk delete";
