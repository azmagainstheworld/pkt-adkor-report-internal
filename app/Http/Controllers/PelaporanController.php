<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelaporan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PelaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Pengaturan Tanggal & Filter Default
        $tanggalToday = Carbon::now()->translatedFormat('l, d F Y');
        
        // Ambil daftar tahun unik yang datanya SUDAH ADA di database
        $tahunTersedia = Pelaporan::select(DB::raw('YEAR(tanggal) as tahun'))
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun')
                            ->toArray();

        // Jika database masih kosong, beri default tahun saat ini
        if (empty($tahunTersedia)) {
            $tahunTersedia = [Carbon::now()->year];
        }

        // Ambil Filter Tahun dan Bulan (Default: 'semua')
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua'); 

        $mapBulan = [
            'Januari' => 1, 'February' => 2, 'Februari' => 2,
            'Maret' => 3, 'April' => 4, 'Mei' => 5,
            'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        // --- QUERY TABEL 2: RINCIAN PELAPORAN ---
        $queryRincian = Pelaporan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $queryRincian->where(function($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                  ->orWhere('laporan', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhere('data_tambahan', 'like', "%{$search}%");
            });
        }

        if ($filterTahun != 'semua') {
            $queryRincian->whereYear('tanggal', $filterTahun);
        }

        if ($filterBulan != 'semua') {
            $monthNum = $mapBulan[$filterBulan] ?? null;
            if ($monthNum) {
                $queryRincian->whereMonth('tanggal', $monthNum);
            }
        }

        $dataRincian = $queryRincian->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        // --- QUERY TABEL 1: RINGKASAN AKUMULASI (Disesuaikan dengan Filter) ---
        // Alih-alih "3 Bulan Terakhir", kita akan mengelompokkan data berdasarkan Bulan & Tahun
        // sesuai dengan filter yang sedang aktif.
        $queryRingkasan = Pelaporan::query()
            ->select(
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('MONTH(tanggal) as bulan_num'),
                DB::raw('SUM(CASE WHEN tujuan = "Eksternal" THEN 1 ELSE 0 END) as eksternal'),
                DB::raw('SUM(CASE WHEN tujuan = "Internal" THEN 1 ELSE 0 END) as internal')
            );

        if ($filterTahun != 'semua') {
            $queryRingkasan->whereYear('tanggal', $filterTahun);
        }
        if ($filterBulan != 'semua') {
            $monthNum = $mapBulan[$filterBulan] ?? null;
            if ($monthNum) {
                $queryRingkasan->whereMonth('tanggal', $monthNum);
            }
        }

        // Dapatkan data yang sudah dikelompokkan dan diurutkan dari terbaru
        $ringkasanRaw = $queryRingkasan->groupBy('tahun', 'bulan_num')
                                       ->orderBy('tahun', 'desc')
                                       ->orderBy('bulan_num', 'desc')
                                       ->get();

        $daftarBulanTeks = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $dataRingkasan = [];

        foreach ($ringkasanRaw as $row) {
            $dataRingkasan[] = [
                'tahun' => $row->tahun,
                'bulan' => $daftarBulanTeks[$row->bulan_num],
                'eksternal' => $row->eksternal,
                'internal' => $row->internal,
                'total_laporan' => $row->eksternal + $row->internal,
            ];
        }

        // --- QUERY CHART (Grouped Bar Dinamis) ---
        $chartData = [];

        if ($filterTahun == 'semua') {
            // JIKA SEMUA TAHUN: Sumbu X adalah Tahun
            $tahunAsc = array_reverse($tahunTersedia);
            $chartQuery = Pelaporan::query();
            
            if ($filterBulan != 'semua') {
                $monthNum = $mapBulan[$filterBulan] ?? null;
                if ($monthNum) $chartQuery->whereMonth('tanggal', $monthNum);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($tahunAsc as $thn) {
                $dataTahunIni = $rawDataForChart->filter(function($item) use ($thn) {
                    return Carbon::parse($item->tanggal)->year == $thn;
                });
                
                $chartData[] = [
                    'label' => (string)$thn,
                    'eksternal' => $dataTahunIni->where('tujuan', 'Eksternal')->count(),
                    'internal' => $dataTahunIni->where('tujuan', 'Internal')->count()
                ];
            }
        } else {
            // JIKA TAHUN SPESIFIK: Sumbu X adalah 12 Bulan
            $chartMonthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartQuery = Pelaporan::whereYear('tanggal', $filterTahun);
            
            if ($filterBulan != 'semua') {
                $monthNum = $mapBulan[$filterBulan] ?? null;
                if ($monthNum) $chartQuery->whereMonth('tanggal', $monthNum);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($chartMonthsShort as $index => $monthShort) {
                $monthNumber = $index + 1; 
                $dataBulanIni = $rawDataForChart->filter(function($item) use ($monthNumber) {
                    return Carbon::parse($item->tanggal)->month == $monthNumber;
                });

                $chartData[] = [
                    'label' => $monthShort,
                    'eksternal' => $dataBulanIni->where('tujuan', 'Eksternal')->count(),
                    'internal' => $dataBulanIni->where('tujuan', 'Internal')->count()
                ];
            }
        }

        return view('pelaporan', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'dataRincian', 'dataRingkasan', 'chartData'
        ));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'tujuan'  => 'required|in:Eksternal,Internal',
            'nomor'   => 'required|string|max:100',
            'laporan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jenis'   => 'required|in:Bulan,Semester,Triwulan,Tahun',
        ]);

        Pelaporan::findOrFail($id)->update($request->all());

        return back()->with('success', 'Data laporan berhasil diperbarui.');
    }

        public function destroyBulk(\Illuminate\Http\Request $request)
    {
        // Fitur Hapus Semua (Delete All Pages)
        if ($request->input('delete_all_pages') == '1') {
            // Re-apply current filters to delete ALL matching data
            $query = \App\Models\Pelaporan::query();
            
            if ($request->filled('tahun') && $request->tahun !== 'semua') {
                $query->whereYear('tanggal', $request->tahun);
            }
            if ($request->filled('bulan') && $request->bulan !== 'semua') {
                $bulanMap = [
                    'Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,
                    'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12
                ];
                if(isset($bulanMap[$request->bulan])) {
                    $query->whereMonth('tanggal', $bulanMap[$request->bulan]);
                }
            }
            
            $count = $query->count();
            $query->delete();
            
            return redirect()->back()->with('success', $count . ' Data pelaporan (seluruh halaman) berhasil dihapus.');
        }

        // Hapus Massal Biasa (Hanya halaman saat ini)
        if ($request->has('ids') && is_array($request->ids)) {
            \App\Models\Pelaporan::whereIn('id', $request->ids)->delete();
            return redirect()->back()->with('success', count($request->ids) . ' Data pelaporan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
    }

    public function destroy($id)
    {
        Pelaporan::findOrFail($id)->delete();
        return back()->with('success', 'Data laporan berhasil dihapus.');
    }
    
    

    public function store(Request $request)
    {
        $request->validate([
            'tujuan'  => 'required|in:Eksternal,Internal',
            'nomor'   => 'required|string|max:100',
            'laporan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jenis'   => 'required|in:Bulan,Semester,Triwulan,Tahun',
        ]);

        Pelaporan::create($request->all());

        return redirect()->route('pelaporan.index', [
            'tahun' => 'semua',
            'bulan' => 'semua'
        ])->with('success', 'Data laporan berhasil ditambahkan.');
    }

    public function storeKolomDinamis(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        $request->validate([
            'modul'      => 'required|string',
            'nama_kolom' => 'required|string|max:100',
            'tipe_input' => 'required|in:text,number,date,dropdown,currency',
        ]);

        $isDuplicate = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', $request->modul)
            ->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim($request->nama_kolom))])->exists();

        if ($isDuplicate) return back()->with('error_modal', 'Kolom sudah ada!');

        $pilihanDropdown = null;
        if ($request->tipe_input === 'dropdown' && $request->pilihan_dropdown) {
            $pilihanDropdown = json_encode(array_map('trim', explode(',', $request->pilihan_dropdown)));
        }

        \Illuminate\Support\Facades\DB::table('dynamic_columns')->insert([
            'modul' => $request->modul, 'nama_kolom' => trim($request->nama_kolom),
            'tipe_input' => $request->tipe_input, 'pilihan_dropdown' => $pilihanDropdown,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return back()->with('success', 'Kolom dinamis baru berhasil ditambahkan.');
    }

    public function destroyKolomDinamis($id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('id', $id)->delete();
        return back()->with('success', 'Kolom dinamis berhasil dihapus.');
    }

    public function import(Request $request)
    {
        set_time_limit(0);
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:51200']);
        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PelaporanImport, $request->file('file'));
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back()->with('success', 'Data Pelaporan berhasil di-import!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Gagal mengimpor data: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', 'semua');
        $bulan = $request->input('bulan', 'semua');
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PelaporanExport(false, $tahun, $bulan), 'Data_Pelaporan.xlsx');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PelaporanExport(true), 'Template_Import_Pelaporan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tahun = $request->input('tahun', 'semua');
        $bulan = $request->input('bulan', 'semua');

        $query = \App\Models\Pelaporan::query();
        if ($tahun !== 'semua') $query->whereYear('tanggal', $tahun);
        if ($bulan !== 'semua') {
            $mapBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
            if (isset($mapBulan[$bulan])) $query->whereMonth('tanggal', $mapBulan[$bulan]);
        }
        $dataRincian = $query->orderBy('tanggal', 'desc')->get();
        
        // Load kolom dinamis agar tidak error di tampilan PDF
        $kolomDinamis = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', 'pelaporan')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pelaporan', compact('dataRincian', 'tahun', 'bulan', 'kolomDinamis'))->setPaper('a4', 'landscape');
        return $pdf->download('Data_Pelaporan.pdf');
    }
}