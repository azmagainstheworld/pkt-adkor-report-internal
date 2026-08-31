<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JasaKurirMaster;
use App\Models\JasaKurirData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class JasaKurirController extends Controller
{
    public function index(Request $request)
    {
        // 1. FILTER DINAMIS: Ambil tahun dan bulan yang benar-benar ada di database
        $availableYears = JasaKurirData::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }
        
        $masterMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $availableMonthsRaw = JasaKurirData::select('bulan')->distinct()->pluck('bulan')->toArray();
        $availableMonths = array_intersect($masterMonths, $availableMonthsRaw); // Jaga urutan bulan
        if (empty($availableMonths)) {
            $availableMonths = [Carbon::now()->translatedFormat('F')];
        }

        $tahunFilter = $request->input('tahun', 'semua');
        $bulanFilter = $request->input('bulan', 'semua');

        $kurirMaster = JasaKurirMaster::where('aktif', true)->orderBy('id')->get();

        $query = JasaKurirData::with('jasaKurirMaster');
        if ($tahunFilter != 'semua') $query->where('tahun', $tahunFilter);
        if ($bulanFilter != 'semua') $query->where('bulan', $bulanFilter);
        
        $rawData = $query->get();

        // 2. FORMAT DATA TABEL & SIAPKAN DATA UNTUK MODAL EDIT
        $tableData = [];
        foreach ($rawData as $data) {
            $key = $data->tahun . '-' . $data->bulan;
            
            if (!isset($tableData[$key])) {
                $tableData[$key] = [
                    'tahun' => $data->tahun,
                    'bulan' => $data->bulan,
                    'total_semua' => 0,
                    'kurir_data' => [], // Untuk Auto-fill modal Edit
                    'data_tambahan' => [] // Untuk menampung kolom dinamis
                ];
                foreach ($kurirMaster as $kurir) {
                    $tableData[$key]['kurir_' . $kurir->id] = 0;
                }
            }

            $tableData[$key]['kurir_' . $data->jasa_kurir_id] = $data->jumlah;
            $tableData[$key]['kurir_data'][$data->jasa_kurir_id] = $data->jumlah;
            $tableData[$key]['total_semua'] += $data->jumlah;
            
            if (!empty($data->data_tambahan)) {
                $tableData[$key]['data_tambahan'] = array_merge($tableData[$key]['data_tambahan'], $data->data_tambahan);
            }
        }

        // 3. FORMAT DATA CHART
        $chartData = [];
        $chartMonthsTarget = ($bulanFilter == 'semua') ? $availableMonths : [$bulanFilter];
        foreach ($chartMonthsTarget as $bulan) {
            $bulanData = ['bulan' => $bulan];
            foreach ($kurirMaster as $kurir) {
                $jumlah = $rawData->where('bulan', $bulan)
                                  ->where('jasa_kurir_id', $kurir->id)
                                  ->first()->jumlah ?? 0;
                $bulanData[$kurir->nama_kurir] = $jumlah;
            }
            $chartData[] = $bulanData;
        }

        // 4. TARIK KOLOM DINAMIS
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'jasa_kurir')->get();

        // 5. PAGINASI ARRAY $tableData (10 BARIS PER HALAMAN)
        $tableDataCollection = collect($tableData);
        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentPageItems = $tableDataCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedTableData = new LengthAwarePaginator($currentPageItems, $tableDataCollection->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'query' => request()->query()
        ]);
        // Re-assign $tableData to the paginator object
        $tableData = $paginatedTableData;

        return view('jasakurir', compact(
            'kurirMaster', 'tableData', 'chartData', 
            'tahunFilter', 'bulanFilter', 
            'availableYears', 'availableMonths', 'kolomDinamis'
        ));
    }

    public function storeMaster(Request $request)
    {
        $request->validate(['nama_kurir' => 'required|string|max:50|unique:jasa_kurir_master,nama_kurir']);
        JasaKurirMaster::create(['nama_kurir' => $request->nama_kurir, 'aktif' => true]);
        return redirect()->back()->with('success', 'Jasa kurir baru berhasil ditambahkan.');
    }

    // Fungsi Store dan Update Data dijadikan satu (Multiple Courier Insert/Update)
    public function storeData(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'kurir' => 'required|array',
            'kurir.*' => 'nullable|integer|min:0'
        ]);

        $dataTambahan = $request->input('data_tambahan', []);

        foreach ($request->kurir as $kurirId => $jumlah) {
            // Jika input ada nilainya, Update atau Create
            if ($jumlah !== null && $jumlah !== '') {
                JasaKurirData::updateOrCreate(
                    ['jasa_kurir_id' => $kurirId, 'tahun' => $request->tahun, 'bulan' => $request->bulan],
                    ['jumlah' => $jumlah, 'data_tambahan' => $dataTambahan]
                );
            } else {
                // Jika input dikosongkan saat edit, hapus data lama jika ada
                JasaKurirData::where([
                    'jasa_kurir_id' => $kurirId, 'tahun' => $request->tahun, 'bulan' => $request->bulan
                ])->delete();
            }
        }

        return redirect()->back()->with('success', 'Data jumlah pengiriman berhasil disimpan/diperbarui.');
    }

    public function destroyData($tahun, $bulan)
    {
        JasaKurirData::where('tahun', $tahun)->where('bulan', $bulan)->delete();
        return redirect()->back()->with('success', 'Seluruh data pengiriman bulan ' . $bulan . ' ' . $tahun . ' berhasil dihapus.');
    }

    // ==========================================
    // BLUEPRINT FUNGSI ATUR KOLOM
    // ==========================================
    public function storeKolomDinamis(Request $request)
    {
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
        DB::table('dynamic_columns')->where('id', $id)->delete();
        return back()->with('success', 'Kolom dinamis berhasil dihapus.');
    }

    public function destroyMaster($id)
    {
        try {
            JasaKurirMaster::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Jasa kurir / ekspedisi berhasil dihapus.');
        } catch (\Exception $e) {
            // Jika ditolak oleh database karena ekspedisi tersebut sudah memiliki data pengiriman (relasi foreign key)
            return redirect()->back()->with('error_modal', 'Ekspedisi tidak dapat dihapus karena sudah memiliki riwayat jumlah pengiriman di dalam sistem! Silakan hapus riwayat datanya terlebih dahulu.');
        }
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'File Excel wajib diunggah!',
            'file.mimes' => 'Format file tidak valid, harus .xlsx atau .xls'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\JasaKurirImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Jasa Kurir berhasil di-import dari Excel!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Import Jasa Kurir Error: " . $e->getMessage());
            return redirect()->back()->with('error_modal', 'Gagal memproses import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request) {
        return redirect()->back()->with('error_modal', 'Fitur Export Excel Jasa Kurir sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function exportPdf(Request $request) {
        return redirect()->back()->with('error_modal', 'Fitur Export PDF Jasa Kurir sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function downloadTemplate(Request $request) {
        return redirect()->back()->with('error_modal', 'Template Excel Jasa Kurir sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }
}
