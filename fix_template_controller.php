<?php
$content = file_get_contents('app/Http/Controllers/TemplateController.php');
$mapping = [
    "case 'anggaran':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'anggaran':\n                \$exportClass = new \App\Exports\AnggaranExport(true);\n",
    "case 'dof':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'dof':\n                \$exportClass = new \App\Exports\DofExport(true);\n",
    "case 'jasa-fotocopy':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'jasa-fotocopy':\n                \$exportClass = new \App\Exports\JasaFotocopyExport(true);\n",
    "case 'karyawan':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'karyawan':\n                \$exportClass = new \App\Exports\KaryawanExport(true);\n",
    "case 'keluarga-karyawan':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'keluarga-karyawan':\n                \$exportClass = new \App\Exports\KeluargaKaryawanExport(true);\n",
    "case 'ketidakhadiran':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'ketidakhadiran':\n                \$exportClass = new \App\Exports\KetidakhadiranExport(true);\n",
    "case 'masalah-kendala':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'masalah-kendala':\n                \$exportClass = new \App\Exports\MasalahKendalaExport(true);\n",
    "case 'pa-non-tekstual':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'pa-non-tekstual':\n                \$exportClass = new \App\Exports\PaNonTekstualExport(true);\n",
    "case 'pa-teknik':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'pa-teknik':\n                \$exportClass = new \App\Exports\PaTeknikExport(true);\n",
    "case 'pa-tekstual':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'pa-tekstual':\n                \$exportClass = new \App\Exports\PaTekstualExport(true);\n",
    "case 'pelaporan':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'pelaporan':\n                \$exportClass = new \App\Exports\PelaporanExport(true);\n",
    "case 'pemeliharaan-peralatan':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'pemeliharaan-peralatan':\n                \$exportClass = new \App\Exports\PemeliharaanPeralatanExport(true);\n",
    "case 'pemeliharaan-rutin':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'pemeliharaan-rutin':\n                \$exportClass = new \App\Exports\PemeliharaanRutinExport(true);\n",
    "case 'pengiriman-dokumen':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'pengiriman-dokumen':\n                \$exportClass = new \App\Exports\PengirimanDokumenExport(true);\n",
    "case 'perizinan-proses':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'perizinan-proses':\n                \$exportClass = new \App\Exports\PerizinanProsesExport(true);\n",
    "case 'perizinan-terbit':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'perizinan-terbit':\n                \$exportClass = new \App\Exports\PerizinanTerbitExport(true);\n",
    "case 'program-strategis':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'program-strategis':\n                \$exportClass = new \App\Exports\ProgramStrategisExport(true);\n",
    "case 'surat':\n                \$exportClass = new \App\Exports\(true);\n" => "case 'surat':\n                \$exportClass = new \App\Exports\SuratExport(true);\n",
];

$content = str_replace(array_keys($mapping), array_values($mapping), $content);
file_put_contents('app/Http/Controllers/TemplateController.php', $content);
echo "Fixed";
