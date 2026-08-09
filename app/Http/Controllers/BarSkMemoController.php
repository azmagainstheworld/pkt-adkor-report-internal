<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarSkMemoMaster;
use App\Models\BarSkMemoData;
use App\Models\BarSkMemoRapat;
use App\Models\SkdTerbit;
use App\Models\SkdProses;
use App\Models\MemoTerbit;
use App\Models\MemoProses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BarSkMemoController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, F j, Y');

        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $tahunTersedia = BarSkMemoData::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];

        // --- AMBIL MASTER DATA ---
        $masterTerbit = BarSkMemoMaster::where('tipe', 'Terbit')->orderBy('id', 'asc')->get();
        $masterProses = BarSkMemoMaster::where('tipe', 'Proses')->orderBy('id', 'asc')->get();

        // --- AMBIL DATA DOKUMEN ---
        $queryDokumen = BarSkMemoData::with('barSkMemoMaster');
        if ($filterTahun != 'semua') $queryDokumen->where('tahun', $filterTahun);
        if ($filterBulan != 'semua') $queryDokumen->where('bulan', $filterBulan);
        $rawDokumen = $queryDokumen->get();

        // Grouping Data
        $groupedDokumen = $rawDokumen->groupBy(function($item) { return $item->tahun . '_' . $item->bulan; });

        $dataTerbitTable = []; $dataProsesTable = []; $dataRekapTable = [];

        foreach($groupedDokumen as $key => $items) {
            $parts = explode('_', $key);
            $tahun = $parts[0]; $bulan = $parts[1];

            $rowTerbit = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];
            $rowProses = ['tahun' => $tahun, 'bulan' => $bulan, 'items' => []];
            
            $baTerbit = 0; $skTerbit = 0; $memoTerbit = 0;
            $baProses = 0; $skProses = 0; $memoProses = 0;

            foreach($items as $item) {
                $master = $item->barSkMemoMaster;
                if ($master->tipe == 'Terbit') {
                    $rowTerbit['items'][$master->id] = $item->jumlah;
                    if (str_contains(strtolower($master->nama_dokumen), 'bar')) $baTerbit += $item->jumlah;
                    elseif (str_contains(strtolower($master->nama_dokumen), 'skd')) $skTerbit += $item->jumlah;
                    elseif (str_contains(strtolower($master->nama_dokumen), 'memo')) $memoTerbit += $item->jumlah;
                } else {
                    $rowProses['items'][$master->id] = $item->jumlah;
                    if (str_contains(strtolower($master->nama_dokumen), 'bar')) $baProses += $item->jumlah;
                    elseif (str_contains(strtolower($master->nama_dokumen), 'skd')) $skProses += $item->jumlah;
                    elseif (str_contains(strtolower($master->nama_dokumen), 'memo')) $memoProses += $item->jumlah;
                }
            }
            $dataTerbitTable[] = $rowTerbit; $dataProsesTable[] = $rowProses;
            $dataRekapTable[] = (object)[
                'tahun' => $tahun, 'bulan' => $bulan, 'ba_terbit' => $baTerbit, 'ba_proses' => $baProses,
                'sk_terbit' => $skTerbit, 'sk_proses' => $skProses, 'memo_terbit' => $memoTerbit, 'memo_proses' => $memoProses
            ];
        }

        $sorter = function($a, $b) use ($monthsOrder) {
            if(is_array($a)) {
                if($a['tahun'] == $b['tahun']) return $monthsOrder[$b['bulan']] <=> $monthsOrder[$a['bulan']];
                return $b['tahun'] <=> $a['tahun'];
            } else {
                if($a->tahun == $b->tahun) return $monthsOrder[$b->bulan] <=> $monthsOrder[$a->bulan];
                return $b->tahun <=> $a->tahun;
            }
        };

        usort($dataTerbitTable, $sorter); usort($dataProsesTable, $sorter); usort($dataRekapTable, $sorter);

        // --- CHART DATA ---
        $chartData = [];
        $chartMonthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartMonthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        foreach ($chartMonthsShort as $index => $monthShort) {
             $rekapBulan = collect($dataRekapTable)->firstWhere('bulan', $chartMonthsFull[$index]);
             if ($rekapBulan) {
                 $chartData[] = [
                    'label' => $monthShort,
                    'ba_proses' => $rekapBulan->ba_proses, 'ba_terbit' => $rekapBulan->ba_terbit,
                    'sk_proses' => $rekapBulan->sk_proses, 'sk_terbit' => $rekapBulan->sk_terbit,
                    'memo_proses' => $rekapBulan->memo_proses, 'memo_terbit' => $rekapBulan->memo_terbit,
                 ];
             } else {
                 $chartData[] = ['label' => $monthShort, 'ba_proses' => 0, 'ba_terbit' => 0, 'sk_proses' => 0, 'sk_terbit' => 0, 'memo_proses' => 0, 'memo_terbit' => 0];
             }
        }

        // --- QUERY TABEL RAPAT ---
        $queryRapat = BarSkMemoRapat::query();
        if ($filterTahun != 'semua') $queryRapat->where('tahun', $filterTahun);
        $dataRapat = $queryRapat->orderBy('tanggal_rapat', 'desc')->get();

        // --- QUERY TABEL SKD & MEMO ---
        $qSkdTerbit = SkdTerbit::query(); $qSkdProses = SkdProses::query();
        $qMemoTerbit = MemoTerbit::query(); $qMemoProses = MemoProses::query();

        if ($filterTahun != 'semua') {
            $qSkdTerbit->where('tahun', $filterTahun); $qSkdProses->where('tahun', $filterTahun);
            $qMemoTerbit->where('tahun', $filterTahun); $qMemoProses->where('tahun', $filterTahun);
        }

        $dataSkdTerbit = $qSkdTerbit->orderBy('tanggal_penetapan', 'desc')->get();
        $dataSkdProses = $qSkdProses->orderBy('tanggal_permintaan', 'desc')->get();
        $dataMemoTerbit = $qMemoTerbit->orderBy('tanggal', 'desc')->get();
        $dataMemoProses = $qMemoProses->orderBy('tanggal_permintaan', 'desc')->get();

        return view('bar-sk-memo', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia', 
            'masterTerbit', 'masterProses', 'dataTerbitTable', 'dataProsesTable', 'dataRekapTable',
            'dataRapat', 'chartData',
            'dataSkdTerbit', 'dataSkdProses', 'dataMemoTerbit', 'dataMemoProses'
        ));
    }

    // ================= CRUD DOKUMEN (DINAMIS) =================
    public function storeDokumen(Request $request) {
        $request->validate(['tipe' => 'required|in:Terbit,Proses', 'tahun' => 'required|integer', 'bulan' => 'required|string', 'jumlah' => 'required|integer|min:0']);
        $dokumen_id = $request->dokumen_id;
        if ($dokumen_id == 'tambah_baru' && $request->filled('nama_dokumen_baru')) {
            $master = BarSkMemoMaster::create(['tipe' => $request->tipe, 'nama_dokumen' => $request->nama_dokumen_baru]);
            $dokumen_id = $master->id;
        }
        if (!$dokumen_id || $dokumen_id == 'tambah_baru') return back()->withErrors(['Jenis Dokumen tidak valid.']);

        BarSkMemoData::updateOrCreate(['tahun' => $request->tahun, 'bulan' => $request->bulan, 'dokumen_id' => $dokumen_id], ['jumlah' => $request->jumlah]);
        return back()->with('success', 'Data Dokumen ' . $request->tipe . ' ditambahkan.');
    }

    public function updateBulan(Request $request) {
        if($request->has('items')) {
            foreach($request->items as $d_id => $jumlah) {
                if ($jumlah !== null && $jumlah !== '') {
                    BarSkMemoData::updateOrCreate(['tahun' => $request->tahun, 'bulan' => $request->bulan, 'dokumen_id' => $d_id], ['jumlah' => $jumlah]);
                }
            }
        }
        return back()->with('success', 'Data bulan tersebut diperbarui.');
    }

    public function destroyBulan(Request $request) {
        $masterIds = BarSkMemoMaster::where('tipe', $request->tipe)->pluck('id');
        BarSkMemoData::where('tahun', $request->tahun)->where('bulan', $request->bulan)->whereIn('dokumen_id', $masterIds)->delete();
        return back()->with('success', 'Data Dokumen ' . $request->tipe . ' di bulan tersebut dihapus.');
    }

    // ================= CRUD RAPAT =================
    public function storeRapat(Request $request) {
        $request->validate(['tanggal_rapat' => 'required|date', 'tentang' => 'required|string', 'status' => 'required|string']);
        $request->merge(['tahun' => Carbon::parse($request->tanggal_rapat)->year]);
        BarSkMemoRapat::create($request->all());
        return back()->with('success', 'Data Rapat berhasil ditambahkan.');
    }
    public function updateRapat(Request $request, $id) {
        $request->validate(['tanggal_rapat' => 'required|date', 'tentang' => 'required|string', 'status' => 'required|string']);
        $request->merge(['tahun' => Carbon::parse($request->tanggal_rapat)->year]);
        BarSkMemoRapat::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data Rapat berhasil diperbarui.');
    }
    public function destroyRapat($id) {
        BarSkMemoRapat::findOrFail($id)->delete();
        return back()->with('success', 'Data Rapat berhasil dihapus.');
    }

    // ================= CRUD SKD TERBIT =================
    public function storeSkdTerbit(Request $request) {
        $request->validate(['nomor_skd' => 'required', 'tentang' => 'required', 'tanggal_penetapan' => 'required|date', 'drafter' => 'required', 'kategori' => 'required']);
        $request->merge(['tahun' => Carbon::parse($request->tanggal_penetapan)->year]);
        SkdTerbit::create($request->all());
        return back()->with('success', 'Data SKD Terbit disimpan.');
    }
    public function updateSkdTerbit(Request $request, $id) {
        $request->merge(['tahun' => Carbon::parse($request->tanggal_penetapan)->year]);
        SkdTerbit::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data SKD Terbit diperbarui.');
    }
    public function destroySkdTerbit($id) {
        SkdTerbit::findOrFail($id)->delete(); return back()->with('success', 'Data dihapus.');
    }

    // ================= CRUD SKD PROSES =================
    public function storeSkdProses(Request $request) {
        $request->validate(['tanggal_permintaan' => 'required|date', 'tentang' => 'required', 'unit_kerja_peminta' => 'required', 'kategori' => 'required']);
        $request->merge(['tahun' => Carbon::parse($request->tanggal_permintaan)->year]);
        SkdProses::create($request->all());
        return back()->with('success', 'Data SKD Proses disimpan.');
    }
    public function updateSkdProses(Request $request, $id) {
        $request->merge(['tahun' => Carbon::parse($request->tanggal_permintaan)->year]);
        SkdProses::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data SKD Proses diperbarui.');
    }
    public function destroySkdProses($id) {
        SkdProses::findOrFail($id)->delete(); return back()->with('success', 'Data dihapus.');
    }

    // ================= CRUD MEMO TERBIT =================
    public function storeMemoTerbit(Request $request) {
        $request->validate(['nomor_memo' => 'required', 'tentang' => 'required', 'tanggal' => 'required|date']);
        $request->merge(['tahun' => Carbon::parse($request->tanggal)->year]);
        MemoTerbit::create($request->all());
        return back()->with('success', 'Data Memo Terbit disimpan.');
    }
    public function updateMemoTerbit(Request $request, $id) {
        $request->merge(['tahun' => Carbon::parse($request->tanggal)->year]);
        MemoTerbit::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data Memo Terbit diperbarui.');
    }
    public function destroyMemoTerbit($id) {
        MemoTerbit::findOrFail($id)->delete(); return back()->with('success', 'Data dihapus.');
    }

    // ================= CRUD MEMO PROSES =================
    public function storeMemoProses(Request $request) {
        $request->validate(['tanggal_permintaan' => 'required|date', 'tentang' => 'required', 'unit_kerja_peminta' => 'required']);
        $request->merge(['tahun' => Carbon::parse($request->tanggal_permintaan)->year]);
        MemoProses::create($request->all());
        return back()->with('success', 'Data Memo Proses disimpan.');
    }
    public function updateMemoProses(Request $request, $id) {
        $request->merge(['tahun' => Carbon::parse($request->tanggal_permintaan)->year]);
        MemoProses::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data Memo Proses diperbarui.');
    }
    public function destroyMemoProses($id) {
        MemoProses::findOrFail($id)->delete(); return back()->with('success', 'Data dihapus.');
    }
}