<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class BarSkMemoController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');

        $tahunTersedia = DB::table('bar_sk_memo')->select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        // FILTER TAHUN DAN BULAN
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $namaBulanUrut = array_keys($monthsOrder);

        $tahunScope = ($filterTahun === 'semua') ? $tahunTersedia : [$filterTahun];
        $bulanScope = ($filterBulan === 'semua') ? $namaBulanUrut : [$filterBulan];

        
        // --- AMBIL DATA DOKUMEN ---
        $rawDataQuery = DB::table('bar_sk_memo')
            ->whereIn('tahun', $tahunScope)
            ->whereIn('bulan', $bulanScope)
            ->get()->toArray();

        usort($rawDataQuery, function($a, $b) use ($monthsOrder) {
            if($a->tahun == $b->tahun) return $monthsOrder[$a->bulan] <=> $monthsOrder[$b->bulan];
            return $a->tahun <=> $b->tahun; 
        });

        // Pisahkan Data Terbit dan Proses
        $terbitCollection = collect($rawDataQuery)->filter(function($row) {
            return $row->skd_keputusan_bersama_terbit || $row->skd_non_ratifikasi_terbit || 
            $row->skd_ratifikasi_terbit || $row->memo_direksi_terbit || $row->bar_monitoring_terbit || 
            $row->bar_manajemen_terbit || ($row->data_tambahan ?? null);
        })->values();

        $prosesCollection = collect($rawDataQuery)->filter(function($row) {
            return $row->proses_skd_keputusan_bersama || $row->proses_skd_non_ratifikasi || 
            $row->proses_skd_ratifikasi || $row->proses_memo_direksi || $row->proses_bar_monitoring || 
            $row->proses_bar_manajemen || ($row->data_tambahan_proses ?? null);
        })->values();

        $perPage = 10;
        $pageTerbit = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('page_terbit');
        $dataTerbit = new \Illuminate\Pagination\LengthAwarePaginator(
            $terbitCollection->slice(($pageTerbit - 1) * $perPage, $perPage)->all(),
            $terbitCollection->count(), $perPage, $pageTerbit, 
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page_terbit']
        );
        $dataTerbit->appends(request()->all());

        $pageProses = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('page_proses');
        $dataProses = new \Illuminate\Pagination\LengthAwarePaginator(
            $prosesCollection->slice(($pageProses - 1) * $perPage, $perPage)->all(),
            $prosesCollection->count(), $perPage, $pageProses, 
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page_proses']
        );
        $dataProses->appends(request()->all());

        $rawData = $rawDataQuery; // Keep it for charts if needed


        // --- TARIK DATA PENGATURAN KOLOM DINAMIS (NAMA TABEL SUDAH DIPERBAIKI) ---
        $kolomDinamisTerbit = DB::table('dynamic_columns')->where('modul', 'bar_sk_memo_terbit')->get();
        $kolomDinamisProses = DB::table('dynamic_columns')->where('modul', 'bar_sk_memo_proses')->get();

        // --- GRAND TOTAL ---
        $grandTotalTerbit = ['skd_kb' => 0, 'skd_nr' => 0, 'skd_r' => 0, 'memo' => 0, 'bar_mon' => 0, 'bar_man' => 0];
        $grandTotalProses = ['skd_kb' => 0, 'skd_nr' => 0, 'skd_r' => 0, 'memo' => 0, 'bar_mon' => 0, 'bar_man' => 0];

        foreach($rawData as $row) {
            $grandTotalTerbit['skd_kb'] += $row->skd_keputusan_bersama_terbit;
            $grandTotalTerbit['skd_nr'] += $row->skd_non_ratifikasi_terbit;
            $grandTotalTerbit['skd_r'] += $row->skd_ratifikasi_terbit;
            $grandTotalTerbit['memo'] += $row->memo_direksi_terbit;
            $grandTotalTerbit['bar_mon'] += $row->bar_monitoring_terbit;
            $grandTotalTerbit['bar_man'] += $row->bar_manajemen_terbit;

            $grandTotalProses['skd_kb'] += $row->proses_skd_keputusan_bersama;
            $grandTotalProses['skd_nr'] += $row->proses_skd_non_ratifikasi;
            $grandTotalProses['skd_r'] += $row->proses_skd_ratifikasi;
            $grandTotalProses['memo'] += $row->proses_memo_direksi;
            $grandTotalProses['bar_mon'] += $row->proses_bar_monitoring;
            $grandTotalProses['bar_man'] += $row->proses_bar_manajemen;
        }

        // --- LOGIKA CHART ---
        $chartRaw = DB::table('bar_sk_memo')->whereIn('tahun', $tahunScope)->get();
        $chartMonthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartMonthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $chartDataTerbit = [];
        $chartDataProses = [];
        
        foreach ($chartMonthsFull as $index => $monthFull) {
             $itemsBln = $chartRaw->where('bulan', $monthFull);
             
             $chartDataTerbit[] = [
                 'label' => $chartMonthsShort[$index], 
                 'items' => [
                     'BAR Monitoring' => $itemsBln->sum('bar_monitoring_terbit'),
                     'Memo Direksi' => $itemsBln->sum('memo_direksi_terbit'),
                     'SKD Keputusan Bersama' => $itemsBln->sum('skd_keputusan_bersama_terbit'),
                     'SKD Non Ratifikasi' => $itemsBln->sum('skd_non_ratifikasi_terbit'),
                     'SKD Ratifikasi' => $itemsBln->sum('skd_ratifikasi_terbit'),
                     'BAR Manajemen' => $itemsBln->sum('bar_manajemen_terbit'),
                 ]
             ];

             $chartDataProses[] = [
                'label' => $chartMonthsShort[$index], 
                'items' => [
                    'Proses BAR Monitoring' => $itemsBln->sum('proses_bar_monitoring'),
                    'Proses Memo Direksi' => $itemsBln->sum('proses_memo_direksi'),
                    'Proses SKD Keputusan Bersama' => $itemsBln->sum('proses_skd_keputusan_bersama'),
                    'Proses SKD Non Ratifikasi' => $itemsBln->sum('proses_skd_non_ratifikasi'),
                    'Proses SKD Ratifikasi' => $itemsBln->sum('proses_skd_ratifikasi'),
                    'Proses BAR Manajemen' => $itemsBln->sum('proses_bar_manajemen'),
                ]
            ];
        }

        $chartColors = ['#BAE6FD', '#3B82F6', '#22C55E', '#F97316', '#1E3A8A', '#FDE047'];

        return view('bar-sk-memo', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia', 
            'rawData', 'dataTerbit', 'dataProses', 'grandTotalTerbit', 'grandTotalProses',
            'kolomDinamisTerbit', 'kolomDinamisProses',
            'chartDataTerbit', 'chartDataProses', 'chartColors'
        ));
    }

    public function storeTerbit(Request $request) {
        $request->validate(['tahun' => 'required', 'bulan' => 'required']);
        $record = DB::table('bar_sk_memo')->where('tahun', $request->tahun)->where('bulan', $request->bulan)->first();
        
        $data = [
            'skd_keputusan_bersama_terbit' => $request->skd_kb ?? 0,
            'skd_non_ratifikasi_terbit' => $request->skd_nr ?? 0,
            'skd_ratifikasi_terbit' => $request->skd_r ?? 0,
            'memo_direksi_terbit' => $request->memo ?? 0,
            'bar_monitoring_terbit' => $request->bar_mon ?? 0,
            'bar_manajemen_terbit' => $request->bar_man ?? 0,
            'data_tambahan' => isset($request->data_tambahan) ? json_encode($request->data_tambahan) : null,
            'updated_at' => Carbon::now()
        ];

        if (!$record) {
            $data['tahun'] = $request->tahun; $data['bulan'] = $request->bulan;
            $data['ba_terbit'] = 0; $data['ba_proses'] = 0; 
            $data['proses_skd_keputusan_bersama'] = 0; $data['proses_skd_non_ratifikasi'] = 0;
            $data['proses_skd_ratifikasi'] = 0; $data['proses_memo_direksi'] = 0;
            $data['proses_bar_monitoring'] = 0; $data['proses_bar_manajemen'] = 0;
            $data['created_at'] = Carbon::now();
            DB::table('bar_sk_memo')->insert($data);
        } else {
            DB::table('bar_sk_memo')->where('id', $record->id)->update($data);
        }
        return back()->with('success', 'Data Terbit berhasil disimpan.');
    }

    public function storeProses(Request $request) {
        $request->validate(['tahun' => 'required', 'bulan' => 'required']);
        $record = DB::table('bar_sk_memo')->where('tahun', $request->tahun)->where('bulan', $request->bulan)->first();
        
        $data = [
            'proses_skd_keputusan_bersama' => $request->skd_kb ?? 0,
            'proses_skd_non_ratifikasi' => $request->skd_nr ?? 0,
            'proses_skd_ratifikasi' => $request->skd_r ?? 0,
            'proses_memo_direksi' => $request->memo ?? 0,
            'proses_bar_monitoring' => $request->bar_mon ?? 0,
            'proses_bar_manajemen' => $request->bar_man ?? 0,
            'data_tambahan_proses' => isset($request->data_tambahan_proses) ? json_encode($request->data_tambahan_proses) : null,
            'updated_at' => Carbon::now()
        ];

        if (!$record) {
            $data['tahun'] = $request->tahun; $data['bulan'] = $request->bulan;
            $data['ba_terbit'] = 0; $data['ba_proses'] = 0; 
            $data['skd_keputusan_bersama_terbit'] = 0; $data['skd_non_ratifikasi_terbit'] = 0;
            $data['skd_ratifikasi_terbit'] = 0; $data['memo_direksi_terbit'] = 0;
            $data['bar_monitoring_terbit'] = 0; $data['bar_manajemen_terbit'] = 0;
            $data['created_at'] = Carbon::now();
            DB::table('bar_sk_memo')->insert($data);
        } else {
            DB::table('bar_sk_memo')->where('id', $record->id)->update($data);
        }
        return back()->with('success', 'Data Proses berhasil disimpan.');
    }

    public function destroyTerbit(Request $request) {
        $ids = $request->ids ?? [$request->id];
        DB::table('bar_sk_memo')->whereIn('id', $ids)->update([
            'skd_keputusan_bersama_terbit' => 0, 'skd_non_ratifikasi_terbit' => 0, 'skd_ratifikasi_terbit' => 0,
            'memo_direksi_terbit' => 0, 'bar_monitoring_terbit' => 0, 'bar_manajemen_terbit' => 0, 'data_tambahan' => null
        ]);
        return back()->with('success', 'Data Terbit berhasil dihapus/direset.');
    }

    public function destroyProses(Request $request) {
        $ids = $request->ids ?? [$request->id];
        DB::table('bar_sk_memo')->whereIn('id', $ids)->update([
            'proses_skd_keputusan_bersama' => 0, 'proses_skd_non_ratifikasi' => 0, 'proses_skd_ratifikasi' => 0,
            'proses_memo_direksi' => 0, 'proses_bar_monitoring' => 0, 'proses_bar_manajemen' => 0, 'data_tambahan_proses' => null
        ]);
        return back()->with('success', 'Data Proses berhasil dihapus/direset.');
    }

    // ==========================================
    // EXPORT PDF
    // ==========================================
    public function exportPdf(Request $request) {
        $filterTahun = $request->query('tahun', 'semua');
        $filterBulan = $request->query('bulan', 'semua');
        $tipe = $request->query('tipe', 'semua'); // 'terbit' atau 'proses'
        
        $query = DB::table('bar_sk_memo');
        if ($filterTahun !== 'semua') { $query->where('tahun', $filterTahun); }
        if ($filterBulan !== 'semua') { $query->where('bulan', $filterBulan); }
        
        $data = $query->get();
        $kolomTerbit = DB::table('dynamic_columns')->where('modul', 'bar_sk_memo_terbit')->get();
        $kolomProses = DB::table('dynamic_columns')->where('modul', 'bar_sk_memo_proses')->get();

        $pdf = Pdf::loadView('pdf.bar-sk-memo', compact('data', 'filterTahun', 'filterBulan', 'tipe', 'kolomTerbit', 'kolomProses'))
                  ->setPaper('A4', 'landscape');
        
        return $pdf->stream("Laporan_BAR_SK_Memo_{$filterBulan}_{$filterTahun}.pdf");
    }

    // ==========================================
    // EXPORT EXCEL (CSV)
    // ==========================================
    public function exportExcel(Request $request) {
        $filterTahun = $request->query('tahun', 'semua');
        $filterBulan = $request->query('bulan', 'semua');
        $tipe = $request->query('tipe', 'semua');

        $tahunTersedia = DB::table('bar_sk_memo')->select('tahun')->distinct()->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [\Carbon\Carbon::now()->year];
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $namaBulanUrut = array_keys($monthsOrder);

        $tahunScope = ($filterTahun === 'semua') ? $tahunTersedia : [$filterTahun];
        $bulanScope = ($filterBulan === 'semua') ? $namaBulanUrut : [$filterBulan];

        $data = DB::table('bar_sk_memo')->whereIn('tahun', $tahunScope)->whereIn('bulan', $bulanScope)->get()->toArray();
        usort($data, function($a, $b) use ($monthsOrder) {
            if($a->tahun == $b->tahun) return $monthsOrder[$a->bulan] <=> $monthsOrder[$b->bulan];
            return $a->tahun <=> $b->tahun; 
        });

        $kolomTerbit = DB::table('dynamic_columns')->where('modul', 'bar_sk_memo_terbit')->get();
        $kolomProses = DB::table('dynamic_columns')->where('modul', 'bar_sk_memo_proses')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        if ($tipe === 'terbit' || $tipe === 'semua') {
            $sheet1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Terbit');
            $spreadsheet->addSheet($sheet1, 0);
            $headerTerbit = ['Tahun', 'Bulan', 'SKD Keputusan Bersama', 'SKD Non Ratifikasi', 'SKD Ratifikasi', 'Memo Direksi', 'BAR Monitoring', 'BAR Manajemen'];
            foreach($kolomTerbit as $k) { $headerTerbit[] = $k->nama_kolom; }
            $sheet1->fromArray($headerTerbit, NULL, 'A1');
            $rowNum = 2;
            foreach ($data as $row) {
                if($row->skd_keputusan_bersama_terbit || $row->skd_non_ratifikasi_terbit || $row->skd_ratifikasi_terbit || $row->memo_direksi_terbit || $row->bar_monitoring_terbit || $row->bar_manajemen_terbit || $row->data_tambahan) {
                    $rowData = [$row->tahun, $row->bulan, $row->skd_keputusan_bersama_terbit, $row->skd_non_ratifikasi_terbit, $row->skd_ratifikasi_terbit, $row->memo_direksi_terbit, $row->bar_monitoring_terbit, $row->bar_manajemen_terbit];
                    $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);
                    foreach($kolomTerbit as $k) { $rowData[] = $tambahan[$k->nama_kolom] ?? ''; }
                    $sheet1->fromArray($rowData, NULL, 'A' . $rowNum++);
                }
            }
        }

        if ($tipe === 'proses' || $tipe === 'semua') {
            $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Proses');
            $spreadsheet->addSheet($sheet2, 1);
            $headerProses = ['Tahun', 'Bulan', 'Proses SKD Kep. Bersama', 'Proses SKD Non Ratifikasi', 'Proses SKD Ratifikasi', 'Proses Memo Direksi', 'Proses BAR Monitoring', 'Proses BAR Manajemen'];
            foreach($kolomProses as $k) { $headerProses[] = $k->nama_kolom; }
            $sheet2->fromArray($headerProses, NULL, 'A1');
            $rowNum = 2;
            foreach ($data as $row) {
                if($row->proses_skd_keputusan_bersama || $row->proses_skd_non_ratifikasi || $row->proses_skd_ratifikasi || $row->proses_memo_direksi || $row->proses_bar_monitoring || $row->proses_bar_manajemen || $row->data_tambahan_proses) {
                    $rowData = [$row->tahun, $row->bulan, $row->proses_skd_keputusan_bersama, $row->proses_skd_non_ratifikasi, $row->proses_skd_ratifikasi, $row->proses_memo_direksi, $row->proses_bar_monitoring, $row->proses_bar_manajemen];
                    $tambahan = is_string($row->data_tambahan_proses) ? json_decode($row->data_tambahan_proses, true) : ($row->data_tambahan_proses ?? []);
                    foreach($kolomProses as $k) { $rowData[] = $tambahan[$k->nama_kolom] ?? ''; }
                    $sheet2->fromArray($rowData, NULL, 'A' . $rowNum++);
                }
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = "Export_BAR_SK_Memo_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function downloadTemplateTerbit() {
        return response()->download(public_path('../referensi/Bar SK memo/Terbit.xlsx'));
    }

    public function downloadTemplateProses() {
        return response()->download(public_path('../referensi/Bar SK memo/Proses.xlsx'));
    }

    public function importExcelTerbit(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        $kolomTerbit = DB::table('dynamic_columns')->where('modul', 'bar_sk_memo_terbit')->get();

        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // Skip header
            $tahun = $row[0] ?? null; $bulan = $row[1] ?? null;
            if (!$tahun || !$bulan) continue;

            $tambahan = [];
            foreach($kolomTerbit as $idx => $k) {
                if(isset($row[8 + $idx])) { $tambahan[$k->nama_kolom] = $row[8 + $idx]; }
            }

            $data = [
                'skd_keputusan_bersama_terbit' => $row[2] ?? 0, 'skd_non_ratifikasi_terbit' => $row[3] ?? 0,
                'skd_ratifikasi_terbit' => $row[4] ?? 0, 'memo_direksi_terbit' => $row[5] ?? 0,
                'bar_monitoring_terbit' => $row[6] ?? 0, 'bar_manajemen_terbit' => $row[7] ?? 0,
                'data_tambahan' => json_encode($tambahan), 'updated_at' => \Carbon\Carbon::now()
            ];

            $existing = DB::table('bar_sk_memo')->where('tahun', $tahun)->where('bulan', $bulan)->first();
            if ($existing) { DB::table('bar_sk_memo')->where('id', $existing->id)->update($data); } 
            else {
                $data['tahun'] = $tahun; $data['bulan'] = $bulan;
                $data['proses_skd_keputusan_bersama'] = 0; $data['proses_skd_non_ratifikasi'] = 0;
                $data['proses_skd_ratifikasi'] = 0; $data['proses_memo_direksi'] = 0;
                $data['proses_bar_monitoring'] = 0; $data['proses_bar_manajemen'] = 0;
                $data['ba_terbit'] = 0; $data['ba_proses'] = 0;
                $data['created_at'] = \Carbon\Carbon::now();
                DB::table('bar_sk_memo')->insert($data);
            }
        }
        return back()->with('success', 'Data Terbit berhasil diimport!');
    }

    public function importExcelProses(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        $kolomProses = DB::table('dynamic_columns')->where('modul', 'bar_sk_memo_proses')->get();

        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // Skip header
            $tahun = $row[0] ?? null; $bulan = $row[1] ?? null;
            if (!$tahun || !$bulan) continue;

            $tambahan = [];
            foreach($kolomProses as $idx => $k) {
                if(isset($row[8 + $idx])) { $tambahan[$k->nama_kolom] = $row[8 + $idx]; }
            }

            $data = [
                'proses_skd_keputusan_bersama' => $row[2] ?? 0, 'proses_skd_non_ratifikasi' => $row[3] ?? 0,
                'proses_skd_ratifikasi' => $row[4] ?? 0, 'proses_memo_direksi' => $row[5] ?? 0,
                'proses_bar_monitoring' => $row[6] ?? 0, 'proses_bar_manajemen' => $row[7] ?? 0,
                'data_tambahan_proses' => json_encode($tambahan), 'updated_at' => \Carbon\Carbon::now()
            ];

            $existing = DB::table('bar_sk_memo')->where('tahun', $tahun)->where('bulan', $bulan)->first();
            if ($existing) { DB::table('bar_sk_memo')->where('id', $existing->id)->update($data); } 
            else {
                $data['tahun'] = $tahun; $data['bulan'] = $bulan;
                $data['skd_keputusan_bersama_terbit'] = 0; $data['skd_non_ratifikasi_terbit'] = 0;
                $data['skd_ratifikasi_terbit'] = 0; $data['memo_direksi_terbit'] = 0;
                $data['bar_monitoring_terbit'] = 0; $data['bar_manajemen_terbit'] = 0;
                $data['ba_terbit'] = 0; $data['ba_proses'] = 0;
                $data['created_at'] = \Carbon\Carbon::now();
                DB::table('bar_sk_memo')->insert($data);
            }
        }
        return back()->with('success', 'Data Proses berhasil diimport!');
    }
}