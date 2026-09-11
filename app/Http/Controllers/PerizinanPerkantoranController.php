<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerizinanTerbit;
use App\Models\PerizinanProsesList;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PerizinanTerbitImport;
use App\Imports\PerizinanProsesImport;
use App\Exports\PerizinanTerbitExport;
use App\Exports\PerizinanProsesExport;

class PerizinanPerkantoranController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('en');
        $tanggalToday = Carbon::now()->format('l, d F Y');
        
        $tahunTersedia = PerizinanTerbit::select(DB::raw('YEAR(tanggal_sejak) as tahun'))
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun')
                            ->toArray();

        if (empty($tahunTersedia)) {
            $tahunTersedia = [Carbon::now()->year];
        }

        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua'); 

        // Map nama bulan Indonesia ke angka
        $mapBulan = [
            'Januari' => 1, 'Februari' => 2,
            'Maret' => 3, 'April' => 4, 'Mei' => 5,
            'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        // --- QUERY TABEL 2: RINCIAN PERIZINAN TERBIT ---
        $queryRincian = PerizinanTerbit::leftJoin('jenis_perizinan_master', 'perizinan_terbit.jenis_perizinan_id', '=', 'jenis_perizinan_master.id')
            ->select('perizinan_terbit.*', 'jenis_perizinan_master.nama_jenis as nama_perizinan');

        if ($filterTahun != 'semua') {
            $queryRincian->whereYear('tanggal_sejak', $filterTahun);
        }

        if ($filterBulan != 'semua') {
            $monthNum = $mapBulan[$filterBulan] ?? null;
            if ($monthNum) {
                $queryRincian->whereMonth('tanggal_sejak', $monthNum);
            }
        }

        $dataRincian = $queryRincian->orderBy('tanggal_sejak', 'desc')
            ->paginate(10, ['*'], 'page_terbit')
            ->fragment('tabel-terbit')
            ->appends(request()->all());

        // --- QUERY TABEL 1: RINGKASAN AKUMULASI ---
        $queryRingkasan = PerizinanTerbit::leftJoin('jenis_perizinan_master', 'perizinan_terbit.jenis_perizinan_id', '=', 'jenis_perizinan_master.id')
            ->selectRaw('
                YEAR(perizinan_terbit.tanggal_sejak) as tahun,
                MONTH(perizinan_terbit.tanggal_sejak) as bulan_num,
                SUM(CASE WHEN perizinan_terbit.kegiatan = \'Produk\' THEN 1 ELSE 0 END) as produk,
                SUM(CASE WHEN perizinan_terbit.kegiatan = \'Aset\' THEN 1 ELSE 0 END) as aset,
                SUM(CASE WHEN perizinan_terbit.kegiatan = \'Proyek\' THEN 1 ELSE 0 END) as proyek,
                SUM(CASE WHEN perizinan_terbit.kegiatan = \'Peralatan Pabrik\' THEN 1 ELSE 0 END) as peralatan_pabrik,
                SUM(CASE WHEN perizinan_terbit.kegiatan = \'Adm & Lainnya\' THEN 1 ELSE 0 END) as adm
            ');

        if ($filterTahun != 'semua') {
            $queryRingkasan->whereYear('perizinan_terbit.tanggal_sejak', $filterTahun);
        }
        if ($filterBulan != 'semua') {
            $monthNum = $mapBulan[$filterBulan] ?? null;
            if ($monthNum) {
                $queryRingkasan->whereMonth('perizinan_terbit.tanggal_sejak', $monthNum);
            }
        }

        $ringkasanRaw = $queryRingkasan->groupBy('tahun', 'bulan_num')
                                       ->orderBy('tahun', 'desc')
                                       ->orderBy('bulan_num', 'desc')
                                       ->paginate(10, ['*'], 'page_ringkasan')
                                       ->fragment('tabel-ringkasan')
                                       ->appends(request()->all());

        $daftarBulanTeks = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $dataRingkasan = [];

        foreach ($ringkasanRaw->items() as $row) {
            $dataRingkasan[] = [
                'tahun' => $row->tahun,
                'bulan' => $daftarBulanTeks[$row->bulan_num],
                'produk' => $row->produk,
                'aset' => $row->aset,
                'proyek' => $row->proyek,
                'peralatan_pabrik' => $row->peralatan_pabrik,
                'adm' => $row->adm,
                'total_terbit' => $row->produk + $row->aset + $row->proyek + $row->peralatan_pabrik + $row->adm,
            ];
        }

        // --- QUERY CHART ---
        $chartData = [];

        if ($filterTahun == 'semua') {
            $tahunAsc = array_reverse($tahunTersedia);
            $chartQuery = PerizinanTerbit::query();
            
            if ($filterBulan != 'semua') {
                $monthNum = $mapBulan[$filterBulan] ?? null;
                if ($monthNum) $chartQuery->whereMonth('tanggal_sejak', $monthNum);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($tahunAsc as $thn) {
                $dataTahunIni = $rawDataForChart->filter(function($item) use ($thn) {
                    return Carbon::parse($item->tanggal_sejak)->year == $thn;
                });
                
                $chartData[] = [
                    'label' => (string)$thn,
                    'produk' => $dataTahunIni->where('kegiatan', 'Produk')->count(),
                    'aset' => $dataTahunIni->where('kegiatan', 'Aset')->count(),
                    'proyek' => $dataTahunIni->where('kegiatan', 'Proyek')->count(),
                    'peralatan_pabrik' => $dataTahunIni->where('kegiatan', 'Peralatan Pabrik')->count(),
                    'adm' => $dataTahunIni->where('kegiatan', 'Adm & Lainnya')->count(),
                ];
            }
        } else {
            $chartMonthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartQuery = PerizinanTerbit::whereYear('tanggal_sejak', $filterTahun);
            
            if ($filterBulan != 'semua') {
                $monthNum = $mapBulan[$filterBulan] ?? null;
                if ($monthNum) $chartQuery->whereMonth('tanggal_sejak', $monthNum);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($chartMonthsShort as $index => $monthShort) {
                $monthNumber = $index + 1; 
                $dataBulanIni = $rawDataForChart->filter(function($item) use ($monthNumber) {
                    return Carbon::parse($item->tanggal_sejak)->month == $monthNumber;
                });

                $chartData[] = [
                    'label' => $monthShort,
                    'produk' => $dataBulanIni->where('kegiatan', 'Produk')->count(),
                    'aset' => $dataBulanIni->where('kegiatan', 'Aset')->count(),
                    'proyek' => $dataBulanIni->where('kegiatan', 'Proyek')->count(),
                    'peralatan_pabrik' => $dataBulanIni->where('kegiatan', 'Peralatan Pabrik')->count(),
                    'adm' => $dataBulanIni->where('kegiatan', 'Adm & Lainnya')->count(),
                ];
            }
        }

        // --- QUERY TABEL 3: PERIZINAN PROSES (DENGAN GROUPING ROWSPAN) ---
        $queryProses = PerizinanProsesList::query();
        if ($filterTahun != 'semua') {
            $queryProses->where('tahun', $filterTahun);
        }
        
        $dataProses = $queryProses->orderBy('tahun', 'desc')
                                  ->orderBy('nama_proses', 'asc')
                                  ->orderBy('id', 'asc')
                                  ->paginate(10, ['*'], 'page_proses')
                                  ->fragment('tabel-proses')
                                  ->appends(request()->all());

        // Grouping data berdasarkan tahun dan nama proses untuk rowspan di Blade
        $groupedProses = collect($dataProses->items())->groupBy(function ($item) {
            return $item->tahun . '_' . $item->nama_proses;
        });

        // --- 6. AMBIL KONFIGURASI KOLOM DINAMIS (Keduanya) ---
        $kolomDinamisTerbit = DB::table('dynamic_columns')->where('modul', 'perizinan_terbit')->get();
        $kolomDinamisProses = DB::table('dynamic_columns')->where('modul', 'perizinan_proses_list')->get();

        return view('perizinan-perkantoran', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'dataRincian', 'dataRingkasan', 'chartData', 'groupedProses', 'ringkasanRaw', 'dataProses',
            'kolomDinamisTerbit', 'kolomDinamisProses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perizinan'    => 'required|string|max:150',
            'kegiatan'          => 'required|string|in:Aset,Adm & Lainnya,Peralatan Pabrik,Produk,Proyek',
            'nomor'             => 'required|string|max:100',
            // Format input date (Y-m) divalidasi sebagai date
            'tanggal_sejak'     => 'required|date',
            // Pastikan format date bisa divalidasi after
            'tanggal_akhir'     => 'required|date|after_or_equal:tanggal_sejak',
            'instansi_penerbit' => 'required|string|max:150',
        ]);

        $jenis = DB::table('jenis_perizinan_master')
            ->whereRaw('LOWER(nama_jenis) = ?', [strtolower($request->nama_perizinan)])
            ->first();

        if (!$jenis) {
            $jenisId = DB::table('jenis_perizinan_master')->insertGetId([
                'nama_jenis' => $request->nama_perizinan,
                'kategori_grup' => $request->kegiatan,
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $jenisId = $jenis->id;
        }

        $request->merge([
            'kategori_grup' => $request->kegiatan,
            'jenis_perizinan_id' => $jenisId
        ]);
        PerizinanTerbit::create($request->except('nama_perizinan'));

        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Rincian Perizinan Terbit berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_perizinan'    => 'required|string|max:150',
            'kegiatan'          => 'required|string|in:Aset,Adm & Lainnya,Peralatan Pabrik,Produk,Proyek',
            'nomor'             => 'required|string|max:100',
            'tanggal_sejak'     => 'required|date',
            'tanggal_akhir'     => 'required|date|after_or_equal:tanggal_sejak',
            'instansi_penerbit' => 'required|string|max:150',
        ]);

        $jenis = DB::table('jenis_perizinan_master')
            ->whereRaw('LOWER(nama_jenis) = ?', [strtolower($request->nama_perizinan)])
            ->first();

        if (!$jenis) {
            $jenisId = DB::table('jenis_perizinan_master')->insertGetId([
                'nama_jenis' => $request->nama_perizinan,
                'kategori_grup' => $request->kegiatan,
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $jenisId = $jenis->id;
        }

        $request->merge([
            'kategori_grup' => $request->kegiatan,
            'jenis_perizinan_id' => $jenisId
        ]);
        PerizinanTerbit::findOrFail($id)->update($request->except('nama_perizinan'));

        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Terbit berhasil diperbarui.');
    }

        public function destroyBulkTerbit(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perizinan_terbit,id',
        ]);

        if ($request->delete_all_pages == '1') {
            $query = \App\Models\PerizinanTerbit::query();
            if ($request->filter_tahun && $request->filter_tahun != 'semua') {
                $query->whereYear('tanggal_sejak', $request->filter_tahun);
            }
            if ($request->filter_bulan && $request->filter_bulan != 'semua') {
                $mapBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
                $monthNum = $mapBulan[$request->filter_bulan] ?? null;
                if ($monthNum) $query->whereMonth('tanggal_sejak', $monthNum);
            }
            $count = $query->count();
            $query->delete();
            return redirect()->back()->with('success', $count . ' Seluruh Data perizinan terbit yang difilter berhasil dihapus.');
        } else {
            \App\Models\PerizinanTerbit::whereIn('id', $request->ids)->delete();
            return redirect()->back()->with('success', count($request->ids) . ' Data perizinan terbit berhasil dihapus.');
        }
    }

    public function destroy($id)
    {
        PerizinanTerbit::findOrFail($id)->delete();
        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Terbit berhasil dihapus.');
    }

    public function storeProses(Request $request)
    {
        $request->validate([
            'tahun'       => 'required|integer',
            'nama_proses' => 'required|string|max:255',
            'target'      => 'nullable|string',
            'periode'     => 'nullable|string|max:100',
        ]);

        PerizinanProsesList::updateOrCreate([
            'tahun'       => $request->tahun,
            'nama_proses' => $request->nama_proses,
            'target'      => $request->target,
            'periode'     => $request->periode,
        ], $request->all());

        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Proses berhasil ditambahkan.');
    }

    public function updateProses(Request $request, $id)
    {
        $request->validate([
            'tahun'       => 'required|integer',
            'nama_proses' => 'required|string|max:255',
            'target'      => 'nullable|string',
            'periode'     => 'nullable|string|max:100',
        ]);

        PerizinanProsesList::findOrFail($id)->update($request->all());

        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Proses berhasil diperbarui.');
    }

        public function destroyBulkProses(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perizinan_proses_list,id',
        ]);

        if ($request->delete_all_pages == '1') {
            $query = \App\Models\PerizinanProsesList::query();
            if ($request->filter_tahun && $request->filter_tahun != 'semua') {
                $query->where('tahun', $request->filter_tahun);
            }
            $count = $query->count();
            $query->delete();
            return redirect()->back()->with('success', $count . ' Seluruh Data perizinan proses yang difilter berhasil dihapus.');
        } else {
            \App\Models\PerizinanProsesList::whereIn('id', $request->ids)->delete();
            return redirect()->back()->with('success', count($request->ids) . ' Data perizinan proses berhasil dihapus.');
        }
    }

    public function destroyProses($id)
    {
        PerizinanProsesList::findOrFail($id)->delete();
        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Proses berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        set_time_limit(0);

        try {
            Excel::import(new PerizinanTerbitImport, $request->file('file'));
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('perizinan-perkantoran.index')->with('success', 'Data Perizinan Terbit berhasil diimpor!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Gagal mengimpor data: ' . $e->getMessage()], 500);
            }
            return redirect()->route('perizinan-perkantoran.index')->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function importProses(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        set_time_limit(0);

        try {
            Excel::import(new PerizinanProsesImport, $request->file('file'));
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('perizinan-perkantoran.index')->with('success', 'Data Perizinan Proses berhasil diimpor!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Gagal mengimpor data: ' . $e->getMessage()], 500);
            }
            return redirect()->route('perizinan-perkantoran.index')->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        return Excel::download(new PerizinanTerbitExport, 'Perizinan_Terbit.xlsx');
    }

    public function exportPdf()
    {
        return Excel::download(new PerizinanTerbitExport, 'Perizinan_Terbit.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function exportExcelProses()
    {
        return Excel::download(new PerizinanProsesExport, 'Perizinan_Proses.xlsx');
    }

    public function exportPdfProses()
    {
        return Excel::download(new PerizinanProsesExport, 'Perizinan_Proses.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function downloadTemplate()
    {
        return Excel::download(new PerizinanTerbitExport(true), 'Template_Perizinan_Terbit.xlsx');
    }

    public function downloadTemplateProses()
    {
        return Excel::download(new PerizinanProsesExport(true), 'Template_Perizinan_Proses.xlsx');
    }

    /**
     * Mengambil data Perizinan Perkantoran untuk laporan PDF bulanan.
     * Single source of truth: identik dengan dashboard.
     */
    public static function getReportData($tahun, $bulan)
    {
        $mapBulan = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];
        $bulanNum = $mapBulan[$bulan] ?? null;

        // Perizinan Terbit: filter by tahun dan bulan (menggunakan tanggal_sejak)
        $queryTerbit = \App\Models\PerizinanTerbit::leftJoin('jenis_perizinan_master', 'perizinan_terbit.jenis_perizinan_id', '=', 'jenis_perizinan_master.id')
            ->whereYear('perizinan_terbit.tanggal_sejak', $tahun)
            ->select('perizinan_terbit.*', 'jenis_perizinan_master.nama_jenis as nama_perizinan');
        if ($bulanNum) {
            $queryTerbit->whereMonth('perizinan_terbit.tanggal_sejak', $bulanNum);
        }
        $perizinanTerbit = $queryTerbit->orderBy('tanggal_sejak', 'desc')->get();

        // Perizinan Proses: hanya filter by tahun (tidak ada kolom bulan)
        $perizinanProses = \App\Models\PerizinanProsesList::where('tahun', $tahun)
            ->orderBy('nama_proses', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Ringkasan Kegiatan Perizinan Terbit untuk periode terpilih (Tabel 1 di menu)
        $ringkasanTerbit = (object) [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'produk' => $perizinanTerbit->where('kegiatan', 'Produk')->count(),
            'aset' => $perizinanTerbit->where('kegiatan', 'Aset')->count(),
            'proyek' => $perizinanTerbit->where('kegiatan', 'Proyek')->count(),
            'peralatan_pabrik' => $perizinanTerbit->where('kegiatan', 'Peralatan Pabrik')->count(),
            'adm' => $perizinanTerbit->where('kegiatan', 'Adm & Lainnya')->count(),
        ];
        $ringkasanTerbit->total_terbit = $ringkasanTerbit->produk + $ringkasanTerbit->aset
            + $ringkasanTerbit->proyek + $ringkasanTerbit->peralatan_pabrik + $ringkasanTerbit->adm;

        // Statistik Perizinan Terbit (Semua Tahun) - untuk chart, TIDAK terikat filter tahun/bulan laporan
        $tahunSemua = \App\Models\PerizinanTerbit::selectRaw('YEAR(tanggal_sejak) as tahun')
            ->distinct()->orderBy('tahun', 'asc')->pluck('tahun');

        $statistikTerbitSemuaTahun = $tahunSemua->map(function ($thn) {
            $rows = \App\Models\PerizinanTerbit::whereYear('tanggal_sejak', $thn)->get();
            return (object) [
                'tahun' => $thn,
                'produk' => $rows->where('kegiatan', 'Produk')->count(),
                'aset' => $rows->where('kegiatan', 'Aset')->count(),
                'proyek' => $rows->where('kegiatan', 'Proyek')->count(),
                'peralatan_pabrik' => $rows->where('kegiatan', 'Peralatan Pabrik')->count(),
                'adm' => $rows->where('kegiatan', 'Adm & Lainnya')->count(),
            ];
        })->values();

        \Log::info('[PDF Section] Perizinan', [
            'bulan' => $bulan, 'tahun' => $tahun,
            'terbit' => $perizinanTerbit->count(), 'proses' => $perizinanProses->count()
        ]);

        return [
            'perizinanTerbit' => $perizinanTerbit,
            'perizinanProses' => $perizinanProses,
            'ringkasanTerbit' => $ringkasanTerbit,
            'statistikTerbitSemuaTahun' => $statistikTerbitSemuaTahun,
        ];
    }
}