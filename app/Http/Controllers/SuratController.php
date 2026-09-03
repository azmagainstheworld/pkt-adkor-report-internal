<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat;
use App\Models\SuratRekap;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SuratImport;
use App\Imports\SuratRekapImport; 

class SuratController extends Controller
{
    // Bulan master untuk sorting dan chart
    protected $masterMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    // ==========================================
    // 1. TAMPILAN UTAMA (GET /surat) - INI YANG TADI ERROR MENGHILANG
    // ==========================================
        private function getMergedRekapData($tahunFilter, $bulanFilter)
    {
        $rekapManualQuery = \App\Models\SuratRekap::query();
        if ($tahunFilter != 'semua') $rekapManualQuery->where('tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $rekapManualQuery->where('bulan', $bulanFilter);
        $rekapManualData = $rekapManualQuery->get();

        $rekapDetailQuery = \App\Models\Surat::selectRaw("
                tahun, bulan,
                SUM(CASE WHEN jenis_surat = 'Surat Masuk' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis_surat = 'Surat Keluar' AND status = 'Terkirim' THEN 1 ELSE 0 END) as total_keluar
            ");
        if ($tahunFilter != 'semua') $rekapDetailQuery->where('tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $rekapDetailQuery->where('bulan', $bulanFilter);
        $rekapDetailData = $rekapDetailQuery->groupBy('tahun', 'bulan')->get();

        // Merge logic
        $merged = collect();
        $keys = $rekapManualData->map(function($i) { return $i->tahun . '_' . $i->bulan; })
            ->concat($rekapDetailData->map(function($i) { return $i->tahun . '_' . $i->bulan; }))
            ->unique();

        foreach ($keys as $key) {
            list($thn, $bln) = explode('_', $key);
            $manual = $rekapManualData->first(function($i) use ($thn, $bln) { return $i->tahun == $thn && $i->bulan == $bln; });
            $detail = $rekapDetailData->first(function($i) use ($thn, $bln) { return $i->tahun == $thn && $i->bulan == $bln; });

            $merged->push((object)[
                'tahun' => $thn,
                'bulan' => $bln,
                'total_masuk' => ($manual ? $manual->surat_masuk : 0) + ($detail ? $detail->total_masuk : 0),
                'total_keluar' => ($manual ? $manual->surat_keluar : 0) + ($detail ? $detail->total_keluar : 0),
            ]);
        }

        return $merged->sortBy(function($item) {
            return array_search($item->bulan, $this->masterMonths);
        })->values();
    }

    public function index(Request $request)
    {
        // Setup Pilihan Tahun
        $tahunSurat = Surat::select('tahun')->distinct()->pluck('tahun')->toArray();
        $tahunRekap = \App\Models\SuratRekap::select('tahun')->distinct()->pluck('tahun')->toArray();
        $tahunTersedia = array_unique(array_merge($tahunSurat, $tahunRekap));
        rsort($tahunTersedia);
        if (empty($tahunTersedia)) {
            $tahunTersedia = [date('Y')];
        }

        // Setup Pilihan Bulan (Diurutkan sesuai master)
        $bulanSurat = Surat::select('bulan')->distinct()->pluck('bulan')->toArray();
        $bulanRekap = \App\Models\SuratRekap::select('bulan')->distinct()->pluck('bulan')->toArray();
        $bulanTersediaRaw = array_unique(array_merge($bulanSurat, $bulanRekap));
        $bulanTersedia = array_intersect($this->masterMonths, $bulanTersediaRaw);
        if (empty($bulanTersedia)) {
            $bulanTersedia = [Carbon::now()->translatedFormat('F')];
        }

        $tahunFilter = $request->input('tahun', date('Y'));
        $bulanFilter = $request->input('bulan', 'semua');

        // --- A. QUERY TABEL 1 (REKAP DATA) ---
        $rekapDataAll = $this->getMergedRekapData($tahunFilter, $bulanFilter);
        $grandTotalMasuk = $rekapDataAll->sum('total_masuk');
        $grandTotalKeluar = $rekapDataAll->sum('total_keluar');

        // Manual Pagination for Rekap Data
        $perPage1 = 10;
        $page1 = $request->input('page1', 1);
        $offset1 = ($page1 - 1) * $perPage1;
        $rekapData = new \Illuminate\Pagination\LengthAwarePaginator(
            $rekapDataAll->slice($offset1, $perPage1)->values(),
            $rekapDataAll->count(),
            $perPage1,
            $page1,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page1']
        );

        // --- B. QUERY TABEL 2 (DETAIL SATUAN) ---
        $detailQuery = Surat::query();
        if ($tahunFilter != 'semua') $detailQuery->where('tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $detailQuery->where('bulan', $bulanFilter);
        $tableDetail = $detailQuery->orderBy('tanggal_surat', 'desc')->paginate(10, ['*'], 'page2')->appends($request->except('page2'));

        // --- C. QUERY CHART ---
        $chartData = [];
        
        if ($tahunFilter == 'semua') {
            $chartRekapAll = $this->getMergedRekapData('semua', 'semua');
            $tahunAsc = array_reverse($tahunTersedia);
            foreach ($tahunAsc as $thn) {
                $dataTahun = $chartRekapAll->where('tahun', $thn);
                $chartData[] = [
                    'label' => (string)$thn,
                    'masuk' => $dataTahun->sum('total_masuk'),
                    'keluar' => $dataTahun->sum('total_keluar')
                ];
            }
        } else {
            $chartRekapYear = $this->getMergedRekapData($tahunFilter, 'semua');
            foreach ($this->masterMonths as $monthStr) {
                $dataBulan = $chartRekapYear->where('bulan', $monthStr);
                $chartData[] = [
                    'label' => substr($monthStr, 0, 3), 
                    'masuk' => $dataBulan->sum('total_masuk'),
                    'keluar' => $dataBulan->sum('total_keluar')
                ];
            }
        }

        // Ambil Kolom Dinamis
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'surat')->get();

        return view('surat-masuk-keluar', compact(
            'tahunFilter', 'bulanFilter', 'tahunTersedia', 'bulanTersedia',
            'rekapData', 'grandTotalMasuk', 'grandTotalKeluar',
            'tableDetail', 'chartData', 'kolomDinamis'
        ));
    }

    // ==========================================
    // TAMPILKAN DETAIL (HALAMAN BARU)
    // ==========================================
    public function show($id)
    {
        $surat = Surat::findOrFail($id);
        
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'surat')->get();
        $dataTambahan = is_string($surat->data_tambahan) ? json_decode($surat->data_tambahan, true) : ($surat->data_tambahan ?? []);

        return view('surat-detail', compact('surat', 'kolomDinamis', 'dataTambahan'));
    }

    // ==========================================
    // 2. SIMPAN DATA BARU
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'periode_laporan' => 'required|date_format:Y-m', 
            'tanggal_surat' => 'required|date',
            'nomor_surat' => 'required|string|unique:surat,nomor_surat', 
            'judul_surat' => 'required|string',
            'jenis_surat' => 'required|in:Surat Masuk,Surat Keluar',
            'status' => 'required|in:Terkirim,Dibatalkan',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $periode = Carbon::parse($request->periode_laporan);
        $tahun = $periode->year;
        $bulanIndo = $this->masterMonths[$periode->month - 1];

        $dataTambahan = $request->input('data_tambahan', []);

        $surat = new Surat($request->except(['file_surat', 'periode_laporan']));
        
        $surat->tahun = $tahun;
        $surat->bulan = $bulanIndo;
        $surat->data_tambahan = $dataTambahan;

        if ($request->hasFile('file_surat')) {
            $path = $request->file('file_surat')->store('uploads/surat', 'public');
            $surat->file_path = $path;
        }

        $surat->save();

        return redirect()->back()->with('success', 'Arsip surat satuan berhasil disimpan. Tabel rekap diperbarui otomatis.');
    }

    // ==========================================
    // 3. EDIT DATA
    // ==========================================
    public function edit($id)
    {
        $surat = Surat::findOrFail($id);
        return response()->json($surat);
    }

    // ==========================================
    // 4. UPDATE DATA
    // ==========================================
    public function update(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);

        $request->validate([
            'periode_laporan' => 'required|date_format:Y-m', 
            'tanggal_surat' => 'required|date',
            'nomor_surat' => 'required|string|unique:surat,nomor_surat,'.$id, 
            'judul_surat' => 'required|string',
            'jenis_surat' => 'required|in:Surat Masuk,Surat Keluar',
            'status' => 'required|in:Terkirim,Dibatalkan',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $periode = Carbon::parse($request->periode_laporan);
        $tahun = $periode->year;
        $bulanIndo = $this->masterMonths[$periode->month - 1];

        $dataTambahan = $request->input('data_tambahan', []);

        $surat->fill($request->except(['file_surat', 'periode_laporan']));
        
        $surat->tahun = $tahun;
        $surat->bulan = $bulanIndo;
        $surat->data_tambahan = $dataTambahan;

        if ($request->hasFile('file_surat')) {
            if ($surat->file_path) {
                Storage::disk('public')->delete($surat->file_path);
            }
            $path = $request->file('file_surat')->store('uploads/surat', 'public');
            $surat->file_path = $path;
        }

        $surat->save();

        return redirect()->back()->with('success', 'Arsip surat satuan berhasil diperbarui.');
    }

    // ==========================================
    // 5. HAPUS DATA
    // ==========================================
        public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surat,id',
        ]);

