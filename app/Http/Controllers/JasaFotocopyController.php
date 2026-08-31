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

        // =====================================
        // TABEL 1: REKAPITULASI (Semua Tahun & Bulan Sesuai Filter)
        // =====================================
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

        // =====================================
        // TABEL 2: DETAIL SPREADSHEET (Semua Data Sesuai Filter)
        // =====================================
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
                
                // Ambil daftar unit kerja unik yang pernah diinput sampai bulan ini
                $unitKerjas = $filteredYearData->unique('unit_kerja')->pluck('unit_kerja');

                foreach ($unitKerjas as $uk) {
                    $ytdData = $filteredYearData->where('unit_kerja', $uk);
                    $dataBulanIni = $dataBulanIniFull->where('unit_kerja', $uk)->first();
                    
                    // Kalau di bulan ini tidak ada pemakaian, tidak perlu ditampilkan barisnya
                    if(!$dataBulanIni) continue;

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

                    $delta = [
                        'pemakaian_bln' => $pemakaianBlnIni, 'pemakaian_sd' => $pemakaianSd,
                        'fee_bln' => $feeBlnIni, 'fee_sd' => $feeSd, 'sewa_bln' => $sewaBlnIni,
                        'total_bln' => $totalJasaSewaBlnIni, 'total_sd' => $totalJasaSewaSd,
                    ];
                    foreach ($delta as $key => $val) {
                        $groupTotals[$key] += $val;
                        $grandTotals[$key] += $val;
                    }
                }

                if($dataBulanIniFull->count() > 0) {
                    $subtotalGroups[$thn . '|' . $bulanNama] = ['tahun' => $thn, 'bulan' => $bulanNama, 'totals' => $groupTotals];
                }
            }
        }

        $tampilkanSubtotalGrup = count($subtotalGroups) > 1;
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'jasa_fotocopy')->get();

        return view('jasa-fotocopy', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia', 
            'dataTable1', 'dataTable2', 'grandTotals', 'subtotalGroups', 'tampilkanSubtotalGrup',
            'kolomDinamis', 'chartJsonData'
        ));
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

    public function destroy($id)
    {
        JasaFotocopy::findOrFail($id)->delete();
        return back()->with('success', 'Data baris tersebut berhasil dihapus.');
    }

    public function storeKolomDinamis(Request $request)
    {
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
        DB::table('dynamic_columns')->where('id', $id)->delete();
        return back()->with('success', 'Kolom dinamis berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        set_time_limit(0);
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls,csv|max:51200']);
        try {
            Excel::import(new \App\Imports\JasaFotocopyImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import. Detail: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', 'semua');
        $bulan = $request->input('bulan', 'semua');
        try {
            return Excel::download(new \App\Exports\JasaFotocopyExport($tahun, $bulan), 'Data_Jasa_Fotocopy.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Gagal Export Excel. Detail: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $query = JasaFotocopy::query();
        if ($filterTahun !== 'semua') $query->where('tahun', $filterTahun);
        if ($filterBulan !== 'semua') $query->where('bulan', $filterBulan);
        $dataRaw = $query->get();

        $dataTable1 = [];
        $groupedByYear = $dataRaw->groupBy('tahun');
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];

        foreach($groupedByYear as $thn => $yearItems) {
            $groupedByMonth = $yearItems->groupBy('bulan');
            foreach($groupedByMonth as $bulan => $items) {
                $dataTable1[] = [
                    'tahun' => $thn, 'bulan' => $bulan,
                    'mesin_fc' => $items->unique('unit_kerja')->count(),
                    'jumlah_pemakaian' => $items->sum('pemakaian_lembar'),
                    'nilai_jasa' => $items->sum(function($i) { return ($i->pemakaian_lembar * $i->biaya_fee_per_lembar) + $i->biaya_sewa_mesin; })
                ];
            }
        }
        usort($dataTable1, function($a, $b) use ($monthsOrder) { 
            if ($a['tahun'] == $b['tahun']) { return $monthsOrder[$a['bulan']] <=> $monthsOrder[$b['bulan']]; }
            return $b['tahun'] <=> $a['tahun']; 
        });

        $pdf = Pdf::loadView('pdf.jasa-fotocopy', compact('filterTahun', 'filterBulan', 'dataTable1'))->setPaper('a4', 'portrait');
        return $pdf->download('Laporan_Jasa_Fotocopy_Rekap.pdf');
    }
}
