<?php

namespace App\Http\Controllers;

use App\Imports\MasalahKendalaImport;
use App\Exports\MasalahKendalaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\MasalahKendala;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MasalahKendalaController extends Controller
{
    public function index(Request $request)
    {
        // Format Tanggal Inggris
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');

        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $tahunTersedia = MasalahKendala::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        $query = MasalahKendala::query();
        if ($filterTahun != 'semua') $query->where('tahun', $filterTahun);
        if ($filterBulan != 'semua') $query->where('bulan', $filterBulan);

        // Sorting agar urut dari bulan terbaru
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $allData = $query->get()->sortByDesc(function($item) use ($monthsOrder) {
            return sprintf('%04d%02d', $item->tahun, $monthsOrder[$item->bulan] ?? 0);
        });

        $perPage = 10;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allData->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $dataMasalah = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($allData), $perPage, $currentPage, ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]);
        $dataMasalah->appends(request()->all());

        // Ambil konfigurasi Atur Kolom khusus modul masalah_kendala
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'masalah_kendala')->get();

        return view('masalah-kendala', compact('tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia', 'dataMasalah', 'kolomDinamis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'masalah_kendala' => 'required|string',
            'solusi' => 'required|string',
        ]);

        $data = $request->all();
        $data['data_tambahan'] = $request->input('data_tambahan', []); // Tangkap JSON

        MasalahKendala::create($data);
        return back()->with('success', 'Data Masalah / Kendala berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'masalah_kendala' => 'required|string',
            'solusi' => 'required|string',
        ]);

        $data = $request->all();
        $data['data_tambahan'] = $request->input('data_tambahan', []); // Tangkap JSON

        MasalahKendala::findOrFail($id)->update($data);
        return back()->with('success', 'Data Masalah / Kendala berhasil diperbarui.');
    }

        public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:masalah_kendala,id',
        ]);

        \App\Models\MasalahKendala::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' Data masalah & kendala berhasil dihapus.');
    }

    public function destroy($id)
    {
        MasalahKendala::findOrFail($id)->delete();
        return back()->with('success', 'Data Masalah / Kendala berhasil dihapus.');
    }

    // ====================================================================
    // FUNGSI KUSTOM ATUR KOLOM (DINAMIS) DENGAN PENCEGAH DUPLIKAT LOWER()
    // ====================================================================
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
            return back()->with('error_modal', 'Kolom dengan nama "' . $request->nama_kolom . '" sudah ada (meskipun kapitalisasinya berbeda). Silakan gunakan nama lain!');
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

    // --- FITUR IMPORT & EXPORT MASALAH KENDALA ---

    public function import(Request $request)
    {
        set_time_limit(0);
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:51200']);

        try {
            Excel::import(new MasalahKendalaImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Masalah Kendala berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', 'semua');
        $bulan = $request->input('bulan', 'semua');
        return Excel::download(new MasalahKendalaExport(false, $tahun, $bulan), 'Data_Masalah_Kendala.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new MasalahKendalaExport(true), 'Template_Import_Masalah_Kendala.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tahun = $request->input('tahun', 'semua');
        $bulan = $request->input('bulan', 'semua');

        $query = MasalahKendala::query();
        if ($tahun !== 'semua') $query->where('tahun', $tahun);
        if ($bulan !== 'semua') $query->where('bulan', $bulan);

        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $dataMasalah = $query->get()->sortByDesc(function($item) use ($monthsOrder) {
            return sprintf('%04d%02d', $item->tahun, $monthsOrder[$item->bulan] ?? 0);
        });
        
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'masalah_kendala')->get();

        $pdf = Pdf::loadView('pdf.masalah-kendala', compact('dataMasalah', 'tahun', 'bulan', 'kolomDinamis'))->setPaper('a4', 'landscape');
        return $pdf->download('Data_Masalah_Kendala.pdf');
    }
}

