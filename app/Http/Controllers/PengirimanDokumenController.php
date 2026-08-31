<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengirimanDokumen; 
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// Import Package Excel & PDF
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PengirimanDokumenImport;
use App\Exports\PengirimanDokumenExport;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanDokumenController extends Controller
{
    protected $masterMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    public function index(Request $request)
    {
        $now = Carbon::now();
        $selectedYear = $request->input('year', $now->year); 
        $selectedMonth = $request->input('month', 'semua'); 

        $availableYears = PengirimanDokumen::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty()) $availableYears = collect([$now->year]); 

        $baseQuery = PengirimanDokumen::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $q->where('tahun', 'like', "%{$search}%")->orWhere('bulan', 'like', "%{$search}%")
                  ->orWhere('penerimaan_mailroom', 'like', "%{$search}%")->orWhere('pengiriman_dalam_negeri', 'like', "%{$search}%")
                  ->orWhere('pengiriman_luar_negeri', 'like', "%{$search}%")->orWhere('registrasi_surat_masuk_dof', 'like', "%{$search}%")
                  ->orWhere('ongkir_dalam_negeri', 'like', "%{$search}%")->orWhere('ongkir_luar_negeri', 'like', "%{$search}%");
            });
        }

        if ($selectedYear != 'semua') $baseQuery->where('tahun', $selectedYear);
        if ($selectedMonth != 'semua') $baseQuery->where('bulan', $selectedMonth);

        $dataForChartBuilder = clone $baseQuery;
        $allDataKeyed = $dataForChartBuilder->get()->keyBy('bulan'); 
        $chartLabels = ($selectedMonth == 'semua') ? $this->masterMonths : [$selectedMonth];
        
        $chartMailroom = []; $chartDof = []; $chartDomestikVolume = []; $chartInternasionalVolume = [];
        foreach ($chartLabels as $bulanNama) {
            $dataRow = $allDataKeyed->get($bulanNama);
            $chartMailroom[] = $dataRow ? $dataRow->penerimaan_mailroom : 0;
            $chartDof[] = $dataRow ? $dataRow->registrasi_surat_masuk_dof : 0;
            $chartDomestikVolume[] = $dataRow ? $dataRow->pengiriman_dalam_negeri : 0;
            $chartInternasionalVolume[] = $dataRow ? $dataRow->pengiriman_luar_negeri : 0;
        }

        $chartVolumeConfig = [
            'labels' => $chartLabels,
            'datasets' => [
                ['label' => 'Penerimaan Mailroom', 'backgroundColor' => '#0056A3', 'data' => $chartMailroom],
                ['label' => 'Registrasi Surat Masuk via DOF', 'backgroundColor' => '#F7941E', 'data' => $chartDof],
                ['label' => 'Pengiriman Dalam Negeri', 'backgroundColor' => '#22C55E', 'data' => $chartDomestikVolume],
                ['label' => 'Pengiriman Luar Negeri', 'backgroundColor' => '#F87171', 'data' => $chartInternasionalVolume]
            ]
        ];

        $costQueryBuilder = clone $baseQuery;
        $costQueryBuilder->orderBy('tahun', 'desc');
        $costQueryBuilder->orderByRaw("FIELD(bulan, '" . implode("','", $this->masterMonths) . "')");
        $costRecords = $costQueryBuilder->paginate(5)->withQueryString(); 

        $totalDomestikOverall = (clone $baseQuery)->sum('ongkir_dalam_negeri');
        $totalInternasionalOverall = (clone $baseQuery)->sum('ongkir_luar_negeri');
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pengiriman_dokumen')->get();

        return view('pengiriman-dokumen', compact(
            'selectedYear', 'selectedMonth', 'availableYears', 'chartVolumeConfig',
            'costRecords', 'totalDomestikOverall', 'totalInternasionalOverall', 'kolomDinamis'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'jenis_form' => 'required|in:volume,ongkir',
        ];

        if ($request->jenis_form === 'volume') {
            $rules['volume_mailroom'] = 'required|integer|min:0';
            $rules['volume_domestik'] = 'required|integer|min:0';
            $rules['volume_internasional'] = 'required|integer|min:0';
            $rules['volume_dof'] = 'required|integer|min:0';
        } else {
            $rules['cost_domestik'] = 'required|integer|min:0';
            $rules['cost_internasional'] = 'required|integer|min:0';
        }

        $request->validate($rules, [
            '*.required' => 'Bidang ini wajib diisi.',
            '*.integer' => 'Bidang ini harus berupa angka.',
        ]);

        try {
            $record = PengirimanDokumen::firstOrNew(['tahun' => $request->tahun, 'bulan' => $request->bulan]);

            if ($request->jenis_form === 'volume') {
                $record->penerimaan_mailroom = $request->volume_mailroom;
                $record->registrasi_surat_masuk_dof = $request->volume_dof;
                $record->pengiriman_dalam_negeri = $request->volume_domestik;
                $record->pengiriman_luar_negeri = $request->volume_internasional;
                $record->data_tambahan = json_encode($request->input('data_tambahan', []));
            } else if ($request->jenis_form === 'ongkir') {
                $record->ongkir_dalam_negeri = $request->cost_domestik;
                $record->ongkir_luar_negeri = $request->cost_internasional;
            }

            $record->penerimaan_mailroom = $record->penerimaan_mailroom ?? 0;
            $record->registrasi_surat_masuk_dof = $record->registrasi_surat_masuk_dof ?? 0;
            $record->pengiriman_dalam_negeri = $record->pengiriman_dalam_negeri ?? 0;
            $record->pengiriman_luar_negeri = $record->pengiriman_luar_negeri ?? 0;
            $record->ongkir_dalam_negeri = $record->ongkir_dalam_negeri ?? 0;
            $record->ongkir_luar_negeri = $record->ongkir_luar_negeri ?? 0;
            $record->e_materai = $record->e_materai ?? 0;
            $record->save();

            return redirect()->route('pengiriman-dokumen.index', ['year' => $request->tahun, 'month' => $request->bulan])
                ->with('success', "Data laporan bulan {$request->bulan} {$request->tahun} berhasil diperbaharui.");

        } catch (\Exception $e) {
            \Log::error("Error saving PengirimanDokumen: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    public function destroy($id)
    {
        try {
            PengirimanDokumen::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Data pengiriman dokumen berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'jenis' => 'required|in:volume,ongkir'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PengirimanDokumenImport($request->jenis), $request->file('file')); 
            return redirect()->back()->with('success', 'Data berhasil ditembak ke Database secara paksa!');
        } catch (\Exception $e) {
            // Tampilkan error ke Pop-up Merah
            return redirect()->back()->with('error_modal', 'ERROR SYSTEM: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $year = $request->input('year', 'semua');
        $month = $request->input('month', 'semua');
        $jenis = $request->input('jenis', 'volume');
        $typeLabel = $jenis == 'volume' ? 'Volume_Dokumen' : 'Biaya_Ongkir';
        
        // Tidak perlu lagi mempassing parameter $jenis ke constructor (otomatis terbaca)
        return Excel::download(new PengirimanDokumenExport(false, $year, $month), "Laporan_Pengiriman_{$typeLabel}_{$month}_{$year}.xlsx");
    }

    public function exportPdf(Request $request)
    {
        $year = $request->input('year', 'semua');
        $month = $request->input('month', 'semua');

        $query = PengirimanDokumen::query();
        if ($year !== 'semua') $query->where('tahun', $year);
        if ($month !== 'semua') $query->where('bulan', $month);

        $query->orderBy('tahun', 'desc')->orderByRaw("FIELD(bulan, '" . implode("','", $this->masterMonths) . "')");
        $records = $query->get();

        $totalDomestik = $records->sum('ongkir_dalam_negeri');
        $totalInternasional = $records->sum('ongkir_luar_negeri');
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'pengiriman_dokumen')->get();

        $pdf = Pdf::loadView('exports.pengiriman-pdf', compact('records', 'year', 'month', 'totalDomestik', 'totalInternasional', 'kolomDinamis'))->setPaper('a4', 'landscape');
        return $pdf->stream("Laporan_Pengiriman_{$month}_{$year}.pdf");
    }

    public function downloadTemplate(Request $request)
    {
        $jenis = $request->input('jenis', 'volume');
        $typeLabel = $jenis == 'volume' ? 'Volume_Dokumen' : 'Biaya_Ongkir';
        return Excel::download(new PengirimanDokumenExport(true, 'semua', 'semua', $jenis), "Template_Pengiriman_{$typeLabel}.xlsx");
    }

    public function storeKolomDinamis(Request $request)
    {
        $request->validate(['modul' => 'required|string', 'nama_kolom' => 'required|string|max:100', 'tipe_input' => 'required|in:text,number,date,dropdown,currency']);
        if (DB::table('dynamic_columns')->where('modul', $request->modul)->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim($request->nama_kolom))])->exists()) {
            return back()->with('error_modal', 'Kolom sudah ada!')->with('failed_modul', $request->modul);
        }

        $pilihanDropdown = ($request->tipe_input === 'dropdown' && $request->filled('pilihan_dropdown')) ? json_encode(array_map('trim', explode(',', $request->pilihan_dropdown))) : null;

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
}