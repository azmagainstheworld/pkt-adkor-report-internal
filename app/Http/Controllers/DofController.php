<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DofMaster;
use App\Models\DofData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Imports\DofImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class DofController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');
        
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $tahunTersedia = DofData::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) {
            $tahunTersedia = [Carbon::now()->year];
        }

        $masterTabel1 = DofMaster::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        $masterTabel2 = DofMaster::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();

        // 1. QUERY UNTUK TABEL BAWAH
        $query = DofData::with('masterDof');
        if ($filterTahun != 'semua') {
            $query->where('tahun', $filterTahun);
        }
        if ($filterBulan != 'semua') {
            $query->where('bulan', $filterBulan);
        }
        $rawData = $query->get();

        $groupedData = $rawData->groupBy(function($item) { 
            return $item->tahun . '_' . $item->bulan; 
        });

        $dataTable1 = []; 
        $dataTable2 = [];
        $totalsTabel1 = array_fill_keys($masterTabel1->pluck('id')->toArray(), 0);
        $totalsTabel2 = array_fill_keys($masterTabel2->pluck('id')->toArray(), 0);

        foreach($groupedData as $key => $items) {
            $parts = explode('_', $key);
            $tahun = $parts[0]; 
            $bulan = $parts[1];

            $rowTabel1 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];
            $rowTabel2 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];

            foreach($items as $item) {
                $master = $item->masterDof;
                if ($master->kelompok_tabel == 1) {
                    $rowTabel1['items'][$master->id] = $item->jumlah;
                    $totalsTabel1[$master->id] += $item->jumlah;
                    if (!empty($item->data_tambahan)) $rowTabel1['data_tambahan'] = array_merge($rowTabel1['data_tambahan'], is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : $item->data_tambahan);
                } else {
                    $rowTabel2['items'][$master->id] = $item->jumlah;
                    $totalsTabel2[$master->id] += $item->jumlah;
                    if (!empty($item->data_tambahan)) $rowTabel2['data_tambahan'] = array_merge($rowTabel2['data_tambahan'], is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : $item->data_tambahan);
                }
            }
            if (count($rowTabel1['items']) > 0) {
                $dataTable1[] = $rowTabel1;
            }
            if (count($rowTabel2['items']) > 0) {
                $dataTable2[] = $rowTabel2;
            }
        }

        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $sorter = function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) {
                return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            }
            return $b['tahun'] <=> $a['tahun'];
        };
        usort($dataTable1, $sorter);
        usort($dataTable2, $sorter);

        $perPage = 10;
        $currentPage1 = LengthAwarePaginator::resolveCurrentPage('page1');
        $currentItems1 = array_slice($dataTable1, ($currentPage1 - 1) * $perPage, $perPage);
        $paginatedTable1 = new LengthAwarePaginator($currentItems1, count($dataTable1), $perPage, $currentPage1, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page1']);
        $paginatedTable1->appends($request->all());

        $currentPage2 = LengthAwarePaginator::resolveCurrentPage('page2');
        $currentItems2 = array_slice($dataTable2, ($currentPage2 - 1) * $perPage, $perPage);
        $paginatedTable2 = new LengthAwarePaginator($currentItems2, count($dataTable2), $perPage, $currentPage2, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page2']);
        $paginatedTable2->appends($request->all());

        // 2. QUERY KHUSUS UNTUK CHARTJS (Agregasi Data)
        $regSuratId = $masterTabel1->where('nama_kegiatan', 'Registrasi Surat Masuk via DOF')->first()->id ?? 0;
        $appScanId = $masterTabel1->where('nama_kegiatan', 'Approval File Scan')->first()->id ?? 0;

        $chartQuery = DofData::whereIn('master_id', [$regSuratId, $appScanId]);
        
        // Logika Filter Chart
        if ($filterTahun != 'semua') {
            $chartQuery->where('tahun', $filterTahun);
            $agregasiChart = $chartQuery->select('bulan', 'master_id', DB::raw('SUM(jumlah) as total'))
                                        ->groupBy('bulan', 'master_id')
                                        ->get();
                                        
            $labels = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $dataReg = array_fill(0, 12, 0);
            $dataScan = array_fill(0, 12, 0);

            foreach ($agregasiChart as $data) {
                $monthIndex = array_search($data->bulan, $labels);
                if ($monthIndex !== false) {
                    if ($data->master_id == $regSuratId) $dataReg[$monthIndex] = $data->total;
                    if ($data->master_id == $appScanId) $dataScan[$monthIndex] = $data->total;
                }
            }
        } else {
            $labels = $tahunTersedia; 
            sort($labels); 
            $dataReg = array_fill(0, count($labels), 0);
            $dataScan = array_fill(0, count($labels), 0);

            $agregasiChart = $chartQuery->select('tahun', 'master_id', DB::raw('SUM(jumlah) as total'))
                                        ->groupBy('tahun', 'master_id')
                                        ->get();

            foreach ($agregasiChart as $data) {
                $yearIndex = array_search($data->tahun, $labels);
                if ($yearIndex !== false) {
                    if ($data->master_id == $regSuratId) $dataReg[$yearIndex] = $data->total;
                    if ($data->master_id == $appScanId) $dataScan[$yearIndex] = $data->total;
                }
            }
        }

        $chartJsonData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Registrasi Surat Masuk via DOF',
                    'data' => $dataReg,
                    'backgroundColor' => '#0056A3',
                ],
                [
                    'label' => 'Approval File Scan',
                    'data' => $dataScan,
                    'backgroundColor' => '#F7941E',
                ]
            ]
        ];

        return view('dof', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'masterTabel1', 'masterTabel2', 'paginatedTable1', 'paginatedTable2', 'dataTable1', 'dataTable2',
            'totalsTabel1', 'totalsTabel2', 'chartJsonData'
        ));
    }

    public function storeDokumen(Request $request)
    {
        $request->validate([
            'kelompok_tabel' => 'required|in:1,2', 
            'tahun' => 'required|integer', 
            'bulan' => 'required|string', 
            'jumlah' => 'required|integer|min:0'
        ]);
        
        $master_id = $request->master_id;
        
        if ($master_id == 'tambah_baru' && $request->filled('nama_kegiatan_baru')) {
            $master = DofMaster::create([
                'kelompok_tabel' => $request->kelompok_tabel, 
                'nama_kegiatan' => $request->nama_kegiatan_baru
            ]);
            $master_id = $master->id;
        }
        
        if (!$master_id || $master_id == 'tambah_baru') {
            return back()->withErrors(['Variabel dokumen tidak valid.']);
        }

        $existingData = DofData::where('tahun', $request->tahun)
                               ->where('bulan', $request->bulan)
                               ->where('master_id', $master_id)
                               ->first();
                               
        if ($existingData) { 
            $existingData->update(['jumlah' => $existingData->jumlah + $request->jumlah]); 
        } else { 
            DofData::create([
                'tahun' => $request->tahun, 
                'bulan' => $request->bulan, 
                'master_id' => $master_id, 
                'jumlah' => $request->jumlah
            ]); 
        }

        return back()->with('success', 'Data Dokumen berhasil ditambahkan/diakumulasi.');
    }

    public function updateBulan(Request $request)
    {
        $request->validate([
            'tahun' => 'required', 
            'bulan' => 'required', 
            'items' => 'required|array', 
            'items.*' => 'required|integer|min:0'
        ], [
            'items.*.required' => 'Semua kolom jumlah wajib diisi!'
        ]);
        
        if ($request->has('items')) {
            foreach ($request->items as $m_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    DofData::updateOrCreate([
                        'tahun' => $request->tahun, 
                        'bulan' => $request->bulan, 
                        'master_id' => $m_id
                    ], [
                        'jumlah' => $jumlah
                    ]);
                }
            }
        }
        return back()->with('success', "Data periode {$request->bulan} {$request->tahun} berhasil diperbarui.");
    }

    public function destroyBulan(Request $request)
    {
        $masterIds = DofMaster::where('kelompok_tabel', $request->kelompok_tabel)->pluck('id');
        DofData::where('tahun', $request->tahun)
               ->where('bulan', $request->bulan)
               ->whereIn('master_id', $masterIds)
               ->delete();
               
        return back()->with('success', 'Seluruh data pada tabel terpilih di bulan tersebut dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->query('tahun', 'semua');
        $bulan = $request->query('bulan', 'semua');
        // Let's use the old format or new format for Export Excel? 
        // We can just use DofExport(1) and DofExport(2) but Excel only supports one export class per file unless we use MultipleSheets.
        // I will recreate DofDataExport that exports the raw data like PaTeknikExport did.
        return Excel::download(new \App\Exports\DofDataExport($tahun, $bulan), 'Data_DOF_'.$tahun.'_'.$bulan.'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $filterTahun = $request->query('tahun', 'semua');
        $filterBulan = $request->query('bulan', 'semua');

        $masterTabel1 = DofMaster::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        $masterTabel2 = DofMaster::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();

        $query = DofData::with('masterDof');
        if ($filterTahun != 'semua') $query->where('tahun', $filterTahun);
        if ($filterBulan != 'semua') $query->where('bulan', $filterBulan);
        $rawData = $query->get();

        $groupedData = $rawData->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });

        $dataTable1 = []; $dataTable2 = [];
        $totalsTabel1 = array_fill_keys($masterTabel1->pluck('id')->toArray(), 0);
        $totalsTabel2 = array_fill_keys($masterTabel2->pluck('id')->toArray(), 0);

        foreach($groupedData as $key => $items) {
            $parts = explode('_', $key);
            $tahun = $parts[0]; $bulan = $parts[1];

            $rowTabel1 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];
            $rowTabel2 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];

            foreach($items as $item) {
                $master = $item->masterDof;
                if ($master->kelompok_tabel == 1) {
                    $rowTabel1['items'][$master->id] = $item->jumlah;
                    $totalsTabel1[$master->id] += $item->jumlah;
                    if (!empty($item->data_tambahan)) $rowTabel1['data_tambahan'] = array_merge($rowTabel1['data_tambahan'], $item->data_tambahan);
                } else {
                    $rowTabel2['items'][$master->id] = $item->jumlah;
                    $totalsTabel2[$master->id] += $item->jumlah;
                    if (!empty($item->data_tambahan)) $rowTabel2['data_tambahan'] = array_merge($rowTabel2['data_tambahan'], $item->data_tambahan);
                }
            }
            if (count($rowTabel1['items']) > 0) $dataTable1[] = $rowTabel1;
            if (count($rowTabel2['items']) > 0) $dataTable2[] = $rowTabel2;
        }

        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $sorter = function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        };
        usort($dataTable1, $sorter);
        usort($dataTable2, $sorter);

        $kolomTabel1 = DB::table('dynamic_columns')->where('modul', 'dof_1')->get();
        $kolomTabel2 = DB::table('dynamic_columns')->where('modul', 'dof_2')->get();

        $pdf = Pdf::loadView('pdf.dof', compact(
            'filterTahun', 'filterBulan', 'masterTabel1', 'masterTabel2', 
            'dataTable1', 'dataTable2', 'totalsTabel1', 'totalsTabel2',
            'kolomTabel1', 'kolomTabel2'
        ))->setPaper('a4', 'landscape');
        
        return $pdf->download('Laporan_DOF_'.$filterTahun.'_'.$filterBulan.'.pdf');
    }

        public function importExcel(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'kelompok' => 'required|in:tabel1,tabel2'
        ]);

        try {
            $kelompokInt = $request->kelompok == 'tabel1' ? 1 : 2;
            $uuid = $request->input('import_uuid', uniqid());
            
            Excel::import(new \App\Imports\DofImport($kelompokInt, $uuid), $request->file('file'));

            return response()->json(['success' => true, 'message' => 'Data DOF berhasil diimport.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal import: ' . $e->getMessage()], 500);
        }
    }
}