<?php

// 1. Patch KaryawanController.php
$controllerFile = 'app/Http/Controllers/KaryawanController.php';
$controllerContent = file_get_contents($controllerFile);

$searchController = <<<'EOT'
        $karyawans = \App\Models\Karyawan::orderBy('nama', 'asc')->get();
        if ($type === 'lengkap') {
            $karyawans->load('keluarga');
        }
        
        $kolomTabel = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', 'karyawan_tabel')->get();
        $kolomProfil = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', 'karyawan_profil')->get();
        $kolomKeluarga = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', 'karyawan_keluarga')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.karyawan', compact('karyawans', 'type', 'kolomTabel', 'kolomProfil', 'kolomKeluarga'))
EOT;

$replaceController = <<<'EOT'
        $karyawan = \App\Models\Karyawan::orderBy('nama', 'asc')->get();
        if ($type === 'lengkap') {
            $karyawan->load('keluarga');
        }
        
        $kolomDinamis = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', 'karyawan_tabel')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.karyawan', compact('karyawan', 'type', 'kolomDinamis'))
EOT;

$controllerContent = str_replace($searchController, $replaceController, $controllerContent);
file_put_contents($controllerFile, $controllerContent);

// 2. Patch resources/views/pdf/karyawan.blade.php
$pdfFile = 'resources/views/pdf/karyawan.blade.php';
$pdfContent = file_get_contents($pdfFile);

$pdfContent = str_replace("<th>Ukuran Kaos</th>\n", "", $pdfContent);
$pdfContent = str_replace("<td class=\"text-center\">{{ \$item->ukuran_kaos }}</td>\n", "", $pdfContent);

file_put_contents($pdfFile, $pdfContent);

echo "Patched KaryawanController and pdf.karyawan.blade.php.\n";
