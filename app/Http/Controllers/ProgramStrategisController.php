<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramStrategis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgramStrategisController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');
        
        $filterTahun = $request->input('tahun', Carbon::now()->year);

        $tahunTersedia = ProgramStrategis::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia) || !in_array(Carbon::now()->year, $tahunTersedia)) {
            $tahunTersedia[] = Carbon::now()->year; 
            rsort($tahunTersedia);
        }

        $query = ProgramStrategis::query();
        if ($filterTahun !== 'semua') {
            $query->where('tahun', $filterTahun);
        }
        
        $query->orderBy('tahun', 'desc')->orderBy('id', 'asc');
        
        $dataProgramRaw = $query->get();

        $groupedProgram = $dataProgramRaw->groupBy(function($item) {
            $sasaran = $item->sasaran ? strtolower(trim($item->sasaran)) : 'tanpa_sasaran';
            $bulan = $item->bulan ? $item->bulan : 'tanpa_bulan';
            return $item->tahun . '_' . $bulan . '_' . $sasaran . '_' . strtolower(trim($item->program_strategis));
        });

        // ✨ FIX MySQL STRICT MODE: Kasih tau buat ambil ID paling kecil (awal) sebagai patokan urutan
        $listSasaranProgram = ProgramStrategis::select('tahun', 'bulan', 'sasaran', 'program_strategis', DB::raw('MIN(id) as sort_id'))
            ->whereNotNull('sasaran')
            ->whereNotNull('program_strategis')
            ->groupBy('tahun', 'bulan', 'sasaran', 'program_strategis')
            ->orderBy('sort_id', 'asc')
            ->get();

        
        $page = $request->get('page', 1);
        $perPage = 10; // Tampilkan 10 grup per halaman
        $paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedProgram->forPage($page, $perPage),
            $groupedProgram->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $groupedProgram = $paginatedGroups;

        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'program_strategis')->get();

        return view('program-strategis', compact('tanggalToday', 'filterTahun', 'tahunTersedia', 'groupedProgram', 'kolomDinamis', 'listSasaranProgram'));
    }

    public function store(Request $request)
    {
        // JIKA SUBMIT DARI MODAL TAMBAH PROGRAM & SASARAN (MASTER)
        if ($request->input('is_master') == '1') {
            $request->validate([
                'periode' => 'required|date_format:Y-m',
                'sasaran' => 'required|string',
                'program_strategis' => 'required|string',
            ]);

            $periodeParts = explode('-', $request->periode);
            
            ProgramStrategis::create([
                'tahun' => $periodeParts[0],
                'bulan' => $periodeParts[1],
                'sasaran' => $request->sasaran,
                'program_strategis' => $request->program_strategis,
                'deskripsi_kegiatan' => 'Belum ada rincian kegiatan (Silakan Edit)',
                'realisasi' => null, 
                'progress_saat_ini' => '-',
                'kendala' => '-',
                'keterangan_tambahan' => '-',
                'status' => '-' 
            ]);

            return back()->with('success', 'Program dan Sasaran berhasil ditambahkan.');
        }

        // JIKA SUBMIT DARI MODAL TAMBAH KEGIATAN NORMAL
        $request->validate([
            'sasaran_program_select' => 'required|string',
            'deskripsi_kegiatan' => 'required|string',
        ]);

        $data = $request->except(['sasaran_program_select', 'is_master']);
        
        $parts = explode('|||', $request->sasaran_program_select);
        $data['tahun'] = $parts[0] ?? '';
        $data['bulan'] = $parts[1] ?? '';
        $data['sasaran'] = $parts[2] ?? '';
        $data['program_strategis'] = $parts[3] ?? '';

        $data['data_tambahan'] = $request->input('data_tambahan', []);

        // AMBIL ATRIBUT DARI PARENT (Baris Pertama yang Cocok)
        $parent = ProgramStrategis::where('tahun', $data['tahun'])
            ->where('bulan', $data['bulan'])
            ->where('sasaran', $data['sasaran'])
            ->where('program_strategis', $data['program_strategis'])
            ->first();

        if ($parent) {
            $data['target_waktu_start'] = $parent->target_waktu_start;
            $data['target_waktu_end'] = $parent->target_waktu_end;
            $data['kendala'] = $parent->kendala;
            $data['keterangan_tambahan'] = $parent->keterangan_tambahan;
            $data['status'] = $parent->status;
        } else {
            // Jaga-jaga jika parent tidak ada
            $data['status'] = '-';
            $data['kendala'] = '-';
            $data['keterangan_tambahan'] = '-';
        }

        // Cek jika parent hanyalah placeholder kosong ("Belum ada rincian kegiatan"), kita timpakan langsung.
        $placeholder = ProgramStrategis::where('tahun', $data['tahun'])
            ->where('bulan', $data['bulan'])
            ->where('sasaran', $data['sasaran'])
            ->where('program_strategis', $data['program_strategis'])
            ->where('deskripsi_kegiatan', 'Belum ada rincian kegiatan (Silakan Edit)')
            ->first();

        if ($placeholder) {
            $placeholder->update($data);
        } else {
            ProgramStrategis::create($data);
        }

        return back()->with('success', 'Kegiatan Program Strategis baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sasaran_program_select' => 'required|string',
            'deskripsi_kegiatan' => 'required|string',
            'status' => 'required|string'
        ]);

        $programModel = ProgramStrategis::findOrFail($id);
        
        $data = $request->except(['sasaran_program_select']);

        $parts = explode('|||', $request->sasaran_program_select);
        $data['tahun'] = $parts[0] ?? '';
        $data['bulan'] = $parts[1] ?? '';
        $data['sasaran'] = $parts[2] ?? '';
        $data['program_strategis'] = $parts[3] ?? '';

        $data['data_tambahan'] = $request->input('data_tambahan', []);

        // Update baris ini
        $programModel->update($data);

        // Update atribut PARENT secara massal untuk semua kegiatan yang satu grup
        ProgramStrategis::where('tahun', $data['tahun'])
            ->where('bulan', $data['bulan'])
            ->where('sasaran', $data['sasaran'])
            ->where('program_strategis', $data['program_strategis'])
            ->update([
                'target_waktu_start' => $data['target_waktu_start'] ?? null,
                'target_waktu_end' => $data['target_waktu_end'] ?? null,
                'status' => $data['status'],
                'kendala' => $data['kendala'] ?? null,
                'keterangan_tambahan' => $data['keterangan_tambahan'] ?? null,
            ]);

        return back()->with('success', 'Data Program Strategis berhasil diperbarui.');
    }

        public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:program_strategis,id',
        ]);

        \App\Models\ProgramStrategis::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' Data program strategis berhasil dihapus.');
    }

    public function destroy($id)
    {
        ProgramStrategis::findOrFail($id)->delete();
        return back()->with('success', 'Program Strategis berhasil dihapus.');
    }

    public function storeKolomDinamis(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        $request->validate([
            'modul'      => 'required|string',
            'nama_kolom' => 'required|string|max:100',
            'tipe_input' => 'required|in:text,number,date,dropdown,currency',
        ]);

        $isDuplicate = DB::table('dynamic_columns')->where('modul', $request->modul)
            ->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim($request->nama_kolom))])->exists();

        if ($isDuplicate) return back()->with('error_modal', 'Kolom sudah ada!');

        $pilihanDropdown = null;
        if ($request->tipe_input === 'dropdown' && $request->pilihan_dropdown) {
            $pilihanDropdown = json_encode(array_map('trim', explode(',', $request->pilihan_dropdown)));
        }

        DB::table('dynamic_columns')->insert([
            'modul' => $request->modul, 'nama_kolom' => trim($request->nama_kolom),
            'tipe_input' => $request->tipe_input, 'pilihan_dropdown' => $pilihanDropdown,
            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Kolom dinamis baru berhasil ditambahkan.');
    }

    public function destroyKolomDinamis($id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        DB::table('dynamic_columns')->where('id', $id)->delete();
        return back()->with('success', 'Kolom dinamis berhasil dihapus.');
    }

    public function import(Request $request)
    {
        set_time_limit(0);
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:51200']);
        try {
            Excel::import(new \App\Imports\ProgramStrategisImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', 'semua');
        return Excel::download(new \App\Exports\ProgramStrategisExport(false, $tahun), 'Data_Program_Strategis_'.$tahun.'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tahun = $request->input('tahun', 'semua');
        $query = ProgramStrategis::query();
        if ($tahun !== 'semua') $query->where('tahun', $tahun);
        
        $dataProgram = $query->orderBy('tahun', 'desc')->orderBy('id', 'asc')->get();
        $groupedProgram = $dataProgram->groupBy(function($item) {
            $sasaran = $item->sasaran ? strtolower(trim($item->sasaran)) : 'tanpa_sasaran';
            $bulan = $item->bulan ? $item->bulan : 'tanpa_bulan';
            return $item->tahun . '_' . $bulan . '_' . $sasaran . '_' . strtolower(trim($item->program_strategis));
        });
        
        
        $page = $request->get('page', 1);
        $perPage = 10; // Tampilkan 10 grup per halaman
        $paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedProgram->forPage($page, $perPage),
            $groupedProgram->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $groupedProgram = $paginatedGroups;

        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'program_strategis')->get();

        $pdf = Pdf::loadView('pdf.program-strategis', compact('groupedProgram', 'tahun', 'kolomDinamis'))->setPaper('a4', 'landscape');
        return $pdf->download('Data_Program_Strategis_'.$tahun.'.pdf');
    }
}
