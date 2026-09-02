<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Undangan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UndanganController extends Controller
{
    public function index(Request $request)
    {
        // 1. FILTER DINAMIS: Ambil Tahun dan Bulan yang BENAR-BENAR ada di database
        $tahunTersedia = Undangan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) {
            $tahunTersedia = [date('Y')];
        }

        $masterMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $bulanTersediaRaw = Undangan::select('bulan')->distinct()->pluck('bulan')->toArray();
        $bulanTersedia = array_intersect($masterMonths, $bulanTersediaRaw); // Menjaga urutan bulan
        if (empty($bulanTersedia)) {
            $bulanTersedia = [Carbon::now()->translatedFormat('F')];
        }

        $tahunFilter = $request->input('tahun', 'semua');
        $bulanFilter = $request->input('bulan', 'semua');

        // 2. QUERY TABEL
        $query = Undangan::query();
        if ($tahunFilter != 'semua') $query->where('tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $query->where('bulan', $bulanFilter);
        
        // Custom Sort Bulan agar urut dari Januari -> Desember (Atau kebalikannya)
        $query->orderBy('tahun', 'desc')->orderByRaw("FIELD(bulan, '" . implode("','", $masterMonths) . "') DESC");
        $tableData = $query->get();

        $totalIntern = $tableData->sum('undangan_intern');
        $totalEkstern = $tableData->sum('undangan_ekstern');

        // 3. QUERY CHART.JS
        $chartData = [];
        if ($tahunFilter == 'semua') {
            $tahunAsc = array_reverse($tahunTersedia);
            $chartQuery = Undangan::query();
            if ($bulanFilter != 'semua') $chartQuery->where('bulan', $bulanFilter);
            $rawDataForChart = $chartQuery->get();

            foreach ($tahunAsc as $thn) {
                $dataTahunIni = $rawDataForChart->where('tahun', $thn);
                $chartData[] = [
                    'label' => (string)$thn,
                    'intern' => $dataTahunIni->sum('undangan_intern'),
                    'ekstern' => $dataTahunIni->sum('undangan_ekstern'),
                ];
            }
        } else {
            $chartQuery = Undangan::where('tahun', $tahunFilter);
            if ($bulanFilter != 'semua') $chartQuery->where('bulan', $bulanFilter);
            $rawDataForChart = $chartQuery->get();

            foreach ($masterMonths as $bulan) {
                $dataBulanIni = $rawDataForChart->where('bulan', $bulan);
                $chartData[] = [
                    'label' => substr($bulan, 0, 3),
                    'intern' => $dataBulanIni->sum('undangan_intern'),
                    'ekstern' => $dataBulanIni->sum('undangan_ekstern'),
                ];
            }
        }

        // 4. TARIK DEFINISI KOLOM DINAMIS
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'undangan')->get();

                // 5. QUERY DAFTAR RINCIAN (TABEL 2)
        $detailQuery = \App\Models\UndanganDetail::join('undangan', 'undangan_details.undangan_id', '=', 'undangan.id')
            ->select('undangan_details.*', 'undangan.tahun', 'undangan.bulan');
            
        if ($tahunFilter != 'semua') $detailQuery->where('undangan.tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $detailQuery->where('undangan.bulan', $bulanFilter);
        
        $detailQuery->orderBy('undangan.tahun', 'desc')
            ->orderByRaw("FIELD(undangan.bulan, '" . implode("','", $masterMonths) . "') DESC")
            ->orderBy('undangan_details.created_at', 'desc');
            
        $detailsData = $detailQuery->get();

        return view('undangan', compact(
            'tableData', 'chartData', 
            'tahunFilter', 'bulanFilter', 
            'totalIntern', 'totalEkstern', 
            'tahunTersedia', 'bulanTersedia', 'kolomDinamis', 'detailsData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'undangan_intern' => 'required|integer|min:0',
            'undangan_ekstern' => 'required|integer|min:0',
        ]);

        // Tangkap JSON
        $dataTambahan = $request->input('data_tambahan', []);

        // Logika Sakti: Jika bulan & tahun sudah ada, update. Jika belum, create.
        Undangan::updateOrCreate(
            ['tahun' => $request->tahun, 'bulan' => $request->bulan],
            [
                'undangan_intern' => $request->undangan_intern,
                'undangan_ekstern' => $request->undangan_ekstern,
                'data_tambahan' => $dataTambahan
            ]
        );

        return redirect()->back()->with('success', 'Data distribusi undangan berhasil disimpan.');
    }

    public function destroy($id)
    {
        Undangan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data undangan berhasil dihapus.');
    }

    // ==========================================
    // BLUEPRINT FUNGSI ATUR KOLOM
    // ==========================================
    public function storeKolomDinamis(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        $request->validate([
            'modul'      => 'required|string',
            'nama_kolom' => 'required|string|max:100',
            'tipe_input' => 'required|in:text,number,date,dropdown,currency',
        ]);

        $isDuplicate = DB::table('dynamic_columns')
            ->where('modul', $request->modul)
            ->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim($request->nama_kolom))])
            ->exists();

        if ($isDuplicate) {
            return back()->with('error_modal', 'Kolom dengan nama "' . $request->nama_kolom . '" sudah ada. Silakan gunakan nama lain!');
        }

        $pilihanDropdown = null;
        if ($request->tipe_input === 'dropdown' && $request->pilihan_dropdown) {
            $arrayPilihan = array_map('trim', explode(',', $request->pilihan_dropdown));
            $pilihanDropdown = json_encode($arrayPilihan);
        }

        DB::table('dynamic_columns')->insert([
            'modul'            => $request->modul,
            'nama_kolom'       => trim($request->nama_kolom),
            'tipe_input'       => $request->tipe_input,
            'pilihan_dropdown' => $pilihanDropdown,
            'created_at'       => Carbon::now(),
            'updated_at'       => Carbon::now(),
        ]);

        return back()->with('success', 'Kolom dinamis baru berhasil ditambahkan.');
    }

    public function destroyKolomDinamis($id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        DB::table('dynamic_columns')->where('id', $id)->delete();
        return back()->with('success', 'Kolom dinamis berhasil dihapus.');
    }

    public function import(Request $request) {
        return redirect()->back()->with('error_modal', 'Fitur Import Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function exportExcel(Request $request) {
        return redirect()->back()->with('error_modal', 'Fitur Export Excel Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function exportPdf(Request $request) {
        return redirect()->back()->with('error_modal', 'Fitur Export PDF Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function downloadTemplate(Request $request) {
        return redirect()->back()->with('error_modal', 'Template Excel Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function destroyBulkDetail(\Illuminate\Http\Request $request)
    {
        $ids = $request->ids;
        if ($ids && is_array($ids)) {
            \Illuminate\Support\Facades\DB::table('undangan_detail')->whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Berhasil menghapus data rincian secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }
}