        \App\Models\Surat::whereIn('id', $request->ids)->delete();

        return redirect()->route('surat.index')->with('success', count($request->ids) . ' Data surat berhasil dihapus.');
    }

    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);
        if ($surat->file_path) {
            Storage::disk('public')->delete($surat->file_path);
        }
        $surat->delete();
        return redirect()->route('surat.index')->with('success', 'Arsip surat berhasil dihapus. Tabel rekap disesuaikan.');
    }

    // ==========================================
    // FUNGSI EXPORT & IMPORT
    // ==========================================
    public function exportPdf(Request $request)
    {
        $tahunFilter = $request->input('tahun', date('Y'));
        $bulanFilter = $request->input('bulan', 'semua');

        $rekapData = $this->getMergedRekapData($tahunFilter, $bulanFilter);

        $detailData = Surat::where('tahun', $tahunFilter)
            ->when($bulanFilter != 'semua', function($q) use ($bulanFilter) {
                return $q->where('bulan', $bulanFilter);
            })
            ->orderBy('tanggal_surat', 'asc')
            ->get();

        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'surat')->get();

        $pdf = Pdf::loadView('pdf.laporan-surat', compact('rekapData', 'detailData', 'tahunFilter', 'bulanFilter', 'kolomDinamis'))
                  ->setPaper('a4', 'landscape'); 

        return $pdf->download('Laporan_Surat_'.$tahunFilter.'_'.$bulanFilter.'.pdf');
    }

        public function importRekapExcel(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:51200',
        ]);

        try {
            Excel::import(new SuratRekapImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data rekapitulasi histori berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function importExcel(Request $request)
    {
        set_time_limit(0);
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:51200',
        ]);

        try {
            Excel::import(new SuratImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Proses impor Excel selesai. Data baru ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $tahunFilter = $request->input('tahun', date('Y'));
        $bulanFilter = $request->input('bulan', 'semua');
        $jenis = $request->input('jenis', 'keduanya'); 

        try {
            return Excel::download(new \App\Exports\SuratExport($tahunFilter, $bulanFilter, $jenis), 'Laporan_Surat_'.$tahunFilter.'_'.$bulanFilter.'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Fitur ekspor Excel belum sepenuhnya dikonfigurasi. Pesan: ' . $e->getMessage());
        }
    }

    // ==========================================
    // FUNGSI KOLOM DINAMIS
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
            return back()->with('error_modal', 'Kolom dinamis dengan nama "' . $request->nama_kolom . '" sudah ada di modul Surat.');
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
}
