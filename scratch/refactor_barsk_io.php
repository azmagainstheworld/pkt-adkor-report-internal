<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\app\Http\Controllers\BarSkMemoController.php';
$content = file_get_contents($file);

// 1. Refactor exportExcel to use PhpSpreadsheet
$searchExport = "    public function exportExcel(Request $request) {";
// We will replace the entire exportExcel method

// 2. Refactor importExcelTerbit and importExcelProses to use PhpSpreadsheet
// Since it's too complex to string replace, we'll write a full replacement for these methods using a regex or by finding the method boundaries.

$newMethods = '
    public function exportExcel(Request $request) {
        $filterTahun = $request->query(\'tahun\', \'semua\');
        $filterBulan = $request->query(\'bulan\', \'semua\');
        $tipe = $request->query(\'tipe\', \'semua\');

        $tahunTersedia = DB::table(\'bar_sk_memo\')->select(\'tahun\')->distinct()->pluck(\'tahun\')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [\Carbon\Carbon::now()->year];
        $monthsOrder = [\'Januari\'=>1,\'Februari\'=>2,\'Maret\'=>3,\'April\'=>4,\'Mei\'=>5,\'Juni\'=>6,\'Juli\'=>7,\'Agustus\'=>8,\'September\'=>9,\'Oktober\'=>10,\'November\'=>11,\'Desember\'=>12];
        $namaBulanUrut = array_keys($monthsOrder);

        $tahunScope = ($filterTahun === \'semua\') ? $tahunTersedia : [$filterTahun];
        $bulanScope = ($filterBulan === \'semua\') ? $namaBulanUrut : [$filterBulan];

        $data = DB::table(\'bar_sk_memo\')->whereIn(\'tahun\', $tahunScope)->whereIn(\'bulan\', $bulanScope)->get()->toArray();
        usort($data, function($a, $b) use ($monthsOrder) {
            if($a->tahun == $b->tahun) return $monthsOrder[$a->bulan] <=> $monthsOrder[$b->bulan];
            return $a->tahun <=> $b->tahun; 
        });

        $kolomTerbit = DB::table(\'dynamic_columns\')->where(\'modul\', \'bar_sk_memo_terbit\')->get();
        $kolomProses = DB::table(\'dynamic_columns\')->where(\'modul\', \'bar_sk_memo_proses\')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        if ($tipe === \'terbit\' || $tipe === \'semua\') {
            $sheet1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, \'Terbit\');
            $spreadsheet->addSheet($sheet1, 0);
            $headerTerbit = [\'Tahun\', \'Bulan\', \'SKD Keputusan Bersama\', \'SKD Non Ratifikasi\', \'SKD Ratifikasi\', \'Memo Direksi\', \'BAR Monitoring\', \'BAR Manajemen\'];
            foreach($kolomTerbit as $k) { $headerTerbit[] = $k->nama_kolom; }
            $sheet1->fromArray($headerTerbit, NULL, \'A1\');
            $rowNum = 2;
            foreach ($data as $row) {
                if($row->skd_keputusan_bersama_terbit || $row->skd_non_ratifikasi_terbit || $row->skd_ratifikasi_terbit || $row->memo_direksi_terbit || $row->bar_monitoring_terbit || $row->bar_manajemen_terbit || $row->data_tambahan) {
                    $rowData = [$row->tahun, $row->bulan, $row->skd_keputusan_bersama_terbit, $row->skd_non_ratifikasi_terbit, $row->skd_ratifikasi_terbit, $row->memo_direksi_terbit, $row->bar_monitoring_terbit, $row->bar_manajemen_terbit];
                    $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);
                    foreach($kolomTerbit as $k) { $rowData[] = $tambahan[$k->nama_kolom] ?? \'\'; }
                    $sheet1->fromArray($rowData, NULL, \'A\' . $rowNum++);
                }
            }
        }

        if ($tipe === \'proses\' || $tipe === \'semua\') {
            $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, \'Proses\');
            $spreadsheet->addSheet($sheet2, 1);
            $headerProses = [\'Tahun\', \'Bulan\', \'Proses SKD Kep. Bersama\', \'Proses SKD Non Ratifikasi\', \'Proses SKD Ratifikasi\', \'Proses Memo Direksi\', \'Proses BAR Monitoring\', \'Proses BAR Manajemen\'];
            foreach($kolomProses as $k) { $headerProses[] = $k->nama_kolom; }
            $sheet2->fromArray($headerProses, NULL, \'A1\');
            $rowNum = 2;
            foreach ($data as $row) {
                if($row->proses_skd_keputusan_bersama || $row->proses_skd_non_ratifikasi || $row->proses_skd_ratifikasi || $row->proses_memo_direksi || $row->proses_bar_monitoring || $row->proses_bar_manajemen || $row->data_tambahan_proses) {
                    $rowData = [$row->tahun, $row->bulan, $row->proses_skd_keputusan_bersama, $row->proses_skd_non_ratifikasi, $row->proses_skd_ratifikasi, $row->proses_memo_direksi, $row->proses_bar_monitoring, $row->proses_bar_manajemen];
                    $tambahan = is_string($row->data_tambahan_proses) ? json_decode($row->data_tambahan_proses, true) : ($row->data_tambahan_proses ?? []);
                    foreach($kolomProses as $k) { $rowData[] = $tambahan[$k->nama_kolom] ?? \'\'; }
                    $sheet2->fromArray($rowData, NULL, \'A\' . $rowNum++);
                }
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = "Export_BAR_SK_Memo_" . date(\'Ymd_His\') . ".xlsx";
        header(\'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\');
        header(\'Content-Disposition: attachment; filename="\'. urlencode($fileName).\'"\');
        $writer->save(\'php://output\');
        exit;
    }

    public function downloadTemplateTerbit() {
        return response()->download(public_path(\'../referensi/Bar SK memo/Terbit.xlsx\'));
    }

    public function downloadTemplateProses() {
        return response()->download(public_path(\'../referensi/Bar SK memo/Proses.xlsx\'));
    }

    public function importExcelTerbit(Request $request) {
        $request->validate([\'file\' => \'required|mimes:xlsx,xls,csv\']);
        $file = $request->file(\'file\');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        $kolomTerbit = DB::table(\'dynamic_columns\')->where(\'modul\', \'bar_sk_memo_terbit\')->get();

        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // Skip header
            $tahun = $row[0] ?? null; $bulan = $row[1] ?? null;
            if (!$tahun || !$bulan) continue;

            $tambahan = [];
            foreach($kolomTerbit as $idx => $k) {
                if(isset($row[8 + $idx])) { $tambahan[$k->nama_kolom] = $row[8 + $idx]; }
            }

            $data = [
                \'skd_keputusan_bersama_terbit\' => $row[2] ?? 0, \'skd_non_ratifikasi_terbit\' => $row[3] ?? 0,
                \'skd_ratifikasi_terbit\' => $row[4] ?? 0, \'memo_direksi_terbit\' => $row[5] ?? 0,
                \'bar_monitoring_terbit\' => $row[6] ?? 0, \'bar_manajemen_terbit\' => $row[7] ?? 0,
                \'data_tambahan\' => json_encode($tambahan), \'updated_at\' => \Carbon\Carbon::now()
            ];

            $existing = DB::table(\'bar_sk_memo\')->where(\'tahun\', $tahun)->where(\'bulan\', $bulan)->first();
            if ($existing) { DB::table(\'bar_sk_memo\')->where(\'id\', $existing->id)->update($data); } 
            else {
                $data[\'tahun\'] = $tahun; $data[\'bulan\'] = $bulan;
                $data[\'proses_skd_keputusan_bersama\'] = 0; $data[\'proses_skd_non_ratifikasi\'] = 0;
                $data[\'proses_skd_ratifikasi\'] = 0; $data[\'proses_memo_direksi\'] = 0;
                $data[\'proses_bar_monitoring\'] = 0; $data[\'proses_bar_manajemen\'] = 0;
                $data[\'created_at\'] = \Carbon\Carbon::now();
                DB::table(\'bar_sk_memo\')->insert($data);
            }
        }
        return back()->with(\'success\', \'Data Terbit berhasil diimport!\');
    }

    public function importExcelProses(Request $request) {
        $request->validate([\'file\' => \'required|mimes:xlsx,xls,csv\']);
        $file = $request->file(\'file\');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        $kolomProses = DB::table(\'dynamic_columns\')->where(\'modul\', \'bar_sk_memo_proses\')->get();

        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // Skip header
            $tahun = $row[0] ?? null; $bulan = $row[1] ?? null;
            if (!$tahun || !$bulan) continue;

            $tambahan = [];
            foreach($kolomProses as $idx => $k) {
                if(isset($row[8 + $idx])) { $tambahan[$k->nama_kolom] = $row[8 + $idx]; }
            }

            $data = [
                \'proses_skd_keputusan_bersama\' => $row[2] ?? 0, \'proses_skd_non_ratifikasi\' => $row[3] ?? 0,
                \'proses_skd_ratifikasi\' => $row[4] ?? 0, \'proses_memo_direksi\' => $row[5] ?? 0,
                \'proses_bar_monitoring\' => $row[6] ?? 0, \'proses_bar_manajemen\' => $row[7] ?? 0,
                \'data_tambahan_proses\' => json_encode($tambahan), \'updated_at\' => \Carbon\Carbon::now()
            ];

            $existing = DB::table(\'bar_sk_memo\')->where(\'tahun\', $tahun)->where(\'bulan\', $bulan)->first();
            if ($existing) { DB::table(\'bar_sk_memo\')->where(\'id\', $existing->id)->update($data); } 
            else {
                $data[\'tahun\'] = $tahun; $data[\'bulan\'] = $bulan;
                $data[\'skd_keputusan_bersama_terbit\'] = 0; $data[\'skd_non_ratifikasi_terbit\'] = 0;
                $data[\'skd_ratifikasi_terbit\'] = 0; $data[\'memo_direksi_terbit\'] = 0;
                $data[\'bar_monitoring_terbit\'] = 0; $data[\'bar_manajemen_terbit\'] = 0;
                $data[\'created_at\'] = \Carbon\Carbon::now();
                DB::table(\'bar_sk_memo\')->insert($data);
            }
        }
        return back()->with(\'success\', \'Data Proses berhasil diimport!\');
    }
}';

// Use regex to replace everything from exportExcel to the end of the class
$pattern = '/\s*public function exportExcel\(Request \$request\).*\}\s*$/s';
$content = preg_replace($pattern, $newMethods, $content);

file_put_contents($file, $content);
echo "Controller export/import refactored\n";
