<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemeliharaanRutinMaster;
use App\Models\PemeliharaanRutinData;
use App\Models\PemeliharaanPeralatanMaster;
use App\Models\PemeliharaanPeralatanData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PemeliharaanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, F j, Y');

        $tahunTersedia = PemeliharaanRutinData::select('tahun')
                            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];

        // =========================================================================
        // 1. DATA TABEL 1 & CHART (PEMELIHARAAN RUTIN DINAMIS)
        // =========================================================================
        $masterRutin = PemeliharaanRutinMaster::orderBy('id', 'asc')->get();
        
        $queryRutin = PemeliharaanRutinData::with('pemeliharaanRutinMaster');
        if ($filterTahun != 'semua') $queryRutin->where('tahun', $filterTahun);
        if ($filterBulan != 'semua') $queryRutin->where('bulan', $filterBulan);
        $rawDataRutin = $queryRutin->get();

        // Grouping Data Rutin
        $groupedRutin = $rawDataRutin->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        $dataRutinTable = [];
        foreach($groupedRutin as $key => $items) {
            $parts = explode('_', $key);
            $row = ['tahun' => $parts[0], 'bulan' => $parts[1], 'items' => []];
            foreach($items as $item) $row['items'][$item->rutin_id] = $item->jumlah;
            $dataRutinTable[] = $row;
        }

        // Urutkan Tabel Rutin
        usort($dataRutinTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        // Setup Chart Data Dinamis
        $chartData = [];
        $chartMonthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartMonthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $chartQuery = PemeliharaanRutinData::query();
        if ($filterTahun != 'semua') $chartQuery->where('tahun', $filterTahun);
        if ($filterBulan != 'semua') $chartQuery->where('bulan', $filterBulan);
        $chartRaw = $chartQuery->get();

        if ($filterTahun == 'semua') {
            $tahunAsc = array_reverse($tahunTersedia);
            foreach ($tahunAsc as $thn) {
                $dataTahunIni = $chartRaw->where('tahun', $thn);
                $items = [];
                foreach($masterRutin as $master) $items[$master->id] = $dataTahunIni->where('rutin_id', $master->id)->sum('jumlah');
                $chartData[] = ['label' => (string)$thn, 'items' => $items];
            }
        } else {
            foreach ($chartMonthsShort as $index => $monthShort) {
                $dataBulanIni = $chartRaw->where('bulan', $chartMonthsFull[$index]);
                $items = [];
                foreach($masterRutin as $master) $items[$master->id] = $dataBulanIni->where('rutin_id', $master->id)->sum('jumlah');
                $chartData[] = ['label' => $monthShort, 'items' => $items];
            }
        }

        // =========================================================================
        // 2. DATA TABEL 2 (RINCIAN PERALATAN DINAMIS)
        // =========================================================================
        $masterPeralatan = PemeliharaanPeralatanMaster::orderBy('id', 'asc')->get();
        
        $queryPeralatan = PemeliharaanPeralatanData::with('pemeliharaanPeralatanMaster');
        if ($filterTahun != 'semua') $queryPeralatan->where('tahun', $filterTahun);
        if ($filterBulan != 'semua') $queryPeralatan->where('bulan', $filterBulan);
        $rawDataPeralatan = $queryPeralatan->get();

        // Grouping Data Peralatan
        $groupedPeralatan = $rawDataPeralatan->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });
        $dataPeralatanTable = [];
        foreach($groupedPeralatan as $key => $items) {
            $parts = explode('_', $key);
            $row = ['tahun' => $parts[0], 'bulan' => $parts[1], 'items' => []];
            foreach($items as $item) $row['items'][$item->peralatan_id] = $item->jumlah;
            $dataPeralatanTable[] = $row;
        }

        // Urutkan Tabel Peralatan
        usort($dataPeralatanTable, function($a, $b) use ($monthsOrder) {
            if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        // Warna untuk Chart dan Legenda
        $chartColors = ['#F7941E', '#0056A3', '#60A5FA', '#A855F7', '#22C55E', '#EF4444', '#EAB308', '#06B6D4', '#EC4899', '#8B5CF6'];

        return view('pemeliharaan', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'masterRutin', 'dataRutinTable', 
            'masterPeralatan', 'dataPeralatanTable', 
            'chartData', 'chartColors'
        ));
    }

    // ================= CRUD TABEL 1: PEMELIHARAAN RUTIN =================
    public function storeRutin(Request $request)
    {
        $request->validate(['tahun' => 'required|integer', 'bulan' => 'required|string', 'jumlah' => 'required|integer|min:0']);
        $rutin_id = $request->rutin_id;

        if ($rutin_id == 'tambah_baru' && $request->filled('nama_rutin_baru')) {
            $master = PemeliharaanRutinMaster::create(['nama_kegiatan' => $request->nama_rutin_baru]);
            $rutin_id = $master->id;
        }

        if (!$rutin_id || $rutin_id == 'tambah_baru') return back()->withErrors(['Jenis Pemeliharaan tidak valid.']);

        PemeliharaanRutinData::updateOrCreate(
            ['tahun' => $request->tahun, 'bulan' => $request->bulan, 'rutin_id' => $rutin_id],
            ['jumlah' => $request->jumlah]
        );
        return back()->with('success', 'Pemeliharaan Rutin ditambahkan.');
    }

    public function updateRutinBulan(Request $request)
    {
        $oldTahun = $request->old_tahun;
        $oldBulan = $request->old_bulan;
        $newTahun = $request->tahun;
        $newBulan = $request->bulan;

        if($request->has('items')) {
            foreach($request->items as $r_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    
                    // Jika tahun atau bulan diubah, hapus data lamanya dulu
                    if ($oldTahun && $oldBulan && ($oldTahun != $newTahun || $oldBulan != $newBulan)) {
                        PemeliharaanRutinData::where('tahun', $oldTahun)
                                             ->where('bulan', $oldBulan)
                                             ->where('rutin_id', $r_id)
                                             ->delete();
                    }

                    // Masukkan/perbarui data dengan bulan & tahun yang baru
                    PemeliharaanRutinData::updateOrCreate(
                        ['tahun' => $newTahun, 'bulan' => $newBulan, 'rutin_id' => $r_id],
                        ['jumlah' => $jumlah]
                    );
                }
            }
        }
        return back()->with('success', 'Pemeliharaan Rutin bulan tersebut diperbarui.');
    }

    public function destroyRutinBulan(Request $request)
    {
        PemeliharaanRutinData::where('tahun', $request->tahun)->where('bulan', $request->bulan)->delete();
        return back()->with('success', 'Seluruh Pemeliharaan Rutin di bulan tersebut dihapus.');
    }

    // ================= CRUD TABEL 2: PERALATAN (DINAMIS) =================
    public function storePeralatan(Request $request)
    {
        $request->validate(['tahun' => 'required|integer', 'bulan' => 'required|string', 'jumlah' => 'required|integer|min:0']);
        $peralatan_id = $request->peralatan_id;

        if ($peralatan_id == 'tambah_baru' && $request->filled('nama_peralatan_baru')) {
            $master = PemeliharaanPeralatanMaster::create(['nama_peralatan' => $request->nama_peralatan_baru]);
            $peralatan_id = $master->id;
        }

        if (!$peralatan_id || $peralatan_id == 'tambah_baru') return back()->withErrors(['Jenis Pemeliharaan tidak valid.']);

        PemeliharaanPeralatanData::updateOrCreate(
            ['tahun' => $request->tahun, 'bulan' => $request->bulan, 'peralatan_id' => $peralatan_id],
            ['jumlah' => $request->jumlah]
        );
        return back()->with('success', 'Rincian Peralatan ditambahkan.');
    }

    public function updatePeralatanBulan(Request $request)
    {
        $oldTahun = $request->old_tahun;
        $oldBulan = $request->old_bulan;
        $newTahun = $request->tahun;
        $newBulan = $request->bulan;

        if($request->has('items')) {
            foreach($request->items as $p_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    
                    // Jika tahun atau bulan diubah, hapus data lamanya dulu
                    if ($oldTahun && $oldBulan && ($oldTahun != $newTahun || $oldBulan != $newBulan)) {
                        PemeliharaanPeralatanData::where('tahun', $oldTahun)
                                                 ->where('bulan', $oldBulan)
                                                 ->where('peralatan_id', $p_id)
                                                 ->delete();
                    }

                    // Masukkan/perbarui data dengan bulan & tahun yang baru
                    PemeliharaanPeralatanData::updateOrCreate(
                        ['tahun' => $newTahun, 'bulan' => $newBulan, 'peralatan_id' => $p_id],
                        ['jumlah' => $jumlah]
                    );
                }
            }
        }
        return back()->with('success', 'Rincian Peralatan bulan tersebut diperbarui.');
    }

    public function destroyPeralatanBulan(Request $request)
    {
        PemeliharaanPeralatanData::where('tahun', $request->tahun)->where('bulan', $request->bulan)->delete();
        return back()->with('success', 'Seluruh Rincian Peralatan di bulan tersebut dihapus.');
    }
}