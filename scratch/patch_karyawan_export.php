<?php
// 1. Tambah fungsi exportPdf dan exportExcel di KaryawanController.php
$controllerFile = 'app/Http/Controllers/KaryawanController.php';
$controllerContent = file_get_contents($controllerFile);

$methodsToAdd = <<<EOT

    public function exportExcel(Request \$request)
    {
        \$type = \$request->query('type', 'ringkasan');
        \$isTemplate = false;
        \$namaFile = 'Data_Karyawan_' . date('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KaryawanExport(\$isTemplate, \$type), \$namaFile);
    }

    public function exportPdf(Request \$request)
    {
        \$type = \$request->query('type', 'ringkasan');
        
        \$karyawans = \App\Models\Karyawan::orderBy('nama', 'asc')->get();
        if (\$type === 'lengkap') {
            \$karyawans->load('keluarga');
        }
        
        \$kolomTabel = \App\Models\DynamicColumn::where('modul', 'karyawan_tabel')->get();
        \$kolomProfil = \App\Models\DynamicColumn::where('modul', 'karyawan_profil')->get();
        \$kolomKeluarga = \App\Models\DynamicColumn::where('modul', 'karyawan_keluarga')->get();
        
        \$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.karyawan', compact('karyawans', 'type', 'kolomTabel', 'kolomProfil', 'kolomKeluarga'))
                ->setPaper('a4', 'landscape');
                
        return \$pdf->download('Data_Karyawan_' . date('Ymd_His') . '.pdf');
    }
}
EOT;

$controllerContent = preg_replace('/}\s*$/', $methodsToAdd, $controllerContent);
file_put_contents($controllerFile, $controllerContent);


// 2. Hapus Ukuran Kaos dari tabel di karyawan.blade.php
$bladeFile = 'resources/views/karyawan.blade.php';
$bladeContent = file_get_contents($bladeFile);

// Hapus dari headers
$bladeContent = str_replace(
    "\$headers = ['No', 'Nama', 'NPK', 'Gol/Grade', 'MPP/PBP', 'Ket. Pensiun', 'Keterangan', 'Ukuran Kaos'];",
    "\$headers = ['No', 'Nama', 'NPK', 'Gol/Grade', 'MPP/PBP', 'Ket. Pensiun', 'Keterangan'];",
    $bladeContent
);

// Hapus dari table row
$bladeContent = str_replace(
    "<td class=\"px-6 py-4 font-bold text-gray-700 text-center\">{{ \$item->ukuran_kaos }}</td>\n                      \n                      <!-- RENDER KOLOM TAMBAHAN TABEL -->",
    "<!-- RENDER KOLOM TAMBAHAN TABEL -->",
    $bladeContent
);
$bladeContent = str_replace(
    "<td class=\"px-6 py-4 font-bold text-gray-700 text-center\">{{ \$item->ukuran_kaos }}</td>",
    "",
    $bladeContent
);


file_put_contents($bladeFile, $bladeContent);

echo "Export methods added and table column removed.\n";
