<?php
$ctrlPath = 'app/Http/Controllers/JasaKurirController.php';
$ctrlContent = file_get_contents($ctrlPath);

$searchExports = <<<PHP
    public function exportExcel(Request \$request) {
        return redirect()->back()->with('error_modal', 'Fitur Export Excel Jasa Kurir sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function exportPdf(Request \$request) {
        return redirect()->back()->with('error_modal', 'Fitur Export PDF Jasa Kurir sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function downloadTemplate(Request \$request) {
        return redirect()->back()->with('error_modal', 'Template Excel Jasa Kurir sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }
PHP;

$replaceExports = <<<PHP
    public function exportExcel(Request \$request) {
        \$tahun = \$request->input('tahun', 'semua');
        \$bulan = \$request->input('bulan', 'semua');
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\JasaKurirExport(false, \$tahun, \$bulan), 'Laporan_Jasa_Kurir_'.\$bulan.'_'.\$tahun.'.xlsx');
    }

    public function exportPdf(Request \$request) {
        \$tahun = \$request->input('tahun', 'semua');
        \$bulan = \$request->input('bulan', 'semua');
        
        \$kurirMaster = \App\Models\JasaKurirMaster::where('aktif', true)->orderBy('id')->get();
        \$kolomDinamis = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', 'jasa_kurir')->get();
        
        // Build tableData like in index
        \$query = \App\Models\JasaKurirData::with('jasaKurirMaster');
        if (\$tahun !== 'semua') \$query->where('tahun', \$tahun);
        if (\$bulan !== 'semua') \$query->where('bulan', \$bulan);
        
        \$rawData = \$query->get();
        \$tableData = [];
        foreach (\$rawData as \$data) {
            \$key = \$data->tahun . '-' . \$data->bulan;
            if (!isset(\$tableData[\$key])) {
                \$tableData[\$key] = [
                    'tahun' => \$data->tahun,
                    'bulan' => \$data->bulan,
                    'total_semua' => 0,
                    'data_tambahan' => []
                ];
                foreach (\$kurirMaster as \$kurir) {
                    \$tableData[\$key]['kurir_' . \$kurir->id] = 0;
                }
            }
            \$tableData[\$key]['kurir_' . \$data->jasa_kurir_id] = \$data->jumlah;
            \$tableData[\$key]['total_semua'] += \$data->jumlah;
            if (!empty(\$data->data_tambahan)) {
                \$tableData[\$key]['data_tambahan'] = array_merge(\$tableData[\$key]['data_tambahan'], \$data->data_tambahan);
            }
        }
        
        \$masterMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        usort(\$tableData, function(\$a, \$b) use (\$masterMonths) {
            if (\$a['tahun'] == \$b['tahun']) {
                \$idxA = array_search(\$a['bulan'], \$masterMonths);
                \$idxB = array_search(\$b['bulan'], \$masterMonths);
                return \$idxA <=> \$idxB;
            }
            return \$b['tahun'] <=> \$a['tahun'];
        });

        \$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.jasakurir', compact('tableData', 'tahun', 'bulan', 'kurirMaster', 'kolomDinamis'))->setPaper('a4', 'landscape');
        return \$pdf->download('Laporan_Jasa_Kurir.pdf');
    }

    public function downloadTemplate(Request \$request) {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\JasaKurirExport(true), 'Template_Import_JasaKurir.xlsx');
    }
PHP;

$ctrlContent = str_replace(str_replace("\r\n", "\n", $searchExports), str_replace("\r\n", "\n", $replaceExports), $ctrlContent);
file_put_contents($ctrlPath, $ctrlContent);
echo "JasaKurirController exports updated!\n";
?>
