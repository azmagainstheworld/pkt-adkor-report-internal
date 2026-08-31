<?php
$content = file_get_contents('app/Http/Controllers/TemplateController.php');

$replacements = [
    'anggaran' => 'AnggaranExport',
    'dof' => 'DofExport',
    'jasa-fotocopy' => 'JasaFotocopyExport',
    'karyawan' => 'KaryawanExport',
    'keluarga-karyawan' => 'KeluargaKaryawanExport',
    'ketidakhadiran' => 'KetidakhadiranExport',
    'masalah-kendala' => 'MasalahKendalaExport',
    'pa-non-tekstual' => 'PaNonTekstualExport',
    'pa-teknik' => 'PaTeknikExport',
    'pa-tekstual' => 'PaTekstualExport',
    'pelaporan' => 'PelaporanExport',
    'pemeliharaan-peralatan' => 'PemeliharaanPeralatanExport',
    'pemeliharaan-rutin' => 'PemeliharaanRutinExport',
    'pengiriman-dokumen' => 'PengirimanDokumenExport',
    'perizinan-proses' => 'PerizinanProsesExport',
    'perizinan-terbit' => 'PerizinanTerbitExport',
    'program-strategis' => 'ProgramStrategisExport',
    'surat' => 'SuratExport'
];

foreach ($replacements as $key => $class) {
    $pattern = "/case '" . preg_quote($key, '/') . "':\s+\\$exportClass = new \\\\App\\\\Exports\\\\\(true\);/ms";
    $replacement = "case '$key':\n                \$exportClass = new \App\Exports\\$class(true);";
    $content = preg_replace($pattern, $replacement, $content);
}

file_put_contents('app/Http/Controllers/TemplateController.php', $content);
echo "Fixed!\n";
