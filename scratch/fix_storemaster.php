<?php
$file = 'D:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\PaTekstualController.php';
$content = file_get_contents($file);

$broken = "    }

            'kelompok_tabel' => 'required|in:1,2',
            'nama_kegiatan' => 'required|string|max:255'";

$fixed = "    }

    public function storeMaster(Request $request)
    {
        \$request->validate([
            'kelompok_tabel' => 'required|in:1,2',
            'nama_kegiatan' => 'required|string|max:255'";

$content = str_replace($broken, $fixed, $content);
file_put_contents($file, $content);
echo "Fixed storeMaster\n";
