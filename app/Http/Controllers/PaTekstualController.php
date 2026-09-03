<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaTekstualImport;
use App\Models\PaTekstualMaster;
use App\Models\PaTekstualKolom;
use App\Models\PaTekstualData;
use App\Exports\PaTekstualExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class PaTekstualController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');
        
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $tahunTersedia = PaTekstualData::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        $masterTabel1 = PaTekstualMaster::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        $masterTabel2 = PaTekstualMaster::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();
        
        $kolomTabel1 = PaTekstualKolom::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        $kolomTabel2 = PaTekstualKolom::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();

        $query = PaTekstualData::with('masterTekstual');
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
                if (!empty($item->data_tambahan)) {
                    if ($item->masterTekstual->kelompok_tabel == 1) {
                        $rowTabel1['data_tambahan'] = array_merge($rowTabel1['data_tambahan'], $item->data_tambahan);
                    } else {
                        $rowTabel2['data_tambahan'] = array_merge($rowTabel2['data_tambahan'], $item->data_tambahan);
                    }
                }
                $master = $item->masterTekstual;
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

        return view('kearsipan-pa-non-teknik-tekstual', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'masterTabel1', 'masterTabel2', 'kolomTabel1', 'kolomTabel2', 'paginatedTable1', 'paginatedTable2', 'dataTable1', 'dataTable2',
            'totalsTabel1', 'totalsTabel2'
        ));
    }

    public function storeDokumen(Request $request)
    {
        $request->validate([
            'kelompok_tabel' => 'required|in:1,2', 
            'tahun' => 'required|integer', 
            'bulan' => 'required|string', 
            'jumlah' => 'required|integer|min:0',
            'master_id' => 'required|exists:pa_tekstual_master,id'
        ]);
        
        $master_id = $request->master_id;

        // Logika Akumulasi (Tambah jumlah jika data sudah ada)
        $existingData = PaTekstualData::where('tahun', $request->tahun)
                                      ->where('bulan', $request->bulan)
                                      ->where('master_id', $master_id)
                                      ->first();

        if ($existingData) {
            $existingData->update([
                'jumlah' => $existingData->jumlah + $request->jumlah
            ]);
        } else {
            PaTekstualData::create([
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

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $dataTambahan = $request->input('data_tambahan', []);

        if ($request->has('items')) {
            $isFirst = true;
            foreach ($request->items as $m_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    $payload = ['jumlah' => $jumlah];
                    if ($isFirst) {
                        $payload['data_tambahan'] = $dataTambahan;
                        $isFirst = false;
                    }
                    
                    PaTekstualData::updateOrCreate(
                        ['tahun' => $tahun, 'bulan' => $bulan, 'master_id' => $m_id],
                        $payload
                    );
                }
            }
        }
        
        return back()->with('success', "Data periode $bulan $tahun berhasil diperbarui.");
    }

        public function destroyBulk(\Illuminate\Http\Request $request)
    {
        if ($request->input('delete_all') == '1') {
            $kelompok = $request->input('kelompok_tabel');
            $tahun = $request->input('filter_tahun', 'semua');
            $bulan = $request->input('filter_bulan', 'semua');
            
            $masterIds = \App\Models\PaTekstualMaster::where('kelompok_tabel', $kelompok)->pluck('id');
            $query = \App\Models\PaTekstualData::whereIn('master_id', $masterIds);
            if ($tahun !== 'semua') $query->where('tahun', $tahun);
            if ($bulan !== 'semua') $query->where('bulan', $bulan);
            
            $count = $query->delete();
            return back()->with('success', "Seluruh data pemeliharaan berhasil dihapus.");
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
                $masterIds = \App\Models\PaTekstualMaster::where('kelompok_tabel', $kelompok)->pluck('id');
                \App\Models\PaTekstualData::whereIn('master_id', $masterIds)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data berhasil dihapus secara massal.');
    }

    public function destroyBulan(Request $request)
    {
        $masterIds = PaTekstualMaster::where('kelompok_tabel', $request->kelompok_tabel)->pluck('id');
        PaTekstualData::where('tahun', $request->tahun)->where('bulan', $request->bulan)->whereIn('master_id', $masterIds)->delete();
        return back()->with('success', 'Seluruh data pada tabel terpilih di bulan tersebut dihapus.');
    }

    public function storeMaster(Request $request)
    {
        $request->validate([
            'kelompok_tabel' => 'required|in:1,2',
            'nama_dokumen' => 'required|string|max:255'
        ]);
        PaTekstualMaster::create([
            'kelompok_tabel' => $request->kelompok_tabel,
            'nama_dokumen' => trim($request->nama_dokumen)
        ]);
        return back()->with('success', 'Kolom kegiatan/dokumen baru berhasil ditambahkan.');
    }

    public function destroyMaster($id)
    {
        $master = PaTekstualMaster::findOrFail($id);
        $master->delete();
        return back()->with('success', 'Kolom kegiatan/dokumen berhasil dihapus beserta seluruh datanya.');
    }

    public function storeKolom(Request $request)
    {
        $request->validate([
            'kelompok_tabel' => 'required|in:1,2',
            'nama_kolom' => 'required|string|max:255',
            'tipe_input' => 'required|in:text,number,currency'
        ]);

        PaTekstualKolom::create([
            'kelompok_tabel' => $request->kelompok_tabel,
            'nama_kolom' => trim($request->nama_kolom),
            'tipe_input' => $request->tipe_input
        ]);

        return back()->with('success', 'Kolom tambahan berhasil dibuat.');
    }

    public function destroyKolom($id)
    {
        $kolom = PaTekstualKolom::findOrFail($id);
        $kolom->delete();
        return back()->with('success', 'Kolom tambahan berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls',
            'kelompok_tabel' => 'required|in:1,2'
        ]);
        try {
            $kelompok = $request->kelompok_tabel == 1 ? 'tabel1' : 'tabel2';
            Excel::import(new PaTekstualImport($kelompok), $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil di-import dari Excel.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $kelompok = $request->kelompok_tabel ?? 1;
        $isTemplate = $request->has('template');
        
        $filename = 'Laporan_PA_Tekstual_Tabel_' . $kelompok . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PaTekstualExport($kelompok, $isTemplate), $filename);
    }

    public function exportPdf(Request $request)
    {
        $kelompok = $request->kelompok_tabel ?? 1;
        $masterTabel = PaTekstualMaster::where('kelompok_tabel', $kelompok)->orderBy('id', 'asc')->get();
        $kolomTabel = PaTekstualKolom::where('kelompok_tabel', $kelompok)->orderBy('id', 'asc')->get();
        
        $masterIds = $masterTabel->pluck('id');
        $query = PaTekstualData::with('masterTekstual')->whereIn('master_id', $masterIds);
        if ($request->tahun && $request->tahun != 'semua') $query->where('tahun', $request->tahun);
        if ($request->bulan && $request->bulan != 'semua') $query->where('bulan', $request->bulan);
        
        $rawData = $query->get();
        $groupedData = $rawData->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        
        $dataTable = [];
        $totals = array_fill_keys($masterTabel->pluck('id')->toArray(), 0);
        
        foreach($groupedData as $key => $items) {
            $parts = explode('_', $key);
            $tahun = $parts[0]; $bulan = $parts[1];
            $row = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];
            
            foreach($items as $item) {
                $row['items'][$item->master_id] = $item->jumlah;
                if(isset($totals[$item->master_id])) {
                    $totals[$item->master_id] += $item->jumlah;
                }
                if (!empty($item->data_tambahan)) {
                    $row['data_tambahan'] = array_merge($row['data_tambahan'], $item->data_tambahan);
                }
            }
            if(count($row['items']) > 0) $dataTable[] = $row;
        }
        
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        usort($dataTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        $pdf = Pdf::loadView('pdf.pa-tekstual', compact('dataTable', 'masterTabel', 'kolomTabel', 'totals', 'kelompok'))
                  ->setPaper('a4', 'landscape');
        
        return $pdf->stream('Laporan_PA_Tekstual_Tabel_' . $kelompok . '.pdf');
    }

}
