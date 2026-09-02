<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PaNonTekstualImport;
use App\Models\PaNonTekstualType;
use App\Models\PaNonTekstualValue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class PaNonTekstualController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');
        
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $tahunTersedia = PaNonTekstualValue::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        // Ambil Master Kolom
        $availableTypes = PaNonTekstualType::where('is_active', true)->orderBy('id', 'asc')->get();

        $query = PaNonTekstualValue::with('type');
        if ($filterTahun != 'semua') $query->where('tahun', $filterTahun);
        if ($filterBulan != 'semua') $query->where('bulan', $filterBulan);
        $rawData = $query->get();

        $groupedData = $rawData->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });

        $dataPa = [];
        $grandTotals = array_fill_keys($availableTypes->pluck('id')->toArray(), 0);

        foreach($groupedData as $key => $items) {
            $parts = explode('_', $key);
            $tahun = $parts[0]; $bulan = $parts[1];

            $rowData = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => [], 'data_tambahan' => []];

            foreach($items as $item) {
                $type = $item->type;
                if ($type && $type->is_active) {
                    $rowData['items'][$type->id] = $item->jumlah;
                    $grandTotals[$type->id] += $item->jumlah;
                }
                
                if (!empty($item->data_tambahan)) {
                    $tambahan = is_string($item->data_tambahan) ? json_decode($item->data_tambahan, true) : $item->data_tambahan;
                    if (is_array($tambahan)) {
                        foreach ($tambahan as $k => $v) {
                            $rowData['data_tambahan'][$k] = $v;
                        }
                    }
                }
            }
            
            if (count($rowData['items']) > 0 || count($rowData['data_tambahan']) > 0) $dataPa[] = $rowData;
        }

        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $sorter = function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        };
        usort($dataPa, $sorter);

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = array_slice($dataPa, ($currentPage - 1) * $perPage, $perPage);
        $paginatedDataPa = new LengthAwarePaginator($currentItems, count($dataPa), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        $paginatedDataPa->appends($request->all());


        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pa_non_tekstual')->get();

        return view('non-teknik-non-tekstual', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'availableTypes', 'paginatedDataPa', 'dataPa', 'grandTotals', 'kolomDinamis'
        ));
    }

    public function storeDokumen(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer', 
            'bulan' => 'required|string', 
            'jumlah' => 'required|integer|min:0'
        ], [
            'tahun.required' => 'Periode tahun wajib diisi.',
            'bulan.required' => 'Periode bulan wajib diisi.',
            'jumlah.required' => 'Jumlah wajib diisi.'
        ]);
        
        $type_id = $request->type_id;

        if ($type_id == 'tambah_baru' && $request->filled('nama_kegiatan_baru')) {
            $type = PaNonTekstualType::create([
                'name' => $request->nama_kegiatan_baru,
                'is_active' => true
            ]);
            $type_id = $type->id;
        }

        if (!$type_id || $type_id == 'tambah_baru') {
            return back()->withErrors(['Variabel dokumen tidak valid atau kosong.']);
        }

        // ==========================================
        // LOGIKA BARU: Cek data dulu, lalu tambahkan
        // ==========================================
        $existingData = PaNonTekstualValue::where('tahun', $request->tahun)
                                          ->where('bulan', $request->bulan)
                                          ->where('type_id', $type_id)
                                          ->first();

        if ($existingData) {
            // Jika data sudah ada, akumulasi/tambahkan jumlahnya
            $existingData->update([
                'jumlah' => $existingData->jumlah + $request->jumlah
            ]);
        } else {
            // Jika belum ada, buat data baru
            PaNonTekstualValue::create([
                'tahun' => $request->tahun, 
                'bulan' => $request->bulan, 
                'type_id' => $type_id,
                'jumlah' => $request->jumlah
            ]);
        }

        return back()->with('success', 'Data Dokumen berhasil ditambahkan/diakumulasi.');
    }

    public function updateBulan(Request $request)
    {
        // Validasi
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
            foreach ($request->items as $t_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    // Cukup langsung timpa/update angka di bulan & tahun tersebut
                    PaNonTekstualValue::updateOrCreate(
                        ['tahun' => $tahun, 'bulan' => $bulan, 'type_id' => $t_id],
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
                \App\Models\PaNonTekstualData::where('kelompok_tabel', $kelompok)
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
        PaNonTekstualValue::where('tahun', $request->tahun)->where('bulan', $request->bulan)->delete();
        return back()->with('success', 'Seluruh data di bulan tersebut berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);
        try {
            Excel::import(new PaNonTekstualImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil di-import dari Excel.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meng-import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request) { /* TODO */ }
    public function exportPdf(Request $request) { /* TODO */ }

}
