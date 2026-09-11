<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JasaFotocopy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class JasaFotocopyController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('id')->translatedFormat('l, d F Y');
        
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $tahunTersedia = JasaFotocopy::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $namaBulanUrut = array_keys($monthsOrder);

        $tahunScope = ($filterTahun === 'semua') ? $tahunTersedia : [$filterTahun];
        $bulanScope = ($filterBulan === 'semua') ? $namaBulanUrut : [$filterBulan];

        $allData = JasaFotocopy::whereIn('tahun', $tahunScope)->get();
        $dataByTahun = $allData->groupBy('tahun');

        // TABEL 1: REKAPITULASI
        $dataTable1 = [];
        foreach ($tahunScope as $thn) {
            $yearDataThn = $dataByTahun->get($thn, collect());
            $groupedByMonth = $yearDataThn->groupBy('bulan');
            foreach ($groupedByMonth as $bulan => $items) {
                if (!in_array($bulan, $bulanScope)) continue;
                $mesinAktif = $items->unique('unit_kerja')->count();
                $totalPemakaian = $items->sum('pemakaian_lembar');
                $totalNilai = $items->sum(function($item) {
                    return ($item->pemakaian_lembar * $item->biaya_fee_per_lembar) + $item->biaya_sewa_mesin;
                });
                $dataTable1[] = ['tahun' => $thn, 'bulan' => $bulan, 'mesin_fc' => $mesinAktif, 'jumlah_pemakaian' => $totalPemakaian, 'nilai_jasa' => $totalNilai];
            }
        }
        usort($dataTable1, function($a, $b) use ($monthsOrder) {
            return [$a['tahun'], $monthsOrder[$a['bulan']]] <=> [$b['tahun'], $monthsOrder[$b['bulan']]];
        });

        $chartLabels = $namaBulanUrut;
        $chartData = array_fill(0, 12, 0);
        foreach ($dataTable1 as $dt) {
            $monthIndex = array_search($dt['bulan'], $chartLabels);
            if ($monthIndex !== false) { $chartData[$monthIndex] += $dt['jumlah_pemakaian']; }
        }
        $chartJsonData = ['labels' => $chartLabels, 'datasets' => [['label' => 'Total Pemakaian Lembar', 'data' => $chartData, 'backgroundColor' => '#3b82f6']]];

        // TABEL 2: DETAIL
        $table2Result = self::getTable2Data($filterTahun, $filterBulan);
        $dataTable2 = $table2Result['dataTable2'];
        $subtotalGroups = $table2Result['subtotalGroups'];
        $grandTotals = $table2Result['grandTotals'];

        $tampilkanSubtotalGrup = count($subtotalGroups) > 1;
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'jasa_fotocopy')->get();

        // Paginate dataTable2 (10 per page)
        $perPage = 10;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $paginatedData = array_slice($dataTable2, $offset, $perPage);
        $dataTable2Paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            count($dataTable2),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('jasa-fotocopy', [
            'tanggalToday' => $tanggalToday,
            'filterTahun' => $filterTahun,
            'filterBulan' => $filterBulan,
            'tahunTersedia' => $tahunTersedia,
            'dataTable1' => $dataTable1,
            'dataTable2' => $dataTable2Paginated,
            'grandTotals' => $grandTotals,
            'subtotalGroups' => $subtotalGroups,
            'tampilkanSubtotalGrup' => $tampilkanSubtotalGrup,
            'kolomDinamis' => $kolomDinamis,
            'chartJsonData' => $chartJsonData
        ]);
    }

    public static function getTable2Data($filterTahun, $filterBulan)
    {
        $tahunTersedia = JasaFotocopy::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $namaBulanUrut = array_keys($monthsOrder);

        $tahunScope = ($filterTahun === 'semua') ? $tahunTersedia : [$filterTahun];
        $bulanScope = ($filterBulan === 'semua') ? $namaBulanUrut : [$filterBulan];

        $allData = JasaFotocopy::whereIn('tahun', $tahunScope)->get();
        $dataByTahun = $allData->groupBy('tahun');

        $dataTable2 = [];
        $subtotalGroups = [];
        $grandTotals = ['pemakaian_bln' => 0, 'pemakaian_sd' => 0, 'fee_bln' => 0, 'fee_sd' => 0, 'sewa_bln' => 0, 'total_bln' => 0, 'total_sd' => 0];

        foreach ($tahunScope as $thn) {
            $yearDataThn = $dataByTahun->get($thn, collect());

            foreach ($namaBulanUrut as $bulanNama) {
                if (!in_array($bulanNama, $bulanScope)) continue;

                $targetMonthIdx = $monthsOrder[$bulanNama];
                $filteredYearData = $yearDataThn->filter(function($item) use ($monthsOrder, $targetMonthIdx) {
                    return isset($monthsOrder[$item->bulan]) && $monthsOrder[$item->bulan] <= $targetMonthIdx;
                });

                $groupTotals = ['pemakaian_bln' => 0, 'pemakaian_sd' => 0, 'fee_bln' => 0, 'fee_sd' => 0, 'sewa_bln' => 0, 'total_bln' => 0, 'total_sd' => 0];
                $dataBulanIniFull = $filteredYearData->where('bulan', $bulanNama);
                $unitKerjas = $filteredYearData->unique('unit_kerja')->pluck('unit_kerja');

                foreach ($unitKerjas as $uk) {
                    $ytdData = $filteredYearData->where('unit_kerja', $uk);
                    $dataBulanIni = $dataBulanIniFull->where('unit_kerja', $uk)->first();
                    if (!$dataBulanIni) continue;

                    $pemakaianBlnIni = $dataBulanIni->pemakaian_lembar;
                    $feePerLbr = $dataBulanIni->biaya_fee_per_lembar;
                    $sewaBlnIni = $dataBulanIni->biaya_sewa_mesin;
                    $pemakaianSd = $ytdData->sum('pemakaian_lembar');
                    $feeBlnIni = $pemakaianBlnIni * $feePerLbr;

                    $feeSd = 0; $sewaSd = 0;
                    foreach ($ytdData as $md) {
                        $feeSd += ($md->pemakaian_lembar * $md->biaya_fee_per_lembar);
                        $sewaSd += $md->biaya_sewa_mesin;
                    }

                    $totalJasaSewaBlnIni = $feeBlnIni + $sewaBlnIni;
                    $totalJasaSewaSd = $feeSd + $sewaSd;

                    $dataTable2[] = [
                        'id' => $dataBulanIni->id, 'tahun' => $thn, 'bulan' => $bulanNama,
                        'unit_kerja' => $dataBulanIni->unit_kerja, 'cost_centre' => $dataBulanIni->cost_centre,
                        'keterangan' => $dataBulanIni->keterangan, 'tipe_mesin' => $dataBulanIni->tipe_mesin,
                        'pemakaian_bln_ini' => $pemakaianBlnIni, 'pemakaian_sd' => $pemakaianSd,
                        'fee_bln_ini' => $feeBlnIni, 'fee_sd' => $feeSd, 'fee_per_lbr' => $feePerLbr,
                        'sewa_bln_ini' => $sewaBlnIni, 'total_bln_ini' => $totalJasaSewaBlnIni, 'total_sd' => $totalJasaSewaSd,
                        'data_tambahan' => $dataBulanIni->data_tambahan,
                    ];

                    $delta = ['pemakaian_bln' => $pemakaianBlnIni, 'pemakaian_sd' => $pemakaianSd, 'fee_bln' => $feeBlnIni, 'fee_sd' => $feeSd, 'sewa_bln' => $sewaBlnIni, 'total_bln' => $totalJasaSewaBlnIni, 'total_sd' => $totalJasaSewaSd];
                    foreach ($delta as $key => $val) { $groupTotals[$key] += $val; $grandTotals[$key] += $val; }
                }

                if ($dataBulanIniFull->count() > 0) {
                    $subtotalGroups[$thn . '|' . $bulanNama] = ['tahun' => $thn, 'bulan' => $bulanNama, 'totals' => $groupTotals];
                }
            }
        }
        
        return [
            'dataTable2' => $dataTable2,
            'subtotalGroups' => $subtotalGroups,
            'grandTotals' => $grandTotals
        ];
    }

    public function store(Request $request)
    {
        $request->validate(['tahun' => 'required', 'bulan' => 'required', 'unit_kerja' => 'required', 'pemakaian' => 'required|numeric']);
        JasaFotocopy::updateOrCreate(
            ['tahun' => $request->tahun, 'bulan' => $request->bulan, 'unit_kerja' => $request->unit_kerja],
            [
                'cost_centre' => $request->cost_centre, 'keterangan' => $request->keterangan ?? 'KOPKAR', 'tipe_mesin' => $request->tipe_mesin,
                'pemakaian_lembar' => $request->pemakaian, 'biaya_fee_per_lembar' => $request->fee ?? 47.22,
                'biaya_sewa_mesin' => $request->sewa ?? 909000, 'data_tambahan' => $request->input('data_tambahan', [])
            ]
        );
        return back()->with('success', 'Data baru berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['unit_kerja' => 'required', 'pemakaian' => 'required|numeric']);
        $row = JasaFotocopy::findOrFail($id);
        $row->update([
            'unit_kerja' => $request->unit_kerja, 'cost_centre' => $request->cost_centre,
            'keterangan' => $request->keterangan ?? 'KOPKAR', 'tipe_mesin' => $request->tipe_mesin,
            'pemakaian_lembar' => $request->pemakaian, 'biaya_fee_per_lembar' => $request->fee ?? 47.22,
            'biaya_sewa_mesin' => $request->sewa ?? 909000, 'data_tambahan' => $request->input('data_tambahan', [])
        ]);
        return back()->with('success', 'Data berhasil diperbarui!');
    }

    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        if ($request->delete_all_pages == '1') {
            $query = \App\Models\JasaFotocopy::query();
            if ($request->tahun && $request->tahun != 'semua') $query->where('tahun', $request->tahun);
            if ($request->bulan && $request->bulan != 'semua') $query->where('bulan', $request->bulan);
            $count = $query->count();
            $query->delete();
            return redirect()->back()->with('success', $count . ' Data jasa fotocopy (dari semua halaman) berhasil dihapus.');
        } else {
            $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:jasa_fotocopy,id']);
            \App\Models\JasaFotocopy::whereIn('id', $request->ids)->delete();
            return redirect()->back()->with('success', count($request->ids) . ' Data jasa fotocopy berhasil dihapus.');
        }
    }

    public function destroy($id)
    {
        JasaFotocopy::findOrFail($id)->delete();
        return back()->with('success', 'Data baris tersebut berhasil dihapus.');
    }

    public function storeKolomDinamis(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        $request->validate(['modul' => 'required|string', 'nama_kolom' => 'required|string|max:100', 'tipe_input' => 'required|in:text,number,date,dropdown,currency']);
        $isDuplicate = DB::table('dynamic_columns')->where('modul', $request->modul)->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim($request->nama_kolom))])->exists();
        if ($isDuplicate) return back()->with('error_modal', 'Kolom dengan nama "' . $request->nama_kolom . '" sudah ada!')->with('failed_modul', $request->modul);

        $pilihanDropdown = null;
        if ($request->tipe_input === 'dropdown' && $request->pilihan_dropdown) {
            $pilihanDropdown = json_encode(array_map('trim', explode(',', $request->pilihan_dropdown)));
        }

        DB::table('dynamic_columns')->insert([
            'modul' => $request->modul, 'nama_kolom' => trim($request->nama_kolom), 'tipe_input' => $request->tipe_input,
            'pilihan_dropdown' => $pilihanDropdown, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
        ]);
        return back()->with('success', 'Kolom dinamis berhasil ditambahkan.');
    }

    public function destroyKolomDinamis($id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        DB::table('dynamic_columns')->where('id', $id)->delete();
        return back()->with('success', 'Kolom dinamis berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        set_time_limit(0);
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:51200']);
        try {
            Excel::import(new \App\Imports\JasaFotocopyImport, $request->file('file'));
            return response()->json(['success' => true, 'message' => 'Data Jasa Fotocopy berhasil diimport.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal import: ' . $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', 'semua');
        $bulan = $request->input('bulan', 'semua');
        try {
            return Excel::download(new \App\Exports\JasaFotocopyExport(false, $tahun, $bulan), 'Data_Jasa_Fotocopy.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Gagal Export Excel. Detail: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $table2Result = self::getTable2Data($filterTahun, $filterBulan);
        $dataTable2 = $table2Result['dataTable2'];
        $subtotalGroups = $table2Result['subtotalGroups'];
        $grandTotals = $table2Result['grandTotals'];

        $pdf = Pdf::loadView('pdf.jasa-fotocopy', compact('filterTahun', 'filterBulan', 'dataTable2', 'subtotalGroups', 'grandTotals'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('Laporan_Jasa_Fotocopy_Detail.pdf');
    }

    /**
     * Mengambil data Jasa Fotocopy untuk laporan PDF bulanan.
     * Single source of truth: menggunakan logika yang sama dengan halaman dashboard.
     */
    public static function getReportData($tahun, $bulan)
    {
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];

        $mapBulanNum = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];
        $bulanNum = $mapBulanNum[$bulan] ?? null;

        $allData = \App\Models\JasaFotocopy::where('tahun', $tahun)
            ->where(function($q) use ($bulan, $bulanNum) {
                $q->whereRaw('LOWER(TRIM(bulan)) = ?', [strtolower(trim($bulan))]);
                if ($bulanNum) {
                    $q->orWhereRaw('CAST(bulan AS UNSIGNED) = ?', [$bulanNum]);
                }
            })
            ->get();

        // Rekapitulasi Tabel 1 (sesuai dashboard)
        $mesinAktif    = $allData->unique('unit_kerja')->count();
        $totalPemakaian= $allData->sum('pemakaian_lembar');
        $totalNilai    = $allData->sum(function($item) {
            return ($item->pemakaian_lembar * $item->biaya_fee_per_lembar) + $item->biaya_sewa_mesin;
        });

        $dataTable1 = [];
        if ($allData->isNotEmpty()) {
            $dataTable1[] = [
                'tahun'           => $tahun,
                'bulan'           => $bulan,
                'mesin_fc'        => $mesinAktif,
                'jumlah_pemakaian'=> $totalPemakaian,
                'nilai_jasa'      => $totalNilai,
            ];
        }

        // Detail Tabel 2 (reuse getTable2Data yang sudah ada)
        $table2Result  = self::getTable2Data($tahun, $bulan);

        \Log::info('[PDF Section] Jasa Fotocopy', [
            'bulan' => $bulan, 'tahun' => $tahun,
            'count' => $allData->count(), 'totalPemakaian' => $totalPemakaian
        ]);

        return [
            'dataTable1'    => $dataTable1,
            'dataTable2'    => $table2Result['dataTable2'],
            'subtotalGroups'=> $table2Result['subtotalGroups'],
            'grandTotals'   => $table2Result['grandTotals'],
            'mesinAktif'    => $mesinAktif,
            'totalPemakaian'=> $totalPemakaian,
            'totalNilai'    => $totalNilai,
        ];
    }
}