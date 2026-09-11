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
        $tableData = $query->paginate(10, ['*'], 'rekap_page')->withQueryString();

        $totalIntern = (clone $query)->sum('undangan_intern');
        $totalEkstern = (clone $query)->sum('undangan_ekstern');

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
            
        $detailsData = $detailQuery->paginate(10, ['*'], 'detail_page')->withQueryString();

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

        private function recalculateRekap($undangan_id = null)
    {
        // If an ID is provided, only recalculate that specific record
        if ($undangan_id) {
            $undangan = \App\Models\Undangan::find($undangan_id);
            if ($undangan) {
                $intern = \App\Models\UndanganDetail::where('undangan_id', $undangan_id)->where('jenis_undangan', 'Intern')->count();
                $ekstern = \App\Models\UndanganDetail::where('undangan_id', $undangan_id)->where('jenis_undangan', 'Eksternal')->count();
                
                // Only update if there are details. If no details exist, leave it alone (it might be manual input)
                if ($intern > 0 || $ekstern > 0) {
                    $undangan->update([
                        'undangan_intern' => $intern,
                        'undangan_ekstern' => $ekstern
                    ]);
                }
            }
            return;
        }

        // Fallback for bulk operations: recalculate all that HAVE details
        $detailCounts = \App\Models\UndanganDetail::selectRaw('undangan_id, jenis_undangan, count(*) as total')
            ->groupBy('undangan_id', 'jenis_undangan')
            ->get();
            
        // Reset ONLY records that have at least one detail (so we don't wipe out manual Rekap imports)
        $idsWithDetails = \App\Models\UndanganDetail::select('undangan_id')->distinct()->pluck('undangan_id');
        \App\Models\Undangan::whereIn('id', $idsWithDetails)->update(['undangan_intern' => 0, 'undangan_ekstern' => 0]);
        
        foreach ($detailCounts as $count) {
            $undangan = \App\Models\Undangan::find($count->undangan_id);
            if ($undangan) {
                if (strtolower($count->jenis_undangan) == 'intern') {
                    $undangan->undangan_intern = $count->total;
                } else {
                    $undangan->undangan_ekstern = $count->total;
                }
                $undangan->save();
            }
        }
    }

    public function storeDetail(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'jenis_undangan' => 'required|string',
            'agenda' => 'required|string',
        ]);

        $undangan = \App\Models\Undangan::firstOrCreate(
            ['tahun' => $request->tahun, 'bulan' => $request->bulan],
            ['undangan_intern' => 0, 'undangan_ekstern' => 0]
        );

        \App\Models\UndanganDetail::create([
            'undangan_id' => $undangan->id,
            'jenis_undangan' => $request->jenis_undangan,
            'agenda' => $request->agenda,
        ]);

        $this->recalculateRekap($undangan->id);

        return redirect()->back()->with('success', 'Berhasil menambahkan rincian undangan.');
    }

    public function updateDetail(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'jenis_undangan' => 'required|string',
            'agenda' => 'required|string',
        ]);

        $detail = \App\Models\UndanganDetail::findOrFail($id);
        
        // Find or create the target Undangan
        $undangan = \App\Models\Undangan::firstOrCreate(
            ['tahun' => $request->tahun, 'bulan' => $request->bulan],
            ['undangan_intern' => 0, 'undangan_ekstern' => 0]
        );

        $detail->update([
            'undangan_id' => $undangan->id,
            'jenis_undangan' => $request->jenis_undangan,
            'agenda' => $request->agenda,
        ]);

        $this->recalculateRekap($undangan->id);

        return redirect()->back()->with('success', 'Berhasil mengupdate rincian undangan.');
    }

    public function destroyDetail($id)
    {
        $detail = \App\Models\UndanganDetail::findOrFail($id);
        $undanganId = $detail->undangan_id;
        $detail->delete();
        
        // Recount. If it drops to 0, it means it's empty now. We can choose to delete it or leave it as 0.
        // The safest is to recount it.
        $intern = \App\Models\UndanganDetail::where('undangan_id', $undanganId)->where('jenis_undangan', 'Intern')->count();
        $ekstern = \App\Models\UndanganDetail::where('undangan_id', $undanganId)->where('jenis_undangan', 'Eksternal')->count();
        
        \App\Models\Undangan::where('id', $undanganId)->update([
            'undangan_intern' => $intern,
            'undangan_ekstern' => $ekstern
        ]);
        
        // Delete if 0
        if ($intern == 0 && $ekstern == 0) {
            \App\Models\Undangan::where('id', $undanganId)->delete();
        }

        return redirect()->back()->with('success', 'Berhasil menghapus rincian undangan.');
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\UndanganImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Undangan berhasil diimpor!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Import Undangan error: ' . $e->getMessage());
            return redirect()->back()->with('error_modal', 'Gagal mengimpor data. Pastikan format file sesuai dengan template.');
        }
    }

    public function importDetail(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\UndanganDetailImport, $request->file('file'));
            $this->recalculateRekap();
            return redirect()->back()->with('success', 'Data Rincian Agenda Undangan berhasil diimpor!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Import Undangan Detail error: ' . $e->getMessage());
            return redirect()->back()->with('error_modal', 'Gagal mengimpor rincian: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request) {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UndanganExport(false), 'Data_Undangan.xlsx');
    }

    public function exportPdf(Request $request) {
        // Fallback for PDF if not fully implemented yet
        return redirect()->back()->with('error_modal', 'Fitur Export PDF Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function downloadTemplate(Request $request) {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UndanganExport(true), 'Template_Import_Undangan.xlsx');
    }

    public function destroyBulkDetail(\Illuminate\Http\Request $request)
    {
        $ids = $request->ids;
        if ($ids && is_array($ids)) {
            \Illuminate\Support\Facades\DB::table('undangan_details')->whereIn('id', $ids)->delete();
            $this->recalculateRekap();
            return redirect()->back()->with('success', 'Berhasil menghapus data rincian secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }

    /**
     * Mengambil data Undangan untuk laporan PDF bulanan.
     * Single source of truth: identik dengan dashboard.
     */
    public static function getReportData($tahun, $bulan)
    {
        $undangan = \App\Models\Undangan::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get();

        $totalIntern = $undangan->sum('undangan_intern');
        $totalEkstern = $undangan->sum('undangan_ekstern');

        // Untuk tabel rincian
        $detailUndangan = \App\Models\UndanganDetail::join('undangan', 'undangan_details.undangan_id', '=', 'undangan.id')
            ->select('undangan_details.*', 'undangan.tahun', 'undangan.bulan')
            ->where('undangan.tahun', $tahun)
            ->where('undangan.bulan', $bulan)
            ->orderBy('undangan_details.created_at', 'desc')
            ->get();

        \Log::info('[PDF Section] Undangan', [
            'bulan' => $bulan, 'tahun' => $tahun,
            'count' => $undangan->count(),
            'totalIntern' => $totalIntern,
            'totalEkstern' => $totalEkstern
        ]);

        return [
            'undangan'       => $undangan,
            'detailUndangan' => $detailUndangan,
            'totalIntern'    => $totalIntern,
            'totalEkstern'   => $totalEkstern,
        ];
    }
}
