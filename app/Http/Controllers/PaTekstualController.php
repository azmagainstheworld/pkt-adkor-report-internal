<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaTekstualImport;
use App\Models\PaTekstualMaster;
use App\Models\PaTekstualData;
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

            $rowTabel1 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];
            $rowTabel2 = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];

            foreach($items as $item) {
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
            $master = PaTekstualMaster::create([
                'kelompok_tabel' => $request->kelompok_tabel, 
                'nama_kegiatan' => $request->nama_kegiatan_baru
            ]);
            $master_id = $master->id;
        }

        if (!$master_id || $master_id == 'tambah_baru') return back()->withErrors(['Variabel dokumen tidak valid.']);

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
        // Validasi Ketat agar user tidak mengosongkan form
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

        if ($request->has('items')) {
            foreach ($request->items as $m_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    // Update langsung pada tahun & bulan tersebut
                    PaTekstualData::updateOrCreate(
                        ['tahun' => $tahun, 'bulan' => $bulan, 'master_id' => $m_id],
                        ['jumlah' => $jumlah]
                    );
                }
            }
        }
        
        return back()->with('success', "Data periode $bulan $tahun berhasil diperbarui.");
    }

        public function destroyBulk(\Illuminate\Http\Request $request)
    {
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
                \App\Models\PaTekstualData::where('kelompok_tabel', $kelompok)
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

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);
        try {
            Excel::import(new PaTekstualImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil di-import dari Excel.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request) { /* TODO */ }
    public function exportPdf(Request $request) { /* TODO */ }

}
