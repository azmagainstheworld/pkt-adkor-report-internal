<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\PaTeknikMaster;
use App\Models\PaTeknikData;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaTeknikExport;

class PaTeknikController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');
        
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $tahunTersedia = PaTeknikData::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        $masterTabel1 = PaTeknikMaster::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        $masterTabel2 = PaTeknikMaster::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();

        $query = PaTeknikData::with('masterTeknik');
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

            $rowTabel1 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];
            $rowTabel2 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];

            foreach($items as $item) {
                $master = $item->masterTeknik;
                if ($master->kelompok_tabel == 1) {
                    $rowTabel1['items'][$master->id] = $item->jumlah;
                    $totalsTabel1[$master->id] += $item->jumlah;
                } else {
                    $rowTabel2['items'][$master->id] = $item->jumlah;
                    $totalsTabel2[$master->id] += $item->jumlah;
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

        $perPage = 10;
        $currentPage1 = LengthAwarePaginator::resolveCurrentPage('page1');
        $currentItems1 = array_slice($dataTable1, ($currentPage1 - 1) * $perPage, $perPage);
        $paginatedTable1 = new LengthAwarePaginator($currentItems1, count($dataTable1), $perPage, $currentPage1, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page1']);
        $paginatedTable1->appends($request->all());

        $currentPage2 = LengthAwarePaginator::resolveCurrentPage('page2');
        $currentItems2 = array_slice($dataTable2, ($currentPage2 - 1) * $perPage, $perPage);
        $paginatedTable2 = new LengthAwarePaginator($currentItems2, count($dataTable2), $perPage, $currentPage2, ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page2']);
        $paginatedTable2->appends($request->all());

        return view('kearsipan-pa-teknik', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'masterTabel1', 'masterTabel2', 'paginatedTable1', 'paginatedTable2', 'dataTable1', 'dataTable2',
            'totalsTabel1', 'totalsTabel2'
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
            $master = PaTeknikMaster::create([
                'kelompok_tabel' => $request->kelompok_tabel, 
                'nama_kegiatan' => $request->nama_kegiatan_baru
            ]);
            $master_id = $master->id;
        }

        if (!$master_id || $master_id == 'tambah_baru') return back()->withErrors(['Variabel dokumen tidak valid.']);

        $existingData = PaTeknikData::where('tahun', $request->tahun)
                                      ->where('bulan', $request->bulan)
                                      ->where('master_id', $master_id)
                                      ->first();

        if ($existingData) {
            $existingData->update(['jumlah' => $existingData->jumlah + $request->jumlah]);
        } else {
            PaTeknikData::create([
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
                    PaTeknikData::updateOrCreate(
                        ['tahun' => $request->tahun, 'bulan' => $request->bulan, 'master_id' => $m_id],
                        ['jumlah' => $jumlah]
                    );
                }
            }
        }
        
        return back()->with('success', "Data periode {$request->bulan} {$request->tahun} berhasil diperbarui.");
    }

    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        if ($request->input('delete_all') == '1') {
            $tahun = $request->input('filter_tahun', 'semua');
            $bulan = $request->input('filter_bulan', 'semua');
            $kelompok = $request->input('kelompok_tabel', 1);
            
            $masterIds = \App\Models\PaTeknikMaster::where('kelompok_tabel', $kelompok)->pluck('id');
            
            $query = \App\Models\PaTeknikData::whereIn('master_id', $masterIds);
            if ($tahun !== 'semua') $query->where('tahun', $tahun);
            if ($bulan !== 'semua') $query->where('bulan', $bulan);
            
            $count = $query->delete();
            return back()->with('success', "Seluruh data Tabel $kelompok berhasil dihapus secara massal.");
        }

        $request->validate([
            'ids' => 'required|array',
        ]);

        $count = 0;
        foreach($request->ids as $val) {
            $parts = explode('|', $val);
            if(count($parts) == 3) {
                $kelompok = $parts[0];
                $tahun = $parts[1];
                $bulan = $parts[2];
                
                $masterIds = \App\Models\PaTeknikMaster::where('kelompok_tabel', $kelompok)->pluck('id');
                \App\Models\PaTeknikData::whereIn('master_id', $masterIds)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data terpilih berhasil dihapus secara massal.');
    }

    public function importExcel(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $kelompok = $request->input('kelompok', 'tabel1');
            Excel::import(new \App\Imports\PaTeknikImport($kelompok), $request->file('file'));
            return response()->json(['success' => true, 'message' => 'Data PA Teknik berhasil diimport.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal import: ' . $e->getMessage()], 500);
        }
    }

    public function destroyBulan(Request $request)
    {
        $masterIds = PaTeknikMaster::where('kelompok_tabel', $request->kelompok_tabel)->pluck('id');
        PaTeknikData::where('tahun', $request->tahun)->where('bulan', $request->bulan)->whereIn('master_id', $masterIds)->delete();
        return back()->with('success', 'Seluruh data pada tabel terpilih di bulan tersebut dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->query('tahun', 'semua');
        $bulan = $request->query('bulan', 'semua');
        $export = new PaTeknikExport($tahun, $bulan);
        $filename = 'Data_PA_Teknik_' . $tahun . '_' . $bulan . '.xlsx';
        return Excel::download($export, $filename);
    }

    public function exportPdf(Request $request)
    {
        $filterTahun = $request->query('tahun', 'semua');
        $filterBulan = $request->query('bulan', 'semua');

        $masterTabel1 = PaTeknikMaster::where('kelompok_tabel', 1)->orderBy('id')->get();
        $masterTabel2 = PaTeknikMaster::where('kelompok_tabel', 2)->orderBy('id')->get();

        $buildRows = function ($masters, $filterTahun, $filterBulan) {
            $masterIds = $masters->pluck('id');
            $query = PaTeknikData::whereIn('master_id', $masterIds)->with('masterTeknik');
            if ($filterTahun !== 'semua') $query->where('tahun', $filterTahun);
            if ($filterBulan !== 'semua') $query->where('bulan', $filterBulan);
            $rawData = $query->get();

            $grouped = [];
            foreach ($rawData as $item) {
                $key = $item->tahun . '|' . $item->bulan;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = ['tahun' => $item->tahun, 'bulan' => $item->bulan, 'items' => []];
                }
                $grouped[$key]['items'][$item->master_id] = $item->jumlah;
            }
            return array_values($grouped);
        };

        $rowsTabel1 = $buildRows($masterTabel1, $filterTahun, $filterBulan);
        $rowsTabel2 = $buildRows($masterTabel2, $filterTahun, $filterBulan);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pa-teknik-pdf', compact(
            'filterTahun', 'filterBulan', 'masterTabel1', 'masterTabel2', 'rowsTabel1', 'rowsTabel2'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_PA_Teknik_' . $filterTahun . '_' . $filterBulan . '.pdf');
    }
}