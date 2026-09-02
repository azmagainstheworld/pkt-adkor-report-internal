<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemeliharaanRutinMaster;
use App\Models\PemeliharaanRutinData;
use App\Models\PemeliharaanPeralatanMaster;
use App\Models\PemeliharaanPeralatanData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class PemeliharaanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('id')->translatedFormat('l, d F Y');

        $tahunTersedia = PemeliharaanRutinData::select('tahun')
                            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        // FILTER TAHUN DAN BULAN
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $namaBulanUrut = array_keys($monthsOrder);

        $tahunScope = ($filterTahun === 'semua') ? $tahunTersedia : [$filterTahun];
        $bulanScope = ($filterBulan === 'semua') ? $namaBulanUrut : [$filterBulan];

        // =========================================================================
        // 1. DATA TABEL 1 (PEMELIHARAAN RUTIN DINAMIS)
        // =========================================================================
        $masterRutin = PemeliharaanRutinMaster::orderBy('id', 'asc')->get();
        
        $rawDataRutin = PemeliharaanRutinData::with('pemeliharaanRutinMaster')
            ->whereIn('tahun', $tahunScope)
            ->whereIn('bulan', $bulanScope)
            ->get();

        $groupedRutin = $rawDataRutin->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        $dataRutinTable = [];
        foreach($groupedRutin as $key => $items) {
            $parts = explode('_', $key);
            $row = ['tahun' => $parts[0], 'bulan' => $parts[1], 'items' => [], 'data_tambahan' => []];
            foreach($items as $item) {
                $row['items'][$item->rutin_id] = $item->jumlah;
                if (!empty($item->data_tambahan)) {
                    $row['data_tambahan'] = array_merge($row['data_tambahan'], $item->data_tambahan);
                }
            }
            $dataRutinTable[] = $row;
        }

        usort($dataRutinTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        // =========================================================================
        // LOGIKA CHART (KEBAL FILTER BULAN AGAR SELALU 12 BULAN TAMPIL)
        // =========================================================================
        $chartData = [];
        $chartMonthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartMonthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $chartQuery = PemeliharaanRutinData::query();
        if ($filterTahun != 'semua') $chartQuery->where('tahun', $filterTahun);
        $chartRaw = $chartQuery->get();

        foreach ($chartMonthsShort as $index => $monthShort) {
            $dataBulanIni = $chartRaw->where('bulan', $chartMonthsFull[$index]);
            $items = [];
            foreach($masterRutin as $master) {
                $items[$master->id] = $dataBulanIni->where('rutin_id', $master->id)->sum('jumlah');
            }
            $chartData[] = ['label' => $monthShort, 'items' => $items];
        }

        // =========================================================================
        // 2. DATA TABEL 2 (RINCIAN PERALATAN DINAMIS)
        // =========================================================================
        $masterPeralatan = PemeliharaanPeralatanMaster::orderBy('id', 'asc')->get();
        
        $rawDataPeralatan = PemeliharaanPeralatanData::with('pemeliharaanPeralatanMaster')
            ->whereIn('tahun', $tahunScope)
            ->whereIn('bulan', $bulanScope)
            ->get();

        $groupedPeralatan = $rawDataPeralatan->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        $dataPeralatanTable = [];
        foreach($groupedPeralatan as $key => $items) {
            $parts = explode('_', $key);
            $row = ['tahun' => $parts[0], 'bulan' => $parts[1], 'items' => [], 'data_tambahan' => []];
            foreach($items as $item) {
                $row['items'][$item->peralatan_id] = $item->jumlah;
                if (!empty($item->data_tambahan)) {
                    $row['data_tambahan'] = array_merge($row['data_tambahan'], $item->data_tambahan);
                }
            }
            $dataPeralatanTable[] = $row;
        }

        usort($dataPeralatanTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        $chartColors = ['#F7941E', '#0056A3', '#60A5FA', '#A855F7', '#22C55E', '#EF4444', '#EAB308', '#06B6D4', '#EC4899', '#8B5CF6'];

        $kolomRutin = DB::table('dynamic_columns')->where('modul', 'pemeliharaan_rutin')->get();
        $kolomPeralatan = DB::table('dynamic_columns')->where('modul', 'pemeliharaan_peralatan')->get();

        return view('pemeliharaan', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'masterRutin', 'dataRutinTable', 
            'masterPeralatan', 'dataPeralatanTable', 
            'chartData', 'chartColors',
            'kolomRutin', 'kolomPeralatan'
        ));
    }

    // ================= MASTER DOKUMEN =================
    public function storeMasterRutin(Request $request) {
        $request->validate(['nama_kegiatan' => 'required|string']);
        PemeliharaanRutinMaster::create(['nama_kegiatan' => $request->nama_kegiatan]);
        return back()->with('success', 'Kegiatan pemeliharaan rutin ditambahkan.');
    }

    public function destroyMasterRutin($id) {
        PemeliharaanRutinMaster::findOrFail($id)->delete();
        return back()->with('success', 'Kegiatan pemeliharaan rutin dihapus.');
    }

    public function storeMasterPeralatan(Request $request) {
        $request->validate(['nama_peralatan' => 'required|string']);
        PemeliharaanPeralatanMaster::create(['nama_peralatan' => $request->nama_peralatan]);
        return back()->with('success', 'Perbaikan peralatan ditambahkan.');
    }

    public function destroyMasterPeralatan($id) {
        PemeliharaanPeralatanMaster::findOrFail($id)->delete();
        return back()->with('success', 'Perbaikan peralatan dihapus.');
    }

    // ================= CRUD TABEL 1: PEMELIHARAAN RUTIN =================
    public function storeRutin(Request $request)
    {
        $request->validate(['tahun' => 'required|integer', 'bulan' => 'required|string', 'jumlah' => 'required|integer|min:0', 'rutin_id' => 'required']);
        PemeliharaanRutinData::updateOrCreate(
            ['tahun' => $request->tahun, 'bulan' => $request->bulan, 'rutin_id' => $request->rutin_id],
            ['jumlah' => $request->jumlah, 'data_tambahan' => $request->input('data_tambahan', [])]
        );
        return back()->with('success', 'Pemeliharaan Rutin ditambahkan.');
    }

    public function updateRutinBulan(Request $request)
    {
        $oldTahun = $request->old_tahun; $oldBulan = $request->old_bulan;
        $newTahun = $request->tahun; $newBulan = $request->bulan;

        if($request->has('items')) {
            foreach($request->items as $r_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    if ($oldTahun && $oldBulan && ($oldTahun != $newTahun || $oldBulan != $newBulan)) {
                        PemeliharaanRutinData::where('tahun', $oldTahun)->where('bulan', $oldBulan)->where('rutin_id', $r_id)->delete();
                    }
                    PemeliharaanRutinData::updateOrCreate(
                        ['tahun' => $newTahun, 'bulan' => $newBulan, 'rutin_id' => $r_id],
                        ['jumlah' => $jumlah, 'data_tambahan' => $request->input('data_tambahan', [])]
                    );
                }
            }
        }
        return back()->with('success', 'Pemeliharaan Rutin diperbarui.');
    }

        public function destroyRutinBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $count = 0;
        foreach($request->ids as $val) {
            $parts = explode('|', $val);
            if(count($parts) == 2) {
                $tahun = $parts[0];
                $bulan = $parts[1];
                \App\Models\PemeliharaanRutinData::where('tahun', $tahun)->where('bulan', $bulan)->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data pemeliharaan rutin berhasil dihapus.');
    }

    public function destroyRutinBulan(Request $request)
    {
        PemeliharaanRutinData::where('tahun', $request->tahun)->where('bulan', $request->bulan)->delete();
        return back()->with('success', 'Pemeliharaan Rutin di bulan tersebut dihapus.');
    }

    // ================= CRUD TABEL 2: PERALATAN =================
    public function storePeralatan(Request $request)
    {
        $request->validate(['tahun' => 'required|integer', 'bulan' => 'required|string', 'jumlah' => 'required|integer|min:0', 'peralatan_id' => 'required']);
        PemeliharaanPeralatanData::updateOrCreate(
            ['tahun' => $request->tahun, 'bulan' => $request->bulan, 'peralatan_id' => $request->peralatan_id],
            ['jumlah' => $request->jumlah, 'data_tambahan' => $request->input('data_tambahan', [])]
        );
        return back()->with('success', 'Rincian Peralatan ditambahkan.');
    }

    public function updatePeralatanBulan(Request $request)
    {
        $oldTahun = $request->old_tahun; $oldBulan = $request->old_bulan;
        $newTahun = $request->tahun; $newBulan = $request->bulan;

        if($request->has('items')) {
            foreach($request->items as $p_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    if ($oldTahun && $oldBulan && ($oldTahun != $newTahun || $oldBulan != $newBulan)) {
                        PemeliharaanPeralatanData::where('tahun', $oldTahun)->where('bulan', $oldBulan)->where('peralatan_id', $p_id)->delete();
                    }
                    PemeliharaanPeralatanData::updateOrCreate(
                        ['tahun' => $newTahun, 'bulan' => $newBulan, 'peralatan_id' => $p_id],
                        ['jumlah' => $jumlah, 'data_tambahan' => $request->input('data_tambahan', [])]
                    );
                }
            }
        }
        return back()->with('success', 'Rincian Peralatan diperbarui.');
    }

        public function destroyPeralatanBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $count = 0;
        foreach($request->ids as $val) {
            $parts = explode('|', $val);
            if(count($parts) == 2) {
                $tahun = $parts[0];
                $bulan = $parts[1];
                \App\Models\PemeliharaanPeralatanData::where('tahun', $tahun)->where('bulan', $bulan)->delete();
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' Data perbaikan peralatan berhasil dihapus.');
    }

    public function destroyPeralatanBulan(Request $request)
    {
        PemeliharaanPeralatanData::where('tahun', $request->tahun)->where('bulan', $request->bulan)->delete();
        return back()->with('success', 'Rincian Peralatan di bulan tersebut dihapus.');
    }

    // ==========================================
    // KOLOM DINAMIS
    // ==========================================
    public function storeKolomDinamis(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        $request->validate(['modul' => 'required|string', 'nama_kolom' => 'required|string|max:100', 'tipe_input' => 'required|in:text,number,date,dropdown,currency']);
        $isDuplicate = DB::table('dynamic_columns')->where('modul', $request->modul)->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim($request->nama_kolom))])->exists();
        if ($isDuplicate) return back()->with('error_modal', 'Kolom "' . $request->nama_kolom . '" sudah ada!')->with('failed_modul', $request->modul);

        $pilihanDropdown = ($request->tipe_input === 'dropdown' && $request->pilihan_dropdown) ? json_encode(array_map('trim', explode(',', $request->pilihan_dropdown))) : null;

        DB::table('dynamic_columns')->insert([
            'modul' => $request->modul, 'nama_kolom' => trim($request->nama_kolom), 'tipe_input' => $request->tipe_input,
            'pilihan_dropdown' => $pilihanDropdown, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
        ]);
        return back()->with('success', 'Kolom dinamis berhasil ditambahkan.');
    }

    public function destroyKolomDinamis($id) {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        DB::table('dynamic_columns')->where('id', $id)->delete();
        return back()->with('success', 'Kolom dinamis dihapus.');
    }

    // ==========================================
    // EXPORT & IMPORT TABEL 1: RUTIN
    // ==========================================
    public function importRutin(Request $request) {
        set_time_limit(0);
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv|max:51200']);
        try {
            Excel::import(new \App\Imports\PemeliharaanRutinImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data Pemeliharaan Rutin berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Gagal Import Rutin: ' . $e->getMessage());
        }
    }

    public function exportExcelRutin(Request $request) {
        $tahun = $request->input('tahun', 'semua');
        $bulan = $request->input('bulan', 'semua');
        return Excel::download(new \App\Exports\PemeliharaanRutinExport($tahun, $bulan), 'Laporan_Pemeliharaan_Rutin.xlsx');
    }

    public function exportPdfRutin(Request $request) {
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $masterRutin = PemeliharaanRutinMaster::orderBy('id', 'asc')->get();
        $kolomRutin = DB::table('dynamic_columns')->where('modul', 'pemeliharaan_rutin')->get();

        $query = PemeliharaanRutinData::with('pemeliharaanRutinMaster');
        if ($filterTahun !== 'semua') $query->where('tahun', $filterTahun);
        if ($filterBulan !== 'semua') $query->where('bulan', $filterBulan);
        
        $grouped = $query->get()->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        
        $dataTable = [];
        foreach($grouped as $key => $items) {
            $parts = explode('_', $key);
            $row = ['tahun' => $parts[0], 'bulan' => $parts[1], 'items' => [], 'data_tambahan' => []];
            foreach($items as $item) {
                $row['items'][$item->rutin_id] = $item->jumlah;
                if (!empty($item->data_tambahan)) $row['data_tambahan'] = array_merge($row['data_tambahan'], $item->data_tambahan);
            }
            $dataTable[] = $row;
        }

        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        usort($dataTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        $pdf = Pdf::loadView('pdf.pemeliharaan-rutin', compact('filterTahun', 'filterBulan', 'dataTable', 'masterRutin', 'kolomRutin'))->setPaper('a4', 'landscape');
        return $pdf->download('Laporan_Pemeliharaan_Rutin.pdf');
    }
    
    // ==========================================
    // EXPORT & IMPORT TABEL 2: PERALATAN
    // ==========================================
    public function importPeralatan(Request $request) {
        set_time_limit(0);
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv|max:51200']);
        try {
            Excel::import(new \App\Imports\PemeliharaanPeralatanImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data Rincian Perbaikan Peralatan berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Gagal Import Peralatan: ' . $e->getMessage());
        }
    }

    public function exportExcelPeralatan(Request $request) {
        $tahun = $request->input('tahun', 'semua');
        $bulan = $request->input('bulan', 'semua');
        return Excel::download(new \App\Exports\PemeliharaanPeralatanExport($tahun, $bulan), 'Laporan_Perbaikan_Peralatan.xlsx');
    }

    public function exportPdfPeralatan(Request $request) {
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $masterPeralatan = PemeliharaanPeralatanMaster::orderBy('id', 'asc')->get();
        $kolomPeralatan = DB::table('dynamic_columns')->where('modul', 'pemeliharaan_peralatan')->get();

        $query = PemeliharaanPeralatanData::with('pemeliharaanPeralatanMaster');
        if ($filterTahun !== 'semua') $query->where('tahun', $filterTahun);
        if ($filterBulan !== 'semua') $query->where('bulan', $filterBulan);
        
        $grouped = $query->get()->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        
        $dataTable = [];
        foreach($grouped as $key => $items) {
            $parts = explode('_', $key);
            $row = ['tahun' => $parts[0], 'bulan' => $parts[1], 'items' => [], 'data_tambahan' => []];
            foreach($items as $item) {
                $row['items'][$item->peralatan_id] = $item->jumlah;
                if (!empty($item->data_tambahan)) $row['data_tambahan'] = array_merge($row['data_tambahan'], $item->data_tambahan);
            }
            $dataTable[] = $row;
        }

        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        usort($dataTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        $pdf = Pdf::loadView('pdf.pemeliharaan-peralatan', compact('filterTahun', 'filterBulan', 'dataTable', 'masterPeralatan', 'kolomPeralatan'))->setPaper('a4', 'landscape');
        return $pdf->download('Laporan_Perbaikan_Peralatan.pdf');
    }
}
